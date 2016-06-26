<? $this->md5_compil='1b8c107ab11c2a2051f25a7a0d34e65f'; ?>
<div class="row-fluid">
  <div class="span12" id="contactlist">
    <div class="widget">
      <div class="widget-title">
        <h4><i class="icon-reorder"></i>Клиенты</h4>
        <div class="actions">
          <div class="btn-group">
              <button data-toggle="dropdown" class="btn dropdown-toggle">Настройки таблицы <span class="caret"></span></button>
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
              <a href="javascript:void(0)" onclick="$page.go('/add/')" data-toggle="dropdown" class="btn btn-success dropdown-toggle"><i class="icon-plus"></i> Добавить</a>
            </div>
            <div class="btn-group" style="display:none">
              <a href="javascript:void(0);" data-toggle="dropdown" class="btn btn-primary dropdown-toggle"><i class="icon-tags"></i> Действия</a>
              <span style="display:none" id="grid-clients_list-groupAction-automenu"></span><script type="text/javascript">
 try {
	var grid_clients_list_groupaction_data = JSON.parse('{"actions":[{"action":"divider"},{"action":"remove","title":"\u0423\u0434\u0430\u043b\u0438\u0442\u044c","icon":"trash","onselect":true},{"action":"block","title":"\u0417\u0430\u0431\u043b\u043e\u043a\u0438\u0440\u043e\u0432\u0430\u0442\u044c","icon":"locked","onselect":true}]}');
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
$this->vars['globalgrid']['clients_list'] = json_decode('{"params":{"name":"clients_list","tpl":"grid.default.html","class":"admin_clients","method":"getClientList"},"settings":{"cols":[{"name":"id","title":"№","search":true,"sort":true},{"name":"label","title":"Метка","search":true,"sort":true},{"name":"name_first","title":"Администратор","search":true,"sort":false,"from_tpl":"clients_list_cname"},{"name":"reg_date","title":"Создан","search":false,"sort":true},{"name":"count_users","title":"Статистика","search":false,"sort":true,"from_tpl":"clients_list_stat"},{"name":"is_free","title":"Параметры","search":false,"sort":true,"from_tpl":"clients_list_free"},{"name":"actions","title":"Действия","search":false,"sort":false,"from_tpl":"clients_list_actions"}],"user_settings":true,"group_actions":true,"autoload":true,"view":"list","limit":10},"instance":{"name":"clients_list","tpl":"grid.default.html","class":"admin_clients","method":"getClientList"}}', true);
$this->vars['instance'] = 'clients_list';
$this->vars['grid'] = json_decode('{"params":{"name":"clients_list","tpl":"grid.default.html","class":"admin_clients","method":"getClientList"},"settings":{"cols":[{"name":"id","title":"№","search":true,"sort":true},{"name":"label","title":"Метка","search":true,"sort":true},{"name":"name_first","title":"Администратор","search":true,"sort":false,"from_tpl":"clients_list_cname"},{"name":"reg_date","title":"Создан","search":false,"sort":true},{"name":"count_users","title":"Статистика","search":false,"sort":true,"from_tpl":"clients_list_stat"},{"name":"is_free","title":"Параметры","search":false,"sort":true,"from_tpl":"clients_list_free"},{"name":"actions","title":"Действия","search":false,"sort":false,"from_tpl":"clients_list_actions"}],"user_settings":true,"group_actions":true,"autoload":true,"view":"list","limit":10},"instance":{"name":"clients_list","tpl":"grid.default.html","class":"admin_clients","method":"getClientList"}}', true);
global $core;
$grid_tpl_site = ($core->is_admin())? $core->getAdminModule() : $core->site_name;
echo $this->get('grid.default.html',$grid_tpl_site);

unset($this->vars['grid'], $this->vars['instance']);
?>

<script type='text/javascript'>
 //try { 
   grid.clients_list = new GridInstance('clients_list'); 
   grid.clients_list.params = JSON.parse('{"name":"clients_list","tpl":"grid.default.html","class":"admin_clients","method":"getClientList"}'); 
   grid.clients_list.settings = JSON.parse('{"cols":[{"name":"id","title":"№","search":true,"sort":true},{"name":"label","title":"Метка","search":true,"sort":true},{"name":"name_first","title":"Администратор","search":true,"sort":false,"from_tpl":"clients_list_cname"},{"name":"reg_date","title":"Создан","search":false,"sort":true},{"name":"count_users","title":"Статистика","search":false,"sort":true,"from_tpl":"clients_list_stat"},{"name":"is_free","title":"Параметры","search":false,"sort":true,"from_tpl":"clients_list_free"},{"name":"actions","title":"Действия","search":false,"sort":false,"from_tpl":"clients_list_actions"}],"user_settings":true,"group_actions":true,"autoload":true,"view":"list","limit":10}'); 
   grid.clients_list.init(); 
 //} catch(err) { 
 //  alert('Error create grid instance. '+err.message); 
 //} 
</script>
   
        
        
        <div id="clients_list_cname" style="display:none">
          <a href="#/show/{id}/"><b>{name_last} {name_first}</b></a><br>
          логин: {login}<br>
          когда заходил: {last_auth_date}
        </div>
        
        <div id="clients_list_free" style="display:none">
          <span class="label label-{enable_label}">{enable_name}</span>
          <span class="label label-{free_label}">{free_name}</span>
        </div>
        
        <div id="clients_list_stat" style="display:none">
          <nobr><b>Пользователей:</b> <span class="badge">{count_users}</span></nobr><br>
          <nobr><b>Магазинов:</b> <span class="badge badge-{shops_count_label}">{count_shops}</span></nobr>
        </div>
        
        
        <div id="clients_list_actions" style="display:none">
            <div class="div-edit-btn"><a href="#/edit/{id}/" class=" mini purple"><i class="icon-edit"></i> Редактировать</a></div>
            <div class="div-delete-btn"><a href="javascript:void(0)" onclick="site_clients.delClient({id})" class="mini black"><i class="icon-trash"></i>&nbsp;Удалить</a></div>
          </div>
     
       </div>
     </div>
  </div>
</div>


