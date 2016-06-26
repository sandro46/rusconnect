<? $this->md5_compil='4934ae4c6d3a3f94e64f508affcd738a'; ?>
<?php if (isset($this->vars['cartInfo']['list']) && $this->vars['cartInfo']['list']  ): ?><?php else: ?><?php if (isset($this->vars['cartbox']) && $this->vars['cartbox']  ): ?><?php $this->vars['cartInfo'] = $this->vars['cartbox']; ?><?php $this->vars['dontshowcart'] = 1; ?><?php endif; ?><?php endif; ?>
<?php if (isset($this->vars['cartInfo']['list']) && $this->vars['cartInfo']['list']  ): ?>
<table class="leftProducts">
  <?php $this->vars['limit'] = 5; ?>
  <?php $this->vars['current'] = 0; ?>
  <?php if(count($this->vars['cartInfo']['list'])) : 
							   		 $this->vars['foreach']['cartInfo.list']['count'] = count($this->vars['cartInfo']['list']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['cartInfo']['list']);
							   		 foreach($this->vars['cartInfo']['list'] as $this->vars['foreach']['cartInfo_list']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['cartInfo_list']['key']; ?>
  <?php $this->vars['current']++; ?>
  <tr>
    <td class="name"><a href="<?php if (isset($this->vars['item']['parent']) && $this->vars['item']['parent']  ): ?><?php if(isset($this->vars['item']['parent']['url'])) echo $this->vars['item']['parent']['url']; ?><?php else: ?><?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?><?php endif; ?>" target="_blank"><?php if (isset($this->vars['item']['parent']) && $this->vars['item']['parent']  ): ?><?php if(isset($this->vars['item']['parent']['title'])) echo $this->vars['item']['parent']['title']; ?> - <?php endif; ?><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></a></td>
    <td class="price"><?php if(isset($this->vars['item']['price_full'])) echo $this->vars['item']['price_full']; ?></td>
  </tr>
  
  <?php if (isset($this->vars['current']) && $this->vars['current'] >= $this->vars['limit']): ?><?php break; ?><?php endif; ?>
  <?php endforeach; ?><?php endif; ?>
  <tr class="morelink">
    <td colspan="2">Всего товаров: <?php if(isset($this->vars['cartInfo']['count'])) echo $this->vars['cartInfo']['count']; ?><br>На сумму: <?php if(isset($this->vars['cartInfo']['sum'])) echo $this->vars['cartInfo']['sum']; ?><?php if (isset($this->vars['dontshowcart']) && $this->vars['dontshowcart']  ): ?><?php else: ?><br><a href="/ru/shop/cart/">Смотреть все</a><?php endif; ?></td>
  </tr>

</table>
<?php else: ?>
	<h1>В корзине пусто!</h1>
<?php endif; ?>