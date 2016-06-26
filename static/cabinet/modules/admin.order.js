var admin_order = {
	productTreeHelper : null,
	productAddLock : false,

	appendFilter : function() {
		var status = $('select[name="filter_status"]', $page.current).val();
		var delivery = $('select[name="delivery_type"]', $page.current).val();
		
		if(!status) {
			grid.orders_list.removeFilter('status');
		} else {
			grid.orders_list.addFilter('status', status);
		}
		
		if(!delivery) {
			grid.orders_list.removeFilter('delivery');
		} else {
			grid.orders_list.addFilter('delivery', delivery);
		}
		
		grid.orders_list.start();
		
		$('#grid-orders_list-filters').slideToggle();
	},
	
	clearFilter : function() {
		grid.orders_list.clearFilter();
		grid.orders_list.start();
		
		$('select[name="delivery_type"]', $page.current).parent().find('.search-choice-close').trigger('mousedown').trigger('mouseup');
		$('select[name="filter_status"]', $page.current).parent().find('.search-choice-close').trigger('mousedown').trigger('mouseup');
		
		$('#grid-orders_list-filters').slideToggle();
	},
	
	newOrder : function() {
		var preloadTplData = [
		           {order_statuses:['admin_shop','getOrderAllStatuses']}, 
		           {delivery_type:['admin_shop','getDeliveryTypes']}
	    ];
		
		$page.show(['order.new.html', 'shop', preloadTplData], false, function(current){
			
		});
	},
	
	showList : function(route) {
		var preloadTplData = [{order_statuses:['admin_shop','getOrderAllStatuses']}, {delivery_type:['admin_shop','getDeliveryTypes']}];
		$page.show(['orders.html', 'shop', preloadTplData], false, function(current){
			$('#grid-orders_list-filters').hide();
			
			grid.orders_list.start(function(){
				grid.orders_list.settings.click = {};
				grid.orders_list.settings.click.cb = function(row) {
					$page.router.setRoute('/show/'+row.order_id+'/');
				};
			});
		});
	},
	
	showClientInfo : function(id) {
		$page.lock();
		admin_shop.getContactInfo(id, function(result){
			var userInfo = result;
			$page.show(['contact.show.html', 'shop'], false, function(current){
				$page.unlock();
				
				$('h4.userName', current).html(userInfo.name+'<br/><small>'+userInfo.source_name+'</small>');
				$('td.birthday', current).html('12.03.1985');
				$('td.email', current).html(userInfo.email);
				$('td.phone', current).html(userInfo.phone);
				
				if(userInfo.address.length) {
					$('td.address', current).html(userInfo.address.zip+', '+userInfo.address.address1+', ' +userInfo.address.address2);
				} else {
					$('td.address', current).html('');
				}
				
				
			});
			
		});
	},
	
	showProductInfo : function(orderId, productId) {
		var preloadTplData = [{productInfo:['admin_shop','getProductEditInfo',[productId]]}];
		
		$page.show(['product_show.html', 'shop',preloadTplData], false, function(current){
			$page.bind('removeProduct', function(){
				$page.confirm('Удаление продукта', 'Вы действительно исключить этот товар из заказа?',function(){
					$page.lock();
					admin_shop.cancelProduct(orderId, productId, function(){
						$page.unlock();
						$page.sticky('Отмена покупки товара','Заказ №'+orderId+' был изменен!');
					});
				});
			});
		});
	},
	
	
	
	
	editOrder : function(orderId) {
		//console.log(orderId);
		var preloadTplData = [
		      {orderInfo:['admin_shop','getOrderInfo',[orderId]]},
		      {statuses:['admin_shop','getOrderAllStatuses']},
		      {contactSources:['admin_shop','getContactSources']},
		];
		
		$page.show(['order.edit.html', 'shop',preloadTplData], false, function(current){
			admin_order.makeProdListUI(current);
			
			$page.bind('saveOrder',function(){
				var info = {
						'order_id':orderId,
						'status_id':$('select[name="o.status_id"]', current).val(),
						'contact':{
							'fio':$('input[name="c.fio"]', current).val(),
							'phone':$('input[name="c.phone"]', current).val(),
							'email':$('input[name="c.email"]', current).val(),
							'source_id':$('select[name="c.source_id"]', current).val()
						},
						'address':{
							'zip':$('input[name="a.zip"]', current).val(),
							'address1':$('input[name="a.address1"]', current).val(),
							'address2':$('input[name="a.address2"]', current).val(),
						}
				};
				
				$page.lock();
				admin_shop.updateOrder(orderId, info, function(){
					$page.back();
					$page.sticky('Заказ изменен','Были внесены изменения в заказ №'+orderId);
				});
			});
			
			$page.bind('back',function(){
				$page.back();
			});
			
			$page.bind('addProduct',function(){
				if(admin_order.productAddLock) {
					$page.sticky('Уведомление','Для добавления нового продукта, завершите редактирование предыдущего.');
					return false;
				}
				
				admin_order.productAddLock = true;
				
				var table = $('table[name="prodList"]', current);
				var countProd = parseInt(table.find('tr').length);
				
				var newRow = $('<tr>'+
								 '<td>'+(countProd+1)+'</td>'+
								 '<td><input type="text" readonly="readonly" placeholder="Выберите товар" id="catalogZtreeInput"><input type="hidden" id="catalogZtreeId" value=""></td>'+
								 '<td class="price" priceClear="0">0 руб.</td>'+
								 '<td class="sales">0 %</td>'+
								 '<td class="count"><input type="text" name="prod_count" style="width:20px;" value="1"></td>'+
								 '<td class="summ">0 руб.</td>'+
								 '<td class="action">'+
								 	'<a name="removeNewProd" style="display:none; font-size:18px;" href="javascript:void(0)"  new_id="" onclick="admin_order.removeProdFromOrder(this, '+orderId+', $(this).attr(\'new_id\'))" class="" style="font-size:18px"><i class="icon-trash"></i></a>'+
								 	'<a href="javascript:void(0)" onclick="admin_order.saveNewProdToOrder(this, '+orderId+')" style="font-size:18px"><i class="icon-ok"></i></a>'+
								 '</td>'+
								'</tr>');
				table.append(newRow);
				
				admin_order.makeProdListUI(newRow);
				
				admin_shop.getProductTreeView(0, function(nodes){
					var setting = {
						edit: {
							enable: false,
							showRemoveBtn: false,
							showRenameBtn: false
						},
						data: {
							simpleData: {
								enable: true
							}
						},
						callback: {
							onClick : function(event, treeId, treeNode, clickFlag) {
								var zTree = admin_order.productTreeHelper;
								var node = zTree.getSelectedNodes()[0];
								
								if(node.is_group == '1') return;
																
								$("body").trigger("mousedown.selectPage", 'close');
								var row = $('#catalogZtreeId', $page.curent).closest('tr');
								
								$('#catalogZtreeId', $page.curent).val(node.id);
								$('#catalogZtreeInput', $page.curent).val(node.name);
								row.find('td.price').html(node.price+' руб.').attr('priceClear', node.price);
								row.find('input[name=prod_count]').trigger('change');
							}
						},
						async : {
							func : admin_shop.getProductTreeView,
							enable : true
						},
						
						view: {
							selectedMulti: false
						}
					};
					
					admin_order.productTreeHelper = $.fn.zTree.init($("#menuCatalogTree"), setting, nodes);
					$('#catalogZtreeInput', $page.curent).unbind('click').bind('click',function(){
						$("#menuCatalogTree", $page.curent).show().css({width:$(this).width()+2})
						var offset = $(this).offset();
						$("#menuCatalog",  $page.curent).css({left:offset.left + "px", top:offset.top + $(this).outerHeight() + "px"}).slideDown("fast");
						$("#menuCatalogTree", $page.curent).show().css({width:'auto'})
						$("body").bind("mousedown.selectPage", function(event){
							if (!(event.target.id == "menuPages" || $(event.target).parents("#menuCatalog").length>0) || event === "close") {
								$("#menuCatalog").fadeOut("fast");
								$("body").unbind("mousedown.selectPage");
							}
						});
					});
					
					
					
					//admin_order.productTreeHelper = $.fn.zTree.init($('#menuCatalogTree',$page.current), setting, nodes);
				});
			});
			
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
		});
	},
		
	makeProdListUI : function(em) {
		em.find('input[name=prod_count]').each(function(){
			$(this).unbind('change keyup').bind('change keyup',function(){
				var row = $(this).closest('tr');
				var count = parseInt($(this).val());
				var price = parseFloat(row.find('.price').attr('priceClear'));
				row.find('.summ').html((price*count)+' руб.');
			});
		});
	},
	
	
	
	saveNewProdToOrder : function(em, orderId) {
		var row = $(em).closest('tr');
		var prodId = $('#catalogZtreeId',row).val();
		console.log(prodId);
		if(!prodId) {
			row.remove();
		} else {
			row.find('a[name=removeNewProd]').show();
			$(em).remove();
			
			var name = row.find('#catalogZtreeInput').val();
			var prodId = row.find('#catalogZtreeId').val(); 
			
			row.find('#catalogZtreeInput').parent().html(name);
			row.find('a[name=removeNewProd]').attr('new_id', prodId);
			var count = row.find('input[name=prod_count]').val();
			
			$page.lock();
			admin_shop.addProductToOrder(orderId, prodId, count, function(){
				$page.unlock();
				$page.sticky('Заказ изменен','Добавлен новый товар в заказ №'+orderId);
			});
		}
		
		admin_order.productAddLock = false;
		admin_order.productTreeHelper.destroy();
	},
	
	removeOrder : function(id) {
		$page.confirm('Удаление заказа','Вы действительно хотите удалить этот заказ?', function(){
			$page.lock();
			admin_shop.removeOrder(id, function(){
				$page.unlock();
				$page.sticky('Изменение заказа','Заказ был удален!');
				grid.orders_list.start();
			});
		});
	},
	
	removeProdFromOrder : function(em, orderId, productId) {
		$page.confirm('Удаление продукта', 'Вы действительно исключить этот товар из заказа?',function(){
			$page.lock();
			admin_shop.cancelProduct(orderId, productId, function(){
				$page.unlock();
				$page.sticky('Отмена покупки товара','Заказ №'+orderId+' был изменен!');
				$(em).closest('tr').remove();
			});
		});		
	},
	
	showOrder : function(id) {
		$page.lock();
		admin_shop.getOrderInfo(id, function(result){
			var orderInfo = result;
			var preloadTplData = [
    		      {orderHistory:['admin_shop','getOrderHistory',[id]]}
    		];
			        					
			$page.show(['order.show.html', 'shop', preloadTplData], {orderId:id}, function(current){
				$page.unlock();
				
				console.log(orderInfo);
				var client = "<li>ФИО: "+orderInfo.contact.surname+' '+orderInfo.contact.name+' '+orderInfo.contact.lastname+ "</li>" +
							 "<li>Телефон: "+orderInfo.contact.phone+"</li>" +
							 "<li>Почта: "+orderInfo.contact.email+"</li>" +
							 "<li>Коментарий: "+orderInfo.comment+"</li>";
				
				
				//$('.invoiceOrderName',current).html(orderInfo.order_id);
				$('.deliveryAddress',current).html(orderInfo.address+'<br>'+orderInfo.delivery_type_name);
				$('.payVariant',current).html(orderInfo.pay_type+'<br><span class="label label-'+orderInfo.pay_status_label+'">'+orderInfo.pay_status_type+'</span>');
				$('.deliveryOrderClient',current).html(client);
				
				/*
				$('a.orderUndoLink',current).unbind('click').bind('click', function(){
					$page.confirm('Отмена заказа', 'Вы действительно хотите отменить заказ?',function(){
						$page.lock();
						admin_shop.cancelOrder(orderInfo.order_id, function(){
							$page.back();
							$page.sticky('Отмена заказа','Заказ №'+orderInfo.order_id+' был отменен!');
						});
					});
				});
				*/			
				//orderItemList
				var tpl = '';
				for(var i in orderInfo.items) {
					tpl = '<tr>'+
							  	'<td>'+(i+1)+'</td>'+
							  	'<td><a href="#/show/'+id+'/product/'+orderInfo.items[i].product_id+'/">'+orderInfo.items[i].product_name+'</a></td>'+
							  	'<td>'+orderInfo.items[i].product_cost+'</td>'+
							  	'<td>'+orderInfo.items[i].count+'</td>'+
							  	'<td>'+orderInfo.items[i].row_total+' руб.</td>'+
							  '</tr>';
					$('.orderItemList',current).append(tpl);
				}
				
				//orderAmounts
				tpl = '<li><strong>Подитог :</strong> '+orderInfo.sum+' руб.</li>'+
                      '<li><strong>Скидка :</strong> 0%</li>'+
                      '<li><strong>Итого :</strong> '+orderInfo.sum+' руб.</li>';
				
				$('.orderAmounts',current).append(tpl);

			});
			
		});
	}
	
};


$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Список заказов',
			always_reload: true,
			on : admin_order.showList,
			
			
			'/show/:id' : {
				name: 'Просмотр заказа',
				on : admin_order.showOrder,
				always_reload: true,
				delete_unload: true,
				
				'/product/:id/' : {
					name: 'Информация о товаре',
					on : admin_order.showProductInfo,
				},
				'/add_product/' : {
					name: 'Добавить товар в заказ',
					on : admin_order.addProduct,
				},
				'/edit/' : {
					always_reload: true,
					delete_unload: true,
					name: 'Редактирование заказа',
					on : admin_order.editOrder,
				},
			},
			
			'/show_client/:id' : {
				name: 'Просмотр заказа',
				on : admin_order.showClientInfo,
			},
			'/add/' : {
				name: 'Создать заказ',
				on : admin_order.newOrder,
			}
		}
	};
	
	$page.init(routes);
});

//grid.templates_list.groupAction.on('copy', function(boofer){

