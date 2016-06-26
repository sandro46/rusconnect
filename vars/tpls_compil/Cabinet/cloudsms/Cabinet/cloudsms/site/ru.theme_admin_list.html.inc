<? $this->md5_compil='79d5c70bf5bc8396abc86fa90955f4f0'; ?>
<div class="row-fluid">
  <div class="span12" id="contactlist">
    <div class="widget">
      <div class="widget-title">
        <h4><i class="icon-reorder"></i>Темы оформления</h4>
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
          </div>

        </div>   
        

        <?php
if(!isset($this->vars['globalgrid'])) $this->vars['globalgrid'] = array();
$this->vars['globalgrid']['themes_list'] = json_decode('{"params":{"name":"themes_list","tpl":"grid.default.html","class":"admin_site","method":"getThemesList"},"settings":{"cols":[{"name":"img_preview","title":"-","search":true,"sort":false,"from_tpl":"thems_img"},{"name":"name","title":"Системное имя","search":true,"sort":false},{"name":"title","title":"Название","search":true,"sort":false},{"name":"description","title":"Описание","search":false,"sort":false},{"name":"demo_url","title":"Демо магазин","search":false,"sort":false,"from_tpl":"thems_demo"},{"name":"is_free","title":"Параметры","search":false,"sort":false,"from_tpl":"thems_free"},{"name":"price","title":"Цена шаблона","search":false,"sort":false},{"name":"actions","title":"Действия","search":false,"sort":false,"from_tpl":"thems_actions"}],"user_settings":false,"group_actions":false,"autoload":true,"view":"list","limit":10},"instance":{"name":"themes_list","tpl":"grid.default.html","class":"admin_site","method":"getThemesList"}}', true);
$this->vars['instance'] = 'themes_list';
$this->vars['grid'] = json_decode('{"params":{"name":"themes_list","tpl":"grid.default.html","class":"admin_site","method":"getThemesList"},"settings":{"cols":[{"name":"img_preview","title":"-","search":true,"sort":false,"from_tpl":"thems_img"},{"name":"name","title":"Системное имя","search":true,"sort":false},{"name":"title","title":"Название","search":true,"sort":false},{"name":"description","title":"Описание","search":false,"sort":false},{"name":"demo_url","title":"Демо магазин","search":false,"sort":false,"from_tpl":"thems_demo"},{"name":"is_free","title":"Параметры","search":false,"sort":false,"from_tpl":"thems_free"},{"name":"price","title":"Цена шаблона","search":false,"sort":false},{"name":"actions","title":"Действия","search":false,"sort":false,"from_tpl":"thems_actions"}],"user_settings":false,"group_actions":false,"autoload":true,"view":"list","limit":10},"instance":{"name":"themes_list","tpl":"grid.default.html","class":"admin_site","method":"getThemesList"}}', true);
global $core;
$grid_tpl_site = ($core->is_admin())? $core->getAdminModule() : $core->site_name;
echo $this->get('grid.default.html',$grid_tpl_site);

unset($this->vars['grid'], $this->vars['instance']);
?>

<script type='text/javascript'>
 //try { 
   grid.themes_list = new GridInstance('themes_list'); 
   grid.themes_list.params = JSON.parse('{"name":"themes_list","tpl":"grid.default.html","class":"admin_site","method":"getThemesList"}'); 
   grid.themes_list.settings = JSON.parse('{"cols":[{"name":"img_preview","title":"-","search":true,"sort":false,"from_tpl":"thems_img"},{"name":"name","title":"Системное имя","search":true,"sort":false},{"name":"title","title":"Название","search":true,"sort":false},{"name":"description","title":"Описание","search":false,"sort":false},{"name":"demo_url","title":"Демо магазин","search":false,"sort":false,"from_tpl":"thems_demo"},{"name":"is_free","title":"Параметры","search":false,"sort":false,"from_tpl":"thems_free"},{"name":"price","title":"Цена шаблона","search":false,"sort":false},{"name":"actions","title":"Действия","search":false,"sort":false,"from_tpl":"thems_actions"}],"user_settings":false,"group_actions":false,"autoload":true,"view":"list","limit":10}'); 
   grid.themes_list.init(); 
 //} catch(err) { 
 //  alert('Error create grid instance. '+err.message); 
 //} 
</script>
   
        
        
        <div id="thems_img" style="display:none">
          <img src="{img_preview}" style="width:120px;">
        </div>
        
        <div id="thems_free" style="display:none">
          <span class="label label-{free_label}">{free_name}</span> <br>
          <span class="label label-{enable_label}" style="margin-top:10px;">{enable_name}</span>
        </div>
        
        
        
        <div id="thems_demo" style="display:none">
          <a href="http://{demo_url}" target="_blank">{demo_url}</a>
        </div>

        <div id="thems_actions" style="display:none">
            <div class="div-edit-btn"><a href="#/edit/{id}/" class=" mini purple"><i class="icon-edit"></i> Редактировать</a></div>
            <div class="div-delete-btn"><a href="javascript:void(0)" onclick="site_clients.delClient({id})" class="mini black"><i class="icon-trash"></i>&nbsp;Удалить</a></div>
          </div>
     
       </div>
     </div>
  </div>
</div>


