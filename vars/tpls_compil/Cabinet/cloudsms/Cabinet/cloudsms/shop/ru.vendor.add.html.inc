<? $this->md5_compil='2fe793110c444dc574b06471a32ba06d'; ?>
<div class="row-fluid">
	<div class="widget mediumWidget">
		<div class="widget-title">
          <h4><i class="icon-reorder"></i><?php if (isset($this->vars['vendor']['id']) && $this->vars['vendor']['id']  ): ?>Редактирование карточки производителя<?php else: ?>Новый производитель<?php endif; ?></h4>
      	</div>
      	<div class="widget-body">
          <form class="form-horizontal">
          	<div class="control-group">
				<label class="control-label">Название производителя</label>
              	<div class="controls">
                  <input class="require span12" type="text"  name="name" value="<?php if(isset($this->vars['vendor']['name'])) echo $this->vars['vendor']['name']; ?>">
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Тип продукции</label>
              	<div class="controls">
                  <input class="require span12" type="text"  name="sub_description" value="<?php if(isset($this->vars['vendor']['sub_description'])) echo $this->vars['vendor']['sub_description']; ?>">
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Логотип</label>
              	<div class="controls">
                  <div class="VendorLogoUploadButton"></div>
                  <div class="VendorLogoContainer">
                  
                  	<?php if (isset($this->vars['vendor']['image_preview']) && $this->vars['vendor']['image_preview']  ): ?>
                    	<div class="imageTumbContainer" style="background-image:url(<?php if(isset($this->vars['vendor']['image_preview'])) echo $this->vars['vendor']['image_preview']; ?>)">
                          <input type="hidden" name="image_preview" value="<?php if(isset($this->vars['vendor']['image_preview'])) echo $this->vars['vendor']['image_preview']; ?>">
                          <input type="hidden" name="image_original" value="<?php if(isset($this->vars['vendor']['image_original'])) echo $this->vars['vendor']['image_original']; ?>">
                    	</div>
                    <?php endif; ?>
                  
                  </div>
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Картинка продукции</label>
              	<div class="controls">
                  <div class="VendorLogo2UploadButton"></div>
                  <div class="VendorLogo2Container">
                  
                  	<?php if (isset($this->vars['vendor']['text_image1']) && $this->vars['vendor']['text_image1']  ): ?>
                    	<div class="imageTumbContainer" style="background-image:url(<?php if(isset($this->vars['vendor']['text_image2'])) echo $this->vars['vendor']['text_image2']; ?>)">
                          <input type="hidden" name="text_image1" value="<?php if(isset($this->vars['vendor']['text_image1'])) echo $this->vars['vendor']['text_image1']; ?>">
                          <input type="hidden" name="text_image2" value="<?php if(isset($this->vars['vendor']['text_image2'])) echo $this->vars['vendor']['text_image2']; ?>">
                    	</div>
                    <?php endif; ?>
                  
                  </div>
              	</div>
            </div>
            
            
            <div class="control-group">
              <h4>Описание</h4>	
              <div class="controls" style="margin-left:0px; width:100%" id="productFormTextEditor"></div>
            </div>
            
            
            <button type="button" action="save" class="btn btn-success">Сохранить</button>
			<button type="button" action="back" class="btn">Назад</button>
          </form>
      	</div>
  	</div>
</div>