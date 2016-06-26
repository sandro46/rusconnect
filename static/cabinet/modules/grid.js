// Copyright 2010-2014 by Alexey Pshenichniy / "CloudServices Framework"
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//   http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

var GridInstance = function(instance) {
	this.instance = instance;
	this.page = 0;
	this.limit = 10;
	this.sort_by = '';
	this.sort_type = '';
	this.filters = null;
	this.rows = false;
	this.cols = false;
	this.view = false;
	this.params = null;
	this.settings = null;
	this.template = '';
	this.dom = null;
	this.ajax = null;
	this.pagination = '';
		
	this.groupAction = {
		instance : this,
		name : this.instance,
		data : {include:[], exclude:[],selectAll:false},
		handlers : {copy:false, paste:false, cut:false, remove:false},
		settings : false,
		container : null,
		events : {}
	};

	this._callbacks = {};
	this._messages = [];
	this._rowCallback = false;
	this._loadInitiated = false;
	
	/* Public methods */
	this.init = function() {
		// check config
		if(typeof(this.params) != 'object' || typeof(this.params['class']) != 'string' || typeof(this.settings.cols) != 'object' || typeof(this.params.method) != 'string') {
			this._debug('error param set!');
			return false;
		};
		
		// base init
		this.filters = new this.iobject();
		this.rows = new this.iobject();
		this.cols = new this.iobject(this.settings.cols);
		this.limit = this.settings.limit;
		this.view = this.settings.view;
		this.dom = $('#grid-'+instance);
		
		this.on('showLoader', function(){
			$page.lock(true);
		});
		
		this.on('hideLoader', function(){
			$page.unlock();
		});
		
		
		$('#grid-'+this.instance+' .grid-body').hide();
		
		this._appendUserSettings();
		
		if(typeof(this.settings.user_settings) != 'undefined' && this.settings.user_settings) {
			this._prepareGridMenuFromWidget();
		}
		
		if(typeof(this.settings.group_actions) != 'undefined' && this.settings.group_actions && this.groupAction.settings) {
			this.groupAction.init();
		}
		
		if(typeof(this.settings.callback) != 'undefined') {
			this._rowCallback = this.settings.callback;
		}
		
		// default setting
		if(typeof(this.settings.autoload) != 'undefined') {
			this.settings.autoload = (this.settings.autoload)? true : false;
		} else {
			this.settings.autoload = true;
		}
		
		// sort on start
		if(this.cols.find(['sorted',true])) {
			var sortColumn = this.cols.find(['sorted',true]);
			this.sort_by = sortColumn.name;
			this.sort_type = sortColumn.sort_type;
			delete(sortColumn);
		} else if(this.cols.find(['name','id'])) {
			this.sort_by = 'id';
			this.sort_type = 'desc';
		} else {
			this.sort_by = '1';
			this.sort_type = 'desc';
		};
		
		this.template = $('#grid-'+this.instance+' .grid-body').html();
		this.pagination = $('#grid-'+this.instance+'-pagiation').html();
		this._makeFilters();

		
		
		if(typeof(jQuery) == 'function' && jQuery.isReady && this.settings.autoload) {
			this._loadInitiated = true;
			this.start();
		}
	},
		
	this.start = function(callback) {
		this.emit("beforeDataLoad");
		this.emit("showLoader");
		
		if(typeof(callback) == 'function') {
			this.once('ready', callback)
		}

		if(!this.ajax && !this.params['class']) {
			this._debug('Query object not set(1).');
			this._destruct();
			return false;
		} else if(!this.ajax && typeof(window[this.params['class']]) != 'object') {
			this._debug('Query object not set(2).');
			this._destruct();
			return false;
		} else if(!this.ajax && typeof(window[this.params['class']][this.params.method]) != 'function') {
			this._debug('Query method not set.');
			this._destruct();
			return false;
		} else if(!this.ajax) {
			this.ajax = window[this.params['class']];
		}

		MAJAX.fromGrid = true;
		
		var sort_by = false;
		
		if(this.sort_by) {
			var _col = this.cols.find(['name', this.sort_by]);
			if(_col) {
				if(typeof(_col.sort_by) != 'undefined') {
					sort_by = _col.sort_by;
				} else {
					sort_by = this.sort_by;
				}
			} else {
				sort_by = this.sort_by;
			}
		} 
		
		this.ajax[this.params.method](this.instance, (this.page*this.limit), this.limit, sort_by, this.sort_type, this.filters.toObject(), this.response);
	},
	
	this.click = function(obj) {
		if(this._locked()) return false;
		
		var row = this.rows.get(parseInt($(obj).attr('local-row-id')));
		 	
		if(typeof(this.settings.click) == 'object') {
			if(typeof(this.settings.click.cb) == 'function') {
				return this.settings.click.cb(row);
			}
			
			if(typeof(this.settings.click.method) == 'undefined') {
				console.log('Grid row click function not assigned!');
				return false;
			}

			if(typeof(this.settings.click.method) != 'function') {
				var _func = (this.settings.click.method.indexOf('.') >= 0)? this._extractFunctionFromObject(this.settings.click.method.split('.')) : this.settings.click.method;

				if(typeof(_func) != 'function' && typeof(window[_func]) != 'function') {
					console.log('Grid row click function not found! Assigned: '+this.settings.click.method);	
					return false;
				}
				
				if(this.settings.click.method.indexOf('.') >= 0) {
					this.settings.click.context = this.settings.click.method.substr(0,this.settings.click.method.indexOf('.'));
					if(typeof(window[this.settings.click.context]) != 'undefined') {
						this.settings.click.context = window[this.settings.click.context];
					} else {
						this.settings.click.context = window;
					}
				} 
				
				this.settings.click.method = _func;
			}
			
			if(!this.settings.click.context) {
				this.settings.click.context = window;
			}
			
			var newParams = [];

			if(typeof(this.settings.click.params) == 'object') {
				for(var i in this.settings.click.params) {
					var _curentParam = this.settings.click.params[i];
					
					if(_curentParam.indexOf('{') == 0 && _curentParam.indexOf('}') == _curentParam.length -1) {
						_curentParam = _curentParam.substr(1,_curentParam.length -2);
						if(typeof(row[_curentParam]) != 'undefined') {
							newParams.push(row[_curentParam]);
						} else {
							newParams.push(this.settings.click.params[i]);
						}
						//
					} else {
						newParams.push(this.settings.click.params[i]);
					}
				}
			}
			
			newParams.push(row);
			
			this.settings.click.method.apply(this.settings.click.context, newParams);
			
			return true;			
		} else if(typeof(this.settings.click) == 'string') {
			var tpl = new templates(this.settings.click, true);
			for(var i in row) {
				tpl.assign(i, row[i]);
			}
			tpl.render();
			
			try {
				eval(tpl.out);
			} catch (e) {
				
			}
		}
	};
	
	this.addFilter = function(key,value) {
		this.filters.set(key,value);
		this.page = 0;
		return this;
	},
	
	this.removeFilter = function(key) {
		this.filters.rm(key);
		this.page = 0;
		return this;
	},
	
	this.clearFilter = function() {
		this.filters.clear();
		this.page = 0;
		return this;
	},
	
	this.sort = function(object) {
		if(this._locked()) return false;
		
		var sortby = $(object).attr('name');
		var col = this.cols.find(['name',sortby]);
		if(col.sort !== true) return false;
		
		if(this.sort_by == col.name) {
			this.sort_type = (this.sort_type == 'desc')? 'asc' : 'desc';
		} else {
			this.sort_by = col.name;
			this.sort_type = 'asc';
		}
				
		this.start();
		return this;
	},
	
	this.response = function(instanceName, response) {
		var instance = grid[instanceName],
			useExtended = false,
			useRowClass = false,
			lastRowClass = 0,
			realRows = 0,
			_currentRow, tpl, extendHtml, ext_tpl;
		
		instance.emit('afterDataLoad');
		instance.dom.find('.grid-body').html('');
		instance._saveResponse(response);
		
		var extendHtml = instance._getFromTplCols();
		
		for(var i in response.data) {
			realRows++;
			var currentRowData = response.data[i];
			
			if(instance._rowCallback !== false){
				eval('var _out = '+instance._rowCallback+'(currentRowData, instance.cols)');
				$('#grid-'+instanceName+' .grid-body').append(_out);
				continue;
			}
			
			tpl = new templates(instance.template, true);
			tpl.assign('__grid_local_row_id', i);
			instance.rows.set(i,currentRowData);

			if(!useRowClass) {
				if(typeof(instance.settings['class']) == 'object') {
					tpl.assign('__row_class__', instance.settings['class'][lastRowClass]);
					lastRowClass = (lastRowClass == 1)? 0 : 1;
				} else {
					tpl.assign('__row_class__', '');
				}				
			}
			
			

			var extendCol = false;
			var ext_tpl = false;
			var usedCollFromExtended = {};
			
			if(extendHtml) {
				for(var i in extendHtml) {
					var extendCol = extendHtml[i];
					try{
						ext_tpl = new templates(extendCol.tpl);
						for(var b in currentRowData) {
							ext_tpl.assign(b, currentRowData[b]);
						}
						ext_tpl.render();
						tpl.assign(extendCol.name, ext_tpl.out);
						usedCollFromExtended[extendCol.name] = true;
					} catch(e) {
						ext_tpl = false;
						tpl.assign(extendCol.name, currentRowData[name]);
					}
				}
			}
			
			for(var a in currentRowData) {
				if(typeof(usedCollFromExtended[a]) != 'undefined') continue;
				tpl.assign(a, currentRowData[a]);
				if(a == '__row_class__') useRowClass = true;
			}
			
			tpl.render();
			$('#grid-'+instanceName+' .grid-body').append($(tpl.out).show());
		};
		
		if(!realRows) {
			var colsCount = instance.cols.count;
			var emptyRow = '<tr><td colspan="'+colsCount+'" style="text-align:center">Данные отсутствуют.</td></tr>';
			$('#grid-'+instanceName+' .grid-body').append(emptyRow);
		}
		
		$('#grid-'+instanceName+'-found-stat').html(instance._makeSelectedAndFound());
		$('#grid-'+instanceName+'-pagiation').html('');
		
		
		if(typeof(instance.settings.group_actions) != 'undefined' && instance.settings.group_actions) {
			instance.groupAction.render();
		}
		
		if(typeof(instance.settings.user_settings) != 'undefined' && instance.settings.user_settings) {
			instance.cols.each(function(index,colInfo){
				if(typeof(colInfo.hidden) != 'undefined' && colInfo.hidden) {
					instance.dom.find('td[name="'+colInfo.name+'"]').attr('hidden','true').hide();
				} else {
					instance.dom.find('td[name="'+colInfo.name+'"]').removeAttr('hidden').show();
				}
			});
		}
		
		instance._makePagination();
		
		$('#grid-'+instanceName+' .grid-body').show();
		
		instance.emit("hideLoader");		
		instance.emit('ready', instanceName);
		instance._markSort();
	},
	
	this.perpage = function(limit) {
		if(this._locked()) return false;
		
		this.limit = parseInt(limit);
		this.page = 0;
		this.start();
		return this;
	},
	
	this.gopage = function(page) {
		if(this._locked()) return false;
		
		this.page = (page == 0)? 0 : page - 1;
		this.start();
		return this;
	},
	
	this.toggleColl = function(name) {
		var colInfo = this.cols.find(['name', name]);
		if(typeof(colInfo.hidden) != 'undefined' && colInfo.hidden) {
			colInfo.hidden = false;
			this.dom.find('th[name="'+colInfo.name+'"]').removeAttr('hidden').show();
			this.dom.find('td[name="'+colInfo.name+'"]').removeAttr('hidden').show();
			this.emit("showColl",this.instance, name);
		} else {
			colInfo.hidden = true;
			this.dom.find('th[name="'+colInfo.name+'"]').attr('hidden','true').hide();
			this.dom.find('td[name="'+colInfo.name+'"]').attr('hidden','true').hide();
			this.emit("hideColl",this.instance, name);
		}
		
		this.cols.replace(['name', name], colInfo);
		
		var store = {}
		this.cols.each(function(index,data) {
			store[data.name] = (data.hidden)? true : false;
		});
		
		if(typeof(globalApi) == 'object' && typeof(globalApi.saveUserSetting) == 'function') {
			globalApi.saveUserSetting('grid_'+this.instance+'_hidecols', store);
		}
		
		this.emit("change_user_settings",this.instance, store);
	},
	
	this.groupAction.init = function() {
		var self = this;
		self.container = $('#grid-'+self.name+'-groupAction-automenu');
		
		if(!self.container.length) {
			self.instance._debug('Group action error. Container not found');
			return false;
		}
		
		var tpl = '<ul class="dropdown-menu">' +
					'<li action="selectAll"><a href="javascript:;"><i class=" icon-check"></i> Выделить все</a></li>'+
			        '<li action="clear"><a href="javascript:;"><i class=" icon-check-empty"></i> Убрать выделение</a></li>'+
			        '<li action="revert"><a href="javascript:;"><i class=" icon-retweet"></i> Инвертировать выделение</a></li>'+
			        '<li class="divider"></li>';
		
		if(typeof(self.settings) == 'object') {
			for(var i in self.settings.actions) {
				if(self.settings.actions[i].action == 'divider') {
					tpl += '<li class="divider"></li>';
					continue;
				}
				
				tpl += '<li action="'+self.settings.actions[i].action+'"';
				
				var isDisable = false;
				
				if(typeof(self.settings.actions[i].onselect) != 'undefined' && self.settings.actions[i].onselect) {
					tpl += ' data-onselect="true" ';
					isDisable = true;
				}
				
				if(typeof(self.settings.actions[i].onboofer) != 'undefined' && self.settings.actions[i].onboofer) {
					tpl += ' data-onboofer="true" ';
					isDisable = true;
				}
				
				if(isDisable) {
					tpl += ' class="disabled" ';
				}
				
				tpl += '><a href="javascript:;">';
				
				if(typeof(self.settings.actions[i].icon)) {
					tpl += '<i class="icon-'+self.settings.actions[i].icon+'"></i> ';
				}
				
				tpl += ((typeof(self.settings.actions[i].title))? self.settings.actions[i].title : self.settings.actions[i].action);
			}
		}
		
		tpl += '</ul>';
		tpl = $(tpl);
		self.container.before(tpl);
		self.container = tpl;
		self.instance.settings.group_action_menu = true;
	},
	
	this.groupAction.on = function(event, func) {
		if(typeof(this.events[event]) != 'object') {
			this.events[event] = [];
		}
		
		this.events[event].push(func);
		
		return this.groupAction;
	},
	
	this.groupAction.emit = function(event, arg) {
		if(typeof(this.events[event]) == 'object') {
			for(var i in this.events[event]) {
				if(typeof(this.events[event] == 'function')) {
					for(var i in this.events[event]) {
						this.events[event][i].call(this,arg);
					}
				}
			}
		}
	},
	
	this.groupAction.call = function(action) {
		
		var self = this;
		switch(action) {
			case 'selectAll':
				self.data.selectAll = true;
				self.data.exclude = [];
				self.data.include = [];
				self.render();
				self.instance._makeSelectedAndFound();
				this.emit('select-all');
			break;
			
			case 'revert':
				if(self.data.selectAll) {
					self.data.include = self.data.exclude.slice(0);
					self.data.exclude = [];
					self.data.selectAll = false;
				} else {
					self.data.exclude = self.data.include.slice(0);
					self.data.include = [];
					self.data.selectAll = true;
				}

				self.render();
				self.instance._makeSelectedAndFound();
				this.emit('revert');
			break;
			
			case 'clear':
				self.data.selectAll = false;
				self.data.exclude = [];
				self.data.include = [];
				self.render();
				self.instance._makeSelectedAndFound();
				this.emit('clear');
			break;
			
			case 'copy':
				self.appendBuffer({from:self.instance.instance, action:'copy', all:self.data.selectAll, inc:self.data.include, exc:self.data.exclude});
				self.instance._makeGroupActionMenu();
				
				this.emit('copy', self.getBuffer(this.name));
			break;
			
			case 'remove':
				self.instance.emit('group-remove', {all:self.data.selectAll, inc:self.data.include, exc:self.data.exclude});
				self.call('clear');
				this.emit('remove', self.getBuffer(this.name));
			break;
			
			case 'cut':
				self.appendBuffer({from:self.instance.instance, action:'cut', all:self.data.selectAll, inc:self.data.include, exc:self.data.exclude});
				self.instance._makeGroupActionMenu();
				this.emit('cut', self.getBuffer(this.name));
			break;
			
			case 'paste' :
				var buffer = self.getBuffer(this.name);
				if(!buffer) return this.instance._debug('paste buffer not found');
				this.emit('paste', buffer);
			break;
			
			default:
				
				this.emit(action);
			break;
		}
	},

	this.groupAction.appendBuffer = function(data) {
		if(typeof(window.gridGroupActionBuffer) != 'object') {
			window.gridGroupActionBuffer = {};
		}
		
		window.gridGroupActionBuffer[this.instance.instance] = data;
	},
	
	this.groupAction.getBuffer = function(sub) {
		if(typeof(window.gridGroupActionBuffer) != 'object') return false;
		if(typeof(gridGroupActionBuffer[this.instance.instance]) != 'object') return false;
		
		return gridGroupActionBuffer[this.instance.instance];
	},

	this.groupAction.render = function() {
		var self = this;

		$('#grid-'+self.name+' input[name="groupAction"]').each(function(){
			var value = $(this).val();
			
			if(!$(this).is('.binded')) {
				$(this).bind('change',function() {
					var checked = $(this).is(':checked');
					var value = $(this).val();

					if(self.data.selectAll == true) {
						if(checked && self.data.exclude.indexOf(value) != -1) { 
							self.data.exclude.splice(self.data.exclude.indexOf(value), 1);
							if(self.data.include.indexOf(value) == -1) {
								self.data.include.push(value);
							}
						} else if(!checked) {
							if(self.data.exclude.indexOf(value) == -1) {
								self.data.exclude.push(value);
							}

							if(self.data.include.indexOf(value) != -1) {
								self.data.include.splice(self.data.include.indexOf(value), 1);
							}
						}
					} else {
						if(checked) {							
							if(self.data.include.indexOf(value) === -1) {
								self.data.include.push(value);
							}
						} else {
							if(self.data.include.indexOf(value) != -1) {
								self.data.include.splice(self.data.include.indexOf(value), 1);
							}
						}
					}
					
					self.instance._makeSelectedAndFound();
				}).addClass('binded');
			}
			
			if(self.data.selectAll == true) {
				if(!self.data.exclude.length || self.data.exclude.indexOf(value) == -1) {
					$(this).attr('checked','checked');
				} else {
					$(this).removeAttr('checked');
				}
			} else {
				if(self.data.include.indexOf(value) != -1) {
					$(this).attr('checked','checked');
				} else {
					$(this).removeAttr('checked');
				}
			}
		});
		
		self.instance.emit('selectionMenuReady');
	},
	
	this.groupAction.getSelectedMark = function() {
		return {all:this.data.selectAll, inc:this.data.include, exc:this.data.exclude};
	};
	
	this.groupAction.getSelectedCount = function() {
		var self = this;
		var all = (typeof(self.instance.ajax.response.found_rows) != 'undefined')? parseInt(self.instance.ajax.response.found_rows) : 0;
		
		if(self.data.selectAll == true) {
			if(self.data.exclude.length > 0) return all - self.data.exclude.length;
			
			return all;
		} else {
			return self.data.include.length;
		}
	},

	this._makeFilters = function() {
		$('.grid-find-input').each(function(){
			$(this).bind('keyup',function(event, ui) {
				var keyCode = $.ui.keyCode;
				if(event.keyCode == keyCode.ENTER) {
					$(this).trigger('find');
				}
				if(event.keyCode == keyCode.ESCAPE) {
					$(this).trigger('clear');
				}
			}).bind('find',function() {_current
				var gridName = $(this).attr('grid');
				var filterName = $(this).attr('filter');
				var filtredInput = $(this);
				if($(this).val() == '') {
					grid[gridName].removeFilter(filterName).start();
				} else {
					grid[gridName].addFilter(filterName,$(this).val()).start(function(){
						filtredInput.focus();
					});
				}
			}).bind('clear',function() {
				var gridName = $(this).attr('grid');
				var filterName = $(this).attr('filter');
				var filtredInput = $(this);
				if($(this).val() != '') {
					grid[gridName].removeFilter(filterName).start();
					$(this).val('')
					filtredInput.focus();
				} 
			});
		});
	},
	
	this._makePagination = function() {		
		var total = this.ajax.response.found_rows
		var perpage = this.limit;
		var current = this.page*perpage;
		var offset = 2;
		var instance = this.instance;
		var type = ($(this.pagination).attr('type'))? $(this.pagination).attr('type') : 'js';
			type = (type == 'hash' || type == 'js')? type : 'js';
		var startHash = document.location.hash;
			startHash = (startHash.substr(-1,1) == '/')? startHash : startHash+'/';

		
		if(total <= perpage) return '';
		if(perpage == 0) return '';
		if(total <= 0) return '';
		
		var total_pages = Math.ceil(total / perpage);
		var prev = (current - perpage)/perpage;
		
		if(total_pages <= 1) return '';
		
		// template
		var btn_first = $(this.pagination).find('*[is="first"]').clone();
		var btn_prev =  $(this.pagination).find('*[is="prev"]').clone();
		var btn_next = $(this.pagination).find('*[is="next"]').clone();
		var btn_last =  $(this.pagination).find('*[is="last"]').clone();
		
		var container = $(this.pagination).clone();
		container.html('');
		
		var pages = ($(this.pagination).find('*[is="pages"]').length > 0)? $(this.pagination).find('*[is="pages"]').clone() : false;
		var pagesContainer = (pages)? pages : container;

		
		var page_active = $(this.pagination).find('*[is="page_active"]');
		var page_dot = $(this.pagination).find('*[is="page_dot"]');
		var page_default = $(this.pagination).find('*[is="page_default"]');
		
		if(pages) {
			pagesContainer.html('');
		}
				
		if(prev >= 0) {
			if(type == 'js') {
				container.append(btn_prev.bind('click', function() { grid[instance].gopage(prev+1); }));
			} else {
				var link = btn_prev.is('a') ? btn_prev : btn_prev.find('a');
				var url = startHash + '' + (prev+1) + '/';
				link.attr('href',url);
			}
		}
		
		var counter = 1;
		var current_page = parseInt(Math.floor((current + perpage) / perpage));
		
		while(counter <= total_pages) {
			if(counter == current_page) {
				this._makePaginationButtonText(page_active, pagesContainer, counter, false, type, startHash);
			} else if((counter > current_page - offset && counter < current_page + offset) || counter == 1 || counter == total_pages) {
				if(counter == total_pages && current_page < total_pages - offset) {
					this._makePaginationButtonText(page_dot, pagesContainer, '...', false, type, startHash);
				}

				this._makePaginationButtonText(page_default, pagesContainer, counter, true, type, startHash);

				if(counter == 1 && current_page > 1 + offset) {
					this._makePaginationButtonText(page_dot, pagesContainer, '...', false, type, startHash);
				}
			}
			counter++;
		}
		
		if(pages) container.append(pagesContainer);
		
		var next = current+perpage;
		
		if (total > next) {
			if(type == 'js') {
				container.append(btn_next.bind('click', function() { grid[instance].gopage(next/perpage+1); }));
			} else {
				var link = btn_next.is('a') ? btn_next : btn_next.find('a');
				var url = startHash + '' + (next/perpage+1) + '/';
				link.attr('href',url);
			}
		}
		
		$('#grid-'+this.instance+'-pagiation').append(container);		
	},
	
	this._makePaginationButtonText = function(template, container, text, click, type, url) {
		var usedAtag = (template.find('a').length > 0)? true : false;
		var element = template.clone();
		var click = (click === true)? true : false;
		
		if(usedAtag) {
			if(click) {
				if(type == 'js') {
					element.find('a').attr('page', text).bind('click', function(){
						grid[instance].gopage($(this).attr('page'));
					});
				} else {
					element.find('a').attr('href', url + text + '/');
				}
			}
			
			if(type == 'js') {
				element.find('a').html(text).attr('href', 'javascript:void(0)');
			}
		} else {
			element.html(text);
			if(click) {
				element.attr('page', text).bind('click', function(){
					grid[instance].gopage($(this).attr('page'));
				});
			}
		}
		
		container.append(element);
	};
	
	this._makeFound = function(is_string) {
		var ret = {found:this.ajax.response.found_rows,show_from:this.page*this.limit+1,show_to:this.page*this.limit+this.limit}
		ret.show_to = (ret.show_to > ret.found)? ret.found : ret.show_to;

		if(ret.found == 0) {
			return 'Найдено 0 записей';
		} else if(is_string === true) {
			return 'Показано с '+ret.show_from+' по '+ret.show_to+' из '+$formater.pluralNumber(ret.found,'записи','записей','записей');			
		} 
		
		return ret;
	},

	this._makeSelectedAndFound = function() {
		if(typeof(this.settings.group_actions) != 'undefined' && this.settings.group_actions) {
			var selectedCount = this.groupAction.getSelectedCount();
			
			this._makeGroupActionMenu(selectedCount);
			
			if(selectedCount > 0) {
				$('#grid-'+this.instance+'-found-stat').html(this._makeFound(true)+'<br>Выбрано: '+$formater.pluralNumber(selectedCount,'запись','записи','записей'));
			} else {
				$('#grid-'+this.instance+'-found-stat').html(this._makeFound(true));
			}
		}
	},
	
	this._makeGroupActionMenu = function(selectedElements) {
				
		if(typeof(this.settings.group_action_menu) == 'undefined') {
			this._debug('Grid group action menu not initialized. Pls set variable [group_action_menu] in grid setting section');
			return false;
		}
		
		if(selectedElements === undefined) {
			selectedElements = this.groupAction.getSelectedCount();
		}
		
		var booferedObjects = false;
		
		if(typeof(window.gridGroupActionBuffer) != 'undefined' && typeof(gridGroupActionBuffer[this.instance]) != 'undefined') {
			booferedObjects = true;
		}
			

		if(this.groupAction.container.find('[action]').length > 0) {
			var instance = this;
			this.groupAction.container.find('[action]').each(function(){
				if(!$(this).is('.binded')) {
					$(this).bind('click', function() {
						if($(this).is('.disabled')) return;
						instance.groupAction.call($(this).attr('action'));
					});
					$(this).addClass('binded');
				}	
				
				if($(this).attr('data-onselect')) {
					if(selectedElements > 0) {
						$(this).removeClass('disabled');
					} else {
						$(this).removeClass('disabled').addClass('disabled');
					}
				}
				
				if($(this).attr('data-onboofer')) {
					if(booferedObjects > 0) {
						$(this).removeClass('disabled');
					} else {
						$(this).removeClass('disabled').addClass('disabled');
					}
				}
			});
		} else {
			this._debug('Group action is on, but not initialized menu!');
		}
	};
	
	this._appendUserSettings = function() {
		var globalSettings = (typeof(window['grid_'+this.instance+'_settings_menu_user_data']))? window['grid_'+this.instance+'_settings_menu_user_data'] : false;
		var colInfo = false;
		if(globalSettings) {
			for(var i in globalSettings) {
				colInfo = this.cols.find(['name', i]);
				if(!colInfo || typeof(colInfo) != 'object' || !typeof(colInfo.hidden)) continue;
				colInfo.hidden = globalSettings[i];
				this.cols.replace(['name', name], colInfo);
			}
		}
		
		if(typeof(window['grid_'+this.instance+'_groupaction_data']) != 'undefined') {
			this.groupAction.settings = window['grid_'+this.instance+'_groupaction_data'];
		} else {
			this.groupAction.settings = false;
		}
	};
	
	this._prepareGridMenuFromWidget = function() {		
		var tplId = 'grid-'+this.instance+'-settings-MenuTemplate';
		if(!$('#'+tplId).length || $('#'+tplId).attr('type') != 'text/template') return false;
		var tplCompiled = new templates(tplId);
		var rows = this.cols.toObject();
		
		for(var i in rows) {
			if(typeof(rows[i].hidden) != 'undefined' && rows[i].hidden) {
				rows[i].hidden = 'col-hidden="true"';
			} else {
				rows[i].hidden = 'col-hidden="false"';
			}
			
			if(rows[i].title == '') {
				delete rows[i];
			}
		}
		
		tplCompiled.assign('item', rows).render();
		$('#'+tplId).after(tplCompiled.out);
		this._prepareGridMenu(true, $('#'+tplId).next());
	};
	
	this._prepareGridMenu = function(fromTpl, obj) {
		var menuElement = (fromTpl === true)? obj : $('#'+this.settings.user_settings_menu);
		if(!menuElement.length) return ;
		var self = this;
		
		menuElement.find('li').each(function(){
			var name = $(this).attr('name');
			var isHidden = ($(this).attr('col-hidden') && $(this).attr('col-hidden') == 'true')? true : false;
			var colInfo = self.cols.find(['name', name]);
			var icon = $(this).find('i');
						
			if(colInfo) {
				colInfo.hidden = isHidden;
				self.cols.replace(['name', name], colInfo);
			}
			
			if(isHidden == true) {
				icon.addClass('icon-check-empty');
			} else {
				icon.addClass('icon-check');
			}
			
			$(this).find('a').bind('click', function(event){
				event.stopPropagation();
				$(this).find('i').trigger('click');
			});
			
			$(this).find('i').bind('click', function(event){
				$(this).toggleClass('icon-check');
				$(this).toggleClass('icon-check-empty');
				var colName = $(this).closest('li').attr('name');
				self.toggleColl(colName);
				event.stopPropagation();
			});
		});
				
		self.cols.each(function(index,data){
			if(!data.hidden) {
				self.dom.find('th[name="'+data.name+'"]').removeAttr('hidden').show();
			}
		});
	},
	
	this._markSort = function() {
		var currentClass = (this.sort_type == 'desc')? 'icon-sort-up' : 'icon-sort-down';
		this.dom.find('.icon-sort-up').removeClass('icon-sort-up').addClass('icon-sort');
		this.dom.find('.icon-sort-down').removeClass('icon-sort-down').addClass('icon-sort');
		this.dom.find('th[name="'+this.sort_by+'"] i').removeClass('icon-sort').addClass(currentClass);
	},

	this._destruct = function() {
		this.emit('hideLoader');
	},
	
	this._debug = function(message) {
		
		this._messages.push(message);
		if(typeof(console) == 'object' && typeof(console.warn) != 'undefined') {
			console.warn('Grid error! Instance: '+this.instance+' Message: '+message);
		}
	};
	
	this._extractFunctionFromObject = function(object) {
		var _current = false;
		for(var i in object) {
			var _name = object[i];
			if(!_current) {
				if(typeof(window[_name]) == 'undefined') return false;
				_current = window[_name];
			} else {
				if(typeof(_current[_name]) == 'undefined') return false;
				_current = _current[_name];
			}
		}
		
		return (typeof(_current) == 'function')? _current : false;
	};

	this._locked = function() {
		if($page.locked) return true;
		
		return false;
	};
	
	this._saveResponse = function(response) {
		this.ajax.response = response;
	};
	
	this._getFromTplCols = function() {
		var cols = this.cols.toObject();
		var out = [];
		for(var i in cols) {
			if(cols[i].from_tpl) {
				out.push({name:cols[i].name, tpl:cols[i].from_tpl});
			}
		}
		
		return (out.length)? out : false;
	};
	
	/* Event emitter */
	/* -> https://github.com/component/emitter */
	this.on = function(event, fn) {
		this._callbacks = this._callbacks || {};
		(this._callbacks[event] = this._callbacks[event] || []).push(fn);
		return this;
	}
	
	this.once = function(event, fn){
		var self = this;
		this._callbacks = this._callbacks || {};

		function on() {
			self.off(event, on);
			fn.apply(this, arguments);
		}

		fn._off = on;
		this.on(event, on);
		
		return this;
	};
	
	this.off = function(event, fn){
		this._callbacks = this._callbacks || {};
		
		  // all
		if (0 == arguments.length) {
			this._callbacks = {};
			return this;
		}
		
		// specific event
		var callbacks = this._callbacks[event];
		if (!callbacks) return this;
		
		// remove all handlers
		if (1 == arguments.length) {
			delete this._callbacks[event];
			return this;
		}
		
		// remove specific handler
		var i = ArrayIndexOf(callbacks, fn._off || fn);
		if (~i) callbacks.splice(i, 1);
		
		return this;
	};
		
	this.emit = function(event){
		this._callbacks = this._callbacks || {};
		var args = [].slice.call(arguments, 1), callbacks = this._callbacks[event];
	
		if(callbacks) {
			callbacks = callbacks.slice(0);
			
			for (var i = 0, len = callbacks.length; i < len; ++i) {
				callbacks[i].apply(this, args);
			}
		}
	
		return this;
	};
	
	this.listeners = function(event){
		this._callbacks = this._callbacks || {};
		return this._callbacks[event] || [];
	};
	
	this.hasListeners = function(event){
		return !! this.listeners(event).length;
	};
	
	/* Extended object (iObject) */
	this.iobject = function(importObject) {
		this.store = {};
		this.count = 0;
		this.increment = 0;
		this.position = 0;
		this._lastFindedIndex = null;
		
		
		if(typeof(importObject) == 'object') {
			for(var item in importObject) {
				this.set(item, importObject[item]);
			}
		};
	};

	this.iobject.prototype.set = function(key,value) {
		if(typeof(value) == 'undefined' && typeof(key) != 'undefined') {
			this.store[this.increment] = key;
			this.count++;
			this.increment++;
		}
		
		if(this.isset(key)) {
			this.store[key] = value;
		} else {
			this.store[key] = value;
			this.count++;
		}
		
		return this;
	};
	
	this.iobject.prototype.replace = function(key, data) {
		var res = this.findObject(key[0],key[1]);
		if(res) {
			this.store[this._lastFindedIndex] = data;
		}
	};
	
	this.iobject.prototype.rm = function(key) {
		if(!this.isset(key)) return this;
		delete this.store[key];
		this.count --;
		return this;
	};
	
	this.iobject.prototype.clear = function() {
		this.store = {};
		this.count = 0;
		return this;
	};
	
	this.iobject.prototype.curent = function() {
		if(!this.isset(this.position)) return null;
		return this.store[this.position];
	};
	
	this.iobject.prototype.key = function() {
		return this.position;
	};
	
	this.iobject.prototype.reset = function() {
		this.position = 0;
	};
	
	this.iobject.prototype.get = function(key) {
		if(typeof(key) == 'undefined') {
			if(!this.isset(this.position)) return null;
			var result = this.store[this.position];
			if(this.increment >= this.position+1) {
				this.position++;
			} else {
				return null;
			}
			return result;
		}
		if(!this.isset(key)) return null;
		return this.store[key];
	};
	
	this.iobject.prototype.toString = function() {
		return JSON.stringify(this.store);
	};
	
	this.iobject.prototype.toObject = function() {
		return JSON.parse(JSON.stringify(this.store));
	};
	
	this.iobject.prototype.isset = function(key) {
		if(typeof(this.store[key]) == 'undefined') return false;
		return true;
	};
	
	this.iobject.prototype.each = function(callback) {
		for(var i in this.store) {
			var result = callback(i, this.store[i]);
			if(result === false) {
				break;
			}
		}
		return this;
	};
	
	this.iobject.prototype.find = function(key,childObject) {
		if(typeof(key) == 'array' || (typeof(key) == 'object' && typeof(key[0]) != 'undefined' && typeof(key[1]) != 'undefined')) {
			return this.findObject(key[0],key[1],childObject);
		} else if(typeof(key) == 'object') {
			var ii = 0;
			var _key = null;
			var _val = null;
			for(var o in key) {
				if(ii == 0) {
					_key = key[o];
				} else if(ii == 1) {
					_val = key[o];
				} else {
					break;	
				}
				ii++;
			}
			delete(ii, key);
			return this.findObject(_key,_val,childObject);
		}
		var element = (typeof(childObject) == 'object')? childObject : this.store;
		if(typeof(element[key]) != 'undefined') {
			return element[key];
		}
		for(var index in element) {
			if(index == key) {
				return element[index];
			}
			if(typeof(element[index])=='object') {
				return this.find(key, element[index]);
			}
		}
		return null;
	};
	
	this.iobject.prototype.findObject = function(key,value,childObject) {
		var element = (typeof(childObject) == 'object')? childObject : this.store;
		if(!element) return null;
		
		if(typeof(element[key]) != 'undefined' && element[key] == value) return element;
		
		for(var index in element) {
			this._lastFindedIndex = index;
			if((index == key && element[index] == value) || (index == key && value == '*')) return element;
			if(typeof(element[index])=='object') {
				var result = this.findObject(key,value, element[index]);
				if(result !== null) {
					return result;
				}
			}
		}
		return null;
	};
	
	this.iobject.prototype.getRand = function() {
		var result;
		for(var prop in this.store)
			if(Math.random() < 1/++count)
		           result = prop;
		return result;
	};	
};

var grid = {};

$(document).ready(function(){
	if(typeof(grid) == 'object') {
		for(var i in grid) {
			if(grid[i].settings.autoload && !grid[i]._loadInitiated) {
				grid[i].start();
			}
		}
	}
});
