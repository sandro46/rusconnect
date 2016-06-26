$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Список товаров',
			//always_reload: true,
			//delete_unload: true,
			on : shopMain.showListNew,
			
			'/edit/:id/' : {
				name: 'Редактирование товара',
				always_reload: true,
				delete_unload: true,
				on : shopMain.editItem,
			},
			
			'/edit/:id/add_category/' : {
				name: 'Новая категория товара',
				always_reload: true,
				delete_unload: true,
				on : shopMain.addProductCategory,
			},
						
			'/add/:id/' : {
				name: 'Новый товар',
				always_reload: true,
				delete_unload: true,
				on : shopMain.addItem,
			},
			
			'/add_cat/:parent_id/' : {
				name: 'Новая категория',
				always_reload: true,
				on : shopMain.addCat,
			},
			
			'/edit_cat/:group_id/' : {
				name: 'Редактирование категории',
				always_reload: true,
				delete_unload: true,
				on : shopMain.editCat,
			},

			'/show/:id/type/:type/' : {
				name: 'Список товаров',
				//always_reload: true,
				//delete_unload: true,
				on : shopMain.showListSub
			},
			
		
			'/history/' : {
				':id/' : {
					name: 'История покупок товара',
					on : shopMain.showList,
				}
			}
		}
	};
	
	$page.init(routes);
});
