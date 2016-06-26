<? $this->md5_compil='3b811ad71534ba16ebac94e0fcc0c439'; ?>
<div class="row-fluid">
  <div class="span12" id="contactlist">
    <div class="widget">
      <div class="widget-title">
        <h4><i class="icon-reorder"></i>Уведомления</h4>
        <div class="actions">
          <div class="btn-group">
              <button data-toggle="dropdown" class="btn dropdown-toggle"><span class="caret"></span></button>
                  	<script type="text/template" id="grid-notice_list-settings-MenuTemplate">
<ul class="dropdown-menu pull-right">
                        {item}<li name="{name}" {hidden}><a href="javascript:;"><i></i> {title}</a></li>{/item}
                      </ul>	</script>
<script type="text/javascript">
 try {
	var grid_notice_list_settings_menu_user_data = JSON.parse('<?php if(isset($this->vars['userSetting']['grid_notice_list_hidecols'])) echo json_encode($this->vars['userSetting']['grid_notice_list_hidecols']); else echo '[]'; ?>');
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
$this->vars['globalgrid']['notice_list'] = json_decode('{"params":{"name":"notice_list","tpl":"grid.default.html","class":"notifications_admin","method":"getNotificationsList"},"settings":{"cols":[{"name":"notice_id","title":"ID","search":true,"sort":true,"hidden":true},{"name":"title","title":"Название","search":true,"sort":true},{"name":"event_name","title":"Событие","search":true,"sort":true},{"name":"action_full_alias","title":"Event map","search":false,"sort":false,"hidden":true,"from_tpl":"notice_list_alias"},{"name":"action_name","title":"Действие","search":false,"sort":true},{"name":"enabled","title":"Активность","search":false,"sort":true,"from_tpl":"notice_list_status"},{"name":"creator_name","title":"Кто создал","search":false,"sort":true,"hidden":true},{"name":"create_date","title":"Когда создано","search":false,"sort":true,"hidden":true},{"name":"actions","title":"Действия","search":false,"sort":false,"from_tpl":"notice_list_actions"}],"user_settings":true,"group_actions":false,"autoload":true,"view":"list","limit":20},"instance":{"name":"notice_list","tpl":"grid.default.html","class":"notifications_admin","method":"getNotificationsList"}}', true);
$this->vars['instance'] = 'notice_list';
$this->vars['grid'] = json_decode('{"params":{"name":"notice_list","tpl":"grid.default.html","class":"notifications_admin","method":"getNotificationsList"},"settings":{"cols":[{"name":"notice_id","title":"ID","search":true,"sort":true,"hidden":true},{"name":"title","title":"Название","search":true,"sort":true},{"name":"event_name","title":"Событие","search":true,"sort":true},{"name":"action_full_alias","title":"Event map","search":false,"sort":false,"hidden":true,"from_tpl":"notice_list_alias"},{"name":"action_name","title":"Действие","search":false,"sort":true},{"name":"enabled","title":"Активность","search":false,"sort":true,"from_tpl":"notice_list_status"},{"name":"creator_name","title":"Кто создал","search":false,"sort":true,"hidden":true},{"name":"create_date","title":"Когда создано","search":false,"sort":true,"hidden":true},{"name":"actions","title":"Действия","search":false,"sort":false,"from_tpl":"notice_list_actions"}],"user_settings":true,"group_actions":false,"autoload":true,"view":"list","limit":20},"instance":{"name":"notice_list","tpl":"grid.default.html","class":"notifications_admin","method":"getNotificationsList"}}', true);
global $core;
$grid_tpl_site = ($core->is_admin())? $core->getAdminModule() : $core->site_name;
echo $this->get('grid.default.html',$grid_tpl_site);

unset($this->vars['grid'], $this->vars['instance']);
?>

<script type='text/javascript'>
 //try { 
   grid.notice_list = new GridInstance('notice_list'); 
   grid.notice_list.params = JSON.parse('{"name":"notice_list","tpl":"grid.default.html","class":"notifications_admin","method":"getNotificationsList"}'); 
   grid.notice_list.settings = JSON.parse('{"cols":[{"name":"notice_id","title":"ID","search":true,"sort":true,"hidden":true},{"name":"title","title":"Название","search":true,"sort":true},{"name":"event_name","title":"Событие","search":true,"sort":true},{"name":"action_full_alias","title":"Event map","search":false,"sort":false,"hidden":true,"from_tpl":"notice_list_alias"},{"name":"action_name","title":"Действие","search":false,"sort":true},{"name":"enabled","title":"Активность","search":false,"sort":true,"from_tpl":"notice_list_status"},{"name":"creator_name","title":"Кто создал","search":false,"sort":true,"hidden":true},{"name":"create_date","title":"Когда создано","search":false,"sort":true,"hidden":true},{"name":"actions","title":"Действия","search":false,"sort":false,"from_tpl":"notice_list_actions"}],"user_settings":true,"group_actions":false,"autoload":true,"view":"list","limit":20}'); 
   grid.notice_list.init(); 
 //} catch(err) { 
 //  alert('Error create grid instance. '+err.message); 
 //} 
</script>
   
        
        
        <div id="notice_list_alias" style="display:none">
        	{object_group}: [{object_alias}.{event_alias}]
        </div>

        <div id="notice_list_status" style="display:none">
          <span class="label label-{enabled_mark}">{enabled_name}</span><br>
        </div>

        <div id="notice_list_actions" style="display:none">
          	<div class="div-edit-btn"><a href="javascript:void(0)" class=" mini purple"><i class="icon-minus-sign"></i> Остановить</a></div>
            <div class="div-edit-btn"><a href="#/edit/{notice_id}/" class=" mini purple"><i class="icon-cog"></i> Настроить</a></div>
            <div class="div-delete-btn"><a href="javascript:void(0)" onclick="site_notice.deleteNotice({notice_id})" class="mini black"><i class="icon-trash"></i>&nbsp;Удалить</a></div>
          </div>
     
       </div>
     </div>
  </div>
</div>


