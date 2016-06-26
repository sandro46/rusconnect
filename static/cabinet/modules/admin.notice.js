var site_notice = {
	textEditor : false,	
	globalRecipients : {},
		
	showList : function() {
		$page.show(['list.html', 'notice'], false, function(current){		
			
		});
	},
		
	deactivate : function(id) {
		
	},
	
	activate : function(id) {
		
	},
	
	deleteNotice : function(id) {
		$page.confirm('Удаление уведомления','Вы действительно хотите удалить это уведомление?', function(){
			$page.lock();
			notifications_admin.deleteNotice(id, function(){
				$page.unlock();
				$page.sticky('Уведомление было удалено','Уведомление было успешно удалено');
				grid.notice_list.start();
			})
		});
	},
	
	editNotice : function(noticeId) {
		$page.lock();
		notifications_admin.getNotification(noticeId, function(noticeInfo){
			var preloadTplData = [ 
      		         {noticeEvents:['notifications_admin','getEvents']},
      		         {noticeActions:['notifications_admin','getActions']}
      		];
     		
     		$page.show(['add.html', 'notice', preloadTplData], {noticeInfo : noticeInfo}, function(current){
     			site_notice.noticeUi(noticeId, noticeInfo);
     			$page.bind('back', function(){
     				$page.back();
     			});
     		});
		});
		
	},
	
	noticeUi : function(noticeId, noticeData) {
		var editorSettings = {
				filebrowserUploadUrl : '/ru/files/upload/source/static/application/ckeditor/',
				toolbarGroups : [
	                 { name: 'document',    groups: [ 'mode', /*'document', 'doctools' */] },
	                 { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
	                 { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
	                 { name: 'links' },
	                 { name: 'insert' },
	                 '/',
	
	                 { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
	                 { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
	
	                 '/',
	                 { name: 'styles' },
	                 { name: 'colors' },
	                 { name: 'tools' },
	                 { name: 'others' }
			 ],

			 extraAllowedContent : 'a(*){*}[*]'
		};
		
		CKEDITOR.config.imageUrlPrefix = '{$url}';
		
		if(noticeId && noticeData.action_id == 1) {
			site_notice.textEditor = CKEDITOR.appendTo('noticeDataTextEditor', site_notice.editorSettings, noticeData.data.text);
		} else {
			site_notice.textEditor = CKEDITOR.appendTo('noticeDataTextEditor', site_notice.editorSettings, '');
		}
		
		
		var header = $page.current.find('div[name=notice_header]');
		var action = header.find('select[name="action"]');
		var body = $page.current.find('div[name="notice_data"]');
		var bodyTitle = $page.current.find('span[name="action_data_block_title"]');
		
		var notice_recepents = {
				1 : 'Покупатель',
				2 : 'Администратор',
				//3 : 'Все менеджеры',
				4 : 'Группа пользователей',
				5 : 'Пользователь',
				6 : 'Произвольный email',
				7 : 'Произвольный номер телефона'
			};
			
		var notice_action_to_recipt = {
			1 : [1,2,4,5,6],
			2 : [1,2,4,5,7],
			3 : [2,4,5]
		};
			
		action.bind('change', function(){
			var actionId = $(this).val();
			var blockTitle = body.find('div[action="'+actionId+'"]').attr('action_block_name');
			
			body.find('div[action]:visible').slideToggle();
			body.find('div[action="'+actionId+'"]').slideToggle();

			bodyTitle.html(blockTitle);
			
			var recip = $page.current.find('div[name=recipientBlock]');
			
			if(typeof(notice_action_to_recipt[actionId]) != 'undefined') {
				var recipChosen = $page.current.find('select[name=recipientType]');
				recipChosen.parent().find('div.chzn-container').remove();
				recipChosen.find('option').remove();
				recipChosen.removeClass('chzn-done');
				recipChosen.removeAttr('id');
				recipChosen.css({width:'334px'});
				
				for(var i in notice_action_to_recipt[actionId]) {
					if(typeof(site_notice.globalRecipients[notice_action_to_recipt[actionId][i]]) != 'undefined' &&  (notice_action_to_recipt[actionId][i] == 1 || notice_action_to_recipt[actionId][i] == 2)) {
						// skip
					} else {
						recipChosen.append($('<option value="'+notice_action_to_recipt[actionId][i]+'">'+notice_recepents[notice_action_to_recipt[actionId][i]]+'</option>'));
					}
				}
				recipChosen.chosen();
				
				
				recip.show();
			} else {
				recip.hide();
			}
			
		});	
		
		if(noticeId) {
			for(var i in noticeData.recipient) {
				var typeId = noticeData.recipient[i].recipient_local_type;
				$page.current.find('ul[name="recipientsList"] li[rec-type="'+typeId+'"]').show();
				
				if(typeId == 1 || typeId == 2) {
					site_notice.globalRecipients[typeId] = true;
				} else {
					
					if(typeof(site_notice.globalRecipients[typeId]) != 'object') {
						site_notice.globalRecipients[typeId] = [];
					}
					
					site_notice.globalRecipients[typeId].push(noticeData.recipient[i].data);
				}
			}
			
			if(typeof(site_notice.globalRecipients[6]) == 'object') {
				$page.current.find('ul[name="recipientsList"] li[rec-type="'+6+'"] .contactsCount').html(site_notice.globalRecipients[6].length);
			}
			
			if(typeof(site_notice.globalRecipients[7]) == 'object') {
				$page.current.find('ul[name="recipientsList"] li[rec-type="'+7+'"] .contactsCount').html(site_notice.globalRecipients[7].length);
			}
			
			if(typeof(site_notice.globalRecipients[5]) == 'object') {
				$page.current.find('ul[name="recipientsList"] li[rec-type="'+5+'"] .contactsCount').html(site_notice.globalRecipients[5].length);
			}
		}
			
		$page.current.find('a[name="recipientAddButton"]').bind('click', function(){
			var recipTypeId = $page.current.find('select[name="recipientType"]').val();
			
			$page.current.find('[name="recipientNotFound"]').hide();
			
			
			if(recipTypeId  == 1 || recipTypeId == 2) {
				site_notice.globalRecipients[recipTypeId] = true;
				$page.current.find('ul[name="recipientsList"] li[rec-type="'+recipTypeId+'"]').show();
			} else {
				if(recipTypeId == 6 || recipTypeId == 7) {
					$page.current.find('.recipientAction[type="'+recipTypeId+'"]').show();
					$page.current.find('.recipientAction[type="'+recipTypeId+'"] input.recipData').val('');
				}
				
				if(recipTypeId == 5) {
					var block = $page.current.find('.recipientAction[type="5"]');
					if(!block.attr('loaded')) {
						$page.lock();
						block.hide();
						tpl.get('simple_list.html', 'users',false, function(html){
							$page.unlock();
							block.append(html);
							block.slideToggle();
							block.attr('loaded', 1);
							var doneButton = $('<a href="javascript:void(0)" class="btn">Готово</a>');
							block.append(doneButton);
							doneButton.bind('click', function(){
								var users = grid.users_list.groupAction.data.include;
								block.slideToggle();
																	
								$page.current.find('ul[name="recipientsList"] li[rec-type="'+recipTypeId+'"]').show();
								$page.current.find('ul[name="recipientsList"] li[rec-type="'+recipTypeId+'"] .contactsCount').html(users.length);
								site_notice.globalRecipients[recipTypeId] = users;
								
								$page.current.find('ul[name="recipientsList"] li[rec-type="'+recipTypeId+'"]').show();
							});
							
							
							
							grid.users_list.once('afterDataLoad', function(){
								if(typeof(site_notice.globalRecipients[5]) == 'object') {
									console.log('callGrlist');
									grid.users_list.groupAction.appendBuffer(site_notice.globalRecipients[5]);
								}
							});
							
							grid.users_list.start(function(){
								
							})
							
							
							
							
							
						});
						
					} else {
						block.slideToggle();
					}
				}
			}
			
		});
		
		action.find('option[selected]').removeAttr('selected');
		
		if(noticeId) {
			action.find('option[value="'+noticeData.action_id+'"]').attr('selected','selected')
		} else {
			action.find('option[value="1"]').attr('selected','selected');
		}
		
		action.trigger('change');
		action.chosen();
		
		
		$page.current.find('.recipientAction[type="6"] a.btn').bind('click',function(){
			var mail = $(this).parent().find('input.recipData').val();
			if(!mail.length) return;
			$page.current.find('ul[name="recipientsList"] li[rec-type="6"]').show();
			if(typeof(site_notice.globalRecipients[6]) != 'object') site_notice.globalRecipients[6] = [];
			if(site_notice.globalRecipients[6].indexOf(mail) == -1) {
				site_notice.globalRecipients[6].push(mail);
				$page.current.find('ul[name="recipientsList"] li[rec-type="6"] .contactsCount').html(site_notice.globalRecipients[6].length);
			} 
			$(this).closest('.recipientAction').slideToggle();
		});
		
		$page.current.find('.recipientAction[type="7"] a.btn').bind('click',function(){
			var phone = $(this).parent().find('input.recipData').val();
			if(!phone.length) return;
			$page.current.find('ul[name="recipientsList"] li[rec-type="7"]').show();
			if(typeof(site_notice.globalRecipients[7]) != 'object') site_notice.globalRecipients[7] = [];
			if(site_notice.globalRecipients[7].indexOf(phone) == -1) {
				site_notice.globalRecipients[7].push(phone);
				$page.current.find('ul[name="recipientsList"] li[rec-type="7"] .contactsCount').html(site_notice.globalRecipients[7].length);
			}
			$(this).closest('.recipientAction').slideToggle();
		});
		
		$page.current.find('ul[name="recipientsList"] li a.rm_recip').bind('click', function(){
			var recipId = $(this).parent().attr('rec-type');
			delete(site_notice.globalRecipients[recipId]);
			$(this).parent().find('.contactsCount').html('0');
			$(this).parent().hide();
		});
		
		$page.bind('save', function(){
			var header = $page.current.find('div[name=notice_header]');
			var body = $page.current.find('div[name="notice_data"]');
			var info = {
					title : header.find('input[name="notice_title"]').val(),
					eventId : header.find('select[name="event"]').val(),
					actionId : header.find('select[name="action"]').val(),
					recipients : site_notice.globalRecipients
			}

			var message = site_notice.getNoticeData(info.actionId);
						
			$page.formMessage.clear();
			
			if(!$page.getForm(header).check) {
				$page.formMessage.error('Ошибка обработки формы.','Заполните все поля формы!');
				$page.top();
				return false;
			} else {
				$page.formMessage.clear();
			}

			if(jQuery.isEmptyObject(info.recipients)) {
				$page.formMessage.error('Ошибка обработки формы.','Укажите получателей уведомления!');
				$page.top();
				return false;
			} else {
				$page.formMessage.clear();
			}

			if(!site_notice.noticeFormCheck(info.actionId, message)) {
				$page.scrolTo(body);
				return false;
			}

			$page.lock();
			notifications_admin.save(noticeId, info, message, function(result){
				$page.unlock();
			});	
		});
	},
	
	addNotice : function() {
		var preloadTplData = [ 
		         {noticeEvents:['notifications_admin','getEvents']},
		         {noticeActions:['notifications_admin','getActions']}
		];
		
		$page.show(['add.html', 'notice', preloadTplData], false, function(current){
			site_notice.noticeUi(false, false);
		});
	},
		
	
	getNoticeData : function(noticeActionId) {
		var body = $page.current.find('div[action="'+noticeActionId+'"]');
		var info = {};
		
		if(noticeActionId == 1) {
			info = {
				title : body.find('input[name="title"]').val(),
				sender : body.find('input[name="sender"]').val(),
				text : 	site_notice.textEditor.getData()
			};
		}
		
		if(noticeActionId == 2) {
			info = {
				sender : body.find('input[name="sender"]').val(),
				text : 	body.find('textarea[name="message"]').val(),
			};
		}
		
		if(noticeActionId == 4) {
			info = {
				url : body.find('input[name="url"]').val(),
				method : body.find('select[name="method"]').val(),
				extended : body.find('textarea[name="extended"]').val(),
			};
		}
		
		if(noticeActionId == 5) {
			info = {
				url : body.find('input[name="url"]').val(),
				method : body.find('select[name="method"]').val(),
				extended : body.find('textarea[name="extended"]').val(),
			};
		}
		
		return info;
	},
	
	sendTestNotice : function(actionId, obj) {
		if(typeof(obj) == 'object' && $(obj).is('.disabled')) return false;
		
		var message = this.getNoticeData(actionId);
		
		if(this.noticeFormCheck(actionId, message, true)) {
			var eventId = $page.current.find('select[name="event"]').val();
			if(!eventId) {
				return $page.sticky('Ошибка отправки тестового сообщения!','Тестовое сообщение не было отправлено. Необходимо указать &laquo;Событие&raquo; для которого будет выполнятся отправка сообщения.');
			}
			if(typeof(obj) == 'object') {
				$(obj).after($('<img src="'+static+'assets/pre-loader/Rounded blocks.gif" style="width:18px; margin-left: -145px;" class="inputloader"></img>'));
				$(obj).addClass('disabled');
			}
			
			var destination = '';
			
			if(actionId == 1) {
				destination = $(obj).parent().find('input[name="test_mail_destination"]').val();
			} else if(actionId == 2) {
				destination = $(obj).parent().find('input[name="test_phone_destination"]').val();
			} else if(actionId == 4 || actionId == 5) {
				destination = (actionId == 4)? 'json' : 'xml';
			}
			
			notifications.test(actionId, eventId, destination, message, function(){
				if(typeof(obj) == 'object') {
					$(obj).parent().find('img.inputloader').remove();
					$(obj).removeClass('disabled');
				}
			});
		}
	},
	
	noticeFormCheck : function(noticeActionId, info, forTest) {
		var body = $page.current.find('div[action="'+noticeActionId+'"]');
		var tpl = $('<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button><strong>Ошибка</strong> Необходимо заполнить форму</div>').hide();
		
		body.find('.control-group').removeClass('error');
		body.find('.alert-error').remove();
		body.prepend(tpl);
	
		if(noticeActionId == 1) {
			if(forTest  === true) {
				var mail = body.find('input[name="test_mail_destination"]').val();
				if(!mail || mail.length < 7) {
					body.find('input[name="test_mail_destination"]').closest('.control-group').addClass('error');
					tpl.show();
					return false;
				}
			}
						
			if(!info.title || info.title.length < 4) {
				tpl.show();
				body.find('input[name="title"]').closest('.control-group').addClass('error');
				return false;
			} 			
			if(!info.sender || info.sender.length < 4) {
				tpl.show();
				body.find('input[name="sender"]').closest('.control-group').addClass('error');
				return false;
			}
		}
		
		if(noticeActionId == 2) {
			if(!info.sender || info.sender.length < 2) {
				tpl.show();
				body.find('input[name="sender"]').closest('.control-group').addClass('error');
				return false;
			}
		}
		
		if(noticeActionId == 4) {			
			if(!info.url || info.url.length < 5) {
				tpl.show();
				body.find('input[name="url"]').closest('.control-group').addClass('error');
				return false;
			}
		}
		
		if(noticeActionId == 5) {
			if(!info.url || info.url.length < 5) {
				tpl.show();
				body.find('input[name="url"]').closest('.control-group').addClass('error');
				return false;
			}
		}
		
		return true;
	}
};




$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Уведомления',
			on : site_notice.showList,
			
			/*
			'/show/:id/' : {
				name: 'Просмотр уведомления',
				on : site_notice.showUser,
				always_reload: true,
				delete_unload: true
			},
			*/
			'/edit/:id/' : {
				name: 'Редактирование уведомления',
				on : site_notice.editNotice,
				always_reload: true,
				delete_unload: true
			},
			
			'/add/' : {
				name : 'Создать уведомление',
				on : site_notice.addNotice,
				always_reload: true,
				delete_unload: true
			}
		}
	};
	
	$page.init(routes);
});