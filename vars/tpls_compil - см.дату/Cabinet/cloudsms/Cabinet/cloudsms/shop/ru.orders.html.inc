<? $this->md5_compil='a4e8ed72f6fcfe9e663e99d50b152501'; ?>
<div class="row-fluid">
  <div class="span12" id="contactlist">
    <div class="widget">
      <div class="widget-title">
        <h4><i class="icon-reorder"></i>Заказы</h4>
        <div class="actions">
          <div class="btn-group">
              <button data-toggle="dropdown" class="btn dropdown-toggle"><span class="icon-cog"></span></button>
                  	<script type="text/template" id="grid-orders_list-settings-MenuTemplate">
<ul class="dropdown-menu pull-right">
                        {item}<li name="{name}" {hidden}><a href="javascript:;"><i></i> {title}</a></li>{/item}
                      </ul>	</script>
<script type="text/javascript">
 try {
	var grid_orders_list_settings_menu_user_data = JSON.parse('<?php if(isset($this->vars['userSetting']['grid_orders_list_hidecols'])) echo json_encode($this->vars['userSetting']['grid_orders_list_hidecols']); else echo '[]'; ?>');
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
            <div class="btn-group" style="display:none">
              <a href="javascript:void(0);" data-toggle="dropdown" class="btn btn-primary dropdown-toggle"><i class="icon-tags"></i> Действия</a>
              <span style="display:none" id="grid-orders_list-groupAction-automenu"></span><script type="text/javascript">
 try {
	var grid_orders_list_groupaction_data = JSON.parse('{"actions":[{"action":"hide","title":"\u041e\u0442\u043c\u0435\u043d\u0438\u0442\u044c \u0437\u0430\u043a\u0430\u0437","onselect":true,"icon":" icon-ban-circle"},{"action":"divider"},{"action":"remove","title":"\u0423\u0434\u0430\u043b\u0438\u0442\u044c","icon":"trash","onselect":true},{"action":"copy","title":"\u041a\u043e\u043f\u0438\u0440\u043e\u0432\u0430\u0442\u044c","icon":"copy","onselect":true},{"action":"paste","title":"\u0412\u0441\u0442\u0430\u0432\u0438\u0442\u044c","icon":"paste","onboofer":true}]}');
 }
 catch(e) {
 	console.log('error grid group action settings! -> '+ e.message);
 }
</script>

            </div>
        
            
           
              <a href="javascript:void(0);" class="btn btn-warning" onclick="$('#grid-orders_list-filters').slideToggle()"><i class="icon-filter"></i> Фильтр <span class="badge badge-success" id="productsFilter_button_badge" style="display:none">0</span></a> 
            
              <?php if (isset($this->vars['IS_DEV']) && $this->vars['IS_DEV']  ): ?>
              
              <a href="#/add/" class="btn btn btn-success"><i class="icon-plus"></i> Создать заказ</a>
              <?php endif; ?>
           
            
          </div>
          
          
          <div class="span4">
            <div class="dataTables_filter" id="sample_1_filter">
              <input type="text" aria-controls="sample_1" class="input-medium">
              <a href="javascript:void(0)" class="btn" onclick="grid.orders_list.addFilter('search', $(this).parent().find('input').val()).start()"><i class="icon icon-search"></i>Искать</a>
            
            </div>
          </div>
        </div>   
        
    
        
        <div class="row-fluid grid-hidden-filters" id="grid-orders_list-filters">
          <div class="span12" style="max-width:600px;">
            <div class="well">
              <h4 style="margin-top: -13px; margin-left: -11px;">Фильтр</h4>
              <form class="form-horizontal" style="margin-bottom:0px;">
                 <div class="control-group">
                  <label class="control-label" style="width:120px;">Статус заказа</label>
                  <div class="controls" style="margin-left:120px;" >
                      <select data-placeholder="Выберите элементы" name="filter_status"  class="chosen-with-diselect span12">
                          <option value="0"></option>
                        	<?php if(count($this->vars['order_statuses'])) : 
							   		 $this->vars['foreach']['order_statuses']['count'] = count($this->vars['order_statuses']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['order_statuses']);
							   		 foreach($this->vars['order_statuses'] as $this->vars['foreach']['order_statuses']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['order_statuses']['key']?>
                          		<option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                        	<?php endforeach; ?><?php endif; ?>
                       </select>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" style="width:120px;">Способ доставки</label>
                  <div class="controls" style="margin-left:120px;" >
                      <select data-placeholder="Выберите элементы" name="delivery_type"  class="chosen-with-diselect span12">
                          <option value="0"></option>
                        
                        
                            <?php if(count($this->vars['delivery_type'])) : 
							   		 $this->vars['foreach']['delivery_type']['count'] = count($this->vars['delivery_type']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['delivery_type']);
							   		 foreach($this->vars['delivery_type'] as $this->vars['foreach']['delivery_type']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['delivery_type']['key']?>
                            	<option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                            <?php endforeach; ?><?php endif; ?>

                       </select>
                  </div>
                </div>
               
                
                
                <a class="btn btn-success"  onclick="admin_order.appendFilter();"><i class="icon-ok icon-white"></i> Применить фильтр</a>
                <a class="btn btn-danger" onclick="admin_order.clearFilter();"><i class="icon-remove icon-white"></i> Очистить фильтр</a>
              </form>              
            </div>
          </div>
        </div>
        
        
        <?php
if(!isset($this->vars['globalgrid'])) $this->vars['globalgrid'] = array();
$this->vars['globalgrid']['orders_list'] = json_decode('{"params":{"name":"orders_list","tpl":"grid.default.html","class":"admin_shop","method":"getOrdersList"},"settings":{"cols":[{"name":"order_id","title":"№","search":true,"sort":true,"sorted":true,"sort_type":"desc","hidden":false},{"name":"create_date","title":"Дата заказа","search":true,"sort":true},{"name":"delivery_date","title":"Дата доставки","search":true,"sort":true,"hidden":true},{"name":"sum","title":"Сумма","search":true,"sort":true,"sorted":false},{"name":"client_name","title":"Клиент","search":true,"sort":true,"from_tpl":"product_list_client"},{"name":"order_short_info","title":"Заказ","search":true,"sort":false},{"name":"order_status_name","title":"Статус заказа","search":true,"sort":true},{"name":"order_pay_name","title":"Статус оплаты","search":true,"sort":true,"hidden":true},{"name":"delivery_name","title":"Способ доставки","search":true,"sort":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"orders_list_actions"}],"user_settings":true,"group_actions":true,"autoload":false,"view":"list","limit":10,"click":"true"},"instance":{"name":"orders_list","tpl":"grid.default.html","class":"admin_shop","method":"getOrdersList"}}', true);
$this->vars['instance'] = 'orders_list';
$this->vars['grid'] = json_decode('{"params":{"name":"orders_list","tpl":"grid.default.html","class":"admin_shop","method":"getOrdersList"},"settings":{"cols":[{"name":"order_id","title":"№","search":true,"sort":true,"sorted":true,"sort_type":"desc","hidden":false},{"name":"create_date","title":"Дата заказа","search":true,"sort":true},{"name":"delivery_date","title":"Дата доставки","search":true,"sort":true,"hidden":true},{"name":"sum","title":"Сумма","search":true,"sort":true,"sorted":false},{"name":"client_name","title":"Клиент","search":true,"sort":true,"from_tpl":"product_list_client"},{"name":"order_short_info","title":"Заказ","search":true,"sort":false},{"name":"order_status_name","title":"Статус заказа","search":true,"sort":true},{"name":"order_pay_name","title":"Статус оплаты","search":true,"sort":true,"hidden":true},{"name":"delivery_name","title":"Способ доставки","search":true,"sort":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"orders_list_actions"}],"user_settings":true,"group_actions":true,"autoload":false,"view":"list","limit":10,"click":"true"},"instance":{"name":"orders_list","tpl":"grid.default.html","class":"admin_shop","method":"getOrdersList"}}', true);
global $core;
$grid_tpl_site = ($core->is_admin())? $core->getAdminModule() : $core->site_name;
echo $this->get('grid.default.html',$grid_tpl_site);

unset($this->vars['grid'], $this->vars['instance']);
?>

<script type='text/javascript'>
 //try { 
   grid.orders_list = new GridInstance('orders_list'); 
   grid.orders_list.params = JSON.parse('{"name":"orders_list","tpl":"grid.default.html","class":"admin_shop","method":"getOrdersList"}'); 
   grid.orders_list.settings = JSON.parse('{"cols":[{"name":"order_id","title":"№","search":true,"sort":true,"sorted":true,"sort_type":"desc","hidden":false},{"name":"create_date","title":"Дата заказа","search":true,"sort":true},{"name":"delivery_date","title":"Дата доставки","search":true,"sort":true,"hidden":true},{"name":"sum","title":"Сумма","search":true,"sort":true,"sorted":false},{"name":"client_name","title":"Клиент","search":true,"sort":true,"from_tpl":"product_list_client"},{"name":"order_short_info","title":"Заказ","search":true,"sort":false},{"name":"order_status_name","title":"Статус заказа","search":true,"sort":true},{"name":"order_pay_name","title":"Статус оплаты","search":true,"sort":true,"hidden":true},{"name":"delivery_name","title":"Способ доставки","search":true,"sort":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"orders_list_actions"}],"user_settings":true,"group_actions":true,"autoload":false,"view":"list","limit":10,"click":"true"}'); 
   grid.orders_list.init(); 
 //} catch(err) { 
 //  alert('Error create grid instance. '+err.message); 
 //} 
</script>
   
        
        

        <div id="orders_list_actions" style="display:none">
          <!--<div class="div-edit-btn"><a href="#/show/{order_id}/" class=" mini purple"><i class="icon-print"></i> Подробно</a></div>-->
          <div class="div-edit-btn"><a href="#/show/{order_id}/edit/"  onclick="evt = event || window.event; evt.cancelBubble = true;" class=" mini purple"><nobr><i class="icon-edit"></i> Редактировать</nobr></a></div>
          <div class="div-delete-btn"><a href="javascript:void(0)" onclick="evt = event || window.event; evt.cancelBubble = true; admin_order.removeOrder({order_id})" class="mini black"><i class="icon-trash"></i>&nbsp;Удалить</a></div>
          </div>
     
          <div id="product_list_client" style="display:none">
         
            <a href="#/show_client/{contact_id}/" onclick="evt = event || window.event; evt.cancelBubble = true;">{client_name}</a><br>
            	 {client_phone}<br>
            	 {client_email}
        </div>
        
       
       </div>
     </div>
  </div>
</div>


