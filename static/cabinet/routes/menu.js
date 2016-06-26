$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Меню сайта',
			always_reload: true,
			delete_unload: true,
			on : site_control.loadMenu,
			
			'/edit/:id/' : {
				name: 'Редактирование пункта меню',
				always_reload: true,
				delete_unload: true,
				on : site_control.editMenu,
			},
			
			'/add/' : {
				name: 'Добавление пункта меню',
				always_reload: true,
				delete_unload: true,
				on : site_control.addMenu,
			}
		}
	};
	
	$page.init(routes);
});