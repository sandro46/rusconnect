<? $this->md5_compil='8c12874980b93243042fe858bbd8abaa'; ?>
<div class="row-fluid">


             <div class="row-fluid">
                 <!--<a class="icon-btn span2" href="javascript:void(0)" onclick="$page.print()">
                     <i class="icon-print"></i>
                     <div>Распечатать</div>
                 </a>
               	<a class="icon-btn span2" href="#/show/<?php if(isset($this->vars['orderId'])) echo $this->vars['orderId']; ?>/edit/">
                     <i class=" icon-edit"></i>
                     <div>Изменить заказ</div>
                 </a>
                 <a class="icon-btn span2 orderUndoLink" href="javascript:void(0)" >
                     <i class="icon-remove" style="color:red"></i>
                     <div>Отменить заказ</div>
                 </a>-->
               <a href="javascript:void(0)" onclick="$page.back()" class="btn"><i class="icon-arrow-left"></i> Назад</a>
               <a href="#/show/<?php if(isset($this->vars['orderId'])) echo $this->vars['orderId']; ?>/edit/" class="btn btn-primary"><i class="icon-edit"></i> Изменить заказ</a>
             </div>
             <div class="space10"></div>
            <div class="row-fluid">
               <div class="span12">
                  <div class="widget">
                        <div class="widget-title">
                          <h4><i class="icon-edit"></i>Заказ №<span class="invoiceOrderName"></span></h4>                   
                        </div>
                        <div class="widget-body">

                            <div class="row-fluid invoice-list">
                              	<div class="span4" style="width:200px">
                                    <h5>Способ оплаты</h5>
                                    <p class="payVariant"></p>
                                </div>
                                <div class="span4" style="width:300px">
                                    <h5>Адрес доставки</h5>
                                    <p class="deliveryAddress"></p>
                                    
                                </div>
                                <div class="span4">
                                    <h5>Клиент</h5>
                                    <ul class="unstyled deliveryOrderClient"></ul>
                                </div>
                              
                              	
                            </div>
                            <div class="space20"></div>
                            <div class="row-fluid">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Название товара</th>
                                        <th class="hidden-480">Цена</th>
                                        <th class="hidden-480">Кол-во</th>
                                        <th>Сумма</th>
                                    </tr>
                                    </thead>
                                    <tbody class="orderItemList"></tbody>
                                </table>
                            </div>
                            <div class="space20"></div>
                            <div class="row-fluid">
                              	<div class="span4">
                                  <a href="javascript:void(0)" onclick="$page.back()" class="btn"><i class="icon-arrow-left"></i> Назад</a>
                                  <a href="#/show/<?php if(isset($this->vars['orderId'])) echo $this->vars['orderId']; ?>/edit/" class="btn btn-primary"><i class="icon-edit"></i> Изменить заказ</a>
                              	</div>
                                <div class="span4 invoice-block pull-right">
                                    <ul class="unstyled amounts orderAmounts">
                                        
                                    </ul>
                                </div>
                            </div>
                            <div class="space20"></div>
                             
                           
                          
                          	<div class="row-fluid">
                              <div class="widget-title">
                                <h4><i class="icon-edit"></i>История выполнения заказа</h4>                   
                              </div>
                              <div class="widget-body">
                                <?php if(count($this->vars['orderHistory'])) : 
							   		 $this->vars['foreach']['orderHistory']['count'] = count($this->vars['orderHistory']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['orderHistory']);
							   		 foreach($this->vars['orderHistory'] as $this->vars['foreach']['orderHistory']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['orderHistory']['key']; ?>
                                  <!-- Comment -->
                                  <div class="msg-time-chat">
                                    <a href="#" class="message-img"><img class="avatar" src="img/avatar1.jpg" alt=""></a>
                                    <div class="message-body msg-in" >
                                      <div class="text">
                                        <p class="attribution">
                                          <a href="#"><?php if(isset($this->vars['item']['name_first'])) echo $this->vars['item']['name_first']; ?><?php if (isset($this->vars['item']['name_last']) && $this->vars['item']['name_last']  ): ?> <?php if(isset($this->vars['item']['name_last'])) echo $this->vars['item']['name_last']; ?><?php endif; ?></a> 
                                          <?php if(isset($this->vars['item']['date'])) echo $this->vars['item']['date']; ?> 
                                          <?php if (isset($this->vars['item']['event_alias']) && $this->vars['item']['event_alias'] == 'comment'): ?><b><?php if(isset($this->vars['item']['event_name'])) echo $this->vars['item']['event_name']; ?>:</b><?php endif; ?>
                                        </p>
                                        
                                        <?php if (isset($this->vars['item']['event_alias']) && $this->vars['item']['event_alias'] != 'comment'): ?>
                                        	<p><?php if(isset($this->vars['item']['event_name'])) echo $this->vars['item']['event_name']; ?></p>
                                        <?php endif; ?>
                                        <p><?php if (isset($this->vars['item']['event_alias']) && $this->vars['item']['event_alias'] != 'comment'): ?><b><?php if(isset($this->vars['item']['comment'])) echo $this->vars['item']['comment']; ?></b><?php else: ?><?php if(isset($this->vars['item']['comment'])) echo $this->vars['item']['comment']; ?><?php endif; ?></p>
                                      </div>
                                    </div>
                                  </div>
                                <?php endforeach; ?><?php endif; ?>
                                  
                                
                              </div>
                          	</div>	
                        </div>
                  </div>
               </div>
            </div>