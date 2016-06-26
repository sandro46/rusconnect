var admin_store = {
	showList : function(route) {
		$page.show(['stores.html', 'shop'], false, function(current){
			
			//$('#grid-stores_list-filters').hide();
			//grid.orders_list.removeFilter('group_id');
			grid.stores_list.start();
		});
	},
	
	editItem : function(id) {
		var preloadTplData = [
		     {store:['admin_shop','getStoreInfo',[id]]},
		     {geo_regions:['admin_shop','getGeoRegions']}
		];
		      		
  		$page.show(['store.edit.html', 'shop', preloadTplData], {}, function(current){
  			$page.bind('back',function(){
  				$page.back();
  			});
  		});
	},
	
	addItem : function(id) {
		var preloadTplData = [
		     //{store:['admin_shop','getStoreInfo',[id]]},
		     {geo_regions:['admin_shop','getGeoRegions']}
		];
		      		
  		$page.show(['store.edit.html', 'shop', preloadTplData], {}, function(current){
  			$page.bind('back',function(){
  				$page.back();
  			});
  		});
	}
};


$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Список складов',
			always_reload: true,
			on : admin_store.showList,
			
			'/edit/:id/' : {
				name: 'Редактирование склада',
				//always_reload: true,
				on : admin_store.editItem,
			},
			
			'/add/' : {
				name: 'Новый склад',
				//always_reload: true,
				on : admin_store.showList,
			},
		}
	};
	
	$page.init(routes);
});


