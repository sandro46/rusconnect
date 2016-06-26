editorSettings = {filebrowserUploadUrl : '/ru/files/upload/source/static/application/ckeditor/'};

var fastTitleEditId = false;
var fastTitleEditType = false;
var fastTitleEditOldText = false;
var fastEditLock = false;
var prodParamsArray = new Object();
var curProdData = new Object();
var current_group_id = 0;
var current_group_name = "Корневая группа";
var  checkedItems = new Object();
var isnew = true;
    
var g_url = false;
var curCropImg = false

var img2delete = new Array();
var params2delete = new Array();
var feature2delete = new Array();



function paramdel(obj){
    obj = $(obj).parent();
    paramId = $(obj).attr('productparam');
    if(typeof(paramId) != 'undefined')
    	params2delete.push(paramId);
    $(obj).remove();
}

function featuredel(obj){
    obj = $(obj).parent();
    featureId = $(obj).attr('productfeature');
    if(typeof(featureId) != 'undefined')
    	feature2delete.push(featureId);
    $(obj).remove();
}

function addnewprod2param(data){
    if(!data){
    	data = new Array();
    	data.push({parameter_id : $('#sel-param-name').val(), parameter_id : $('#sel-param-name').val(), variant_id : $('#sel-param-value').val(), id:''});
    }else{
    	$('#edit-item-paramlist').html('');
    }
    
    for(i in data){
    	nameId = data[i].parameter_id;
    	valId = data[i].variant_id;
    	productparam = data[i].id;
    	
//    	
//	    tmpl = '<div class="paramlist-item" productparam="'+productparam+'" paramid="'+nameId+'" valid="'+valId+'">'+
//	          		'<div class="param-name">'+paramsArray[nameId].name+'</div>'+
//	          		'<div class="param-value">'+paramsArray[nameId].variants[valId].name+'</div>'+
//	          		'<div class="param-del" onclick="paramdel(this)">x</div>'+
//	          	'</div>';
//	    
	    tmpl = 	'<tr class="paramlist-item" productparam="'+productparam+'" paramid="'+nameId+'" valid="'+valId+'">'+
		'<td class="param-name">'+paramsArray[nameId].name+'</td>'+
		'<td class="param-value">'+paramsArray[nameId].variants[valId].name+'</td>'+
		'<td class="param-del" onclick="paramdel(this)">Удалить</td></tr>';

	    
	    $('#edit-item-paramlist').append(tmpl);
	    $('#emptyparamlist').remove();
    }
    if($('#edit-item-paramlist').html() == '') 
    	$('#edit-item-paramlist').append('<tr id="emptyparamlist"><td colspan="3" style="text-align:center;">Нет параметров</td></tr>');
  }

function addnewprod2feature(data){
    if(!data){
    	data = new Array();
    	data.push({feature_id : $('#sel-feature-name').val(), feature_id : $('#sel-feature-name').val(), variant_id : $('#sel-feature-value').val(), id:''});
    }else{
    	$('#edit-item-featurelist').html('');
    }
    
    for(i in data){
    	nameId = data[i].feature_id;
    	valId = data[i].variant_id;
    	productfeature = data[i].id;
//	    tmpl = '<div class="featurelist-item" productfeature="'+productfeature+'" featureid="'+nameId+'" valid="'+valId+'">'+
//	          '<div class="feature-name">'+featureArray[nameId].name+'</div>'+
//	          '<div class="feature-value">'+featureArray[nameId].variants[valId].name+'</div>'+
//	          '<div class="feature-del" onclick="featuredel(this)">x</div>'+
//	        '</div>';
	    
	    tmpl = 	'<tr class="featurelist-item" productfeature="'+productfeature+'" featureid="'+nameId+'" valid="'+valId+'">'+
		'<td class="feature-name">'+featureArray[nameId].name+'</td>'+
		'<td class="feature-value">'+featureArray[nameId].variants[valId].name+'</td>'+
		'<td class="feature-del" onclick="featuredel(this)">Удалить</td></tr>';
	    
	    $('#edit-item-featurelist').append(tmpl);
	    $('#emptyfeature').remove();
    }
    
    if($('#edit-item-featurelist').html() == '') 
    	$('#edit-item-featurelist').append('<tr id="emptyfeature"><td colspan="3" style="text-align:center;">Нет характеристик</td></tr>');
  }
  
//function newParamForm(){
//	$('#newParamForm-input').val('');
//	$('#newParamForm').dialog('destroy');
//	$('#newParamForm').dialog({title:'Название нового параметра',autoOpen: true, modal: true, width:820, resizable:false, draggable:false});
//}

function saveNewParam(){
	name = $('#newParamForm-input').val();
	ajaxLoad();
	client_shop.add_user_param({name:name}, function(data){
		$('#sel-param-name').append('<option selected="selected" value="'+data.param_id+'">'+data.name+'</option>');
		paramsArray[data.param_id] = data;
		$("#sel-param-name").trigger('change');
		ajaxLoad();
	});
}

function saveNewfeature(){
	name = $('#newfeatureForm-input').val();
	ajaxLoad();
	client_shop.add_user_feature({name:name}, function(data){
		$('#sel-feature-name').append('<option  selected="selected" value="'+data.feature_id+'">'+data.name+'</option>');
		featureArray[data.feature_id] = data;
		$("#sel-feature-name").trigger('change');
		ajaxLoad();
	});
}






function saveNewParamVariant(){
	name = $('#newParamVal-input').val();
	parameter_id = $('#sel-param-name').val();
	ajaxLoad();
	client_shop.add_user_param_variant({name:name}, parameter_id, function(data){
		$('#sel-param-value').append('<option  selected="selected" value="'+data.variant_id+'">'+data.name+'</option>');
		
		paramsArray[$('#sel-param-name').val()].variants = new Array();
		paramsArray[$('#sel-param-name').val()].variants[data.variant_id] = data;
		$("#sel-param-value").trigger('change');
		ajaxLoad();
	});
}

function saveNewfeatureVariant(){
	name = $('#newfeatureVal-input').val();
	feature_id = $('#sel-feature-name').val();
	ajaxLoad();
	client_shop.add_user_feature_variant({name:name}, feature_id, function(data){
		$('#sel-feature-value').append('<option selected="selected" value="'+data.variant_id+'">'+data.name+'</option>');
		featureArray[$('#sel-feature-name').val()].variants = new Array();
		featureArray[$('#sel-feature-name').val()].variants[data.variant_id] = data;
		$("#sel-feature-value").trigger('change');
		ajaxLoad();
	});
}











    
function productAutoUrl(){
	text = $('#edit-item-title').val();
	$('#edit-item-seoUrl').val(text.toLowerCase().translit()+'.html');
	
}

function enableProductAutoRewrite(status){
	if(status){
		productAutoUrl();
		$('#edit-item-title').bind('keyup change', function(){
			productAutoUrl();
		});
		$('#edit-item-seoUrl').bind('keyup', function(){
			enableProductAutoRewrite(0);
		});
	}else{
		$('#edit-item-title').unbind('keyup change');
		$('#edit-item-seoUrl').unbind('keyup');
	}
}

    function deleteThisOrder(){
    	if(!confirm('Удалить заказ?'))
    		return;
    	ajaxLoad();
    	client_shop.delOrder(order_data,function(data){
    		ajaxLoad();
    		$('#order-form').dialog('destroy');
    		grid.orders.start();
    	});
    }
     
    function updateManufactList(data){
    	$('#edit-item-manufacturer_id').html('');
    	$('#edit-item-manufacturer_id').append('<option value="0">Название производителя</option>');
    	for(i in data){
    		$('#edit-item-manufacturer_id').append('<option value="'+data[i]['man_id']+'">'+data[i]['name']+'</option>');
    	}
    }
       
    function newManufacturer(){
    	$('#newManufactName').val('');
    	$('#createNewManufactForm').dialog('destroy');
    	$('#createNewManufactForm').dialog({title:'Добавление производителя',autoOpen: true, modal: true, width:820, resizable:false, draggable:false});
    }
    
    function saveNewManufactForm(){
    	name = $('#newManufactName').val();

    	client_shop.new_manufacturer({name:name},function(data){
    		
    		curProdData['manufacturer'] = data;
    		updateManufactList(data);
    		$('#createNewManufactForm').dialog('destroy');
    	})
    }
    
    function showProdGroup(type){
    	grid.orders.addFilter('show_blocks', type).start(function(){activate_prod_grid();get_group_tree();});
    }
       
    function showStore(id){
    	grid.orders.addFilter('show_store', id).start(function(){activate_prod_grid();get_group_tree();});
    }
    
    function deleteItem(type, id){
      if(confirm('Удалить?'))
    	client_shop.delete_item(type, id, function(){
    		grid.orders.start(function(){
            	activate_prod_grid();
            	get_group_tree();
            });
    		
            ajaxLoad();
    	})
    }
    
    //добавление нового значения для текущего параметра
   
  
    function saveNewParamForm(){
    	name = $('#newParamName').val();
    	ajaxLoad();
    	client_shop.add_new_param({name:name},function(data){
    		curProdData['params_all'] = data;
    		addParamToProduct();
    		$('#createNewParamForm').dialog('destroy');
    		ajaxLoad();
    	})
    }
   
    function showCreateNewParamForm(){
    	$('#newParamName').val('');
    	$('#createNewParamForm').dialog('destroy');
    	$('#createNewParamForm').dialog({title:'Новый параметр',autoOpen: true, modal: true, width:820, resizable:false, draggable:false});
    }
   
    function saveProductForm(){
    	//curProdData.id = fastTitleEditId;
    	if(isnew) {
    		curProdData.id = false;
    	}
    		
    	curProdData.title = $('#edit-item-title').val();
    	curProdData.price = $('#edit-item-price').val();
    	curProdData.sale  = $('#edit-item-sale').val();
    	curProdData.public_site = $('#edit-item-public_site').is(':checked');
    	curProdData.recommend = $('#edit-item-recommend').is(':checked');
    	curProdData.product_day = $('#edit-item-product_day').is(':checked');
    	curProdData.slider = $('#edit-item-slider').is(':checked');
    	curProdData.neww = $('#edit-item-new').is(':checked');
    	curProdData.description = $('#edit-item-description').val();
    	curProdData.full_description = textEditor.getData();
    	curProdData.article = $('#edit-item-article').val();
    	curProdData.seo_url = $('#edit-item-seoUrl').val();
    	curProdData.seo_title = $('#edit-item-seoTitle').val();
    	curProdData.seo_keywords = $('#edit-item-seoKeywords').val();
    	curProdData.seo_description = $('#edit-item-seoDescription').val();
    	curProdData.group_id = $('#edit-item-group_id').val();
    	curProdData.manufacturer_id = $('#edit-item-manufacturer_id').val();
    	curProdData.store_id = $('#edit-item-store_id').val();
    	curProdData.quantity = $('#edit-item-store-count').val();
    	curProdData.model_name = $('#edit-item-model_name').val();
    	curProdData.product_day_date = $('#product_day_date').val();
    	curProdData.img = new Array();
    	curProdData.img2delete = img2delete;
    	$('.product-img-item img').each(function(){
    		
    		ismainpic = (typeof($(this).parent().find('input[name=main_img]:checked').val()) != "undefined") ? 1 : 0;
    		
    		curProdData.img.push({url: $(this).attr('src'), 	url_big: $(this).attr('url_original'),	url_preview: $(this).attr('url_preview'),
    			sx:$(this).attr('sx'), 	sy:$(this).attr('sy'), 	sw:$(this).attr('sw'), 	sh:$(this).attr('sh'), 	img_id:$(this).attr('img_id'),
    			is_main:ismainpic
    		});
    	});
    	
    	
    	//'<div class="paramlist-item" paramid="'+nameId+'" valid="'+valId+'">'+
    	curProdData.params = new Array();
    	curProdData.params2delete = params2delete;
    	$('#edit-item-paramlist .paramlist-item').each(function(){
    		curProdData.params.push({paramid: $(this).attr('paramid'), valid:$(this).attr('valid'), id:$(this).attr('productparam')});
    	});
    	
    	curProdData.feature = new Array();
    	curProdData.feature2delete = feature2delete;
    	$('#edit-item-featurelist .featurelist-item').each(function(){
    		curProdData.feature.push({featureid: $(this).attr('featureid'), valid:$(this).attr('valid'), id:$(this).attr('productfeature')});
    	});

        ajaxLoad();
        client_shop.save_product_form(curProdData,function(){
        	ajaxLoad();
        	$('#main-product-tab').click();
        	$('#product-form').dialog('close');
        	grid.orders.start(function(){
            	activate_prod_grid();
            	get_group_tree();
            });
        });
    }
    
    
  
    // груповые операции
    function publicOnSite(atype){
    	if(atype == 1)
    		msgText = "Опубликовать выбранные?";
    	else
    		msgText = "Скрыть выбранные?";
    	
    	
    	if(confirm(msgText)){
    		client_shop.group_public_site(checkedItems, atype,  function(data){
                checkedItems['folder'] = new Object();
                checkedItems['product'] = new Object();
                grid.orders.start(function(){
                	activate_prod_grid();
                	get_group_tree();
                });
                ajaxLoad();
            });
    	}
    }
    
    function deleteSelected(type, id){
    	if(confirm('Удалить выбранные?')){
    		client_shop.delete_selected(checkedItems,  function(data){
                checkedItems['folder'] = new Object();
                checkedItems['product'] = new Object();
                grid.orders.start(function(){
                	activate_prod_grid();
                	get_group_tree();
                });
                ajaxLoad();
            });
    	}
    }
    
    function showMoveDialog(){
        $('.tree-element').unbind('click').click(function(event){
          event.stopPropagation();
          parrent_id = $(this).attr('item-id');
          ajaxLoad();
          client_shop.move_group(checkedItems, parrent_id, function(data){
            checkedItems['folder'] = new Object();
            checkedItems['product'] = new Object();
            grid.orders.addFilter('group_id', data).start(function(){activate_prod_grid();get_group_tree();$('#move-selected').dialog('destroy');});
            ajaxLoad();
          });
         
        });
          
          for(i in checkedItems['folder']){
            $('.tree-element[item-id="'+i+'"]').unbind('click').click(function(event){
              event.stopPropagation();
              alert('Действие не возможно. Один из выбранных элементов является коревой для выбранной папки. Выберете другую группу.');
            });
            $('.tree-element[item-id="'+i+'"]').find('li').unbind('click').click(function(event){
              event.stopPropagation();
              alert('Невозможно выполнить!');
              
            });
          }
        
        $('#move-selected').dialog({title:'Группа товара',autoOpen: true, modal: true, resizable:false, draggable:false});
      }
    
    function changeGroupDialog(){
        $('.tree-element').unbind('click').click(function(event){
          event.stopPropagation();
          id = $(this).attr('item-id');
          name = $(this).find('a').html();
          $('#edit-item-group_id').val(id);
          $('#edit-item-group_name').html(name);   
          $('#move-selected').dialog('destroy');
        })
        $('#move-selected').dialog('destroy');
        $('#move-selected').dialog({title:'Группа товара',autoOpen: true, modal: true, resizable:false, draggable:false});
      }
    
    function addOrderProductDialog(){
    	
        $('.tree-element').unbind('click').click(function(event){
        	event.stopPropagation();
        	id = $(this).attr('item-id');
        	grid.products.addFilter('group_id', id ).start();
        })
        $('#addProductToOrderForm').dialog('destroy');
        $('#addProductToOrderForm').dialog({title:'Добавление товара',autoOpen: true, width:'auto', modal: true, resizable:false, draggable:false});
        grid.products.addFilter('group_id', 0).start();
      }
    
    function get_group_tree(){
    	client_shop.get_group_tree(function(tree){
    		
    		resultHtml = '<ul style="margin-left:0;"><li item-id="0" class="tr-0 tree-element"><a href="javascript:void(0);">Корневая группа</a></li></ul>';
    		$('.tree-div').html(resultHtml);
    		for(a in tree){
    			$('.tr-'+tree[a]['parrent_id']).append('<ul><li item-id="'+tree[a]['group_id']+'" class="tree-element tr-'+tree[a]['group_id']+'"><a href="javascript:void(0);">'+tree[a]['name']+'</a></li></ul>');
    		}
    	
    	});
    }
    
    function activate_prod_grid(){
    	//alert('activate_prod_grid');
    	$('#grid-orders input').bind('click', function(event){
    		event.stopPropagation();
    	});
    	$('#grid-orders input').parent().bind('click', function(event){
    		
    		event.stopPropagation();
    	});
    	
    	fastEditLock = false;
    	 $('.group_acts_div').hide();
    	 folderCnt = 0;
    	 productCnt = 0;
    	for(i in checkedItems['folder']){
            $('.grCheckBox[item-id="'+i+'"][item-type="folder"]').attr("checked","checked");
            folderCnt = 1;
    	}

    	for(i in checkedItems['product']){
            $('.grCheckBox[item-id="'+i+'"][item-type="product"]').attr("checked","checked");
            productCnt = 1;
    	}
    	
    	if(folderCnt>0 || productCnt>0)
    		$('.group_acts_div').show();
    	
    	$('#grid-orders input').uniform();
    	 $('.grCheckBox').click(function(){
    		
    	        id = $(this).attr('item-id');
    	        type = $(this).attr('item-type');

    	        if(typeof checkedItems[type][id] != 'undefined'){
    	          delete checkedItems[type][id];
    	          $(this).removeAttr("checked");
    	        }else{
    	        	checkedItems[type][id] = type;
    	          $(this).attr("checked","checked");
    	        } 
    	        $('.group_acts_div').hide();
    	        for(i in checkedItems['product']){
    	        	$('.group_acts_div').show();
    	        }
    	        for(i in checkedItems['folder']){
    	        	$('.group_acts_div').show();
    	        }
    	      })
    	      
    	  $('.addition-items').click(function(){
    	        $('#addition-form').dialog({title:'Сопутствующие товары',autoOpen: true, modal: true, width:625, resizable:false, draggable:false});
    	      });
    	      /*
    	      $('.sale').click(function(){
    	        if(!fastEditLock){
    	          fastEditLock = true;
    	          $('#saleForm').dialog({title:'Управление скидками',autoOpen: true, modal: true, width:625, resizable:false, draggable:false});
    	    
    	        }      
    	      });
    	      
    	      $('.price').click(function(){
    	        
    	        fastTitleEditId = $(this).parent().attr('item-id');
    	              if(typeof(fastTitleEditId) != 'undefined' && !fastEditLock){
    	                fastEditLock = true
    	                  $(this).find("span:first").hide();
    	                $(this).append($('<div id="price-edit">'+
    	                                 '<table style="width:100px"><tr><td><input style="width:40px" class="decimal" type="number" value="3009"></td>'+
    	                                 '<td><select><option>$</option><option>руб.</option><option>&euro;</option></select></td></tr>'+
    	                                 '<tr><td>за</td><td><select><option>шт.</option></select></td></tr>'+
    	                                 '<tr><td></td><td><a onclick="evt = event || window.event; evt.cancelBubble = true;" title="Сохранить" class="green btn i_tick small"></a>'+
    	                                 '<a onclick="fastPriceEditCancel();evt = event || window.event; evt.cancelBubble = true;" title="Отмена" class="btn i_cross small red"></a></td></tr></table></div>'));

    	              }
    	      }); */
    	 
    	 //SRAKA
//    	      $('.title').hover(
//    	            function () {
//    	              ooo = $(this).parent().parent().parent();
//    	              fastTitleEditId = ooo.attr('item-id');
//    	              fastTitleEditType = ooo.attr('item-type');
//    	              if(typeof(fastTitleEditId) != 'undefined' && !fastEditLock){
//    	             
//    	            	if(fastTitleEditType == 'folder') {
//        	                //$(this).append($('<img onclick="showTpGroupDialog('+fastTitleEditId+'); evt = event || window.event; evt.cancelBubble = true;" style="cursor:pointer;" width="17" src="/templates/admin/green/images/icons/dark/pencil.png">'));
//    	            	} else {
//        	                $(this).append($('<img onclick="fastTitleEdit($(this));evt = event || window.event; evt.cancelBubble = true;" style="cursor:pointer;" width="17" src="/templates/admin/green/images/icons/dark/pencil.png">'));
//    	            	}
//
//    	              }
//    	            }, 
//    	            function () {
//    	              $(this).find("img:last").remove();
//    	            }); 
    }
 
    function fastPriceEditCancel(obj){

      $('#price-edit').remove();
      $('.price span').show();

      fastEditLock = false;
    }
   
    function fastTitleEdit(obj){
      var tmp = $(obj).parent();
      $(obj).remove();
      fastEditLock = true;
      fastTitleEditOldText = $(tmp).html();
      $(tmp).html('<div style="width:80%"><input onclick="evt = event || window.event; evt.cancelBubble = true;" id="fastEditTitleNewText" type="text" value="'+$(tmp).html()+
                  '"></div><div style="margin-top:-3px"><a onclick="fastTitleEditSave($(this));evt = event || window.event; evt.cancelBubble = true;" title="Сохранить" class="green btn i_tick small"></a>'+
                  '<a onclick="fastTitleEditCancel($(this));evt = event || window.event; evt.cancelBubble = true;" title="Отмена" class="red btn i_cross small"></a></div><br style="clear:both">');
    }
    
    function fastTitleEditCancel(obj){
      $(obj).parent().parent().html(fastTitleEditOldText);
      fastEditLock = false
    }
    
    function fastTitleEditSave(obj){
    	
      if($('#fastEditTitleNewText').val().length > 1){
    	  title = $('#fastEditTitleNewText').val();
        $(obj).parent().parent().html($('#fastEditTitleNewText').val());
        fastEditLock = false;
        	client_shop.edit_title({type:fastTitleEditType, id:fastTitleEditId, title:title}, function(){
        		
        	})
        
        	
        
      }else{
        alert('Введите заголовок!');
      }
    }

    function clearProductForm(){
    	$('#edit-item-title').val('');
        $('#edit-item-price').val('');
        $('#edit-item-sale').val('');
        $('#edit-item-description').val('');
        //$('#edit-item-full-description').text('');
        $('#edit-item-add_date').html('');
        $('#edit-item-update_date').html('');
        $('#edit-item-article').val('');
        $('#edit-item-seoUrl').val('');
        $('#edit-item-seoTitle').val('');
        $('#edit-item-seoKeywords').val('');
        $('#edit-item-seoDescription').val('');
        $('.product-img-list').html('');
        $('#edit-item-property-block').html('');
        $('#edit-item-group_name').html('');
        $('#edit-item-group_id').val(0);
        $('#edit-item-model_name').val('');
        $('#main-product-tab').click();
        $('#product_day_date').val('');
        img2delete = new Array();
        params2delete = new Array();
        feature2delete = new Array();
        $('.qq-upload-list').html('');
        //
        //alert('clear');
        
        //$("#edit-item-full-description").wl_Editor('destroy');
        
    	//txteditor = $("#edit-item-full-description").wl_Editor();
        //$("#edit-item-full-description").wl_Editor('clear');
        
    }
    
    function addProductDialog(){
    	curProdData = new Object();
    	clearProductForm();
    	client_shop.create_empty_product(current_group_id, function (id){
//    		fastTitleEditId = id;
    		showProduct(id);
    		$('#edit-item-group_id').val(current_group_id);
        	$('#edit-item-group_name').html(current_group_name);
        	$('.qq-upload-list').html('');
    	});
    }
    
    function editProdParamsForm(){
    	$('#editProdParamsForm').dialog({title:'Значения свойства',autoOpen: true, modal: true, width:500, resizable:false, draggable:false});
    }
    
    function refreshSaleForm(){
      var newsale = 0;
      var currentPrice = parseInt($('#currentPrice').val());
      var saleValue = parseInt($('#saleValue').val());
      if($('#saleType').val() == 2){
        if(saleValue >= currentPrice || saleValue < 0){
          alert('Размер скидки задан не верно!');
          return;
        }
        newsale = currentPrice - saleValue
        $('#newPrice').val(newsale);
      }else
        if($('#saleType').val() == 1){
          if(saleValue > 100 || saleValue < 0){
            alert('Размер скидки задан не верно!');
            return;
          }
          newsale = currentPrice / 100 * saleValue;
          newsale = Math.round((currentPrice - newsale)*100)/100;
          $('#newPrice').val(newsale);
        }
    }
  
    
    function changeAvailable(){
    value = $('#edit-item-available').val();
      $('.available-variant').hide();
      if(value == 1){
        $('#available-variant-1').show();
      }
      if(value == 3){
        $('#available-variant-2').show();
      }
    }
    
    function changeCountType(){
      $('.countType').html($("#edit-item-count-type option:selected").text());
    }
    
    function addSaleItem(){
      $('#saleVariants').append(
                      '<div class="saleItem"><input id="edit-item-slae" class="decimal" type="number" value="">'+
                      '<div><select><option>%</option><option class="saleType"></option></select></div>'+
        '<div style="margin-left:15px;margin-top:7px; margin-right:3px">от:</div><div><input class="inpMeasure" type="text" value="0"><span  style="float: left; margin-right:20px; margin-left:5px;margin-top:7px"  class="countType"></span></div>'+
                      '<a onclick="$(this).parent().remove();" title="Удалить" class="abtn btn i_cross small"></a></div>'
      );
     
      $('.saleItem:last select').uniform();
      
      changeCountType();
      changeSaleType();
      
    }
    
    function changeSaleType(){
      $('.saleType').html($("#saleType option:selected").text());
    }
   
    function checkcheck(id, status){
    	if(status){
    		$('#'+id).attr("checked","checked")
    	}else{
    		$('#'+id).removeAttr("checked")
    	}
    }
    
    var txteditor = false;
    
    
    
    
    function loadFormData(data){
      curProdData = data;
      
      $('.qq-upload-list').html('');
      img2delete = new Array();
      params2delete = new Array();
      feature2delete = new Array();
      
      if(data.public_site == '1'){
    	  $('#uniform-edit-item-public_site span').addClass('checked');
    	  $('#edit-item-public_site').attr("checked","checked");
      }else{
    	  $('#edit-item-public_site').removeAttr("checked");
    	  $('#uniform-edit-item-public_site span').removeClass('checked');
      }
      
      if(data.recommend == '1'){
    	  $('#uniform-edit-item-recommend span').addClass('checked');
    	  $('#edit-item-recommend').attr("checked","checked");
      }else{
    	  $('#edit-item-recommend').removeAttr("checked");
    	  $('#uniform-edit-item-recommend span').removeClass('checked');
      }
      
      if(data.slider == '1'){
    	  $('#uniform-edit-item-slider span').addClass('checked');
    	  $('#edit-item-slider').attr("checked","checked");
      }else{
    	  $('#edit-item-slider').removeAttr("checked");
    	  $('#uniform-edit-item-slider span').removeClass('checked');
      }
      
      if(data.neww == '1'){
    	  $('#uniform-edit-item-new span').addClass('checked');
    	  $('#edit-item-new').attr("checked","checked");
      }else{
    	  $('#edit-item-new').removeAttr("checked");
    	  $('#uniform-edit-item-new span').removeClass('checked');
      }
      if(data.product_day == '1'){
    	  $('#uniform-edit-item-product_day span').addClass('checked');
    	  $('#edit-item-product_day').attr("checked","checked");
      }else{
    	  $('#edit-item-product_day').removeAttr("checked");
    	  $('#uniform-edit-item-product_day span').removeClass('checked');
      }
      
      $('#product_day_date').val(data.product_day_date);
      $('#edit-item-title').val(data.title);
      $('#edit-item-price').val(data.price);
      $('#edit-item-sale').val(data.sale);
      $('#edit-item-description').val(data.description);
      $('#edit-item-add_date').html(data.add_date);
      $('#edit-item-update_date').html(data.update_date);
      $('#edit-item-article').val(data.article);
      $('#edit-item-model_name').val(data.model_name);
      //SEO
      $('#edit-item-parrentUrl').html(data.parrent_rewrite_url);
      $('#edit-item-seoUrl').val(data.seo_url);
      $('#edit-item-seoTitle').val(data.seo_title);
      $('#edit-item-seoKeywords').val(data.seo_keywords);
      $('#edit-item-seoDescription').val(data.seo_description);
      $('.product-img-list').html('');
      
      $('#edit-item-group_name').html(data.group_name);
      $('#edit-item-group_id').val(data.group_id);
//      $("#edit-item-full-description").wl_Editor('destroy');
//      
      client_shop.get_user_param_list(function(data){
          paramsArray = data;
          
          $('#sel-param-name').html('');
          $('#sel-param-name').append('<option value="" disabled="disabled" selected="selected">Выбрать параметр</option>'+
                                      '<option value="new">Новый параметр</option>');
          
          for(i in paramsArray){
            $('#sel-param-name').append('<option value="'+paramsArray[i].param_id+'">'+paramsArray[i].name+'</option>')
          }
          if(curProdData.params)
          addnewprod2param(curProdData.params);
        });
      
      
      client_shop.get_user_feature_list(function(data){
    	  featureArray = data;
          
          $('#sel-feature-name').html('');
          $('#sel-feature-name').append('<option value="" disabled="disabled" selected="selected">Выбрать характеристику</option>'+
                                      '<option value="new">Новая характеристика</option>');
          
          for(i in featureArray){
            $('#sel-feature-name').append('<option value="'+featureArray[i].feature_id+'">'+featureArray[i].name+'</option>')
          }
          if(curProdData.feature)
          addnewprod2feature(curProdData.feature);
        });
      
      loadImagesToForm(data.img);
      
      
      changeAvailable();
      changeCountType();
      updateManufactList(data.manufacturer);
      $('#edit-item-manufacturer_id').val(data.manufacturer_id);
      $('#edit-item-manufacturer_id').val(data.manufacturer_id);
      
      
      
      	$('#edit-item-store_id').val(data.store_id);
  		$('#edit-item-store-count').val(data.quantity);
      
      
      $('#product-form').dialog({title:'Карточка товара',autoOpen: true, modal: true, width:900, resizable:false});
      $('#main-product-tab').click();
      setTimeout(function(){
    	  $(document).unbind('mousedown.dialog-overlay').unbind('mousedown.dialog-overlay');
      }, 200);
      
            
      if(textEditor) {
    	 textEditor.setData(data.full_description);
  	  } else {
  	      textEditor =  CKEDITOR.appendTo('texteditor1', {filebrowserUploadUrl : '/ru/files/upload/source/static/application/ckeditor/'}, data.full_description);
  	  }
      
      ajaxLoad();
    }
    
    
    function delImg(obj){
    	if(confirm('Удалить изображение?')){
    		
	    	obj = $(obj).parent()
	    	imId = $(obj).find('img').attr('img_id');
	    	$(obj).remove();

	    	if(typeof(imId) != 'undefined')
	    		img2delete.push(imId);
    	}
    		
    }
    
    function addUploadedImg(original, sqOriginal, smallSqOriginal){
    	 $('.product-img-list').append(
     			  '<div class="product-img-item" id="">'+
     			  		'<div><input type="radio" name="main_img"></div><div class="img-del" onclick="delImg(this)">Х</div>'+
     			  		'<img url_original = "'+original+'" url_preview = "'+sqOriginal+'" id="" src="'+smallSqOriginal+'"  style="width:150px; height:150px;" onclick="cropThisImgDialog(this);"></div>'+
                 '</div>');
    }
    
    
    function addUploadedGroupImg(original, sqOriginal, smallSqOriginal){
    	$('.qq-upload-list').html('');
        $('#imgForGroup').removeAttr("src").attr('src',smallSqOriginal);
        $('#imgForGroup').attr('url_original',original).attr('url_preview',sqOriginal)
        $('#imgForGroup').unbind('click');
        $('#imgForGroup').bind('click', function(){
        	cropThisImgDialog(this);
        });
        
        
        
        $('#edit-group-image').val(smallSqOriginal);
        $('#edit-group-image-original').val(original);
        $('#edit-group-image-sqOriginal').val(sqOriginal);
        $('#edit-group-removeImageButton').show();
   }
    
    
    function loadImagesToForm(imgs){
    	 $('.product-img-list').html('');
    	for(var a in imgs){
      	  var img = imgs[a];
      	  
      	  add_me = '';
      	  if(img.is_main == 1)
      		add_me = ' checked="checked" ';
      	  $('.product-img-list').append(
	   			  '<div class="product-img-item" id="">'+
	   			  		'<div><input '+add_me+' type="radio" name="main_img"></div><div class="img-del" onclick="delImg(this)">Х</div>'+
	   			  		'<img img_id="'+img.img_id+'" sx = "'+img.sx+'" sy = "'+img.sy+'" sw = "'+img.sw+'" sh = "'+img.sh+'" url_original = "'+img.url_big+'" url_preview = "'+img.url_preview+'" id="" src="'+img.url+'"  style="width:150px; height:150px;" onclick="cropThisImgDialog(this);"></div>'+
	               '</div>');
        }
    	//$('.img').Jcrop();
    }
    
    
    function cropThisImgDialog(img){
    	curCropImg = img;
    	url = $(img).attr('url_preview');
    	g_url = url;
    	if(typeof(jcrop_api) != "undefined")
    		jcrop_api.destroy();
    	
    	$('#cropDialog').dialog('destroy');
    	$('#img4crop').remove();
    	$('#cropDialog').append('<img id="img4crop" style="width:300px;float:left;">');
    	$('#img4crop').attr('src', url);
    	$('#cropDialog').dialog({
    		modal: true,
	        width:345,
	        buttons: {
	        	"Сохранить": function() {
	        	
	        		saveCropedImg({x:x, y:y, w:w, h:h, url:g_url});
	        		
	        		$( this ).dialog( "close" );
	        		$('#cropDialog').dialog('destroy');
	            },
	            "Отмена": function() {
	            	$( this ).dialog( "close" );
	            	$('#cropDialog').dialog('destroy');
	            }
            }
	    });
    	
    	$('#img4crop').Jcrop({
    		aspectRatio: 1,
	        onSelect: updateCoords
	        
	    },function(){
	    	jcrop_api = this;
	    	var k = 0;
	    	var imgcrop = document.getElementById('img4crop'), 
	    	//wwwww = imgcrop.naturalWidth;
	        k = 300/imgcrop.naturalWidth;
	    	img = curCropImg;
	    	//alert('k'+k+' sx'+$(img).attr('sx')+' sy'+$(img).attr('sy')+' sw'+$(img).attr('sw')+' sh'+$(img).attr('sh'));
	    	
	    	//alert(img.naturalWidth);
//	    	alert(imgId);
	    	

	    	if(typeof($(img).attr('sx')) != 'undefined' && typeof($(img).attr('sx')) != "null"){
	    		x = $(img).attr('sx')*k;
	    	    y = $(img).attr('sy')*k;
	    	    w = $(img).attr('sw')*k;
	    	    h = $(img).attr('sh')*k;
	    	    //alert('k'+k+' sx'+x+' sy'+y+' sw'+w+' sh'+h);
	    		jcrop_api.animateTo([x,y,(w+x),(h+y)]);
	    		
	    	}
	    	else{
	    		x = 100;
	    	    y = 100;
	    	    w = 100;
	    	    h = 100;
	    		jcrop_api.animateTo([x,y,(w+x),(h+y)]);
	    		
	    	}
	    });
    }
    
    function showProduct(id){
    	ajaxLoad();
        client_shop.tp_get_all_product_info(id, function(data){
        	isnew = false;
        	loadFormData(data);
        });
      }
    
    function showCopyProduct(id){
        client_shop.tp_get_all_product_info(id, function(data){
        	isnew = true;
        	loadFormData(data);
        });
      }
    
    
    function addNewGroup(){

      client_shop.add_new_group({parrent_id:current_group_id, title:$('#newGroupName').val()}, function(data){
      grid.orders.start(function(){activate_prod_grid();get_group_tree()});
      });
      $('#newGroupName').val('');
      $('#new-group-form').dialog('destroy');
    }
    
    function gotogroup(id){
      current_group_id = id;
      current_group_name = 'Корневая группа';
      window.location.hash = id;
      grid.orders.removeFilter('search');
      grid.orders.removeFilter('show_blocks');
      grid.orders.addFilter('group_id', id).start(function(){activate_prod_grid();});
    }
    
    function implode( glue, pieces ) {
    	return ( ( pieces instanceof Array ) ? pieces.join ( glue ) : pieces );
    }
    
    
    
    
    /*******************************************************************************/
    /*******************************************************************************/
    
    // очищает форму с редактирвоанием группы перед показом диалога
    function clearTpGroupDialog(){
    	$('#edit-group-title').val('');
    	$('#edit-group-descr').val('');
    	$('#edit-group-rewrite').val('');
    	$('#edit-group-rewrite_id').val('');
    	$('#edit-group-seo-title').val('');
    	$('#edit-group-seo-keyw').val('');
    	$('#edit-group-seo-descr').val('');
    	$('#edit-group-parrent_id').val('');
    	
    	$('#edit-group-editId').val('');
    	
    	$('#edit-group-rewrite').removeAttr('item-parent-auto-rewrite');
    	$('#edit-group-rewrite').removeAttr('item-parent-rewrite');
    	$('#edit-group-rewrite').removeAttr('item-auto-rewrite');
    	$('#edit-group-rewrite').removeAttr('item-prev-rewrite');
    	
    	$('#imgForGroup').attr('src',$('#imgForGroup').attr('item-nophoto-src'));
    	$('#edit-group-image').val('');
    	$('.qq-upload-list').html('');
    	$('#imageUploaderFromWisywigGroup-Window .qq-upload-size').remove();
    	$('#edit-group-removeImageButton').hide();
    }
    
    // сохраняет диалог группы
    function saveTpGroupNew(){
    	var id = $('#edit-group-editId').val(),
    		info = {
	    		name : $('#edit-group-title').val(),
	    		description : $('#edit-group-descr').val(),
	    		meta_title : $('#edit-group-seo-title').val(),
	    		meta_keyword : $('#edit-group-seo-keyw').val(),
	    		meta_description : $('#edit-group-seo-descr').val(),
	    		parrent_id : $('#edit-group-parrent_id').val(),
	    		image : '',
	    		rewrite_id : $('#edit-group-rewrite_id').val(),
	    		rewrite : $('#edit-group-rewrite').val(),
	    		prev_rewrite : $('#edit-group-rewrite').attr('item-prev-rewrite'),
	    		rewrite_action : ''
    		};
    	
    		if($('#imgForGroup').attr('src') != $('#imgForGroup').attr('item-nophoto-src')) {
    			info.image = $('#imgForGroup').attr('src');
    			info.image_attr = {
    				url_big: $('#imgForGroup').attr('url_original'),	
    				url_preview: $('#imgForGroup').attr('url_preview'),
        			sx:$('#imgForGroup').attr('sx'), 	
        			sy:$('#imgForGroup').attr('sy'), 	
        			sw:$('#imgForGroup').attr('sw'), 	
        			sh:$('#imgForGroup').attr('sh')
        		};
    		}
    		
    		
    		// тут сложности с рерайтом.
    		if(id) {
    			if(info.prev_rewrite == info.rewrite) {
    				// у нас есть рерайт и он был до редактирвоания такой же. Ничего не делаем с ним.
    				info.rewrite_action = 'none';
    			} else {
    				// рерайт отличается от того что был до этого
    				if(info.rewrite_id) { // был ли ранее рерайт вообще?
    					info.rewrite_action = (info.rewrite == '' || info.rewrite == '/' || info.rewrite == '//') ? 'delete' : 'edit'; // если был, то он не стал ли пустым? если он стал пустым, то нужно избавиться от него
    				} else {
    					info.rewrite_action = (info.rewrite == '' || info.rewrite == '/' || info.rewrite == '//') ? 'none' : 'new'; // рерайта небыло. проверяем на пустой рерайт.
    				}
    			}
    		} else {
    			// у нас новая категория.
    			if(info.rewrite && (info.rewrite != '' || info.rewrite != '/' || info.rewrite != '//')) {
    				info.rewrite_action = 'new';
    			} else {
    				info.rewrite_action = 'none';
    			}
    		}
    		
    		//delete info.prev_rewrite;
    		
    		if($('#edit-group-rewrite_id').val()) {
    			var prev_rewrite = $('#edit-group-rewrite').attr('item-prev-rewrite');
    			if(prev_rewrite == $('#edit-group-rewrite')) {
    				info.rewrite_id = $('#edit-group-rewrite_id').val();
    			} else {
    				if($('#edit-group-rewrite')) {
    					info.rewrite = $('#edit-group-rewrite');
    				}
    				if($('#edit-group-rewrite_id').val()) {
    					info.rewrite_id = $('#edit-group-rewrite_id').val();
    				}
    			}
    		}
    		
    		if(!info.name) {
    			$.msg('Не заполнено обязательное поле &laquo;Название&raquo;!');
    			$('#edit-group-title').focus();
    			$('#edit-group-title').parent().parent().addClass('error');
    		} else {
    			$('#edit-group-title').removeClass('error');
    			ajaxLoad();
    			client_shop.save_group_info_new(id, info, function(){
    				grid.orders.start(function(){
    	            	activate_prod_grid();
    	            	get_group_tree();
    	            });
    				
    				ajaxLoad();
    				$('#groupEditDialog').dialog('destroy');
    				$.msg('Данные успешно сохранены!');
    			});
    		}
    }
    
    // удаляет картинку из группы
    function removeImageTpGroupDialog() {
    	$('#imgForGroup').attr('src',$('#imgForGroup').attr('item-nophoto-src'));
		$('#edit-group-image').val('');
		$('#edit-group-removeImageButton').hide();
    }
    
    // показывает диалог редактирования группы
    function showTpGroupDialog(id){
    	clearTpGroupDialog();
    	ajaxLoad();
    	var parent_id = false;
    	
    	if(id) {
			$('#edit-group-editId').val(id);
			parent_id = 0;
		} else {
			$('#edit-group-editId').val('');
			parent_id = (grid.orders.filters.isset('group_id'))? grid.orders.filters.get('group_id') : 0;
		}
    	
    	client_shop.get_group_info(id, parent_id, function(data) {
    		ajaxLoad();
    		
    		if(!parent_id) parent_id = data.parrent_id;
    		
    		$('#edit-group-title').val(data.name);
        	$('#edit-group-descr').val(data.description);
        	$('#edit-group-rewrite').val(data.rewrite_url);
        	$('#edit-group-rewrite_id').val(data.rewrite_id);
        	$('#edit-group-seo-title').val(data.meta_title);
        	$('#edit-group-seo-keyw').val(data.meta_keyword);
        	$('#edit-group-seo-descr').val(data.meta_description);
        	$('#edit-group-parrent_id').val(parent_id);
        	
        	$('#edit-group-rewrite').attr('item-parent-auto-rewrite', data.rewrite_parent_auto);
        	$('#edit-group-rewrite').attr('item-parent-rewrite', data.rewrite_parent);
        	$('#edit-group-rewrite').attr('item-auto-rewrite', data.auto_rewrite);
        	$('#edit-group-rewrite').attr('item-prev-rewrite', data.rewrite_url);
        	
        	if(id) {
        		$('#edit-group-title').unbind('keyup change');
        		$('#edit-group-rewrite').parent().find('a').show();
        	} else {
        		$('#edit-group-rewrite').parent().find('a').click();
        		$('#edit-group-rewrite').parent().find('a').show();
        		
        		var insertBaseRewrite = $('#edit-group-rewrite').attr('item-parent-rewrite');
        		if(!insertBaseRewrite) {
        			insertBaseRewrite = $('#edit-group-rewrite').attr('item-parent-auto-rewrite');
        		}
        		$('#edit-group-rewrite').val(insertBaseRewrite);
        		
        		$('#edit-group-title').bind('keyup change', function(){
        			var rewriteCompile = insertBaseRewrite+(($(this).val()).toLowerCase().translit())+'/';
        			$('#edit-group-rewrite').val(rewriteCompile);
        			$('#edit-group-rewrite').attr('item-auto-rewrite', rewriteCompile);
        		});
        	}
        	
        	if(data.image) {
        		$('#imgForGroup').attr('src',data.image);
        		if(!data.url_preview){
        			data.url_preview = data.image;
        			data.url_big = data.image;
        		}
           		$('#imgForGroup').attr('url_original',data.url_big);	
        		$('#imgForGroup').attr('url_preview',data.url_preview);
        		$('#imgForGroup').attr('sx',data.sx);
        		$('#imgForGroup').attr('sy',data.sy);
        		$('#imgForGroup').attr('sw',data.sw);
        		$('#imgForGroup').attr('sh',data.sh);

        		
        		$('#edit-group-image').val(data.image);
        		$('#edit-group-removeImageButton').show();
        		$('#imgForGroup').bind('click', function(){
                	cropThisImgDialog(this);
                });
        	} else {
        		$('#imgForGroup').attr('src',$('#imgForGroup').attr('item-nophoto-src'));
        		$('#edit-group-image').val('');
        		$('#edit-group-removeImageButton').hide();
        	}
        	
        	var form_title = (id)? 'Редактирвоание категории' : 'Новая категория';
        	
        	$('#groupEditDialog').dialog({title:form_title,autoOpen: true, close:function(){ $(this).dialog('destroy') }, modal: true, width:600, resizable:false, 
        		buttons: {
    	        	"Сохранить": function() {
    	        		saveTpGroupNew();
    	            },
    	            "Отмена": function() {

    	            	$(this).dialog('destroy');
    	            }
                }
        	});
        	
        	setTimeout(function(){
        	   $(document).unbind('mousedown.dialog-overlay').unbind('mousedown.dialog-overlay');
          	   $(document).unbind('mousedown.dialog-overlay').unbind('mousedown.dialog-overlay');
            }, 500);
        	
          	
    	});
    }
    
    
    
    
    String.prototype.translit = (function(){
    	var L = {' ':'_', 'А':'A','а':'a','Б':'B','б':'b','В':'V','в':'v','Г':'G','г':'g','Д':'D','д':'d','Е':'E','е':'e','Ё':'Yo','ё':'yo','Ж':'Zh','ж':'zh',
    	'З':'Z','з':'z','И':'I','и':'i','Й':'Y','й':'y','К':'K','к':'k','Л':'L','л':'l','М':'M','м':'m','Н':'N','н':'n','О':'O','о':'o','П':'P','п':'p','Р':'R','р':'r','С':'S','с':'s','Т':'T','т':'t',
    	'У':'U','у':'u','Ф':'F','ф':'f','Х':'Kh','х':'kh','Ц':'Ts','ц':'ts','Ч':'Ch','ч':'ch','Ш':'Sh','ш':'sh','Щ':'Sch','щ':'sch','Ъ':'"','ъ':'"','Ы':'Y','ы':'y','Ь':"'",'ь':"'",'Э':'E','э':'e','Ю':'Yu','ю':'yu','Я':'Ya','я':'ya'},
    	r = '',k;
    	
    	for (k in L) {
    		r += k;
    	}
    	
    	r = new RegExp('[' + r + ']', 'g');
    	k = function(a){
    		return a in L ? L[a] : '';
    	};
    	
    	return function(){
    		return this.replace(r, k).replace(/[^A-z0-9\_\-\.]/g,'');
    	};
	})();
    
    
    
    
    
    
    
    
    
    