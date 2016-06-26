<? $this->md5_compil='07d1ee069b14b15b19b97577f09031c8'; ?>
<div class="mainContentBlockFullWidth">
  <div class="title">
    <h1><img class="icon" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/icon_cart_white.png"><span> Моя корзина</span></h1>
  </div>

  <div class="content">
    <?php if (isset($this->vars['cartInfo']['count']) && $this->vars['cartInfo']['count'] > 0): ?>
    <table class="table" id="cartProdListTable">
      <tbody>
        <tr class="header">
          <td>Товар:</td>
          <td></td>
          <td>Ед. изм.</td>
          <td>Упаковка:</td>
          <td>Цена:</td>
          <td>Кол-во:</td>         
          <td>Стоимость:</td>
        </tr>
        <?php if(count($this->vars['cartInfo']['list'])) : 
							   		 $this->vars['foreach']['cartInfo.list']['count'] = count($this->vars['cartInfo']['list']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['cartInfo']['list']);
							   		 foreach($this->vars['cartInfo']['list'] as $this->vars['foreach']['cartInfo_list']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['cartInfo_list']['key']; ?>
       
        <tr class="row">
          <td>
            <a href="<?php if (isset($this->vars['item']['parent']) && $this->vars['item']['parent']  ): ?><?php if(isset($this->vars['item']['parent']['url'])) echo $this->vars['item']['parent']['url']; ?><?php else: ?><?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?><?php endif; ?>" target="_blank"><img width="76" height="76" alt="image description" src="<?php if (isset($this->vars['item']['parent']) && $this->vars['item']['parent']  ): ?><?php if(isset($this->vars['item']['parent']['img'])) echo $this->vars['item']['parent']['img']; ?><?php else: ?><?php if(isset($this->vars['item']['img'])) echo $this->vars['item']['img']; ?><?php endif; ?>"></a>
          </td>
          <td class="title">
          	<a href="<?php if (isset($this->vars['item']['parent']) && $this->vars['item']['parent']  ): ?><?php if(isset($this->vars['item']['parent']['url'])) echo $this->vars['item']['parent']['url']; ?><?php else: ?><?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?><?php endif; ?>" target="_blank"><?php if (isset($this->vars['item']['parent']) && $this->vars['item']['parent']  ): ?><?php if(isset($this->vars['item']['parent']['title'])) echo $this->vars['item']['parent']['title']; ?> - <?php endif; ?><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></a>
            <br>
            <?php if (isset($this->vars['item']['parent']) && $this->vars['item']['parent']  ): ?><div class="mod">Размер: <?php if(isset($this->vars['item']['description'])) echo $this->vars['item']['description']; ?></div><?php endif; ?>
            <div class="mod">Артикул: <?php if(isset($this->vars['item']['article'])) echo $this->vars['item']['article']; ?></div>
          </td>  
          <td><?php if (isset($this->vars['item']['min_order']) && $this->vars['item']['min_order'] == 1): ?>уп.<?php else: ?>шт.<?php endif; ?></td>
          <td><?php if(isset($this->vars['item']['pack_size'])) echo $this->vars['item']['pack_size']; ?> <?php if(isset($this->vars['item']['pack_unit_name'])) echo $this->vars['item']['pack_unit_name']; ?></td>
          <td><?php if(isset($this->vars['item']['price'])) echo $this->vars['item']['price']; ?></td>
          <td>
          	<div class="count quantity">
              <span class="down">-</span>
              <input type="text" maxlength="2" value="<?php if(isset($this->vars['item']['qt'])) echo $this->vars['item']['qt']; ?>" name="qt" prod-id="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>">
              <span class="up">+</span>
            </div>
            <br>
            <span class="del delete" prod-id="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>">Удалить</span>
          </td>
          <td>
          	<span class="price" name="priceQtSumm-<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>"><?php if(isset($this->vars['item']['price_full'])) echo $this->vars['item']['price_full']; ?></span>
            
          </td>
        </tr>
        <?php endforeach; ?><?php endif; ?>
      </tbody>
    </table>
    <div class="horisontalDivider"></div>
    <div class="carSummary">
      <div class="leftBlockSummary">
        <p>Тут должен быть небольшой текст про доставку и оплату.</p>
        <div class="contextLink"><a href="/delivery.html">Условия и стоимость доставки</a></div>
        <p>Небольшой текст про возврат товара</p>
        <div class="contextLink"><a href="/return.html">Условия возврата покупки</a></div>

    
        <div class="returnLink">
        	<span class="arrowLeft"><span class="c1">&lt;</span><span class="c2">&mdash;</span></span>
        	<a href="<?php if(isset($this->vars['backurl'])) echo $this->vars['backurl']; ?>">Продолжить покупки</a>
        </div>
      </div>
      <div class="rightBlockSummary">
        <table>
          <tr>
            <td class="l">Всего:</td>
            <td class="r"><span name="priceCartSummary"><?php if(isset($this->vars['cartInfo']['sum'])) echo $this->vars['cartInfo']['sum']; ?></span></td>
          </tr>
          <tr>
            <td class="l">Доставка:</td>
            <td class="r"><span>0 р.</span></td>
          </tr>
          <tr>
            <td class="l"><b>Итого:</b></td>
            <td class="r"><b><span name="priceCartSummary"><?php if(isset($this->vars['cartInfo']['sum'])) echo $this->vars['cartInfo']['sum']; ?></span></b></td>
          </tr>
          <tr>
            <td colspan="2" class="orderNext">
              <a href="/ru/shop/place_an_order/" class="btnOrange">Оформить заказ</a>
            </td>
          </tr>
        </table>
      </div>
    </div>
    
    <br style="clear:both">
    

    
    <?php else: ?>
		<h1 style="padding-top:25px; text-align:center; font-size:36px;padding-bottom:40px;">Ваша корзина пуста.</h1>
    <?php endif; ?>
  </div>
</div>



<script type="text/javascript">
  function updateCartCheckout(data) {
    $('#priceCartSummary').html(data.sum);
    $('#priceCartSummary2').html(data.sum);
    for(var i in data.list) {
      $('#cartProdListTable span[name="priceQtSumm-'+i+'"]').html(data.list[i].price_full);
    }
  }

  $(document).ready(function(){
    $('#cartProdListTable input[name="qt"]').bind('change',function(){
      changeCartQt($(this).attr('prod-id'), $(this).val(), updateCartCheckout);
    });

    $('#cartProdListTable .delete').bind('click',function(){
      var prod = $(this).attr('prod-id');
      $(this).parents('tr').fadeOut(200, function(){ $(this).remove()});
      removeProductFromCart(prod);
    });

    $('#cartProdListTable .quantity .up').bind('click',function(){
      var cntInput = $(this).closest('.quantity').find('input[name="qt"]');
      var cnt = parseInt(cntInput.val());
      if(isNaN(cnt) || cnt == undefined) cnt = 0;
      cnt++;
      cntInput.val(cnt).trigger('change');
    });
    
    $('#cartProdListTable .quantity .down').bind('click',function(){
      var cntInput = $(this).closest('.quantity').find('input[name="qt"]');
      var cnt = parseInt(cntInput.val());
      if(isNaN(cnt) || cnt == undefined) cnt = 0;
      if(cnt == 0) return;
      cnt--;
      cntInput.val(cnt).trigger('change');
    });

  });

</script>
    
   
