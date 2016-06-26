String.prototype.replaceAll = function(search, replace){return this.split(search).join(replace);}
String.prototype.trim=function(){return this.replace(/^\s+|\s+$/g, '');};
String.prototype.lpad = function(padString, length) {
    var str = this;
    while (str.length < length)
        str = padString + str;
    return str;
};
String.prototype.rpad = function(padString, length) {
    var str = this;
    while (str.length < length)
        str+= padString;
    return str;
}

ArrayIndexOf = function(arr, obj){
	if (arr.indexOf) return arr.indexOf(obj);
	for (var i = 0; i < arr.length; ++i) {
		if (arr[i] === obj) return i;
	}
	return -1;
};

var $page = new (function(){
	var self = this;
	
	self.module = 'Cabinet';
	self.stack = [];
	self.locked = false;
	self.container = '';
	self.loader = false;
	self.current = false;
	self.showLoackedAfterAppend = false;
	self.routes = {};
	self.router = null;
	self.nextId = 0;
	
	
	self.crumbs = {
		container : false,
		title : '',
		items : [],
		loaded : false,
		urlIndex : {},
		lastQuery : {},
		
		init : function() {
			this.container = $('#crumbs');
			this.title = $(document).attr('title');
			this.container.append('<li><a href="/"><i class="icon-home"></i></a><span class="divider">&nbsp;</span></li>');
			
			// preload assigned crumbs from html
		},
		
		load : function(list, isLoaded){	
			this.lastQuery = list;
			this.checkToDeleteObjects();
			
			
			if(!this.loaded) {
				this.items = [];
				this.urlIndex = {};
				isLoaded = (isLoaded)? true : false;
				
				for(var i in list) {		
					this.add(this._findName(list[i]), list[i].url, list[i].data, isLoaded);
				}

				this.loaded = true;
			} else {
				var isNew = false,
					lasIteration = false,
					lastNewIteration = false;
				
				for(var i in list) {
					if(typeof(this.urlIndex[list[i].url]) != 'undefined') {
						//console.log(list[i].url + ' -> used');
						lasIteration = this.urlIndex[list[i].url];
					} else {
						isNew = true;
						lastNewIteration = i;
						break;
					}
				}
				
				if(isNew) {
					list.splice(0, lastNewIteration);
										
					if(lasIteration == this.items.length-1) {
						//console.log('add to end of crumbs');
					} else {
						//console.log('merge crumbs');
						while(true) {												
							if(this.items[this.items.length-1].url == this.items[lasIteration].url) {
								break;						
							} else {
								this.del(this.items.length-1);
							}						
						}
					}	

					for(var i in list) {
						var name = this._findName(list[i]);
						this.add(name, list[i].url, list[i].data, isLoaded);
						this.setTitle(this.title + ' / '+name);
					}					
				} else {					
					while(true) {												
						if(/*this.items[this.items.length-1].url == this.items[lasIteration].url && */this.items[this.items.length-1].url == self.router.getRouteString()) {
							//this.items[this.items.length-1].isLoaded = isLoaded;
							break;						
						} else {
							this.del(this.items.length-1);
						}						
					}
				}
			}
			
			
			this.updateTitle();
		},
		
		del : function(crumbId) {
			this.items[crumbId].el.removeAttr('active');
			this.items[crumbId].el.animate({width: 'toggle'}, 400, function() {
				$(this).remove();
			});

			
			this.items[crumbId-1].el.find('span.divider').removeClass('divider').addClass('divider-last');
			this.items.splice(crumbId,1);
			this.urlIndex = [];
			
			for(var i in this.items) {
				this.urlIndex[this.items[i].url] = i;
			}			
		},
		
		add : function(name, url, data, isLoaded) {
			var crumbItem = {name:name, url:url, el:null, data:data, isLoaded:isLoaded};
			this.items.push(crumbItem);
			this.container.find('span.divider-last').removeClass('divider-last').addClass('divider');
			this.container.append('<li style="display:none" active="true"><a href="#'+url+'">'+name+'</a> <span class="divider-last">&nbsp;</span></li>');
			this.items[this.items.length-1].el = $(this.container.find('li[active]:last-child').eq(0));
			this.items[this.items.length-1].el.animate({width: 'toggle'});
			this.urlIndex[url] = this.items.length-1;
		},
		
		updateTitle : function() {
			var title = this.title;
			for(var i in this.items) {
				title += ' / '+this.items[i].name;
			}
			
			this.setTitle(title);
		},
		
		back : function() {
			/*
			this.top();
			this.crumbs[this.crumbs.length-1].object.animate({width: 'toggle'}, function(){
				this.crumbs[this.crumbs.length-1].object.remove();
				this.crumbs.pop();
				this.crumbs[this.crumbs.length-1].object.find('span.divider').removeClass('divider').addClass('divider-last');
			})
			
			this.autoTitle();*/
		},
			
		checkToDeleteObjects : function() {
			for(var i in this.items) {
				if(typeof(this.items[i].data) == 'object' && typeof(this.items[i].data.delete_unload) && this.items[i].data.delete_unload) {
					$page.removeFromUrl(this.items[i].url)
				}
			}
		},
		
		setTitle : function(title){
			$(document).attr('title', title);
		},
		
		getByUrl : function(url) {
			if(typeof(this.urlIndex[url]) == 'undefined') return false;
			
			return this.items[this.urlIndex[url]];
		},
		
		getCurrentUrl : function() {
			return (this.items.length > 0)? this.getCurrent().url : false;
		},
		
		getCurrent : function() {
			return this.getByUrl(self.router.getRouteString());
		},
		
		getBackUrl : function() {
			return this.items[this.items.length-2].url;
		},
		
		reload : function() {
			this.load(this.lastQuery, true);
		},
		
		_findName : function(obj) {
			if(typeof(obj.url) != 'undefined' && obj.url == '/') {
				if(typeof(obj.name) != 'undefined') return obj.name;
				if(typeof(obj.data) != 'undefined' && typeof(obj.data.name) != 'undefined') return obj.data.name;
				if(typeof(obj._data) != 'undefined' && typeof(obj._data.name) != 'undefined') return obj._data.name;
				return this.title;
			} else {
				if(typeof(obj.name) != 'undefined') return obj.name;
				if(typeof(obj.data) != 'undefined' && typeof(obj.data.name) != 'undefined') return obj.data.name;
				if(typeof(obj._data) != 'undefined' && typeof(obj._data.name) != 'undefined') return obj._data.name;
				return '';
			}
		}
			
	};
	
	self.init = function(routes) {
		self.crumbs.init();
		self.cookie.init(); 
		self.container = $('#main-content .container-fluid').eq(0);
		
		if(typeof(routes) == 'object') {
			this.routes = self._makeRoutes(routes);
		
			if(typeof(this.routes) != 'object') {
				console.log('Routes not found!');
				console.log({input_routes:routes});
				console.log({compiled_routes:this.routes});
				return;
			}
			
			this.router = Router(this.routes);
			this.router.configure({
		        async:false,
		        html5history:false,
		        strict:false,
		        after:function(a) {
		        	self.crumbs.load(this.getUrlCallStack());
		        	
		        	var currentUrl = this.getRouteString(),
		        		pagePreloaded = self.getByUrl(currentUrl),
		        		isLoaded = false;
		        	
		        	if(pagePreloaded) {
		        		if(typeof(pagePreloaded.crumb.data.always_reload) != 'undefined' && pagePreloaded.crumb.data.always_reload) {
		        			pagePreloaded.element.hide();
		        			self.removeFromUrl(pagePreloaded.crumb.url);
		        			//console.log(':: Remove last showed page!');
		        		} else if(typeof(pagePreloaded.crumb.data.reload_callback) == 'function') {
		        			self.crumbs.getCurrent().after_on = pagePreloaded.crumb.data.reload_callback;
		        			self.crumbs.getCurrent().isLoaded =  true;
		        		} else {
		        			//console.log(':: Show pre-loaded page, without query!');
		        			self.crumbs.getCurrent().isLoaded =  true;
		        		}        		
		        	}
		        },
		        notfound : function() {
		        	self.message.error('Ошибка 404', 'Страница, которую Вы запрашивали, не найдена.');
	            }
		    });
			
			this.router.init();

			if(!window.location.hash.replace(/^#/, '')) {
				this.router.setRoute('/', null,null,true);
				this.router.on('after');
			} 
		} else {
			// code work without routes!
		}			
	};
	
	self.getLastObject = function() {
		if(!self.stack || self.stack.length == 0) return false;
		
		return this.stack[self.stack.length-1].element;
	};
	
	self.getLast = function() {
		return self.stack[self.stack.length-1];
	};
	
	self.getBeforeLastObject = function() {
		return self.stack[self.stack.length-2].element;
	};
	
	self.getBeforeLast = function() {
		return self.stack[self.stack.length-2];
	};

	self.getByUrl = function(url, onlyIndex) {
		for(var i in self.stack) {
			if(url == self.stack[i].crumb.url) {
				return (onlyIndex === true)? i : self.stack[i];
			}
		}
		
		return false;
	};
	
	self.moveToEndByUrl = function(url) {
		var emIndex = self.getByUrl(url);
		if(emIndex === false) return false;		
		self.stack = self._moveIndex(self.stack, emIndex, self.stack.length-1);
	};
	
	self.hideAllPages = function() {
		for(var i in self.stack) {
			$(self.stack[i].element).hide();
		}
	};
	
	self.removeFromUrl = function(url) {
		for(var i in self.stack) {
			if(url == self.stack[i].crumb.url) {
				self.stack[i].element.hide().remove();
				self.stack.splice(i, 1);
				self.current = self.getLastObject();
				break;
			}
		}
	};
	
	self.removeLast = function(){
		if(self.stack.length > 0) {
			self.getLastObject().remove();
			self.stack.pop();
			self.current = self.getLastObject();
		}
	};

	self.showLast  = function(){
		self.hideAllPages();
		self.getLastObject().show();
		self.top();
	};
	
	self.showLoader = function() {
		var last = self.getLastObject();
		
		self.container.append('<img class="loader" alt="Ожидайте загрузки страници" src="'+static+'img/ajax-loader.gif" style="margin:0 auto; margin-top: 15px;">');
		self.loader = $('#main-content .container-fluid .loader');
		
		if(last.length) {
			$(last).find('.row-fluid').eq(0).fadeTo("fast" , 0.5);
			
			self.loader.css({position:'absolute', 'margin-left':self.getLastObject().find(':first-child').width()/2-8,'margin-top':-(self.getLastObject().find(':first-child').height()/2-8)});
		} else {
			self.loader.css({position:'absolute', 'margin-left':$('#main-content').width()/2-8,'margin-top':-($('#main-content').find(':first-child').height()/2-8)});
		}
		
	};
	
	self.hideLoader = function(toShowOld) {

		if(toShowOld === true) {
			if(self.getLastObject()) {
				$(self.getLastObject()).find('.row-fluid').eq(0).fadeTo("fast" , 1);
			}
		}
		
		self.container.find('.loader').remove();
	};
		
	self.lock = function(callback) {
		if(typeof(callback) != 'function' && callback === true && self.locked == true) {
			self.showLoackedAfterAppend = true;
			return this;
		} else if(self.locked && callback !== true) {
			console.log('Page already locked!');
			return false;
		} 
		
		self.showLoader();
		self.locked = true;
		
		if(typeof(callback) == 'function') callback();
		
		return this;
	};
	
	self.unlock = function() {
		self.locked = false;
		self.hideLoader(true);
		
		if(self.showLoackedAfterAppend) {
			self.showLoackedAfterAppend = false;
		}
	};
	
	self.show = function(template, assignData, callback) {
		var tplModule = (typeof(template) == 'object')? template[1] : self.module;
		var tplName = (typeof(template) == 'object')? template[0] : template;
		
		self.lock();
		
		if(self.crumbs.getCurrent().isLoaded) {
			self.unlock();
			self.hideAllPages();
			self.getByUrl(self.crumbs.getCurrent().url).element.show();
			self.moveToEndByUrl(self.crumbs.getCurrent().url);
			self.top();
			
			if(typeof(self.crumbs.getCurrent().after_on) == 'function') {
				self.crumbs.getCurrent().after_on();
			}
			
			return true;
		}
						
		var TplCallback = function(html_source) {
			self.unlock();
			
			self.hideAllPages();
			
			var newPageId = self.getNextId();
			var newPage = $(html_source).attr('page-content-id',newPageId);
			
			
			self.container.append(newPage);
			
			var page = {
				id:newPageId,
				element:newPage,
				crumb:self.crumbs.getCurrent(),
				showed:true
			};

			self.stack.push(page);
			self.current = self.getLastObject();
			self.locked = false;
			
			if(self.showLoackedAfterAppend) {
				self.showLoader();
			}
			
			self.formFix(self.current);
			self.top();
			if(typeof(callback) == 'function') {
				callback(self.current);
			}
		};
		
		if((typeof(template) == 'object') && template.length == 3) {
			MAJAX.CALL('templates','get',[tplName,tplModule,assignData,TplCallback], template[2]);
		} else {
			tpl.get(tplName, tplModule, assignData, TplCallback);
		}
	};
	
	self.bind = function(action, callback) {
		var element = $('[action='+action+']',self.current);
		if(element.length) {
			element.unbind('click').bind('click',callback);
		}
	};
	
	self.add = function(name, template, assignData, callback) {
		self.show(template, assignData, callback);
		self.crumbs.add(name, '', {}, true);
	};
	
	self.shadowBack = function(callback) {
		//console.log(self.crumbs, self.crumbs.items[self.crumbs.items.length-1].url);
		
		self.crumbs.del(self.crumbs.items.length-1);
		self.crumbs.updateTitle();
		self.removeLast();
		self.showLast();
		
		if(typeof(callback) == 'function') {
			callback();
		}
	};
	
	self.back = function(callback, killPage){	
		if(self.stack.length == 1) {
			if(self.crumbs.items.length == 1) {
				console.log('It is Last level!');
				return false;
			} 
		} 

		if(killPage === true) {
			self.getLastObject().remove();
		}
		
		if(self.crumbs.items[self.crumbs.items.length-1].url == '') {
			self.crumbs.del(self.crumbs.items.length-1);
			self.crumbs.updateTitle();
			self.removeLast();
			self.showLast();
		} else {
			self.router.setRoute(self.crumbs.getBackUrl(), null,null,true);
			self.router.on('after');
		}
	};
	
	
	self.histiry_back = function(){	
		window.history.back();
	};
	
	self.update = function(){
		self.crumbs.reload();
		$page.router.update(true);		
	};
	
	self.top = function(){
		$('html,body').animate({scrollTop: 0}, 'slow');
	};
	
	self.scrolTo = function(object){
		$('html,body').animate({scrollTop: object.offset().top}, 'slow');
	};
		
	self.go = function(url) {
		$page.router.setRoute(url);
	};
	
	self.appendScripts = function (html) {
		var scripts = self._parseScriptInHtml(html);
		
		if(typeof(scripts) == 'object' && scripts.length) {
			for(var i in scripts) {
				if(scripts[i].src) {
					var script = document.createElement("script");
					script.type = "text/javascript";
					script.src = scripts[i].src;
					self.container.append(script);
				} else {
					var script = document.createElement("script");
					script.type = "text/javascript";
					script.text = scripts[i].text;
					self.container.append(script);
				}
			}
		}
	};
	
	self.formFix = function(object) {
		$(object).find('input[name="bday"]').datepicker({
		      changeMonth: true,
		      changeYear: true
		});
		
		$(object).find('input.date-picker').datepicker();
			
		$('.basic-toggle-button',object).toggleButtons(); 
		$("input[type=checkbox]:not(.toggle,.nouniform), input[type=radio]:not(.toggle,.nouniform)", object).uniform(); 
		$(".chosen-with-diselect", object).chosen({allow_single_deselect: true});
		$(".chosen", object).chosen();  
		$(".popovers", object).popover(); 
		$(object).find(".tooltips").each(function(){
			$(this).tooltip();
		})
	}
	
	self.getForm = function(object,useCheck) {
		var res = {};
		var require = {};
		var useCheck = (useCheck === false)? false : true;
		var checkOk = true;
		
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
					self._domFindParent($(this),'.control-group', function(obj){
						$(obj).removeClass('error').addClass('error');
					});
				} else {
					self._domFindParent($(this),'.control-group', function(obj){
						$(obj).removeClass('error');
					});
				}
			}
		});
		
		return (useCheck)? {check:checkOk, data:res} : res;
	};
	
	self.getFormExtended = function(container) {
		var res = {};
		var push_counters = {};
		var patterns = {
                "validate": /^[a-zA-Z][a-zA-Z0-9_\-\.]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                "key":      /[a-zA-Z0-9_\-\.]+|(?=\[\])/g,
                "push":     /^$/,
                "fixed":    /^\d+$/,
                "named":    /^[a-zA-Z0-9_\-\.]+$/
        };
		
		var push_counter = function(key) {
			 if(push_counters[key] === undefined){
	                push_counters[key] = 0;
	         }
			 
	         return push_counters[key]++;
		};
		
		var build = function(base, key, value){
            base[key] = value;
            return base;
        };
        
        $(container).find('input, textarea, select').each(function(){
			var type = $(this).attr('type'),
				name = $(this).attr('name'),
				value = $(this).val();
		
			if((type == 'radio' || type == 'checkbox')  && !$(this).is(':checked')) {
				return;
			}
		
			// manual skip
			if($(this).attr('skip-data')) return;
			
			// skip invalid keys
            if(!name || !patterns.validate.test(name) || $(this).attr('skip_get_form')){
                return;
            }

			var k,
            	keys = name.match(patterns.key),
            	merge = value,
            	reverse_key = name;
			
			while((k = keys.pop()) !== undefined){
                // adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                // push
                if(k.match(patterns.push)){
                    merge = build([], push_counter(reverse_key), merge);
                }

                // fixed
                else if(k.match(patterns.fixed)){
                    merge = build([], k, merge);
                }

                // named
                else if(k.match(patterns.named)){
                    merge = build({}, k, merge);
                }
                
                res = $.extend(true, res, merge);
            }
        });
		
        return res;
	};
	
	self.sticky = function(title, text) {
		$.gritter.add({
            title: title,
            text: text,
            image: '',
            sticky: false,
            time: 4000
        });
	};
	
	self.getNextId = function() {
		self.nextId++;
		return 'pagecontiner'+self.nextId;
	}
	
	self.makeUniqId = function(len) {
		var text = "";
		var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		len = (len)? len : 12;
		for( var i=0; i < len; i++ )
		   text += possible.charAt(Math.floor(Math.random() * possible.length));

		return text;
	}
	
	self.confirm = function(title, text, callback, cancelCallback){
		var modal = $('<div  class="modal hide fade" tabindex="-1" role="dialog"  aria-hidden="true">'+
		    		'<div class="modal-header">'+
		    			'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
		    			'<h3>'+title+'</h3>'+
		    		'</div>'+
		    		'<div class="modal-body">'+
		    			'<p>'+text+'</p>'+
		    		'</div>'+
		    		'<div class="modal-footer">'+
		    			'<button class="btn btn-cancel" data-dismiss="modal" aria-hidden="true">Отмена</button>'+
		    			'<button data-dismiss="modal" class="btn btn-success btn-ok">Ok</button>'+
		    		'</div>'+
		    	  '</div>');
		
		modal.find('button.btn-ok').click(function(event) {
			if(typeof(callback) == 'function') {
				callback();
			}
		});
		
		modal.find('button.btn-cancel').click(function(event) {
			if(typeof(cancelCallback) == 'function') {
				cancelCallback();
			}
		});
		
		modal.modal('show'); 	
	};
	
	self.alert = function(title, text, callback) {			
		var modal = $('<div  class="modal hide fade" tabindex="-1" role="dialog"  aria-hidden="true">'+
		    		'<div class="modal-header">'+
		    			'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
		    			'<h3>'+title+'</h3>'+
		    		'</div>'+
		    		'<div class="modal-body">'+
		    			'<p>'+text+'</p>'+
		    		'</div>'+
		    		'<div class="modal-footer">'+
		    			'<button data-dismiss="modal" class="btn btn-success btn-ok">Ok</button>'+
		    		'</div>'+
		    	  '</div>');
		
		modal.find('button.btn-ok').click(function(event) {
			if(typeof(callback) == 'function') {
				callback();
			}
		});
		
		modal.modal('show'); 	
	};
	
	self.message = {
		notice : function(name, text) {
			this.system('alert alert-info', name, text);
		},
		error : function(name, text) {
			this.system('alert alert-error', name, text);
		},
		warning : function(name,text) {
			this.system('alert', name, text);
		},
		success : function (name,text) {
			this.system('alert alert-success', name, text);
		},
		system : function (type,name,text) {
			var tpl = $('<div class="'+type+'"><button class="close" data-dismiss="alert">×</button><strong>'+name+'</strong>'+text+'</div>');
			tpl.hide();
			$('#crumbs').before(tpl);
			tpl.slideToggle();
		}
	};
	
	
	self.formMessage = {
			clear : function() {
				$('div.alert',self.current).remove();
			},
			notice : function(name, text) {
				this.system('alert alert-info', name, text);
			},
			error : function(name, text) {
				this.system('alert alert-error', name, text);
			},
			warning : function(name,text) {
				this.system('alert', name, text);
			},
			success : function (name,text) {
				this.system('alert alert-success', name, text);
			},
			system : function (type,name,text) {
				var tpl = $('<div class="'+type+'"><button class="close" data-dismiss="alert">×</button><strong>'+name+'</strong> '+text+'</div>').hide();
				$('form', self.current).eq(0).prepend(tpl);
				tpl.slideToggle();
			}
	};
	
	self.download = function(link) {
		$('body').append($('<iframe src="'+link+'"></iframe>').css({width:1, height:1, position:'absolute',left:-90000}).bind('ready', function(){
			alert('iframe ready');
		}));
	};
	
	self.print = function(element) {
		var tplFrame = $('<div id="_printFrame"></div>').css({'background-color':'#FFF'/*position:'absolute',left:'-99999',top:'-99999',width:'1px',height:'1px'*/});
		tplFrame.on('ready', function(){
			console.log('frameReady');
		});
		if(element && typeof(element) == 'object') {
			tplFrame.append($(element).clone());
		} else {
			tplFrame.append($page.current.clone());
		}
		
		$('body').prepend(tplFrame);
		$('#header, #container, #footer').hide();
		window.print();
		$('#_printFrame').remove();
		$('#header, #container, #footer').show();
	};
	
	self.cookie = {
	        // Initialize by splitting the array of Cookies
		init: function () {
			var allCookies = document.cookie.split('; ');
			for (var i=0;i<allCookies.length;i++) {
				var cookiePair = allCookies[i].split('=');
				this[cookiePair[0]] = cookiePair[1];
			}
		},
	        // Create Function: Pass name of cookie, value, and days to expire
		create: function (name,value,secs) {
			if (secs) {
				var date = new Date();
				date.setTime(date.getTime()+secs);
				var expires = "; expires="+date.toGMTString();
			}
			else var expires = "";
			document.cookie = name+"="+value+expires+"; path=/";
			this[name] = value;
		},
		
		get: function(name) {
			return (typeof(this[name]) == 'undefined')? null : this[name];
		},
	        // Erase cookie by name
		erase: function (name) {
			this.create(name,'',-1);
			this[name] = undefined;
		}
	};
	
	self.transliteral = function(text,space) {
		space = (typeof(space) == 'undefined')? '_' : space;
		var transl = {
				'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'e', 'ж': 'zh',
				'з': 'z', 'и': 'i', 'й': 'j', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n',
				'о': 'o', 'п': 'p', 'р': 'r','с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h',
				'ц': 'c', 'ч': 'ch', 'ш': 'sh', 'щ': 'sh','ъ': space, 'ы': 'y', 'ь': space, 'э': 'e', 'ю': 'yu', 'я': 'ya',
				' ': space, '_': space, '`': space, '~': space, '!': space, '@': space,
				'#': space, '$': space, '%': space, '^': space, '&': space, '*': space,
				'(': space, ')': space,'-': space, '\=': space, '+': space, '[': space,
				']': space, '\\': space, '|': space, '/': space,'.': space, ',': space,
				'{': space, '}': space, '\'': space, '"': space, ';': space, ':': space,
				'?': space, '<': space, '>': space, '№':space
				};
		text = text.toLowerCase();
		var curent_sim = '';
		var result = '';
		for(i=0; i < text.length; i++) {
		    if(transl[text[i]] != undefined) {
		         if(curent_sim != transl[text[i]] || curent_sim != space){
		             result += transl[text[i]];
		             curent_sim = transl[text[i]];
		         }                                                                            
		    } else {
		        result += text[i];
		        curent_sim = text[i];
		    }                             
		}     
		
		result = result.replace(/^-/, '');
		result = result.replace(/-$/, '');
		result = result.replace(new RegExp('[\\'+space+']{2,}', 'gm'), space);
		
		return result;
	};
	
	
	self._domFindParent = function(object, is, callback) {
		var itterationLimit = 10;
		var curerntIteration = 0;
		var currentObject = $(object);
		
		while(true) {
			curerntIteration++;
			if($(currentObject).parent().is(is)) {
				if(typeof(callback) == 'function') {
					callback($(currentObject).parent());
					break;
				} else {
					return $(currentObject).parent();
				}
			} else {
				if(curerntIteration >= itterationLimit) break;
				currentObject = currentObject.parent();
			}
		}
		
		return false;
	};
	
	self._parseScriptInHtml = function(html) {
		var rexp = /<script.+?src=\"(.+?\.js(?:\?v=\d)*).+?script>/ig, 
			rematch = false;
			scripts = [];
		
		while (rematch = rexp.exec(html)) {
			scripts.push({'src':rematch[1], 'text':false});
		}
		
		rexp = /<script.+?>([\s\S]*?)<\/script>/ig;
		rematch = false;
		
		while (rematch = rexp.exec(html)) {
			rematch = rematch[1].trim();
			if(rematch.length > 0) {
				scripts.push({'src':false, 'text':rematch});
			}
		}
		
		return scripts;
	};

	self._makeRoutes = function(routes, prefix) {
		if(typeof(routes) != 'object') return false;
		
		var out = {};
		var prefix = prefix || '';
		
		for(var i in routes) {
			if(typeof(routes[i]) == 'object' && (typeof(routes[i].on) != 'undefined' || typeof(routes[i].tpl) != 'undefined')) {
				
				for(var s in routes[i]) {
					if(s.substr(0,1).match(/[a-zA-Z]/) && s.substr(s.length-1,1).match(/[a-zA-Z]/)) {
						var subprefix = ((prefix.substr(prefix.length-1,1) == '/' && i.substr(0,1) == '/')? prefix.substr(0,prefix.length-1)+i : prefix+i).replace(/\/\//g, '/');

						if(typeof(out[subprefix]) != 'object') {
							out[subprefix] = {_data:{}};
						}
						
						if(s != 'on' &&  s != 'tpl') {
							out[subprefix]._data[s] = routes[i][s];
						} else {
							out[subprefix][s] = routes[i][s];
						}
					} else {
						var subprefix = ((prefix+i == '/')? '' : prefix+i).replace(/\/\//g, '/');
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
							$page.show(data.tpl);
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
	
	self._extend = function(dst, src){
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
	
	self._moveIndex = function (object, old_index, new_index) {
	    if (new_index >= object.length) {
	        var k = new_index - object.length;
	        while ((k--) + 1) {
	        	object.push(undefined);
	        }
	    }
	    object.splice(new_index, 0, object.splice(old_index, 1)[0]);
	    return object; 
	};
})();

var $formater = new (function(){
	var self = this;
	
	self.numberDelimiter = '.';
	
	self.humanNumber = function(num) {
		var strnum = num.toString();
			strnum = strnum.replace(new RegExp("(^\\d{"+(strnum.length %3 || -1)+"})(?=\\d{3})"),"$1"+self.numberDelimiter).replace(/(\d{3})(?=\d)/g,"$1"+self.numberDelimiter);
		
		return strnum;
	};
	
	self.pluralNumber = function(number, one, two, more) {
		return self.humanNumber(number)+' '+self.plural(number, one, two, more);
	};
	
	self.pluralIdenty = function(a) {
		if ( a % 10 == 1 && a % 100 != 11 ) {
			return 0; 
		} else if ( a % 10 >= 2 && a % 10 <= 4 && ( a % 100 < 10 || a % 100 >= 20)) {
			return 1;
		} else {
			return 2;
		}
	}
	
	self.plural = function(cnt, str1, str2, str3) { // кол-во, один, два, много
		switch (self.pluralIdenty(cnt)) {
			case 0: return str1;
			case 1: return str2;
			default: return str3;
		}
	}	
})();

