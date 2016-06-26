<? $this->md5_compil='7d56978f39c40009a9f942cc764ebc3b'; ?>


<?php if (isset($this->vars['cart_summary']['list']) && $this->vars['cart_summary']['list']  ): ?>
<?php if(count($this->vars['cart_summary']['list'])) : 
							   		 $this->vars['foreach']['cart_summary.list']['count'] = count($this->vars['cart_summary']['list']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['cart_summary']['list']);
							   		 foreach($this->vars['cart_summary']['list'] as $this->vars['foreach']['cart_summary_list']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['cart_summary_list']['key']; ?>
<div class="item" product-id="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>">
  <div class="cont">
    <div class="thumb">
      <img alt="" src="<?php if (isset($this->vars['item']['parent']) && $this->vars['item']['parent']  ): ?><?php if(isset($this->vars['item']['parent']['img'])) echo $this->vars['item']['parent']['img']; ?><?php else: ?><?php if(isset($this->vars['item']['img'])) echo $this->vars['item']['img']; ?><?php endif; ?>" style="width:56px" />
    </div>
    <div class="description">
      <span class="title">
        <?php if (isset($this->vars['item']['parent']) && $this->vars['item']['parent']  ): ?><?php if(isset($this->vars['item']['parent']['name'])) echo $this->vars['item']['parent']['name']; ?> - <?php endif; ?>
        <?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?>
      </span>
      <span class="price"><?php if(isset($this->vars['item']['price'])) echo $this->vars['item']['price']; ?>/ <?php if(isset($this->vars['item']['pack_size'])) echo $this->vars['item']['pack_size']; ?> <?php if(isset($this->vars['item']['pack_unit_name'])) echo $this->vars['item']['pack_unit_name']; ?></span>
    </div>
  </div>
  <div class="itemprops">
    <span class="amount"><?php if(isset($this->vars['item']['qt'])) echo $this->vars['item']['qt']; ?> <?php if (isset($this->vars['item']['min_order']) && $this->vars['item']['min_order'] == 1): ?>уп.<?php else: ?>шт.<?php endif; ?></span>
    <span class="actions">
      <a href="javascript:void(0)" onclick="removeProductFromCart(<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>)">Удалить</a>
    </span>
  </div>
</div>
<?php endforeach; ?><?php endif; ?>
<?php endif; ?>




