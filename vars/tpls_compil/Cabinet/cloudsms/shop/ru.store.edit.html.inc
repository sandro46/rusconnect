<? $this->md5_compil='787d01bae322484600f7b573fdd5da2a'; ?>

<div class="row-fluid">
	<div class="widget smallWidget">
		<div class="widget-title">
          <h4><i class="icon-reorder"></i><?php if (isset($this->vars['store']['store_id']) && $this->vars['store']['store_id']  ): ?>Редактирование склада<?php else: ?>Новый склад<?php endif; ?></h4>
      	</div>
      	<div class="widget-body">
          <form class="form-horizontal">
          	<div class="control-group">
				<label class="control-label">Название</label>
              	<div class="controls">
                  <input class="require" type="text" name="c.name" value="<?php if(isset($this->vars['store']['name'])) echo $this->vars['store']['name']; ?>" style="width: 350px;">
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Регион</label>
              	<div class="controls">
                  <select name="c.region_id" class="chosen" data-placeholder="Выберите регион" style="width: 350px;">
                    <option></option>
                    <?php if(count($this->vars['geo_regions'])) : 
							   		 $this->vars['foreach']['geo_regions']['count'] = count($this->vars['geo_regions']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['geo_regions']);
							   		 foreach($this->vars['geo_regions'] as $this->vars['foreach']['geo_regions']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['geo_regions']['key']?>
                    <option value="<?php if(isset($this->vars['item']['region_id'])) echo $this->vars['item']['region_id']; ?>" <?php if (isset($this->vars['store']['store_id']) && $this->vars['store']['store_id']  ): ?><?php if (isset($this->vars['item']['region_id']) && $this->vars['item']['region_id'] == $this->vars['store']['region_id']): ?>selected="selected"<?php endif; ?><?php endif; ?>><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                    <?php endforeach; ?><?php endif; ?>
                  </select>
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Город</label>
              	<div class="controls">
                  <select name="c.city_id" class="chosen" data-placeholder="Выберите город" style="width: 350px;">
                    <option></option>
                  </select>
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Адрес</label>
              	<div class="controls">
                  <input class="require" type="text" name="c.name" value="<?php if(isset($this->vars['store']['address'])) echo $this->vars['store']['address']; ?>" style="width: 350px;">
              	</div>
            </div>
            
            
            <div class="control-group">
				<label class="control-label">Индекс</label>
              	<div class="controls">
                  <input class="require" type="text" name="c.name" value="<?php if(isset($this->vars['store']['index'])) echo $this->vars['store']['index']; ?>" style="width: 350px;">
              	</div>
            </div>
            

            
            <button type="button" action="save" class="btn btn-success">Сохранить</button>
			<button type="button" action="back" class="btn">Назад</button>
          </form>
      	</div>
  	</div>
</div>