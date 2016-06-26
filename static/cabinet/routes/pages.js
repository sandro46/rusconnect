$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Страницы сайта',
			always_reload: false,
			on : site_control.showTree,			
			'/edit/:id/' : {
				name: 'Редактирование страницы',
				always_reload: true,
				delete_unload: true,
				on : site_control.editPage,
			},
			
			'/add/:type/:parent_id/' : {
				name: 'Создание страницы',
				always_reload: true,
				delete_unload: true,
				on : site_control.addPage,
			}
		}
	};
	
	$page.init(routes);
});