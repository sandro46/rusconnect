<? $this->md5_compil='5317aa68ee85060031da12be137e69d3'; ?>
<div class="row-fluid">
  <div class="span12" id="contactlist">
    <div class="widget">
      <div class="widget-title">
        <h4><i class="icon-reorder"></i>Список складов</h4>
        <div class="actions">
          <div class="btn-group">
              <button data-toggle="dropdown" class="btn dropdown-toggle">Настройки таблицы <span class="caret"></span></button>
                  	<script type="text/template" id="grid-stores_list-settings-MenuTemplate">
<ul class="dropdown-menu pull-right">
                        {item}<li name="{name}" {hidden}><a href="javascript:;"><i></i> {title}</a></li>{/item}
                      </ul>	</script>
<script type="text/javascript">
 try {
	var grid_stores_list_settings_menu_user_data = JSON.parse('<?php if(isset($this->vars['userSetting']['grid_stores_list_hidecols'])) echo json_encode($this->vars['userSetting']['grid_stores_list_hidecols']); else echo '[]'; ?>');
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
            <a href="javascript:void(0);" class="btn btn-success"><i class="icon-plus"></i> Добавить новый склад</a>
            
            <div class="btn-group">
              <a href="javascript:void(0);" data-toggle="dropdown" class="btn btn-primary dropdown-toggle"><i class="icon-tags"></i> Действия</a>
              <span style="display:none" id="grid-stores_list-groupAction-automenu"></span><script type="text/javascript">
 try {
	var grid_stores_list_groupaction_data = JSON.parse('{"actions":[{"action":"hide","title":"\u041f\u0440\u0438\u043e\u0441\u0442\u0430\u043d\u043e\u0432\u0438\u0442\u044c \u0441\u043a\u043b\u0430\u0434","onselect":true,"icon":" icon-pause"},{"action":"divider"},{"action":"remove","title":"\u0423\u0434\u0430\u043b\u0438\u0442\u044c","icon":"trash","onselect":true},{"action":"copy","title":"\u041a\u043e\u043f\u0438\u0440\u043e\u0432\u0430\u0442\u044c","icon":"copy","onselect":true},{"action":"paste","title":"\u0412\u0441\u0442\u0430\u0432\u0438\u0442\u044c","icon":"paste","onboofer":true}]}');
 }
 catch(e) {
 	console.log('error grid group action settings! -> '+ e.message);
 }
</script>

            </div>
        
            

          </div>
            <div class="span4">
            	<div class="dataTables_filter" id="sample_1_filter"><label>Поиск: <input type="text" aria-controls="sample_1" class="input-medium"></label></div>
          	</div>
       


        </div>   
        
    
        
        
        
        <?php
if(!isset($this->vars['globalgrid'])) $this->vars['globalgrid'] = array();
$this->vars['globalgrid']['stores_list'] = json_decode('{"params":{"name":"stores_list","tpl":"grid.catalog.html","class":"admin_shop","method":"getStoresList"},"settings":{"cols":[{"name":"id","title":"id","search":true,"sort":true,"hidden":true},{"name":"index","title":"Индекс","search":true,"sort":true,"hidden":true},{"name":"name","title":"Название","search":true,"sort":true},{"name":"store_address","title":"Адрес","search":false,"sort":true,"from_tpl":"stores_list_address"},{"name":"status_name","title":"Статус","search":true,"sort":true,"from_tpl":"stores_list_status"},{"name":"full_quantity","title":"Всего товаров","search":true,"sort":true},{"name":"uniq_quantity","title":"Всего наименований","search":true,"sort":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"stores_list_actions"}],"user_settings":true,"group_actions":true,"autoload":false,"view":"list","limit":10},"instance":{"name":"stores_list","tpl":"grid.catalog.html","class":"admin_shop","method":"getStoresList"}}', true);
$this->vars['instance'] = 'stores_list';
$this->vars['grid'] = json_decode('{"params":{"name":"stores_list","tpl":"grid.catalog.html","class":"admin_shop","method":"getStoresList"},"settings":{"cols":[{"name":"id","title":"id","search":true,"sort":true,"hidden":true},{"name":"index","title":"Индекс","search":true,"sort":true,"hidden":true},{"name":"name","title":"Название","search":true,"sort":true},{"name":"store_address","title":"Адрес","search":false,"sort":true,"from_tpl":"stores_list_address"},{"name":"status_name","title":"Статус","search":true,"sort":true,"from_tpl":"stores_list_status"},{"name":"full_quantity","title":"Всего товаров","search":true,"sort":true},{"name":"uniq_quantity","title":"Всего наименований","search":true,"sort":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"stores_list_actions"}],"user_settings":true,"group_actions":true,"autoload":false,"view":"list","limit":10},"instance":{"name":"stores_list","tpl":"grid.catalog.html","class":"admin_shop","method":"getStoresList"}}', true);
global $core;
$grid_tpl_site = ($core->is_admin())? $core->getAdminModule() : $core->site_name;
echo $this->get('grid.catalog.html',$grid_tpl_site);

unset($this->vars['grid'], $this->vars['instance']);
?>

<script type='text/javascript'>
 //try { 
   grid.stores_list = new GridInstance('stores_list'); 
   grid.stores_list.params = JSON.parse('{"name":"stores_list","tpl":"grid.catalog.html","class":"admin_shop","method":"getStoresList"}'); 
   grid.stores_list.settings = JSON.parse('{"cols":[{"name":"id","title":"id","search":true,"sort":true,"hidden":true},{"name":"index","title":"Индекс","search":true,"sort":true,"hidden":true},{"name":"name","title":"Название","search":true,"sort":true},{"name":"store_address","title":"Адрес","search":false,"sort":true,"from_tpl":"stores_list_address"},{"name":"status_name","title":"Статус","search":true,"sort":true,"from_tpl":"stores_list_status"},{"name":"full_quantity","title":"Всего товаров","search":true,"sort":true},{"name":"uniq_quantity","title":"Всего наименований","search":true,"sort":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"stores_list_actions"}],"user_settings":true,"group_actions":true,"autoload":false,"view":"list","limit":10}'); 
   grid.stores_list.init(); 
 //} catch(err) { 
 //  alert('Error create grid instance. '+err.message); 
 //} 
</script>
   

          <div id="stores_list_status" style="display:none"><span class="store_work_status_{status_id}">{status_name}</span></div>
          <div id="stores_list_address" style="display:none"><a href="#{store_address}">{store_address}</a></div>
        
          <div id="stores_list_actions" style="display:none">
            <div class="div-edit-btn"><a href="#/edit/{id}/" class=" mini purple"><i class="icon-edit"></i> Редактировать</a></div>
            <div class="div-delete-btn"><a href="javascript:void(0)" onclick="admin_templates.deleteItem({id})" class="mini black"><i class="icon-trash"></i>&nbsp;Удалить</a></div>
            <!--<div class="div-delete-btn"><a href="#/history/show/{id}/" class="mini black"><i class="icon-trash"></i>&nbsp;История покупок</a></div>-->
          </div>

       </div>
     </div>
  </div>
</div>


