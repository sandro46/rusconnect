<? $this->md5_compil='6f9e0d8e516c3947067a2a30a3b77b1c'; ?>
<script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/md5.min.js"></script>

<div class="mainContentBlockFullWidth" >
  
  
  <div class="contentLeft loginPage">
    <div class="help_blue_block_text">
      <h3>Возникли вопросы?</h3>
      <p>Наши менеджеры помогут вам по любым вопросам, просто позвоните нам:<br><strong>8 (800) 920 50 38</strong><br>или закажите<br><strong>обратный звонок</strong><br>или воспользуйтесь<br><strong>онлайн-поддержкой</strong></p>
    </div>
  </div>
  
  <div class="contentRight loginPage adaptRightContent">
    <div class="title2"><h1><img class="icon" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/login_user_icon.png"><span>Вход или регистрация</span></h1></div>
    
    <div class="body" style="margin-bottom:0;">            
      <div class="borderedTwoCol" style="border-right:none;">
        <p class="errorRegisterMessage"></p>
        <p class="successRegisterMessage"></p>  
        
        <?php if (isset($this->vars['userInfo']['user_id']) && $this->vars['userInfo']['user_id']  ): ?>

          <div class="register colLeft" style="border:none;">
            <div class="blockContent">
              <h1>Ваш персональный аккаунт</h1>
              <h3>Вы уже авторизованы на сайте.</h3><br>
              <h3>Ваши данные:</h3>

              <ul>
                <?php if (isset($this->vars['userInfo']['company_id']) && $this->vars['userInfo']['company_id']  ): ?><li><b>Компания: </b> <?php if(isset($this->vars['userInfo']['contact']['company_name'])) echo $this->vars['userInfo']['contact']['company_name']; ?></li><?php endif; ?>
                <li><b>Фамилия, имя: </b><?php if(isset($this->vars['userInfo']['contact']['name'])) echo $this->vars['userInfo']['contact']['name']; ?> <?php if(isset($this->vars['userInfo']['contact']['surname'])) echo $this->vars['userInfo']['contact']['surname']; ?></li>
                <li><b>Контактный телефон: </b> <?php if(isset($this->vars['userInfo']['contact']['phone'])) echo $this->vars['userInfo']['contact']['phone']; ?></li>
                <li><b>Контактный email: </b> <?php if(isset($this->vars['userInfo']['contact']['email'])) echo $this->vars['userInfo']['contact']['email']; ?></li>
              </ul>
              <br>
              <a href="javascript:void(0)" id="logoutOrder" class="smallLink">Войти под другой учетной записью?</a>
              <div class="btnContainer">
                <a href="/ru/cabinet/" class="btnBlue">Личный кабинет <span class="arrowRight"></span></a>
              </div>
            </div>
          </div>
   

        <?php else: ?>
        
        <div class="login colLeft">
          <div class="blockContent" style="padding-right: 40px; padding-left:15px;">
            <h1>Вход в персональный аккаунт</h1>
            <h3>Войти с вашим адресом электронной почты и паролем</h3>
            
            <form class="form-horizontal" id="loginForm">
              <div class="control-group">
                <label class="control-label">Адрес E-mail</label>
                <div class="controls"><input class="span8" type="text" name="email" /></div>
              </div>
              <div class="control-group">
                <label class="control-label">Ваш пароль</label>
                <div class="controls"><input class="span8" type="password" name="password" /></div>
              </div>
              <div class="btnContainer">
              	<a href="#" class="btnBlue" id="loginButton">войти <span class="arrowRight"></span></a>
              	<a href="#" class="smallLink" id="PassResetShow">Забыли пароль?</a>
              </div>
            </form>
            
          </div>
          
          <div class="blockContent" id="PassResetForm" style="padding-right: 40px; padding-left:15px; display:none;">
            <h1>Сбросить пароль</h1>
            <h3>Ввевдите E-mail, указанный при регистрации:</h3>
            
            <form class="form-horizontal" id="loginForm" style="margin-top:0;" >
              <div class="control-group">
                <label class="control-label">Адрес E-mail</label>
                <div class="controls"><input class="span8" type="text" name="email" /></div>
              </div>
              <div class="btnContainer">
              	<a href="#" class="btnBlue" id="PassResetButton">Сбросить <span class="arrowRight"></span></a>
              </div>
            </form>
            
          </div>
          
          
        </div>
        
        <div class="register  colRight">
          <div class="blockContent">
            <h1>Для новых покупателей</h1>
            <h3>Зарегистрировавшись на нашем сайте, вы сможете:</h3>
            <ul>
              <li>Быстро оформлять заказы</li>
              <li>Отслеживать статус и историю заказов</li>
              <li>Общаться с персональным менеджером</li>
              <li>Узнавать о новых поступлениях и акциях</li>
            </ul>
            <div class="btnContainer">
              <a href="/ru/shop/login/op/register/" class="btnBlue">зарегистрироваться <span class="arrowRight"></span></a>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript">
  $(document).ready(function(){
    
    $('#logoutOrder').bind('click', function(){
      ajaxLogout(function(){
        document.location.href = '/ru/shop/login/';
      });
    });
    
    $('#loginButton').bind('click',function(){
      $('.mainContentBlockFullWidth .errorRegisterMessage').slideUp();
      $('.mainContentBlockFullWidth .successRegisterMessage').slideUp();
      ajaxLoginCheck(
        $('#loginForm input[name="email"]').val(), 
        $('#loginForm input[name="password"]').val(), 
        $(this),
        function(code, message){
          $('.mainContentBlockFullWidth .errorRegisterMessage').html(message).slideDown();
          $('.mainContentBlockFullWidth .successRegisterMessage').html(message).slideUp();
        }, function() {
          document.location.href = '/ru/cabinet/';
        }
      );
    });
    
    $('#PassResetShow').bind('click', function(){
    	$('#PassResetForm').slideToggle();
      	$('.mainContentBlockFullWidth .errorRegisterMessage').slideUp();
    });
    
    $('#PassResetButton').bind('click',function(){
      ajaxPassReset(
        $('#PassResetForm input[name="email"]').val(), 
        $(this),
        function(code, message){
          $('.mainContentBlockFullWidth .successRegisterMessage').html(message).slideUp();
          $('.mainContentBlockFullWidth .errorRegisterMessage').html(message).slideDown();
        }, function(message) {
          $('.mainContentBlockFullWidth .errorRegisterMessage').html(message).slideUp();
          $('.mainContentBlockFullWidth .successRegisterMessage').html(message).slideDown();
        }
      );
    });
      
  });
</script>