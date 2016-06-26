var admin_channel = {
		
	listChannel : function() {
		$page.show(['channel.list.html', 'shop'], false, function(current){
			grid.channel_list.on('ready', function(){
				this.dom.find('div.installChannelButtonGroup').each(function(){
					$(this).find('.btn').hide();
					if(!$(this).attr('installed')) {
						$(this).find('[name="on"]').show();
					} else {
						$(this).find('[name="off"]').show();
					}
				});
				
				this.dom.find('div.actionsChannelButtonGroup').each(function(){
					$(this).find('.action-button').hide();
					
					if($(this).attr('installed')) {
						$(this).find('[name="list"]').show();
						$(this).find('[name="add"]').show();
					}
				});
				
				
			});
			grid.channel_list.start();
		});
	},
	
	addChannel : function(id) {
		$page.lock();
		admin_sales_channel.addChannel(id, function(){
			$page.unlock();
			$page.sticky(' ', 'Канал продаж успешно добавлен к текущему сайту.');
			grid.channel_list.start();
		});
	},
	
	removeChannel : function(id) {
		$page.confirm('Отключение канала продаж', 'Вы действительно хотите отключить этот канал продаж?<br>Все ранее добавленые акции не будут более отслеживаться и вскоре будут удалены!', function(){
			$page.lock();
			admin_sales_channel.removeChannel(id, function(){
				$page.unlock();
				$page.sticky(' ', 'Канал продаж отключен от текущего сайта.');
				grid.channel_list.start();
			});
		});
	},
	
	about : function(id) {
		var preloadTplData = [
  		      	{channelInfo:['admin_sales_channel','getChannelInfo', [id]]}
  		];
		
		$page.show(['channel.about.html', 'shop', preloadTplData], false, function(current){
			$page.bind('back', function(){
				$page.back();
			})
		});
	},
	
	showlist : function(id) {
		$page.show(['channel.my.list.html', 'shop'], {channelId:id}, function(current){
			grid.mychannel_list.addFilter('id', id);
			grid.mychannel_list.start();
		});
	},
	
	addItem : function(id) {
		$page.show(['channel.my.add.html', 'shop'], {channelId:id}, function(current){
			
			
		});
	}
}


$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Каналы продаж',
			always_reload: true,
			on : admin_channel.listChannel,
			
			'/about/:id/' : {
				name: 'Информация о канале продаж',
				always_reload: true,
				delete_unload: true,
				on : admin_channel.about,
			},
			
			'/my/:id/' : {
				name: 'Список акций',
				always_reload: true,
				delete_unload: true,
				on : admin_channel.showlist,
				
				'/add/' : {
					name: 'Создать акцию',
					always_reload: true,
					delete_unload: true,
					on : admin_channel.addItem,
				}
			}
		}
	};
	
	$page.init(routes);
});
