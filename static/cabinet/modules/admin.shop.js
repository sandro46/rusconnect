

var shopMain = {
		
	categoryList : false,
	ymCategoryList : false,
	vendorList : false,
	productUploader : false,
	textEditor : false, textEditor2 : false, textEditor3 : false,
	editorSettings : false,
	productImages : false,
	removeActionsHelper : {groups:[],images:[],features:[]},
	imageProductSize : ['smart:80x80','smart:150x150','smart:300x300','original'],

	
	loadSyncData : function(ob) {
		var container = $(ob).closest('form');
		var articles = [];
		

		container.find('tr[item-id]').each(function(){
			articles.push({article: $(this).find('input[name="m.article"]').val(), row: $(this)});
		});
		
		var count = articles.length;
		var currentIteration = 0;
		var stateEnd = function(){
			currentIteration++;
			if(currentIteration == count) {
				$page.unlock();
				$page.sticky('Синхронизация 1С','Все данные загружены');
			}
		};
		
		$page.lock();
		
		articles.forEach(function(item){
			shopMain.Sync(item.article, item.row, stateEnd);
		});

	},
	
	Sync: function(article, row ,callback) {
		sync.getProductByArticle(article, function(result){
			//currentIteration++;
			//console.log(result);
			
			row.find('input[name="m.price1"]').val(result.price.price);
			row.find('input[name="m.price2"]').val(result.price.price2);
			row.find('input[name="m.price3"]').val(result.price.price3);
			row.find('input[name="m.price4"]').val(result.price.price4);
			row.find('input[name="m.price5"]').val(result.price.price5);
			callback();
		});
	},
	
	
	catalogActionEdit : function(id, type, onlyEdit) {	
		if(type == 'folder') {
			if(onlyEdit === true) {
				$page.router.setRoute('/edit_cat/'+id+'/');
			} else {
				$page.router.setRoute('/show/'+id+'/type/folder/');
			}
		} else {
			$page.router.setRoute('/edit/'+id+'/');
		}
	},
	
	catalogActionDel : function(id, type) {
		if(type == 'folder') {
			$page.confirm('Подтвердите удаление','Вы действительно хотите удалить эту категорию? Все твоары и под-категориибудут также удалены!', function(){
				$page.lock();
				admin_shop.deleteGroup(id, function(){
					$page.unlock();
					$page.sticky('Удаление категории','Категория и все товары в ней были удалены.');
					grid.product_list.start();
				});
			});
		} else {
			$page.confirm('Подтвердите удаление','Вы действительно хотите удалить этот товар? Данные будет невозможно восстановить!', function(){
				$page.lock();
				admin_shop.deleteProduct(id, function(){
					$page.unlock();
					$page.sticky('Удаление товара','товар были удален.');
					grid.product_list.start();
				});
			});
		}
	},
		
	appendFilter : function() {
		var feed = $('select[name="filter_feed"]', $page.current).val();
		var avaliable = $('select[name="filter_avaliable"]', $page.current).val();
		var sales = $('select[name="filter_sales"]', $page.current).val();
		var vendor = $('select[name="filter_vendor"]', $page.current).val();
		var category = $('select[name="filter_category"]', $page.current).val();
		
		if(!feed) {
			grid.product_list.removeFilter('feed');
		} else {
			grid.product_list.addFilter('feed', feed);
		}
		
		if(!avaliable) {
			grid.product_list.removeFilter('avaliable');
		} else {
			grid.product_list.addFilter('avaliable', avaliable);
		}
		
		if(!sales) {
			grid.product_list.removeFilter('sales');
		} else {
			grid.product_list.addFilter('sales', sales);
		}
		
		if(!vendor) {
			grid.product_list.removeFilter('vendor');
		} else {
			grid.product_list.addFilter('vendor', vendor);
		}
		
		if(!category) {
			grid.product_list.removeFilter('category');
		} else {
			grid.product_list.addFilter('category', category);
		}
		
		grid.product_list.start();
		$('#grid-product_list-filters').slideToggle();
	},
	
	clearFilter : function() {
		grid.product_list.clearFilter();
		grid.product_list.start();
		
		$('select[name="filter_feed"]', $page.current).parent().find('.search-choice-close').trigger('mousedown').trigger('mouseup');	
		$('select[name="filter_avaliable"]', $page.current).parent().find('.search-choice-close').trigger('mousedown').trigger('mouseup');	
		$('select[name="filter_sales"]', $page.current).parent().find('.search-choice-close').trigger('mousedown').trigger('mouseup');
		$('select[name="filter_vendor"]', $page.current).parent().find('.search-choice-close').trigger('mousedown').trigger('mouseup');
		$('select[name="filter_category"]', $page.current).parent().find('.search-choice-close').trigger('mousedown').trigger('mouseup');
		
		$('#grid-product_list-filters').slideToggle();
	},
	
	makeGroupActionOnCatalog : function() {
		var groupActions = {
				paste: function() {
					console.log('paste')
				},
				remove: function() {
					console.log('remove')
				},
				
				hide: function() {
					console.log('hide')
				},
				change: function() {
					$page.lock();
					var preloader = [
		               {product_feeds:['admin_shop','getFeeds']},
				       {product_vendor:['admin_shop','getVendorsListShort']}
					];
					
					tpl.get(['product.group.change.html', 'shop', preloader],'shop', {}, function(tpl){
						$page.unlock();
						
					});
				},
				category: function() {
					console.log('category')
				}
		};
		
		for(var i in groupActions) {
			grid.product_list.groupAction.on(i, groupActions[i]);
		}
	},
	
	showList : function(route) {
		
		
		if(typeof(grid.product_list) != 'undefined') {
			$('#catalog_crumbs').html('').hide();
			$('#addProductCategoryButton').attr('href', '#/add_cat/0/');
			$('#addProductButton').attr('href', '#/add/0/');
			
			shopMain.makeGroupActionOnCatalog();
			grid.product_list.removeFilter('group_id');
			grid.product_list.start(shopMain.showCatalogCallback);
			
			// TODO: remove this bug, pshon
			if(!$page.current.is(':visible')) {
				$page.current.show();
			}
		} else {
			$('#catalog_crumbs').html('').hide();
			var preloader = [
			       {shopFeeds:['admin_shop','getFeeds']},
			       {shopVendors:['admin_shop','getVendorsListShort']},
			       {shopCategory:['admin_shop','getProductCategoriesAll']}
			];
			$page.show(['catalog.html', 'shop', preloader], false, function(current){
				$('#grid-product_list-filters').hide();
				
				shopMain.makeGroupActionOnCatalog();
				grid.product_list.removeFilter('group_id');
				grid.product_list.start(shopMain.showCatalogCallback);
				$('#addProductCategoryButton', current).attr('href', '#/add_cat/0/');
				$('#addProductButton').attr('href', '#/add/0/');
			});
		}
	},
	
	showListNew : function() {
		var preloader = false;
		
		$page.show(['catalog.new.html', 'shop', preloader], false, function(current){
			// load category tree
			// load all products
			$page.lock();
			var state = 1;
			shopMain.loadCategoryTree(function(){
				if(state == 1) $page.unlock();
				state++;
			});
			
			
			
			/*
			$('#grid-product_list-filters').hide();
			grid.product_list.removeFilter('group_id');
			grid.product_list.start(shopMain.showCatalogCallback);
			$('#addProductCategoryButton', current).attr('href', '#/add_cat/0/');
			$('#addProductButton').attr('href', '#/add/0/');*/
		});
		
		
		//console.log('new controll');
	},
		
	loadCategoryTree : function(callback) {
		$('.basic_tree',$page.current).html('');
		
		admin_shop.getGroupListForTree(0, function(nodes){
			var addHoverDom = function(treeId, treeNode) {
				var sObj = $("#" + treeNode.tId + "_span");
				if (treeNode.editNameFlag || $("#addBtn_"+treeNode.tId).length>0) return;
				var addStr = "<i class='icon-plus bigediticon' id='addBtn_" + treeNode.tId+ "' title='add node' onfocus='this.blur();'></i>";
				sObj.after(addStr);
				var btn = $("#addBtn_"+treeNode.tId);
				if (btn) btn.bind("click", function(){
					var zTree = $.fn.zTree.getZTreeObj("treeDemo");
					zTree.addNodes(treeNode, {id:(100 + newCount), pId:treeNode.id, name:"new node" + (newCount++)});
					return false;
				});
			};
			
			var removeHoverDom = function(treeId, treeNode) {
				$("#addBtn_"+treeNode.tId).unbind().remove();
			};
			
			var setting = {
				edit: {
					enable: true,
					showRemoveBtn: true,
					showRenameBtn: true,
					removeTitle : 'Удалить категорию',
					renameTitle : "Редактировать категорию",
					addHoverDom: addHoverDom,
					removeHoverDom: removeHoverDom,
				},
				data: {
					simpleData: {
						enable: true
					},
				
				},
				callback: {
					beforeDrag: function(treeId, treeNodes) {
						for (var i=0,l=treeNodes.length; i<l; i++) {
							if (treeNodes[i].drag === false) {
								console.log('drag', treeNodes[i])
								return false;
							}
						}
						
						return true;
					},
					beforeDrop: function(treeId, treeNodes, targetNode, moveType) {
						console.log(arguments);
						return targetNode ? targetNode.drop !== false : true;
					},
					
					onDblClick: function(event, treeId, treeNode) {
						console.log('dbl click', treeNode);
					},
					
					onClick : function(event, treeId, treeNode, clickFlag) {
						if(clickFlag == 1) {
	
						}
					}
				},
				async : {
					func : admin_shop.getGroupListForTree,
					enable : true
				},
				
				view: {
					selectedMulti: false,
					showIcon: false
				}
			};
				
			shopMain.treeMain = $.fn.zTree.init($('.basic_tree',$page.current), setting, nodes);
			if(typeof(callback) == 'function') {
				callback(shopMain.treeMain);
			}
		});
	},
	
	showCatalogCallback : function() {
		// sort category functions
		this.dom.find("tr[row-type='folder']").hover(function(){
			$(this).find('.showOnHover').css({display:'inline-block'});
		}, function(){
			$(this).find('.showOnHover').css({display:'none'});
		});
		
		this.dom.find("tr[row-type='folder'] td[name='photo'] div.folder").css({cursor:'move'});
		this.dom.find("tbody").sortable({
			handle : 'td[name="photo"] div.folder',
			opacity: 0.8,
			update : function() {
				var order = 1;
				var sort = [];
				var dom = grid.product_list.dom;
				
				dom.find("tbody tr[row-type='folder']").each(function(){
					var localId = $(this).attr('local-row-id');
					var item = grid.product_list.rows.get(localId);
					if(typeof(item) == 'object') {
						sort.push({'group_id':item.group_id, 'order':order});
						order += 1;
					}
				});
				
				$page.lock();
				admin_shop.reorderCategory(sort, function(){
					$page.unlock();
				});
			}
		});
	},
	
	showListSub : function(id, type, extended1) {
		console.log('show sub');
		if(extended1 && extended1.length) {
			var page = parseInt(extended1.substr(0, extended1.length -1));
			page = (!isNaN(page) && page != undefined && page > 0)? page-1 : false;
		}
		
		if(type == 'folder') {
			$page.lock();
			admin_shop.getAdminCatalogCrumbs(id, function(result){
				if(typeof(grid.product_list) != 'undefined') {
					
					$('#catalog_crumbs').html(shopMain.makeCatalogCrumbs(result)).show();
					$('#addProductCategoryButton').attr('href', '#/add_cat/'+id+'/');
					$('#addProductButton').attr('href', '#/add/'+id+'/');
					
					shopMain.makeGroupActionOnCatalog();
					grid.product_list.addFilter('group_id', id);
					grid.product_list.start(shopMain.showCatalogCallback);
					
					// TODO: remove this bug, pshon
					if(!$page.current.is(':visible')) {
						$page.current.show();
					}
				} else {
					var groupId = id;
					var preloader = [{shopFeeds:['admin_shop','getFeeds']}];
					$page.show(['catalog.html', 'shop',preloader], false, function(current){
						$('#catalog_crumbs').html(shopMain.makeCatalogCrumbs(result)).show();
						$('#grid-product_list-filters').hide();
						$('#addProductCategoryButton').attr('href', '#/add_cat/'+groupId+'/');
						$('#addProductButton').attr('href', '#/add/'+groupId+'/');
						shopMain.makeGroupActionOnCatalog();
						grid.product_list.addFilter('group_id', groupId);
						grid.product_list.start(shopMain.showCatalogCallback);
					});
				}
			})
		} else {
			shopMain.editItem(id);
		}
	},

	saveNewItem : function(container, back) {
		var form = this.getProductFormRawData(container);
	
		// TODO: Првоерка на заполненость формы!
		$page.lock();
		admin_shop.addProduct(form, function(productId) {
			if(back === true) {
				$page.unlock();
				$page.sticky('Товар успешно добавлен!','Товар успешно добавлен!');				
				if(typeof(grid.product_list) == 'object') {
					$page.shadowBack(function(){
						grid.product_list.start();
					});
				} else {
					$page.go('/');
				}
			} else {
				$page.sticky('Товар успешно добавлен!','Товар успешно добавлен!');
				$page.router.setRoute('/edit/'+productId+'/');
			}
		});
	},
	
	saveEditItem : function(id, container, back) {
		var form = this.getProductFormRawData(container);
		var productId = id;
		var self = this;
		$page.lock();
		
		var modSort = [];
		$page.current.find('table[name="modifications"] tbody tr').each(function(){
			modSort.push($(this).attr('item-id'));
		});
		
		form.modifications_sort = modSort;
		
		admin_shop.addProduct(form, id, function(result) {
			if(back === true) {
				$page.unlock();
				$page.sticky('Товар успешно изменен!','Данные в списке обновляются автоматически.');
				
				if(back === true) {					
					window.history.back();
					if(typeof(grid.product_list) == 'object') {
						$page.shadowBack(function(){
							grid.product_list.start();
						});
					} else {
						$page.go('/');
					}
				}
			} else {
				self.updateProductFormAfterSave(productId, function(){
					$page.unlock();
					$page.sticky('Товар успешно изменен!','Данные в списке обновляются автоматически.');
				});
			}
		});
	},
	
	editCat : function(categoryId) {
		var preloadTplData = [
		      	{category_list:['admin_shop','getProductCategoriesAll']},
		      	{group_tree:['admin_shop','getGroupTreePlain']},
		      	{category:['admin_shop','getGroupInfo', [categoryId]]}
		];
		
		$page.show(['category.edit.html', 'shop', preloadTplData], {categoryId:categoryId}, function(current){
			$page.bind('back',function(){
				if(typeof(grid.product_list) == 'object') {
					$page.histiry_back();
						grid.product_list.start();
				} else {
					$page.go('/');
				}
			});

			$page.bind('saveandexit',function(){
				var data = $page.getForm(current);
				if(!data.check) return false;
				data.data.description2 = shopMain.textEditor4.getData();
				$page.formMessage.clear();
				$page.lock();
				var parentId = $('select[name="c.parent_id"]',current).val();
				admin_shop.addCategory(categoryId, parentId, data.data, function(result){
					if(typeof(result) == 'string') {
						$page.unlock();
						$page.formMessage.error('Ошибка!',result);
					} else {
						admin_shop.getGroupTreePlain(function(data){
							shopMain.categoryList = data;
							$page.unlock();
							if(typeof(grid.product_list) == 'object') {
								$page.histiry_back();
									grid.product_list.start();
							} else {
								$page.go('/');
							}
						});
					}
				});			
			});
			
			$page.bind('save',function(){
				var data = $page.getForm(current);
				if(!data.check) return false;
				data.data.description2 = shopMain.textEditor4.getData();
				$page.formMessage.clear();
				$page.lock();
				var parentId = $('select[name="c.parent_id"]',current).val();
				admin_shop.addCategory(categoryId, parentId, data.data, function(result){
					if(typeof(result) == 'string') {
						$page.unlock();
						$page.formMessage.error('Ошибка!',result);
					} else {
						admin_shop.getGroupTreePlain(function(data){
							shopMain.categoryList = data;
							$page.unlock();
							$page.sticky('Данные успешно обновлены.', ' ');
						});
					}
				});			
			});

			$('input[name="c.name"]', container).bind('change keyup mouseup',function(){
				var rewrite = $page.transliteral($(this).val())+'/';
				$('input[name="c.url"]', container).val(rewrite);
			});
			
			var vendorImage = uploader.init({
				container : $(".CategoryLogoUploadButton",current),
				hideUploaded : true,
				formCaption : '',
				resize : ['50','500w'],
				done : function(info) {
					$('.CategoryLogoContainer',current).html('');
					var item = $('<div></div>').addClass('imageTumbContainer').css({backgroundImage:'url("'+info[1].name+'")'});
					item.append('<input type="hidden" name="image_preview" value="'+info[0].name+'">');
					item.append('<input type="hidden" name="image_original" value="'+info[1].name+'">');
					$('.CategoryLogoContainer',current).append(item);
				}
			});
			
			var vendorImage2 = uploader.init({
				container : $(".CategoryIconUploadButton",current),
				hideUploaded : true,
				formCaption : '',
				resize : ['100w'],
				done : function(info) {
					$('.CategoryIconContainer',current).html('');
					var item = $('<div></div>').addClass('imageTumbContainer').css({backgroundImage:'url("'+info[0].name+'")'});
					item.append('<input type="hidden" name="image_icon" value="'+info[0].name+'">');
					$('.CategoryIconContainer',current).append(item);
				}
			});
			
			
			var text = current.find('textarea[name="c.description2"]').val();
			current.find('textarea[name="c.description2"]').remove();
			shopMain.textEditor4 = CKEDITOR.appendTo('categoryFormTextEditor', shopMain.editorSettings, text);
		});
	},
	
	addCat : function(categoryId) {
		var categoryId = categoryId;
		var preloadTplData = [
		        {category_list:['admin_shop','getYandexProductCategories']},
		        {group_tree:['admin_shop','getGroupTreePlain']}
		];
				
		$page.show(['category.edit.html', 'shop', preloadTplData], {categoryId:categoryId}, function(current){
			$page.bind('back',function(){
				if(typeof(grid.product_list) == 'object') {
					$page.histiry_back();
						grid.product_list.start();
				} else {
					$page.go('/');
				}
			});

			$('input[name="c.name"]', container).bind('change keyup mouseup',function(){
				var rewrite = $page.transliteral($(this).val())+'/';
				$('input[name="c.url"]', container).val(rewrite);
			});
			
		
			shopMain.textEditor4 = CKEDITOR.appendTo('categoryFormTextEditor', shopMain.editorSettings, '');
			
			$page.bind('save',function(){
				var data = $page.getForm(current);
				if(!data.check) return false;
				
				$page.formMessage.clear();
				$page.lock();
				var parentId = $('select[name="c.parent_id"]',current).val();
				data.data.description2 = shopMain.textEditor4.getData();
				
				admin_shop.addCategory(false, parentId, data.data, function(result){
					if(typeof(result) == 'string') {
						$page.unlock();
						$page.formMessage.error('Ошибка!',result);
					} else {
						admin_shop.getGroupTreePlain(function(data){
							shopMain.categoryList = data;
							$page.unlock();
							$page.sticky('Данные успешно обновлены.', ' ');
						});
					}
				});			
			});
			
			$page.bind('saveandexit',function(){
				var data = $page.getForm(current);
				if(!data.check) return false;
				
				$page.formMessage.clear();
				$page.lock();
				var parentId = $('select[name="c.parent_id"]',current).val();
				data.data.description2 = shopMain.textEditor4.getData();
				
				admin_shop.addCategory(false, parentId, data.data, function(result){
					if(typeof(result) == 'string') {
						$page.unlock();
						$page.formMessage.error('Ошибка!',result);
					} else {
						admin_shop.getGroupTreePlain(function(data){
							shopMain.categoryList = data;
							$page.unlock();							
							if(typeof(grid.product_list) == 'object') {
								$page.histiry_back();
									grid.product_list.start();
							} else {
								$page.go('/');
							}
						});
					}
				});			
			});

			
			
			$('input[name="c.name"]',current).val('');
			$('.CategoryLogoContainer',current).html('');
			
			var vendorImage = uploader.init({
				container : $(".CategoryLogoUploadButton",current),
				hideUploaded : true,
				formCaption : '',
				resize : ['50','500w'],
				done : function(info) {
					$('.CategoryLogoContainer',current).html('');
					var item = $('<div></div>').addClass('imageTumbContainer').css({backgroundImage:'url("'+info[1].name+'")'});
					item.append('<input type="hidden" name="image_preview" value="'+info[0].name+'">');
					item.append('<input type="hidden" name="image_original" value="'+info[1].name+'">');
					$('.CategoryLogoContainer',current).append(item);
				}
			});
			
			var vendorImage2 = uploader.init({
				container : $(".CategoryIconUploadButton",current),
				hideUploaded : true,
				formCaption : '',
				resize : ['100w'],
				done : function(info) {
					$('.CategoryIconContainer',current).html('');
					var item = $('<div></div>').addClass('imageTumbContainer').css({backgroundImage:'url("'+info[0].name+'")'});
					item.append('<input type="hidden" name="image_icon" value="'+info[0].name+'">');
					$('.CategoryIconContainer',current).append(item);
				}
			});
			
		});
	},
	
	addItem : function(groupId) {
		var self = this;
		var groupId = groupId;

		
		admin_shop.getProductFormSupport(groupId, function(info) {
			var preloadTplData = [
			        {feature_unit_list:['admin_shop','getFeatureUnits']},
			        {product_feeds : ['admin_shop','getFeeds']}
			];

			$page.show(['product.edit.html', 'shop', preloadTplData], {}, function(current){				
				$page.bind('save', function(){
					shopMain.saveNewItem(current,false);
				});

				$page.bind('back', function(){
					if(typeof(grid.product_list) == 'object') {
						$page.shadowBack(function(){
							grid.product_list.start();
						});
					} else {
						$page.go('/');
					}
				});
				
				$page.bind('saveback', function(){
					shopMain.saveNewItem(current,true);
				});
				

				$page.current.find('table[name="modifications"] tbody').append($('<tr name="nodata"><td colspan="7" align="center">Нет модификаций</td></tr>'));
				$page.bind('add-mod', function(){
					shopMain.addModification(0);
				});

				$('input[name="p.name"]', container).bind('change keyup mouseup',function(){
					var rewrite = '/catalog/' + $page.transliteral($(this).val())+'/';
					$('input[name="p.url"]', container).val(rewrite);
				});
				
				$('a[name="p.updare-rewrite"]').hide();
				
				shopMain.productImages = false;
				shopMain.UIproductForm(current);
				shopMain.addExtendedCategory(groupId);
				shopMain.makeStores(info.stores);
				shopMain.makeAvaliable({type:3});
				shopMain.makeProductCategory(info.category_id);
				shopMain.makeProductVendor();
				shopMain.textEditor = CKEDITOR.appendTo('productFormTextEditor', shopMain.editorSettings, '');
				shopMain.textEditor2 = CKEDITOR.appendTo('productFormTextEditor2', shopMain.editorSettings, '');
				shopMain.textEditor3 = CKEDITOR.appendTo('productFormTextEditor3', shopMain.editorSettings, '');
			});
		});
	},
	
	editItem : function(id) {
		var self = this;
		var productId = id;
		admin_shop.getProductEditInfo(id,function(productInfo){
			var preloadTplData = [
			  {feature_unit_list:['admin_shop','getFeatureUnits']},
			  {product_feeds : ['admin_shop','getProductFeeds',[productId, true]]}
			];
			
			$page.show(['product.edit.html', 'shop', preloadTplData], {formSectionActive:'featuresу', productCurrency : productInfo.currency}, function(current){
				shopMain.productImages = false;
				shopMain.UIproductForm(current);
				shopMain.addExtendedCategory(productInfo.group_id);
				
				if(productInfo.extended_groups.length > 0) {
					for(var i in productInfo.extended_groups) {
						shopMain.addExtendedCategory(productInfo.extended_groups[i].group_id);
					}
				}
				
				$('input[name="p.name"]').val(productInfo.title);
				$('input[name="p.article"]').val(productInfo.article);
				$('input[name="p.url"]').val(productInfo.rewrite);
				
				
				$('textarea[name="p.description"]').val(productInfo.description);
				
				if(productInfo.status_id == 2) {
					$('[name="p.published"]',current).removeAttr('checked').trigger('change');
				}
				
				$('[name="p.retail_price"]').val(productInfo.price);
				$('[name="p.cost_price"]').val(productInfo.price_cost);
				
				$('[name="p.seo_description"]').val(productInfo.seo_description);
				$('[name="p.seo_keywords"]').val(productInfo.seo_keywords);
				
				if(typeof(productInfo.price2) != 'undefined') {
					$('[name="p.price2"]').val(productInfo.price2);
				}
				
				if(typeof(productInfo.price3) != 'undefined') {
					$('[name="p.price3"]').val(productInfo.price3);
				}
				
				if(typeof(productInfo.price4) != 'undefined') {
					$('[name="p.price4"]').val(productInfo.price4);
				}
				
				if(typeof(productInfo.price5) != 'undefined') {
					$('[name="p.price5"]').val(productInfo.price5);
				}
				
				if(typeof(productInfo.pack_unit) != 'undefined') {
					$('[name="p.product_measure"]').find('option[value="'+productInfo.pack_unit+'"]').attr('selected', 'selected');
				}
				
				$('[name="p.product_measure"]').addClass('chosen').chosen();
				
				if(typeof(productInfo.pack_size) != 'undefined') {
					$('[name="p.pakage_size"]').val(productInfo.pack_size);
				}

				if(typeof(productInfo.modifications) == 'object' && productInfo.modifications.length > 0) {
					var container = $page.current.find('table[name="modifications"] tbody');
					container.find('tr[name="nodata"]').remove();
					for(var i in productInfo.modifications) {
						shopMain.makeModification(productInfo.modifications[i], productId);
					}
				} else {
					$page.current.find('table[name="modifications"] tbody').append($('<tr name="nodata"><td colspan="7" align="center">Нет модификаций</td></tr>'));
				}
				
				$page.bind('add-mod', function(){
					shopMain.addModification(productId);
				});

				
				if(productInfo.sales) {
					$('[name="p.product_sales"]').attr('checked','checked').trigger('change');
					$('[name="p.periodic_price_new"]').val(productInfo.sales_summ);
					$('[name="p.periodic_price_procent"]').val(productInfo.sales_procent);
					
					if(productInfo.sales == 2) {
						$('[name="p.useTimeAction"]').attr('checked','checked').trigger('change');
						$('[name="p.timeActionStartDate"]').val(productInfo.sales_start);
						$('[name="p.timeActionStopDate"]').val(productInfo.sales_end);
					}
				}
				
				if(productInfo.dimensions) {
					$('div[name="width"]').find('input.basic-size').val(productInfo.dimensions.width);
					$('div[name="height"]').find('input.basic-size').val(productInfo.dimensions.height);
					$('div[name="depth"]').find('input.basic-size').val(productInfo.dimensions.depth);
					$('div[name="weight"]').find('input.basic-size').val(productInfo.dimensions.weight);
					
					$('div[name="width"]').find('select option:selected').removeAttr('selected')
					$('div[name="width"]').find('select option[value="'+productInfo.dimensions.width_unit+'"]').attr('selected','selected');
			
					$('div[name="height"]').find('select option:selected').removeAttr('selected')
					$('div[name="height"]').find('select option[value="'+productInfo.dimensions.height_unit+'"]').attr('selected','selected');
					
					$('div[name="depth"]').find('select option:selected').removeAttr('selected')
					$('div[name="depth"]').find('select option[value="'+productInfo.dimensions.depth_unit+'"]').attr('selected','selected');
					
					$('div[name="weight"]').find('select option:selected').removeAttr('selected')
					$('div[name="weight"]').find('select option[value="'+productInfo.dimensions.weight_unit+'"]').attr('selected','selected');
				}

				shopMain.textEditor = CKEDITOR.appendTo('productFormTextEditor', shopMain.editorSettings, productInfo.full_description);
				shopMain.textEditor2 = CKEDITOR.appendTo('productFormTextEditor2', shopMain.editorSettings, productInfo.full_description2);
				shopMain.textEditor3 = CKEDITOR.appendTo('productFormTextEditor3', shopMain.editorSettings, productInfo.full_description3);
							
				if(productInfo.images.length > 0) {
					for(var i in productInfo.images) {
						shopMain.addImageToProduct(productInfo.images[i], false);
					}
				}

				shopMain.makeProductCategory(productInfo.category_id, productId);
				shopMain.makeProductVendor(productInfo.vendor_id);
				shopMain.makeProductFeatures(productInfo.user_features);
				
				if(typeof(productInfo.avaliable.store) != 'undefined' &&  typeof(productInfo.avaliable.store.store_id) != 'undefined') {
					shopMain.makeStores(productInfo.stores, productInfo.avaliable.store.store_id);
				} else {
					shopMain.makeStores(productInfo.stores);
				}
				
				shopMain.makeAvaliable(productInfo.avaliable);
				
				$('a[name="p.updare-rewrite"]').show().click(function(){
					var name = $page.current.find('input[name="p.name"]').val();
					var rewrite = '/catalog/'+ $page.transliteral(name) + '-' + productId + '/';
					$page.current.find('input[name="p.url"]').val(rewrite);
				});
				
				
				$page.bind('save', function(){
					shopMain.saveEditItem(productId, current, false);
				});
				
				$page.bind('back', function(){
					if(typeof(grid.product_list) == 'object') {
						$page.shadowBack(function(){
							grid.product_list.start();
						});
					} else {
						$page.go('/');
					}
					
				});
				
				$page.current.find('table[name="modifications"] tbody').sortable({
					handle : '.moveModificationIcon',
					opacity: 0.8,
					update : function() {
						shopMain.calcModificationOrder()
					}
				});
				
				$page.bind('saveback', function(){
					shopMain.saveEditItem(productId, current,true);
				});
				
			});
		});
	},
	
	calcModificationOrder : function() {
		
	},
	
	editModification : function(id, pid, callback) {
		var productId = id;
		var parent_id = pid;
		
		$page.lock();
		admin_shop.getModificationById(id, function(data){
			$page.show(['product.edit.mod.html', 'shop'], false, function(current){
				$page.unlock();
				current.find('input[name="m.name"]').val(data.title);
				current.find('input[name="m.description"]').val(data.description);
				current.find('input[name="m.article"]').val(data.article);
				current.find('input[name="m.price1"]').val(data.price);
				current.find('input[name="m.price2"]').val(data.price2);
				current.find('input[name="m.price3"]').val(data.price3);
				current.find('input[name="m.price4"]').val(data.price4);
				current.find('input[name="m.price5"]').val(data.price5);
				current.find('input[name="m.pakage_size"]').val(data.pack_size);
				
				current.find('select[name="m.product_measure"]').find('option[value="'+data.pack_unit+'"]').attr('selected','selected')
				current.find('select[name="m.product_measure"]').chosen();
				
				current.find('div[name="m.pricetype.container"] input[type="radio"]:checked').removeAttr('checked').parent().removeClass('checked');
				current.find('div[name="m.pricetype.container"] input[value="'+data.price_type+'"]').attr('checked','checked').trigger('change').parent().addClass('checked');
				
				current.find('div[name="m.minorder.container"] input[type="radio"]:checked').removeAttr('checked').parent().removeClass('checked');
				current.find('div[name="m.minorder.container"] input[value="'+data.min_order+'"]').attr('checked','checked').trigger('change').parent().addClass('checked');
			
				$page.bind('back', function(){
					$page.removeLast();
					$page.showLast();
					$page.top();
				});
				
				$page.bind('save', function(){
					var mod = $page.getForm(current);
					if(!mod.check) {
						$page.top();
						return;
					}
					
					var item = {
							name : mod.data['m.name'],
							description : mod.data['m.description'],
							article : mod.data['m.article'],
							price : mod.data['m.price1'],
							price2 : mod.data['m.price2'],
							price3 : mod.data['m.price3'],
							price4 : mod.data['m.price4'],
							price5 : mod.data['m.price5'],
							pakage : mod.data['m.pakage_size'],
							unit : mod.data['m.product_measure'],
							price_type : mod.data['m.price_type'],
							minorder : mod.data['m.min_order_type']
					};
					
										
					$page.lock();
					admin_shop.editModification(productId, parent_id, item, function(mod) {
						$page.unlock();
						$page.removeLast();
						$page.showLast();
						$page.top();
						if(typeof(callback) == 'function') {
							callback(mod);
						}
					});
				});
			});
		});
	},
		
	deleteModification : function(id) {
		$page.confirm('Удаление модификации', 'Вы действительно хотите удалить эту модификацию товара?', function(){
			$page.lock();
			admin_shop.removeModification(id, function(){
				var container = $page.current.find('table[name="modifications"] tbody').find('tr[item-id="'+id+'"]').remove();
				$page.unlock();
			});
		});
	},
	
	addModification : function(productId){
		$page.show(['product.edit.mod.html', 'shop'], false, function(current){
			$page.bind('back', function(){
				$page.removeLast();
				$page.showLast();
				$page.top();
			});
			
			$page.bind('save', function(){
				var mod = $page.getForm(current);
				if(!mod.check) {
					$page.top();
					return;
				}
				
				var item = {
						name : mod.data['m.name'],
						description : mod.data['m.description'],
						article : mod.data['m.article'],
						price : mod.data['m.price1'],
						price2 : mod.data['m.price2'],
						price3 : mod.data['m.price3'],
						price4 : mod.data['m.price4'],
						price5 : mod.data['m.price5'],
						pakage : mod.data['m.pakage_size'],
						unit : mod.data['m.product_measure'],
						price_type : mod.data['m.price_type'],
						minorder : mod.data['m.min_order_type']
				};
				
									
				$page.lock();
				admin_shop.editModification(0, productId, item, function(mod) {
					$page.unlock();
					$page.removeLast();
					$page.showLast();
					$page.top();
					shopMain.makeModification(mod, 0);
				});
			});
		});
	},
	
	makeModification : function(data,fromId) {
		var container = $page.current.find('table[name="modifications"] tbody');
		var tpl = container.find('tr[name="tpl"]').clone();
		tpl.removeAttr('name').attr('item-id', data.product_id).find('input[skip-data]').removeAttr('skip-data');
		
		tpl.find('input[name="m.article"]').val(data.article);
		tpl.find('input[name="m.title"]').val(data.title);
		tpl.find('input[name="m.price1"]').val(data.price);
		tpl.find('input[name="m.price2"]').val(data.price2);
		tpl.find('input[name="m.price3"]').val(data.price3);
		tpl.find('input[name="m.price4"]').val(data.price4);
		tpl.find('input[name="m.price5"]').val(data.price5);
		tpl.find('[mod-action="edit"]').bind('click', function(){
			shopMain.editModification(data.product_id, fromId, function(mod){
				tpl.find('input[name="m.article"]').val(mod.article);
				tpl.find('input[name="m.title"]').val(mod.title);
				tpl.find('input[name="m.price1"]').val(mod.price);
				tpl.find('input[name="m.price2"]').val(mod.price2);
				tpl.find('input[name="m.price3"]').val(mod.price3);
				tpl.find('input[name="m.price4"]').val(mod.price4);
				tpl.find('input[name="m.price5"]').val(mod.price5);
				console.log('saveOk!', mod);
			});
		});
		tpl.find('[mod-action="copy"]').bind('click', function(){
			shopMain.copyModification(data.product_id, fromId);
		});
		tpl.find('[mod-action="del"]').bind('click', function(){
			shopMain.deleteModification(data.product_id, fromId);
		});
		tpl.show();
		
		container.append(tpl);
	},
	
	makeProductFeatures : function(data) {
		for(var i in data) {
			shopMain.addFeatureBox($('.otherFeatureButton', $page.current), data[i]);
		}
	},
	
	addFeatureBox : function(obj, data) {
		var box = $('#CustomFeatureTpl').clone().removeAttr('id');
		var isNew = (typeof(data) == 'object')? false:true;
		var itemNum = parseInt($('#CustomFeatureTpl').attr('box-number')) +1;
		
		box.show().find('[is_feature_input]').each(function(){
			$(this).removeAttr('skip_get_form');
			var inputName = $(this).attr('name');
			var name = ($(this).attr('name') == 'value')? 'customFeature['+itemNum+'][v'+$(this).attr('ftype')+']' : 'customFeature['+itemNum+']['+$(this).attr('name')+']';
			$(this).attr('name', name);
			if(!isNew) {
				if(inputName == 'name') {
					$(this).val(data.name)
				}
				
				if(inputName == 'value' && $(this).attr('ftype') != 2) {
					$(this).val(data.value)
				} else {
					if($(this).attr('ftype') == 2) {
						if($(this).val() == data.value) {
							$(this).attr('checked', 'checked');
						} else {
							$(this).removeAttr('checked');
						}
					}
				}
			}
			
		});
		box.attr('isuserfeature',1);
		
		$(obj).parent().before(box);
		$('[ftype="4"] input',box).tagsInput({width: 310, defaultText:'Добавить'});
		
		if(!isNew) {
			box.append('<input type="hidden" name="customFeature['+itemNum+'][fid]" value="'+data.id+'">');
			box.append('<input type="hidden" name="customFeature['+itemNum+'][vid]" value="'+data.variant_id+'">');
		}
		
		var selectBoxType = $('select.feture-type-select',box);
				
		selectBoxType.bind('change', function(){
			var ftype = $(this).val();
			
			$(this).parent().find('[ftype]').hide();
			$(this).parent().find('[ftype="'+ftype+'"]').show();
		});
		
		$('#CustomFeatureTpl').attr('box-number',itemNum);
		
		if(!isNew) {
			selectBoxType.find('option[selected]').removeAttr('selected');
			selectBoxType.find('option[value='+data.type+']').attr('selected','selected');
			selectBoxType.trigger('change');
		}
	},
	
	updateProductFormAfterSave : function(productId, callback) {
		$('.otherFeatureButton', $page.current).parent().parent().find('div[isuserfeature]').remove();
		
		var self = this;
		admin_shop.getProductFeatures(productId,function(features){
			self.makeProductFeatures(features);
			if(typeof(callback) == 'function') callback();
		});
	},
	
	removeImage : function(imageIndex) {
		$("#productImageGallery div[imageindex="+imageIndex+"]").remove();
		for(var i in shopMain.productImages) {
			if(shopMain.productImages[i][0].uniqid == imageIndex) {
				if(typeof(shopMain.productImages[i][0].img_id) != 'undefined') {
					shopMain.removeActionsHelper.images.push(shopMain.productImages[i][0].img_id);
				}
				
				shopMain.productImages.splice(i,1);
				break;
			}
		}
		shopMain.calcImagesOrder();
	},
	
	updateImageStore : function(uniq, data) {
		for(var i in shopMain.productImages) {
			if(shopMain.productImages[i][0].uniqid == uniq) {
				for(var o in shopMain.productImages[i]) {
					shopMain.productImages[i][o]['name'] = data[o].name
				}
				break;
			}
		}
	},
		
	calcImagesOrder : function() {
		var order = 0;
		var index = '';
		
		$("#productImageGallery").sortable('refreshPositions');
		$('#productImageGallery .ProductImageItem[imageindex]').each(function(){
			order += 10;
			index = $(this).attr('imageindex');
			for(var i in shopMain.productImages) {
				if(shopMain.productImages[i][0].uniqid == index) {
					//console.log('changeOrder', shopMain.productImages[i][0].uniqid, shopMain.productImages[i][0].order, order);
					shopMain.productImages[i][0].order = order;
					shopMain.productImages[i][1].order = order;
					shopMain.productImages[i][2].order = order;
					shopMain.productImages[i][3].order = order;
				}
			}
		});
	},
	
	addImageToProduct : function(image, newImage) {		
		if(!shopMain.productImages) shopMain.productImages = [];
		if(newImage) {
			 var order = (typeof(shopMain.productImages) == 'object')? ((shopMain.productImages.length+1)*10) : 10;
			 image[0].order = order;
			 image[1].order = order;
			 image[2].order = order;
			 image[3].order = order;
		}

		var imageIndex = $page.makeUniqId();
		
		image[0].uniqid = imageIndex;
		image[1].uniqid = imageIndex;
		image[2].uniqid = imageIndex;
		image[3].uniqid = imageIndex;
		
		
		shopMain.productImages.push(image);
		
		var tpl = $('<div class="ProductImageItem" imageindex="'+imageIndex+'">'+
						'<div class="thumbnail">'+
						
							'<div class="item" style="width:125px; height:125px; background-image:url(\''+image[1].name+'\'); ">'+
								'<div class="edit_photo_panel">'+
									'<ul>'+
										'<li class="moveAnchor"><i class=" icon-move"></i> <a href="javascript:void(0)">Передвинуть</a></li>'+
										'<li><i class=" icon-pencil"></i> <a name="editImage" href="javascript:void(0)">Редактировать</a></li>'+
										'<li><i class=" icon-trash"></i> <a href="javascript:void(0)" onclick="shopMain.removeImage(\''+imageIndex+'\')">Удалить</a></li>'+
									'</ul>'+
								'</div>'+
							'</div>'+
						'</div>'+
					'</div>');
		
		var editor = new cropper();
		editor.init({
			image : image[3].name,
			imageid : imageIndex,
			trigger : $(tpl).find('a[name="editImage"]'),
			resize : shopMain.imageProductSize,
			done : function(result){
				shopMain.updateImageStore(this.imageid, result.images);
				$("#productImageGallery").find('div[imageindex="'+this.imageid+'"]').find('div.item').css({backgroundImage:'url('+result.images[1].name+')'});
			}
		});
		
		$("#productImageGallery").append($(tpl));
		$("#productImageGallery").sortable({
			handle : '.moveAnchor',
			opacity: 0.8,
			update : function() {
				shopMain.calcImagesOrder()
			}
		});
	},
		
	addVendorToProduct : function() {
		this.addNewVendor(function(vendor){
			shopMain.vendorList.push(vendor);
			shopMain.makeProductVendor(vendor.id);
		});
	},
	
	addProductCategory : function() {
		//$page.show(['product_category.add.html', 'shop'], false, function(current){
		//$page.add('Новая категория продуктов',  ['product_category.add.html', 'shop'], false, function(current){
		
		var preloadTplData = [
  		        {feature_unit_list:['admin_shop','getFeatureUnits']}
  		];
		
		$page.show(['product_category.add.html', 'shop', preloadTplData], false, function(current){
			$page.bind('back',function(){
				$page.removeLast();
	        	$page.showLast();
			});

			$page.bind('save',function(){
				
				var res = {};
				var push_counters = {};
				var patterns = {
		                "validate": /^[a-zA-Z][a-zA-Z0-9_\-\.]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
		                "key":      /[a-zA-Z0-9_\-\.]+|(?=\[\])/g,
		                "push":     /^$/,
		                "fixed":    /^\d+$/,
		                "named":    /^[a-zA-Z0-9_\-\.]+$/
		        };
				
				var push_counter = function(key) {
					 if(push_counters[key] === undefined){
			                push_counters[key] = 0;
			         }
					 
			         return push_counters[key]++;
				};
				
				var build = function(base, key, value){
		            base[key] = value;
		            return base;
		        };
		        
		        $(current).find('input, textarea, select').each(function(){
					var type = $(this).attr('type'),
						name = $(this).attr('name'),
						value = $(this).val();
				
					if((type == 'radio' || type == 'checkbox')  && !$(this).is(':checked')) {
						return;
					}
							
					// skip invalid keys
		            if(!name || !patterns.validate.test(name) || $(this).attr('skip_get_form')){
		                return;
		            }
	
					var k,
		            	keys = name.match(patterns.key),
		            	merge = value,
		            	reverse_key = name;
					
					while((k = keys.pop()) !== undefined){
		                // adjust reverse_key
		                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');
	
		                // push
		                if(k.match(patterns.push)){
		                    merge = build([], push_counter(reverse_key), merge);
		                }
	
		                // fixed
		                else if(k.match(patterns.fixed)){
		                    merge = build([], k, merge);
		                }
	
		                // named
		                else if(k.match(patterns.named)){
		                    merge = build({}, k, merge);
		                }
		                
		                res = $.extend(true, res, merge);
		            }
					
					return res;
		        });
				
		        if(!$page.getForm(current).check) return false;
		        
		        var info = {
		        		name : res.name,
		        		features : res.customFeature
		        }
		        
		        admin_shop.addCustomGroupFeatures(info, function(catId){
		        	$page.removeLast();
		        	$page.showLast();
		        	$page.sticky('Новая категория товаров','Добавлена новая категория товаров.');
		        	$('#productTypeCategory').html('');
		        	shopMain.ymCategoryList = false;
		        	shopMain.makeProductCategory(catId, false);
		        });
		        
				
			});

			
		});
	},
	
	addFeatureBoxForCat : function(obj) { 
		var box = $('#CategoryFeatureTpl').clone().removeAttr('id');
		var isNew = (typeof(data) == 'object')? false:true;
		var itemNum = parseInt($('#CategoryFeatureTpl').attr('box-number')) +1;
		
		box.show().find('[is_feature_input]').each(function(){
			$(this).removeAttr('skip_get_form');
			var inputName = $(this).attr('name');
			var name = ($(this).attr('name') == 'value')? 'customFeature['+itemNum+'][v'+$(this).attr('ftype')+']' : 'customFeature['+itemNum+']['+$(this).attr('name')+']';
			$(this).attr('name', name);
			if(!isNew) {
				if(inputName == 'name') {
					$(this).val(data.name)
				}
				
				if(inputName == 'value') {
					$(this).val(data.value)
				}
			}
			
		});
		$(obj).parent().before(box);
		
		
		if(!isNew) {
			box.append('<input type="hidden" name="customFeature['+itemNum+'][fid]" value="'+data.id+'">');
			box.append('<input type="hidden" name="customFeature['+itemNum+'][vid]" value="'+data.variant_id+'">');
		}
				
		$('select.feture-type-select',box).bind('change', function(){
			var ftype = $(this).val();
			
			box.find('td[ftype]').hide();
			box.find('td[ftype="'+ftype+'"]').show();	
		});
		
		$('button[name=addVarianTo]',box).bind('click', function(){
			var cont = box.find('.variantsList');
			var variantsCount = cont.find('input').length;
			var em = $('<div><input type="text" is_feature_input="true" name="customFeature['+itemNum+'][variant]['+variantsCount+']"><a class="btn"  href="javascript:void(0)" onclick="$(this).parent().remove()"><span class=" icon-trash"></span></a></div>');
			cont.append(em);
		});
		
		$('#CategoryFeatureTpl').attr('box-number',itemNum);
	},
	
	addNewVendor : function(callback) {
		var vendorAddCallback = callback;
		$page.add('Добавление производителя',  ['vendor.add.html', 'shop'], false, function(current){
			$page.bind('back',function(){
				$page.back();
			});

			$page.bind('save',function(){
				var data = $page.getForm(current);
				if(!data.check) return false;
				$page.formMessage.clear();
				$page.lock();
				admin_shop.saveVendor(false, data.data, function(result){
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

			$('input[name="vendor_name"]',current).val('');
			$('.VendorLogoContainer',current).html('');
			
			var vendorImage = uploader.init({
				container : $(".VendorLogoUploadButton",current),
				hideUploaded : true,
				formCaption : '',
				resize : ['50','500w'],
				done : function(info) {
					$('.VendorLogoContainer',current).html('');
					var item = $('<div></div>').addClass('imageTumbContainer').css({backgroundImage:'url("'+info[1].name+'")'});
					item.append('<input type="hidden" name="image_preview" value="'+info[0].name+'">');
					item.append('<input type="hidden" name="image_original" value="'+info[1].name+'">');
					$('.VendorLogoContainer',current).append(item);
				}
			});
		});
	},
		
	loadYMfeatures : function(productCategory, productId) {
		$page.lock();
		
		var productId = typeof(productId != 'undefined')? productId : false;
		
		admin_shop.getYandexFeatureSet(productCategory, productId, function(result) {
			$page.unlock();
			if(typeof(result) == 'object') {
				$('#YMFeatureTable').html('');
				for(var i in result) {
					if(result[i].type == 2) {
						var box = $('#YMFeatureContainer .ym_feature_list_tpl').clone();
						box.removeClass('ym_feature_list_tpl');
						box.find('.name').eq(0).html(result[i].title);
						var select = box.find('.variant-select').eq(0);
							select.attr('name', 'ym-feature['+result[i].id+'][variant]')
							select.removeAttr('skip_get_form');
							
						select.append('<option value="0"></option>');
						for(var v in result[i].list_variant) {
							var selected = (typeof(result[i].variant_id) != 'undefined' && result[i].variant_id == result[i].list_variant[v].id)? 'selected=selected' : '';
							select.append('<option '+selected+' value="'+result[i].list_variant[v].id+'">'+result[i].list_variant[v].value+'</option>');
						}
					} else if(result[i].type == 1) {
						var box = $('#YMFeatureContainer .ym_feature_radio_tpl').clone();
						box.find('.name').eq(0).html(result[i].title);
						box.removeClass('ym_feature_radio_tpl');
						box.find('input[type=radio]').attr('name', 'ym-feature['+result[i].id+'][value]').removeAttr('skip_get_form');
						if(typeof(result[i].value) != 'undefined') {
							if(parseInt(result[i].value) == 1) {
								box.find('input[type=radio]').eq(0).attr('checked','checked');
							} else {
								box.find('input[type=radio]').eq(1).attr('checked','checked');
							}
						}
					} else if(result[i].type == 4) {
						var box = $('#YMFeatureContainer .ym_feature_text_tpl').clone();
						box.find('.name').eq(0).html(result[i].title);
						box.removeClass('ym_feature_text_tpl');
						box.find('input.string').attr('name', 'ym-feature['+result[i].id+'][value]').removeAttr('skip_get_form');;
						if(typeof(result[i].value) != 'undefined') {
							box.find('input.string').val(result[i].value);
						}
						
						if(result[i].unit && result[i].unit != '0') {
							box.find('span.unit').html(result[i].unit).show();
						}
					} else {
						continue;
					}
					
					$('#YMFeatureTable').append(box);
				}
				
				$('#YMFeatureContainer').show();
			}
		});
	},
	
	makeProductCategory : function(selectedCategory, productId) {
		if(!shopMain.ymCategoryList) {
			admin_shop.getProductCategoriesAll(function(data){
				shopMain.ymCategoryList = data;
				shopMain.makeProductCategory(selectedCategory, productId);
			});
			
			return false;
		}
		
		var group = false;
		var container = $('<select><option></option></select>')
							.attr('data-placeholder', 'Выберите тип товара')
							.addClass('span12')
							.attr('name','p.productCategory')
							.css({width:'320px'})
							.bind('change',function(){
								shopMain.loadYMfeatures($(this).val());
							});

		var selectedExists = false;
		
		for(var i in shopMain.ymCategoryList) {
			if(shopMain.ymCategoryList[i].level == 1) {
				group = $('<optgroup label="'+shopMain.ymCategoryList[i].name+'"></optgroup>');
				container.append(group);
				continue;
			}
			
			if(selectedCategory && selectedCategory == shopMain.ymCategoryList[i].id) {
				selectedExists = true;
			}
			
			var selected = (selectedCategory != undefined && selectedCategory == shopMain.ymCategoryList[i].id)? 'selected="selected"' : '';
			group.append($('<option '+selected+' value="'+shopMain.ymCategoryList[i].id+'">'+shopMain.ymCategoryList[i].name.lpad('- ',shopMain.ymCategoryList[i].name.length + shopMain.ymCategoryList[i].level*2)+'</option>'))
		}
		
		$('#productTypeCategory').append(container);
		$(container).chosen({allow_single_deselect: true});
		if(typeof(selectedCategory) != 'undefined' && selectedExists) {
			shopMain.loadYMfeatures(selectedCategory, productId);
		}
	},
	
	makeProductVendor : function(selectedVendor) {
		
		if(!shopMain.vendorList) {
			admin_shop.getVendorsListShort(function(data){
				shopMain.vendorList = data;
				shopMain.makeProductVendor(selectedVendor);
			});
			
			return false;
		}

		var container = $('<select><option></option></select>').attr('data-placeholder', 'Выберите производителя').addClass('span12').attr('name','p.productVendor').css({width:'320px'});
		
		for(var i in shopMain.vendorList) {
			var selected = (selectedVendor != undefined && selectedVendor == shopMain.vendorList[i].id)? 'selected="selected"' : '';
			container.append('<option '+selected+' value="'+shopMain.vendorList[i].id+'">'+shopMain.vendorList[i].name+'</option>');
		}
		
		$('#productVendor').html('');
		$('#productVendor').append(container);
		$(container).chosen({allow_single_deselect: true});
	},
	
	makeStores : function(stores, selectedStore) {
		var storesContainer = $('<select class="chosen" name="p.avaliable_store_id"></select>').css({width:'300px'}).attr('data-placeholder', 'Выберите склад');
		var storeItem = $('<option></option>');
		
		storesContainer.append(storeItem);
		for(var i in stores) {
			storeItem = $('<option value="'+stores[i].id+'">'+stores[i].name+'</option>');
			if(typeof(selectedStore) != 'undefined' && parseInt(selectedStore) == parseInt(stores[i].id)) {
				storeItem.attr('selected','selected');
			}
			storesContainer.append(storeItem);
		}
		$('#pSkladAvaliableBlock .sclad').append(storesContainer);
		storesContainer.chosen();
	},
	
	makeAvaliable : function(avaliable) {
		$('#pSkladAvaliableBlock input[type="radio"]').bind('change',function(){
			if($(this).val() == 1) {
				$('#pSkladAvaliableBlock .groupradio').hide();
				$('#pSkladAvaliableBlock .sclad').show();
			} else if($(this).val() == 2) {
				$('#pSkladAvaliableBlock .groupradio').hide();
				$('#pSkladAvaliableBlock .fororder').show();
			} else {
				$('#pSkladAvaliableBlock .groupradio').hide();
			}
		});
				
		
		$('#pSkladAvaliableBlock input[type="radio"]:checked').removeAttr('checked');
		
		if(avaliable.type == 1) {
			if(typeof(avaliable.store) == 'object') {
				$('#pSkladAvaliableBlock input[name="p.avaliable_qt"]').val(avaliable.store.quantity);
			} else {
				$('#pSkladAvaliableBlock input[name="p.avaliable_qt"]').val('0');
			}
			$('#pSkladAvaliableBlock input[type="radio"]').eq(0).attr('checked', 'checked').trigger('change');
		} else if(avaliable.type == 2) {
			if(typeof(avaliable.store) == 'object') {
				$('#pSkladAvaliableBlock input[name="p.avaliable_pending"]').val(avaliable.store.quantity);
			} else {
				$('#pSkladAvaliableBlock input[name="p.avaliable_pending"]').val('0');
			}
			$('#pSkladAvaliableBlock input[type="radio"]').eq(1).attr('checked', 'checked').trigger('change');
		} else {
			$('#pSkladAvaliableBlock input[name="p.avaliable_qt"]').val('0');
			$('#pSkladAvaliableBlock input[name="p.avaliable_pending"]').val('0');
			$('#pSkladAvaliableBlock input[type="radio"]').eq(2).attr('checked', 'checked').trigger('change');
		}		
	},

	addExtendedCategory : function(selectedCategory) {
		if(!shopMain.categoryList) {
			shopMain.loadCategoryList(function(){
				shopMain.addExtendedCategory(selectedCategory);
			})
			
			return false;
		}
		
		var container = $('<select><option></option></select>').attr('data-placeholder', 'Выберите категорию').addClass('span12').attr('name','p.extendedCategory[]');
		
		
		for(var i in shopMain.categoryList) {
			var selected = (selectedCategory != undefined && selectedCategory == shopMain.categoryList[i].id)? 'selected="selected"' : '';
			container.append('<option '+selected+' value="'+shopMain.categoryList[i].id+'">'+shopMain.categoryList[i].name.lpad('- ',shopMain.categoryList[i].name.length + shopMain.categoryList[i].level*2)+'</option>')
		}
		
		$('#extendedCategoryList').append(container);
		$(container).chosen({allow_single_deselect: true});
	},
		
	loadCategoryList : function(callback) {
		$page.lock();
		admin_shop.getGroupTreePlain(function(data){
			shopMain.categoryList = data;
			$page.unlock();
			callback(data);
		});
	},
	
	UIproductForm : function(container) {
		// Табы
		$(".widget-title .tab-header", container).each(function(itemCntId){
			$(this).bind('click',function(){
				var tab_name = $(this).attr('name');
				var widgget_body = $(this).parent().parent().find('.widget-body').eq(0);
				
				$(this).parent().find('span[data-tab-select]').removeAttr('data-tab-select').css({'font-size': '13px', 'font-weight': 'normal', 'color':'#868686', 'cursor':'pointer'});
				$(this).css({'font-size': '13px', 'font-weight': 'bold', 'color':'#4C4C4C', 'cursor':'normal'}).attr('data-tab-select','1');

				widgget_body.find('.tab-body').hide();
				widgget_body.find('.tab-body[section="'+tab_name+'"]').show();
				
				delete widgget_body;
				delete tab_name;
			});
			
			// если есть атрибут, то кликаем по вкладке
			if($(this).attr('is_active')) {
				$(this).trigger('click');
			}
			
			// кликаем первый элемент, если ни один не выбран
			if(itemCntId+1 == $(".widget-title .tab-header", container).length) {
				if(!$(".widget-title span[data-tab-select]", container).length) {
					$(".widget-title .tab-header", container).eq(0).trigger('click');
				}
			}
		});
				
		$('input[name="p.retail_price"]', container).bind('change keyup',function(){
			var price = $(this).val();
			price = price.replace(/([^0-9\,\.])/g, '');
			price = price.replace(',', '.');
			$(this).val(price);
			
			if($('input[name="p.product_sales"]', $page.current).is(':checked')) {
				var saleProcent = $('input[name="p.periodic_price_new"]', container).val();
				var saleSumm = $('input[name="p.periodic_price_procent"]', container).val();
				
				if(!saleSumm && saleProcent) {
					saleSumm = Math.round(parseFloat(saleProcent)/100*price,2);
					$('input[name="p.periodic_price_new"]', container).val(saleSumm);
				} else if(saleSumm && !saleProcent) {
					saleProcent = Math.round(saleSumm*100/price);
					$('input[name="p.periodic_price_procent"]', container).val(saleProcent);
				}
			}
		});
		
		$('input[name="p.periodic_price_procent"]', container).bind('change keyup',function(){
			var price = $('input[name="p.retail_price"]', container).val();
			var procent = $(this).val().replace(/([^0-9\,\.])/g, '');
			procent = procent.replace(',', '.');
			procent = parseFloat(procent);
			procent = isNaN(procent)? 0 : procent;
			$(this).val(procent);
				
			saleSumm = Math.round(price*(procent/100));
			$('input[name="p.periodic_price_new"]', container).val(saleSumm);
		});
				
		// категория
		$('#extendedCategoryList', container).html('');
		
		// Опубликован товар
		$('.product_public_in_site_switch', container).toggleButtons({
            label: {
                enabled: "Да",
                disabled: "Нет"
            }
        });
		
		// загрузка фото
		shopMain.productUploader = uploader.init({
			container : $("#productImageUploadBar",container),
			hideUploaded : true,
			formCaption : '',
			resize : shopMain.imageProductSize,
			multiple : true,
			done : function(info) {
				if(info.multiple) {
					for(var i in info.data) {
						shopMain.addImageToProduct([
                        	{name:info.data[i].images[0].name, id:0, order:0},
                    		{name:info.data[i].images[1].name, id:0, order:0},
                    		{name:info.data[i].images[2].name, id:0, order:0},
                    		{name:info.data[i].images[3].name, id:0, order:0},
						], true);
					}
				} else {
					shopMain.addImageToProduct([
                    	{name:info.data[0].name, id:0, order:0},
                		{name:info.data[1].name, id:0, order:0},
                		{name:info.data[2].name, id:0, order:0},
                		{name:info.data[3].name, id:0, order:0},
					], true);
				}
			}
		});
		
		
		
		// редактор
		shopMain.editorSettings = {
			filebrowserUploadUrl : '/ru/files/upload/source/static/application/ckeditor/',
			extraPlugins: 'youtube,fileupload',
			toolbar : [
	        	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', 'Templates' ] },
	        	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: ['PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
	        	{ name: 'insert', items: [ 'Image', 'fileupload', 'Youtube', 'Table', 'HorizontalRule' ] },
	        	
	        	
	        	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
	        	{ name: 'links', items: [ 'Link', 'Unlink'] },
	        	
	        	'/',
	        	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
	        	{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
	        	{ name: 'colors', items: [ 'TextColor', 'BGColor'] },

	        ],
			
			extraAllowedContent : 'a(*){*}[*]'
		};
	},
	
	makeCatalogCrumbs : function(list) {
		var out = '<ul><li><a href="#/">Каталог</a></li>';
		for(var i in list) {
			out += '<li><i class="icon-caret-right"></i> <a href="#/show/'+list[i].id+'/type/folder">'+list[i].name+'</a></li>';
		}
		out += '</ul>';
		out += '<br style="clear:both">';
		return out;
	},
	
	getProductFormRawData : function(container) {
		var res = {};
		var push_counters = {};
		var patterns = {
                "validate": /^[a-zA-Z][a-zA-Z0-9_\-\.]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                "key":      /[a-zA-Z0-9_\-\.]+|(?=\[\])/g,
                "push":     /^$/,
                "fixed":    /^\d+$/,
                "named":    /^[a-zA-Z0-9_\-\.]+$/
        };
		
		var push_counter = function(key) {
			 if(push_counters[key] === undefined){
	                push_counters[key] = 0;
	         }
			 
	         return push_counters[key]++;
		};
		
		var build = function(base, key, value){
            base[key] = value;
            return base;
        };
        
        $(container).find('input, textarea, select').each(function(){
			var type = $(this).attr('type'),
				name = $(this).attr('name'),
				value = $(this).val();
		
			if((type == 'radio' || type == 'checkbox')  && !$(this).is(':checked')) {
				return;
			}
					
			// skip invalid keys
            if(!name || !patterns.validate.test(name) || $(this).attr('skip_get_form')){
                return;
            }

			var k,
            	keys = name.match(patterns.key),
            	merge = value,
            	reverse_key = name;
			
			while((k = keys.pop()) !== undefined){
                // adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                // push
                if(k.match(patterns.push)){
                    merge = build([], push_counter(reverse_key), merge);
                }

                // fixed
                else if(k.match(patterns.fixed)){
                    merge = build([], k, merge);
                }

                // named
                else if(k.match(patterns.named)){
                    merge = build({}, k, merge);
                }
                
                res = $.extend(true, res, merge);
            }
			
			return res;
        });
                
        var getUserFeatureBlock = function(form) {
        	var data = form.customFeature;
        	
        	var ret = [];
        	for(var i in data) {
        		var ftmp = {
        			name : data[i].name,
        			type : parseInt(data[i].type),
        			value : data[i]['v'+data[i].type],
        			unit : ''
        		};
        		
        		if(typeof(data[i].fid) != 'undefined') {
        			ftmp.fid = data[i].fid;
        		}
        		
        		if(typeof(data[i].vid) != 'undefined') {
        			ftmp.vid = data[i].vid;
        		}
        		
        		if(data[i].type == 3) {
        			ftmp.unit = data[i].unit;
        		}
        		
        		ret.push(ftmp);
        	}
        	
        	return ret;
        };
        
        var getYMFeatureBlock = function(form) {
        	var ret = [];
        	var data = form['ym-feature'];
        	for(var i in data) {
        		var tmp = {
        				id : i,
        				cat_id : form['p.productCategory'],
        				variant_id : (typeof(data[i].variant) != 'undefined')? data[i].variant : '',
        				value : (typeof(data[i].value) != 'undefined')? data[i].value : ''
        		};
        		
        		ret.push(tmp);
        	}
        	
        	return ret;
        };
        
        
        var result = {
				title : res['p.name'],
				url : res['p.url'],
				group_id : (typeof(res['p.extendedCategory']) == 'object')? res['p.extendedCategory'].shift() : false,
				extended_groups : res['p.extendedCategory'],
				avaliable : {
					type : res['p.avaliable'],
					pending : res['p.avaliable_pending'],
					quantity : res['p.avaliable_qt'],
					store_id : res['p.avaliable_store_id']
				},
				publiched : (typeof(res['p.published']) != 'undefined')? 1 : false,
				feeds : res['p.feeds'],
				description : res['p.description'],
				images : shopMain.productImages,
				full_description : shopMain.textEditor.getData(),
				full_description2 : shopMain.textEditor2.getData(),
				full_description3 : shopMain.textEditor3.getData(),
				retail_price : res['p.retail_price'],
				
				
				cost_price : res['p.cost_price'],				
				sales : (res['p.product_sales'] == 1)? true : false,
				sales_data : {
					procent : res['p.periodic_price_procent'],
					summ : res['p.periodic_price_new'],
					timeaction : (res['p.useTimeAction'] == 1)? true : false,
					start : res['p.timeActionStartDate'],
					stop : res['p.timeActionStopDate'],
				},				
				
				wholesale : (res['p.useWholesale'] == 1)? true : false,
				wholesale_procent : res['p.useWholesale_procent'],
				wholesale_qt : res['p.useWholesale_qt'],
				
				/**/
				price2 : res['p.price2'],
				price3 : res['p.price3'],
				price4 : res['p.price4'],
				price5 : res['p.price5'],
				price_type : res['p.price_type'],
				unit : res['p.product_measure'],
				pakage : res['p.pakage_size'],
				minorder : res['p.min_order_type'],
				/**/	
				
				
				category_id : res['p.productCategory'], 
				vendor_id : res['p.productVendor'], 
				size : res['p.basicFuture'], 
				user_features : getUserFeatureBlock(res),
				ym_features : getYMFeatureBlock(res), 
				meta_description : res['p.seo_description'],
				meta_keywords : res['p.seo_keywords'], 
				remove_helper : shopMain.removeActionsHelper,
				article : res['p.article'],
				currency : (typeof(res['p.currency']) != 'undefined')? res['p.currency'] : false
		};
        
        var mod = [];
        var modContainer = $page.current.find('table[name="modifications"]');
        modContainer.find('tr').each(function(){
        	if($(this).attr('item-id')) {
        		mod.push({
        			id : $(this).attr('item-id'),
        			title : $(this).find('input[name="m.title"]').val(),
        			article : $(this).find('input[name="m.article"]').val(),
        			price1 : $(this).find('input[name="m.price1"]').val(),
        			price2 : $(this).find('input[name="m.price2"]').val(),
        			price3 : $(this).find('input[name="m.price3"]').val(),
        			price4 : $(this).find('input[name="m.price4"]').val(),
        			price5 : $(this).find('input[name="m.price5"]').val()
        		});
        	}
        });
        
        result.modifications = mod;        
		
        return result;
	}
	
};




