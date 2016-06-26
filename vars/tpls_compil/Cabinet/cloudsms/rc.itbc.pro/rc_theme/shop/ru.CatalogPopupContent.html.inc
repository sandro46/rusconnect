<? $this->md5_compil='07b812f19b2f6bf6ff6018319635e6f9'; ?>
<div class="categoryProductsContainer">
  <div class="optionsMenuTop">
    <a href="<?php if(isset($this->vars['category_info']['url'])) echo $this->vars['category_info']['url']; ?>" class="linkCategory">Перейти в раздел <span><img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/arrlightblue.png"></span></a>
    <a href="#" class="button orange">Скачать в PDF</a>
    <a href="javascript:void(0)" onclick="downloadXLS(<?php if(isset($this->vars['category_info']['id'])) echo $this->vars['category_info']['id']; ?>)" class="button blue">Скачать прайслист</a>
  </div>

  <?php if (isset($this->vars['shop_products']) && $this->vars['shop_products']  ): ?>
    <?php $this->vars['rowOpen'] = 0; ?>
    <?php $this->vars['iterator'] = 0; ?>  
    <?php $this->vars['colsPerRow'] = 5; ?>
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
      <?php if (isset($this->vars['iterator']) && $this->vars['iterator'] == $this->vars['colsPerRow']): ?><?php $this->vars['rowOpen'] = 0; ?><?php $this->vars['iterator'] = 0; ?></div><?php endif; ?>
    <?php endforeach; ?><?php endif; ?>
  	<?php if (isset($this->vars['rowOpen']) && $this->vars['rowOpen']  ): ?></div><?php endif; ?>
  <?php else: ?>
    <div style="height:450px;">
      <h2 style="font-size:25px; padding-top:16px;">Товаров по заданному запросу не найдено.</h2>
    </div>
  <?php endif; ?>

  <?php if (isset($this->vars['pagesCount']) && $this->vars['pagesCount'] > 1): ?>
	<div class="loadmore" currentpage="1">Показать больше товаров >>></div>
  <?php endif; ?>

  <div class="optionsMenuBottom">
    <a href="<?php if(isset($this->vars['category_info']['url'])) echo $this->vars['category_info']['url']; ?>" class="linkCategory">Перейти в раздел <span><img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/arrlightblue.png"></span></a>
    <a href="#" class="button orange">Скачать в PDF</a>
    <a href="javascript:void(0)" onclick="downloadXLS(<?php if(isset($this->vars['category_info']['id'])) echo $this->vars['category_info']['id']; ?>)" class="button blue">Скачать прайслист</a>
    <?php if (isset($this->vars['pagesCount']) && $this->vars['pagesCount'] > 1): ?>
    	Страница: <span class="pageCurrentItem"><?php if(isset($this->vars['pageItemNum'])) echo $this->vars['pageItemNum']; ?></span> из <?php if(isset($this->vars['pagesCount'])) echo $this->vars['pagesCount']; ?>
    <?php endif; ?>
  </div>
</div>