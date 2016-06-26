<? $this->md5_compil='b0b6658fbf288249e05840da2db739ae'; ?>
<div class="row-fluid">
	<div class="widget tab-widget-product">
		<div class="widget-title">
           <span class="tools tab-header" name="main" style="float: left;"><span class="item"><i class="icon-cog"></i> Общие настройки</span></span>
           <span class="tools tab-header" name="domains" style="float: left;"><span class="item"><i class="icon-globe"></i> Домен</span></span>
		   <span class="tools tab-header" name="modules" style="float: left;"><span class="item"><i class=" icon-sitemap"></i> Модули</span></span>
           <span class="tools tab-header" name="integration" style="float: left;"><span class="item"><i class="icon-signal"></i> Интеграция</span></span>
		   <span class="tools tab-header" name="seo" style="float: left;"><span class="item"><i class="icon-bullhorn"></i> SEO</span></span>
      	</div>
      	<div class="widget-body">
          <div class="settings-form tab-body" section="main">
            <form class="form-horizontal ">
               <input type="hidden" name="b.site_id" value="<?php if(isset($this->vars['siteInfo']['shop_id'])) echo $this->vars['siteInfo']['shop_id']; ?>">
              <div class="control-group">
                <label class="control-label">Название магазина</label>
                <div class="controls">
                  <input type="text" name="b.name" class="span6 require" value="<?php if(isset($this->vars['siteInfo']['name'])) echo $this->vars['siteInfo']['name']; ?>" /><br>
                  <span class="help-inline">Будет отображаться на всех страницах под логотипом</span>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Контактный телефон</label>
                <div class="controls">
                  <input type="text" name="b.phone" class="span6 require" value="<?php if(isset($this->vars['siteInfo']['phone'])) echo $this->vars['siteInfo']['phone']; ?>"  /><br>
                  <span class="help-inline">Будет отображаться в шапке сайта</span>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Контактный Email</label>
                <div class="controls">
                  <input type="text" name="b.email" class="span6 require" value="<?php if(isset($this->vars['siteInfo']['email'])) echo $this->vars['siteInfo']['email']; ?>" /><br>
                  <span class="help-inline">Будет отображаться в подвале сайта</span>
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Логотип</label>
                <div class="controls">
                  <span style="color:#B94A48">* Максимальный размер логотипа: 160х60 px.</span>
                  <div class="LogoUploadButton"></div>
                  <div class="LogoContainer">
                  	<?php if (isset($this->vars['siteInfo']['logo']) && $this->vars['siteInfo']['logo']  ): ?>
                    	<img src="<?php if(isset($this->vars['siteInfo']['logo'])) echo $this->vars['siteInfo']['logo']; ?>">
                    	<input type="hidden" name="s.logo" value="<?php if(isset($this->vars['siteInfo']['logo'])) echo $this->vars['siteInfo']['logo']; ?>">
                    <?php endif; ?>
                  </div>               
                </div>
              </div>

            </form>
          </div>
          
          <div class="settings-form tab-body" section="domains">
            <form class="form-horizontal ">
              <div class="control-group">
                <label class="control-label">Основной домен:</label>
                <div class="controls"><input class="span12 " readonly="true" type="text" name="p.domain" value="<?php if(isset($this->vars['siteInfo']['domains']['server_name'])) echo $this->vars['siteInfo']['domains']['server_name']; ?>" /></div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Зеркала сайта:</label>
                <div class="controls">
                  <table class="table table-hover siteDomainsList">
                    <?php if(count($this->vars['siteInfo']['domains']['alias'])) : 
							   		 $this->vars['foreach']['siteInfo.domains.alias']['count'] = count($this->vars['siteInfo']['domains']['alias']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['siteInfo']['domains']['alias']);
							   		 foreach($this->vars['siteInfo']['domains']['alias'] as $this->vars['foreach']['siteInfo_domains_alias']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['siteInfo_domains_alias']['key']?>
                    <tr>
                      <td><?php if(isset($this->vars['item']['server_name'])) echo $this->vars['item']['server_name']; ?></td>
                      <td><a href="javascript:void(0)" onclick="site_control.removeMirror(<?php if(isset($this->vars['siteInfo']['shop_id'])) echo $this->vars['siteInfo']['shop_id']; ?>, '<?php if(isset($this->vars['item']['server_name'])) echo $this->vars['item']['server_name']; ?>', this)"><i class="icon-trash"></i> Удалить</a></td>
                    </tr>
                    <?php endforeach; ?><?php endif; ?>
                  </table>
                </div>
                
                <div class="controls" name="domainMirrorsContainer" style="display:none">
                  <br>
                  <input type="text"  placeholder="www.example.com" site-id="<?php if(isset($this->vars['siteInfo']['shop_id'])) echo $this->vars['siteInfo']['shop_id']; ?>">
                  <a href="javascript:void(0)" class="btn "><i class="icon-ok"></i></a>
                </div>
                <br>
                <div class="controls">
                  
                  <a href="javascript:void(0)" class="btn btn-primary" onclick="site_control.addMirror()"><i class="icon-plus"></i> Добавить домен</a>
                </div>
              </div>
            </form>
          </div>
          
          <div class="settings-form tab-body" section="modules">
              <table class="table table-hover">
                <tbody>
                  <?php if(count($this->vars['siteInfo']['modules'])) : 
							   		 $this->vars['foreach']['siteInfo.modules']['count'] = count($this->vars['siteInfo']['modules']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['siteInfo']['modules']);
							   		 foreach($this->vars['siteInfo']['modules'] as $this->vars['foreach']['siteInfo_modules']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['siteInfo_modules']['key']?>
                  <tr class="moduleName" on-module="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>">
                    <td><strong><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></strong><br><span><?php if(isset($this->vars['item']['description'])) echo $this->vars['item']['description']; ?></span></td>
                    <td><div class="toggleButtonContainer"><input type="checkbox" <?php if (isset($this->vars['item']['used']) && $this->vars['item']['used']  ): ?>checked="checked"<?php endif; ?> class="toggle moduleActiveSwitch" name="module-active-<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>"  module-id="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>" value="1"></div></td>
                    <td><a href="javascript:void(0)" onclick="site_control.showModuleSettings(<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>)"><i class="icon-cog"></i> Настройки</a></td>
                  </tr>
                  <tr class="moduleSetting" style="display:none" for-module="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>">
                    <td colspan="3">
                    <?php $this->vars['moduleId'] = $this->vars['item']['id']; ?>
                    <?php $this->vars['moduleInfo'] = $this->vars['item']['settings']; ?>
                  	<?php echo $this->get($this->vars['item']['template']); ?>
                    </td>
                  </tr>
                  <?php endforeach; ?><?php endif; ?>
                </tbody>
              </table>
            <br>
          </div>
          
          <div class="settings-form tab-body" section="integration">
            <?php if (isset($this->vars['siteInfo']['integration']) && $this->vars['siteInfo']['integration']  ): ?>
            <?php $this->vars['integration'] = $this->vars['siteInfo']['integration']['integrations']; ?>
            <?php endif; ?>
            <form class="form-horizontal ">
              <div class="control-group">
                <label class="control-label">Оплата Uniteller</label>
                <div class="controls">
                  <div class="moduleSwitcher2"><input type="checkbox" name="i.uniteller" value="1" class="toggle" <?php if (isset($this->vars['integration']['uniteller']) && $this->vars['integration']['uniteller']  ): ?>checked="checked"<?php endif; ?> /></div>
                  <a href="javascript:void(0)" class="popovers" data-placement="top" data-trigger="hover" data-original-title="Интеграция с процессингом" style="font-size:18px;" data-content="Для приема оплаты от клиентов, Вам необходимо заполнить все документы и обратится к менеджеру в компанию Uniteller. После оформления документов, Вам дадут API ключ и API пароль. (см. справочный раздел)"><i class="icon icon-question-sign"></i></a>

                  <br>
                  <div class="input-prepend moduleSetting" name="i.uniteller">
                    <input type="text" name="i.uniteller_id" placeholder="API ID" class="" <?php if (isset($this->vars['integration']['uniteller']) && $this->vars['integration']['uniteller']  ): ?>value="<?php if(isset($this->vars['integration']['uniteller']['uniteller_id'])) echo $this->vars['integration']['uniteller']['uniteller_id']; ?>"<?php endif; ?>>
                    <input type="text" name="i.uniteller_sign" placeholder="API KEY" class="" <?php if (isset($this->vars['integration']['uniteller']) && $this->vars['integration']['uniteller']  ): ?>value="<?php if(isset($this->vars['integration']['uniteller']['uniteller_sign'])) echo $this->vars['integration']['uniteller']['uniteller_sign']; ?>"<?php endif; ?>>
                  </div>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Доставка СДЭК</label>
                <div class="controls">
                  <div class="moduleSwitcher2"><input type="checkbox" name="i.sdek" value="1" class="toggle" <?php if (isset($this->vars['integration']['sdek']) && $this->vars['integration']['sdek']  ): ?>checked="checked"<?php endif; ?> /></div>
                  <a href="javascript:void(0)" class="popovers" data-placement="top" data-trigger="hover" data-original-title="Интеграция с доставкой" style="font-size:18px;" data-content="Для осуществления доставки через службу СДЭК, Вам необходимо заполнить все документы и обратится к менеджеру в компанию СДЭК. После оформления документов, Вам дадут API ключ и API пароль. (см. справочный раздел)"><i class="icon icon-question-sign"></i></a>

                  <br>
                  <div class="input-prepend moduleSetting" name="m.sdek">
                    <input type="text" name="i.sdek_id" placeholder="API ID" class="" <?php if (isset($this->vars['integration']['sdek']) && $this->vars['integration']['sdek']  ): ?>value="<?php if(isset($this->vars['integration']['sdek']['sdek_id'])) echo $this->vars['integration']['sdek']['sdek_id']; ?>"<?php endif; ?>>
                    <input type="text" name="i.sdek_sign" placeholder="API KEY" class="" <?php if (isset($this->vars['integration']['sdek']) && $this->vars['integration']['sdek']  ): ?>value="<?php if(isset($this->vars['integration']['sdek']['sdek_sign'])) echo $this->vars['integration']['sdek']['sdek_sign']; ?>"<?php endif; ?>>
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label">Google Аналитика</label>
                <div class="controls">
                  <div class="moduleSwitcher2"><input type="checkbox" name="i.ga" value="1" class="toggle" <?php if (isset($this->vars['integration']['ga']) && $this->vars['integration']['ga']  ): ?>checked="checked"<?php endif; ?> /></div>
                  <a href="javascript:void(0)" class="popovers" data-placement="top" data-trigger="hover" data-original-title="Google Analitics" style="font-size:18px;" data-content="Для подклчюения этого модуля, Вам необходимо зарегистрироваться на сайте www.google.com/analytics/ и создать новый проект. Проекту будет присвоен номер. Его необходимо будет вписать в поле настройки."><i class="icon icon-question-sign"></i></a>

                  <br>
                  <div class="input-prepend moduleSetting" name="i.ga" >
                    <input type="text" name="i.ga_id" placeholder="Укажите ID счетчика" class="" <?php if (isset($this->vars['integration']['ga']) && $this->vars['integration']['ga']  ): ?>value="<?php if(isset($this->vars['integration']['ga']['ga_id'])) echo $this->vars['integration']['ga']['ga_id']; ?>"<?php endif; ?>>
                  </div>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Yandex.Метрика</label>
                <div class="controls">
                  <div class="moduleSwitcher2"><input type="checkbox" name="i.ym" value="1" class="toggle" <?php if (isset($this->vars['integration']['ym']) && $this->vars['integration']['ym']  ): ?>checked="checked"<?php endif; ?> /></div>
                  <a href="javascript:void(0)" class="popovers" data-placement="top" data-trigger="hover" data-original-title="Yandex Metrika" style="font-size:18px;" data-content="Для подклчюения этого модуля, Вам необходимо зарегистрироваться на сайте www.metrika.yandex.ru и создать новый проект. Проекту будет присвоен номер. Его необходимо будет вписать в поле настройки."><i class="icon icon-question-sign"></i></a>

                  <br>
                  <div class="input-prepend moduleSetting" name="i.ym">
                    <input type="text" name="i.ym_id" placeholder="Укажите ID счетчика" class="" <?php if (isset($this->vars['integration']['ym']) && $this->vars['integration']['ym']  ): ?>value="<?php if(isset($this->vars['integration']['ym']['ym_id'])) echo $this->vars['integration']['ym']['ym_id']; ?>"<?php endif; ?>>
                  </div>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Чат Jivosite</label>
                <div class="controls">
                  <div class="moduleSwitcher2"><input type="checkbox" name="i.jivosite" value="1" class="toggle" <?php if (isset($this->vars['integration']['jivosite']) && $this->vars['integration']['jivosite']  ): ?>checked="checked"<?php endif; ?> /></div>
                  <a href="javascript:void(0)" class="popovers" data-placement="top" data-trigger="hover" data-original-title="JivoSite" style="font-size:18px;" data-content="Для подклчюения этого модуля, Вам необходимо зарегистрироваться на сайте www.jivosite.ru и создать новый виджет. Виджету будет присвоен номер. Его необходимо будет вписать в поле настройки."><i class="icon icon-question-sign"></i></a>

                  <br>
                  <div class="input-prepend moduleSetting" name="i.jivosite">
                    <input type="text" name="i.jivosite_id" placeholder="Укажите ID виджета" class="" <?php if (isset($this->vars['integration']['jivosite']) && $this->vars['integration']['jivosite']  ): ?>value="<?php if(isset($this->vars['integration']['jivosite']['jivosite_id'])) echo $this->vars['integration']['jivosite']['jivosite_id']; ?>"<?php endif; ?>>
                  </div>
                </div>                  
              </div>
            </form>
          </div>
          
          <div class="settings-form tab-body" section="seo">
            <form class="form-horizontal ">
              <div class="control-group">
                <label class="control-label">Title prefix:</label>
                <div class="controls">
                  <input class="span12 " type="text" name="seo.title_prefix" value="<?php if(isset($this->vars['siteInfo']['meta_title_prefix'])) echo $this->vars['siteInfo']['meta_title_prefix']; ?>" />
                  <p><i class="icon-question-sign"></i> Префикс в заголовок ко всем страницам</p>
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Title index:</label>
                <div class="controls">
                  <input class="span12 " type="text" name="seo.title" value="<?php if(isset($this->vars['siteInfo']['meta_title'])) echo $this->vars['siteInfo']['meta_title']; ?>" />
                  <p><i class="icon-question-sign"></i> Заголовок главной страницы</p>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label">Meta description:</label>
                <div class="controls">
                  <input class="span12 " type="text" name="seo.meta_description" value="<?php if(isset($this->vars['siteInfo']['meta_description'])) echo $this->vars['siteInfo']['meta_description']; ?>" />
                  <p><i class="icon-question-sign"></i> Тег <b>meta_description</b> для главной страницы</p>
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Meta keywords:</label>
                <div class="controls">
                  <input class="span12 " type="text" name="seo.meta_keywords" value="<?php if(isset($this->vars['siteInfo']['meta_keywords'])) echo $this->vars['siteInfo']['meta_keywords']; ?>" />
                  <p><i class="icon-question-sign"></i> Тег <b>meta_keywords</b> для главной страницы</p>
                </div>
                
              </div>
              
              <div class="control-group">
                <label class="control-label">Robots.txt:</label>
                <div class="controls">
                  <textarea name="seo.robots"><?php if(isset($this->vars['siteInfo']['meta_robots'])) echo $this->vars['siteInfo']['meta_robots']; ?></textarea>
                </div>
              </div>
              
            </form>
          </div>
          
          <form class="form-horizontal"> 
            <button type="button" action="save" class="btn btn-success">Сохранить</button>
			<button type="button" action="back" class="btn">Назад</button>
          </form>
      	</div>
  	</div>
</div>