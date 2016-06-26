<? $this->md5_compil='d7bc03efd2d80f2446764dda3a325f58'; ?>
<?php if (isset($this->vars['userInfo']) && $this->vars['userInfo']  ): ?>
<!-- ajax defined -->
<script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>cabinet/js/ajax.js"></script>
<?php if(isset($this->vars['majax'])) echo $this->vars['majax']; ?>

<!-- cabinet scripts -->
<script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>cabinet/js/overlay.js"></script>
<script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>cabinet/js/twig.min.js"></script>
<script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>cabinet/js/director.js"></script>
<script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>cabinet/js/main.js"></script>
<script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>cabinet/js/routes.js"></script>
<!-- <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>cabinet/js/nodechat.js"></script>
<script type="text/javascript" src="http://<?php if(isset($this->vars['SERVER_NAME'])) echo $this->vars['SERVER_NAME']; ?>:8080/socket.io/socket.io.js" id="socketio"></script> -->
<?php endif; ?>


<link type="text/css" rel="stylesheet" href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>cabinet/css/style.css" media="screen"/>


<script type="text/javascript">
  
  var cabinetStaticPath = '<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>cabinet/';
  $(document).ready(function(){
  
    cabinet.init(routes);
  });
</script>




<div class="cabinetMain">
  <div class="contentLeft">
    <div class="cabinetMenu">
      <ul>
        <?php if (isset($this->vars['userInfo']) && $this->vars['userInfo']  ): ?>
        <li alias="main_page"><a href="#/"><i class="icon user"></i> Панель управления</a></li>
        <li alias="personal"><a href="#/my"><i class="icon vcard"></i> Персональные данные</a></li>
        <!--<li alias="addresslist"><a href="#/address"><i class="icon home"></i> Адреса доставки</a></li>
        <li alias="discount"><a href="#/discount"><i class="icon discount"></i> Ценовая категория</a></li>-->
        <!--<li alias="billing"><a href="#/billing_information"><i class="icon billing"></i> Реквизиты юр. лица</a></li>-->
        <li alias="history"><a href="#/history"><i class="icon box"></i> История заказов</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
  <div class="contentCenter">
    <div class="header" <?php if (isset($this->vars['userInfo']) && $this->vars['userInfo']  ): ?><?php else: ?>style="padding-top:10px;"<?php endif; ?>>
      <!--<?php print_r($this->vars['userInfo']) ?>-->
      <?php if (isset($this->vars['userInfo']) && $this->vars['userInfo']  ): ?>
      <h1><img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>cabinet/images/login_user_icon.png"><span><?php if(isset($this->vars['userInfo']['contact']['surname'])) echo $this->vars['userInfo']['contact']['surname']; ?> <?php if(isset($this->vars['userInfo']['contact']['name'])) echo $this->vars['userInfo']['contact']['name']; ?></span></h1>
      <a href="javascript:void(0)" action="logout" class="button gray">выйти</a>
      <?php else: ?>
      <p class="errorRegisterMessage" style="display:block"><b>Ошибка 401 - Вы не авторизованы.</b><br>Для доступа в этот раздел Вам необходимо пройти <a href="/login/">авторизацию</a>.</p>
      <?php endif; ?>
    </div>
    <div id="cabinetMainContent"></div>
  </div>
  
  <!--<div class="contentRight">
    <?php if (isset($this->vars['userInfo']) && $this->vars['userInfo']  ): ?>
    <?php if (isset($this->vars['Manager']) && $this->vars['Manager']  ): ?> 
    <div id="nodeChatbox" data-user_name="<?php if(isset($this->vars['userInfo']['contact']['name'])) echo $this->vars['userInfo']['contact']['name']; ?>" data-user_surname="<?php if(isset($this->vars['userInfo']['contact']['surname'])) echo $this->vars['userInfo']['contact']['surname']; ?>" data-manager_name="<?php if(isset($this->vars['Manager']['name_first'])) echo $this->vars['Manager']['name_first']; ?>"  data-manager_surname="<?php if(isset($this->vars['Manager']['name_last'])) echo $this->vars['Manager']['name_last']; ?>">
      <script type="text/template" name="userMessageTemplate">
          <div class="message">
            <div class="title">
              <span class="uname">{{name}}</span>
              <span class="date">{{date}}</span>
     	    </div> 
            <div class="body">{{text}}</div>
      	  </div>
      </script>
      <script type="text/template" name="userMessageincommingTemplate">
          <div class="message incoming">
            <div class="title">
              <span class="uname">{{name}}</span>
              <span class="date">{{date}}</span>
            </div>
            <div class="body">{{text}}</div>
          </div>
      </script>
      
      <div class="chatHeader">
        <h1><i class="icon user"></i> <?php if(isset($this->vars['Manager']['name_first'])) echo $this->vars['Manager']['name_first']; ?> <?php if(isset($this->vars['Manager']['name_last'])) echo $this->vars['Manager']['name_last']; ?></h1>
        <span>Персональный менеджер</span>
      </div>
      
      <div class="chatbox"></div>
      
      <div class="sendBox">
        <textarea name="messageText"></textarea>
        <a href="javascript:void(0)" action="sendMessage" class="button blue">Отправить</a>
      </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>-->
  
</div>