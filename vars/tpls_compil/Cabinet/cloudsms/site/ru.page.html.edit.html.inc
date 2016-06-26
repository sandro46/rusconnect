<? $this->md5_compil='aa094a60fc431b416f0a22872dc4a76e'; ?>
<script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>modules/uploader.js"></script>

<div class="row-fluid">
	<div class="widget tab-widget-product">
		<div class="widget-title">
            <span class="tools tab-header" name="main" style="float: left;"><span class="item"><i class="icon-cog"></i> Общая информация</span></span>
			<span class="tools tab-header" name="template" style="float: left;"><span class="item"><i class="icon-globe"></i> Шаблон, SEO, опции</span></span>
			<span class="tools tab-header" name="access" style="float: left;"><span class="item"><i class="icon-globe"></i> Права доступа</span></span>
		</div>
		<div class="widget-body">
			<div class="settings-form tab-body" section="main">
				<form class="form-horizontal ">
					<div class="control-group">
						<label class="control-label">Заголовок:</label>
						<div class="controls"><input class="span12 require" type="text" name="p.name" value="<?php if(isset($this->vars['pageInfo']['title'])) echo $this->vars['pageInfo']['title']; ?>" /></div>
					</div>
                  	<div class="control-group">
						<label class="control-label">URL:</label>
                      	<div class="controls">
                          <div class="input-prepend">
                            <span class="add-on"><?php if(isset($this->vars['shopInfo']['url'])) echo $this->vars['shopInfo']['url']; ?>/</span>
                              <input class="" type="text" placeholder="" name="p.url" value="<?php if(isset($this->vars['pageInfo']['url'])) echo $this->vars['pageInfo']['url']; ?>">
                          </div>
                      </div>
					</div>
					                  
                  	<div class="control-group">
						<label class="control-label">Опубликовать на сайте:</label>
						<div class="controls">
                          <div class="product_public_in_site_switch"><input type="checkbox" <?php if (isset($this->vars['pageInfo']) && $this->vars['pageInfo']  ): ?><?php if (isset($this->vars['pageInfo']['hidden']) && $this->vars['pageInfo']['hidden'] == "0"): ?>checked="checked"<?php endif; ?><?php else: ?>checked="checked"<?php endif; ?> name="p.published" value="1" class="toggle" /></div>
                      	</div>
					</div>
                  
                  	<div class="control-group">
						<label class="control-label">Тэги:</label>
                      	<div class="controls"><input class="span12 " name="p.tags" value="<?php if(isset($this->vars['pageInfo']['tags'])) echo $this->vars['pageInfo']['tags']; ?>"/></div>
					</div>
                  
					<div class="control-group">
						<label class="control-label">Короткое описание:</label>
						<div class="controls"><textarea class="span12" name="p.description"><?php if(isset($this->vars['pageInfo']['description'])) echo $this->vars['pageInfo']['description']; ?></textarea></div>
					</div>
                  
                    <div class="control-group">
                      <label class="control-label">Иконка в заголовке:</label>
                      <div class="controls">
                        <div id="pageImageUploadBar"></div>
                        <div id="pageImageUploadItem" class="controls span12" style="min-height:0px; margin-left:0px"></div>
                      </div>
                    </div>
				
					<div class="control-group">
						<h4>Полное описание</h4>	
						<div class="controls" style="margin-left:0px; width:100%" id="productFormTextEditor"></div>
					</div>
				
				</form>
			</div>
	
			<div class="settings-form tab-body" section="template">
				<form class="form-horizontal ">
					<div class="control-group">
						<label class="control-label">Шаблон:</label>
						<div class="controls">
                          	<select class="chosen" data-placeholder="Выберите шаблон отображения" name="p.template">
                               <?php if(count($this->vars['formTemplates'])) : 
							   		 $this->vars['foreach']['formTemplates']['count'] = count($this->vars['formTemplates']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['formTemplates']);
							   		 foreach($this->vars['formTemplates'] as $this->vars['foreach']['formTemplates']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['formTemplates']['key']; ?>
                                 <option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>" <?php if (isset($this->vars['pageInfo']) && $this->vars['pageInfo']  ): ?><?php if (isset($this->vars['pageInfo']['template_id']) && $this->vars['pageInfo']['template_id'] == $this->vars['item']['id']): ?>selected="selected"<?php endif; ?><?php else: ?><?php if (isset($this->vars['item']['name']) && $this->vars['item']['name'] == 'simple_show.html'): ?>selected="selected"<?php endif; ?><?php endif; ?>><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                               <?php endforeach; ?><?php endif; ?>
                            </select>
						</div>
					</div>
                  
                  	<div class="control-group">
						<label class="control-label">Тип сортировки:</label>
						<div class="controls">
                          	<select class="chosen" data-placeholder="Выберите метод сортировки" name="p.sort_type">
                              <option></option>
                              <option value="1" <?php if (isset($this->vars['pageInfo']) && $this->vars['pageInfo']  ): ?><?php if (isset($this->vars['pageInfo']['childs_order']) && $this->vars['pageInfo']['childs_order'] == 1): ?>selected="selected"<?php endif; ?><?php else: ?> selected="selected"<?php endif; ?> >Стандартно (по расположению в каталоге)</option>
                              <option value="2" <?php if (isset($this->vars['pageInfo']) && $this->vars['pageInfo']  ): ?><?php if (isset($this->vars['pageInfo']['childs_order']) && $this->vars['pageInfo']['childs_order'] == 1): ?>selected="selected"<?php endif; ?><?php endif; ?>>По дате добавления</option>
                              <option value="3" <?php if (isset($this->vars['pageInfo']) && $this->vars['pageInfo']  ): ?><?php if (isset($this->vars['pageInfo']['childs_order']) && $this->vars['pageInfo']['childs_order'] == 1): ?>selected="selected"<?php endif; ?><?php endif; ?>>По дате документа</option>
                            </select>
						</div>
					</div>
                  
                  	<div class="control-group">
						<label class="control-label">Дата публикации:</label>
						<div class="controls">
                          <div class="input-append date date-picker"> 
                            <input style="width:85px;" name="p.sdate" class=" m-ctrl-medium date-picker"  data-date-format="dd.mm.yyyy" size="8" type="text" value="<?php if(isset($this->vars['pageInfo']['special_date'])) echo $this->vars['pageInfo']['special_date']; ?>" />
                            <span class="add-on"><i class="icon-calendar"></i></span>
                          </div>
                      	</div>
					</div>
                  
                  	<div class="control-group">
						<label class="control-label">Meta description:</label>
						<div class="controls"><input class="span10" type="text" name="p.meta_description" value="<?php if(isset($this->vars['pageInfo']['meta_description'])) echo $this->vars['pageInfo']['meta_description']; ?>"/></div>
					</div>
                     
					<div class="control-group">
						<label class="control-label">Meta keywords:</label>
						<div class="controls"><input class="span10" type="text" name="p.meta_keywords" value="<?php if(isset($this->vars['pageInfo']['meta_keywords'])) echo $this->vars['pageInfo']['meta_keywords']; ?>"/></div>
					</div>
				</form>
			</div>
          
         	<div class="settings-form tab-body" section="access">
				<form class="form-horizontal ">
                  	выбор групп пользователей для доступа
                  <i>commin soon...</i>
				</form>
			</div>
		</div>

		<div class="form-actions">
			<button type="button" action="save" class="btn btn-success">Сохранить</button>
			<button type="button" action="back" class="btn">Отмена</button>
		</div>
	</div>
</div>


