<? $this->md5_compil='6c638c6aa33e6e1267bf7586f3f9ecc0'; ?>
<div class="row-fluid">
  <div class="span12" id="contactlist">
    <div class="widget">
      <div class="widget-title">
        <h4><i class="icon-reorder"></i>Клиенты</h4>
        <div class="actions">
          <div class="btn-group">
              <button data-toggle="dropdown" class="btn dropdown-toggle"><span class="icon-cog"></span></button>
                  	<script type="text/template" id="grid-clients_list-settings-MenuTemplate">
<ul class="dropdown-menu pull-right">
                        {item}<li name="{name}" {hidden}><a href="javascript:;"><i></i> {title}</a></li>{/item}
                      </ul>	</script>
<script type="text/javascript">
 try {
	var grid_clients_list_settings_menu_user_data = JSON.parse('<?php if(isset($this->vars['userSetting']['grid_clients_list_hidecols'])) echo json_encode($this->vars['userSetting']['grid_clients_list_hidecols']); else echo '[]'; ?>');
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
            
            <div class="btn-group">
              <a href="javascript:void(0);" class="btn btn-warning" onclick="$('#grid-clients_list-filters').slideToggle()"><i class="icon-filter"></i> Фильтр <span class="badge badge-success" id="productsFilter_button_badge" style="display:none">0</span></a>
            </div>
            
          </div>
          
          
          <div class="span4">
            <div class="dataTables_filter" id="sample_1_filter">
              <input type="text" aria-controls="sample_1" class="input-medium">
              <a href="javascript:void(0)" class="btn" onclick="grid.clients_list.addFilter('search', $(this).parent().find('input').val()).start()"><i class="icon icon-search"></i>Искать</a>
            </div>
          </div>
        </div>   
        
    
        
        <div class="row-fluid grid-hidden-filters" id="grid-clients_list-filters">
          <div class="span12" style="max-width:600px;">
            <div class="well">
              <h4 style="margin-top: -13px; margin-left: -11px;">Фильтр</h4>
              <form class="form-horizontal" style="margin-bottom:0px;">
                 <div class="control-group">
                  <label class="control-label" style="width:120px;">Источник клиента</label>
                  <div class="controls" style="margin-left:120px;">
                      <select data-placeholder="Выберите элементы" name="clietns_filter_source"  class="chosen-with-diselect span12">
                          <option value="0"></option>
                        <?php if(count($this->vars['clientSources'])) : 
							   		 $this->vars['foreach']['clientSources']['count'] = count($this->vars['clientSources']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['clientSources']);
							   		 foreach($this->vars['clientSources'] as $this->vars['foreach']['clientSources']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['clientSources']['key']; ?>
                        	<option value="<?php if(isset($this->vars['item']['source_id'])) echo $this->vars['item']['source_id']; ?>"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                        <?php endforeach; ?><?php endif; ?>
                       </select>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" style="width:120px;">Спец. фильтр</label>
                  <div class="controls" style="margin-left:120px;" >
                      <select data-placeholder="Выберите элементы" name="clietns_filter_special" data-connect-listen="templatesFilter-site"  class="chosen-with-diselect span12">
                          <option value="0"></option>
                        	<option value="1">Делал покупки в магазине</option>
                        	<option value="2">Подписан на рассылку</option>
                        	<option value="3">Заказал обратный звонок</option>
                        	<option value="4">Добавлен менеджером</option>
                        	<option value="5">Еще не оплатил заказ</option>
                       </select>
                  </div>
                </div>
               
                <a class="btn btn-success" onclick="admin_shop_clients.appendFilter();"><i class="icon-ok icon-white"></i> Применить фильтр</a>
                <a class="btn btn-danger" onclick="admin_shop_clients.clearFilter();"><i class="icon-remove icon-white"></i> Очистить фильтр</a>
              </form>              
            </div>
          </div>
        </div>
        
        
        <?php
if(!isset($this->vars['globalgrid'])) $this->vars['globalgrid'] = array();
$this->vars['globalgrid']['clients_list'] = json_decode('{"params":{"name":"clients_list","tpl":"grid.default.html","class":"admin_shop","method":"getClientsList"},"settings":{"cols":[{"name":"id","title":"id","search":true,"sort":true,"hidden":true},{"name":"name","title":"Имя клиента","search":true,"sort":true,"from_tpl":"orders_list_client"},{"name":"date","title":"Доабвления в базу","search":true,"sort":true,"sorted":false},{"name":"phone","title":"Телефон","search":true,"sort":true},{"name":"email","title":"email","search":true,"sort":true},{"name":"source_name","title":"Источник","search":true,"sort":true},{"name":"resp_user","title":"Менеджер","search":true,"sort":true,"hidden":true},{"name":"orders","title":"Всего сделал заказов","search":true,"sort":true},{"name":"orders_sum","title":"Сумма заказов","search":true,"sort":true,"hidden":true},{"name":"comment","title":"Коментарий","search":true,"sort":true,"hidden":true}],"user_settings":true,"autoload":false,"view":"list","limit":10,"click":"$page.go(\'\/show\/{id}\/\')"},"instance":{"name":"clients_list","tpl":"grid.default.html","class":"admin_shop","method":"getClientsList"}}', true);
$this->vars['instance'] = 'clients_list';
$this->vars['grid'] = json_decode('{"params":{"name":"clients_list","tpl":"grid.default.html","class":"admin_shop","method":"getClientsList"},"settings":{"cols":[{"name":"id","title":"id","search":true,"sort":true,"hidden":true},{"name":"name","title":"Имя клиента","search":true,"sort":true,"from_tpl":"orders_list_client"},{"name":"date","title":"Доабвления в базу","search":true,"sort":true,"sorted":false},{"name":"phone","title":"Телефон","search":true,"sort":true},{"name":"email","title":"email","search":true,"sort":true},{"name":"source_name","title":"Источник","search":true,"sort":true},{"name":"resp_user","title":"Менеджер","search":true,"sort":true,"hidden":true},{"name":"orders","title":"Всего сделал заказов","search":true,"sort":true},{"name":"orders_sum","title":"Сумма заказов","search":true,"sort":true,"hidden":true},{"name":"comment","title":"Коментарий","search":true,"sort":true,"hidden":true}],"user_settings":true,"autoload":false,"view":"list","limit":10,"click":"$page.go(\'\/show\/{id}\/\')"},"instance":{"name":"clients_list","tpl":"grid.default.html","class":"admin_shop","method":"getClientsList"}}', true);
global $core;
$grid_tpl_site = ($core->is_admin())? $core->getAdminModule() : $core->site_name;
echo $this->get('grid.default.html',$grid_tpl_site);

unset($this->vars['grid'], $this->vars['instance']);
?>

<script type='text/javascript'>
 //try { 
   grid.clients_list = new GridInstance('clients_list'); 
   grid.clients_list.params = JSON.parse('{"name":"clients_list","tpl":"grid.default.html","class":"admin_shop","method":"getClientsList"}'); 
   grid.clients_list.settings = JSON.parse('{"cols":[{"name":"id","title":"id","search":true,"sort":true,"hidden":true},{"name":"name","title":"Имя клиента","search":true,"sort":true,"from_tpl":"orders_list_client"},{"name":"date","title":"Доабвления в базу","search":true,"sort":true,"sorted":false},{"name":"phone","title":"Телефон","search":true,"sort":true},{"name":"email","title":"email","search":true,"sort":true},{"name":"source_name","title":"Источник","search":true,"sort":true},{"name":"resp_user","title":"Менеджер","search":true,"sort":true,"hidden":true},{"name":"orders","title":"Всего сделал заказов","search":true,"sort":true},{"name":"orders_sum","title":"Сумма заказов","search":true,"sort":true,"hidden":true},{"name":"comment","title":"Коментарий","search":true,"sort":true,"hidden":true}],"user_settings":true,"autoload":false,"view":"list","limit":10,"click":"$page.go(\'\/show\/{id}\/\')"}'); 
   grid.clients_list.init(); 
 //} catch(err) { 
 //  alert('Error create grid instance. '+err.message); 
 //} 
</script>
   
        
        

        <div id="orders_list_actions" style="display:none">
            <div class="div-edit-btn"><a href="#/edit/{id}/" class=" mini purple"><i class="icon-edit"></i> Редактировать</a></div>
            <div class="div-delete-btn"><a href="javascript:void(0)" onclick="admin_templates.deleteItem({id})" class="mini black"><i class="icon-trash"></i>&nbsp;Удалить</a></div>
          </div>
     
          <div id="orders_list_client" style="display:none">
            {surname} {name} {lastname}
        </div>
       </div>
     </div>
  </div>
</div>


