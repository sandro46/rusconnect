<? $this->md5_compil='fa1ad1c7ced0b10bd0998ce22c6ef2a7'; ?>
<div class="row-fluid">
  <div class="span12" id="contactlist">
    <div class="widget">
      <div class="widget-title">
        <h4><i class="icon-reorder"></i>Все пользователи</h4>
        <div class="actions">
          <div class="btn-group">
              <button data-toggle="dropdown" class="btn dropdown-toggle"><span class="caret"></span></button>
                  	<script type="text/template" id="grid-users_list-settings-MenuTemplate">
<ul class="dropdown-menu pull-right">
                        {item}<li name="{name}" {hidden}><a href="javascript:;"><i></i> {title}</a></li>{/item}
                      </ul>	</script>
<script type="text/javascript">
 try {
	var grid_users_list_settings_menu_user_data = JSON.parse('<?php if(isset($this->vars['userSetting']['grid_users_list_hidecols'])) echo json_encode($this->vars['userSetting']['grid_users_list_hidecols']); else echo '[]'; ?>');
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
          </div>
          
          
          <div class="span4">
            <div class="dataTables_filter" id="sample_1_filter"><label>Поиск: <input type="text" aria-controls="sample_1" class="input-medium"></label></div>
          </div>
        </div>   
        
    
        
        
        
        <?php
if(!isset($this->vars['globalgrid'])) $this->vars['globalgrid'] = array();
$this->vars['globalgrid']['users_list'] = json_decode('{"params":{"name":"users_list","tpl":"grid.default.html","class":"admin_users","method":"getUsersList"},"settings":{"cols":[{"name":"user_id","title":"№","search":true,"sort":true},{"name":"avatar","title":"Аватар","search":true,"sort":true,"from_tpl":"users_list_avatar"},{"name":"name_first","title":"Фио","search":true,"sort":false,"from_tpl":"users_list_name"},{"name":"reg_date","title":"Создан","search":false,"sort":true},{"name":"phone","title":"Контактные данные","search":false,"sort":true,"from_tpl":"users_list_contact"},{"name":"enabled","title":"Статус","search":false,"sort":true,"from_tpl":"users_list_status"},{"name":"actions","title":"Действия","search":false,"sort":false,"from_tpl":"users_list_actions"}],"user_settings":true,"group_actions":false,"autoload":true,"view":"list","limit":10},"instance":{"name":"users_list","tpl":"grid.default.html","class":"admin_users","method":"getUsersList"}}', true);
$this->vars['instance'] = 'users_list';
$this->vars['grid'] = json_decode('{"params":{"name":"users_list","tpl":"grid.default.html","class":"admin_users","method":"getUsersList"},"settings":{"cols":[{"name":"user_id","title":"№","search":true,"sort":true},{"name":"avatar","title":"Аватар","search":true,"sort":true,"from_tpl":"users_list_avatar"},{"name":"name_first","title":"Фио","search":true,"sort":false,"from_tpl":"users_list_name"},{"name":"reg_date","title":"Создан","search":false,"sort":true},{"name":"phone","title":"Контактные данные","search":false,"sort":true,"from_tpl":"users_list_contact"},{"name":"enabled","title":"Статус","search":false,"sort":true,"from_tpl":"users_list_status"},{"name":"actions","title":"Действия","search":false,"sort":false,"from_tpl":"users_list_actions"}],"user_settings":true,"group_actions":false,"autoload":true,"view":"list","limit":10},"instance":{"name":"users_list","tpl":"grid.default.html","class":"admin_users","method":"getUsersList"}}', true);
global $core;
$grid_tpl_site = ($core->is_admin())? $core->getAdminModule() : $core->site_name;
echo $this->get('grid.default.html',$grid_tpl_site);

unset($this->vars['grid'], $this->vars['instance']);
?>

<script type='text/javascript'>
 //try { 
   grid.users_list = new GridInstance('users_list'); 
   grid.users_list.params = JSON.parse('{"name":"users_list","tpl":"grid.default.html","class":"admin_users","method":"getUsersList"}'); 
   grid.users_list.settings = JSON.parse('{"cols":[{"name":"user_id","title":"№","search":true,"sort":true},{"name":"avatar","title":"Аватар","search":true,"sort":true,"from_tpl":"users_list_avatar"},{"name":"name_first","title":"Фио","search":true,"sort":false,"from_tpl":"users_list_name"},{"name":"reg_date","title":"Создан","search":false,"sort":true},{"name":"phone","title":"Контактные данные","search":false,"sort":true,"from_tpl":"users_list_contact"},{"name":"enabled","title":"Статус","search":false,"sort":true,"from_tpl":"users_list_status"},{"name":"actions","title":"Действия","search":false,"sort":false,"from_tpl":"users_list_actions"}],"user_settings":true,"group_actions":false,"autoload":true,"view":"list","limit":10}'); 
   grid.users_list.init(); 
 //} catch(err) { 
 //  alert('Error create grid instance. '+err.message); 
 //} 
</script>
   
        
        
        <div id="users_list_avatar" style="display:none">
        	<img src="{avatar_small || <?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>img/profile-pic.jpg}" style="width:100px;">
        </div>
        
        <div id="users_list_name" style="display:none">
          <a href="#/show/{user_id}/"><b>{name_last} {name_first} {name_second}</b></a><br>
          логин: {login}<br>
          когда заходил: {last_auth}
        </div>
        
        <div id="users_list_contact" style="display:none">
          <div><i class="icon-phone"></i> {phone}</div>
          <div><i class="icon-envelope"></i> {email}</div>
        </div>
        
        
        
        
        <div id="users_list_status" style="display:none">
          <span class="label label-{enable_label}">{enable_name}</span><br>
          {block_reason}
        </div>

        <div id="users_list_actions" style="display:none">
            <div class="div-edit-btn"><a href="#/edit/{user_id}/" class=" mini purple"><i class="icon-edit"></i> Редактировать</a></div>
            <div class="div-delete-btn"><a href="javascript:void(0)" onclick="site_users.deleteUser({user_id})" class="mini black"><i class="icon-trash"></i>&nbsp;Удалить</a></div>
          </div>
     
       </div>
     </div>
  </div>
</div>


