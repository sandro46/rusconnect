<? $this->md5_compil='7e574f9a85245245e9ed0f04b591c417'; ?>
<div class="row-fluid">
  <div class="span12" id="contactlist">
    <div class="widget">
      <div class="widget-title">
        <h4><i class="icon-reorder"></i>Товары</h4>
        <div class="actions">
          <div class="btn-group">
            <button data-toggle="dropdown" class="btn dropdown-toggle"><i class="icon-cog"></i></button>
                  	<script type="text/template" id="grid-product_list-settings-MenuTemplate">
<ul class="dropdown-menu pull-right">
                        {item}<li name="{name}" {hidden}><a href="javascript:;"><i></i> {title}</a></li>{/item}
                      </ul>	</script>
<script type="text/javascript">
 try {
	var grid_product_list_settings_menu_user_data = JSON.parse('<?php if(isset($this->vars['userSetting']['grid_product_list_hidecols'])) echo json_encode($this->vars['userSetting']['grid_product_list_hidecols']); else echo '[]'; ?>');
 }
 catch(e) {
 	console.log('error user settings! -> '+ e.message);
 }
</script>

          </div>
        </div>
        
      </div>
      <div class="widget-body">
        <div class="row-fluid">         
          <div class="span8">
            <a href="javascript:void(0);" id="addProductCategoryButton" class="btn btn-success"><i class="icon-plus"></i> Добавить категорию</a>
            <a href="javascript:void(0);" id="addProductButton" class="btn btn-success"><i class="icon-plus"></i> Добавить товар</a>

            
            <div class="btn-group" >
              <a href="javascript:void(0);" data-toggle="dropdown" class="btn btn-primary dropdown-toggle"><i class="icon-tags"></i> Действия</a>
              <span style="display:none" id="grid-product_list-groupAction-automenu"></span><script type="text/javascript">
 try {
	var grid_product_list_groupaction_data = JSON.parse('{"actions":[{"action":"hide","title":"\u041d\u0435 \u043f\u043e\u043a\u0430\u0437\u044b\u0432\u0430\u0442\u044c \u043d\u0430 \u0441\u0430\u0439\u0442\u0435","onselect":true,"icon":" icon-eye-close"},{"action":"change","title":"\u0418\u0437\u043c\u0435\u043d\u0438\u0442\u044c \u043f\u0430\u0440\u0430\u043c\u0435\u0442\u0440","onselect":true,"icon":" icon-cog"},{"action":"category","title":"\u041f\u0440\u0438\u0432\u044f\u0437\u0430\u0442\u044c \u043a \u043a\u0430\u0442\u0435\u0433\u043e\u0440\u0438\u0438","onselect":true,"icon":" icon-tags"},{"action":"divider"},{"action":"remove","title":"\u0423\u0434\u0430\u043b\u0438\u0442\u044c","icon":"trash","onselect":true},{"action":"copy","title":"\u041a\u043e\u043f\u0438\u0440\u043e\u0432\u0430\u0442\u044c","icon":"copy","onselect":true},{"action":"paste","title":"\u0412\u0441\u0442\u0430\u0432\u0438\u0442\u044c","icon":"paste","onboofer":true}]}');
 }
 catch(e) {
 	console.log('error grid group action settings! -> '+ e.message);
 }
</script>

            </div>
        
            
            <div class="btn-group">
              <a href="javascript:void(0);" class="btn btn-warning" onclick="$('#grid-product_list-filters').slideToggle()"><i class="icon-filter"></i> Фильтр <span class="badge badge-success" id="productsFilter_button_badge" style="display:none">0</span></a>
            </div>
            
          </div>
          
          
          <div class="span4">
            <div class="dataTables_filter" id="sample_1_filter">
              <input type="text" aria-controls="sample_1" class="input-medium">
              <a href="javascript:void(0)" class="btn" onclick="grid.product_list.addFilter('search', $(this).parent().find('input').val()).start()"><i class="icon icon-search"></i>Искать</a>
            
            </div>
          </div>
        </div>   
        
        <div id="catalog_crumbs"></div>
        
        <div class="row-fluid grid-hidden-filters" id="grid-product_list-filters">
          <div class="span12" style="max-width:600px;">
            <div class="well">
              <h4 style="margin-top: -13px; margin-left: -11px;">Фильтр</h4>
              <form class="form-horizontal" style="margin-bottom:0px;">
                 <div class="control-group">
                  <label class="control-label" style="width:120px;">Публикуемый раздел</label>
                  <div class="controls" style="margin-left:120px;" >
                      <select data-placeholder="Выберите элементы" name="filter_feed" class="chosen-with-diselect span12">
                          <option value="0"></option>
                          <?php if(count($this->vars['shopFeeds'])) : 
							   		 $this->vars['foreach']['shopFeeds']['count'] = count($this->vars['shopFeeds']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['shopFeeds']);
							   		 foreach($this->vars['shopFeeds'] as $this->vars['foreach']['shopFeeds']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['shopFeeds']['key']; ?>
                        	<option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                          <?php endforeach; ?><?php endif; ?>
                       </select>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" style="width:120px;">Наличие товара</label>
                  <div class="controls" style="margin-left:120px;">
                      <select data-placeholder="Выберите элементы"  name="filter_avaliable"  class="chosen-with-diselect span12">
                          <option value="0"></option>
                        	<option value="1">На складе</option>
                        	<option value="2">Под заказ</option>
                        	<option value="3">Нет в наличии</option>
                       </select>
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label" style="width:120px;">Товары со скидкой</label>
                  <div class="controls" style="margin-left:120px;">
                      <select data-placeholder="Выберите элементы"  name="filter_sales"  class="chosen-with-diselect span12">
                          <option value="0"></option>
                        	<option value="1">Постоянная скидка</option>
                        	<option value="2">Временная скидка</option>
                        	<option value="10">Все товары со скидкой</option>
                       </select>
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label" style="width:120px;">Производитель</label>
                  <div class="controls" style="margin-left:120px;">
                      <select data-placeholder="Выберите элементы"  name="filter_vendor"  class="chosen-with-diselect span12">
                          <option value="0"></option>
                          <?php if(count($this->vars['shopVendors'])) : 
							   		 $this->vars['foreach']['shopVendors']['count'] = count($this->vars['shopVendors']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['shopVendors']);
							   		 foreach($this->vars['shopVendors'] as $this->vars['foreach']['shopVendors']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['shopVendors']['key']; ?>
                           <option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                          <?php endforeach; ?><?php endif; ?>
                       </select>
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label" style="width:120px;">Тип товара</label>
                  <div class="controls" style="margin-left:120px;">
                      <select data-placeholder="Выберите элементы"  name="filter_category"  class="chosen-with-diselect span12">
                          <option value="0"></option>
                          <?php $this->vars['iterationNum'] = 0; ?>
                          <?php if(count($this->vars['shopCategory'])) : 
							   		 $this->vars['foreach']['shopCategory']['count'] = count($this->vars['shopCategory']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['shopCategory']);
							   		 foreach($this->vars['shopCategory'] as $this->vars['foreach']['shopCategory']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['shopCategory']['key']; ?>
                        	<?php if (isset($this->vars['item']['level']) && $this->vars['item']['level'] == 1): ?>
                        		<?php if (isset($this->vars['iterationNum']) && $this->vars['iterationNum'] > 0): ?></optgroup><?php endif; ?>
                        		<optgroup label="<?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?>">
                            <?php else: ?>      
                                <option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>"> -- <?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                        	<?php endif; ?>
                            <?php $this->vars['iterationNum']++; ?>
                          <?php endforeach; ?><?php endif; ?>
                            </optgroup>
                       </select>
                  </div>
                </div>
               
                <a class="btn btn-success" onclick="shopMain.appendFilter();"><i class="icon-ok icon-white"></i> Применить фильтр</a>
                <a class="btn btn-danger" onclick="shopMain.clearFilter();"><i class="icon-remove icon-white"></i> Очистить фильтр</a>
              </form>              
            </div>
          </div>
        </div>
        
        
        <?php
if(!isset($this->vars['globalgrid'])) $this->vars['globalgrid'] = array();
$this->vars['globalgrid']['product_list'] = json_decode('{"params":{"name":"product_list","tpl":"grid.catalog.html","class":"admin_shop","method":"getAdminCatalogList"},"settings":{"cols":[{"name":"id","title":"id","search":false,"sort":true,"hidden":true},{"name":"article","title":"Артикул","search":true,"sort":true,"hidden":true},{"name":"photo","title":"Фото","search":false,"sort":false,"from_tpl":"product_list_photo"},{"name":"name","title":"Название","search":true,"sort":true,"sorted":false,"from_tpl":"product_list_show"},{"name":"update_date","title":"Дата обновления","search":true,"sort":true},{"name":"price","title":"Цена","search":true,"sort":true},{"name":"sale","title":"Скидка","search":true,"sort":true,"hidden":true},{"name":"status_name","title":"Статус","search":true,"sort":true},{"name":"public_site","title":"Опубликован","search":true,"sort":true,"hidden":true},{"name":"product_type_name","title":"Тип продукта","search":true,"sort":true,"hidden":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"product_list_actions"}],"user_settings":true,"group_actions":true,"autoload":false,"view":"list","limit":10},"instance":{"name":"product_list","tpl":"grid.catalog.html","class":"admin_shop","method":"getAdminCatalogList"}}', true);
$this->vars['instance'] = 'product_list';
$this->vars['grid'] = json_decode('{"params":{"name":"product_list","tpl":"grid.catalog.html","class":"admin_shop","method":"getAdminCatalogList"},"settings":{"cols":[{"name":"id","title":"id","search":false,"sort":true,"hidden":true},{"name":"article","title":"Артикул","search":true,"sort":true,"hidden":true},{"name":"photo","title":"Фото","search":false,"sort":false,"from_tpl":"product_list_photo"},{"name":"name","title":"Название","search":true,"sort":true,"sorted":false,"from_tpl":"product_list_show"},{"name":"update_date","title":"Дата обновления","search":true,"sort":true},{"name":"price","title":"Цена","search":true,"sort":true},{"name":"sale","title":"Скидка","search":true,"sort":true,"hidden":true},{"name":"status_name","title":"Статус","search":true,"sort":true},{"name":"public_site","title":"Опубликован","search":true,"sort":true,"hidden":true},{"name":"product_type_name","title":"Тип продукта","search":true,"sort":true,"hidden":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"product_list_actions"}],"user_settings":true,"group_actions":true,"autoload":false,"view":"list","limit":10},"instance":{"name":"product_list","tpl":"grid.catalog.html","class":"admin_shop","method":"getAdminCatalogList"}}', true);
global $core;
$grid_tpl_site = ($core->is_admin())? $core->getAdminModule() : $core->site_name;
echo $this->get('grid.catalog.html',$grid_tpl_site);

unset($this->vars['grid'], $this->vars['instance']);
?>

<script type='text/javascript'>
 //try { 
   grid.product_list = new GridInstance('product_list'); 
   grid.product_list.params = JSON.parse('{"name":"product_list","tpl":"grid.catalog.html","class":"admin_shop","method":"getAdminCatalogList"}'); 
   grid.product_list.settings = JSON.parse('{"cols":[{"name":"id","title":"id","search":false,"sort":true,"hidden":true},{"name":"article","title":"Артикул","search":true,"sort":true,"hidden":true},{"name":"photo","title":"Фото","search":false,"sort":false,"from_tpl":"product_list_photo"},{"name":"name","title":"Название","search":true,"sort":true,"sorted":false,"from_tpl":"product_list_show"},{"name":"update_date","title":"Дата обновления","search":true,"sort":true},{"name":"price","title":"Цена","search":true,"sort":true},{"name":"sale","title":"Скидка","search":true,"sort":true,"hidden":true},{"name":"status_name","title":"Статус","search":true,"sort":true},{"name":"public_site","title":"Опубликован","search":true,"sort":true,"hidden":true},{"name":"product_type_name","title":"Тип продукта","search":true,"sort":true,"hidden":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"product_list_actions"}],"user_settings":true,"group_actions":true,"autoload":false,"view":"list","limit":10}'); 
   grid.product_list.init(); 
 //} catch(err) { 
 //  alert('Error create grid instance. '+err.message); 
 //} 
</script>
   
        
        
          <div id="product_list_actions" style="display:none">
            <div class="div-edit-btn"><a href="javascript:void(0)" onclick="shopMain.catalogActionEdit({id},'{row_type}', true)" class=" mini purple"><i class="icon-edit"></i> Редактировать</a></div>
            <div class="div-delete-btn"><a href="javascript:void(0)" onclick="shopMain.catalogActionDel({id},'{row_type}')" class="mini black"><i class="icon-trash"></i>&nbsp;Удалить</a></div>
          </div>
        
          <div id="product_list_photo" style="display:none">
            <div class="products_list {row_type}">
              <i class="showOnHover icon-move" style="position: absolute; color: rgb(50, 194, 205); display: none; font-size: 20px; margin-top: 14px; float: right; margin-left: 10px;"></i>
              <div class="plist-photo" style="background-repeat: no-repeat; height: 50px; width: 50px; background-size: contain; background-position: center center; background-image:url('{img}')"></div>
              <i class="plist-folder icon-folder-close" style="font-size:45px;"></i>
              
              <i class=" icon-caret-down" style="position: absolute; color: rgb(50, 194, 205); font-size: 18px; margin-top: 20px; margin-left: -37px; display:none"></i> 
              <i class=" icon-caret-up" style="position: absolute; color: rgb(50, 194, 205); font-size: 18px; margin-left: -37px; margin-top: 7px; display:none"></i>
            </div>
          </div>
          <div id="product_list_show" style="display:none">
            <a href="javascript:void(0)" onclick="shopMain.catalogActionEdit({id},'{row_type}')" class="catalog_name_link">{name}<span class="{row_type}"> ({items_count})</span></a>
            <p class="catalog_name_sub mod-{row_type}{mod_count}">Модификаций: {mod_count}</p>
          </div>
       </div>
     </div>
  </div>
</div>


