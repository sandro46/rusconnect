var admin_vendors = {
	textEditor : false,
	editorSettings : {
			filebrowserUploadUrl : '/ru/files/upload/source/static/application/ckeditor/',
			toolbarGroups : [
                 { name: 'document',    groups: [ 'mode' ] },
                 { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
                 { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                 { name: 'links' },
                 { name: 'insert' },
                 '/',

                 { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                 { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },

                 '/',
                 { name: 'styles' },
                 { name: 'colors' },
                 { name: 'tools' },
                 { name: 'others' }
		 ],

		 extraAllowedContent : 'a(*){*}[*]'
	},
		
	showList : function(route) {
		$page.show(['vendors.list.html', 'shop'], false, function(current){
			
			//$('#grid-stores_list-filters').hide();
			//grid.orders_list.removeFilter('group_id');
			grid.vendors_list.start();
		});
	},
	
	del : function(id) {
		$page.confirm('Удаление производителя','Вы действительно хотите удалить этого производителя?',function(){
			$page.lock();
			admin_shop.deleteVendor(id, function(){
				$page.unlock();
				grid.vendors_list.start();
				$page.sticky('Уделение производителя','производитель удален. Все продукты данного производителя обновлены автоматически.');
			})
		});
	},
	
	edit : function(id) {
		var vendorEditId = id;
		admin_shop.getVendorsList('page',0,1,'1','asc',{'id':id},function(result){
			$page.show(['vendor.add.html', 'shop'], {vendor:result}, function(current){
				$page.bind('back',function(){
					$page.back();
				});

				$page.bind('save',function(){
					var data = $page.getForm(current);
					if(!data.check) return false;
					$page.formMessage.clear();
					$page.lock();
					data.data.description = admin_vendors.textEditor.getData();
					
					admin_shop.saveVendor(vendorEditId, data.data, function(result){
						$page.unlock();
						if(typeof(result) == 'string') {
							$page.formMessage.error('Ошибка!',result);
						} else {
							$page.back();
							if(typeof(vendorAddCallback) == 'function') {
								vendorAddCallback({id:result,name:data.data.name});
							}
						}
					});				
				});
				
				var vendorImage = uploader.init({
					container : $(".VendorLogoUploadButton",current),
					hideUploaded : true,
					formCaption : '',
					
					resize : ['smart:154x50','original'],
					done : function(info) {
						console.log('upload1', info)
						$('.VendorLogoContainer',current).html('');
						var item = $('<div></div>').addClass('imageTumbContainer').css({backgroundImage:'url("'+info[1].name+'")'});
						item.append('<input type="hidden" name="image_preview" value="'+info[0].name+'">');
						item.append('<input type="hidden" name="image_original" value="'+info[1].name+'">');
						$('.VendorLogoContainer',current).append(item);
					}
				});
				
				var vendorImage2 = uploader.init({
					container : $(".VendorLogo2UploadButton",current),
					hideUploaded : true,
					formCaption : '',
					
					resize : ['smart:500x400','smart:500x400'],
					done : function(info) {
						console.log('upload2', info)
						$('.VendorLogo2Container',current).html('');
						var item = $('<div></div>').addClass('imageTumbContainer').css({backgroundImage:'url("'+info[1].name+'")'});
						item.append('<input type="hidden" name="text_image1" value="'+info[0].name+'">');
						item.append('<input type="hidden" name="text_image2" value="'+info[1].name+'">');
						$('.VendorLogo2Container',current).append(item);
					}	
				});
				
				admin_vendors.textEditor = CKEDITOR.appendTo('productFormTextEditor', admin_vendors.editorSettings, result.description);
			});
		});
	},
	
	add : function(){
		$page.show(['vendor.add.html', 'shop'], false, function(current){
			$page.bind('back',function(){
				$page.back();
			});

			$page.bind('save',function(){
				var data = $page.getForm(current);
				if(!data.check) return false;
				$page.formMessage.clear();
				$page.lock();
				data.data.description = admin_vendors.textEditor.getData();
				
				admin_shop.saveVendor(false, data.data, function(result){
					$page.unlock();
					if(typeof(result) == 'string') {
						$page.formMessage.error('Ошибка!',result);
					} else {
						$page.back();
					}
				});				
			});

			$('input[name="vendor_name"]',current).val('');
			$('.VendorLogoContainer',current).html('');
			
			admin_vendors.textEditor = CKEDITOR.appendTo('productFormTextEditor', admin_vendors.editorSettings, '');
			
			
			var vendorImage = uploader.init({
				container : $(".VendorLogoUploadButton",current),
				hideUploaded : true,
				formCaption : '',
				resize : ['smart:154x50', 'original'],
				done : function(info) {
					$('.VendorLogoContainer',current).html('');
					var item = $('<div></div>').addClass('imageTumbContainer').css({backgroundImage:'url("'+info[0].name+'")'});
					item.append('<input type="hidden" name="image_preview" value="'+info[1].name+'">');
					item.append('<input type="hidden" name="image_original" value="'+info[0].name+'">');
					$('.VendorLogoContainer',current).append(item);
				}
			});
			
			var vendorImage2 = uploader.init({
				container : $(".VendorLogo2UploadButton",current),
				hideUploaded : true,
				formCaption : '',
				
				resize : ['smart:500x400','smart:500x400'],
				done : function(info) {
					$('.VendorLogo2Container',current).html('');
					var item = $('<div></div>').addClass('imageTumbContainer').css({backgroundImage:'url("'+info[1].name+'")'});
					item.append('<input type="hidden" name="text_image1" value="'+info[0].name+'">');
					item.append('<input type="hidden" name="text_image2" value="'+info[1].name+'">');
					$('.VendorLogo2Container',current).append(item);
				}
			});
		});
	}
	
};


$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Список производителей',
			always_reload: true,
			on : admin_vendors.showList,
			
			'/edit/:id/' : {
				name: 'Редактирование производителя',
				always_reload: true,
				delete_unload: true,
				on : admin_vendors.edit,
			},
			
			'/add/' : {
				name: 'Новый производитель',
				always_reload: true,
				delete_unload: true,
				on : admin_vendors.add,
			},
		}
	};
	
	$page.init(routes);
});


