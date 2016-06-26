$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Начало работы',
			always_reload: true,
			delete_unload: true,
			on : site_control.welcomeMessage,	
	
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