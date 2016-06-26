<? $this->md5_compil='f4744b0047d49d3d0fba4b3d121fdae7'; ?>
<div class="breadcrumbs">
  <ul>
    <li class="home" itemtype="http://data-vocabulary.org/Breadcrumb" itemscope=""><a href="/" itemprop="url"><i class="icon-home"></i><span itemprop="title">Крепеж и крепежные изделия оптом</span></a></li>
    <?php if(count($this->vars['shop_crumbs'])) : 
							   		 $this->vars['foreach']['shop_crumbs']['count'] = count($this->vars['shop_crumbs']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['shop_crumbs']);
							   		 foreach($this->vars['shop_crumbs'] as $this->vars['foreach']['shop_crumbs']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['shop_crumbs']['key']; ?>
    	<li itemtype="http://data-vocabulary.org/Breadcrumb" itemscope="" class="item <?php if (isset($this->vars['item']['is_last']) && $this->vars['item']['is_last']  ): ?>current<?php endif; ?>"><a itemprop="url" href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>"><span itemprop="title"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></span></a></li>
    <?php endforeach; ?><?php endif; ?>
  </ul>
</div>