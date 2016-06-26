<? $this->md5_compil='504b83321fe665d253580fbd1fee81b0'; ?>
<div class="mainContentBlock">
  
  <?php echo $this->get("breadcrumbs.html", "shop"); ?>
  <div class="flex">
    <div class="leftPanel">
      
      <div class="catListsContainer fluid">
        <?php echo $this->get("CategoryMenu.html", "shop"); ?>
      </div>
      
    </div>
    
    <div class="rightPanel">
      <div class="categoryDesciptionContainer">
        <div class="categoryDesciption" style="background-image:url('<?php if(isset($this->vars['category_info']['url_big'])) echo $this->vars['category_info']['url_big']; ?>')">
          <div class="catHeaderBlock">
            <?php if (isset($this->vars['category_info']['ext_char1']) && $this->vars['category_info']['ext_char1']  ): ?>
              	<div class="icon" style="background-image: url('<?php if(isset($this->vars['category_info']['ext_char1'])) echo $this->vars['category_info']['ext_char1']; ?>');"> </div>
            <?php else: ?>
              <?php if (isset($this->vars['category_info']['parent_icon']) && $this->vars['category_info']['parent_icon']  ): ?>
                  <div class="icon" style="background-image: url('<?php if(isset($this->vars['category_info']['parent_icon'])) echo $this->vars['category_info']['parent_icon']; ?>');"> </div>
              <?php else: ?>
                  <div class="icon" style="background-image: url('<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>samples/krepezh.png');"> </div>
              <?php endif; ?>
            <?php endif; ?>
            <div class="iconFrame"></div>
            <h1><?php if(isset($this->vars['category_info']['name'])) echo $this->vars['category_info']['name']; ?></h1>
            <br style="clear:both">
          </div>
          <div class="catDesciptionText">
            <p><?php if(isset($this->vars['category_info']['description'])) echo $this->vars['category_info']['description']; ?></p>
          </div>
        </div>
      </div>
      
      <div class="categoryProductsContainer">
  
        <div class="optionsMenuTop">
          <a href="/vars/files/Full_price_Rusconnect_02_03_16.xls" class="button orange" onclick="metrika('downloader_price');">Скачать полный прайслист</a>
          <a href="javascript:void(0)" onclick="downloadXLS(<?php if(isset($this->vars['category_info']['id'])) echo $this->vars['category_info']['id']; ?>)" class="button blue">Скачать прайслист раздела</a>

          
          <div class="sortFilter">
            Сортировать: 
            <ul>
              <li>
                <?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "new"): ?><a rel="nofolow" href="#">Новые</a><?php endif; ?>
                <?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "popular"): ?><a rel="nofolow" href="#">Популярные</a><?php endif; ?>
                <?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "sale"): ?><a rel="nofolow" href="#">Акция</a><?php endif; ?>
                <ul>
                  <li><?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "new"): ?>Новые<?php else: ?><a rel="nofolow" href="<?php if(isset($this->vars['sort_prefix'])) echo $this->vars['sort_prefix']; ?>order/new/">Новые</a><?php endif; ?></li>
                  <li><?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "popular"): ?>Популярные<?php else: ?><a rel="nofolow" href="<?php if(isset($this->vars['sort_prefix'])) echo $this->vars['sort_prefix']; ?>order/popular/">Популярные</a><?php endif; ?></li>
                  <li><?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "sale"): ?>Акция<?php else: ?><a rel="nofolow" href="<?php if(isset($this->vars['sort_prefix'])) echo $this->vars['sort_prefix']; ?>order/sale/">Акция</a><?php endif; ?></li>    
                </ul>
              </li>
            </ul>
          </div> 

          
          <div class="showPagesCount">
            Показать: <ul>
            <li><?php if (isset($this->vars['page_limit']) && $this->vars['page_limit'] == 20): ?><span>20</span><?php else: ?><a rel="nofolow" href="<?php if(isset($this->vars['current_url'])) echo $this->vars['current_url']; ?>limit/20/">20</a><?php endif; ?></li>
            <li><?php if (isset($this->vars['page_limit']) && $this->vars['page_limit'] == 40): ?><span>40</span><?php else: ?><a rel="nofolow" href="<?php if(isset($this->vars['current_url'])) echo $this->vars['current_url']; ?>limit/40/">40</a><?php endif; ?></li>
            <li><?php if (isset($this->vars['page_limit']) && $this->vars['page_limit'] == 0): ?><span>все</span><?php else: ?><a rel="nofolow" href="<?php if(isset($this->vars['current_url'])) echo $this->vars['current_url']; ?>limit/0/">все</a><?php endif; ?></li>
            </ul>
          </div> 
          
          <!--
          <div class="categoryViewStyle">
            <a rel="nofolow" href="#" class="grid selected">&nbsp;</a>
            <a rel="nofolow" href="#" class="list">&nbsp;</a>
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
              <div class="itemimage" style="background-image:url('<?php if(isset($this->vars['item']['img'])) echo $this->vars['item']['img']; ?>')">
                <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/clear.gif" />
              </div>
              <span class="title"><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></span>
              <span class="price">
                <?php if (isset($this->vars['item']['mod']) && $this->vars['item']['mod']  ): ?>
                <?php if(isset($this->vars['item']['mod']['price_s'])) echo $this->vars['item']['mod']['price_s']; ?><i>/ за <?php if(isset($this->vars['item']['mod']['pack_unit_name'])) echo $this->vars['item']['mod']['pack_unit_name']; ?></i>
                <?php else: ?>
                <?php if(isset($this->vars['item']['price_s'])) echo $this->vars['item']['price_s']; ?><i>/ за <?php if(isset($this->vars['item']['pack_unit_name'])) echo $this->vars['item']['pack_unit_name']; ?></i>
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
          
          <!--<a href="/vars/files/Full_price_Rusconnect_02_03_16.xls" class="button orange">Скачать полный прайслист</a>
          <a href="javascript:void(0)" onclick="downloadXLS(<?php if(isset($this->vars['category_info']['id'])) echo $this->vars['category_info']['id']; ?>)" class="button blue">Скачать прайслист раздела</a>
          <div class="showPagesCount">
            Показать: <ul>
            <li><?php if (isset($this->vars['page_limit']) && $this->vars['page_limit'] == 20): ?>20<?php else: ?><a rel="nofolow" href="<?php if(isset($this->vars['current_url'])) echo $this->vars['current_url']; ?>limit/20/">20</a><?php endif; ?></li>
            <li><?php if (isset($this->vars['page_limit']) && $this->vars['page_limit'] == 40): ?>40<?php else: ?><a rel="nofolow" href="<?php if(isset($this->vars['current_url'])) echo $this->vars['current_url']; ?>limit/40/">40</a><?php endif; ?></li>
            <li><?php if (isset($this->vars['page_limit']) && $this->vars['page_limit'] == 0): ?>все<?php else: ?><a rel="nofolow" href="<?php if(isset($this->vars['current_url'])) echo $this->vars['current_url']; ?>limit/0/">все</a><?php endif; ?></li>
            </ul>
          </div>-->
          
          <!--<div class="sortFilter">
            Сортировать: 
            <ul>
              <li>
                <?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "new"): ?><a rel="nofolow" href="#">Новые</a><?php endif; ?>
                <?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "popular"): ?><a rel="nofolow" href="#">Популярные</a><?php endif; ?>
                <?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "sale"): ?><a rel="nofolow" href="#">Акция</a><?php endif; ?>
                <ul>
                  <li><?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "new"): ?>Новые<?php else: ?><a rel="nofolow" href="<?php if(isset($this->vars['sort_prefix'])) echo $this->vars['sort_prefix']; ?>order/new/">Новые</a><?php endif; ?></li>
                  <li><?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "popular"): ?>Популярные<?php else: ?><a rel="nofolow" href="<?php if(isset($this->vars['sort_prefix'])) echo $this->vars['sort_prefix']; ?>order/popular/">Популярные</a><?php endif; ?></li>
                  <li><?php if (isset($this->vars['sort_name']) && $this->vars['sort_name'] == "sale"): ?>Акция<?php else: ?><a rel="nofolow" href="<?php if(isset($this->vars['sort_prefix'])) echo $this->vars['sort_prefix']; ?>order/sale/">Акция</a><?php endif; ?></li>    
                </ul>
              </li>
            </ul>
          </div>--> 
          
          <?php if (isset($this->vars['pagination']) && $this->vars['pagination']  ): ?>
          	<span style="font-size:13px; font-family: Verdana, sans-serif; font-weight:bold;">Страница:</span> <?php if(isset($this->vars['pagination'])) echo $this->vars['pagination']; ?>
          <?php endif; ?>
          
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

  <?php if (isset($this->vars['category_info']['ext_text2']) && $this->vars['category_info']['ext_text2']  ): ?>
  	<div id="catExtendedText" class="normalText"><?php if(isset($this->vars['category_info']['ext_text2'])) echo $this->vars['category_info']['ext_text2']; ?><br><br></div>
  <?php endif; ?>

</div>

