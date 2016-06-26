<? $this->md5_compil='e07f560db50f6550f1d556b3a0546144'; ?>
<!--
      <?php if (isset($this->vars['userInfo']['company_id']) && $this->vars['userInfo']['company_id']  ): ?>Юр. лицо<?php else: ?>Физ. лицо<?php endif; ?>
      <?php if(isset($this->vars['userInfo']['contact']['name'])) echo $this->vars['userInfo']['contact']['name']; ?>
      <?php if(isset($this->vars['userInfo']['contact']['surname'])) echo $this->vars['userInfo']['contact']['surname']; ?>
      <?php if(isset($this->vars['userInfo']['contact']['phone'])) echo $this->vars['userInfo']['contact']['phone']; ?>
      <?php if(isset($this->vars['userInfo']['contact']['email'])) echo $this->vars['userInfo']['contact']['email']; ?>
      <?php if(isset($this->vars['userInfo']['contact']['company_name'])) echo $this->vars['userInfo']['contact']['company_name']; ?>
-->





<script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/md5.min.js"></script>
<div class="mainContentBlockFullWidth">
  <div class="contentLeft">
    <div class="title">
      <?php echo $this->get("order.top.html"); ?>
    </div>
    <div class="body">
      
      <div class="borderedTwoCol borderBottom">
        <div class="colLeft">
          <div class="blockContent">
            <div class="checkBoxPs checked" name="legalForm" value="1">
              <div class="icon"></div>
              <div class="label">Доставка по Москве</div> 
            </div>
          </div>
        </div>
        <div class="colRight">
          <div class="blockContent">
            <div class="checkBoxPs" name="legalForm" value="2">
              <div class="icon"></div>
              <div class="label">Доставка в регионы</div> 
            </div>
          </div>
        </div>
      </div>
      
      <div class="formbody" formbody="1">
        <div class="borderedTwoCol woborder" style="margin-top:10px;">
          <div class="register colLeft" style="width:69%">
            <div class="blockContent">
              <h1>Адрес доставки</h1>
              <h3 class="highliteIfError">Заполните все поля отмеченные звездочкой.</h3>
              <p class="errorRegisterMessage"></p>
              <form class="form-horizontal long" id="addressForm1">
                <input type="hidden" name="address-id" value="<?php if(isset($this->vars['mainAddress']['address_id'])) echo $this->vars['mainAddress']['address_id']; ?>">
                <div class="control-group">
                  <label class="control-label">Улица <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="street" value="<?php if(isset($this->vars['mainAddress']['street'])) echo $this->vars['mainAddress']['street']; ?>" /></div>
                </div>
                <div class="control-group">
                  <label class="control-label">Адрес <span>*</span></label>
                  
                  <div class="controls controls-multi">
                    <label class="control-label">дом </label>
                  	<input class="span8" type="text" name="house" value="<?php if(isset($this->vars['mainAddress']['house'])) echo $this->vars['mainAddress']['house']; ?>" />
                  </div>
                  <div class="controls controls-multi">
                    <label class="control-label">корп. / стр.</label>
                  	<input class="span8" type="text" name="building" value="<?php if(isset($this->vars['mainAddress']['building'])) echo $this->vars['mainAddress']['building']; ?>" />
                  </div>
                  <div class="controls controls-multi">
                    <label class="control-label">оф. / кв. </label>
                  	<input class="span8" type="text" name="flat" value="<?php if(isset($this->vars['mainAddress']['flat'])) echo $this->vars['mainAddress']['flat']; ?>" />
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Контактный телефон <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="phone" value="<?php if(isset($this->vars['userInfo']['contact']['phone'])) echo $this->vars['userInfo']['contact']['phone']; ?>" /></div>
                </div>
                <div class="control-group">
                  <label class="control-label">Дополнительный телефон</label>
                  <div class="controls"><input class="span8" type="text" name="phone2" <?php if (isset($this->vars['preorde']) && $this->vars['preorde']  ): ?>value="<?php if(isset($this->vars['preorde']['order_phone2'])) echo $this->vars['preorde']['order_phone2']; ?>"<?php endif; ?> /></div>
                </div>
                <div class="control-group">
                  <label class="control-label" style="vertical-align: top;">Коментарий</label>
                  <div class="controls">
                    <textarea name="comment"><?php if (isset($this->vars['preorde']) && $this->vars['preorde']  ): ?><?php if(isset($this->vars['preorde']['order_comment'])) echo $this->vars['preorde']['order_comment']; ?><?php endif; ?></textarea>
                  </div>
                </div>
              </form>
            </div>
          </div>
          
          
          <div class="login colRight" style="width:30%">
            <div class="blockContent">
              <form class="form-horizontal" style="margin-top:76px;" id="adressInsertForm1">
                <?php if (isset($this->vars['allAddresses']) && $this->vars['allAddresses']  ): ?>
                	<h1>Вы можете выбрать один из ранее указаных адресов</h1>
                	<div class="addressSeselect">
                	<?php if(count($this->vars['allAddresses'])) : 
							   		 $this->vars['foreach']['allAddresses']['count'] = count($this->vars['allAddresses']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['allAddresses']);
							   		 foreach($this->vars['allAddresses'] as $this->vars['foreach']['allAddresses']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['allAddresses']['key']; ?>
                      <div class="addressItem"><a href="javascript:void(0)" data-id="<?php if(isset($this->vars['item']['address_id'])) echo $this->vars['item']['address_id']; ?>" class="link"><?php if (isset($this->vars['item']['zip']) && $this->vars['item']['zip']  ): ?><?php if(isset($this->vars['item']['zip'])) echo $this->vars['item']['zip']; ?>, <?php endif; ?><?php if(isset($this->vars['item']['city'])) echo $this->vars['item']['city']; ?>, <?php if(isset($this->vars['item']['street'])) echo $this->vars['item']['street']; ?>, <?php if(isset($this->vars['item']['house'])) echo $this->vars['item']['house']; ?></a></div>
                	<?php endforeach; ?><?php endif; ?>
                  </div>
                <?php endif; ?>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      <div class="formbody" formbody="2" style="display:none">
       <div class="borderedTwoCol woborder" style="margin-top:10px;">
          <div class="register colLeft" style="width:69%">
            <div class="blockContent">
              <h1>Адрес доставки</h1>
              <h3 class="highliteIfError">Заполните все поля отмеченные звездочкой.</h3>
              <p class="errorRegisterMessage"></p>
              <form class="form-horizontal long" id="addressForm2">
                <div class="control-group">
                  <label class="control-label">Индекс <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="zip" style="width:100px;" value="<?php if(isset($this->vars['mainAddress']['zip'])) echo $this->vars['mainAddress']['zip']; ?>"/></div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Область / Край</label>
                  <div class="controls"><input class="span8" type="text" name="region" value="<?php if(isset($this->vars['mainAddress']['region'])) echo $this->vars['mainAddress']['region']; ?>" /></div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Город <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="city" value="<?php if(isset($this->vars['mainAddress']['city'])) echo $this->vars['mainAddress']['city']; ?>"/></div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Улица <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="street" value="<?php if(isset($this->vars['mainAddress']['street'])) echo $this->vars['mainAddress']['street']; ?>"/></div>
                </div>
                <div class="control-group">
                  <label class="control-label">Точный адрес <span>*</span></label>
                  
                  <div class="controls controls-multi">
                    <label class="control-label">дом </label>
                  	<input class="span8" type="text" name="house" value="<?php if(isset($this->vars['mainAddress']['house'])) echo $this->vars['mainAddress']['house']; ?>"/>
                  </div>
                  <div class="controls controls-multi">
                    <label class="control-label">корп. / стр.</label>
                  	<input class="span8" type="text" name="building" value="<?php if(isset($this->vars['mainAddress']['building'])) echo $this->vars['mainAddress']['building']; ?>"/>
                  </div>
                  <div class="controls controls-multi">
                    <label class="control-label">оф. / кв. </label>
                  	<input class="span8" type="text" name="flat" value="<?php if(isset($this->vars['mainAddress']['flat'])) echo $this->vars['mainAddress']['flat']; ?>"/>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Контактный телефон <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="phone" value="<?php if(isset($this->vars['userInfo']['contact']['phone'])) echo $this->vars['userInfo']['contact']['phone']; ?>" /></div>
                </div>
                <div class="control-group">
                  <label class="control-label">Дополнительный телефон</label>
                  <div class="controls"><input class="span8" type="text" name="phone2" <?php if (isset($this->vars['preorde']) && $this->vars['preorde']  ): ?>value="<?php if(isset($this->vars['preorde']['order_phone2'])) echo $this->vars['preorde']['order_phone2']; ?>"<?php endif; ?>/></div>
                </div>
                <div class="control-group">
                  <label class="control-label" style="vertical-align: top;">Коментарий</label>
                  <div class="controls">
                    <textarea name="comment"><?php if (isset($this->vars['preorde']) && $this->vars['preorde']  ): ?><?php if(isset($this->vars['preorde']['order_comment'])) echo $this->vars['preorde']['order_comment']; ?><?php endif; ?></textarea>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="login colRight" style="width:30%">
            <div class="blockContent">
              <form class="form-horizontal" style="margin-top:76px;" id="adressInsertForm2">
                <?php if (isset($this->vars['allAddresses']) && $this->vars['allAddresses']  ): ?>
                	<h1>Вы можете выбрать один из ранее указаных адресов</h1>
                	<div class="addressSeselect">
                	<?php if(count($this->vars['allAddresses'])) : 
							   		 $this->vars['foreach']['allAddresses']['count'] = count($this->vars['allAddresses']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['allAddresses']);
							   		 foreach($this->vars['allAddresses'] as $this->vars['foreach']['allAddresses']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['allAddresses']['key']; ?>
                      <div class="addressItem"><a href="javascript:void(0)" data-id="<?php if(isset($this->vars['item']['address_id'])) echo $this->vars['item']['address_id']; ?>" class="link"><?php if (isset($this->vars['item']['zip']) && $this->vars['item']['zip']  ): ?><?php if(isset($this->vars['item']['zip'])) echo $this->vars['item']['zip']; ?>, <?php endif; ?><?php if(isset($this->vars['item']['city'])) echo $this->vars['item']['city']; ?>, <?php if(isset($this->vars['item']['street'])) echo $this->vars['item']['street']; ?>, <?php if(isset($this->vars['item']['house'])) echo $this->vars['item']['house']; ?></a></div>
                	<?php endforeach; ?><?php endif; ?>
                  </div>
                <?php endif; ?>
              </form>
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
        <a href="/ru/shop/place_an_order/" class="btnGray btnLeft" id="prevButton"><span class="arrowLeft"></span> назад </a>
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
    $('div[name="legalForm"]').each(function(){
      $(this).find('.icon').bind('click',function(){
        if($(this).parent().is('.checked')) return true;
        var value = $(this).parent().attr('value');
        $('div.checked[name="'+$(this).parent().attr('name')+'"]').removeClass('checked');
        $(this).parent().addClass('checked');
        $('div.formbody').hide();
        $('div.formbody[formbody="'+value+'"]').fadeIn();
      });
    });
    
    //$('div[name="news"]').find('.icon').bind('click', function(){
    //  $(this).parent().toggleClass('checked');
    //});

    $('#nextButton').bind('click', function(){
      var type = $('div.checked[name="legalForm"]').attr('value');
      var form = $('div.formbody[formbody="'+type+'"]');
      
      var data = {
        type: type,
        zip: (type == 2)? form.find('input[name="zip"]').val() : '',
        city:  (type == 2)? form.find('input[name="city"]').val() : 'Москва',
        region: (type == 2)? form.find('input[name="region"]').val() : '',
        street: form.find('input[name="street"]').val(),
        house: form.find('input[name="house"]').val(),
        building: form.find('input[name="building"]').val(),
        flat: form.find('input[name="flat"]').val(),
        phone: form.find('input[name="phone"]').val(),
        phone2: form.find('input[name="phone2"]').val(),
        comment: form.find('textarea[name="comment"]').val()
      };
      
      if(form.find('input[name="address-id"]') && form.find('input[name="address-id"]').val()) {
        data.id = form.find('input[name="address-id"]').val();
      }
      
	  
      
      var error = false;
      form.find('.errorRegisterMessage').hide();
      
      if(data.type == 2 && (!data.zip || data.zip.length != 6)) {
        error = true;
       	errorInput('zip', form);
      } else {
        errorInput('zip', form, true);
      }
      
      if(data.type == 2 && (!data.city || data.city.length < 2)) {
        error = true;
       	errorInput('city', form);
      } else {
        errorInput('city', form, true);
      }
      
      if(!data.street || data.street.length < 3) {
        error = true;
       	errorInput('street', form);
      } else {
        errorInput('street', form, true);
      }
      
      if(!data.house) {
        error = true;
       	errorInput('house', form);
      } else {
        errorInput('house', form, true);
      }
      
      if(!data.phone) {
        error = true;
       	errorInput('phone', form);
      } else {
        errorInput('phone', form, true);
      }
      

      
      
      if(error) {
       	if(!form.find('.highliteIfError').is('.error')) form.find('.highliteIfError').addClass('error');
      } else {
        form.find('.highliteIfError').removeClass('error');

        saveUserAddress(data, $(this), function(errorCode, errorMessage){
          form.find('.errorRegisterMessage').html(errorMessage).slideToggle();
        }, function(result) {
          form.find('.errorRegisterMessage').hide();
          document.location.href = '/ru/shop/place_an_order/step/3/';
        });
      }
      
    });    
  });
  
  function errorInput(name, form, clear) {
    var input = form.find('input[name="'+name+'"]');
    if(clear === true) {
       input.closest('.control-group').removeClass('error');
    } else {
       if(!input.closest('.control-group').is('.error')) input.closest('.control-group').addClass('error');
    }
  }
  
</script>





