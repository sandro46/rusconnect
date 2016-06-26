<? $this->md5_compil='7d8fb05b720a9326831cbafc357c15ed'; ?>
<div id="sidebar" class="nav-collapse collapse">
  <div class="sidebar-toggler hidden-phone"></div>   

   <div class="navbar-inverse">
      <form class="navbar-search visible-phone">
         <input type="text" class="search-query" placeholder="Search" />
      </form>
   </div>

    <ul class="sidebar-menu">
      <!--<li <?php if (isset($this->vars['is_index']) && $this->vars['is_index']  ): ?>class="active"<?php endif; ?>><a href="/"><span class="icon-box"> <i class="icon-home"></i></span> Рабочий стол<span class="arrow"></span></a></li>-->
       
      		<li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'shop' && $this->vars['controller']['name'] == 'order'): ?>class="active"<?php endif; ?>><a href="/ru/shop/order/"><span class="icon-box"> <i class="icon-shopping-cart"></i></span> <span class="menutext">Заказы</span><span class="arrow"></span></a></li>
      		<li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'shop' && $this->vars['controller']['name'] == 'clients'): ?>class="active"<?php endif; ?>><a href="/ru/shop/clients/"><span class="icon-box"> <i class="icon-user"></i></span> <span class="menutext">Клиенты</span><span class="arrow"></span></a></li>
     	 	
      		<li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'shop' && $this->vars['controller']['name'] == ''): ?>class="active"<?php endif; ?>><a href="/ru/shop/"><span class="icon-box"> <i class="icon-folder-close"></i></span> <span class="menutext">Товары</span><span class="arrow"></span></a></li>
      		<li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'shop' && $this->vars['controller']['name'] == 'vendors'): ?>class="active"<?php endif; ?>><a href="/ru/shop/vendors/"><span class="icon-box"> <i class="icon-list"></i></span> <span class="menutext">Производители</span><span class="arrow"></span></a></li>
     		<li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'users' && $this->vars['controller']['name'] == ''): ?>class="active"<?php endif; ?>><a href="/ru/users/"><span class="icon-box"> <i class="icon-group"></i></span> <span class="menutext">Пользователи</span><span class="arrow"></span></a></li>
      		
      		<li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'site' && $this->vars['controller']['name'] == 'pages'): ?>class="active"<?php endif; ?>><a href="/ru/site/pages/"><span class="icon-box"> <i class="icon-file-alt"></i></span> <span class="menutext">Страницы</span><span class="arrow"></span></a></li>
      		<li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'nodechat' && $this->vars['controller']['name'] == ''): ?>class="active"<?php endif; ?>><a href="/ru/nodechat/"><span class="icon-box"> <i class="icon-comments"></i></span> <span class="menutext">Чат</span><span class="arrow"></span></a></li>
			
      
      		<li class="has-sub <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'templates' && $this->vars['controller']['name'] == ''): ?>active open<?php endif; ?> <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'site'): ?>active open<?php endif; ?>"><a href="javascript:void(0)"><span class="icon-box"> <i class="icon-cog"></i></span> <span class="menutext">Настройка</span><span class="arrow"></span></a>
              <ul class="sub">
                <li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'site' && $this->vars['controller']['name'] == 'settings'): ?>class="active"<?php endif; ?>><a href="/ru/site/settings/">Мои сайты</a></li>
                <li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'site' && $this->vars['controller']['name'] == 'menu'): ?>class="active"<?php endif; ?>><a href="/ru/site/menu/">Меню</a></li>
                <li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'site' && $this->vars['controller']['name'] == 'theme'): ?>class="active"<?php endif; ?>><a href="/ru/site/theme/">Внешний вид</a></li>
                <li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'templates' && $this->vars['controller']['name'] == ''): ?>class="active"<?php endif; ?>><a href="/ru/templates/">Редактор шаблонов</a></li>
                <li <?php if(isset($this->vars['module']) && isset($this->vars['module']['name']) && $this->vars['module']['name'] == 'notice' && $this->vars['controller']['name'] == ''): ?>class="active"<?php endif; ?>><a href="/ru/notice/">Уведомления</a></li>
                
              </ul>
      		</li>
      

    </ul>
</div>