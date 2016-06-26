<? $this->md5_compil='66ea43ce72e7b2df6d4f973e746a4f1e'; ?>
<div id="header" class="navbar navbar-inverse navbar-fixed-top">
   <div class="navbar-inner">
       <div class="container-fluid">
           <a class="brand" href="/">ITBC.CloudShop</a>
           <a class="btn btn-navbar collapsed" id="main_menu_trigger" data-toggle="collapse" data-target=".nav-collapse">
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="arrow"></span>
           </a>
           
           <div class="top-nav ">
              
               <ul class="nav pull-right top-menu" >
                   <li class="dropdown">                    
                       <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                         <span class="icon icon-user" style="font-size: 20px; margin-right: 4px;"></span>
                         <span class="username" id="auth_user_info_block" data-uid="<?php if(isset($this->vars['userInfo']['user_id'])) echo $this->vars['userInfo']['user_id']; ?>" data-name="<?php if(isset($this->vars['userInfo']['name_last'])) echo $this->vars['userInfo']['name_last']; ?> <?php if(isset($this->vars['userInfo']['name_first'])) echo $this->vars['userInfo']['name_first']; ?>" style="white-space: nowrap;"><?php if(isset($this->vars['userInfo']['name_last'])) echo $this->vars['userInfo']['name_last']; ?> <?php if(isset($this->vars['userInfo']['name_first'])) echo $this->vars['userInfo']['name_first']; ?></span>
                         
                           <b class="caret"></b>
                       </a>

                       <ul class="dropdown-menu">
                         <li><a href="/ru/users/#/my/"><i class="icon-user"></i> Настройки аккаунта</a></li>
                           <li class="divider"></li>
                         <li><a href="/ru/-utils/exit/"><i class="icon-signout"></i> Выход</a></li>
                       </ul>
                   </li>
               </ul>
            
               <?php if (isset($this->vars['shopInfo']) && $this->vars['shopInfo']  ): ?>
               <ul class="nav pull-right top-menu" >
                   <li class="dropdown">
                       <a href="#" class="dropdown-toggle highlight" data-toggle="dropdown">
                         <span class="icon icon-globe" style="font-size: 20px; margin-right: 4px;"></span>
                         <span class="username"  style="white-space: nowrap;"><?php if(isset($this->vars['shopInfo']['url'])) echo $this->vars['shopInfo']['url']; ?></span>
                         <b class="caret"></b>
                       </a>
						
                       <ul class="dropdown-menu">
                           <li><a href="<?php if(isset($this->vars['shopInfo']['url'])) echo $this->vars['shopInfo']['url']; ?>" target="_blank"><i class="icon-link"></i> Перейти на сайт</a></li>
                         <?php if (isset($this->vars['shopInfo']['url']) && $this->vars['shopInfo']['url'] != $this->vars['shopInfo']['site_mirror']): ?><li><a href="http://<?php if(isset($this->vars['shopInfo']['site_mirror'])) echo $this->vars['shopInfo']['site_mirror']; ?>" target="_blank"><i class="icon-link"></i> Зеркало сайта</a></li><?php endif; ?>
                           <li class="divider"></li>
                           <li class="disable">&nbsp;&nbsp;&nbsp;Выбрать другой сайт:</li>
                           <?php if(count($this->vars['mySites'])) : 
							   		 $this->vars['foreach']['mySites']['count'] = count($this->vars['mySites']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['mySites']);
							   		 foreach($this->vars['mySites'] as $this->vars['foreach']['mySites']['key'] => $this->vars['site']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['mySites']['key']; ?>
                           	<li><a href="javascript:void(0)" onclick="changeEditSite(<?php if(isset($this->vars['site']['shop_id'])) echo $this->vars['site']['shop_id']; ?>)"><i class="icon-globe"></i> <?php if(isset($this->vars['site']['url'])) echo $this->vars['site']['url']; ?></a></li>
                           <?php endforeach; ?><?php endif; ?>
                       </ul>
                   </li>
               </ul>
              <?php endif; ?>
             
               <div id="top_menu" class="nav notify-row"  style="float:right; margin-right:0px">
                 <script type="text/template" id="chatNewMessageTpl">
                   <li data-msgid="{msgid}" data-fromuid="{fromuid}">
                      <a href="#">
                        <span class="subject">
                          <span class="from">{from}</span>
                          <span class="time">{time}</span>
                        </span>
                        <span class="message">{text}</span>
                       </a>
                   </li>
                 </script>
                 
                 
                 <ul class="nav top-menu" style="margin-right:0px">
                   <li class="dropdown" id="header_inbox_bar" style="display:none">
                           <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                               <i class="icon-envelope-alt"></i>
                               <span class="badge badge-important" style="display:none" id="new_messages_counter">0</span>
                           </a>
                           <ul class="dropdown-menu extended inbox">
                               <li>
                                   <p id="chatInboxCounter"></p>
                               </li>
                               <li id="chatInboxShowAll">
                                 <a href="/ru/nodechat/">Посмотреть все сообщения</a>
                               </li>
                           </ul>
                       </li>
                   </ul>
               </div>
           </div>
       </div>
   </div>
</div>