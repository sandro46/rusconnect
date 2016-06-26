<? $this->md5_compil='cf554fd263b51363dce89477cf7dcb53'; ?>
<div class="row-fluid">
  <div class="span12" id="contactlist">
    <div class="widget">
      <div class="widget-title">
        <h4><i class="icon-reorder"></i>Список шаблонов</h4>
        <div class="actions">
          <div class="btn-group">
              <button data-toggle="dropdown" class="btn dropdown-toggle">Настройки таблицы <span class="caret"></span></button>
                  	<script type="text/template" id="grid-templates_list-settings-MenuTemplate">
<ul class="dropdown-menu pull-right">
                        {item}<li name="{name}" {hidden}><a href="javascript:;"><i></i> {title}</a></li>{/item}
                      </ul>	</script>
<script type="text/javascript">
 try {
	var grid_templates_list_settings_menu_user_data = JSON.parse('<?php if(isset($this->vars['userSetting']['grid_templates_list_hidecols'])) echo json_encode($this->vars['userSetting']['grid_templates_list_hidecols']); else echo '[]'; ?>');
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
            <a href="#/add/" class="btn btn-success"><i class="icon-plus"></i> Содать шаблон</a>
            
            <div class="btn-group">
              <a href="javascript:void(0);" data-toggle="dropdown" class="btn btn-primary dropdown-toggle"><i class="icon-tags"></i> Действия</a>
              <span style="display:none" id="grid-templates_list-groupAction-automenu"></span><script type="text/javascript">
 try {
	var grid_templates_list_groupaction_data = JSON.parse('{"actions":[{"action":"compil","title":"\u0421\u0431\u0440\u043e\u0441\u0438\u0442\u044c \u043a\u044d\u0448","onselect":true,"icon":"eject"},{"action":"clear_history","title":"\u041e\u0447\u0438\u0441\u0442\u0438\u0442\u044c \u0438\u0441\u0442\u043e\u0440\u0438\u044e","onselect":true,"icon":"exclamation-sign"},{"action":"divider"},{"action":"remove","title":"\u0423\u0434\u0430\u043b\u0438\u0442\u044c","icon":"trash","onselect":true},{"action":"copy","title":"\u041a\u043e\u043f\u0438\u0440\u043e\u0432\u0430\u0442\u044c","icon":"copy","onselect":true},{"action":"paste","title":"\u0412\u0441\u0442\u0430\u0432\u0438\u0442\u044c","icon":"paste","onboofer":true}]}');
 }
 catch(e) {
 	console.log('error grid group action settings! -> '+ e.message);
 }
</script>

            </div>
        
            
            <div class="btn-group">
              <a href="javascript:void(0);" class="btn btn-warning" onclick="$('#grid-templates_list-filters').slideToggle()"><i class="icon-filter"></i> Фильтр <span class="badge badge-success" id="contactsFilter_button_badge" style="display:none">0</span></a>
            </div>
            
            <a href="/ru/files/" class="btn"><i class="icon-indent-left"></i> Файлы</a>
          </div>
          
          
          <div class="span4">
            <div class="dataTables_filter" id="sample_1_filter">
              <input type="text" aria-controls="sample_1" class="input-medium">
              <a href="javascript:void(0)" class="btn" onclick="grid.templates_list.addFilter('name', $(this).parent().find('input').val()).start()"><i class="icon icon-search"></i>Искать</a>
            
            </div>
          </div>
        </div>   
        
    
        
        <div class="row-fluid grid-hidden-filters" id="grid-templates_list-filters">
          <div class="span12" style="max-width:600px;">
            <div class="well">
              <h4 style="margin-top: -13px; margin-left: -11px;">Фильтр</h4>
              <form class="form-horizontal" style="margin-bottom:0px;">
                 <div class="control-group">
                  <label class="control-label" style="width:120px;">Тема</label>
                  <div class="controls" style="margin-left:120px;">
                      <select data-placeholder="Выберите тему"  name="gridTemplatesFilter-theme" class="chosen-with-diselect span12">
                          <option value="0"></option>                        
                          <?php if(count($this->vars['themesList'])) : 
							   		 $this->vars['foreach']['themesList']['count'] = count($this->vars['themesList']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['themesList']);
							   		 foreach($this->vars['themesList'] as $this->vars['foreach']['themesList']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['themesList']['key']?>
								<option value="<?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?>"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                          <?php endforeach; ?><?php endif; ?>
                       </select>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" style="width:120px;">Модуль</label>
                  <div class="controls" style="margin-left:120px;">
                      <select data-placeholder="Выберите модуль" name="gridTemplatesFilter-module" data-connect-listen="templatesFilter-site"  class="chosen-with-diselect span12">
                          
                          <option value="0"></option>
                          <?php if(count($this->vars['modulesList'])) : 
							   		 $this->vars['foreach']['modulesList']['count'] = count($this->vars['modulesList']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['modulesList']);
							   		 foreach($this->vars['modulesList'] as $this->vars['foreach']['modulesList']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['modulesList']['key']?>
								<option value="<?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?>"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                          <?php endforeach; ?><?php endif; ?>
                       </select>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" style="width:120px;">Сайт</label>
                  <div class="controls" style="margin-left:120px;" >
                      <select data-placeholder="Выберите сайт" class="chosen span12">
                          <option value="0"></option>
                       </select>
                  </div>
                </div>
               
                <a class="btn btn-success" onclick="admin_templates.appendFilter();"><i class="icon-ok icon-white"></i> Применить фильтр</a>
                <a class="btn btn-danger" onclick="admin_templates.clearFilter();"><i class="icon-remove icon-white"></i> Очистить фильтр</a>
              </form>              
            </div>
          </div>
        </div>
        
        
        <?php
if(!isset($this->vars['globalgrid'])) $this->vars['globalgrid'] = array();
$this->vars['globalgrid']['templates_list'] = json_decode('{"params":{"name":"templates_list","tpl":"grid.default.html","class":"ModuleTemplates","method":"getList"},"settings":{"cols":[{"name":"id","title":"ID","search":true,"sort":true},{"name":"site_name","title":"Сайт","search":true,"sort":true},{"name":"name","title":"Название","search":true,"sort":true,"sorted":false,"from_tpl":"template_name_action"},{"name":"name_module","title":"Модуль","search":true,"sort":true},{"name":"theme","title":"Тема","search":true,"sort":true},{"name":"description","title":"Описание","search":true,"sort":true},{"name":"lang","title":"Язык","search":true,"sort":true},{"name":"date","title":"Дата изменения","search":true,"sort":true,"sorted":true,"sort_type":"desc"},{"name":"edit_history_count","title":"Версия","search":true,"sort":true},{"name":"user_name","title":"Редактировал","search":true,"sort":true,"hidden":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"templates_list_actions"}],"user_settings":true,"group_actions":true,"autoload":false,"view":"list","limit":10},"instance":{"name":"templates_list","tpl":"grid.default.html","class":"ModuleTemplates","method":"getList"}}', true);
$this->vars['instance'] = 'templates_list';
$this->vars['grid'] = json_decode('{"params":{"name":"templates_list","tpl":"grid.default.html","class":"ModuleTemplates","method":"getList"},"settings":{"cols":[{"name":"id","title":"ID","search":true,"sort":true},{"name":"site_name","title":"Сайт","search":true,"sort":true},{"name":"name","title":"Название","search":true,"sort":true,"sorted":false,"from_tpl":"template_name_action"},{"name":"name_module","title":"Модуль","search":true,"sort":true},{"name":"theme","title":"Тема","search":true,"sort":true},{"name":"description","title":"Описание","search":true,"sort":true},{"name":"lang","title":"Язык","search":true,"sort":true},{"name":"date","title":"Дата изменения","search":true,"sort":true,"sorted":true,"sort_type":"desc"},{"name":"edit_history_count","title":"Версия","search":true,"sort":true},{"name":"user_name","title":"Редактировал","search":true,"sort":true,"hidden":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"templates_list_actions"}],"user_settings":true,"group_actions":true,"autoload":false,"view":"list","limit":10},"instance":{"name":"templates_list","tpl":"grid.default.html","class":"ModuleTemplates","method":"getList"}}', true);
global $core;
$grid_tpl_site = ($core->is_admin())? $core->getAdminModule() : $core->site_name;
echo $this->get('grid.default.html',$grid_tpl_site);

unset($this->vars['grid'], $this->vars['instance']);
?>

<script type='text/javascript'>
 //try { 
   grid.templates_list = new GridInstance('templates_list'); 
   grid.templates_list.params = JSON.parse('{"name":"templates_list","tpl":"grid.default.html","class":"ModuleTemplates","method":"getList"}'); 
   grid.templates_list.settings = JSON.parse('{"cols":[{"name":"id","title":"ID","search":true,"sort":true},{"name":"site_name","title":"Сайт","search":true,"sort":true},{"name":"name","title":"Название","search":true,"sort":true,"sorted":false,"from_tpl":"template_name_action"},{"name":"name_module","title":"Модуль","search":true,"sort":true},{"name":"theme","title":"Тема","search":true,"sort":true},{"name":"description","title":"Описание","search":true,"sort":true},{"name":"lang","title":"Язык","search":true,"sort":true},{"name":"date","title":"Дата изменения","search":true,"sort":true,"sorted":true,"sort_type":"desc"},{"name":"edit_history_count","title":"Версия","search":true,"sort":true},{"name":"user_name","title":"Редактировал","search":true,"sort":true,"hidden":true},{"name":"actions","title":"","search":false,"sort":false,"from_tpl":"templates_list_actions"}],"user_settings":true,"group_actions":true,"autoload":false,"view":"list","limit":10}'); 
   grid.templates_list.init(); 
 //} catch(err) { 
 //  alert('Error create grid instance. '+err.message); 
 //} 
</script>
   
        
        
          <div id="templates_list_actions" style="display:none">
            <div class="div-edit-btn"><a href="#/edit/{id}/" class=" mini purple"><i class="icon-edit"></i> Редактировать</a></div>
            <div class="div-delete-btn"><a href="javascript:void(0)" onclick="admin_templates.deleteItem({id})" class="mini black"><i class="icon-trash"></i>&nbsp;Удалить</a></div>
          </div>
          <div id="template_name_action" style="display:none">
            <a href="#/edit/{id}/" class=" mini purple">{name}</a></div>
          </div>
        
       </div>
     </div>
  </div>
</div>


