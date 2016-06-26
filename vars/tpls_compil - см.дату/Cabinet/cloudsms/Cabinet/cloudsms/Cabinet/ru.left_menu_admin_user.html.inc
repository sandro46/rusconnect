<? $this->md5_compil='fc91256b61ed09592f096ee3c1889914'; ?>
<div id="sidebar" class="nav-collapse collapse">
  <div class="sidebar-toggler hidden-phone"></div>   

   <div class="navbar-inverse">
      <form class="navbar-search visible-phone">
         <input type="text" class="search-query" placeholder="Search" />
      </form>
   </div>

    <ul class="sidebar-menu">
      <li <?php if (isset($this->vars['is_index']) && $this->vars['is_index']  ): ?>class="active"<?php endif; ?>><a href="/"><span class="icon-box"> <i class="icon-home"></i></span> Рабочий стол<span class="arrow"></span></a></li>
      <li class="has-sub <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'clients' && $this->vars['controller']['name'] == ''): ?>active open<?php endif; ?>"><a href="javascript:void(0)"><span class="icon-box"> <i class="icon-user"></i></span> Клиенты<span class="arrow"></span></a>
     	<ul class="sub">
           <li><a href="/ru/clients/#/">Клиенты</a></li>
           <li><a href="/ru/clients/#/partners/">Партнеры</a></li>
        </ul>
      
      </li>
      <li class="has-sub <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'site'): ?>active open<?php endif; ?>"><a href="javascript:void(0)"><span class="icon-box"> <i class="icon-cog"></i></span> Настройка<span class="arrow"></span></a>
     	<ul class="sub">
          <li><a href="/ru/site/tarifs/">Тарифы</a></li>
          <li><a href="/ru/site/theme_admin/">Темы оформления</a></li>
          <li><a href="/ru/site/services_admin/">Доп. услуги</a></li>
          <li><a href="/ru/faq/">Справка (FAQ)</a></li>
        </ul>
      </li>
      
      <li class="has-sub"><a href="/ru/templates/"><span class="icon-box"> <i class="icon-globe"></i></span> Шаблоны<span class="arrow"></span></a></li>
      
      <!--
      <li class="has-sub"><a href="javascript:void(0)"><span class="icon-box"> <i class="icon-barcode"></i></span> Счета<span class="arrow"></span></a></li>

     
     
      
      
      		<li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'shop' && $this->vars['controller']['name'] == 'clients'): ?>class="active"<?php endif; ?>><a href="/ru/shop/clients/"><span class="icon-box"> <i class="icon-user"></i></span> Клиенты<span class="arrow"></span></a></li>
     	 	<li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'shop' && $this->vars['controller']['name'] == 'order'): ?>class="active"<?php endif; ?>><a href="/ru/shop/order/"><span class="icon-box"> <i class="icon-shopping-cart"></i></span> Заказы<span class="arrow"></span></a></li>
      		<li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'shop' && $this->vars['controller']['name'] == ''): ?>class="active"<?php endif; ?>><a href="/ru/shop/"><span class="icon-box"> <i class="icon-folder-close"></i></span> Товары<span class="arrow"></span></a></li>
      		<li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'shop' && $this->vars['controller']['name'] == 'store'): ?>class="active"<?php endif; ?>><a href="/ru/shop/store/"><span class="icon-box"> <i class="icon-truck"></i></span> Склад<span class="arrow"></span></a></li>
      		<li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'shop' && $this->vars['controller']['name'] == 'vendors'): ?>class="active"<?php endif; ?>><a href="/ru/shop/vendors/"><span class="icon-box"> <i class="icon-list"></i></span> Производители<span class="arrow"></span></a></li>
      		
      		<li class="has-sub <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'shop' && $this->vars['controller']['name'] == 'pay_cabinet'): ?>active open<?php endif; ?> <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'bugalter'): ?>active open<?php endif; ?>"><a href="javascript:void(0)"><span class="icon-box"> <i class="icon-money"></i></span> Деньги<span class="arrow"></span></a>
      			<ul class="sub">
                  <li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'bugalter' && $this->vars['controller']['name'] == ''): ?>class="active"<?php endif; ?>><a href="#">Платежи клиентов</a></li>
                  <li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'shop' && $this->vars['controller']['name'] == 'pay_cabinet'): ?>class="active"<?php endif; ?>><a href="/ru/shop/pay_cabinet/">Оплата услуг</a></li>
              	</ul>
      		</li>
      
      		<li class="has-sub <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'templates' && $this->vars['controller']['name'] == ''): ?>active open<?php endif; ?> <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'site'): ?>active open<?php endif; ?>"><a href="javascript:void(0)"><span class="icon-box"> <i class="icon-cog"></i></span> Настройка<span class="arrow"></span></a>
              <ul class="sub">
                <li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'site' && $this->vars['controller']['name'] == 'settings'): ?>class="active"<?php endif; ?>><a href="/ru/site/settings/">Мои сайты</a></li>
                <li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'site' && $this->vars['controller']['name'] == 'menu'): ?>class="active"<?php endif; ?>><a href="/ru/site/menu/">Меню</a></li>
                <li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'site' && $this->vars['controller']['name'] == 'pages'): ?>class="active"<?php endif; ?>><a href="/ru/site/pages/">Страницы</a></li>
                <li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'site' && $this->vars['controller']['name'] == 'theme'): ?>class="active"<?php endif; ?>><a href="/ru/site/theme/">Внешний вид</a></li>
              </ul>
      		</li>
      
      		<li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'nodechat' && $this->vars['controller']['name'] == ''): ?>class="active"<?php endif; ?>><a href="/ru/nodechat/"><span class="icon-box"> <i class="icon-bullhorn"></i></span> Поддержка<span class="arrow"></span></a></li>
      		<li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'faq' && $this->vars['controller']['name'] == ''): ?>class="active"<?php endif; ?>><a href="/ru/faq/"><span class="icon-box"> <i class="icon-info-sign"></i></span> Справка<span class="arrow"></span></a></li>
-->
    </ul>
</div>