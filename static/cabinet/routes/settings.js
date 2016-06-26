$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Мои сайты',
			always_reload: true,
			delete_unload: true,
			on : site_control.sitesList,			
			'/edit/:id/' : {
				name: 'Настройка сайта',
				always_reload: true,
				delete_unload: true,
				on : site_control.editSite,
			},
	
			'/add/' : {
				name: 'Мастер создания сайта',
				always_reload: true,
				delete_unload: true,
				on : site_control.siteWizard,
			}
		}
	};
	
	$page.init(routes);
});