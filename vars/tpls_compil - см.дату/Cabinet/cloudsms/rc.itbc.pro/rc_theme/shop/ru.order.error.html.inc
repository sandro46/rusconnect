<? $this->md5_compil='bd774ce971b70dd6fd4804227d94cb76'; ?>
<div class="mainContentBlockFullWidth">
  <div class="contentLeft">
    <div class="title">
      <?php echo $this->get("order.top.html"); ?>
    </div>
    <div class="body">
      <div class="errorRegisterMessage" style="display:block"><?php if(isset($this->vars['errorMessage'])) echo $this->vars['errorMessage']; ?></div>
      <br><br>
      <div class="btnContainer">
        <a href="javascript:void(0)" onclick="window.history.go(-1)" class="btnGray btnLeft" id="prevButton"><span class="arrowLeft"></span> назад </a>
        <br style="clear:both">
      </div>
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
