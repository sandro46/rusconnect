$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Управление темами',
			always_reload: true,
			delete_unload: true,
			on : site_control.themeslist,			
			
			
			'/edit/:id/' : {
				name: 'Редактирование темы',
				always_reload: true,
				delete_unload: true,
				on : site_control.editSite,
			},
	
			'/add/' : {
				name: 'Мастер создания темы',
				always_reload: true,
				delete_unload: true,
				on : site_control.siteWizard,
			}
		}
	};
	
	$page.init(routes);
});