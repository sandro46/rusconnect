<? $this->md5_compil='a2cbdaad974f2d8c888a73170053cf7c'; ?>
<div class="row-fluid">
  <div class="span12"> 
    <div class="widget box blue siteWizard" >
      <div class="widget-title">
        <h4>
          <i class="icon-reorder"></i> Мастер создания сайта - <span class="step-title">Шаг 1 из 4</span>
        </h4>
        <span class="tools">
          <a href="javascript:;" class="icon-chevron-down"></a>
          <a href="javascript:;" class="icon-remove"></a>
        </span>
      </div>
      <div class="widget-body form">
        <form action="#" class="form-horizontal">
          <div class="form-wizard">
            <div class="navbar steps">
              <div class="navbar-inner">
                <ul class="row-fluid">
                  <li class="span3">
                    <a href="#tab1" data-toggle="tab" class="step active">
                      <span class="number">1</span>
                      <span class="desc"><i class="icon-ok"></i> Логотип, домен</span>
                    </a>
                  </li>
                  <li class="span3">
                    <a href="#tab2" data-toggle="tab" class="step">
                      <span class="number">2</span>
                      <span class="desc"><i class="icon-ok"></i> Тема оформления</span>
                    </a>
                  </li>
                  <li class="span3">
                    <a href="#tab3" data-toggle="tab" class="step">
                      <span class="number">3</span>
                      <span class="desc"><i class="icon-ok"></i> Модули, интеграция</span>
                    </a>
                  </li>
                  <li class="span3">
                    <a href="#tab4" data-toggle="tab" class="step">
                      <span class="number">4</span>
                      <span class="desc"><i class="icon-ok"></i> Создание сайта</span>
                    </a> 
                  </li>
                </ul>
              </div>
            </div>
            <div id="bar" class="progress progress-striped">
              <div class="bar"></div>
            </div>
            <div class="tab-content">
              <div class="tab-pane active" id="tab1">
               
                <div class="control-group">
                  <label class="control-label">Название магазина</label>
                  <div class="controls">
                    <input type="text" name="b.name" class="span6 require" value="" placeholder="Магазин бытовой техники №1"/><br>
                    <span class="help-inline">Будет отображаться на всех страницах под логотипом</span>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Контактный телефон</label>
                  <div class="controls">
                    <input type="text" name="b.phone" class="span6 require" placeholder="8 (800) 111-11-11" /><br>
                    <span class="help-inline">Будет отображаться в шапке сайта</span>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Контактный Email</label>
                  <div class="controls">
                    <input type="text" name="b.email" class="span6 require" placeholder="info@example.com"/><br>
                    <span class="help-inline">Будет отображаться в подвале сайта</span>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Домен</label>
                  <div class="controls">
                    <label class="radio" style="margin-right:3px;">
                      <input type="radio" name="useMyDomain" value="2" checked />
                      Использовать общий домен
                    </label>
                    <a href="javascript:void(0)" style="margin-right:20px; font-size:18px;" class="popovers" data-placement="top" data-trigger="hover" data-original-title="Регистрация домена" data-content="Всем пользователям мы бесплатно даем домен третьего уровня."><i class="icon icon-question-sign"></i></a>

                    <label class="radio" style="margin-right:3px;">
                      <input type="radio" name="useMyDomain" value="1" />
                      Привязать свой домен
                    </label>
                    <a href="javascript:void(0)" style="margin-right:20px; font-size:18px;" class="popovers" data-placement="top" data-trigger="hover" data-original-title="Регистрация домена" data-content="Если у Вас уже есть доменное имя, то впишите его в эту форму. После создания магазина, прочитайте справочный раздел о привязке своего домена к сайту в нашей системе."><i class="icon icon-question-sign"></i></a>

                    <label class="radio" style="margin-right:3px;">
                      <input type="radio" name="useMyDomain" value="0"  />
                      Зарегистрировать новый 
                    </label>
                    <a href="javascript:void(0)" class="popovers" data-placement="top" data-trigger="hover" data-original-title="Регистрация домена" style="font-size:18px;" data-content="Вы можете указать как уже зарегистрированый домен (информацию о привязке домена к сайту смотрите в справочном разделе) так и зарегистрировать новый прямо из этой формы."><i class="icon icon-question-sign"></i></a>
                    <br><br>
                    
             		<div class="domain-2" name="domainInputControl" style=""><input type="text"  class="span4" name="b.domain" placeholder="<?php if(isset($this->vars['userAutoDomain'])) echo $this->vars['userAutoDomain']; ?>" readonly="readonly" /> <a href="javascript:void(0)" action="changeDomain">Сменить адрес</a></div>
                    <div class="domain-1" name="domainInputControl" style="display:none"><input type="text"  class="span4" name="b.domain" placeholder="example.com"/> </div>
                    <div class="domain-0" name="domainInputControl" style="display:none"><input type="text"  class="span4" name="b.domain" placeholder="example.com"/> <a href="javascript:void(0)" action="checkDomain">Проверить домен</a></div>
                    <span name="domainSearcheResult" style="display:none"></span>
                    <br>
                    <a href="javascript:void(0)" class="link"><i class="icon icon-plus"></i> Добавить зеркало</a>
                    <a href="javascript:void(0)" class="popovers" data-placement="top" data-trigger="hover" data-original-title="Зеркало сайта" style="font-size:18px;" data-content="Вы можете указать несколько доменов для одного сайта. Например site.com - основной домен, mysite.com дополнительный. Оба домена будут вести на один и тот же сайт."><i class="icon icon-question-sign"></i></a>
                  </div>
                </div>
                
                <div class="control-group">
                  
                  <label class="control-label">Логотип</label>

                  <div class="controls">
                    <span style="color:#B94A48">* Максимальный размер логотипа: 160х60 px.</span>
                    <div class="LogoUploadButton"></div>
                  	<div class="LogoContainer"></div>               
                  </div>
                </div>
              </div>
              
              <div class="tab-pane active" id="tab2">
                <h3>Выбор темы оформления</h3>
                <div class="control-group">
                  <?php if(count($this->vars['themesList'])) : 
							   		 $this->vars['foreach']['themesList']['count'] = count($this->vars['themesList']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['themesList']);
							   		 foreach($this->vars['themesList'] as $this->vars['foreach']['themesList']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['themesList']['key']?>
                    <div class="row-fluid product-search">
                      <div class="span4 product-text">
                        <img src="<?php if(isset($this->vars['item']['img_preview'])) echo $this->vars['item']['img_preview']; ?>" rel="elevateZoom" data-zoom-image="<?php if(isset($this->vars['item']['img_big'])) echo $this->vars['item']['img_big']; ?>" alt="" style="width:100px;">
                        <div class="portfolio-text-info">
                          <h4><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></h4>
                          <p><?php if(isset($this->vars['item']['description'])) echo $this->vars['item']['description']; ?></p>
                        </div>
                      </div>
                      <div class="span8">
                        <div class="product-info">
                          Демо магазин:
                          <span><a href="http://<?php if(isset($this->vars['item']['demo_url'])) echo $this->vars['item']['demo_url']; ?>" target="_blank"><?php if(isset($this->vars['item']['demo_url'])) echo $this->vars['item']['demo_url']; ?></a></span>
                        </div>
                        <div class="product-info">
                          Цена шаблона:
                          <span style="color:#87BB33"><?php if (isset($this->vars['item']['is_free']) && $this->vars['item']['is_free']  ): ?>БЕСПЛАТНО<?php else: ?><?php if(isset($this->vars['item']['price'])) echo $this->vars['item']['price']; ?><?php endif; ?></span>
                        </div>
                        <div class="product-info" style="padding-top:37px;">

                          <a class="btn " action="selectTheme" theme_id="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>"><i class=" icon-check-empty "></i> Выбрать</a>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?><?php endif; ?>
                </div>
              </div>
              <div class="tab-pane active" id="tab3">
                <h3>Подключаемые модули</h3>
                <div class="control-group">
                  <label class="control-label">Оплата Uniteller</label>
                  <div class="controls">
                    <div class="moduleSwitcher"><input type="checkbox" name="m.uniteller" value="1" class="toggle" /></div>
                    <a href="javascript:void(0)" class="popovers" data-placement="top" data-trigger="hover" data-original-title="Интеграция с процессингом" style="font-size:18px;" data-content="Для приема оплаты от клиентов, Вам необходимо заполнить все документы и обратится к менеджеру в компанию Uniteller. После оформления документов, Вам дадут API ключ и API пароль. (см. справочный раздел)"><i class="icon icon-question-sign"></i></a>

                    <br>
                    <div class="input-prepend moduleSetting" name="m.uniteller">
                      <input type="text" name="m.uniteller_id" placeholder="API ID" class="">
                      <input type="text" name="m.uniteller_sign" placeholder="API KEY" class="">
                    </div>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Доставка СДЭК</label>
                  <div class="controls">
                    <div class="moduleSwitcher"><input type="checkbox" name="m.sdek" value="1" class="toggle" /></div>
                    <a href="javascript:void(0)" class="popovers" data-placement="top" data-trigger="hover" data-original-title="Интеграция с доставкой" style="font-size:18px;" data-content="Для осуществления доставки через службу СДЭК, Вам необходимо заполнить все документы и обратится к менеджеру в компанию СДЭК. После оформления документов, Вам дадут API ключ и API пароль. (см. справочный раздел)"><i class="icon icon-question-sign"></i></a>

                    <br>
                    <div class="input-prepend moduleSetting" name="m.sdek">
                      <input type="text" name="m.sdek_id" placeholder="API ID" class="">
                      <input type="text" name="m.sdek_sign" placeholder="API KEY" class="">
                    </div>
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Google Аналитика</label>
                  <div class="controls">
                    <div class="moduleSwitcher"><input type="checkbox" name="m.ga" value="1" class="toggle" /></div>
                    <a href="javascript:void(0)" class="popovers" data-placement="top" data-trigger="hover" data-original-title="Google Analitics" style="font-size:18px;" data-content="Для подклчюения этого модуля, Вам необходимо зарегистрироваться на сайте www.google.com/analytics/ и создать новый проект. Проекту будет присвоен номер. Его необходимо будет вписать в поле настройки."><i class="icon icon-question-sign"></i></a>

                    <br>
                    <div class="input-prepend moduleSetting" name="m.ga">
                      <input type="text" name="m.ga_id" placeholder="Укажите ID счетчика" class="">
                    </div>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Yandex.Метрика</label>
                  <div class="controls">
                    <div class="moduleSwitcher"><input type="checkbox" name="m.ym" value="1" class="toggle" /></div>
                    <a href="javascript:void(0)" class="popovers" data-placement="top" data-trigger="hover" data-original-title="Yandex Metrika" style="font-size:18px;" data-content="Для подклчюения этого модуля, Вам необходимо зарегистрироваться на сайте www.metrika.yandex.ru и создать новый проект. Проекту будет присвоен номер. Его необходимо будет вписать в поле настройки."><i class="icon icon-question-sign"></i></a>

                    <br>
                    <div class="input-prepend moduleSetting" name="m.ym">
                      <input type="text" name="m.ym_id" placeholder="Укажите ID счетчика" class="">
                    </div>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Чат Jivosite</label>
                  <div class="controls">
                    <div class="moduleSwitcher"><input type="checkbox" name="m.jivosite" value="1" class="toggle" /></div>
                    <a href="javascript:void(0)" class="popovers" data-placement="top" data-trigger="hover" data-original-title="JivoSite" style="font-size:18px;" data-content="Для подклчюения этого модуля, Вам необходимо зарегистрироваться на сайте www.jivosite.ru и создать новый виджет. Виджету будет присвоен номер. Его необходимо будет вписать в поле настройки."><i class="icon icon-question-sign"></i></a>

					<br>
                    <div class="input-prepend moduleSetting" name="m.jivosite">
                      <input type="text" name="m.jivosite_id" placeholder="Укажите ID виджета" class="">
                    </div>
                  </div>                  
                </div>
              </div>
              <div class="tab-pane active" id="tab4">
                 <div class="alert alert-block alert-info fade in">
                   <button data-dismiss="alert" class="close" type="button">×</button>
                   <h4 class="alert-heading">Ура, Вы почти у цели!</h4>
                   <p>
                    	Все готово для создания Вашего интернет-магазина. <br>
                     	После того, как Вы нажмете на кнопку &laquo;Создать сайт&raquo;, Вы сможете торговать в интернете.<br>
                    	Но прежде чем перейти к новому сайту, ознакомтесь с услугами, котоыре смогут Вам помочь:<br><br>
                     <a class="btn btn-small" href="javascript:void(0)" onclick="$(this).closest('#tab4').find('div[name=extendedOptions]').slideToggle()">Дополнительные услуги</a>
                   </p>
                </div>
                
                <div class="control-group" name="extendedOptions" style="display:none">
                    <div class="row-fluid product-search">
                      <div class="span4 product-text">
                        <div class="portfolio-text-info" style="padding-left:15px;">
                          <h4 style="margin-bottom:0px;">SEO оптимизация</h4>
                          <p>Профессиональная команда SEO специалистов поможет провести аудит и оптимизацию сайта для привлечение большей аудитории</p>
                        </div>
                      </div>
                      <div class="span8">

                        <div class="product-info">
                          Стоимость услуги:
                          <span style="color:#87BB33">Бесплатно</span>
                        </div>
                        <div class="product-info" style="padding-top:37px;">
                          <a class="btn " data-id="1" action="selectBoxMagic"><i class=" icon-check-empty "></i> Выбрать</a>
                        </div>
                      </div>
                    </div>
                  
                  	<div class="row-fluid product-search">
                      <div class="span4 product-text">
                        <div class="portfolio-text-info" style="padding-left:15px;">
                          <h4 style="margin-bottom:0px;">Фотосъемка товаров</h4>
                          <p>В нашем штате также присутствую провессиональные фотографы, со специализированным оборудованием для качественной съемки Вашей продукции.</p>
                        </div>
                      </div>
                      <div class="span8">

                        <div class="product-info">
                          Стоимость услуги:
                          <span style="color:#87BB33">По договору</span>
                        </div>
                        <div class="product-info" style="padding-top:37px;">
                          <a class="btn " data-id="2" action="selectBoxMagic"><i class=" icon-check-empty "></i> Выбрать</a>
                        </div>
                      </div>
                    </div>
                  
                  	<div class="row-fluid product-search">
                      <div class="span4 product-text">
                        <div class="portfolio-text-info" style="padding-left:15px;">
                          <h4 style="margin-bottom:0px;">Наполнение товарами</h4>
                          <p>Армия профессиональных контент-менеджеров и рерайтеров помогут в наполнении магазина, и составлении полного описания товара.</p>
                        </div>
                      </div>
                      <div class="span8">

                        <div class="product-info">
                          Стоимость услуги:
                          <span style="color:#87BB33">По договору</span>
                        </div>
                        <div class="product-info" style="padding-top:37px;">
                          <a class="btn " data-id="3" action="selectBoxMagic"><i class=" icon-check-empty "></i> Выбрать</a>
                        </div>
                      </div>
                    </div>
                  
                 	<div class="row-fluid product-search">
                      <div class="span4 product-text">
                        <div class="portfolio-text-info" style="padding-left:15px;">
                          <h4 style="margin-bottom:0px;">Уникальный дизайн</h4>
                          <p>Наши дизайнеры разработают для Вас уникальный дизайн с учетом любых Ваших пожеланий</p>
                        </div>
                      </div>
                      <div class="span8">

                        <div class="product-info">
                          Стоимость услуги:
                          <span style="color:#87BB33">от 10 000 руб.</span>
                        </div>
                        <div class="product-info" style="padding-top:37px;">
                          <a class="btn " data-id="4" action="selectBoxMagic"><i class=" icon-check-empty "></i> Выбрать</a>
                        </div>
                      </div>
                    </div> 
                  
                  
                </div>
              </div>
            </div>
            
            <div class="form-actions clearfix">
              <a href="javascript:;" class="btn button-previous">
                <i class="icon-angle-left"></i> Назад 
              </a>
              <a href="javascript:;" class="btn btn-primary blue button-next">
                Далее <i class="icon-angle-right"></i>
              </a>
              <a href="javascript:;" class="btn btn-success button-submit" action="wizardDone">
                Создать сайт! <i class="icon-ok"></i>
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
      