<? $this->md5_compil='5647776efa3b0d4d7ecc8ad9f2f01211'; ?>
<div class="row-fluid">
	<div class="widget tab-widget-product">
		<div class="widget-title">
           <span class="tools tab-header" name="order" style="float: left;"><span class="item"><i class="icon-cog"></i> Заказ</span></span>
           <span class="tools tab-header" name="delivery" style="float: left;"><span class="item"><i class="icon-globe"></i> Доставка</span></span>
		   <span class="tools tab-header" name="contact" style="float: left;"><span class="item"><i class="icon-globe"></i> Контакт</span></span>
           <!--<span class="tools tab-header" name="payment" style="float: left;"><span class="item"><i class="icon-globe"></i> Оплата</span></span>-->
      	</div>
      	<div class="widget-body">
          <div class="settings-form tab-body" section="order">
            <form class="form-horizontal ">
              <div class="control-group">
                <label class="control-label">Статус заказа:</label>
                <div class="controls">
                 	<select name="o.status_id" class="chosen require" data-placeholder="Выберите статус">
                      <option></option>  
                      <?php $this->vars['orderStatusId'] = $this->vars['orderInfo']['status_id']; ?>
                      <?php if(count($this->vars['statuses'])) : 
							   		 $this->vars['foreach']['statuses']['count'] = count($this->vars['statuses']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['statuses']);
							   		 foreach($this->vars['statuses'] as $this->vars['foreach']['statuses']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['statuses']['key']; ?>
                      <option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>" <?php if (isset($this->vars['orderStatusId']) && $this->vars['orderStatusId'] == $this->vars['item']['id']): ?>selected="selected"<?php endif; ?>><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                      <?php endforeach; ?><?php endif; ?>
                    </select>
                </div>
              </div>
              <h3>Товары:</h3>
              <table class="table table-striped table-hover" name="prodList">
                <tr>
                  <td>#</td>
                  <td>Название</td>
                  <td>Цена</td>
                  <td>Скидка</td>
                  <td>Кол-во</td>
                  <td>Сумма</td>
                  <td>-</td>
                </tr>
              	<?php if(count($this->vars['orderInfo']['items'])) : 
							   		 $this->vars['foreach']['orderInfo.items']['count'] = count($this->vars['orderInfo']['items']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['orderInfo']['items']);
							   		 foreach($this->vars['orderInfo']['items'] as $this->vars['foreach']['orderInfo_items']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['orderInfo_items']['key']; ?>
                  <tr>
                    <td><?php if(isset($this->vars['item']['order_item_id'])) echo $this->vars['item']['order_item_id']; ?></td>
                    <td><?php if(isset($this->vars['item']['product_name'])) echo $this->vars['item']['product_name']; ?></td>
                    <td class="price" priceClear="<?php if(isset($this->vars['item']['product_cost'])) echo $this->vars['item']['product_cost']; ?>"><?php if(isset($this->vars['item']['product_cost'])) echo $this->vars['item']['product_cost']; ?> руб.</td>
                    <td><?php if(isset($this->vars['item']['product_action_procent'])) echo $this->vars['item']['product_action_procent']; ?>% (<?php if(isset($this->vars['item']['product_action_type_name'])) echo $this->vars['item']['product_action_type_name']; ?>)</td>
                    <td><input type="text" name="prod_count" style="width:20px;" value="<?php if(isset($this->vars['item']['count'])) echo $this->vars['item']['count']; ?>"></td>
                    <td class="summ"><?php if(isset($this->vars['item']['row_total'])) echo $this->vars['item']['row_total']; ?></td>
                    <td>
                      <a href="javascript:void(0)" onclick="admin_order.removeProdFromOrder(this, <?php if(isset($this->vars['orderInfo']['order_id'])) echo $this->vars['orderInfo']['order_id']; ?>, <?php if(isset($this->vars['item']['product_id'])) echo $this->vars['item']['product_id']; ?>)" class="" style="font-size:18px"><i class="icon-trash"></i></a>
                    </td>
                  </tr>	
                <?php endforeach; ?><?php endif; ?>
              </table>
              <a href="javascript:void(0)" action="addProduct" class="btn btn-primary" style="float:right"><i class="icon-plus"></i> Добавить товар</a>
            
              <div id="menuCatalog" class="menuCatalogContent" style="display:none; position: absolute;">
                 <ul id="menuCatalogTree" class="ztree dropdown-menu" style="margin-top:0; min-width: 208px;"></ul>
              </div>
            </form>
          </div>
          
          <div class="settings-form tab-body" section="delivery">
            <form class="form-horizontal ">
              <div class="control-group">
                <label class="control-label">Почтовый индекс:</label>
                <div class="controls"><input class="span12 require" type="text" name="a.zip" value="<?php if(isset($this->vars['orderInfo']['contact']['address']['zip'])) echo $this->vars['orderInfo']['contact']['address']['zip']; ?>" /></div>
              </div>
              
              
              <div class="control-group">
                <label class="control-label">Регион, город:</label>
                <div class="controls"><input class="span12 require" type="text" name="a.address1" value="<?php if(isset($this->vars['orderInfo']['contact']['address']['address1'])) echo $this->vars['orderInfo']['contact']['address']['address1']; ?>" /></div>
              </div>
              <div class="control-group">
                <label class="control-label">Адрес:</label>
                <div class="controls"><input class="span12 require" type="text" name="a.address2" value="<?php if(isset($this->vars['orderInfo']['contact']['address']['address2'])) echo $this->vars['orderInfo']['contact']['address']['address2']; ?>" /></div>
              </div>
            </form>
          </div>
          
		  <div class="settings-form tab-body" section="contact">
            <form class="form-horizontal ">
              <div class="control-group">
                <label class="control-label">Ф.И.О:</label>
                <div class="controls"><input class="span12 require" type="text" name="c.fio" value="<?php if(isset($this->vars['orderInfo']['contact']['name'])) echo $this->vars['orderInfo']['contact']['name']; ?>" /></div>
              </div>
              
              
              <div class="control-group">
                <label class="control-label">Телефон:</label>
                <div class="controls"><input class="span12 " type="text" name="c.phone" value="<?php if(isset($this->vars['orderInfo']['contact']['phone'])) echo $this->vars['orderInfo']['contact']['phone']; ?>" /></div>
              </div>
              <div class="control-group">
                <label class="control-label">Email:</label>
                <div class="controls"><input class="span12 require" type="text" name="c.email" value="<?php if(isset($this->vars['orderInfo']['contact']['email'])) echo $this->vars['orderInfo']['contact']['email']; ?>" /></div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Источник:</label>
                <div class="controls">
                
                	<select name="c.source_id" class="chosen require" data-placeholder="Выберите источник лида">
                      <option></option>  
                      <?php $this->vars['contactSourceId'] = $this->vars['orderInfo']['contact']['source_id']; ?>
                      <?php if(count($this->vars['contactSources'])) : 
							   		 $this->vars['foreach']['contactSources']['count'] = count($this->vars['contactSources']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['contactSources']);
							   		 foreach($this->vars['contactSources'] as $this->vars['foreach']['contactSources']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['contactSources']['key']; ?>
                      <option value="<?php if(isset($this->vars['item']['source_id'])) echo $this->vars['item']['source_id']; ?>" <?php if (isset($this->vars['contactSourceId']) && $this->vars['contactSourceId'] == $this->vars['item']['source_id']): ?>selected="selected"<?php endif; ?>><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                      <?php endforeach; ?><?php endif; ?>
                    </select>
                
                </div>
              </div>
              
            </form>
          </div>
          
          <form class="form-horizontal"> 
            <button type="button" action="saveOrder" class="btn btn-success">Сохранить</button>
			<button type="button" action="back" class="btn">Назад</button>
          </form>
      	</div>
  	</div>
</div>