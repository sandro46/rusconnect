<? $this->md5_compil='b27f20a4c8cef961c1d63f7355fcda09'; ?>
<div class="row-fluid">
	<div class="widget mediumWidget">
		<div class="widget-title">
          <h4><i class="icon-reorder"></i>Событие</h4>
      	</div>
      	<div class="widget-body" name="notice_header">
          <form class="form-horizontal">
            <div class="control-group">
				<label class="control-label">Название уведомления: </label>
              	<div class="controls">
                  <input type="text" name="notice_title" value="<?php if(isset($this->vars['noticeInfo']['title'])) echo $this->vars['noticeInfo']['title']; ?>" class="span12 require">
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Событие: </label>
              	<div class="controls">
                  <select class="chosen require span6" name="event" data-placeholder="Укажите на событие системы">
                    <option></option>
                    <?php if(count($this->vars['noticeEvents'])) : 
							   		 $this->vars['foreach']['noticeEvents']['count'] = count($this->vars['noticeEvents']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['noticeEvents']);
							   		 foreach($this->vars['noticeEvents'] as $this->vars['foreach']['noticeEvents']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['noticeEvents']['key']; ?>
                    	<optgroup label="<?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?>">
                        	<?php if(count($this->vars['item']['events'])) : 
							   		 $this->vars['foreach']['item.events']['count'] = count($this->vars['item']['events']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['item']['events']);
							   		 foreach($this->vars['item']['events'] as $this->vars['foreach']['item_events']['key'] => $this->vars['event']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['item_events']['key']; ?>
                          <option value="<?php if(isset($this->vars['event']['id'])) echo $this->vars['event']['id']; ?>" <?php if (isset($this->vars['noticeInfo']) && $this->vars['noticeInfo']  ): ?><?php if (isset($this->vars['noticeInfo']['event_id']) && $this->vars['noticeInfo']['event_id'] == $this->vars['event']['id']): ?>selected="selected"<?php endif; ?><?php endif; ?>><?php if(isset($this->vars['event']['title'])) echo $this->vars['event']['title']; ?></option>
                          	<?php endforeach; ?><?php endif; ?>
                    	</optgroup>
                    <?php endforeach; ?><?php endif; ?>
                  </select>
              	</div>
            </div>
                        
            <div class="control-group">
				<label class="control-label">Действие: </label>
              	<div class="controls">
                  <select class="require span6" name="action" data-placeholder="Укажите какое действие выполнять">
                    <option></option>
                    <?php if(count($this->vars['noticeActions'])) : 
							   		 $this->vars['foreach']['noticeActions']['count'] = count($this->vars['noticeActions']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['noticeActions']);
							   		 foreach($this->vars['noticeActions'] as $this->vars['foreach']['noticeActions']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['noticeActions']['key']; ?>
                       <option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>" <?php if (isset($this->vars['noticeInfo']) && $this->vars['noticeInfo']  ): ?><?php if (isset($this->vars['noticeInfo']['action_id']) && $this->vars['noticeInfo']['action_id'] == $this->vars['item']['id']): ?>selected="selected"<?php endif; ?><?php endif; ?>><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                    <?php endforeach; ?><?php endif; ?>
                  </select>
              	</div>
            </div>
            
            <div class="control-group" name="recipientBlock">
				<label class="control-label">Получатели уведомления: </label>
              	<div class="controls">                  
                  <div>
                    <select class="span6" name="recipientType"  data-placeholder="Выберите получателя и нажмите добавить"></select>
                    <a href="javascript:void(0)" class="btn" style="margin-top:-24px;" name="recipientAddButton"><i class="icon-signout"></i> Добавить</a>
                  </div>
                  
                 
                  
                  <div class="recipientAction" type="5" style="display:none"></div>
                  <div class="recipientAction" type="4" style="display:none"></div>
                  
                  <div class="recipientAction" type="6" style="display:none">
                    <div class="input-icon left">
                      <i class="icon-envelope"></i>
                      <input type="text" placeholder="Введите email адрес и нажмите OK" class="recipData span6">    
                       <a href="javascript:void(0)" class="btn btn-success">OK</a>
                    </div>
                  </div>
                  
                  <div class="recipientAction" type="7" style="display:none">
                  	<div class="input-icon left">
                      <i class="icon-phone"></i>
                      <input type="text" placeholder="Введите номер телефона и нажмите OK" class="recipData span6 ">    
                      <a href="javascript:void(0)" class="btn btn-success">OK</a>
                    </div>
                  </div>
                  
                  
                  
                  
                  <h4 name="recipientNotFound">Получатели не указаны</h4>
                  
                  <ul style="margin-top:15px;" name="recipientsList">
                    <li rec-type="1" style="display:none; font-size:14px">Покупатель <a class="rm_recip" href="javascript:void(0)">Удалить</a></li>
                    <li rec-type="2" style="display:none; font-size:14px">Администратор <a class="rm_recip" href="javascript:void(0)">Удалить</a></li>
                    <li rec-type="4" style="display:none; font-size:14px">Группа пользователей (всего контактов <span class="contactsCount badge badge-success">0</span>) <a class="ed_recip" href="javascript:void(0)">Изменить</a> <a class="rm_recip" href="javascript:void(0)">Удалить</a></li>
                    <li rec-type="5" style="display:none; font-size:14px">Пользователи (всего контактов <span class="contactsCount badge badge-success">0</span>) <a class="ed_recip" href="javascript:void(0)">Изменить</a> <a class="rm_recip" href="javascript:void(0)">Удалить</a></li>
                    <li rec-type="6" style="display:none; font-size:14px">Произвольные Email адреса (всего контактов <span class="contactsCount badge badge-success">0</span>) <a class="ed_recip" href="javascript:void(0)">Изменить</a> <a class="rm_recip" href="javascript:void(0)">Удалить</a></li>
                    <li rec-type="7" style="display:none; font-size:14px">Произвольные номера (всего контактов <span class="contactsCount badge badge-success">0</span>) <a class="ed_recip" href="javascript:void(0)">Изменить</a> <a class="rm_recip" href="javascript:void(0)">Удалить</a></li>
                  </ul>
              	</div>
            </div>
          </form>
      	</div>
            
        <div class="widget-title">
          <h4><i class="icon-reorder"></i> <span name="action_data_block_title">Email уведомление</span></h4>
        </div>
      
      	<div class="widget-body" name="notice_data">
          <form class="form-horizontal">
            
            <!-- email -->
            <div action="1" style="display:none" action_block_name="Параметры Email уведомления">
              <div class="control-group">
                <label class="control-label">Тема письма:</label>
                <div class="controls">
                  <input type="text" name="title" value="<?php if (isset($this->vars['noticeInfo']['action_id']) && $this->vars['noticeInfo']['action_id'] == 1): ?><?php if(isset($this->vars['noticeInfo']['data']['title'])) echo $this->vars['noticeInfo']['data']['title']; ?><?php endif; ?>" class="span12 require">
                </div>
              </div>
      
              <div class="control-group">
                <label class="control-label">Отправитель:</label>
                <div class="controls">
                  <input type="text" name="sender" class="span12 require" value="<?php if (isset($this->vars['noticeInfo']['action_id']) && $this->vars['noticeInfo']['action_id'] == 1): ?><?php if(isset($this->vars['noticeInfo']['data']['sender'])) echo $this->vars['noticeInfo']['data']['sender']; ?><?php else: ?><?php if(isset($this->vars['shopInfo']['email'])) echo $this->vars['shopInfo']['email']; ?><?php endif; ?>">
                </div>
              </div>

              <div class="control-group">
                <label class="control-label"></label>
                <div class="controls">
                  <a href="javascript:void(0)" onclick="$(this).parent().find('.testme').slideToggle()" style="text-decoration:none; border-bottom:dashed 1px;">Отправить мне тестовое уведомление</a>
                  <div style="margin-top:10px; display:none" class="testme">
                    <input type="text" name="test_mail_destination"  class=" require" value="<?php if(isset($this->vars['userInfo']['email'])) echo $this->vars['userInfo']['email']; ?>">
                    <a href="javascript:void(0)" class="btn " onclick="site_notice.sendTestNotice(1, this)"><i class="icon-signout"></i> Отправить</a>
                  </div>
                  
                </div>
                
                
              </div>
              
              <div class="control-group">
                <h4>Текст письма: <small>Допустимо использовать html и <a href="javascript:void(0)" style="border-bottom:dashed 1px;">переменные шаблонов</a></small></h4>
                <div class="controls" style="margin-left:0px; width:100%" name="text" id="noticeDataTextEditor"></div>
              </div>
              
            </div>
            
            <!-- sms -->
            <div action="2" style="display:none" action_block_name="Параметры SMS уведомления">
              <div class="control-group">
                <label class="control-label">Имя отправителя:</label>
                <div class="controls">
                  <input type="text" name="sender" class="span4 require" value="<?php if (isset($this->vars['noticeInfo']['action_id']) && $this->vars['noticeInfo']['action_id'] == 2): ?><?php if(isset($this->vars['noticeInfo']['data']['sender'])) echo $this->vars['noticeInfo']['data']['sender']; ?><?php endif; ?>">
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label"></label>
                <div class="controls">
                  <a href="javascript:void(0)" onclick="$(this).parent().find('.testme').slideToggle()" style="text-decoration:none; border-bottom:dashed 1px;">Отправить мне тестовое уведомление</a>
                  <div style="margin-top:10px; display:none" class="testme">
                    <input type="text" name="test_phone_destination"  class=" require" value="<?php if(isset($this->vars['userInfo']['phone'])) echo $this->vars['userInfo']['phone']; ?>">
                    <a href="javascript:void(0)" class="btn "  onclick="site_notice.sendTestNotice(2, this)"><i class="  icon-signout"></i> Отправить</a>
                  </div>
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Текст сообщения:</label>
                <div class="controls">
                  <h4><small>Допустимо использовать <a href="javascript:void(0)" style="border-bottom:dashed 1px;">специальные переменные</a></small></h4>
                  <textarea class="span12" name="message" style="height:200px"><?php if (isset($this->vars['noticeInfo']['action_id']) && $this->vars['noticeInfo']['action_id'] == 2): ?><?php if(isset($this->vars['noticeInfo']['data']['text'])) echo $this->vars['noticeInfo']['data']['text']; ?><?php endif; ?></textarea>
                </div>
              </div>
              
            </div>
            
            <!-- local -->
            <div action="3" style="display:none" action_block_name="Параметры локального уведомления">
              <div class="control-group">
                <label class="control-label">Отметка:</label>
                <div class="controls">
                  <a href="javascript:void(0)" class="btn btn-danger"><i class=" icon-check-empty"></i> </a>
                  <a href="javascript:void(0)" class="btn btn-warning"><i class=" icon-check-empty"></i> </a>
                  <a href="javascript:void(0)" class="btn btn-info"><i class=" icon-check-empty"></i> </a>
                  <a href="javascript:void(0)" class="btn btn-success"><i class=" icon-check-empty"></i> </a>
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Текст сообщения:</label>
                <div class="controls">
                  <h4><small>Допустимо использовать <a href="javascript:void(0)" style="border-bottom:dashed 1px;">специальные переменные</a></small></h4>
                  <textarea class="span12" style="height:200px"><?php if (isset($this->vars['noticeInfo']['action_id']) && $this->vars['noticeInfo']['action_id'] == 3): ?><?php if(isset($this->vars['noticeInfo']['data']['text'])) echo $this->vars['noticeInfo']['data']['text']; ?><?php endif; ?></textarea>
                </div>
              </div>
              
            </div>
            
            <!-- json -->
            <div action="4" style="display:none" action_block_name="Параметры отправки JSON уведомления">
              <div class="control-group">
                <label class="control-label">URL принимающей стороны:</label>
                <div class="controls">
                  <input type="text" name="url" class="span12 require" value="<?php if (isset($this->vars['noticeInfo']['action_id']) && $this->vars['noticeInfo']['action_id'] == 4): ?><?php if(isset($this->vars['noticeInfo']['data']['url'])) echo $this->vars['noticeInfo']['data']['url']; ?><?php endif; ?>">
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Метод: </label>
                <div class="controls">
                  <select class="chosen require " name="method" data-placeholder="Укажите какой метод отправки использовать">
                    <option></option>
                    <option value="POST" <?php if (isset($this->vars['noticeInfo']['action_id']) && $this->vars['noticeInfo']['action_id'] == 4): ?><?php if (isset($this->vars['noticeInfo']['data']['method']) && $this->vars['noticeInfo']['data']['method'] == 'post'): ?>selected="selected"<?php endif; ?><?php endif; ?>>POST (content-tyte:text/json)</option>
                    <option value="PUT" <?php if (isset($this->vars['noticeInfo']['action_id']) && $this->vars['noticeInfo']['action_id'] == 4): ?><?php if (isset($this->vars['noticeInfo']['data']['method']) && $this->vars['noticeInfo']['data']['method'] == 'put'): ?>selected="selected"<?php endif; ?><?php endif; ?>>PUT (content-tyte:text/json)</option>
                  </select>
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Дополнительные параметры:</label>
                <div class="controls">
                  <span>Вы можете указать дополнительные параметры которые будут перессылаться. Формат: Ключ = значение</span>
                  <textarea name="extended" class="span12" style="height:100px"><?php if (isset($this->vars['noticeInfo']['action_id']) && $this->vars['noticeInfo']['action_id'] == 4): ?><?php if(isset($this->vars['noticeInfo']['data']['extended'])) echo $this->vars['noticeInfo']['data']['extended']; ?><?php endif; ?></textarea>
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Тестирование:</label>
                <div class="controls">
                  <a href="javascript:void(0)" class="btn" onclick="site_notice.sendTestNotice(4, this)"><i class="icon-signout"></i> Отправить тестовый пакет</a>
                </div>
              </div>
            </div>

            
            <!-- xml -->
            <div action="5" style="display:none" action_block_name="Параметры отправки XML уведомления">
              <div class="control-group">
                <label class="control-label">URL принимающей стороны:</label>
                <div class="controls">
                  <input type="text" name="url" class="span12 require" value="<?php if (isset($this->vars['noticeInfo']['action_id']) && $this->vars['noticeInfo']['action_id'] == 5): ?><?php if(isset($this->vars['noticeInfo']['data']['url'])) echo $this->vars['noticeInfo']['data']['url']; ?><?php endif; ?>">
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Метод: </label>
                <div class="controls">
                  <select class="chosen require " name="method" data-placeholder="Укажите какой метод отправки использовать">
                    <option></option>
                    <option value="POST" <?php if (isset($this->vars['noticeInfo']['action_id']) && $this->vars['noticeInfo']['action_id'] == 5): ?><?php if (isset($this->vars['noticeInfo']['data']['method']) && $this->vars['noticeInfo']['data']['method'] == 'post'): ?>selected="selected"<?php endif; ?><?php endif; ?>>POST (content-tyte:text/json)</option>
                    <option value="PUT" <?php if (isset($this->vars['noticeInfo']['action_id']) && $this->vars['noticeInfo']['action_id'] == 5): ?><?php if (isset($this->vars['noticeInfo']['data']['method']) && $this->vars['noticeInfo']['data']['method'] == 'put'): ?>selected="selected"<?php endif; ?><?php endif; ?>>PUT (content-tyte:text/json)</option>
                  </select>
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Дополнительные параметры:</label>
                <div class="controls">
                  <span>Вы можете указать дополнительные параметры которые будут перессылаться. Формат: Ключ = значение</span>
                  <textarea class="span12" name="extended" style="height:100px"><?php if (isset($this->vars['noticeInfo']['action_id']) && $this->vars['noticeInfo']['action_id'] == 5): ?><?php if(isset($this->vars['noticeInfo']['data']['extended'])) echo $this->vars['noticeInfo']['data']['extended']; ?><?php endif; ?></textarea>
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Тестирование:</label>
                <div class="controls">
                  <a href="javascript:void(0)" class="btn"  onclick="site_notice.sendTestNotice(5, this)"><i class="icon-signout"></i> Отправить тестовый пакет</a>
                </div>
              </div>
              
            </div>

            
          	          
            
            <button type="button" action="save" class="btn btn-success">Сохранить</button>
			<button type="button" action="back" class="btn">Назад</button>
          </form>
      	</div>
  	</div>
</div>