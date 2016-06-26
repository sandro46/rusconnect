var admin_templates = {
	editor : null,
	editItem : function(tplId) {
		$page.lock();
		
		ModuleTemplates.getSource(tplId, false, function(tplInfo){
			var preloader = [
                 	{tplInfo:['ModuleTemplates', 'getSource', [tplId, true]]},
                 	{history:['ModuleTemplates', 'getHistory', [tplId]]}
			];
			
			$page.show(['create.html', 'templates', preloader], false, function(current){
				$page.unlock();

				var md5 = hex_md5(tplInfo.source+tplInfo.theme+tplInfo.name_module+tplInfo.name+tplInfo.description);
			
				current.find('.savenexit-button').bind('click', function(){
					$page.current.find('.save-button').one('aftersaveok', function(){
						$page.current.find('.close-button').trigger('click');
					}).trigger('click');
				}).show();
				
				current.find('.close-button').bind('click',function(){
					var tpl = $page.getForm(current);
					tpl.data.source = admin_templates.editor.getValue();
					
					var md5_new = hex_md5(tpl.data.source+
							tpl.data.theme+
							tpl.data.name_module+
							tpl.data.name+
							tpl.data.description);
					
					if(md5 != md5_new) {
						$page.confirm('Шаблон был изменен', 'Вы внесли правки в шаблон, но не сохранили его!<br>Сохранить изменения перед выходом?',function(){
							current.find('.save-button').trigger('click');
							$page.back();
						}, function(){
							$page.back();
						})
					} else {
						$page.back();
					}
				});
				
				current.find('.save-button').bind('click',function(){
					var tpl = $page.getForm(current);
					tpl.data.source = admin_templates.editor.getValue();
					
					ModuleTemplates.save(tplId, tpl.data, function(result){
						md5 = hex_md5(tpl.data.source+
								tpl.data.theme+
								tpl.data.name_module+
								tpl.data.name+
								tpl.data.description);
						
						$page.sticky('Шаблон был изменен','Данные успешно записаны!');
						$page.current.find('.save-button').trigger('aftersaveok');
					});
				});
				
				admin_templates.tplEditorUI();
				admin_templates.editor.setValue(tplInfo.source);
			});
		});
	},
	
	addItem : function(){
		$page.lock();
		$page.show(['create.html', 'templates'], false, function(current){
			$page.unlock();
			admin_templates.tplEditorUI();
			current.find('.savenexit-button').hide();
			
			current.find('.close-button').bind('click',function(){
				var tpl = $page.getForm(current);
				tpl.data.source = admin_templates.editor.getValue();
				
				$page.confirm('Создание нового шаблона', 'Вы не сохранили изменения перед выходом. Сохранить сейчас?',
					function(){
						current.find('.save-button').trigger('click');
					}, function(){
						$page.back();
				});
			});
			
			var saveTpl = function(callback) {
				var tpl = $page.getForm(current);
				tpl.data.source = admin_templates.editor.getValue();
				$page.lock();
				
				ModuleTemplates.save(false, tpl.data, function(tplId){
					if(typeof(callback) == 'function') callback(tplId);
				})
			};
			
			current.find('.save-button').bind('click',function(){
				saveTpl(function(tplId){
					$page.unlock();
					$page.sticky('Шаблон успешно добавлен','Данные успешно записаны!');
					$page.router.setRoute('/edit/'+tplId+'/');
				})
			});
		});
	},
	
	tplEditorUI : function() {
		var cnt = 0;
		$("#source-tempate-editor-menu span.tools").each(function(){
			if(cnt == 0) {
				$(this).css({'font-size': '13px', 'font-weight': 'bold', 'color':'#4C4C4C', 'cursor':'normal'}).attr('data-tab-select','1');
			} 
			
			$(this).bind('click',function(){
				$('div.'+$(this).parent().find('span[data-tab-select]').attr('name')).hide();
				$(this).parent().find('span[data-tab-select]').removeAttr('data-tab-select').css({'font-size': '13px', 'font-weight': 'normal', 'color':'#868686', 'cursor':'pointer'});
				$(this).css({'font-size': '13px', 'font-weight': 'bold', 'color':'#4C4C4C', 'cursor':'normal'}).attr('data-tab-select','1');
				$('div.'+$(this).attr('name')).show();
			});
			
			cnt++;
		});
		
		$page.current.find('a[action="rollback"]').bind('click',function(){
			var rollbackId = $(this).attr('revision');
			$page.confirm('Откат шаблона', 'Вы действительно хотите откатить шаблон до ревизии № '+rollbackId+'?<br>Изменения вступят в силу сразу после нажатия на кнопку OK!',function(){
				$page.lock();
				ModuleTemplates.rollback(rollbackId, function(){
					$page.update();
					$page.sticky('Откат шаблона', 'Откат шаблона выполнен успешно. Текущая ревизия: №'+rollbackId);
				})
			});
		});
		
		admin_templates.editor = CodeMirror.fromTextArea(document.getElementById('source-tempate-editor'), {
			  mode: "text/html",
	          extraKeys: {"Ctrl-Space": "autocomplete"},
			  styleActiveLine: true,
			  lineNumbers: true,
			  lineWrapping: false,
			  foldGutter: true,
			  gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],

			  highlightSelectionMatches: {showToken: /\w/},
			  matchTags: {bothTags: true},
			  extraKeys: {"Ctrl-J": "toMatchingTag","Ctrl-S":function(){
				  $page.current.find('.save-button').trigger('click');
			  }}
		});
		
		$(admin_templates.editor.getWrapperElement()).resizable()
		$(admin_templates.editor.getWrapperElement()).css({height:600})
	},
	
	deleteItem : function(tplId) {
		var data = grid.templates_list.rows.find(['id', tplId]);
		//console.log(data)
		$page.confirm('Удаление шаблона', 'Вы действительно хотите удалить шаблон '+data.name, function(){
			ModuleTemplates.remove(tplId, 0, function(){
				$page.sticky('Удаление шаблона', 'Шаблон '+data.name+' был удален. Если Вы его удалили по ошибке, шаблон можно востановить в корзине.')
				if(typeof(grid.templates_list) == 'object') grid.templates_list.start();
			});
		});
	},
	
	appendFilter : function() {
		var theme = $('[name="gridTemplatesFilter-theme"]', $page.current).val();
		var module = $('[name="gridTemplatesFilter-module"]', $page.current).val();
		
		if(!module) {
			grid.templates_list.removeFilter('name_module');
		} else {
			grid.templates_list.addFilter('name_module', module);
		}
		
		if(!theme) {
			grid.templates_list.removeFilter('theme');
		} else {
			grid.templates_list.addFilter('theme', theme);
		}
		
		grid.templates_list.start();
		$('#grid-templates_list-filters').slideToggle();
	},
	
	clearFilter : function() {
		grid.templates_list.clearFilter();
		grid.templates_list.start();
		
		$('[name="gridTemplatesFilter-module"]', $page.current).parent().find('.search-choice-close').trigger('mousedown').trigger('mouseup');
		$('[name="gridTemplatesFilter-theme"]', $page.current).parent().find('.search-choice-close').trigger('mousedown').trigger('mouseup');
		
		$('#grid-templates_list-filters').slideToggle();
	},
	
	showTemplatesList : function(route){
		var preloader = [
             	{modulesList:['ModuleTemplates', 'getAllModules']},
             	{themesList:['ModuleTemplates', 'getAllThems']}
             	
		];
		
		
		$page.show(['templates.list.html', 'templates', preloader], false, function(current){
			$('#grid-templates_list-filters').hide();
			grid.templates_list.start();
		});
	}
};


$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Список шаблонов',
			always_reload: true,
			delete_unload: true,
			on : admin_templates.showTemplatesList,
			
			'/edit/:id/' : {
				name: 'Редактирование шаблона',
				always_reload: true,
				delete_unload: true,
				on : admin_templates.editItem,
			},
			
			'/add/' : {
				name: 'Новый шаблон',
				always_reload: true,
				delete_unload: true,
				on : admin_templates.addItem,
			},
			
			'/history/' : {
				name: 'История изменений',
				on : admin_templates.showHistoryList,
				
				':id/' : {
					name: 'История изменений шаблона',
					on : admin_templates.showHistoryItem,
				}
			}
		}
	};
	
	$page.init(routes);
});

//grid.templates_list.groupAction.on('copy', function(boofer){

