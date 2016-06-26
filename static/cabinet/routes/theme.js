$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Тема оформления',
			always_reload: true,
			delete_unload: true,
			on : site_control.showTheme,			
			'/edit/' : {
				name: 'Настройка темы',
				always_reload: true,
				delete_unload: true,
				on : site_control.themeSetting,
			}
		}
	};
	
	$page.init(routes);
});