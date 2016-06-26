<? $this->md5_compil='8389d0e2fae01f116db7cce35b6e67e7'; ?>
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
              <div class="label">Физическое лицо</div> 
            </div>
          </div>
        </div>
        <div class="colRight">
          <div class="blockContent">
            <div class="checkBoxPs" name="legalForm" value="2">
              <div class="icon"></div>
              <div class="label">Юридическое лицо</div> 
            </div>
          </div>
        </div>
      </div>
      
      <div class="formbody" formbody="1">
        <div class="borderedTwoCol woborder" style="margin-top:10px;">
          <div class="register colLeft">
            <div class="blockContent">
              <h1>Персональная информация</h1>
              <h3 class="highliteIfError">Заполните все поля отмеченные звездочкой.</h3>
              <p class="errorRegisterMessage"></p>
              <form class="form-horizontal">
                <div class="control-group">
                  <label class="control-label">Фамилия <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="surname" /></div>
                </div>
                <div class="control-group">
                  <label class="control-label">Имя <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="name" /></div>
                </div>
                <div class="control-group">
                  <label class="control-label">Отчество <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="lastname" /></div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Телефон <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="phone" /></div>
                </div>
              </form>
            </div>
          </div>
          <div class="login colRight">
            <div class="blockContent">
              <form class="form-horizontal" style="margin-top:76px;">
                <div class="control-group">
                  <label class="control-label">Ваш E-mail <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="email" /></div>
                </div>
                <div class="control-group">
                  <label class="control-label">Ваш пароль <span>*</span><br><span class="hint">мин. 6 символов</span></label>
                  <div class="controls"><input class="span8" type="password" name="password" /></div>
                </div>
                <div class="control-group">
                  <label class="control-label">Подтвердите пароль <span>*</span></label>
                  <div class="controls"><input class="span8" type="password" name="password-retype" /></div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      <div class="formbody" formbody="2" style="display:none">
        <div class="borderedTwoCol woborder" style="margin-top:10px;">
          <div class="register colLeft">
            <div class="blockContent">
              <h1>Персональная информация</h1>
              <h3 class="highliteIfError">Заполните все поля отмеченные звездочкой.</h3>
              <p class="errorRegisterMessage"></p>
              <form class="form-horizontal">
                <div class="control-group">
                  <label class="control-label">Название компании <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="company_name" /></div>
                </div>
                <div class="control-group">
                  <label class="control-label">Имя<span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="name" /></div>
                </div>
                <div class="control-group">
                  <label class="control-label">Фамилия<span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="surname" /></div>
                </div>
                <div class="control-group">
                  <label class="control-label">Телефон <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="phone" /></div>
                </div>
              </form>
            </div>
          </div>
          <div class="login colRight">
            <div class="blockContent">
              <form class="form-horizontal" style="margin-top:76px;">
                <div class="control-group">
                  <label class="control-label">E-mail <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="email" /></div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Ваш пароль <span>*</span><br><span class="hint">мин. 6 символов</span></label>
                  <div class="controls"><input class="span8" type="password" name="password" /></div>
                </div>
                <div class="control-group">
                  <label class="control-label">Подтвердите пароль <span>*</span></label>
                  <div class="controls"><input class="span8" type="password" name="password-retype" /></div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
        
      <div class="formbody" formbody="2" style="display:none">
        <div class="borderedTwoCol woborder" style="margin-top:10px;">
          <div class="register colLeft">
            <div class="blockContent">
              <h1>Реквизиты компании</h1>
              <p class="errorRegisterMessage"></p>
              <form class="form-horizontal">
                <div class="control-group">
                  <label class="control-label">ИНН <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="inn" /></div>
                </div>
                <div class="control-group">
                  <label class="control-label">КПП </label>
                  <div class="controls"><input class="span8" type="text" name="kpp" /></div>
                </div>
                <div class="control-group">
                  <label class="control-label">ОГРН (ОГРНИП)<span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="ogrn" /></div>
                </div>
                <div class="control-group">
                  <label class="control-label">Юр. Адрес <span>*</span></label>
                  <div class="controls"><input class="span8" type="text" name="office-address" /></div>
                </div>
              </form>
            </div>
          </div>
          <div class="login colRight">
            <div class="blockContent">
              <form class="form-horizontal" style="margin-top:54px;">
                <div class="control-group">
                  <label class="control-label">Название банка </label>
                  <div class="controls"><input class="span8" type="text" name="bank" /></div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">БИК </label>
                  <div class="controls"><input class="span8" type="text" name="bik" /></div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Рс. сч. </label>
                  <div class="controls"><input class="span8" type="text" name="bill" /></div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Кор. сч. </label>
                  <div class="controls"><input class="span8" type="text" name="kor" /></div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
        
      
      
      <div class="borderedTwoCol" style="margin-top:10px;">
        <div class="register colLeft woborder" >
          <div class="blockContent">
      		<div class="checkBoxPs boxBorder checked" name="news" value="1">
              <div class="icon"></div>
              <div class="label">Получать новости интернет-магазина</div> 
            </div>
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
    
    $('div[name="news"]').find('.icon').bind('click', function(){
      $(this).parent().toggleClass('checked');
    });

    $('#nextButton').bind('click', function(){
      var type = $('div.checked[name="legalForm"]').attr('value');
      var form = $('div.formbody[formbody="'+type+'"]');
      
      var data = {
        type: type,
        password: form.find('input[name="password"]').val(),
        passwordre: form.find('input[name="password-retype"]').val(),
        phone: form.find('input[name="phone"]').val(),
        email: form.find('input[name="email"]').val(),
        name: form.find('input[name="name"]').val(),
        surname: form.find('input[name="surname"]').val(),
        lastname: form.find('input[name="lastname"]').val(),
        comapny: (type == 2)? form.find('input[name="company_name"]').val() : '',
        
        
      };
      
      var error = false;
      form.find('.errorRegisterMessage').hide();
      
      if(!data.surname) {
       	error = true;
       	if(!form.find('input[name="surname"]').parent().parent().is('.error')) form.find('input[name="surname"]').parent().parent().addClass('error');
      } else {
        form.find('input[name="surname"]').parent().parent().removeClass('error');
      }
      
      if(!data.name) {
       	error = true;
       	if(!form.find('input[name="name"]').parent().parent().is('.error')) form.find('input[name="name"]').parent().parent().addClass('error');
      } else {
        form.find('input[name="name"]').parent().parent().removeClass('error');
      }
      
      /*
      if(!data.lastname) {
       	error = true;
       	if(!form.find('input[name="lastname"]').parent().parent().is('.error')) form.find('input[name="lastname"]').parent().parent().addClass('error');
      } else {
        form.find('input[name="lastname"]').parent().parent().removeClass('error');
      }*/
      
      
      if(!data.email) {
       error = true;
       	if(!form.find('input[name="email"]').parent().parent().is('.error')) form.find('input[name="email"]').parent().parent().addClass('error');
      } else {
        form.find('input[name="email"]').parent().parent().removeClass('error');
      }
      
      if(!data.phone) {
       error = true;
       	if(!form.find('input[name="phone"]').parent().parent().is('.error')) form.find('input[name="phone"]').parent().parent().addClass('error');
      } else {
        form.find('input[name="phone"]').parent().parent().removeClass('error');
      }
      
      if(!data.password || data.password.length < 6) {
       error = true;
       	if(!form.find('input[name="password"]').parent().parent().is('.error')) form.find('input[name="password"]').parent().parent().addClass('error');
      } else {
        form.find('input[name="password"]').parent().parent().removeClass('error');
      }
      
      if(!data.passwordre || data.passwordre != data.password) {
      	error = true;
       	if(!form.find('input[name="password-retype"]').parent().parent().is('.error')) form.find('input[name="password-retype"]').parent().parent().addClass('error');
      } else {
        form.find('input[name="password-retype"]').parent().parent().removeClass('error');
      }
      
      if(type == 2 && !data.comapny) {
        error = true;
       	if(!form.find('input[name="company_name"]').parent().parent().is('.error')) form.find('input[name="company_name"]').parent().parent().addClass('error');
      } else {
        form.find('input[name="company_name"]').parent().parent().removeClass('error');
      }
      
      if(data.type == 2) {
        var company = {
          inn : form.find('input[name="inn"]').val(),
          kpp : form.find('input[name="kpp"]').val(),
          ogrn : form.find('input[name="ogrn"]').val(),
          bank : form.find('input[name="bank"]').val(),
          bik : form.find('input[name="bik"]').val(),
          kor : form.find('input[name="kor"]').val(),
          bill : form.find('input[name="bill"]').val(),
          address : form.find('input[name="office-address"]').val(),
        }
       	 
        if(!company.inn || (company.inn.length != 10 && company.inn.length != 12)) {
         	error = true;
       		if(!form.find('input[name="inn"]').parent().parent().is('.error')) form.find('input[name="inn"]').parent().parent().addClass('error');
        } else {
          	form.find('input[name="inn"]').parent().parent().removeClass('error');
        }
        
        if(!company.ogrn || (company.ogrn.length != 13 && company.ogrn.length != 15)) {
         	error = true;
       		if(!form.find('input[name="ogrn"]').parent().parent().is('.error')) form.find('input[name="ogrn"]').parent().parent().addClass('error');
        } else {
          	form.find('input[name="ogrn"]').parent().parent().removeClass('error');
        }
        
        if(!company.address) {
         	error = true;
       		if(!form.find('input[name="office-address"]').parent().parent().is('.error')) form.find('input[name="office-address"]').parent().parent().addClass('error');
        } else {
          	form.find('input[name="office-address"]').parent().parent().removeClass('error');
        }
      }
      
      data.company_info = company;
      
      
      
      if(error) {
       	if(!form.find('.highliteIfError').is('.error')) form.find('.highliteIfError').addClass('error');
      } else {
        form.find('.highliteIfError').removeClass('error');
        
        registerAndLogin(data, $(this), function(errorCode, errorMessage){
          form.find('.errorRegisterMessage').html(errorMessage).slideToggle();
        }, function(result) {
          form.find('.errorRegisterMessage').hide();
          document.location.href = '/ru/shop/place_an_order/step/2/';
        });
      }
      
    });    
  });
  
</script>





