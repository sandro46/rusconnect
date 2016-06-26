var cabinet = (function(){
	var self= this;
	
	self.api = userCabinet;
	self.routes = false;
	self.chat = false;
	self.page = false;	
	

	self.init = function(routes) {		
		self.page = $('#cabinetMainContent');
		self.page.bind('page.ready', function(){
			self.page.action('back', function(){
				history.back();
			});
			self.unlock();
		});
		self.page.action = function(name, callback){
			$(this).find('a[action="'+name+'"], button[action="'+name+'"]').bind('click', callback);
		};
		
		self.routes = self._makeRoutes(routes);
		
		if(typeof(self.routes) != 'object') {
			self.error('Ошибка 503', 'Не найдена таблица маршрутизации.');
			return;
		}
		
		self.router = Router(self.routes);
		self.router.configure({
	        async:false,
	        html5history:false,
	        strict:false,
	        before: function() {
	        	self.lock();
	        },
	        after:function(a) {
	        	self.page.find('.errorRegisterMessage').remove();
	        	var current = this.getUrlCallStack().pop();
	        	if(typeof(current) == 'object' && typeof(current.data) == 'object' && typeof(current.data.alias) != 'undefined') {
	        		$('.cabinetMain .cabinetMenu').find('li.active').removeClass('active');
		        	$('.cabinetMain .cabinetMenu').find('li[alias="'+current.data.alias+'"]').addClass('active');
	        	}
	        },
	        notfound : function() {
	        	self.error('Ошибка 404', 'Страница, которую Вы запрашивали, не найдена.');
            }
	    });

		self.router.init();

		if(!window.location.hash.replace(/^#/, '')) {
			self.router.setRoute('/', null,null,true);
			self.router.on('after');
		} 
		
		$('.cabinetMain').find('a[action="logout"]').click(function(){
			ajaxLogout(function(){
		        document.location.href = '/ru/shop/login/';
		    });
		});
	};

	self.initNodeChat = function() {		
		if(!$('#nodeChatbox').attr('binded')) {
			var scrol = $('#nodeChatbox .chatbox').addClass('scroll-pane').jScrollPane();
			var host = getNodeChatHost();
			if(!host) {
				$('#nodeChatbox .chatbox').html('<p class="error">Временно недоступен!</p>');
				return false;
			}

			self.chat = new nodechat();
			self.chat.MyMessageBox = scrol.data('jsp').getContentPane();
			self.chat.MyMessageBoxApi = scrol;
			self.chat.init({
				iourl : 'http://'+host,
				username : $('#nodeChatbox').data('user_name') + ' ' + $('#nodeChatbox').data('user_surname').substr(0,1)+'.',
				message : function(data) {
					var tplInc = $('#nodeChatbox script[name="userMessageincommingTemplate"]').html();
					var tplOut = $('#nodeChatbox script[name="userMessageTemplate"]').html();
										
					if(data.type == 2) {
						var tpl = twig({data: tplOut});
					} else {
						var tpl = twig({data: tplInc});
					}
					
					var time = new Date();
					time.setTime(data.timestamp * 1000);
					data.timestamp = time.format('dd.mm.YYYY H:i');
					
					var html = tpl.render({
						name: data.from_username,
						date: data.timestamp,
						text: data.text
					});
					
					self.chat.MyMessageBox.append(html);
					self.chat.MyMessageBoxApi.data('jsp').reinitialise();
					self.chat.MyMessageBoxApi.data('jsp').scrollToBottom();
				}
			});
							
			$('#nodeChatbox').find('a[action="sendMessage"]').click(function(){
				var message = $('#nodeChatbox').find('textarea[name="messageText"]').val();
				if(message.length) {
					var data = {
							text:self.chat.makeText(message),
							timestamp:(new Date()).getTime()
					};
					
					self.chat.send(data, true);
						
					var message = {
						from_username : $('#nodeChatbox').data('user_name') + ' ' + $('#nodeChatbox').data('user_surname').substr(0,1)+'.',
						timestamp : (new Date()).format('d.m.Y H:i'),
						text : self.chat.makeText(message)
					}
					
					var tpl = twig({data: $('#nodeChatbox script[name="userMessageTemplate"]').html()});
					var tpl = tpl.render({
						name: message.from_username,
						date: message.timestamp,
						text: message.text
					});
					
					
					self.chat.MyMessageBox.append(tpl);
					self.chat.MyMessageBoxApi.data('jsp').reinitialise();
					self.chat.MyMessageBoxApi.data('jsp').scrollToBottom();
					$('#nodeChatbox').find('textarea[name="messageText"]').val('');
				}
			});
		}
	};
	
	self.lock = function() {
		self.page.oLoader({
		  wholeWindow: true, 
		  lockOverflow: false, 
		  backgroundColor: '#000',
		  fadeInTime: 100,
		  fadeLevel: 0.1,
		  image: cabinetStaticPath + 'images/ajax-loader.gif',  
		});
	};
	
	self.unlock = function() {
		self.page.oLoader('hide');
	};
	
	self.error = function(title, message) {
		var block = $('<p class="errorRegisterMessage"><b>'+title+'</b> '+message+'</p>');
		self.page.prepend(block);
		block.slideToggle();
	};
	
	self.formError = function(title, message) {
		var block = $('<p class="errorRegisterMessage formErrorMessage"><b>'+title+'</b> '+message+'</p>');
		self.page.find('form').before(block);
		block.slideToggle();
	};
	
	self.flashMessage = function(type, title, message) {
		var className = type + 'Message';
		var block = $('<p class="'+className+' formErrorMessage"><b>'+title+'</b> '+message+'</p>');
		self.page.find('form').before(block);
		block.fadeIn();
		setTimeout(function(){
			block.fadeOut();
		}, 3000);
	};
	
	self.clearFormError = function() {
		self.page.find('.formErrorMessage').remove();
	};
	
	self.getForm = function(useCheck) {
		var res = {}, require = {}, checkOk = true, object = self.page;
		useCheck = (useCheck === false)? false : true;
		
		$(object).find('input, textarea, select').each(function(){
			var type = $(this).attr('type');
			if((type == 'radio' || type == 'checkbox')  && !$(this).is(':checked')) {
				return;
			}
			
			if(!$(this).attr('name') || $(this).attr('name') == undefined) return;
			res[$(this).attr('name')] = $(this).val();
			if(useCheck && $(this).is('.require')) {
				if(!res[$(this).attr('name')] || res[$(this).attr('name')] == '') {
					checkOk = false;
					if(!$(this).closest('.control-group').is('.error')) {
						$(this).closest('.control-group').addClass('error');
					}
				} else {
					$(this).closest('.control-group').removeClass('error');
				}
			}
		});
		
		return (useCheck)? {check:checkOk, data:res} : res;
	};
		
	self._makeRoutes = function(routes, prefix) {
		if(typeof(routes) != 'object') return false;
		var out = {}, prefix = prefix || '', subprefix;
		
		for(var i in routes) {
			if(typeof(routes[i]) == 'object' && (typeof(routes[i].on) != 'undefined' || typeof(routes[i].tpl) != 'undefined')) {
				for(var s in routes[i]) {
					if(s.substr(0,1).match(/[a-zA-Z]/) && s.substr(s.length-1,1).match(/[a-zA-Z]/)) {
						subprefix = ((prefix.substr(prefix.length-1,1) == '/' && i.substr(0,1) == '/')? prefix.substr(0,prefix.length-1)+i : prefix+i).replace(/\/\//g, '/');
						if(typeof(out[subprefix]) != 'object') {
							out[subprefix] = {_data:{}};
						}
						
						if(s != 'on' &&  s != 'tpl') {
							out[subprefix]._data[s] = routes[i][s];
						} else {
							out[subprefix][s] = routes[i][s];
						}
					} else {
						subprefix = ((prefix+i == '/')? '' : prefix+i).replace(/\/\//g, '/');
						out = self._extend(out, self._makeRoutes(routes[i], subprefix));
					}
				}
				
				if(typeof(out[subprefix]) == 'undefined' && typeof(out[i]) != 'undefined') {
					subprefix = '/';
				}
								
				if(typeof(out[subprefix]) != 'undefined' && typeof(out[subprefix].on) != 'function' && typeof(out[subprefix].tpl) != 'undefined') {
					out[subprefix].on = function(){
						var data = arguments[arguments.length-1];
						if(typeof(data.tpl) !='undefined') {
							//$page.show(data.tpl);
							console.log('show tpl');
						} else {
							console.log('Not valid callback to page');
						}
					}
					out[subprefix]._data.tpl = out[subprefix].tpl;
					delete out[subprefix].tpl;
				}
			}
		}
		
		return out;
	};
	
	self._extend = function(dst, src) {
		var args = arguments, i = 1, n = args.length, key;
		dst = dst || {};

		for( ; i < n; i++ ){
			src = args[i];
			if( src && /object|function/.test(typeof src) ){
				for( key in src ){
					if( src.hasOwnProperty(key) ){
						dst[key] = src[key];
					}
				}
			}
		}

		return dst;
	};
	

	
	return {
		init: function(routes) {
			self.initNodeChat();
			self.init(routes);
		},
		
		main_page : function() {
			userCabinet.getAllInfo(function(data) {
				tpl.get('index.html', 'cabinet', function(template){
					var tpl = twig({data:template}).render(data);
					self.page.html(tpl).trigger('page.ready');				
				});
			});
		},
		
		personal: function() {
			userCabinet.getClientInfo(function(data){
				tpl.get('personal.html', 'cabinet', function(template){
					var tpl = twig({data:template}).render(data);	
					self.page.html(tpl).trigger('page.ready');		
					self.page.action('save', function(){
						self.clearFormError();
						var form = self.getForm();
						
						if(!form.check) {
							self.formError('Ошибка!','Заполните все поля отмеченные звездочкой.');
							return false;
						} 
						
						userCabinet.updatePersonal(form.data, function(result){
							if(!result) {
								self.formError('Ошибка!','Не удалось сохранить данные. Попробуйте позднее.');
							} else if(typeof(result) == 'object') {
								self.formError('Ошибка!', result.message);
							} else {
								self.flashMessage('success', '', 'Данные успешно сохранены!');
							}
						});
					});
				});
			});
		},
				
		personalEditPass: function() {
			tpl.get('personal.passchange.html', 'cabinet', function(template){
				var tpl = twig({data:template}).render();
				self.page.html(tpl).trigger('page.ready');		
				self.page.action('save', function(){
					self.clearFormError();
					var form = self.getForm();
					
					if(!form.check) {
						self.formError('Ошибка!','Заполните все поля отмеченные звездочкой.');
						return false;
					} 
					
					if(form.data.newpass1 != form.data.newpass2) {
						self.formError('Ошибка!','Пароли не совпадают. Проверьте правильность ввода.');
						return false;
					}
					
					userCabinet.updatePassword(form.data.oldpass, form.data.newpass1, function(result) {
						if(!result) {
							self.formError('Ошибка!','Не удалось сохранить данные. Попробуйте позднее.');
						} else if(typeof(result) == 'object') {
							self.formError('Ошибка!', result.message);
						} else {
							self.flashMessage('success', '', 'Данные успешно сохранены!');
						}
					});
				});
			});
		},
		
		addressList: function() {
			tpl.get('address.html', 'cabinet', function(template){
				var tpl = twig({data:template}).render();
				self.page.html(tpl).trigger('page.ready');				
			});
		},
		
		addressAdd: function() {
			tpl.get('address.add.html', 'cabinet', function(template){
				var tpl = twig({data:template}).render();
				self.page.html(tpl).trigger('page.ready');				
			});
		},
		
		addressRemove: function(id) {
			console.log('call addressRemove');
		},
		
		addressEdit: function(id) {
			// load address data 
			
			tpl.get('address.edit.html', 'cabinet', function(template){
				var tpl = twig({data:template}).render();
				self.page.html(tpl).trigger('page.ready');				
			});
		},
		
		addressMainEdit: function() {
			console.log('edit main address');
		},
		
		discount: function() {
			// load discount data 
			tpl.get('discount.html', 'cabinet', function(template){
				var tpl = twig({data:template}).render();
				self.page.html(tpl).trigger('page.ready');				
			});
		},
		
		
		billing: function() { 
			console.log('call billing');
		},
		
		billingEdit: function() {
			console.log('call billingEdit');
		},
		
		history: function() {
			userCabinet.getOrders(function(data){
				tpl.get('history.html', 'cabinet', function(template){
					var tpl = twig({data:template}).render({orders:data});
					self.page.html(tpl).trigger('page.ready');				
				});
			})
		},
		
		historyInfo : function(id) {
			
		}
	}
})();

Date.prototype.format = function(format) //author: meizz
{
  var o = {
    "m+" : this.getMonth()+1, //month
    "d+" : this.getDate(),    //day
    "H+" : this.getHours(),   //hour
    "i+" : this.getMinutes(), //minute
    "s+" : this.getSeconds(), //second
    "Y+" : this.getFullYear()
  }

  //if(/(Y+)/.test(format)) format=format.replace(RegExp.$1,
  //d  (this.getFullYear()+"").substr(4 - RegExp.$1.length));
  for(var k in o)if(new RegExp("("+ k +")").test(format))
    format = format.replace(RegExp.$1,
      RegExp.$1.length==1 ? o[k] :
        ("00"+ o[k]).substr((""+ o[k]).length));
  return format;
}

function getNodeChatHost() {
	if(!$('#socketio').length) return false;
	var rexp = /\/\/([a-zA-Z\.\:0-9\-]{0,})\//;
	var url = $('#socketio').attr('src');
		url = rexp.exec(url);
		
	if(typeof(url) == 'object' && typeof(url[1]) != 'undefined') {
		return url[1];
	} 
	
	return false;
}
