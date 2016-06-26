<? $this->md5_compil='9904f2f2f8b04379632d70b20dd69ae7'; ?>
<div class="row-fluid" style="width:600px;">
	<div class="widget">
		<div class="widget-title">
          <h4><i class="icon-reorder"></i> Текущая тема сайта &laquo;<?php if(isset($this->vars['themeInfo']['name'])) echo $this->vars['themeInfo']['name']; ?>&raquo;</h4>
      	</div>
      	<div class="widget-body">
          <img src="<?php if(isset($this->vars['themeInfo']['img_preview'])) echo $this->vars['themeInfo']['img_preview']; ?>" style="width:200px; float:left; margin-right:15px;">
          <div>
            <h4><?php if(isset($this->vars['themeInfo']['title'])) echo $this->vars['themeInfo']['title']; ?></h4><br>
            <p><?php if(isset($this->vars['themeInfo']['description'])) echo $this->vars['themeInfo']['description']; ?></p>
            <!--<a class="btn" href="#/edit/"><i class="icon-cog"></i> Настроить</a>-->
          </div>
          <br style="clear:both">
          
      	</div>
  	</div>
</div>

<div class="row-fluid">
	<div class="widget">
		<div class="widget-title">
          <h4><i class="icon-reorder"></i> Другие темы для сайта</h4>
      	</div>
      	<div class="widget-body">
          <?php if(count($this->vars['themesList'])) : 
							   		 $this->vars['foreach']['themesList']['count'] = count($this->vars['themesList']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['themesList']);
							   		 foreach($this->vars['themesList'] as $this->vars['foreach']['themesList']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['themesList']['key']; ?>
            <div class="row-fluid product-search">
              <div class="span4 product-text">
                <img src="<?php if(isset($this->vars['item']['img_preview'])) echo $this->vars['item']['img_preview']; ?>" rel="elevateZoom" data-zoom-image="<?php if(isset($this->vars['item']['img_big'])) echo $this->vars['item']['img_big']; ?>" alt="" style="width:100px;">
                <div class="portfolio-text-info">
                  <h4><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></h4>
                  <p><?php if(isset($this->vars['item']['description'])) echo $this->vars['item']['description']; ?></p>
                </div>
              </div>
              <div class="span8">
                <div class="product-info">
                  Демо магазин:
                  <span><a href="http://<?php if(isset($this->vars['item']['demo_url'])) echo $this->vars['item']['demo_url']; ?>" target="_blank"><?php if(isset($this->vars['item']['demo_url'])) echo $this->vars['item']['demo_url']; ?></a></span>
                </div>
                
                <div class="product-info" style="padding-top:37px;">
                 <?php if (isset($this->vars['item']['name']) && $this->vars['item']['name'] == $this->vars['themeInfo']['name']): ?>
                  <strong>Сейчас установлена</strong>
                  <?php else: ?>
                  <button class="btn btn-success" onclick="site_control.changeTheme(<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>, '<?php if(isset($this->vars['shopInfo']['url'])) echo $this->vars['shopInfo']['url']; ?>')"><i class="icon-ok icon-white"></i> Установить</button>
                  <?php endif; ?>
                 
                </div>
              </div>
            </div>
          <?php endforeach; ?><?php endif; ?>
      	</div>
  	</div>
</div>
