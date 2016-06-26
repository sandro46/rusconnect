var site_control = {
		editorSettings : {},
		textEditor : {},
		treeMain : null,
		wizardData : {},
		
		/* Управление темами */
		
		themeslist : function() {
			$page.show(['theme_admin_list.html', 'site'], false);
		},
		
		/* welcome */
		
		welcomeMessage : function() {
			$page.show(['welcome_message.html', 'site'], false);
		},
		
		/*  Сайты  */
		sitesList : function() {
			$page.show(['sites_list.html', 'site'], false);
		},
		
		removeMirror : function(siteId, domain, row){
			$page.confirm('Удаление зеркала сайта','Вы действительно хотите отвязать домен <b>'+domain+'</b> от этого интернет-магазина?',function(){
				$page.lock();
				admin_site.removeMirror(siteId, domain, function(){
					$(row).closest('tr').remove();
					$page.unlock();
					$page.sticky('Изменение настроек','От сайта был отвязан домен '+domain+'!');
				});
			});
		},
		
		addMirror : function() {
			var domainCorrect = (function(){
				var domain = $(this).val();
				
				if(!domain.length) {
					$(this).attr('stop-check',1)
					$(this).css({'border-color':'#DDDDDD'});
				}
				if(domain.indexOf('://') != -1) {
					domain = domain.substr(domain.indexOf('://')+3);
				}
				
				if(domain.indexOf('www.') != -1) {
					domain = domain.substr(4);
				}
				
				if(domain.substr(domain.length-1) == '/') {
					domain = domain.substr(0,domain.length-1);
				}
				
				var regexp = /^([a-zа-я0-9]+\.)?[a-zа-я0-9][a-zа-я0-9-]*\.[a-zа-я]{2,6}$/;
				if(!regexp.test(domain)) {
					$(this).css({'border-color':'#B94A48'});
					$(this).attr('stop-check',1);
				} else {
					$(this).css({'border-color':'#DDDDDD'});
					$(this).removeAttr('stop-check');
				}
			});
			
			$('[name="domainMirrorsContainer"]', $page.current).show().find('input').val('');
			$('[name="domainMirrorsContainer"]', $page.current).find('a').unbind('click').bind('click',function(){
				var input = $(this).parent().find('input');
				var siteId = input.attr('site-id');
				var domain = input.val();
				var regexp = /^([a-zа-я0-9]+\.)?[a-zа-я0-9][a-zа-я0-9-]*\.[a-zа-я]{2,6}$/;
				
				if(!regexp.test(domain)) {
					input.css({'border-color':'#B94A48'});
					input.attr('stop-check',1);
				} else {
					input.css({'border-color':'#DDDDDD'});
					input.removeAttr('stop-check');
					
					$page.lock();
					admin_site.addMirror(siteId, domain, function(){
						$page.sticky('Добавлен новый домен','К проекту добавлен новый домен. Для работы проекта на этом домене, Вам необходимо перенаправить домен на наши сервера.');
						$page.unlock();
						$('.siteDomainsList',$page.current).append('<tr><td>'+domain+'</td><td><a href="javascript:void(0)" onclick="site_control.removeMirror('+siteId+', \''+domain+'\', this)"><i class="icon-trash"></i> Удалить</a></td></tr>');
					});
					
				}

			});
		},
		
		editSite : function(siteId) {
			var preloadTplData = [
			  			  		{siteInfo:['admin_site','getSiteInfo',[siteId]]}
			  			];
			
			$page.show(['site_edit.html', 'site', preloadTplData], false, function(current){
				$(".widget-title .tab-header", current).each(function(itemCntId){
					$(this).bind('click',function(){
						var tab_name = $(this).attr('name');
						var widgget_body = $(this).parent().parent().find('.widget-body').eq(0);
						
						$(this).parent().find('span[data-tab-select]').removeAttr('data-tab-select').css({'font-size': '13px', 'font-weight': 'normal', 'color':'#868686', 'cursor':'pointer'});
						$(this).css({'font-size': '13px', 'font-weight': 'bold', 'color':'#4C4C4C', 'cursor':'normal'}).attr('data-tab-select','1');

						widgget_body.find('.tab-body').hide();
						widgget_body.find('.tab-body[section="'+tab_name+'"]').show();
						
						delete widgget_body;
						delete tab_name;
					});
					
					// если есть атрибут, то кликаем по вкладке
					if($(this).attr('is_active')) {
						$(this).trigger('click');
					}
					
					// кликаем первый элемент, если ни один не выбран
					if(itemCntId+1 == $(".widget-title .tab-header", container).length) {
						if(!$(".widget-title span[data-tab-select]", container).length) {
							$(".widget-title .tab-header", container).eq(0).trigger('click');
						}
					}
				});
				
				$('.toggleButtonContainer', container).toggleButtons({
		            label: {
		                enabled: "Вкл",
		                disabled: "Выкл"
		            }
		        });
								
				var logoImage = uploader.init({
					container : $(".LogoUploadButton",current),
					hideUploaded : true,
					formCaption : '',
					buttonCaption : 'Загрузить логотип',
					resize : ['original'],
					//maxsize : '210x90',
					done : function(info) {
						console.log(info);
						$('.LogoContainer',current).html('');
						if(typeof(info.error) != 'undefined') {
							
						} else {
							var item = $('<img></img>').attr('src', info[0].name);
							item.append('<input type="hidden" name="s.logo" value="'+info[0].name+'">');
							$('.LogoContainer',current).append(item);
						}
					}
				});
				
				
				$page.bind('changeDomain', function(){
					$page.lock();
					admin_site.getUserNextDomain(function(res){
						$page.unlock();
						$('input[name="b.domain"]').eq(0).val(res);
					})
				});
	
				$('.moduleSwitcher2', $page.current).each(function(){
					$(this).find('input[type="checkbox"]').bind('change',function(){
						var name = $(this).attr('name');
						
						if($(this).is(':checked')) {
							$(this).closest('div.controls').find('.moduleSetting').show();
						} else {
							$(this).closest('div.controls').find('.moduleSetting').hide();
						}
					});
					
					if($(this).find('input[type="checkbox"]').eq(0).is(':checked')) {
						$(this).closest('div.controls').find('.moduleSetting').show();
					} else {
						$(this).closest('div.controls').find('.moduleSetting').hide();
					}
					
					
					$(this).toggleButtons({
			            label: {
			                enabled: "Вкл",
			                disabled: "Выкл"
			            }
			        });
				});
				
				$page.bind('back',function(){
					$page.back();
				});
				
				$page.bind('save',function(){
					var form = $page.getForm($page.current);
					if(!form.check) return false;
					form = form.data;
					
					var info = {
							site_id : form['b.site_id'],
							name: form['b.name'],
							email: form['b.email'],
							phone: form['b.phone'],
							logo: form['s.logo'],
							meta_description : form['seo.meta_description'], 
							meta_keywords : form['seo.meta_keywords'],
							meta_title : form['seo.title'],
							meta_title_prefix : form['seo.title_prefix'],
							modules : {
								
							},
							integration : {
								
							}
					};
					
					$page.current.find('.moduleActiveSwitch').each(function(){
						var mid = $(this).attr('module-id');
						
						if(typeof(info.modules[mid]) != 'object') {
							info.modules[mid] = {};
						}
						
						info.modules[mid].enable = ($(this).is(':checked'))? true : false;
					});
						
					
					$page.current.find('[for-module]').each(function(){
						var mid = $(this).attr('for-module');
						
						$(this).find('input[module-id]').each(function(){
							if(typeof(info.modules[mid]) != 'object') {
								info.modules[mid] = {};
							}
							
							if($(this).attr('type')=='checkbox') {
								info.modules[mid][$(this).attr('name')] = ($(this).is(':checked'))? true : false;
							} else {
								info.modules[mid][$(this).attr('name')] = $(this).val();
							}
						});
						
						
					});
					
					
					var modules = {'sdek':['sdek_id', 'sdek_sign'], 'uniteller':['uniteller_id','uniteller_sign'], 'jivosite':['jivosite_id'], 'ga':['ga_id'], 'ym':['ym_id']};
        			var tmpSetting = {};
        			var cntSet = 0;
        			var makeArray = function(key, val) {var a = {}; a[key]=val; return a;}
        			
        			for(var i in modules) {
        				if(typeof(form['i.'+i]) != 'undefined' && form['i.'+i] != "") {
        					tmpSetting = {};
        					cntSet = 0;
        					for(var o in modules[i]) {
        						if(typeof(form['i.'+modules[i][o]]) != 'undefined') {
        							tmpSetting[modules[i][o]] = form['i.'+modules[i][o]];
        							cntSet++;
        						}
        					}
        					
        					if(cntSet > 0) {
        						info.integration[i] = tmpSetting;
        					}
        				}
        			}
        			
        			$page.lock();
        			admin_site.updateSite(info, function(result) {
        				
        				$page.unlock();
        				$page.back();
        				$page.sticky('Изменены настройки проекта','Проект был успешно изменен. Новый настройки вступили в силу.');
        			});
					
					
				});
				
      		});
		},
		
		showModuleSettings : function(moduleId) {
			$page.current.find('[on-module="'+moduleId+'"]').toggleClass('active');
			$page.current.find('[for-module="'+moduleId+'"]').toggle();
		},
		
		deleteProject : function(siteId) {
			$page.confirm('Удаление проекта','Вы действительно хотите удалить этот преокт? <br><span>ВНИМАНИЕ!</span> Данные будут полностью утрачены!',function(){
				$page.lock();
				admin_site.deleteShop(siteId, function(){
					$page.unlock();
					$page.sticky('Удаление сайта.','Проект удален. Все данные, файлы, заказы по этому проекту были удалены!');
					grid.sites_list.start();
				});
			});
		},
		
		siteWizard : function() {
			var preloadTplData = [
			  		{themesList:['admin_site','getThemesList']},
			  		//{userAutoDomain:['admin_site','getUserNextDomain']}
			];
			
			$page.show(['site_create.html', 'site', preloadTplData], false, function(current){
				site_control.wizardData = false;
				site_control.siteWizardUI();
				

				$page.bind('wizardDone',function(){
					var extService = [];
					$('#tab4 a[action="selectBoxMagic"][is_selected]').each(function(){
        				extService.push($(this).attr('data-id'));
        			});
        			
        			site_control.wizardData.services = extService;
        			
        			var info = {
        					name : site_control.wizardData['b.name'],
        					phone : site_control.wizardData['b.phone'],
        					email : site_control.wizardData['b.email'],
        					domain : site_control.wizardData['domain'],
        					domain_type : site_control.wizardData['useMyDomain'],
        					logo : (typeof(site_control.wizardData['b.logo']) != 'undefined')? site_control.wizardData['b.logo'] : '',
        					theme_id : site_control.wizardData['theme_id'],
        					services : site_control.wizardData['services'],
        					settings : {}
        			};
        			
        			var modules = {'sdek':['sdek_id', 'sdek_sign'], 'uniteller':['uniteller_id','uniteller_sign'], 'jivosite':['jivosite_id'], 'ga':['ga_id'], 'ym':['ym_id']};
        			var tmpSetting = {};
        			var cntSet = 0;
        			var makeArray = function(key, val) {var a = {}; a[key]=val; return a;}
        			
        			for(var i in modules) {
        				if(typeof(site_control.wizardData['m.'+i]) != 'undefined' && site_control.wizardData['m.'+i] != "") {
        					tmpSetting = {};
        					cntSet = 0;
        					for(var o in modules[i]) {
        						if(typeof(site_control.wizardData['m.'+modules[i][o]]) != 'undefined') {
        							tmpSetting[modules[i][o]] = site_control.wizardData['m.'+modules[i][o]];
        							cntSet++;
        						}
        					}
        					
        					if(cntSet > 0) {
        						info.settings[i] = tmpSetting;
        					}
        				}
        			}
        			
        			$page.lock();
        			admin_site.createShop(info, function(){        				
        				$page.sticky('Проект создан','Поздравляем, проект был успешно создан.');
        				
        				
        				setTimeout(function(){
        					window.document.location.href="http://my.ncity.biz/ru/shop/";
        				}, 1000);
        			});
        			//console.log(info);
				})
			});
		},
		
		
		siteWizardUI : function(current) {
			$('.LogoContainer',current).html('');
			
			/*
			var logoImage = uploader.init({
				container : $(".LogoUploadButton",current),
				hideUploaded : true,
				formCaption : '',
				resize : ['smart:154x50'],
				done : function(info) {
					$('.LogoContainer',current).html('');
					var item = $('<img></img>').attr('src', info[0].name);
					item.append('<input type="hidden" name="b.logo" value="'+info[0].name+'">');
					$('.LogoContainer',current).append(item);
				}
			});*/
			
			var logoImage = uploader.init({
				container : $(".LogoUploadButton",current),
				hideUploaded : true,
				formCaption : '',
				buttonCaption : 'Загрузить логотип',
				resize : ['original'],
				//maxsize : '210x90',
				done : function(info) {
					console.log(info);
					$('.LogoContainer',current).html('');
					if(typeof(info.error) != 'undefined') {
						
					} else {
						var item = $('<img></img>').attr('src', info[0].name);
						item.append('<input type="hidden" name="s.logo" value="'+info[0].name+'">');
						$('.LogoContainer',current).append(item);
					}
				}
			});
			
			
			$page.bind('changeDomain', function(){
				$page.lock();
				admin_site.getUserNextDomain(function(res){
					$page.unlock();
					$('input[name="b.domain"]').eq(0).val(res);
				})
			});
			
			
					
			var domainCorrect = (function(){
				var domain = $(this).val();
				
				if(!domain.length) {
					$(this).attr('stop-check',1)
					$(this).css({'border-color':'#DDDDDD'});
				}
				if(domain.indexOf('://') != -1) {
					domain = domain.substr(domain.indexOf('://')+3);
				}
				
				if(domain.indexOf('www.') != -1) {
					domain = domain.substr(4);
				}
				
				if(domain.substr(domain.length-1) == '/') {
					domain = domain.substr(0,domain.length-1);
				}
				
				var regexp = /^([a-zа-я0-9]+\.)?[a-zа-я0-9][a-zа-я0-9-]*\.[a-zа-я]{2,6}$/;
				if(!regexp.test(domain)) {
					$(this).css({'border-color':'#B94A48'});
					$(this).attr('stop-check',1);
				} else {
					$(this).css({'border-color':'#DDDDDD'});
					$(this).removeAttr('stop-check');
				}
			});
			
			var subdomainCheck = (function(){
				var domain = $(this).val();
				
				if(!domain.length) {
					$(this).attr('stop-check',1)
					$(this).css({'border-color':'#B94A48'});
					if(!$(this).closest('.control-group').is('.error')) $(this).closest('.control-group').addClass('error');
				}
				
				if(domain.indexOf('www.') != -1) {
					domain = domain.substr(4);
				}
				
				var regexp = /([^a-zA-Z0-9\-\_]+)/gi;
				domain = domain.replace(regexp, '');
				
				if(!domain.length) {
					$(this).attr('stop-check',1)
					$(this).css({'border-color':'#B94A48'});
					if(!$(this).closest('.control-group').is('.error')) $(this).closest('.control-group').addClass('error');
					return false;
				}
								
				
				var obj = $(this);
				$page.current.find('img[name="localdomainCheckLoader"]').show();
				$page.current.find('div[name="localdomainCheckResult"]').hide();
				$(this).closest('.control-group').removeClass('error');
				admin_site.checkLocalDomain(domain, function(result){
					$page.current.find('img[name="localdomainCheckLoader"]').hide();
					if(result) {
						obj.css({'border-color':'#B94A48'});
						obj.attr('stop-check',1);
						$page.current.find('div[name="localdomainCheckResult"]').show();
						if(!$(this).closest('.control-group').is('.error')) $(this).closest('.control-group').addClass('error');
					} else {
						obj.removeAttr('stop-check');
						obj.css({'border-color':'#a5d16c'});
						$page.current.find('div[name="localdomainCheckResult"]').hide();
						$(this).closest('.control-group').removeClass('error');
					}
				});	
			});
			
			$('input[name="b.domain"]').eq(0).bind('change keyup', subdomainCheck).attr('stop-check',1); 
			$('input[name="b.domain"]').eq(1).bind('change keyup', domainCorrect).attr('stop-check',1); 
			$('input[name="b.domain"]').eq(2).bind('change keyup', domainCorrect).attr('stop-check',1); 
			
			
			
			$page.bind('checkDomain', function(){
				var domain = $('input[name="b.domain"]').eq(2).val();
				
				if($('input[name="b.domain"]').eq(2).attr('stop-check')) {
					$('span[name="domainSearcheResult"]').removeClass('text-error').removeClass('text-success').addClass('text-error').text('Некорректный адрес!').show();
					return;
				}
				
				$page.lock();
				Whois.isAvailable(domain, function(res){
					$page.unlock();
					if(res == true) {
						$('span[name="domainSearcheResult"]').removeClass('text-error').removeClass('text-success').addClass('text-success').text('Домен свободен!').show();
					} else {
						$('span[name="domainSearcheResult"]').removeClass('text-error').removeClass('text-success').addClass('text-error').text('Домен занят!').show();
					}
				})
			});
			
			
			$('input[name="useMyDomain"]', current).bind('change',function(){
				$('div[name="domainInputControl"]').hide();
				$('div.domain-'+$(this).val(),current).show();
				$('span[name="domainSearcheResult"]').hide();
			});
			
			$('a[action="selectBoxMagic"]', current).bind('click',function(){
				if($(this).attr('is_selected')) {
					$(this).removeAttr('is_selected').removeClass('btn-success').find('i').removeClass('icon-check').addClass('icon-check-empty');
				} else {
					$(this).addClass('btn-success').attr('is_selected','1').find('i').removeClass('icon-check-empty').addClass('icon-check');
				}

			});
			
			$('.moduleSwitcher').each(function(){
				$(this).find('input[type="checkbox"]').bind('change',function(){
					var name = $(this).attr('name');
					if($(this).is(':checked')) {
						$(this).closest('div.controls').find('div[name="'+name+'"]').show();
					} else {
						$(this).closest('div.controls').find('div[name="'+name+'"]').hide();
					}
				});
				
				$(this).toggleButtons({
		            label: {
		                enabled: "Вкл",
		                disabled: "Выкл"
		            }
		        });
			})
			
			$('a[action="selectTheme"]', current).bind('click',function(){
				if($(this).attr('is_selected')) return;
				
				$('a.btn-success[action="selectTheme"]', current).each(function(){
					$(this).removeAttr('is_selected');
					$(this).removeClass('btn-success');
					$(this).find('i').removeClass('icon-check').addClass('icon-check-empty');
				});
				
				$(this).addClass('btn-success').attr('is_selected','1').find('i').removeClass('icon-check-empty').addClass('icon-check');
			});
			
			$('img[rel="elevateZoom"]').elevateZoom({
			   // zoomType: "inner",
				cursor: "crosshair",
				zoomWindowFadeIn: 500,
				zoomWindowFadeOut: 750,
				zoomWindowWidth:400,
		        zoomWindowHeight:400
			}); 
			
			$('.siteWizard',current).bootstrapWizard({
				 'nextSelector': '.button-next',
		         'previousSelector': '.button-previous',
		         onNext: function (tab, navigation, index) {
		        	//console.log(index);
		        	if(!(function(){
		        		if(!site_control.wizardData) site_control.wizardData = {};

		        		if(index == 2) {
		        			var theme = $('#tab2 a[action="selectTheme"][is_selected]');
		        			$('#tab2 div.alert-error').remove();
		        			if(!theme.length) {
		        				var tpl = $('<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button><strong>Ошибка!</strong> Для продолжения, необходимо выбрать дизайн сайта.</div>').hide();
		        				$('#tab2').prepend(tpl);
		        				tpl.slideToggle();
		        				return false;
		        			} else {
		        				site_control.wizardData.theme_id = theme.attr('theme_id');
		        				return true;
		        			}
		        		} else if(index == 4) {
		        			return true;
		        		}
		        		var data = $page.getForm($('#tab'+index, $page.curent));
		        		site_control.wizardData = $.extend({}, site_control.wizardData, data.data);	
		        			
		        		if(index == 1) {
		        			var typeDomain = parseInt($('input[name="useMyDomain"]:checked', $page.curent).val());
		        			//console.log('type:', typeDomain);
		        			if(typeDomain < 2) {
		        				var emIndex = (typeDomain == 1)? 1 : 2;
		        				if($('input[name="b.domain"]', $page.curent).eq(emIndex).attr('stop-check')) {
		        					$('input[name="b.domain"]', $page.curent).eq(emIndex).css({'border-color':'#B94A48'});
		        					return false;
		        				} else {
		        					$('input[name="b.domain"]', $page.curent).eq(emIndex).css({'border-color':'#DDDDDD'});
		        					var domain = $('input[name="b.domain"]', $page.curent).eq(emIndex).val();
		        				}
		        			} else {		        					
		        					if($('input[name="b.domain"]', $page.curent).eq(0).attr('stop-check')) {
			        					$('input[name="b.domain"]', $page.curent).eq(0).css({'border-color':'#B94A48'});
			        					$('input[name="b.domain"]', $page.curent).eq(0).closest('.control-group').addClass('error');
			        					return false;
			        				} else {
			        					$('input[name="b.domain"]', $page.curent).eq(0).css({'border-color':'#DDDDDD'});
			        					$('input[name="b.domain"]', $page.curent).eq(0).closest('.control-group').removeClass('error');
			        					var domain = $('input[name="b.domain"]', $page.curent).eq(0).val();
			        				}
		        			}
		        			
		        			if(domain.indexOf('www.') != -1) {
        						domain = domain.substr(4,domain.length);
        					}
		        			site_control.wizardData.domain = domain;
		        		}
		        			        		
		        		return data.check;
		        	})()) return false;
		        	
	                var total = navigation.find('li').length;
	                var cur = index + 1;
	                $('.step-title', current).text('Шаг ' + (index + 1) + ' из ' + total);
	                $('li', $('.siteWizard', current)).removeClass("done");
	                var li_list = navigation.find('li');
	                for (var i = 0; i < index; i++) {
	                    $(li_list[i]).addClass("done");
	                }

	                if (cur == 1) {
	                	$('.siteWizard', current).find('.button-previous').hide();
	                } else {
	                	$('.siteWizard', current).find('.button-previous').show();
	                }

	                if (cur >= total) {
	                	
	                	$('.siteWizard', current).find('.button-next').hide();
	                	$('.siteWizard', current).find('.button-submit').show();
	                } else {
	                	$('.siteWizard', current).find('.button-next').show();
	                	$('.siteWizard', current).find('.button-submit').hide();
	                }
		          },
		         onPrevious: function (tab, navigation, index) {
		                var total = navigation.find('li').length;
		                var cur = index + 1;
		                $('.step-title', current).text('Шаг ' + (cur) + ' из ' + total);
		                $('li', $('.siteWizard', current)).removeClass("done");
		                var li_list = navigation.find('li');
		                for (var i = 0; i < index; i++) {
		                    $(li_list[i]).addClass("done");
		                }
	
		                if (cur == 1) {
		                	$('.siteWizard', current).find('.button-previous').hide();
		                } else {
		                	$('.siteWizard', current).find('.button-previous').show();
		                }
	
		                if (cur >= total) {
		                	$('.siteWizard', current).find('.button-next').hide();
		                	$('.siteWizard', current).find('.button-submit').show();
		                } else {
		                	
		                	$('.siteWizard', current).find('.button-next').show();
		                	$('.siteWizard', current).find('.button-submit').hide();
		                }
		            },
		            onTabShow: function (tab, navigation, index) {
		            	//console.log('show tab: '+index);
		            	//return false;
		            	 var total = navigation.find('li').length;
			             var cur = index + 1;
			                
			                
		            	if(cur == total) {
		            		var lastDone = navigation.find('li.done');
		            		if(lastDone.length == 3) {
		            			$('.siteWizard', current).find('.button-next').hide();
		            			$('.siteWizard', current).find('.button-submit').show();
		            		} else {
		            			$('.siteWizard', current).find('.button-submit').hide();
		            			$('.siteWizard', current).find('.button-next').hide();
		            		}
		            	} else {
		            		 var lastIndex = navigation.find('li.done').length;
		            		 if(cur-1 <= lastIndex) {
		            			 $('.siteWizard', current).find('.button-next').show();
		            		 } else {
		            			 $('.siteWizard', current).find('.button-next').hide();
		            		 }
		            		
		            		
		            	}
		            	
		               
		                var percent = (cur / total) * 100;
		                $('.siteWizard', current).find('.bar').css({
		                    width: percent + '%'
		                });
		                
		                if(cur == 1) {
		                	$('.siteWizard', current).find('.button-submit').hide();
		                }
		            }
			 });
		},
		
		/* Тема  */
		
		showTheme : function() {
			var preloadTplData = [
			        {themeInfo:['admin_site','getThemeInfo']},
			        {themesList:['admin_site','getThemesList']}
			];

			$page.show(['theme_info.html', 'site', preloadTplData], false, function(current){
				
				
				
				$('img[rel="elevateZoom"]').elevateZoom({
				   // zoomType: "inner",
					cursor: "crosshair",
					zoomWindowFadeIn: 500,
					zoomWindowFadeOut: 750,
					zoomWindowWidth:400,
			        zoomWindowHeight:400
				}); 
				
				
				
      		});
		},
		
		changeTheme : function(themeId, siteUrl) {
			$page.confirm('Изменение темы','<strong style="color:red">Внимание!</strong><br>Вы собираетесь изменить тему оформления. Все настройки текущей темы будут сброшены. Все изменения в шаблонах и файлах темы будут утеряны!<br><strong>Изменить тему оформления?</strong>',function(){
				$page.lock();
				admin_site.changeTheme(themeId, function(){
					$('body').append($('<iframe id="clear_site_cache_helper"></iframe>').attr('src',siteUrl+'ru/-utils/clear_cache/').css({width:'1px;',height:'1px',position:'absolute', left:'-99999',top:'-500',overflow:'hidden',border:'none'}));
					setTimeout(function(){
						$('#clear_site_cache_helper').remove();
						$page.unlock();
						$page.update();
						$page.sticky('Изменение темы.','Тема успешно установлена.');
					}, 2000);
				});
			});
		},
		
		themeSetting : function() {
			var preloadTplData = [
  			        {themeInfo:['admin_site','getThemeInfo']},
  			        {themeGrid:['admin_site', 'getThemeGridHtml']}
  			];
			
			$page.lock();
			
			admin_site.getThemeBlocks(function(blocks){
				$page.show(['theme_setting.html', 'site', preloadTplData], false, function(current){
					site_control.makeGrid(blocks, current);
					$page.unlock();
		    	});
			});
		},
		
		addBlock : function(hp,vp) {
			$page.show(['theme_block_add_master.html', 'site', [{allBlocks:['admin_site', 'getAllThemeBlocks']}]], false, function(current){
				$(".widget-title .tab-header", current).each(function(itemCntId){
					$(this).bind('click',function(){
						var tab_name = $(this).attr('name');
						var widgget_body = $(this).parent().parent().find('.widget-body').eq(0);
						
						$(this).parent().find('span[data-tab-select]').removeAttr('data-tab-select').css({'font-size': '13px', 'font-weight': 'normal', 'color':'#868686', 'cursor':'pointer'});
						$(this).css({'font-size': '13px', 'font-weight': 'bold', 'color':'#4C4C4C', 'cursor':'normal'}).attr('data-tab-select','1');

						widgget_body.find('.tab-body').hide();
						widgget_body.find('.tab-body[section="'+tab_name+'"]').show();
						
						delete widgget_body;
						delete tab_name;
					});
					
					// если есть атрибут, то кликаем по вкладке
					if($(this).attr('is_active')) {
						$(this).trigger('click');
					}
					
					// кликаем первый элемент, если ни один не выбран
					if(itemCntId+1 == $(".widget-title .tab-header", container).length) {
						if(!$(".widget-title span[data-tab-select]", container).length) {
							$(".widget-title .tab-header", container).eq(0).trigger('click');
						}
					}
				});
				
				$page.bind('back', function(){
					$page.back();
				});
				
	    	});
		},
		
		makeGrid : function(blocks, current) {
			$('table.theme-grid td[hpos]', current).html('<span>&nbsp;</span>');
			var findColl = function(hp,vp) {
				return $('td[hpos="'+hp+'"][vpos="'+vp+'"]', current);
			}
			
			var insertBlock = function(block) {
				var em = findColl(block.h_position, block.v_position);
				console.log(em);
				var tpl = '<div class="block type-'+block.type_id+'">'+
							'<div class="widget-title">'+
								'<h4>'+block.block_title+'</h4>'+
								'<a href="javascript:void(0)" class="btn btn-link"><i class="icon-cog"></i> Параметры</a>'+
								'<a href="javascript:void(0)" class="btn btn-link"><i class="icon-trash"></i> Удалить</a>'+
							'</div>'+
							'<div class="generatedHtml">'+block.template_html+'</div>'+
						  '</div>';
				var blockEm = $(tpl);
				em.append(blockEm);
			}
			
			var hpos = 0;
			var vpos = 0;
			var block = false;
			var content = false;
			
			for(var a in blocks) {
				for(var b in blocks[a]) {
					block = blocks[a][b];
					for(var c in block) {
						insertBlock(block[c]);
					}
				}
			}
			
			$('table.theme-grid td[hpos]', current).each(function(){
				var tpl = '<a class="btn btn-small btn-primary" onclick="site_control.addBlock('+$(this).attr('hpos')+','+$(this).attr('vpos')+')" hreef="javascript:void(0)">Добавить блок</a>';
				$(this).append(tpl)
			});
			
		},
		
		
		
		
		
		
		
		
		
		
		
		
		
		showTree : function() {
			$page.show(['tree.html', 'site'], false, function(current){
				site_control.loadTree();
      		});
			
		},
		
		createPage : function(type) {
			var nodes = site_control.treeMain.getSelectedNodes();
			var parentId = (!nodes || !nodes.length)? 0 : nodes[0].id;
			
			$page.router.setRoute('/add/'+type+'/'+parentId+'/');
		},
		
		changeMenuOrder : function(e) {
			var list = e.length ? e : $(e.target);
			var out = list.nestable('serialize');
			
			$page.lock();
			admin_site.changeMenuOrder(out, function() {
				$page.unlock();
			});
			
			
		},
				
		loadTree : function(callback) {
			
			$('.basic_tree',$page.current).html('');
			admin_site.getPagesListFromParent(0, function(nodes){
				var setting = {
						edit: {
							enable: true,
							showRemoveBtn: false,
							showRenameBtn: false
						},
						data: {
							simpleData: {
								enable: true
							}
						},
						callback: {
							beforeDrag: function(treeId, treeNodes) {
								for (var i=0,l=treeNodes.length; i<l; i++) {
									if (treeNodes[i].drag === false) {
										return false;
									}
								}
								
								return true;
							},
							beforeDrop: function(treeId, treeNodes, targetNode, moveType) {
								treeNodes = treeNodes[0];
								
								if(moveType == 'inner') {
									var parentId = (targetNode && typeof(targetNode) == 'object')? targetNode.id : 0;
									$page.lock();
									admin_site.changePageParent(treeNodes.id, parentId, function(){
										$page.unlock()
									});
								}
								
								if(moveType == 'next' || moveType == 'prev') {
									var context = targetNode.id;
									$page.lock();
									admin_site.changePageOrder(treeNodes.id, context, moveType,  function(){
										$page.unlock()
									});
								}

								console.log('source', treeNodes);
								console.log(moveType, targetNode);
								
								
								return targetNode ? targetNode.drop !== false : true;
							},
							
							onDblClick: function(event, treeId, treeNode) {
								console.log('dbl click', treeNode);
							},
							
							onClick : function(event, treeId, treeNode, clickFlag) {
								if(clickFlag == 1) {
									$('a[action="removePage"]',$page.current).removeClass('disabled').unbind('click').bind('click',site_control.removePage);
									$('a[action="editPage"]',$page.current).removeClass('disabled').unbind('click').bind('click',function(){
										var node = site_control.treeMain.getSelectedNodes();
										if(!node || !node.length) {
											return false;
										} 
										console.log(node);
										$page.router.setRoute('/edit/'+node[0].id+'/');
									});
								}
							}
						},
						async : {
							func : admin_site.getPagesListFromParent,
							enable : true
						},
						
						view: {
							selectedMulti: false
						}
					};
				
				site_control.treeMain = $.fn.zTree.init($('.basic_tree',$page.current), setting, nodes);
				if(typeof(callback) == 'function') {
					callback(site_control.treeMain);
				}
			});
		},
		
		editPage : function(pageId) {
			var type = 'html';
  			var types = ['html', 'text', 'tpl'];
  			
  			if(types.indexOf(type) == -1) {
  				$page.sticky('Не корректный тип документа!','Ошибочка вышла!');
  				$page.back();
  				return false;
  			}
  			
  			var tpl = 'page.'+type+'.edit.html';
  			
  			$page.lock();
  			admin_site.getPageInfo(pageId, function(pageInfo){
				var preloadTplData = [
				       {formTemplates:['admin_site','getPagesTemplates']}
				];
				  				
  				$page.show([tpl, 'site', preloadTplData], {pageInfo:pageInfo}, function(current){
  	  				site_control._pageFormHtmlUI();
  	  				site_control.textEditor = CKEDITOR.appendTo('productFormTextEditor', site_control.editorSettings, pageInfo.text);
  	  				
  	  				$page.bind('save',function(){
  	  					var form = $page.getForm(current);
  	  					if(!form.check) return $page.top();
  	  					
  	  					var info = {
  	  							title : form.data['p.name'],
  	  							description : form.data['p.description'],
  	  							text : site_control.textEditor.getData(),
  	  							childs_order : form.data['p.sort_type'],
  	  							type : type,
  	  							template_id : form.data['p.template'],
  	  							meta_description : form.data['p.meta_description'],
  	  							meta_keywords : form.data['p.meta_keywords'],
  	  							special_date : form.data['p.sdate'],
  	  							rewrite : form.data['p.url'],
  	  							hidden : (form.data['p.published'] == 1)? 0 : 1,
  	  							image : form.data['p.image']
  	  					}
  	  					
  	  					$page.lock();
  	  					admin_site.editPage(pageId,info, function(id){
  	  						$page.unlock();
  	  						if(typeof(site_control.treeMain) == 'object' && site_control.treeMain) {
	  	  						var node = site_control.treeMain.getSelectedNodes();
	  	  						node[0].name = info.title;
	  	  						site_control.treeMain.updateNode(node[0].name);
  	  						}
  	  						

  	  						$page.sticky('Изменение страницы','Страница успешно изменена.');
  	  						$page.back();
  	  					});
  	  				});
  	  				
			  		$('input[name="p.name"]', container).bind('change keyup mouseup',function(){
			  			var rewrite = $page.transliteral($(this).val())+'.html';
			  			$('input[name="p.url"]', container).val(rewrite);
			  		});
  	  				
  	  				$page.bind('back',function(){
  	  					$page.back();
  	  				});
  	  			});
  			});
		},
	
		removePage : function() {
			var node = site_control.treeMain.getSelectedNodes();
			if(!node || !node.length) {
				return false;
			} else {
				node = node[0];
			}

			$page.confirm('Подтвердите удаление страницы.','Вы действительно хотите удалить страницу &laquo;'+node.name+'&raquo;? Данные невозможно будет востановить!', function(){
				admin_site.deletePage(node.id, function(){
					site_control.treeMain.removeNode(node);
					$page.sticky('Удаление элемента.','Страница успешно удалена.');
				});
			});
		},
		
		loadMenu : function() {
			
			var preloadTplData = [
      		      	{menuList:['admin_site','getMenuList']}
			];
			      		
      		$page.show(['menu.list.html', 'site', preloadTplData], false, function(current){
      			$("#main_menu_nestable", current).nestable({
                    group: 1,
                    maxDepth:2
                }).on('change', site_control.changeMenuOrder);
      			
      			$page.bind('back',function(){
      				$page.back();
      			});

      			$page.bind('save',function(){

      			});
      		});
		},
		
		delItem : function(id, obj) {
			$page.confirm('Подтвердите удаление','Вы действительно хотите удалить этот пункт меню из сайта магазина? Данные будет невозможно восстановить!', function(){
				$page.lock();
				admin_site.deleteItemMenu(id, function(){
					$page.unlock();
					$page.sticky('Меню изменено.','Пункт меню был удален.');
					$(obj).closest('li').remove();
				});
			});
			
		},
		
		visibleMenuItem : function(id, obj) {
			$page.lock();
			admin_site.changeMenuVisible(id, function(hidden){
				$page.unlock();
				$(obj).find('i[name="visMenuIcon"]').removeClass('icon-eye-close').removeClass('icon-eye-open');
				$(obj).removeClass('eye_hidden');
				
				if(hidden) {
					$(obj).find('i[name="visMenuIcon"]').addClass('icon-eye-close');
					$(obj).addClass('eye_hidden');
					
				} else {
					$(obj).find('i[name="visMenuIcon"]').addClass('icon-eye-open');
				}
			});
		},
		
		addMenu : function() {
			site_control.editMenu(false);
		},
		
		editMenuItem : function(id) {
			$page.router.setRoute('/edit/'+id+'/');
		},
		
		showPagesList : function(selectedCategory) {
			
			$('#menuPagesTree', $page.current).html('');
			admin_site.getPagesListFromParent(0, function(nodes){
				var setting = {
						edit: {
							enable: false,
							showRemoveBtn: false,
							showRenameBtn: false
						},
						data: {
							simpleData: {
								enable: true
							}
						},
						callback: {
							onClick : function(event, treeId, treeNode, clickFlag) {
								var zTree = $.fn.zTree.getZTreeObj("menuPagesTree");
								var node = zTree.getSelectedNodes()[0];
								$("body").trigger("mousedown.selectPage", 'close');
								$('input[name="m.value2"]', $page.curent).val(node.id);
								$('input[name="m.titlevalue2"]', $page.curent).val(node.name);
								
								console.log(node);
							}
						},
						async : {
							func : admin_site.getPagesListFromParent,
							enable : true
						},
						
						view: {
							selectedMulti: false
						}
					};
				
				$.fn.zTree.init($("#menuPagesTree"), setting, nodes);
				$('input[name="m.titlevalue2"]', $page.curent).unbind('click').bind('click',function(){
					var offset = $(this).offset();
					$("#menuPages",  $page.curent).css({left:offset.left + "px", top:offset.top + $(this).outerHeight() + "px"}).slideDown("fast");
					$("#menuPagesTree", $page.curent).show().css({width:$(this).width()+2})
					$("body").bind("mousedown.selectPage", function(event){
						if (!(event.target.id == "menuPages" || $(event.target).parents("#menuPages").length>0) || event === "close") {
							$("#menuPages").fadeOut("fast");
							$("body").unbind("mousedown.selectPage");
						}
					});
				});
			});
		},
		
		showCatalogList : function(selectedCategory) {
			$('.item_destination .type-3', $page.current).html('');
			$page.lock();
			admin_site.getCatalogList(function(result){
				var container = $('<select><option></option></select>').attr('data-placeholder', 'Выберите категорию').addClass('span12').attr('name','m.value3');
				
				container.append($('<option value="0" '+(selectedCategory != undefined && selectedCategory == 0 ? 'selected="selected"' : '')+'>Корневой раздел каталога</option>'));
				for(var i in result) {
					var selected = (selectedCategory != undefined && selectedCategory == result[i].id)? 'selected="selected"' : '';
					container.append('<option '+selected+' value="'+result[i].id+'">'+result[i].name.lpad('- ',result[i].name.length  + result[i].level*2)+'</option>')
				}
				
				$('.item_destination .type-3', $page.current).append(container);
				$(container).chosen();
				$page.unlock();
			});
		},
		
		addPage : function(type,parent_id) {
			var preloadTplData = [
			 	{formTemplates:['admin_site','getPagesTemplates']}
			];
			
			var types = ['html', 'text', 'tpl'];
			if(types.indexOf(type) == -1) {
				$page.sticky('Не корректный тип документа!','Ошибочка вышла!');
				$page.back();
				return false;
			}
			var tpl = 'page.'+type+'.edit.html';
			
			$page.show([tpl, 'site', preloadTplData], {}, function(current){
				site_control._pageFormHtmlUI();
				site_control.textEditor = CKEDITOR.appendTo('productFormTextEditor', site_control.editorSettings, '');
				
				$page.bind('save',function(){
					var form = $page.getForm(current);
					if(!form.check) return $page.top();
					
					var info = {
							title : form.data['p.name'],
							description : form.data['p.description'],
							text : site_control.textEditor.getData(),
							parent_id : parent_id,
							childs_order : form.data['p.sort_type'],
							type : type,
							template_id : form.data['p.template'],
							meta_description : form.data['p.meta_description'],
							meta_keywords : form.data['p.meta_keywords'],
							special_date : form.data['p.sdate'],
							rewrite : form.data['p.url'],
							hidden : (form.data['p.published'] == 1)? 0 : 1	,
							image : form.data['p.image']
					}
					
					$page.lock();
					admin_site.editPage(false,info, function(id){
						$page.unlock();
						var node = {
								id:id,
								pid:parent_id,
								name:info.title,
								type:type,
								hidden:info.hidden,
								childs:0,
								isLastNode:true,
								children:false
						};
						
						if(typeof(site_control.treeMain) == 'object') {
							var parentNode = site_control.treeMain.getSelectedNodes();
							if(!parentNode || !parentNode.length) {
								parentNode = null;
							} else {
								parentNode = parentNode[0];
							}
							site_control.treeMain.addNodes(parentNode, node);
						}
						$page.back();
					});
	
					$('input[name="p.name"]', container).bind('change keyup mouseup',function(){
			  			var rewrite = $page.transliteral($(this).val())+'.html';
			  			$('input[name="p.url"]', container).val(rewrite);
			  		});
  	  				
					
				});
				
				$page.bind('back',function(){
					$page.back();
				});
				
			});
		},
		
		
		editMenu : function(id) {
			$page.lock();
						
			var showMenuForm = function(id, menuItemInfo) {
				var preloadTplData = [
				     {menuParrent:['admin_site','getMenuList', [true]]}
				];
				$page.show(['menu.edit.html', 'site', preloadTplData], {menuInfo:menuItemInfo}, function(current){
					if(id) {
						if(menuItemInfo.type_id == 3) {
							site_control.showCatalogList(menuItemInfo.value);
						} else if(menuItemInfo.type_id == 2) {
							site_control.showPagesList(menuItemInfo.value);
						} else if(menuItemInfo.type_id == 4) {
							$('input[name="m.value4"]').val(menuItemInfo.custom_link);
						}
					}
					
					
	      			$page.bind('back',function(){
	      				$page.back();
	      			});

	      			$page.bind('save',function(){
	      				$page.formMessage.clear();
	      				var form = $page.getForm(current).data;
	      				var info = {
	      					name : form['m.name'],
	      					parent_id : form['m.parent_id'],
	      					type_id : form['m.type_id'],
	      					visible : form['m.visible']
	      				};
	      				
	      				info.value = (typeof(form['m.value'+info.type_id]) != 'undefined') ? form['m.value'+info.type_id] : false;
	      				
	      				if(!info.value) {
	      					$page.top();
	      					return $page.formMessage.error('Ошибка!','Укажите назначение пункта меню!');
	      				}
	      				
	      				$page.lock();
	      				admin_site.editMenu(id, info, function(result){
	      					$page.unlock();
	      					$page.back();
	      					$page.sticky('Меню было изменено', 'Данные успешно сохранены.');
	      				});
	      			});
	      			
	      			$('.visibleMenuItem', current).toggleButtons({
	      	            label: {
	      	                enabled: "Да",
	      	                disabled: "Нет"
	      	            }
	      	        });
	      			
	      			$('.item_destination .type-2',current).bind('load.data', function(){
	      				if($(this).attr('data-loaded')) return true; 
	      				site_control.showPagesList();
	      				$(this).attr('data-loaded', true);
	      			});
	      			
      				$('.item_destination .type-3',current).bind('load.data', function(){
	      				if($(this).children().length > 0) return true; 
	      				site_control.showCatalogList();
	      			});
	      			
	      			$('select.chosen-menu-type', current).bind('change', function(){
	      				$('.item_destination .type-2',current).hide();
	      				$('.item_destination .type-3',current).hide();
	      				$('.item_destination .type-4',current).hide();
	      				$('.item_destination .type-'+$(this).val(),current).show().trigger('load.data');
	      			});
	      			
	      			$('select.chosen-menu-type', current).chosen();
	      		});				
			}
			
			if(id) {
				admin_site.getMenuItemInfo(id, function(menuItemInfo){
					showMenuForm(id, menuItemInfo);
				});		
			} else {
				showMenuForm(false, {});
			}
		},
				
		_pageFormHtmlUI : function() {
			var current = $page.current;
			$(".widget-title .tab-header", current).each(function(itemCntId){
				$(this).bind('click',function(){
					var tab_name = $(this).attr('name');
					var widgget_body = $(this).parent().parent().find('.widget-body').eq(0);
					
					$(this).parent().find('span[data-tab-select]').removeAttr('data-tab-select').css({'font-size': '13px', 'font-weight': 'normal', 'color':'#868686', 'cursor':'pointer'});
					$(this).css({'font-size': '13px', 'font-weight': 'bold', 'color':'#4C4C4C', 'cursor':'normal'}).attr('data-tab-select','1');

					widgget_body.find('.tab-body').hide();
					widgget_body.find('.tab-body[section="'+tab_name+'"]').show();
					
					delete widgget_body;
					delete tab_name;
				});
				
				// если есть атрибут, то кликаем по вкладке
				if($(this).attr('is_active')) {
					$(this).trigger('click');
				}
				
				// кликаем первый элемент, если ни один не выбран
				if(itemCntId+1 == $(".widget-title .tab-header", container).length) {
					if(!$(".widget-title span[data-tab-select]", container).length) {
						$(".widget-title .tab-header", container).eq(0).trigger('click');
					}
				}
			});
			
			// Опубликован товар
			$('.product_public_in_site_switch', container).toggleButtons({
	            label: {
	                enabled: "Да",
	                disabled: "Нет"
	            }
	        });
			
			// загрузка фото
			uploader.init({
				container : $("#pageImageUploadBar",container),
				hideUploaded : true,
				formCaption : '',
				resize : ['original'],
				done : function(info) {
					info = info[0];
					$('#pageImageUploadItem', container).html('');
					$('#pageImageUploadItem', container).append('<input type="hidden" name="p.image" value="'+info.name+'">');
					$('#pageImageUploadItem', container).append('<div><img src="'+info.name+'"><br><a href="javascript:void(0)">Удалить</a></div>');
					$('#pageImageUploadItem', container).find('a').bind('click', function(){
						$(this).closest('#pageImageUploadItem').html('');
					});
				}
			});
			
			// редактор
			site_control.editorSettings = {
					filebrowserUploadUrl : '/ru/files/upload/source/static/application/ckeditor/',
					toolbarGroups : [
		                 { name: 'document',    groups: [ 'mode', /*'document', 'doctools' */] },
		                 { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		                 { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		                 { name: 'links' },
		                 { name: 'insert' },
		                 '/',
		                 //{ name: 'forms' },
		
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
			
			
			$('input[name="p.tags"]', current).tagsInput({
	            width: 'auto'
	        });
			
		}
			
};




