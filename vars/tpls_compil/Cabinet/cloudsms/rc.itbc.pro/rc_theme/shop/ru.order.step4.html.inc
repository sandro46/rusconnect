<? $this->md5_compil='3f69f83a9d0b06e05408e56209a6f69c'; ?>
<div class="mainContentBlockFullWidth">
  <div class="contentLeft">
    <div class="title">
      <?php echo $this->get("order.top.html"); ?>
    </div>
    <div class="body">
      
      <div class="borderedTwoCol">
        <div style="border:none;" class="register colLeft">
          <div class="blockContent">
            <h1>Ваш заказ #<?php if(isset($this->vars['order']['order_id'])) echo $this->vars['order']['order_id']; ?> сформирован!</h1>
    

            <ul>
              <li style="font-weight:normal"><b>Сумма заказа: </b><?php if(isset($this->vars['cartbox']['sum'])) echo $this->vars['cartbox']['sum']; ?></li>
              <li style="font-weight:normal"><b>Адрес доставки: </b><?php if(isset($this->vars['order']['short_address'])) echo $this->vars['order']['short_address']; ?></li>
              <li style="font-weight:normal"><b>Контактный телефон: </b> <?php if(isset($this->vars['order']['phone'])) echo $this->vars['order']['phone']; ?></li>
			  <li style="font-weight:normal"><b>Способ оплаты: </b> <?php if(isset($this->vars['order']['pay_type_name'])) echo $this->vars['order']['pay_type_name']; ?></li>
            </ul>
            <br>
            <div class="btnContainer">
              <a class="btnBlue" href="<?php if(isset($this->vars['order']['nextstep_action'])) echo $this->vars['order']['nextstep_action']; ?>" <?php if (isset($this->vars['order']['nextstep_click']) && $this->vars['order']['nextstep_click']  ): ?>onclick="<?php if(isset($this->vars['order']['nextstep_click'])) echo $this->vars['order']['nextstep_click']; ?>"<?php endif; ?>><?php if(isset($this->vars['order']['nextstep_name'])) echo $this->vars['order']['nextstep_name']; ?> <span class="arrowRight"></span></a>
            </div>
          </div>
        </div>
      </div>
          
      <?php if(isset($this->vars['payFormResult'])) echo $this->vars['payFormResult']; ?>
    </div>
  </div>
  <div class="contentRight">
  	<div class="title">
      <h3>Товары в корзине</h3>
    </div>
    <div class="body">
    	<?php echo $this->get("order.sidebar.html"); ?>
    </div>
  </div>
</div>
