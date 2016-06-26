var admin_backups = {

	info : function() {
		var preloadTplData = [
		     {lastBackup:['admin_site','getLastBackup']},
		     {backupsList:['admin_site','getBackupList']},
		];
		
		$page.show(['backups.info.html', 'site', preloadTplData]);
	},
	
	restore : function(id) {
		$page.confirm('Восстановление из резервной копии','Вы действительно хотите восстановить данные из этой резервной копии?', function(){
			$page.lock();
			admin_site.restoreSnapshot(id, function(){
				$page.unlock();
				$page.sticky('Восстановление данныъ','Данные из резервной копии успешно восстановлены!');
				$page.update();
			});
		});
	},
	
	remove : function(id) {
		$page.confirm('Удаление резервной копии','Вы действительно хотите удалить эту копию сайта?', function(){
			$page.lock();
			admin_site.removeSnapshot(id, function(){
				$page.unlock();
				$page.sticky(' ','Резервная копия удалена!');
				$page.update();
			});
		});
	},
	
	create : function(obj) {
		if($(this).is('.disabled')) return false;
		$(this).addClass('.disabled');
		$page.lock();
		admin_site.createSnapshot(function(){
			$page.unlock();
			$page.sticky(' ','Резервная копия была создана!');
			$page.update();
		});
	}
}


$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Резервные копии и восстановление',
			always_reload: true,
			on : admin_backups.info,
		}
	};
	
	$page.init(routes);
});
