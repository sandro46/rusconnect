var admin_theme_setup = {
	data : {},
		
		
	main : function() {
		$page.show(['theme_setting.form.html', 'site'], false, function(){
			$page.lock();
			admin_site.getThemeSettingFields(function(data){
				console.log(data);
				
				if(!data || typeof(data) != 'object') {
					alert('cleared settings!');
					return false;
				}
				
				
				
				var $el = $('#settingseditor');
				$el._containers = {};
				$el.getItem = function(id){
					return this._containers[id];
				};
				$el.addItem = function(id, obj) {
					obj.attr('item-id', id);
					this._containers[id] = obj;
					this.append(obj);
					console.log(this);
				};

				if(typeof(data.groups) == 'object') {
					for(var i in data.groups) {
						var colapsed = (typeof(data.groups[i].opened) != 'undefined' && data.groups[i].opened)? false : true;
						$el.addItem(data.groups[i].id, admin_theme_setup._getPortlet(data.groups[i].name, colapsed));
					}
					
					delete data.groups;
				}
				
				for(var i in data) {
					if(typeof(data[i].multiple) != 'undefined' && data[i].multiple) {
						var portler = admin_theme_setup._getPortlet(data[i].name, false);
						$el.prepend(portler);
						
						if(typeof(data[i].data) == 'object') {
							admin_theme_setup.data[data[i].alias] = data[i].data;
							
							var container = $('<table class="table"><tbody></tbody></table>').appendTo(portler.body).find('tbody');
							var num = 0;
							for(o in data[i].data) {
								num++;
								var row = $('<tr item-id='+o+'>'+
												'<td>'+num+'. '+data[i].data[o].title+'</td>'+
												'<td>'+
													'<a href="javascript:void(0)" name="edit" class="btn btn-default btn-small">Редактировать</a>'+
													'<a href="javascript:void(0)" name="delete" class="btn btn-danger btn-small">Удалить</a></td>'+
												'</td>'+
											 '</tr>');
								container.append(row);
								
								
								
							}
						}
						
						
					}
				}
				
				console.log(admin_theme_setup.data);
				$page.unlock();
			});
		});
	},
	
	_getPortlet : function(title, colapsed) {
		colapsed = (colapsed === true)? true : false;
		var icon = (colapsed)? 'icon-chevron-up' : 'icon-chevron-down';
		var bstyle = (colapsed)? 'display: none;' : 'display: block;';
		
		var tpl = $('<div class="widget mediumWidget">'+
					  '<div class="widget-title">'+
						'<h4><i class="icon-reorder"></i>'+title+'</h4>'+
						'<div class="tools"><a name="widget-toggle" class="'+icon+'" href="javascript:;"></a></div>'+
					  '</div>'+
					  '<div class="widget-body" style="'+bstyle+'"></div>'+
				    '</div>');
		
		tpl.find('a[name="widget-toggle"]').click(function(){
			if($(this).is('.icon-chevron-down')) {
				$(this).removeClass('icon-chevron-down').addClass('icon-chevron-up');
				$(this).closest('.widget').find('.widget-body').slideUp(200);
			} else {
				$(this).removeClass('icon-chevron-up').addClass('icon-chevron-down');
				$(this).closest('.widget').find('.widget-body').slideDown(200);
			}
		});
		
		tpl.body = tpl.find('.widget-body');
		
		return tpl;
	}
	
};

$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Основные настройки',
			always_reload: true,
			delete_unload: true,
			on : admin_theme_setup.main,
		}
	};
	
	$page.init(routes);
});
