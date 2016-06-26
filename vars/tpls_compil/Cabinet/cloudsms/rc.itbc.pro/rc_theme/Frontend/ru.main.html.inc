<? $this->md5_compil='ee857aa18ad85dd8fec5dd1da459e7a4'; ?>
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
    <link type="text/css" rel="stylesheet" href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>css/adaptive_catalog.css" media="screen"/>
    
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
        <div id="catitem" item="1" class="categoryLightbox"><a href="#" class="item menuItemButton" >Каталог</a></div>
       
        <span class="devider"></span>   
        
        <div class="m-item categoryLightbox" item="2" id="catitem2"><a href="#" class="item" id="listitem">Категории</a></div>
        
        <span class="devider"></span>
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
        <span class="devider"></span>
        <div class="callinfo">
          <span class="phone"><a href="/contacts/" >8 (800) 920 50 38</a></span>
          <span class="worktime">
            Время работы:<br>
            пн-пт, с 9:00 до 20:00
          </span>
        </div>
        <span class="devider"></span>
        <?php if (isset($this->vars['userInfo']['user_id']) && $this->vars['userInfo']['user_id']  ): ?>
        <a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> class="account logined" href="/ru/cabinet/">
          <span class="acc">Аккаунт</span>
          <span class="actions active">
            <?php if(isset($this->vars['userInfo']['contact']['name'])) echo $this->vars['userInfo']['contact']['name']; ?><br>
            <?php if(isset($this->vars['userInfo']['contact']['surname'])) echo $this->vars['userInfo']['contact']['surname']; ?>
          </span>
        </a>
        <?php else: ?>
        <noindex>
        <a rel="nofolow" class="account" href="/login/">
          <span class="acc">Аккаунт</span>
          <span class="bcc">ВОЙТИ</span>
          <span class="actions">
            Вход<br>
            или регистрация
          </span>
        </a>
        </noindex>
        <?php endif; ?>
        <span class="devider"></span>               
        <div class="cart" id="cart_box_frame" data-summ="<?php if(isset($this->vars['cart_summary']['summ_float'])) echo $this->vars['cart_summary']['summ_float']; ?>" data-count="<?php if(isset($this->vars['cart_summary']['count'])) echo $this->vars['cart_summary']['count']; ?>">
          <span class="title">Корзина</span>
          <span class="cnt" name="cartsummaryCount"><?php if (isset($this->vars['cart_summary']['count']) && $this->vars['cart_summary']['count']  ): ?><?php if(isset($this->vars['cart_summary']['count'])) echo $this->vars['cart_summary']['count']; ?><?php else: ?>0<?php endif; ?></span>
          <span name="cartSummaryCleared" class="actions" <?php if (isset($this->vars['cart_summary']['count']) && $this->vars['cart_summary']['count']  ): ?>style="width: 115px;display:none"<?php else: ?>style="width: 115px;display:block"<?php endif; ?>>Ваша корзина<br> пуста</span>
          <span name="cartSummaryStat" class="actions showCart" <?php if (isset($this->vars['cart_summary']['count']) && $this->vars['cart_summary']['count']  ): ?>style="width: 115px; display:block"<?php else: ?>style="width: 115px; display:none"<?php endif; ?>>
            <b name="cartsummarySumm"><?php if(isset($this->vars['cart_summary']['sum'])) echo $this->vars['cart_summary']['sum']; ?></b>
          </span>
         
          
          
          <div class="fullcart">
            <div name="cartItems" class="cartItems scroll-pane jspScrollable" style="width:245px; height: 450px; ">
              
            	<?php echo $this->get("MainCartbox.html", "shop"); ?>
            </div>
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
        <!--<span class="devider noshowfluid"></span>-->            
        <div class="feed">
          <span class="icon">
            <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/icon_feed.png" />
          </span>                
          <span class="actions">
            Обратный<br>
            звонок
          </span>
          <div class="feedForm" id="callMeFormBlock">
            <p class="callNotSended" style="display:none">Для Вашего удобства, мы предлагаем Вам возможность заказать обратный звонок одного из наших менеджеров.</p>
            <p class="callOkSend" style="display:none">Мы получили Ваш запрос на обратный звонок. Наши менеджеры свяжутся с Вами в ближайшее время.</p>
            <p class="callSended" style="display:none">Вы недавно уже отправляли запрос на обратный звонок. Мы обязательно Вам перезвоним. Пожалуйста, подождите еще немного... </p>
            <p class="callError" style="display:none"></p>
            <form>
              <input type="text" name="phone" placeholder="Телефон" />
              <input type="text" name="name" placeholder="Контактное имя"/>
              <a href="javascript:void(0)" <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> onclick="callMe()">позвонить мне</a>
            </form>
          </div>
        </div>
        <!--<span class="devider"></span>-->             
        <a class="support" <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="javascript:void(0)" onclick="jivo_api.open()">
          <span class="icon">
            <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/icon_support.png" />
          </span>                
          <span class="actions">
            Онлайн<br>
            поддержка
          </span>
        </a>
        <!--<span class="devider"></span>-->            
        <a class="help" href="/faq/" <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?>>
          <span class="icon">
            <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/icon_help.png" />
          </span>                
          <span class="actions">
            Помощь<br>
            по сайту
          </span>
        </a>
      </div>
    </div>

    <div class="categoryPopupContainer categoryLightbox">
      <div class="categorymain catlist1">              
        <div class="leftPanel">
          <div class="catListsContainer">
            <div class="catListsHolder">
              <?php if($core->lib->widget("ProductGroups")): ?>
<?php $core->widgets->ProductGroups()->setting("parent_id", "31"); ?>
<?php $core->widgets->ProductGroups()->setting("current_id", "".$this->vars['category_id'].""); ?>
<?php $core->widgets->ProductGroups()->setting("limit", "0"); ?>
<?php $core->widgets->ProductGroups()->setting("showSubLevel", "2"); ?>

<?php $core->widgets->ProductGroups()->appendSettings(); $core->widgets->ProductGroups()->main(); endif; ?>
              <ul class="categoryProductsList" id="categoryPopupList">
                <?php if(count($this->vars['ProductGroupsOut'])) : 
							   		 $this->vars['foreach']['ProductGroupsOut']['count'] = count($this->vars['ProductGroupsOut']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['ProductGroupsOut']);
							   		 foreach($this->vars['ProductGroupsOut'] as $this->vars['foreach']['ProductGroupsOut']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['ProductGroupsOut']['key']; ?>
                <li <?php if (isset($this->vars['category_id']) && $this->vars['category_id'] == $this->vars['item']['id']): ?>class="active"<?php endif; ?>><a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="javascript:void(0)" data-category="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></a>
                <?php if (isset($this->vars['item']['childs']) && $this->vars['item']['childs']  ): ?>
                  <ul>
                  <?php if(count($this->vars['item']['childs'])) : 
							   		 $this->vars['foreach']['item.childs']['count'] = count($this->vars['item']['childs']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['item']['childs']);
							   		 foreach($this->vars['item']['childs'] as $this->vars['foreach']['item_childs']['key'] => $this->vars['sub']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['item_childs']['key']; ?>
                     <li <?php if (isset($this->vars['category_id']) && $this->vars['category_id'] == $this->vars['sub']['id']): ?>class="active"<?php endif; ?> is-child="1"><a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="javascript:void(0)" data-category="<?php if(isset($this->vars['sub']['id'])) echo $this->vars['sub']['id']; ?>"><?php if(isset($this->vars['sub']['name'])) echo $this->vars['sub']['name']; ?></a>

                  <?php endforeach; ?><?php endif; ?>
                    </ul>
                <?php endif; ?> 
                </li>
                <?php endforeach; ?><?php endif; ?>
              </ul>
            </div>
          </div>
        </div>
        <div class="ajaxload"><img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/ajax-loader.gif"></div>
        <div class="rightPanel categoryPopupProdList"></div>
      </div>

      <div class="categorymain catlist2">              
        <div class="leftPanel">
          <div class="catListsContainer">
            <div class="catListsHolder">
              <?php if($core->lib->widget("ProductGroups")): ?>
<?php $core->widgets->ProductGroups()->setting("parent_id", "32"); ?>
<?php $core->widgets->ProductGroups()->setting("current_id", "".$this->vars['category_id'].""); ?>
<?php $core->widgets->ProductGroups()->setting("limit", "0"); ?>

<?php $core->widgets->ProductGroups()->appendSettings(); $core->widgets->ProductGroups()->main(); endif; ?>
              <ul class="categoryProductsList" id="categoryPopupList">
                <?php if(count($this->vars['ProductGroupsOut'])) : 
							   		 $this->vars['foreach']['ProductGroupsOut']['count'] = count($this->vars['ProductGroupsOut']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['ProductGroupsOut']);
							   		 foreach($this->vars['ProductGroupsOut'] as $this->vars['foreach']['ProductGroupsOut']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['ProductGroupsOut']['key']; ?>
                <li <?php if (isset($this->vars['category_id']) && $this->vars['category_id'] == $this->vars['item']['id']): ?>class="active"<?php endif; ?>><a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="javascript:void(0)" data-category="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></a></li>
                <?php endforeach; ?><?php endif; ?>
              </ul>
            </div>
          </div>
        </div>
        <div class="ajaxload"><img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/ajax-loader.gif"></div>
        <div class="rightPanel categoryPopupProdList"></div>
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
            <h3>Служба доставки</h3>
            <p>Собственная транспортная служба доставит товар до любой точки Москвы.</p>
          </div>
        </a>           
        <a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="/assortiment_na_sklade.html" class="item">
          <div class="icon">
            <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/services_02.png" />
          </div>
          <div class="caption">
            <h3>Ассортимент на складе</h3>
            <p>Вся продукция, представленная в каталоге, всегда имеется в наличии на складе компании.</p>
          </div>
        </a>           
        <a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="/personal_nyj_menedzher.html" class="item">
          <div class="icon">
            <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/services_03.png" />
          </div>
          <div class="caption">
            <h3>Персональный менеджер</h3>
            <p>Для удобства и оперативности работы каждый клиент пользуется услугами персонального менеджера.</p>
          </div>
        </a>            
        <a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="/evropejskoe_kachestvo.html" class="item">
          <div class="icon">
            <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/services_04.png" />
          </div>
          <div class="caption">
            <h3>Европейское качество</h3>
            <p>Сотрудничество с ведущими европейскими производителями позволяет гарантировать высокое качество товаров.</p>
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
          <li class="menuHeader">О компании</li>
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
          <li class="menuHeader">Каталог</li>
          
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
        <ul class="menuCustomers">
          <li class="menuHeader">Для покупателей</li>
          
          <?php if($core->lib->widget("ShopMenu")): ?>
<?php $core->widgets->ShopMenu()->setting("menu_id", "2"); ?>

<?php $core->widgets->ShopMenu()->appendSettings(); $core->widgets->ShopMenu()->main(); endif; ?>
                      
          <?php if(count($this->vars['ShopMenuOut'])) : 
							   		 $this->vars['foreach']['ShopMenuOut']['count'] = count($this->vars['ShopMenuOut']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['ShopMenuOut']);
							   		 foreach($this->vars['ShopMenuOut'] as $this->vars['foreach']['ShopMenuOut']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['ShopMenuOut']['key']; ?>
          <li><a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>"><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></a></li>
          <?php endforeach; ?><?php endif; ?>
        </ul>  
        <ul class="menuCust">
          <li class="menuHeader">Наш склад</li>
          <li>
            Россия, Московская область,<br>
            Мытищинский район,<br>
            Осташковское шоссе 14<br><br>
          </li>
          <li><a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="/contact_map/">Как до нас доехать</a></li>
        </ul>        
        <ul class="contInfo">
          <li class="menuHeader">8 (800) 920 50 38</li>
          <!--<li>
            Время работы: пн-пт,<br>
            с 9:00 до 20:00<br><br>
          </li>
          <li><a <?php if (isset($this->vars['index_page']) && $this->vars['index_page']  ): ?><?php else: ?>rel="nofolow"<?php endif; ?> href="/contact_map/">Как до нас доехать</a></li>-->
        </ul>
        <!--<div class="secure">
          <img alt="" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>samples/secure.png" />
        </div>-->
      </div> 
      <div class="footer-logo"></div>
      <div class="cred">
        © 2015 <p>rusconnect.ru 8(495)221-73-50 крепёж оптом</p>
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
    
    
  </body>
</html>