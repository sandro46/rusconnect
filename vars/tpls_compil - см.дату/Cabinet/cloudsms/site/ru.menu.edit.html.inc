<? $this->md5_compil='acec74d8fa287537313a144fccca0816'; ?>
<div class="row-fluid">
	<div class="widget smallWidget">
		<div class="widget-title">
          <h4><i class="icon-reorder"></i>Редактирование пункта меню</h4>
      	</div>
      	<div class="widget-body">
          <form class="form-horizontal">
          	<div class="control-group">
				<label class="control-label">Заголовок</label>
              	<div class="controls">
                  <input class="require" type="text" name="m.name" value="<?php if(isset($this->vars['menuInfo']['title'])) echo $this->vars['menuInfo']['title']; ?>">
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Выдимый</label>
              	<div class="controls">
                    <div class="visibleMenuItem"><input type="checkbox" <?php if (isset($this->vars['menuInfo']['hidden']) && $this->vars['menuInfo']['hidden']  ): ?><?php else: ?>checked="checked"<?php endif; ?> name="m.visible" value="1" class="toggle" /></div>
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Разделы:</label>
              	<div class="controls" >
                  	<select name="m.parent_id" class="chosen require" data-placeholder="Выберите раздел">
                      <option></option>  
						<?php if(count($this->vars['menuParrent'])) : 
							   		 $this->vars['foreach']['menuParrent']['count'] = count($this->vars['menuParrent']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['menuParrent']);
							   		 foreach($this->vars['menuParrent'] as $this->vars['foreach']['menuParrent']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['menuParrent']['key']; ?>
                      		<option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>" <?php if (isset($this->vars['menuInfo']['parent_id']) && $this->vars['menuInfo']['parent_id'] == $this->vars['item']['id']): ?>selected="selected"<?php endif; ?>><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></option>
                  		<?php endforeach; ?><?php endif; ?>
                      </select>
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Тип ссылки: </label>
              	<div class="controls">
					<select name="m.type_id" class="chosen-menu-type require" data-placeholder="Выберите тип ссылки">
                      <option></option>  
                      <option value="2" <?php if (isset($this->vars['menuInfo']['type_id']) && $this->vars['menuInfo']['type_id'] == 2): ?>selected="selected"<?php endif; ?>>Ссылка на документ (страницу на сайте)</option>
                      <option value="3" <?php if (isset($this->vars['menuInfo']['type_id']) && $this->vars['menuInfo']['type_id'] == 3): ?>selected="selected"<?php endif; ?>>Ссылка на раздел каталога</option>
                      <option value="4" <?php if (isset($this->vars['menuInfo']) && $this->vars['menuInfo']  ): ?><?php else: ?>selected="selected"<?php endif; ?> <?php if (isset($this->vars['menuInfo']['type_id']) && $this->vars['menuInfo']['type_id'] == 4): ?>selected="selected"<?php endif; ?>>Произвольная ссылка</option>
                  	</select>
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Назначение ссылки:</label>
              	<div class="controls item_destination">
					
                  <div class="input-prepend type-4" <?php if (isset($this->vars['menuInfo']['type_id']) && $this->vars['menuInfo']['type_id'] != 4): ?>style="display:none"<?php endif; ?>>
                      <span class="add-on">http://</span>
                      <input class="" type="text" placeholder="" name="m.value4" <?php if (isset($this->vars['menuInfo']['type_id']) && $this->vars['menuInfo']['type_id'] == 4): ?>value="<?php if(isset($this->vars['menuInfo']['value'])) echo $this->vars['menuInfo']['value']; ?>"<?php endif; ?>>
                  </div>
                  <div class="groupTreeListSelect type-3"></div>
                  <div class="pagesTreeListSelect type-2">
                    <input type="hidden"  name="m.value2" <?php if (isset($this->vars['menuInfo']['type_id']) && $this->vars['menuInfo']['type_id'] == 2): ?>value="<?php if(isset($this->vars['menuInfo']['value'])) echo $this->vars['menuInfo']['value']; ?>"<?php endif; ?>>
                    <input type="text" readonly="true" name="m.titlevalue2">
                    
                  	<div id="menuPages" class="menuContent" style="display:none; position: absolute;">
                      	<ul id="menuPagesTree" class="ztree dropdown-menu" style="margin-top:0; width:160px;"></ul>
                  	</div>
                  
                  </div>
              	</div>
            </div>
            
            
            <button type="button" action="save" class="btn btn-success">Сохранить</button>
			<button type="button" action="back" class="btn">Назад</button>
          </form>
      	</div>
  	</div>
</div>