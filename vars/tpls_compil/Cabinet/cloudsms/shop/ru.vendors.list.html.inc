<? $this->md5_compil='625baebbafc415ecc3fced04f1952bb3'; ?>
<div class="row-fluid">
  <div class="span12" id="contactlist">
    <div class="widget">
      <div class="widget-title">
        <h4><i class="icon-reorder"></i>Производители</h4>
        <div class="actions">
          <div class="btn-group">
              <button data-toggle="dropdown" class="btn dropdown-toggle">Настройки таблицы <span class="caret"></span></button>
                  	<script type="text/template" id="grid-vendors_list-settings-MenuTemplate">
<ul class="dropdown-menu pull-right">
                        {item}<li name="{name}" {hidden}><a href="javascript:;"><i></i> {title}</a></li>{/item}
                      </ul>	</script>
<script type="text/javascript">
 try {
	var grid_vendors_list_settings_menu_user_data = JSON.parse('<?php if(isset($this->vars['userSetting']['grid_vendors_list_hidecols'])) echo json_encode($this->vars['userSetting']['grid_vendors_list_hidecols']); else echo '[]'; ?>');
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
              <a href="javascript:void(0)" onclick="$page.go('/add/')" data-toggle="dropdown" class="btn btn-success dropdown-toggle"><i class="icon-plus"></i> Добавить</a>
            </div>
            <div class="btn-group" style="display:none">
              <a href="javascript:void(0);" data-toggle="dropdown" class="btn btn-primary dropdown-toggle"><i class="icon-tags"></i> Действия</a>
              <span style="display:none" id="grid-vendors_list-groupAction-automenu"></span><script type="text/javascript">
 try {
	var grid_vendors_list_groupaction_data = JSON.parse('{"actions":[{"action":"divider"},{"action":"remove","title":"\u0423\u0434\u0430\u043b\u0438\u0442\u044c","icon":"trash","onselect":true},{"action":"copy","title":"\u041a\u043e\u043f\u0438\u0440\u043e\u0432\u0430\u0442\u044c","icon":"copy","onselect":true},{"action":"paste","title":"\u0412\u0441\u0442\u0430\u0432\u0438\u0442\u044c","icon":"paste","onboofer":true}]}');
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
$this->vars['globalgrid']['vendors_list'] = json_decode('{"params":{"name":"vendors_list","tpl":"grid.default.html","class":"admin_shop","method":"getVendorsList"},"settings":{"cols":[{"name":"id","title":"№","search":true,"sort":true,"hidden":false},{"name":"name","title":"Название","search":true,"sort":true},{"name":"prod_count","title":"Товаров","search":true,"sort":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"orders_list_actions"}],"user_settings":true,"group_actions":false,"autoload":false,"view":"list","limit":10},"instance":{"name":"vendors_list","tpl":"grid.default.html","class":"admin_shop","method":"getVendorsList"}}', true);
$this->vars['instance'] = 'vendors_list';
$this->vars['grid'] = json_decode('{"params":{"name":"vendors_list","tpl":"grid.default.html","class":"admin_shop","method":"getVendorsList"},"settings":{"cols":[{"name":"id","title":"№","search":true,"sort":true,"hidden":false},{"name":"name","title":"Название","search":true,"sort":true},{"name":"prod_count","title":"Товаров","search":true,"sort":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"orders_list_actions"}],"user_settings":true,"group_actions":false,"autoload":false,"view":"list","limit":10},"instance":{"name":"vendors_list","tpl":"grid.default.html","class":"admin_shop","method":"getVendorsList"}}', true);
global $core;
$grid_tpl_site = ($core->is_admin())? $core->getAdminModule() : $core->site_name;
echo $this->get('grid.default.html',$grid_tpl_site);

unset($this->vars['grid'], $this->vars['instance']);
?>

<script type='text/javascript'>
 //try { 
   grid.vendors_list = new GridInstance('vendors_list'); 
   grid.vendors_list.params = JSON.parse('{"name":"vendors_list","tpl":"grid.default.html","class":"admin_shop","method":"getVendorsList"}'); 
   grid.vendors_list.settings = JSON.parse('{"cols":[{"name":"id","title":"№","search":true,"sort":true,"hidden":false},{"name":"name","title":"Название","search":true,"sort":true},{"name":"prod_count","title":"Товаров","search":true,"sort":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"orders_list_actions"}],"user_settings":true,"group_actions":false,"autoload":false,"view":"list","limit":10}'); 
   grid.vendors_list.init(); 
 //} catch(err) { 
 //  alert('Error create grid instance. '+err.message); 
 //} 
</script>
   
        
        

        <div id="orders_list_actions" style="display:none">
            <div class="div-edit-btn"><a href="#/edit/{id}/" class=" mini purple"><i class="icon-edit"></i> Редактировать</a></div>
            <div class="div-delete-btn"><a href="javascript:void(0)" onclick="admin_vendors.del({id})" class="mini black"><i class="icon-trash"></i>&nbsp;Удалить</a></div>
          </div>
     
       </div>
     </div>
  </div>
</div>


