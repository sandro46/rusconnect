<? $this->md5_compil='7b36f7618826c14232da2e8d172b04de'; ?>
<div class="row-fluid">
  <div class="span12" id="contactlist">
    <div class="widget">
      <div class="widget-title">
        <h4><i class="icon-reorder"></i>Партнеры</h4>
        <div class="actions">
          <div class="btn-group">
              <button data-toggle="dropdown" class="btn dropdown-toggle">Настройки таблицы <span class="caret"></span></button>
                  	<script type="text/template" id="grid-partners_list-settings-MenuTemplate">
<ul class="dropdown-menu pull-right">
                        {item}<li name="{name}" {hidden}><a href="javascript:;"><i></i> {title}</a></li>{/item}
                      </ul>	</script>
<script type="text/javascript">
 try {
	var grid_partners_list_settings_menu_user_data = JSON.parse('<?php if(isset($this->vars['userSetting']['grid_partners_list_hidecols'])) echo json_encode($this->vars['userSetting']['grid_partners_list_hidecols']); else echo '[]'; ?>');
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
              <a href="#/partners/add/" data-toggle="dropdown" class="btn btn-success dropdown-toggle"><i class="icon-plus"></i> Добавить</a>
            </div>
           
        

          </div>
          
          
          <div class="span4">
            <div class="dataTables_filter" id="sample_1_filter"><label>Поиск: <input type="text" aria-controls="sample_1" class="input-medium"></label></div>
          </div>
        </div>   
        
    
        
        
        
        <?php
if(!isset($this->vars['globalgrid'])) $this->vars['globalgrid'] = array();
$this->vars['globalgrid']['partners_list'] = json_decode('{"params":{"name":"partners_list","tpl":"grid.default.html","class":"admin_clients","method":"getPartnerList"},"settings":{"cols":[{"name":"id","title":"№","search":true,"sort":true},{"name":"name","title":"ФИО","search":true,"sort":true,"from_tpl":"partners_list_nameaction"},{"name":"city","title":"Город","search":false,"sort":true},{"name":"email","title":"Email","search":true,"sort":true},{"name":"reg_date","title":"Дата регистрации","search":false,"sort":true},{"name":"partner_type_name","title":"Направление","search":false,"sort":true,"from_tpl":"partners_list_type"},{"name":"subscribes","title":"Подписчиков","search":false,"sort":true},{"name":"","title":"Действия","search":false,"sort":false,"from_tpl":"clients_list_actions"}],"user_settings":true,"autoload":true,"view":"list","limit":10},"instance":{"name":"partners_list","tpl":"grid.default.html","class":"admin_clients","method":"getPartnerList"}}', true);
$this->vars['instance'] = 'partners_list';
$this->vars['grid'] = json_decode('{"params":{"name":"partners_list","tpl":"grid.default.html","class":"admin_clients","method":"getPartnerList"},"settings":{"cols":[{"name":"id","title":"№","search":true,"sort":true},{"name":"name","title":"ФИО","search":true,"sort":true,"from_tpl":"partners_list_nameaction"},{"name":"city","title":"Город","search":false,"sort":true},{"name":"email","title":"Email","search":true,"sort":true},{"name":"reg_date","title":"Дата регистрации","search":false,"sort":true},{"name":"partner_type_name","title":"Направление","search":false,"sort":true,"from_tpl":"partners_list_type"},{"name":"subscribes","title":"Подписчиков","search":false,"sort":true},{"name":"","title":"Действия","search":false,"sort":false,"from_tpl":"clients_list_actions"}],"user_settings":true,"autoload":true,"view":"list","limit":10},"instance":{"name":"partners_list","tpl":"grid.default.html","class":"admin_clients","method":"getPartnerList"}}', true);
global $core;
$grid_tpl_site = ($core->is_admin())? $core->getAdminModule() : $core->site_name;
echo $this->get('grid.default.html',$grid_tpl_site);

unset($this->vars['grid'], $this->vars['instance']);
?>

<script type='text/javascript'>
 //try { 
   grid.partners_list = new GridInstance('partners_list'); 
   grid.partners_list.params = JSON.parse('{"name":"partners_list","tpl":"grid.default.html","class":"admin_clients","method":"getPartnerList"}'); 
   grid.partners_list.settings = JSON.parse('{"cols":[{"name":"id","title":"№","search":true,"sort":true},{"name":"name","title":"ФИО","search":true,"sort":true,"from_tpl":"partners_list_nameaction"},{"name":"city","title":"Город","search":false,"sort":true},{"name":"email","title":"Email","search":true,"sort":true},{"name":"reg_date","title":"Дата регистрации","search":false,"sort":true},{"name":"partner_type_name","title":"Направление","search":false,"sort":true,"from_tpl":"partners_list_type"},{"name":"subscribes","title":"Подписчиков","search":false,"sort":true},{"name":"","title":"Действия","search":false,"sort":false,"from_tpl":"clients_list_actions"}],"user_settings":true,"autoload":true,"view":"list","limit":10}'); 
   grid.partners_list.init(); 
 //} catch(err) { 
 //  alert('Error create grid instance. '+err.message); 
 //} 
</script>
   
        
        

        <div id="partners_list_nameaction" style="display:none">
          <a href="javascript:void(0)">{name}</a>
        </div>
        
        <div id="partners_list_type" style="display:none">
          <span class="label label-info">{partner_type_name}</span>
        </div>
        
        <div id="clients_list_actions" style="display:none">
          <div class="div-edit-btn"><a href="#/partners/edit/{id}/" class=" mini purple"><i class="icon-edit"></i> Редактировать</a></div>
            <div class="div-delete-btn"><a href="javascript:void(0)" onclick="site_clients.deletePrtner({id})" class="mini black"><i class="icon-trash"></i>&nbsp;Удалить</a></div>
          </div>
     
       </div>
     </div>
  </div>
</div>


