var admin_reviews = {
		
	showList : function() {
		$page.show(['reviews.list.html', 'site']);
	},
			
	remove : function(id) {
		$page.confirm('Удаление отзыва', 'Вы действительно хотите удалить этот отзыв?', function(){
			$page.lock();
			admin_site.deleteReview(id, function(){
				$page.unlock();
				$page.sticky(' ', 'Отзыв удален.');
				grid.reviews.start();
			});
		});
	},
	
	
	
	addItem : function() {
		$page.show(['reviews.add.html', 'site'], false, function(current){
			$('.approved_review', container).toggleButtons({
	            label: {
	                enabled: "Да",
	                disabled: "Нет"
	            }
	        });
			
			$page.bind('back', function(){
				$page.back()
			});
			
			$page.bind('save', function(){
				var form = $page.getForm(current);
				
				if(form.check) {
					$page.lock();
					var info = {
						user_name : form.data.user_name,
						text : form.data.text,
						approved : form.data.approved
					};
					
					$page.lock();
					admin_site.addReview(false, info, function(){
						$page.unlock();
						$page.sticky(' ', 'Отзыв добавлен.');
						$page.back();
						//grid.reviews.start();
					});
				}
			});
			
		});
	},
	
	editItem : function(id) {
		var preloadTplData = [
  		      	{reviewInfo:['admin_site','getReviewInfo', [id]]},
  		];
		
		$page.show(['reviews.add.html', 'site', preloadTplData], false, function(current){
			$('.approved_review', container).toggleButtons({
	            label: {
	                enabled: "Да",
	                disabled: "Нет"
	            }
	        });
			
			$page.bind('back', function(){
				$page.back()
			});
			
			$page.bind('save', function(){
				var form = $page.getForm(current);
				
				if(form.check) {
					$page.lock();
					var info = {
						user_name : form.data.user_name,
						text : form.data.text,
						approved : form.data.approved
					};
					
					$page.lock();
					admin_site.addReview(id, info, function(){
						$page.unlock();
						$page.sticky(' ', 'Отзыв обновлен.');
						$page.back();
						//grid.reviews.start();
					});
				}
			});
			
		});
	}
}


$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Отзывы пользователей',
			always_reload: true,
			on : admin_reviews.showList,
			
			'/edit/:id/' : {
				name: 'Редактирование отзыва',
				always_reload: true,
				delete_unload: true,
				on : admin_reviews.editItem,
			},

			'/add/' : {
				name: 'Новый отзыв',
				always_reload: true,
				delete_unload: true,
				on : admin_reviews.addItem,
			}
		}
	};
	
	$page.init(routes);
});
