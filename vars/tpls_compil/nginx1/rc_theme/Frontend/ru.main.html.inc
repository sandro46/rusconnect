<? $this->md5_compil='69794ef0b57ea1287f1d074372d6e245'; ?>
<!doctype html>
<html>
  <head>
    <meta charset="UTF-8">
    <title><?php if(isset($this->vars['title'])) echo $this->vars['title']; ?></title>
    <link type="text/css" rel="stylesheet" href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>css/styles.css" />
    <link type="text/css" rel="stylesheet" href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>css/jquery.jscrollpane.css" />
    <link type="text/css" rel="stylesheet" href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>css/jcarousel.basic.css" />
    
    
    <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/jquery-1.8.1.js"></script>
    <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/jquery.mousewheel.min.js"></script>
    <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/jquery.jscrollpane.min.js"></script>
    <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/jquery.jcarousel-core.min.js"></script>    
    <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/jquery.jcarousel-pagination.min.js"></script>
    <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/easyTooltip.js"></script>
    <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>source/jquery.fancybox.js?v=2.1.5"></script>
    <link rel="stylesheet" type="text/css" href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>source/jquery.fancybox.css?v=2.1.5" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>source/helpers/jquery.fancybox-buttons.css?v=1.0.5" />
    <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
    <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/script.js"></script>
    
    <meta name="robots"  content="noyaca"/>
    <meta name="description" content="<?php if(isset($this->vars['meta_description'])) echo $this->vars['meta_description']; ?>" />
    <meta name="keywords" content="<?php if(isset($this->vars['meta_keywords'])) echo $this->vars['meta_keywords']; ?>" />
    
    <?php if (isset($this->vars['current_page']) && $this->vars['current_page'] > 0): ?>
      <meta name="robots" content="noindex, follow" />
    <?php endif; ?>
  </head>
  <body>
    <script type="text/javascript">var $csrf = '<?php if(isset($this->vars['csrftoken'])) echo $this->vars['csrftoken']; ?>';</script>
    <div class="topMenuContainer" id="topMenuContainer">
      <div class="wrapper">
        
        <div id="mainHomeLink">
          <a href="/"></a>
        </div>
        
        <div id="catitem" item="1" class="categoryLightbox">
          <a href="#" class="item menuItemButton"></a>
        </div>
       
        <!-- <span class="devider"></span>   -->
        <!-- <div class="m-item categoryLightbox" item="2" id="catitem2"><a href="#" class="item" id="listitem">Категории</a></div> -->
        
        <div class="searchtop" id="serachContainer">
          <input type="text" placeholder="Название, артикул" />
          <a rel="nofolow" href="javascript:void(0)" class="findlink">&nbsp;</a>
          
          
          <div class="searchResult">
            <noindex>
              <div class="foundItems"></div>
              <div class="summary"><span>Всего найдено товаров: <i name="cartsummaryCount"></i></span></div>
              <div class="cartActions"><a href="javascript:void(0)"  class="link findlink" rel="nofollow">Показать все результаты</a></div>
              <div class="item resultItemTpl" style="display:none"> 
                <div class="cont">
                  <div class="thumb">
                    <img alt="" src="" style="width:56px" />
                  </div>
                  <div class="description">
                    <span class="title"></span>
                    <span class="price"></span>
                  </div>
                </div>
              </div> 
            </noindex>
          </div>
        </div>
        
        <div class="callinfo">
          <span class="phone"><a href="/contacts/" >8 (495) 221 73 50</a></span>
          <span class="worktime">
            Время работы:<br>
            пн-пт, с 9:00 до 17:00
          </span>
        </div>
        
        <div class="account">
        <noindex>
        <?php if (isset($this->vars['userInfo']['user_id']) && $this->vars['userInfo']['user_id']  ): ?>
          <a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> class="logined" href="/ru/cabinet/">
            <span class="acc">Аккаунт</span>
            <span class="actions active"><?php if(isset($this->vars['userInfo']['contact']['name'])) echo $this->vars['userInfo']['contact']['name']; ?> <?php if(isset($this->vars['userInfo']['contact']['surname'])) echo $this->vars['userInfo']['contact']['surname']; ?></span>
          </a>
          <a href="#" action="logout">выйти</a>
        <?php else: ?>
          <a rel="nofolow" href="/login/">
            <span class="acc">Аккаунт</span>
            <span class="bcc">ВОЙТИ</span>
            <span class="actions">Вход<br> или регистрация</span>
          </a>
        <?php endif; ?>
        </noindex>
        </div>  
             
        <div class="cart" id="cart_box_frame" data-summ="<?php if(isset($this->vars['cart_summary']['summ_float'])) echo $this->vars['cart_summary']['summ_float']; ?>" data-count="<?php if(isset($this->vars['cart_summary']['count'])) echo $this->vars['cart_summary']['count']; ?>">
          <span class="title">Корзина</span>
          <span class="cnt" name="cartsummaryCount"><?php if (isset($this->vars['cart_summary']['count']) && $this->vars['cart_summary']['count']  ): ?><?php if(isset($this->vars['cart_summary']['count'])) echo $this->vars['cart_summary']['count']; ?><?php else: ?>0<?php endif; ?></span>
          <span name="cartSummaryCleared" class="actions" <?php if (isset($this->vars['cart_summary']['count']) && $this->vars['cart_summary']['count']  ): ?>style="width: 115px;display:none"<?php else: ?>style="width: 115px;display:block"<?php endif; ?>>Ваша корзина<br> пуста</span>
          <span name="cartSummaryStat" class="actions showCart" <?php if (isset($this->vars['cart_summary']['count']) && $this->vars['cart_summary']['count']  ): ?>style="width: 115px; display:block"<?php else: ?>style="width: 115px; display:none"<?php endif; ?>>
            <b name="cartsummaryCount" class="cartsummaryCount"><?php if(isset($this->vars['cart_summary']['count'])) echo $this->vars['cart_summary']['count']; ?></b> <b class="cartsummaryCountDesc">товаров</b><br>
            <b name="cartsummarySumm" class="cartsummarySumm"><?php if(isset($this->vars['cart_summary']['sum'])) echo $this->vars['cart_summary']['sum']; ?></b>
          </span>
         
          <div class="fullcart">
            <div name="cartItems" class="cartItems scroll-pane jspScrollable" style="width:245px; height: 450px;"><?php echo $this->get("MainCartbox.html", "shop"); ?></div>
            <span class="deviderVert"></span>
            <div class="summary">
              <span>Всего товаров: <i name="cartsummaryCount"><?php if(isset($this->vars['cart_summary']['count'])) echo $this->vars['cart_summary']['count']; ?></i></span>
              <span>На сумму: <i name="cartsummarySumm"><?php if(isset($this->vars['cart_summary']['sum'])) echo $this->vars['cart_summary']['sum']; ?></i></span>
            </div>
            <div class="cartActions">
              <noindex><a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="/ru/shop/cart/" class="cartLink" rel="nofollow">в корзину</a></noindex>
              <noindex><a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="/ru/shop/place_an_order/" class="doOrder" rel="nofollow">оформить</a></noindex>
            </div>
          </div>
        </div>
               
        <div class="feed">
          <span class="icon"></span>                
          <span class="actions">
            Обратный<br>звонок
          </span>
          <div class="feedForm" id="callMeFormBlock">
            <p class="callNotSended" style="display:none">Для Вашего удобства, мы предлагаем Вам возможность заказать обратный звонок одного из наших менеджеров.</p>
            <p class="callOkSend" style="display:none">Мы получили Ваш запрос на обратный звонок. Наши менеджеры свяжутся с Вами в ближайшее время.</p>
            <p class="callSended" style="display:none">Вы недавно уже отправляли запрос на обратный звонок. Мы обязательно Вам перезвоним. Пожалуйста, подождите еще немного... </p>
            <p class="callError" style="display:none"></p>
            <form>
              <input type="text" name="phone" placeholder="Телефон" />
              <input type="text" name="name" placeholder="Контактное имя"/>
              <a href="javascript:void(0)" <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> onclick="callMe(); metrika('obratniy_zvonok');">позвонить мне</a>
            </form>
          </div>
        </div>
         
        <!-- <a class="support" <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="javascript:void(0)" onclick="jivo_api.open()">
          <span class="icon">
            <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/icon_support.png" />
          </span>                
          <span class="actions">
            Онлайн<br>
            поддержка
          </span>
        </a> -->
        
        <a class="help" href="/faq/" <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?>>
          <span class="icon"></span>                
          <span class="actions">Помощь<br>по сайту</span>
        </a>
        
      </div>
    </div>

    <div class="catalogPopupContainer">
      <div class="ajaxload"><img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/ajax-loader.gif"></div>
      <div class="closeBlock">
        <div class="closeButton"><span>×</span>Закрыть</div>
      </div>
    </div>
    
    <!--<div class="topMenuFluid"><div id="catitem" class=""></div></div>-->
   
   <?php if(isset($this->vars['content'])) echo $this->vars['content']; ?>
   

    <div class="serviceInformation contentContainer <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?>mainpage<?php endif; ?>">
      <div class="content">
        <a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="/sluzhba_dostavki.html" class="item">
          <div class="icon">
            <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/services_01.png" />
          </div>
          <div class="caption">
            <h3>Доставка</h3>
            <p>Быстро доставим груз в любой регион</p>
          </div>
        </a>           
        <a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="/assortiment_na_sklade.html" class="item">
          <div class="icon">
            <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/services_02.png" />
          </div>
          <div class="caption">
            <h3>Ассортимент на складе</h3>
            <p>На складе компании постоянно доступны тысячи товарных позиций</p>
          </div>
        </a>           
        <a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="/personal_nyj_menedzher.html" class="item">
          <div class="icon">
            <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/services_03.png" />
          </div>
          <div class="caption">
            <h3>Персональный менеджер</h3>
            <p>Высокое качество обслуживания каждого клиента. Индивидуальный подход</p>
          </div>
        </a>            
        <a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="/evropejskoe_kachestvo.html" class="item">
          <div class="icon">
            <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/services_04.png" />
          </div>
          <div class="caption">
            <h3>Высокое качество</h3>
            <p>Мы всегда ответственно подходим к данному вопросу</p>
          </div>
        </a>
      </div>
    </div>
    
    <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?>
    <div class="toPartners">
      <div class="offer">
        <h3>Партнерское предложение</h3>
        <p>Экспозиция для вашего магазина, адаптированная
          к ассортименту вашего магазина! Более 2000
          предлагаемых товаров. Взаимовыгодное
          сотрудничество!</p>
        <a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="/partner.html" class="link">узнать больше о предложении</a>
      </div>
      <div class="image">
        <span class="rcLogo">
          <a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="/partner.html"><img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/rcLogo.png" /></a>
        </span>
      </div>
    </div>
    <?php endif; ?>
    
    <div class="subscribe contentContainer <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?>mainpage<?php endif; ?>">
      <div class="content">
        <h2>ПОДПИСКА НА НОВОСТИ</h2>
        <p>Подписывайтесь и получайте информацию о новинках и акциях</p>
        <form name="subscription" id="subscriptionAction">
          <input type="text" name="email" placeholder="Введите e-mail" />
          <a href="javascript:void(0)" onclick="newsleter(this)">Подписаться</a>
        </form>
      </div>
    </div>

    <div class="footer contentContainer">
      <div class="menusCont">
        <ul class="menuShop">
          <li class="menuHeader">Интернет-магазин</li>
          <?php if($core->lib->widget("ShopMenu")): ?>
<?php $core->widgets->ShopMenu()->setting("menu_id", "1"); ?>

<?php $core->widgets->ShopMenu()->appendSettings(); $core->widgets->ShopMenu()->main(); endif; ?>
                      
          <?php if(count($this->vars['ShopMenuOut'])) : 
							   		 $this->vars['foreach']['ShopMenuOut']['count'] = count($this->vars['ShopMenuOut']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['ShopMenuOut']);
							   		 foreach($this->vars['ShopMenuOut'] as $this->vars['foreach']['ShopMenuOut']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['ShopMenuOut']['key']; ?>
          	<li><a href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>"><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></a></li>
          <?php endforeach; ?><?php endif; ?>
        </ul>        
        <ul class="menuCustomers">
          <li class="menuHeader">Покупателям</li>
          
          <?php if($core->lib->widget("ShopMenu")): ?>
<?php $core->widgets->ShopMenu()->setting("menu_id", "2"); ?>

<?php $core->widgets->ShopMenu()->appendSettings(); $core->widgets->ShopMenu()->main(); endif; ?>
                      
          <?php if(count($this->vars['ShopMenuOut'])) : 
							   		 $this->vars['foreach']['ShopMenuOut']['count'] = count($this->vars['ShopMenuOut']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['ShopMenuOut']);
							   		 foreach($this->vars['ShopMenuOut'] as $this->vars['foreach']['ShopMenuOut']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['ShopMenuOut']['key']; ?>
          	<li><a href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>"><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></a></li>
          <?php endforeach; ?><?php endif; ?>
        </ul>
        <ul class="menuCust">
          <li class="menuHeader">Наш склад<br>
            					ВЕШКИ ПАРК</li>
          <li>
            Россия, Московская область, Мытищинский район,<br>
            Липкинское шоссе, 2-й километр, вл. 7 стр. 1<br>
            ОЛЦ <b>"Вешки Парк"</b><br>
            <br>
          </li>
          <li><a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="/contact_map/">Как до нас доехать</a></li>
        </ul>        
        <ul class="contInfo">
          <li class="menuHeader">8 (495) 221-73-50<br>
            					8 (812) 649-19-49<br>
            					8 (925) 518-20-81</li>
          <li>
            Время работы офиса: пн-пт,<br>
            с 9:00 до 17:00<br>
            Время работы склада: пн-пт,<br>
            с 9:00 до 17:00<br>
            <br>
          </li>
          <li><a href="#" id="feedback">Обратный звонок</a></li>
        </ul>
        <!--<div class="secure">
          <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>samples/secure.png" />
        </div>-->
        
        <!-- Yandex.Metrika informer -->
        <a style="float:right" href="https://metrika.yandex.ru/stat/?id=26141199&amp;from=informer"
        target="_blank" rel="nofollow"><img src="https://informer.yandex.ru/informer/26141199/3_1_FFFFFFFF_EFEFEFFF_0_pageviews"
        style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="try{Ya.Metrika.informer({i:this,id:26141199,lang:'ru'});return false}catch(e){}" /></a>
        <!-- /Yandex.Metrika informer -->
        
      </div>
      <div class="cred">
        © 2016 <p>Русконнект ООО. Интернет-магазин крепежных изделий</p>
      </div>
    </div>
    <!-- BEGIN JIVOSITE CODE -->
    <script type='text/javascript'>
      (function(){ 
        var widget_id = 'UQcZA5ooSM';
        var s = document.createElement('script'); 
        s.type = 'text/javascript'; 
        s.async = true; 
        s.src = '//code.jivosite.com/script/widget/'+widget_id; 
        var ss = document.getElementsByTagName('script')[0]; 
        ss.parentNode.insertBefore(s, ss);
      })();
    </script>
    <!-- END JIVOSITE CODE -->
    
    

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
    (function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
    try {
    w.yaCounter26141199 = new Ya.Metrika({
    id:26141199,
    clickmap:true,
    trackLinks:true,
    accurateTrackBounce:true,
    webvisor:true,
    trackHash:true
    });
    } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
    s = d.createElement("script"),
    f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = "https://mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
    d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/26141199" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->

    <!-- GA -->
    
    <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-73221777-1', 'auto');
    ga('send', 'pageview');
    </script>
    
    
    <!-- Google Tag Manager -->
    <noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-PNGPFC"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-PNGPFC');</script>
    <!-- End Google Tag Manager -->

  </body>
</html>