<? $this->md5_compil='d38ccaad4ab177313170ae3c3d5477cd'; ?>
<div class="cartOrderTop">
  <div class="stephome">
    <a href="/"><img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/carthome.jpg"></a>
  </div>
  <div class="step <?php if (isset($this->vars['orderStep']) && $this->vars['orderStep'] >= 1): ?>active<?php endif; ?>">
    <a href="javascript:void(0)">1. Авторизация</a>
  </div>
  <div class="step <?php if (isset($this->vars['orderStep']) && $this->vars['orderStep'] >= 2): ?>active<?php endif; ?>">
    <a href="javascript:void(0)">2. Доставка</a>
  </div>
  <div class="step <?php if (isset($this->vars['orderStep']) && $this->vars['orderStep'] >= 3): ?>active<?php endif; ?>">
    <a href="javascript:void(0)">3. Оплата</a>
  </div>
  <div class="step <?php if (isset($this->vars['orderStep']) && $this->vars['orderStep'] >= 4): ?>active<?php endif; ?> final">
    <a href="javascript:void(0)">4. Подтверждение</a>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    var bgl = '<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/cartstep_active_left.png';
    var bgr = '<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/cartstep_active_right.png';
    var prevActive = false;
    var prevEm = false;
    $('.cartOrderTop div.step').each(function(index){
      if($(this).is('.active')) {
        $(this).css({'background-image':'url('+bgr+')'});
        
        if(index != 0) {
          if(prevActive) {
          	 prevEm.css({'background-image':'url('+bgl+')'});
          }
        } 
        
        prevActive = true;
        prevEm = $(this);
      } 
      
    });
  });  
  
</script>