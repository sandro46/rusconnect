<? $this->md5_compil='af930a392c839ad55349773101f1345e'; ?>

<div class="row-fluid">
	<div class="widget mediumWidget">
		<div class="widget-title">
          <h4><i class="icon-reorder"></i><?php if (isset($this->vars['category']['group_id']) && $this->vars['category']['group_id']  ): ?>Редактирование категории<?php else: ?>Новая категория<?php endif; ?></h4>
      	</div>
      	<div class="widget-body">
          <form class="form-horizontal">
          	<div class="control-group">
				<label class="control-label">Название категории</label>
              	<div class="controls">
                  <input class="require" type="text" name="c.name" value="<?php if(isset($this->vars['category']['name'])) echo $this->vars['category']['name']; ?>" style="width: 350px;">
              	</div>
            </div>
            
            <div class="control-group">
						<label class="control-label">URL</label>
                      	<div class="controls">
                          <div class="input-prepend">
                            <span class="add-on"><?php if(isset($this->vars['shopInfo']['url'])) echo $this->vars['shopInfo']['url']; ?>/</span>
                              <input class="" type="text" placeholder="" value="<?php if(isset($this->vars['category']['url'])) echo $this->vars['category']['url']; ?>" name="c.url">
                          </div>
                      </div>
					</div>
            
            <div class="control-group">
				<label class="control-label">Родительская категория</label>
              	<div class="controls">
                  <select name="c.parent_id" class="chosen" data-placeholder="Выберите родительскую категорию" style="width: 350px;">
                    <option></option>
                    <option value="0">Корневой раздел</option>
                    <?php if(count($this->vars['group_tree'])) : 
							   		 $this->vars['foreach']['group_tree']['count'] = count($this->vars['group_tree']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['group_tree']);
							   		 foreach($this->vars['group_tree'] as $this->vars['foreach']['group_tree']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['group_tree']['key']; ?>
                    <option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>" <?php if (isset($this->vars['category']['group_id']) && $this->vars['category']['group_id']  ): ?><?php if (isset($this->vars['category']['parrent_id']) && $this->vars['category']['parrent_id'] == $this->vars['item']['id']): ?>selected="selected"<?php endif; ?><?php else: ?><?php if (isset($this->vars['item']['id']) && $this->vars['item']['id'] == $this->vars['categoryId']): ?>selected="selected"<?php endif; ?><?php endif; ?>><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                    <?php endforeach; ?><?php endif; ?>
                  </select>
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Изображение</label>
              	<div class="controls">
                  <div class="CategoryLogoUploadButton"></div>
                  <div class="CategoryLogoContainer">
                  
                  	<?php if (isset($this->vars['category']['image_preview']) && $this->vars['category']['image_preview']  ): ?>
                    	<div class="imageTumbContainer" style="background-image:url(<?php if(isset($this->vars['category']['image_original'])) echo $this->vars['category']['image_original']; ?>)">
                          <input type="hidden" name="image_preview" value="<?php if(isset($this->vars['category']['image_preview'])) echo $this->vars['category']['image_preview']; ?>">
                          <input type="hidden" name="image_original" value="<?php if(isset($this->vars['category']['image_original'])) echo $this->vars['category']['image_original']; ?>">
                    	</div>
                    
                    <?php endif; ?>
                  
                  </div>
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Тип товаров</label>
              	<div class="controls">
                  <select name="c.category" data-placeholder="Какого типа будут твоары" class="chosen" style="width: 350px;">
                    <option></option>
                    <?php $this->vars['closed'] = 1; ?>
                    <?php if(count($this->vars['category_list'])) : 
							   		 $this->vars['foreach']['category_list']['count'] = count($this->vars['category_list']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['category_list']);
							   		 foreach($this->vars['category_list'] as $this->vars['foreach']['category_list']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['category_list']['key']; ?>
                    <?php if (isset($this->vars['item']['level']) && $this->vars['item']['level'] == 1): ?>
                    	<optgroup label="<?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?>"><?php $this->vars['closed'] = 0; ?>
                    <?php else: ?>
                    	<?php $this->vars['closed'] = 1; ?>
                    	<option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>" <?php if (isset($this->vars['category']) && $this->vars['category']  ): ?><?php if (isset($this->vars['item']['id']) && $this->vars['item']['id'] == $this->vars['category']['product_category']): ?>selected="selected"<?php endif; ?><?php endif; ?>><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                    <?php endif; ?>
                    <?php if (isset($this->vars['item']['level']) && $this->vars['item']['level'] == 1): ?><?php if (isset($this->vars['closed']) && $this->vars['closed'] == 1): ?></optgroup><?php endif; ?><?php endif; ?>
                    <?php endforeach; ?><?php endif; ?>
                  </select>
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Короткое описание</label>
              	<div class="controls">
                  <textarea class="span12" name="c.description"><?php if(isset($this->vars['category']['description'])) echo $this->vars['category']['description']; ?></textarea>
              	</div>
            </div>
            
            <div class="control-group">
              <h4>Полное описание</h4>	
              <div class="controls" style="margin-left:0px; width:100%" id="categoryFormTextEditor"><textarea name="c.description2"><?php if(isset($this->vars['category']['ext_text2'])) echo $this->vars['category']['ext_text2']; ?></textarea></div>
            </div>
            
            <button type="button" action="save" class="btn btn-success">Сохранить</button>
            <button type="button" action="saveandexit" class="btn btn-primary">Сохранить и выйти</button>
			<button type="button" action="back" class="btn">Назад</button>
          </form>
      	</div>
  	</div>
</div>