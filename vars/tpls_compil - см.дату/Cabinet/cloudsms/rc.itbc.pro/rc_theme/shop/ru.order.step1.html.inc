<? $this->md5_compil='3b7ab7ebf52e20fb613973f22a87912f'; ?>
<script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/md5.min.js"></script>
<div class="mainContentBlockFullWidth">
  <div class="contentLeft">
    <div class="title">
      <?php echo $this->get("order.top.html"); ?>
    </div>
    <div class="body">      
      <?php if (isset($this->vars['userInfo']['user_id']) && $this->vars['userInfo']['user_id']  ): ?>
      <div class="borderedTwoCol">
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
              <a href="/ru/shop/place_an_order/step/2/" class="btnBlue">Продолжить <span class="arrowRight"></span></a>
            </div>
          </div>
        </div>
      </div>
          
      <?php else: ?>
      
      <div class="borderedTwoCol">
        <p class="errorRegisterMessage"></p>
        <div class="register colLeft">
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
              <a href="/ru/shop/place_an_order/step/1/op/register/" class="btnBlue">зарегистрироваться <span class="arrowRight"></span></a>
            </div>
          </div>
        </div>
        <div class="login colRight">
          <div class="blockContent">
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
              	<a href="#" class="smallLink">Забыли пароль?</a>
              </div>
            </form>
            
          </div>
        </div>
      </div>
      <?php endif; ?>
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
    $('#logoutOrder').bind('click', function(){
      ajaxLogout(function(){
        document.location.href = '/ru/shop/place_an_order/';
      });
    });
    $('#loginButton').bind('click',function(){
      $('.mainContentBlockFullWidth .errorRegisterMessage').hide();
      ajaxLoginCheck(
        $('#loginForm input[name="email"]').val(), 
        $('#loginForm input[name="password"]').val(), 
        $(this),
        function(code, message){
          $('.mainContentBlockFullWidth .errorRegisterMessage').html(message).slideToggle();
        }, function() {
          document.location.href = '/ru/shop/place_an_order/step/2/';
        }
      );
        
    });
  });
</script>



