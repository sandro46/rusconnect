<? $this->md5_compil='2f710a57cbf91de55f75bd5c5fab80e2'; ?>
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