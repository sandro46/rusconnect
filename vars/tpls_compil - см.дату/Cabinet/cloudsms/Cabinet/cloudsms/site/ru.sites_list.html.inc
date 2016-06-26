<? $this->md5_compil='03f78282a13309f52ff44c1a176e4ed4'; ?>
<div class="row-fluid">
  <div class="span12" id="contactlist">
    <div class="widget">
      <div class="widget-title">
        <h4><i class="icon-reorder"></i>Сайты</h4>
        <div class="actions">
          <div class="btn-group">
              <button data-toggle="dropdown" class="btn dropdown-toggle">Настройки таблицы <span class="caret"></span></button>
                  	<script type="text/template" id="grid-sites_list-settings-MenuTemplate">
<ul class="dropdown-menu pull-right">
                        {item}<li name="{name}" {hidden}><a href="javascript:;"><i></i> {title}</a></li>{/item}
                      </ul>	</script>
<script type="text/javascript">
 try {
	var grid_sites_list_settings_menu_user_data = JSON.parse('<?php if(isset($this->vars['userSetting']['grid_sites_list_hidecols'])) echo json_encode($this->vars['userSetting']['grid_sites_list_hidecols']); else echo '[]'; ?>');
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
              <a href="#/add/" class="btn btn-success"><i class="icon-plus"></i> Создать сайт</a>
            </div>
          </div>
          
          
          <div class="span4">
            <div class="dataTables_filter" id="sample_1_filter"><label>Поиск: <input type="text" aria-controls="sample_1" class="input-medium"></label></div>
          </div>
        </div>   
        

        <?php
if(!isset($this->vars['globalgrid'])) $this->vars['globalgrid'] = array();
$this->vars['globalgrid']['sites_list'] = json_decode('{"params":{"name":"sites_list","tpl":"grid.default.html","class":"admin_site","method":"getSitesList"},"settings":{"cols":[{"name":"id","title":"№","search":true,"sort":true,"hidden":false},{"name":"name","title":"Название","search":true,"sort":true},{"name":"create_date","title":"Дата создания","search":true,"sort":true},{"name":"status_name","title":"Статус","search":true,"sort":true},{"name":"url","title":"Адрес","search":true,"sort":true},{"name":"prepaid_to","title":"Период работы","search":true,"sort":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"sites_list_actions"}],"user_settings":true,"autoload":true,"view":"list","limit":10},"instance":{"name":"sites_list","tpl":"grid.default.html","class":"admin_site","method":"getSitesList"}}', true);
$this->vars['instance'] = 'sites_list';
$this->vars['grid'] = json_decode('{"params":{"name":"sites_list","tpl":"grid.default.html","class":"admin_site","method":"getSitesList"},"settings":{"cols":[{"name":"id","title":"№","search":true,"sort":true,"hidden":false},{"name":"name","title":"Название","search":true,"sort":true},{"name":"create_date","title":"Дата создания","search":true,"sort":true},{"name":"status_name","title":"Статус","search":true,"sort":true},{"name":"url","title":"Адрес","search":true,"sort":true},{"name":"prepaid_to","title":"Период работы","search":true,"sort":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"sites_list_actions"}],"user_settings":true,"autoload":true,"view":"list","limit":10},"instance":{"name":"sites_list","tpl":"grid.default.html","class":"admin_site","method":"getSitesList"}}', true);
global $core;
$grid_tpl_site = ($core->is_admin())? $core->getAdminModule() : $core->site_name;
echo $this->get('grid.default.html',$grid_tpl_site);

unset($this->vars['grid'], $this->vars['instance']);
?>

<script type='text/javascript'>
 //try { 
   grid.sites_list = new GridInstance('sites_list'); 
   grid.sites_list.params = JSON.parse('{"name":"sites_list","tpl":"grid.default.html","class":"admin_site","method":"getSitesList"}'); 
   grid.sites_list.settings = JSON.parse('{"cols":[{"name":"id","title":"№","search":true,"sort":true,"hidden":false},{"name":"name","title":"Название","search":true,"sort":true},{"name":"create_date","title":"Дата создания","search":true,"sort":true},{"name":"status_name","title":"Статус","search":true,"sort":true},{"name":"url","title":"Адрес","search":true,"sort":true},{"name":"prepaid_to","title":"Период работы","search":true,"sort":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"sites_list_actions"}],"user_settings":true,"autoload":true,"view":"list","limit":10}'); 
   grid.sites_list.init(); 
 //} catch(err) { 
 //  alert('Error create grid instance. '+err.message); 
 //} 
</script>
   
        
        

        <div id="sites_list_actions" style="display:none">
            <div class="div-edit-btn"><a href="#/edit/{id}/" class=" mini purple"><i class="icon-edit"></i> Настройки</a></div>
            <div class="div-delete-btn"><a href="javascript:void(0)" onclick="site_control.deleteProject({id})" class="mini black"><i class="icon-trash"></i>&nbsp;Удалить</a></div>
        </div>
       </div>
     </div>
  </div>
</div>


