<? $this->md5_compil='d74946d8f5d4a1f89b0b982a913b91c0'; ?>
<!--
      
    <pre>
    	UserInfo 
      <?php if (isset($this->vars['userInfo']['company_id']) && $this->vars['userInfo']['company_id']  ): ?>Юр. лицо<?php else: ?>Физ. лицо<?php endif; ?>
      <?php if(isset($this->vars['userInfo']['contact']['name'])) echo $this->vars['userInfo']['contact']['name']; ?>
      <?php if(isset($this->vars['userInfo']['contact']['surname'])) echo $this->vars['userInfo']['contact']['surname']; ?>
      <?php if(isset($this->vars['userInfo']['contact']['phone'])) echo $this->vars['userInfo']['contact']['phone']; ?>
      <?php if(isset($this->vars['userInfo']['contact']['email'])) echo $this->vars['userInfo']['contact']['email']; ?>
      <?php if(isset($this->vars['userInfo']['contact']['company_name'])) echo $this->vars['userInfo']['contact']['company_name']; ?>
	
    	Cartbox
        <?php print_r($this->vars['cartbox']) ?>
        
        Address
        <?php print_r($this->vars['address']) ?>
        
        OrderInfo
        <?php print_r($this->vars['orderInfo']) ?>
	</pre>
-->






<script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/md5.min.js"></script>
<div class="mainContentBlockFullWidth">
  <div class="contentLeft">
    <div class="title">
      <?php echo $this->get("order.top.html"); ?>
    </div>
    <div class="body">
      <!--
      <div class="borderedTwoCol borderBottom">
        <div class="colLeft woborder">
          <div class="blockContent">
            <div class="checkBoxPs checked" name="legalForm" value="1">
              <div class="icon"></div>
              <div class="label">Оплата заказа</div> 
            </div>
          </div>
        </div>
      </div>
	  -->      

      <div class="formbody" formbody="1">
        <div class="borderedTwoCol woborder" style="margin-top:10px;">
          <div class="register colLeft" style="width:85%">
            <div class="blockContent">
              <h1>Оплата заказа</h1>
              <h3>Выберите удобный для Вас способ оплаты.</h3>
              <p class="errorRegisterMessage"></p>
              <ul class="payType">
                
                <?php if (isset($this->vars['userInfo']['company_id']) && $this->vars['userInfo']['company_id']  ): ?>
                <li style="background-position: 0px 18px !important;" data-type="1">
                  <img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/invoice-icon.jpg" style="width: 50px; margin-right: 20px;">
                  <p style="padding-top:9px;">Безналичный расчет для Юр. лиц</p>
                  <span>Средства зачисляются в течении трех банковских дней</span>
                  <br style="clear:both">
                </li>
                <?php else: ?>
                <li data-type="2">
                  <img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/invoicephys.png">
                  <p>Оплата наличными в отделении Сбербанка</p>
                  <span>Средства зачисляются в течении пяти рабочих дней</span>
                  <br style="clear:both">
                </li>
                <?php endif; ?>
              </ul>
                <br><br>
                
                <?php if(count($this->vars['pay_systems'])) : 
							   		 $this->vars['foreach']['pay_systems']['count'] = count($this->vars['pay_systems']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['pay_systems']);
							   		 foreach($this->vars['pay_systems'] as $this->vars['foreach']['pay_systems']['key'] => $this->vars['p_category']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['pay_systems']['key']; ?>
                <h1><?php if(isset($this->vars['p_category']['name'])) echo $this->vars['p_category']['name']; ?></h1>
                
                	<ul class="payType">
                  <?php if(count($this->vars['p_category']['list'])) : 
							   		 $this->vars['foreach']['p_category.list']['count'] = count($this->vars['p_category']['list']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['p_category']['list']);
							   		 foreach($this->vars['p_category']['list'] as $this->vars['foreach']['p_category_list']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['p_category_list']['key']; ?>
                      <li class="logoAutoItem" data-type="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>">
                        <div class="paysystemlogo" style="background-image:url('<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?><?php if(isset($this->vars['item']['logo'])) echo $this->vars['item']['logo']; ?>')"></div>
                        <p style="padding-top:0px;"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></p>
                        <br style="clear:both">
                      </li>

                  <?php endforeach; ?><?php endif; ?>
                          </ul>
                <?php endforeach; ?><?php endif; ?>
                
          
               
              
              <input type="hidden" name="paytype" value="0">
            </div>
          </div>
        </div>
      </div>
      

        
      <div class="borderedTwoCol" style="margin-top:10px;">
        <div class="register colLeft woborder" >
          <div class="blockContent">
      		
          </div>
        </div>
      </div>
      <div class="btnContainer">
        <a href="/ru/shop/place_an_order/step/2/" class="btnGray btnLeft" id="prevButton"><span class="arrowLeft"></span> назад </a>
        <a href="#" class="btnBlue btnRight" id="nextButton">продолжить <span class="arrowRight"></span></a>
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





<script type="text/javascript">
  $(document).ready(function(){
    $('ul.payType li').bind('click', function(){
      	$(this).closest('.formbody').find('li.checked').removeClass('checked');
      	$(this).addClass('checked');
      	$('input[name="paytype"]').val($(this).data('type'));
    });
    $('#nextButton').bind('click', function(){
      $('.errorRegisterMessage').hide();
      var type = parseInt($('input[name="paytype"]').val());
      if(!type || isNaN(type)) {
         $('.errorRegisterMessage').html('Выберите способ оплаты').slideToggle();
      } else {
        document.location.href="/ru/shop/place_an_order/step/4/type/"+type+'/';
      }
    });    
  });
  

  
</script>







