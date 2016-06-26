<? $this->md5_compil='28ba5a7cec5764eab186509874f0f0f8'; ?>
<div class="mainContentBlock">
  <div class="leftPanel">
    <div class="leftLogo">
      <a href="/"><img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/logo.png" alt=""/></a>
    </div>
    <div class="catListsContainer fluid">
      <?php echo $this->get("CategoryMenu.html", "shop"); ?>
    </div>
  </div>
  <div class="rightPanel">
    <div class="categoryDesciptionContainer">
      <div class="categoryDesciption">
        <div class="catHeaderBlockBrand">
          <img src="<?php if(isset($this->vars['vendor_info']['image_preview'])) echo $this->vars['vendor_info']['image_preview']; ?>">
          <h1><?php if(isset($this->vars['vendor_info']['name'])) echo $this->vars['vendor_info']['name']; ?></h1>
        </div>
        <div class="catDesciptionText">
          <p><?php if(isset($this->vars['vendor_info']['description'])) echo $this->vars['vendor_info']['description']; ?></p>
        </div>
      </div>
      
      <div class="categoryImage">
        <?php if (isset($this->vars['vendor_info']['text_image1']) && $this->vars['vendor_info']['text_image1']  ): ?>
        <img src="<?php if(isset($this->vars['vendor_info']['text_image1'])) echo $this->vars['vendor_info']['text_image1']; ?>" alt="<?php if(isset($this->vars['vendor_info']['name'])) echo $this->vars['vendor_info']['name']; ?>" />
      	<?php else: ?>
        <p>Нужно сюда поставить картинку!</p>
        <?php endif; ?>
      </div>                
    </div>
    <div class="categoryProductsContainer">
      <div class="optionsMenuTop">
        <a href="javascript:void(0)" onclick="downloadPDF(<?php if(isset($this->vars['vendor_info']['id'])) echo $this->vars['vendor_info']['id']; ?>)" class="button orange">Скачать в PDF</a>
        <a href="javascript:void(0)" onclick="downloadXLS(<?php if(isset($this->vars['vendor_info']['id'])) echo $this->vars['vendor_info']['id']; ?>)" class="button blue">Скачать прайслист</a>
        <div class="showPagesCount">
          Показать: <ul>
          <li><?php if (isset($this->vars['page_limit']) && $this->vars['page_limit'] == 20): ?>20<?php else: ?><a href="<?php if(isset($this->vars['current_url'])) echo $this->vars['current_url']; ?>limit/20/">20</a><?php endif; ?></li>
          <li><?php if (isset($this->vars['page_limit']) && $this->vars['page_limit'] == 40): ?>40<?php else: ?><a href="<?php if(isset($this->vars['current_url'])) echo $this->vars['current_url']; ?>limit/40/">40</a><?php endif; ?></li>
          <li><?php if (isset($this->vars['page_limit']) && $this->vars['page_limit'] == 0): ?>все<?php else: ?><a href="<?php if(isset($this->vars['current_url'])) echo $this->vars['current_url']; ?>limit/0/">все</a><?php endif; ?></li>
          </ul>
        </div>                    
        <div class="sortFilter">
          Сортировать: 
          <ul>
            <li>
              <?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "new"): ?><a href="#">Новые</a><?php endif; ?>
              <?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "popular"): ?><a href="#">Популярные</a><?php endif; ?>
              <?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "sale"): ?><a href="#">Акция</a><?php endif; ?>
              <ul>
                <li><?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "new"): ?>Новые<?php else: ?><a href="<?php if(isset($this->vars['sort_prefix'])) echo $this->vars['sort_prefix']; ?>order/new/">Новые</a><?php endif; ?></li>
                <li><?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "popular"): ?>Популярные<?php else: ?><a href="<?php if(isset($this->vars['sort_prefix'])) echo $this->vars['sort_prefix']; ?>order/popular/">Популярные</a><?php endif; ?></li>
                <li><?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "sale"): ?>Акция<?php else: ?><a href="<?php if(isset($this->vars['sort_prefix'])) echo $this->vars['sort_prefix']; ?>order/sale/">Акция</a><?php endif; ?></li>    
              </ul>
            </li>
          </ul>
        </div> 
        <?php if(isset($this->vars['pagination'])) echo $this->vars['pagination']; ?>
        <!--
        <div class="categoryViewStyle">
          <a href="#" class="grid selected">&nbsp;</a>
          <a href="#" class="list">&nbsp;</a>
        </div>
		-->
      </div>
      <?php if (isset($this->vars['shop_products']) && $this->vars['shop_products']  ): ?>
      
      <?php $this->vars['rowOpen'] = 0; ?>
      <?php $this->vars['iterator'] = 0; ?>  
      
      <?php if(count($this->vars['shop_products'])) : 
							   		 $this->vars['foreach']['shop_products']['count'] = count($this->vars['shop_products']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['shop_products']);
							   		 foreach($this->vars['shop_products'] as $this->vars['foreach']['shop_products']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['shop_products']['key']; ?>
      	<?php if (isset($this->vars['iterator']) && $this->vars['iterator'] == 0): ?><div class="row"><?php $this->vars['rowOpen'] = 1; ?><?php endif; ?>
        
      	<div class="item">
          <a href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>">
            <div class="itemimage">
              <img alt="" src="<?php if(isset($this->vars['item']['img'])) echo $this->vars['item']['img']; ?>" />
            </div>
            <span class="title"><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></span>
            <span class="price">
              <?php if (isset($this->vars['item']['mod']) && $this->vars['item']['mod']  ): ?>
              <?php if(isset($this->vars['item']['mod']['price'])) echo $this->vars['item']['mod']['price']; ?><i>/ за <?php if(isset($this->vars['item']['mod']['pack_unit'])) echo $this->vars['item']['mod']['pack_unit']; ?></i>
              <?php else: ?>
              <?php if(isset($this->vars['item']['price'])) echo $this->vars['item']['price']; ?><i>/ за <?php if(isset($this->vars['item']['pack_unit'])) echo $this->vars['item']['pack_unit']; ?></i>
              <?php endif; ?>
            </span>
          </a>
        </div> 
      
      	<?php $this->vars['iterator']++; ?>
  		<?php if (isset($this->vars['iterator']) && $this->vars['iterator'] == 5): ?><?php $this->vars['rowOpen'] = 0; ?><?php $this->vars['iterator'] = 0; ?></div><?php endif; ?>
      <?php endforeach; ?><?php endif; ?>
      <?php if (isset($this->vars['rowOpen']) && $this->vars['rowOpen']  ): ?></div><?php endif; ?>
    	
      <?php else: ?>
    	<div style="height:450px;">
    		<h2 style="font-size:25px; padding-top:16px;">Товаров по заданному запросу не найдено.</h2>
    	</div>
      <?php endif; ?>
      <div class="optionsMenuBottom">
        <a href="javascript:void(0)" onclick="downloadPDF(<?php if(isset($this->vars['vendor_info']['id'])) echo $this->vars['vendor_info']['id']; ?>)" class="button orange">Скачать в PDF</a>
        <a href="javascript:void(0)" onclick="downloadXLS(<?php if(isset($this->vars['vendor_info']['id'])) echo $this->vars['vendor_info']['id']; ?>)" class="button blue">Скачать прайслист</a>
        <div class="showPagesCount">
          Показать: <ul>
          <li><?php if (isset($this->vars['page_limit']) && $this->vars['page_limit'] == 20): ?>20<?php else: ?><a href="<?php if(isset($this->vars['current_url'])) echo $this->vars['current_url']; ?>limit/20/">20</a><?php endif; ?></li>
          <li><?php if (isset($this->vars['page_limit']) && $this->vars['page_limit'] == 40): ?>40<?php else: ?><a href="<?php if(isset($this->vars['current_url'])) echo $this->vars['current_url']; ?>limit/40/">40</a><?php endif; ?></li>
          <li><?php if (isset($this->vars['page_limit']) && $this->vars['page_limit'] == 0): ?>все<?php else: ?><a href="<?php if(isset($this->vars['current_url'])) echo $this->vars['current_url']; ?>limit/0/">все</a><?php endif; ?></li>
          </ul>
        </div>                    
        <div class="sortFilter">
          Сортировать: 
          <ul>
            <li>
              <?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "new"): ?><a href="#">Новые</a><?php endif; ?>
              <?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "popular"): ?><a href="#">Популярные</a><?php endif; ?>
              <?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "sale"): ?><a href="#">Акция</a><?php endif; ?>
              <ul>
                <li><?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "new"): ?>Новые<?php else: ?><a href="<?php if(isset($this->vars['sort_prefix'])) echo $this->vars['sort_prefix']; ?>order/new/">Новые</a><?php endif; ?></li>
                <li><?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "popular"): ?>Популярные<?php else: ?><a href="<?php if(isset($this->vars['sort_prefix'])) echo $this->vars['sort_prefix']; ?>order/popular/">Популярные</a><?php endif; ?></li>
                <li><?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "sale"): ?>Акция<?php else: ?><a href="<?php if(isset($this->vars['sort_prefix'])) echo $this->vars['sort_prefix']; ?>order/sale/">Акция</a><?php endif; ?></li>    
              </ul>
            </li>
          </ul>
        </div> 
        <?php if(isset($this->vars['pagination'])) echo $this->vars['pagination']; ?>
        <!--
        <div class="categoryViewStyle">
          <a href="#" class="grid selected">&nbsp;</a>
          <a href="#" class="list">&nbsp;</a>
        </div>
		-->
      </div>
    </div>
  </div>
</div>

