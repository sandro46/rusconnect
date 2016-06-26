$(document).ready(function(){

	$('#feedback').click(function(e) {
		e.preventDefault();
		$('#callMeFormBlock').show();
	});

	var rightPanelH = $('.flex #productContainer').outerHeight();

	if ( rightPanelH >= 775 ) {
		$('.flex .leftPanel .scroll-pane').css({'height': rightPanelH - 110}).jScrollPane();
	}
	else {
		$('.scroll-pane').jScrollPane();
	}

	$('.tab').on("click",function(e) {
		e.preventDefault();
		if (!$(this).hasClass('active')) {
			$(this).parent().find('.active').removeClass('active');
			$(this).addClass('active');  
			$(this).parent().next().find('.activeTab').each(function() {
				$(this).removeClass('activeTab');
			});
			$($(this).attr('href')).addClass('activeTab');			
		}
	});

	$('#cart_box_frame .fullcart .cartItems').css({width:'264'}).jScrollPane();


	/* PRICE SELECT !!!!*/
/*	 var updateProductModificationPrice = function() {
		if($('#productBuyForm').length) {
			var container = $('#productBuyForm .modelPrice');
			var priceType = container.find('span.price').attr('price-type');
			//container.find('ul.priceList li').show();
			var price = container.find('li.discount'+priceType).hide().data();
			container.find('span.price span').html(price['priceFloatPerUnit']);
			container.find('span.price i').html(price['priceUnit']);
		}
	}

	updateProductModificationPrice(); */

	if($( document ).height() > ($(window).height()+120)) {
		$(window).bind('scroll', function() {
			if($(window).scrollTop() > 60) {
				if(!$('#topMenuContainer').is('.blocked') && !$('#topMenuContainer').is('.fluid')) {
					$('#topMenuContainer').addClass('fluid');
					$('.mainContentBlock').addClass('fluid');
					$('.cabinetMain').addClass('fluid');
					$('.mainContentBlockFullWidth').addClass('fluid');
				}
			} else {
				if($('#topMenuContainer').is('.fluid')) {
					$('#topMenuContainer').removeClass('fluid');
					$('.mainContentBlock').removeClass('fluid');
					$('.cabinetMain').removeClass('fluid');
					$('.mainContentBlockFullWidth').removeClass('fluid');
				}
			}
		});
	}

	var catlistWidth=0;
	$('.catList>li').each(function(){
		catlistWidth +=$(this).width();

	});

	if (catlistWidth<=$('.mainCatsCategories .catSlider').width()) { 
		$('.mainCatsCategories .controls').hide();
	} else {
		$('.catSlider').jcarousel();   
	}

	$('.addToCart .quantity .up').click(function(){
		var qt = parseInt($(this).parent().find('input').val());
		qt = (isNaN(qt))? 1 : qt+1;
		$(this).parent().find('input').val(qt);

	});

	$('.addToCart .quantity .down').click(function(){
		var qt = parseInt($(this).parent().find('input').val());
		qt = (isNaN(qt) || qt <= 1)? 1 : qt-1;
		$(this).parent().find('input').val(qt);

	});

	$('.centerSlider .jcarousel').jcarousel({
		animation: {
			duration: 800
		},
		auto: 1,
		wrap: 'circular'
	}).jcarouselAutoscroll({
		interval: 3000,
		target: '+=1',
		autostart: true
	})/*.on('jcarousel:animate', function(event, carousel){
	$(carousel._element.context).find('li').hide().fadeIn(1000);
	})*/;



	$('.rightSlider .jcarousel').jcarousel({
		animation: {
			duration: 0
		},
		auto: 1,
		wrap: 'circular'
	}).jcarouselAutoscroll({
		interval: 4000,
		target: '+=1',
		autostart: true
	}).on('jcarousel:animate', function(event, carousel){
		$(carousel._element.context).find('li').hide().fadeIn(1000);
	});
	$('.productGallery .jcarousel').jcarousel({
		'vertical': 1
	});

	$('.mainCatsGoods .prev').click(function(e) {
		e.preventDefault();
		$('.mainCatsGoods .jcarousel').jcarousel().jcarousel('scroll', '-=1');
	});

	$('.mainCatsGoods .next').click(function(e) {
		e.preventDefault();
		$('.mainCatsGoods .jcarousel').jcarousel().jcarousel('scroll', '+=1');
	});	
	$('.mainCatsCategories .prev').click(function(e) {
		e.preventDefault();
		$('.mainCatsCategories .jcarousel').jcarousel('scroll', '-=1');
	});

	$('.mainCatsCategories .next').click(function(e) {
		e.preventDefault();
		$('.mainCatsCategories .jcarousel').jcarousel('scroll', '+=1');
	});	

	$('.productGallery .prev').click(function(e) {
		e.preventDefault();
		$('.productGallery .jcarousel').jcarousel('scroll', '-=1');
	});

	$('.productGallery .next').click(function(e) {
		e.preventDefault();
		$('.productGallery .jcarousel').jcarousel('scroll', '+=1');
	});

	$('.galleryList li a').on("click", function(e) {
		e.preventDefault();
		$('.galleryList li a.active').removeClass('active');
		$(this).addClass('active');

		$('.productLargeImage').css('background-image', 'url('+$(this).attr("href")+')');
		$('.productLargeImage .container a').attr('href', $(this).data("big"));

		$('#prodTitle').text($(this).data('title'));
		$('#prodChars').text($(this).data('chars'));
	});

	// Select-box styling
	$('select').each(function(){
		$(this).siblings('p').text( $(this).children('option:selected').text() );
	});
	$('select').change(function(){
		$(this).siblings('p').text( $(this).children('option:selected').text() );
	});

	//slider pagination
	$('.jcarousel-pagination')
	.on('jcarouselpagination:active', 'a', function() {
		$(this).addClass('active');
	})
	.on('jcarouselpagination:inactive', 'a', function() {
		$(this).removeClass('active');
	})
	.jcarouselPagination();	

	$("a.questIcon").easyTooltip();
	$('.fancybox').fancybox({});   

	$('a[data-lightbox="imageGallery"]').each(function(){
		$(this).fancybox({
			href : $(this).attr('href'),
			title : $(this).attr('title'),
		});  
	})

	$('#cart_box_frame').hover(function(){
		if(!$(this).data('count')) return;
		$(this).find('.fullcart').show();
		$('#cart_box_frame .fullcart .cartItems').css({width:'264'}).data('jsp').reinitialise();
		}, function(){
			$(this).find('.fullcart').hide();
	});
	$('#modificationSelect').bind('change', function(){
		$.ajax({
			url:'/ru/-utils/product/',
			type : 'POST',
			data : {product:$(this).val()},
			dataType: "json"
		}).done(function(result){
			if(typeof(result) != 'object' || typeof(result.status) == 'undefined' || result.status == 'error') {
				alert('Error: product not found');
			} else {
				var container = $('#productBuyForm');
				container.find('span.modelSize').html(result.data.description);
				var mod = container.find('div.modelInfo ul');
				mod.find('[name="article"]').html(result.data.article);
				mod.find('[name="package"]').html(result.data.pack_size+' '+result.data.pack_unit_name);
				mod.find('[name="article"]').html(result.data.article);

/*			  container.find('div.modelPrice ul li').each(function(){
					var type = $(this).data('type');
					var priceKey = (type == 1)? 'price' : 'price'+type;
					var data = result.data;
					$(this).data('price-pack', data[priceKey]);
					$(this).data('price-unit', data.pack_unit_name);
					$(this).data('price-pack-size', data.pack_size);
					$(this).data('price-float-per-unit', parce_digits(data[priceKey+'_sf']));
					$(this).find('span').text(data[priceKey+'_s']);
				}); */
				
				var data = result.data;
				
				var discountMax = $('.discountMax');
				var type = discountMax.data('type');
				var priceKey = (type == 1)? 'price' : 'price'+type;
				
				discountMax.data('price-pack', data[priceKey]);
				discountMax.data('price-unit', data.pack_unit_name);
				discountMax.data('price-pack-size', data.pack_size);
				discountMax.data('price-float-per-unit', parce_digits(data[priceKey+'_sf']));
				discountMax.text(data[priceKey+'_s']);
				
				$('.price1 span').text((data['price_float']).replace('.',','));
				
				//updateProductModificationPrice();
			}
		});
	});

	$('a[name="addToCartButtonMod"]').bind('click', function(){
		var product = $('#modificationSelect').val();
		var qty = $(this).closest('form').find('input[name="amount"]').val();
		var img = $('#productContainer .productLargeImage');

		if(img) {
			img.clone()
			.css({'display':'block', 'position' : 'absolute', 'z-index' : '11100', top: $(this).offset().top-200, left:$(this).offset().left-150})
			.appendTo("body")
			.animate({
				left: $('#cart_box_frame').offset()['left']+20,
				top: $('#cart_box_frame').offset()['top']-50,
				width: 100 
				}, 1000, function() {
					$(this).remove();
			});
		}



		$.ajax({
			url:'/ru/-utils/shopcart/',
			type : 'POST',
			data : {
				op : 'add',
				prod : product,
				qt : qty,
				tpl : ['MainCartbox.html', 'shop']
			},
			dataType: "json"
		}).done(function(data){
			updateShopCart(data);
		});
	});

	$('a[name="addToCartSmallButtonMod"]').bind('click', function(){
		var product = $(this).closest('tr').data('product_id');
		var qty = $(this).closest('tr').find('input[name="amount"]').val();

		var img = $(this).closest('tr').find('td[name="title"]');

		if(img) {
			img.clone()
			.css({'display':'block', 'position' : 'absolute', 'z-index' : '11100', top: $(this).offset().top-200, left:$(this).offset().left-150})
			.appendTo("body")
			.animate({
				left: $('#cart_box_frame').offset()['left']+20,
				top: $('#cart_box_frame').offset()['top']-50,
				width: 100 
				}, 1000, function() {
					$(this).remove();
			});
		}



		$.ajax({
			url:'/ru/-utils/shopcart/',
			type : 'POST',
			data : {
				op : 'add',
				prod : product,
				qt : qty,
				tpl : ['MainCartbox.html', 'shop']
			},
			dataType: "json"
		}).done(function(data){
			updateShopCart(data);
		});
	});

	$('#categoryPopupList li').each(function(){
		$(this).find('a').bind('click', function(){
			if($(this).parent().is('[is-child="1"]')) {
				$(this).closest('ul').find('li.active').removeClass('active');
			} else {
				$('#categoryPopupList li.active').removeClass('active');
			}

			$(this).parent().addClass('active');
			showMainPopupCatalogCategory(0, $(this).data('category'), $(this).closest('.categorymain').find('.categoryPopupProdList'));
		})
	});

	$('#catitem  a.item').bind('click', openCatalogAjax);
	$('.catalogPopupContainer .closeButton').bind('click', openCatalogAjax);
	
	//$('#catitem2 a.item').bind('click', openCatalogItemMain);
	//$('#catitem  a.item').bind('click', openCatalogItemMain);
	//$('#catitem2 a.item').bind('click', openCatalogItemCategory);

	if(readCookie('callback_sended') == 'sended') {
		$('#callMeFormBlock .callNotSended').hide();
		$('#callMeFormBlock .callOkSend').hide();
		$('#callMeFormBlock form').hide();
		$('#callMeFormBlock .callSended').show();
	} else {
		$('#callMeFormBlock .callNotSended').show();
		$('#callMeFormBlock .callOkSend').hide();
		$('#callMeFormBlock form').show();
		$('#callMeFormBlock .callSended').hide();
	}

	$('#callMeFormBlock input[name="phone"]').mask("7 (999) 999-9999");
	$('#addressForm1 input[name="phone"]').mask("7 (999) 999-9999");
	$('#addressForm2 input[name="phone"]').mask("7 (999) 999-9999");

	if($('#addressForm1').length) {
		var mainAddress = $('#addressForm1 input[name="address-id"]').val();
		$('#adressInsertForm1 .addressItem a').click(function(){
			var self = $(this);
			var form = $('#addressForm1');
			var id = $(this).data('id');
			var address = $(this).data('address'); 		
			var insert = function(address) {
				form.find('input[name="address-id"]').val(address.address_id);
				form.find('input[name="street"]').val(address.street);
				form.find('input[name="house"]').val(address.house);
				form.find('input[name="building"]').val(address.building);
				form.find('input[name="flat"]').val(address.flat);
				form.find('input[name="zip"]').val(address.zip);
			};

			if(!address) {
				getUserAddress(id, function(result){
					if(typeof(result) == 'object' || typeof(result.status) != 'undefined') {
						self.data('address', result.data);
						insert(result.data);
					} else {
						alert('Адрес не найден');
					}
				});
			} else {
				insert(address);
			}
		});

		var mainAddress = $('#addressForm2 input[name="address-id"]').val();
		$('#adressInsertForm2 .addressItem a').click(function(){
			var self = $(this);
			var form = $('#addressForm2');
			var id = $(this).data('id');
			var address = $(this).data('address'); 		
			var insert = function(address) {
				form.find('input[name="address-id"]').val(address.address_id);
				form.find('input[name="street"]').val(address.street);
				form.find('input[name="house"]').val(address.house);
				form.find('input[name="building"]').val(address.building);
				form.find('input[name="flat"]').val(address.flat);
				form.find('input[name="zip"]').val(address.zip);
			};

			if(!address) {
				getUserAddress(id, function(result){
					if(typeof(result) == 'object' || typeof(result.status) != 'undefined') {
						self.data('address', result.data);
						insert(result.data);
					} else {
						alert('Адрес не найден');
					}
				});
			} else {
				insert(address);
			}
		});
	}

	$('#serachContainer').hover(function(){

		}, function() {
			$('#serachContainer .searchResult').hide();
	});


	$('#serachContainer .findlink').click(function(){
		var query = $(this).parent().find('input').val();
		document.location.href = '/ru/shop/search/query/' + query + '/';
	});


	$('#serachContainer input').bind('input propertychange', function(){
		var query = $.trim($(this).val());
		var queryBefore = $(this).data('query-before');
		$(this).data('query-before', query);
		if(query == queryBefore) return;

		if($(this).data('query-timeout')) {
			clearTimeout($(this).data('query-timeout'));
		}

		$(this).data('query-timeout', setTimeout(function(){
			$('#serachContainer input').data('query-timeout', false);
			$.ajax({
				url:'/ru/shop/search/',
				type : 'POST',
				headers : {
					From : 'MAJAX:client_shop:findProduct:/',
				},
				data : {page:'index', query:query, limit:5},
				dataType: "json"
			}).done(function(data){
				if(typeof(data) == 'object') {
					if(typeof(data.error) != 'undefined' && data.error) {
						container.hide();
					} else {
						var container = $('#serachContainer .searchResult');
						if(data.found) {
							container.find('.foundItems').html('');
							for(var i in data.data) {
								var tpl = container.find('.resultItemTpl').clone();
								tpl.removeClass('resultItemTpl');
								tpl.find('.title').text(data.data[i].title);
								tpl.find('img').attr('src', data.data[i].img);
								tpl.data(data.data[i]);
								tpl.bind('click', function(){
									document.location.href = $(this).data('url')
								});

								container.find('.foundItems').append(tpl.show());
							}

							container.find('.summary i').html(data.found);
							container.find('.summary').show();
							container.find('.cartActions').show();
							container.find('.deviderVert').show();
							container.find('.cartActions a').attr('href', '/ru/shop/search/query/'+query+'/');


						} else {
							container.find('.foundItems').html('По вашему запросу ничего не найдено');
							container.find('.summary').hide();
							container.find('.cartActions').hide();
							container.find('.deviderVert').hide();
						}

						container.show();
					}
				}
			});
			},200));
	}); 

	if($('#mainCatCarousel').length > 0) {
		$('#mainCatCarouselHeader li').find('a').click(function(){
			if($(this).is('.active')) return;

			var feedId = $(this).attr('item-id');


			$('#mainCatCarouselHeader').find('a.active').removeClass('active');
			$(this).addClass('active');

			var list = $('#feddsAllProducts ul[item-id="'+feedId+'"]').clone();

			if($('#mainCatCarousel').data('carousel')) {
				$('#mainCatCarousel').jcarousel('destroy');
			}

			$('#mainCatCarousel').html(list);
			var carousel = $('#mainCatCarousel').jcarousel();
			$('#mainCatCarousel').data('carousel', carousel);
		});

		$('#mainCatCarouselHeader li').eq(0).find('a').click();
	} 

	$(document).bind('click.closecat', function(event){	
		if(!$(event.target).closest('.categorymain').length) {
			var active = false;
			if($('#catitem').is('.active')) $('#catitem a').click();
			if($('#catitem2').is('.active'))  $('#catitem2 a').click();
		}
	});
	
	
	/* Список категорий в три столбца на главной */
	
	catalog_preview = $('.catalogIndex .preview li');
	catalog_preview_len = catalog_preview.length;

	if(catalog_preview_len) {
		catalog_preview_floor = Math.floor(catalog_preview_len / 3);
		catalog_preview.each(function(index, elem){

			if(index % catalog_preview_floor == 0 && index > 0){
			  $('.catalogIndex .preview').append('<ul class="visible"></ul>');
			  last_ul = $('.catalogIndex .preview ul').last();
			}
			
			if(index >= catalog_preview_floor){
				last_ul.append(elem);
			}
		});
	}
	
	
	$('.account').find('a[action="logout"]').click(function(){
		ajaxLogout(function(){
			document.location.href = '/ru/shop/login/';
		});
	});
	
	
	$('.priceNote2>span').click(function(){
		$('.discountProgram').fadeIn();
	});
	
	$('.discountProgram').click(function(){
		$(this).fadeOut();
	});

});





function openCatalogAjax(ev){
	ev.stopPropagation();

	metrika('nagatie_na_knopku_katalog');

	var container = $('.catalogPopupContainer');
	var ajaxContainer = $('.catalogPopupContainer .ajaxload');

	//ajaxLoad();

	if(container.is(':visible')){
		
		container.hide();
		$('.mainContentBlock').show();
		$('.mainContentBlockFullWidth').show();
		$('.mainContentBlockPages').show();
		$('.featuredGoods').show();
		$('.mainCats').show();
		$('.mainManufacturers').show();
		$('.catalogIndex').show();
		$('.serviceInformation').show();
		$('.toPartners').show();
		$('.subscribe').show();
		$('.footer ').show();
		
	} else {
	
		$.ajax({
			url:'/ru/-utils/catalog/',
			type : 'POST',
			data : {
				act: 'showCatalogAjax'
			},
			dataType: "json"
		}).done(function(response){
			//ajaxLoad();
			ajaxContainer.html(response.data.tpl);
			
			container.show();
			$('.mainContentBlock').hide();
			$('.mainContentBlockFullWidth').hide();
			$('.mainContentBlockPages').hide();
			$('.featuredGoods').hide();
			$('.mainCats').hide();
			$('.mainManufacturers').hide();
			$('.catalogIndex').hide();
			$('.serviceInformation').hide();
			$('.toPartners').hide();
			$('.subscribe').hide();
			$('.footer ').hide();
			
		});
	
	}

}

function openCatalogItemMain(ev) {
	ev.stopPropagation();
	var item = $(this).parent().attr('item');	
	if($(this).parent().is('.active')) {
		$(this).parent().removeClass('active');
		$('#categoryPopupOverlay').remove();
		$('.mainContentBlock').show();
		$('.contentContainer').show();
		$('.mainCats').show();
		$('.toPartners').show();
		$('#topMenuContainer').removeClass('blocked');
		$('.categoryPopupContainer').hide();
	} else {
		if($('#catitem').is('.active') || $('#catitem2').is('.active')) {
			console.log('ok active')
			$('#catitem').removeClass('active');
			$('#catitem2').removeClass('active');
			$('#categoryPopupOverlay').remove();
			$('.categoryPopupContainer').hide();
		}

		$('<div id="categoryPopupOverlay"></div>').appendTo('body')
		$(this).parent().addClass('active');
		showCategoryPopup(item);
		$('.mainContentBlock').hide();
		$('.contentContainer').hide();
		$('.mainCats').hide();
		$('.toPartners').hide();

	}
}

function openCatalogItemCategory(ev) {
	ev.stopPropagation();

	if($(this).parent().is('.active')) {
		$(this).parent().removeClass('active');
		$('#categoryPopupOverlay').remove();
		$('.mainContentBlock').show();
		$('.contentContainer').show();
		$('.mainCats').show();
		$('.toPartners').show();
		$('#topMenuContainer').removeClass('blocked');
		$('.categoryPopupContainer').hide();
	} else {
		$('<div id="categoryPopupOverlay"></div>').appendTo('body')
		$(this).parent().addClass('active');		
		showCategoryPopup(2);
		$('.mainContentBlock').hide();
		$('.contentContainer').hide();
		$('.mainCats').hide();
		$('.toPartners').hide();
	}
}

function showMainPopupCatalogCategory(page, categoryId, container) {
	if(!page) page = 0;
	var box = container.closest('.categorymain');
	var metric = box.data('metric');
	var updateSizes = function(metric, box) {
		box.find('.rightPanel .item').css({
			width: metric.blockWidth,
			height: metric.blockHeight,
		});
	};
	ajaxLoad();
	$.ajax({
		url:'/ru/-utils/category/',
		type : 'POST',
		data : {
			category:categoryId, 
			page: page,
			limit: metric.limit,
			cb: 'showMainPopupCatalogCategory'
		},
		dataType: "json"
	}).done(function(response){
		ajaxLoad();
		container.html(response.data.tpl);
		container.attr('loaded', 1);
		box.on('categoryloaded', true);

		// fix size
		updateSizes(metric, box);
		if(box.find('.loadmore')) {
			box.find('.loadmore').bind('click', function() {
				var loadmore = box.find('.loadmore');
				var page = loadmore.attr('currentpage');
				ajaxLoad();
				$.ajax({
					url:'/ru/-utils/category/',
					type : 'POST',
					data : {
						category:categoryId, 
						page: page,
						limit: metric.limit,
						onlyrows: 1,
						cb: 'showMainPopupCatalogCategory'
					},
					dataType: "json"
				}).done(function(response){
					ajaxLoad();
					loadmore.before(response.data.tpl);
					updateSizes(metric, box);
					var currentPage = parseInt(response.data.page) +1;
					loadmore.attr('currentpage', currentPage)
					box.find('.optionsMenuBottom .pageCurrentItem').text(currentPage);
					if(currentPage >= parseInt(response.data.pages)) {
						loadmore.hide();
					}
				});
			});
		}
	});
}

function showCategoryPopup(item) {
	if(!$('#topMenuContainer').is('.blocked')) {
		$('#topMenuContainer').addClass('blocked');
	}

	var itemContainer = (item == 1)? '.catlist1' : '.catlist2';

	$('.categoryPopupContainer .categorymain').hide();
	$('.categoryPopupContainer').show();
	$('.categoryPopupContainer '+itemContainer).show();

	var metric = {
		width: $('.categoryPopupContainer '+itemContainer+' .rightPanel').width() - 50,
		height: $('.categoryPopupContainer '+itemContainer+' .leftPanel').height() - 125,
		blockPerRow: 5
	};

	metric.blockWidth =  metric.width / metric.blockPerRow - (metric.blockPerRow);
	metric.blockHeight = metric.blockWidth + metric.blockWidth * 0.20; // add 25% to height metric
	metric.rows = Math.floor(metric.height / (metric.blockHeight+2));
	metric.limit = metric.rows * metric.blockPerRow;

	$('.categoryPopupContainer '+itemContainer).data('metric', metric);
	$('.categoryPopupContainer '+itemContainer+' .leftPanel .catListsHolder > ul > li').eq(0).find('a').eq(0).trigger('click'); // load first category
}

function callMe() {
	$('#callMeFormBlock .callError').hide();

	var info = {
		phone : $('#callMeFormBlock input[name="phone"]').val(),
		name : $('#callMeFormBlock input[name="name"]').val()
	};



	info.phone = info.phone.replace(/^[0-9]/g,'');

	if(!info.phone || info.phone.length < 11) {
		$('#callMeFormBlock .callError').html('Укажите Ваш номер телефона').slideToggle();
		return;
	} 

	if(!info.name || info.name.length < 2) {
		$('#callMeFormBlock .callError').html('Укажите как к Вам обращаться').slideToggle();
		return;
	} 

	$.ajax({
		url:'/ru/-utils/phonecallback/',
		type : 'POST',
		data : info,
		dataType: "json"
	}).done(function(data){
		$('#callMeFormBlock form').hide();
		$('#callMeFormBlock .callError').hide();
		$('#callMeFormBlock .callNotSended').hide();
		$('#callMeFormBlock .callOkSend').show();
		createCookie('callback_sended', 'sended', 2*60*60); //2h
	});
}



function ajaxLoad() {
	if($('.ajaxload').is(':visible')) {
		$('#globalPopupOverlay').remove();
		$('.ajaxload').hide();
	} else {
		$('.ajaxload').css("top", Math.max(0, (($(window).height() - $('.ajaxload').outerHeight()) / 2) + $(window).scrollTop()) + "px");
		$('.ajaxload').css("left", Math.max(0, (($(window).width() - $('.ajaxload').outerWidth()) / 2) + $(window).scrollLeft()) + "px");
		$('.ajaxload').show();
		$('<div id="globalPopupOverlay"></div>').appendTo('body');
		$('#globalPopupOverlay').bind('click', function(e){
			e.preventDefault();
			return false;
		});
	}
}


function removeProductFromCart(productId) {
	var cartbox = $('#cart_box_frame');
	var prod = cartbox.find('div[product-id="'+productId+'"]');
	if(prod) {
		prod.remove();
		$.ajax({
			url:'/ru/-utils/shopcart/',
			type : 'POST',
			data : {
				op:'rm', 
				prod: productId,
				tpl : ['MainCartbox.html', 'shop']
			},
			dataType: "json"
		}).done(function(data){
			updateShopCart(data);
		});
	}
}

function changeCartQt(prodId, qt, callback) {
	var info = {
		op : 'change',
		prod : prodId,
		qt : qt
	};

	$.ajax({
		url:'/ru/-utils/shopcart/',
		type : 'POST',
		data : info,
		dataType: "json"
	}).done(function(data){
		updateShopCart(data, callback);
	});
}

function downloadPDF(category) {

}

function downloadXLS(category) {
	
	metrika('downloader_price');
	
	$.ajax({
		url:'/ru/-utils/price/',
		type : 'POST',
		data : {
			category: category,
			type:'csv'
		},
		dataType: "json"
	}).done(function(response){
		downloadFile(response.data);
	});
}

function downloadFile(link) {
	$('body').append($('<iframe src="'+link+'"></iframe>').css({width:1, height:1, position:'absolute',left:-90000}));
};

function updateShopCart(data, callback) {
	if(typeof(data) == 'object') {
		var cartbox = $('#cart_box_frame');
		if(!data.error && data.result) {
			cartbox.data('summ', data.result.summ_float);
			cartbox.data('count', data.result.count);

			cartbox.find('[name="cartsummaryCount"]').html(data.result.count);
			cartbox.find('[name="cartsummarySumm"]').html( data.result.sum);
			cartbox.find('[name="cartsummarySumm"]').parent().animate({color: '#fe9508'}, 300, 'linear').delay( 250 ).animate({color: cartbox.find('[name="cartsummarySumm"]').css('color')}, 300, 'linear')
			$('.mainContentBlockFullWidth span[name="priceCartSummary"]').html(data.result.sum);

			if(data.result.count > 0) {
				cartbox.find('[name="cartSummaryStat"]').show();
				cartbox.find('[name="cartSummaryCleared"]').hide();
			} else {
				cartbox.find('[name="cartSummaryStat"]').hide();
				cartbox.find('[name="cartSummaryCleared"]').show();
			}
			var api = cartbox.find('div[name="cartItems"]').data('jsp');
			api.reinitialise();
			cartbox.find('div[name="cartItems"]').html(data.result.tpl);

			if(typeof(callback) == 'function') {
				callback(data.result);
			}
		}	
	} else {
		$.ajax({
			url:'/ru/-utils/shopcart/',
			type : 'POST',
			data : {tpl : ['MainCartbox.html', 'shop']},
			dataType: "json"
		}).done(function(data){
			updateShopCart(data);
		});
	}
} 

function ajaxLogout(callback) {
	$.ajax({
		url:'/ru/-utils/shopuserlogin/',
		type : 'POST',
		data : {
			from:'ajax',
			op: 'logout'
		},
		dataType: "json"
	}).done(function(result){
		if(typeof(callback) == 'function') {
			callback();
		}
	});
}

function ajaxLoginCheck(email, pass, button, errorCallback, okCallback) {
	if(button.is('.load')) return false;
	var password = md5(pass);
	button.addClass('load');

	$.ajax({
		url:'/ru/-utils/shopuserlogin/',
		type : 'POST',
		data : {
			email: email,
			password: password
		},
		dataType: "json"
	}).done(function(result){
		button.removeClass('load');

		if(!result || typeof(result) != 'object') {
			errorCallback(99, 'Произошла ошибка на сервере. Попробуйте позднее.');
			return false;
		} 

		if(typeof(result.error) != 'undefined') {
			if(typeof(errorCallback) == 'function') errorCallback(result.error, result.message);
			return false;
		}

		if(!result.data) {
			errorCallback(6, 'Пользователь с таким логином и паролем не найден.');
			return false;
		}

		if(typeof(okCallback) == 'function') {
			okCallback(result);
		}

		return true;
	});
}

function ajaxPassReset(email, button, errorCallback, okCallback) {
	if(button.is('.load')) return false;
	button.addClass('load');
	

	 $.ajax({
		url:'/ru/-utils/shopuserresetpass/',
		type : 'POST',
		data : {
			email: email
		},
		dataType: "json"
	}).done(function(result){
		button.removeClass('load');

		if(!result || typeof(result) != 'object') {
			errorCallback(99, 'Произошла ошибка на сервере. Попробуйте позднее.');
			return false;
		} 

		if(typeof(result.error) != 'undefined') {
			if(typeof(errorCallback) == 'function') errorCallback(result.error, result.message);
			return false;
		}

		if(typeof(okCallback) == 'function') {
			okCallback(result.data);
		}

		return true;
	});

}

function registerAndLogin(data, button, errorCallback, okCallback) {
	if(button.is('.load')) return false;
	button.addClass('load');

	$.ajax({
		url:'/ru/-utils/shopuserregister/',
		type : 'POST',
		data : data,
		dataType: "json"
	}).done(function(result){
		button.removeClass('load');

		if(!result || typeof(result) != 'object') {
			errorCallback(99, 'Произошла ошибка на сервере. Попробуйте позднее.');
			return false;
		}

		if(typeof(result.error) != 'undefined') {
			if(typeof(errorCallback) == 'function') errorCallback(result.error, result.message);
			return false;
		}

		if(typeof(okCallback) == 'function') okCallback(result);

		return true;
	});
}

function saveUserAddress(data, button, errorCallback, okCallback) {
	if(button.is('.load')) return false;
	button.addClass('load');

	$.ajax({
		url:'/ru/-utils/shopuserregister/op/address/',
		type : 'POST',
		data : data,
		dataType: "json"
	}).done(function(result){
		button.removeClass('load');

		if(!result || typeof(result) != 'object') {
			errorCallback(99, 'Произошла ошибка на сервере. Попробуйте позднее.');
			return false;
		}

		if(typeof(result.error) != 'undefined') {
			if(typeof(errorCallback) == 'function') errorCallback(result.error, result.message);
			return false;
		}

		if(typeof(okCallback) == 'function') okCallback(result);

		return true;
	});
}

function getUserAddress(addressId, callback) {
	$.ajax({
		url:'/ru/-utils/shopuserregister/op/getaddress/',
		type : 'POST',
		data : {id: addressId},
		dataType: "json"
	}).done(function(result){		
		if(!result || typeof(result) != 'object' || typeof(result.error) != 'undefined') {
			callback(false);
		} else {
			callback(result);
		}
	});	
}

function newsleter(obj) {
	var container = $(obj).closest('form');
	var data = {
		email: container.find('input[name="email"]').val(),
		name: container.find('input[name="email"]').val(),
		list: 5908438,
		token: $csrf
	};

	if(!data.email) {
		container.find('input[name="email"]').addClass('error');
		return;
	} else {
		container.find('input[name="email"]').removeClass('error');
	}

	if(readCookie('newslater')) {
		container.after($('<div style="padding-top: 11px; font-weight: bold;">Вы уже подписаны на рассылку.</div>'));
		container.remove();
		return;
	} 

	$.post('/ru/-utils/unisender/', data, function(result) {
		result = JSON.parse(result);
		if(result.success) {
			createCookie('newslater', 1, 999999);
			container.after($('<div style="padding-top: 11px; font-weight: bold;">Спасибо что подписались.</div>'));
			container.remove();
		} else {
			alert(result.message);
		}
	});
}


function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') {
			c = c.substring(1,c.length);
		}
		if (c.indexOf(nameEQ) == 0) {
			return c.substring(nameEQ.length,c.length);
		}
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}

function createCookie(name,value,secs) {
	if (secs) {
		var date = new Date();
		date.setTime(date.getTime()+(secs*1000));
		var expires = "; expires="+date.toGMTString();
	} else {
		var expires = "";
	}
	document.cookie = name+"="+value+expires+"; path=/";
}

function parce_digits(int) {
	var zerro = false, dig = '', int = int+'', len, i;
	var trim = function trim(str) {	
		charlist = ' \s\xA0';
		var re = new RegExp('^[' + charlist + ']+|[' + charlist + ']+$', 'g');
		return str.replace(re, '');
	}

	if(int.indexOf('.') !== -1) {
		ret = int.split(".");
		if(ret.length == 2) {
			zerro = trim(ret[1]);
			int = trim(ret[0]);
		}
	}

	len = int.length;
	for(i = 0; i < (len + 3); i += 3) {
		dig = int.substr((len - i), 3) + " " + dig;
		if((len - i) < 3 && (len- i != 0) && (len != i)) {
			dig= int.substr(0, len - i) + " " + dig;
			break;
		}
	}

	if(zerro !== false) dig = trim(dig) + "," + zerro;

	return trim(dig);
}

function metrika(target){
	
	try{
		yaCounter26141199.reachGoal(target);
		console.info('Метрика - ' + target);
	} catch(e){
		console.error('Ошибка метрики - ' + target);
	}

	try{
		ga('send', 'event', 'rusconnect', target);
		
		console.info('Аналитика - ' + target);
	} catch(e){
		console.error('Ошибка аналитики - ' + target);
	}
}