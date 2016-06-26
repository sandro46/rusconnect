<? $this->md5_compil='a21535acdeb4afdbe2b69022a01af070'; ?>
<div class="row-fluid">
  <div class="span12">
    <div class="widget mediumWidget">
      <div class="widget-title">
        <h4><i class="icon-user"></i>Профиль клиента</h4>                
      </div>
      <div class="widget-body">
        <div class="span3">
          <div class="text-center profile-pic">
            <img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>img/profile-pic.jpg" alt="">

          </div>
          <ul class="nav nav-tabs nav-stacked">
            <li><a href="javascript:void(0)"><i class="icon icon-pencil"></i> Редактировать профиль</a></li>
          </ul>

          <ul class="nav nav-tabs nav-stacked">
            <li><a href="javascript:void(0)"> Создать заказ</a></li>
            <li><a href="javascript:void(0)"> Написать в чат</a></li>
          </ul>
        </div>
        <div class="span6">

          <h4 class="userName"></h4>

          <table class="table table-borderless tableFeatures" name="userContactInfo">
            <tbody>
              <tr>
                <td class="span2">Email :</td>
                <td name="email"></td>
              </tr>
              <tr>
                <td class="span2">Телефон :</td>
                <td name="phone"></td>
              </tr>
              <tr>
                <td class="span2">Основной адрес :</td>
                <td name="address"></td>
              </tr>
            </tbody>
          </table>
          <br>
          <div name="userBillingInfoContainer">
            <h4>Данные юр.лица</h4>
            <table class="table table-borderless tableFeatures">
              <tbody>
                <tr>
                  <td class="span2">Компания :</td>
                  <td name="company"></td>
                </tr>
                <tr>
                  <td class="span2">Юр. Адрес :</td>
                  <td name="legal_address"></td>
                </tr>
                <tr>
                  <td class="span2">ИНН :</td>
                  <td name="inn"></td>
                </tr>
                <tr>
                  <td class="span2">ОГРН :</td>
                  <td name="ogrn"></td>
                </tr>
                <tr>
                  <td class="span2">Банк :</td>
                  <td name="bank"></td>
                </tr>
                <tr>
                  <td class="span2">БИК :</td>
                  <td name="bik"></td>
                </tr>
                <tr>
                  <td class="span2">Счет № :</td>
                  <td name="bill"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="span3">
          <h4>Статус клиента</h4>
          <span class="label">Обычный</span> Цена: <span class="badge badge-warning pricelabel3">1</span>

          <h4>Менеджер</h4>
          <a href="/ru/users/#/show/1088/">Сергеев Сергей</a>
        </div>
        <div class="space5"></div>
      </div>
    </div>
    
    
    <div class="widget mediumWidget">
      <div class="widget-title">
        <h4>Заказы клиента</h4>                  
      </div>
      <div class="widget-body">
        
         <?php
if(!isset($this->vars['globalgrid'])) $this->vars['globalgrid'] = array();
$this->vars['globalgrid']['orders_local_list'] = json_decode('{"params":{"name":"orders_local_list","tpl":"grid.default.html","class":"admin_shop","method":"getOrdersList"},"settings":{"cols":[{"name":"order_id","title":"№","search":true,"sort":true,"sorted":true,"sort_type":"desc"},{"name":"create_date","title":"Дата заказа","search":true,"sort":true},{"name":"sum","title":"Сумма","search":true,"sort":true,"sorted":false},{"name":"order_status_name","title":"Статус заказа","search":true,"sort":true},{"name":"order_pay_name","title":"Статус оплаты","search":true,"sort":true}],"autoload":false,"view":"list","limit":10,"click":"true"},"instance":{"name":"orders_local_list","tpl":"grid.default.html","class":"admin_shop","method":"getOrdersList"}}', true);
$this->vars['instance'] = 'orders_local_list';
$this->vars['grid'] = json_decode('{"params":{"name":"orders_local_list","tpl":"grid.default.html","class":"admin_shop","method":"getOrdersList"},"settings":{"cols":[{"name":"order_id","title":"№","search":true,"sort":true,"sorted":true,"sort_type":"desc"},{"name":"create_date","title":"Дата заказа","search":true,"sort":true},{"name":"sum","title":"Сумма","search":true,"sort":true,"sorted":false},{"name":"order_status_name","title":"Статус заказа","search":true,"sort":true},{"name":"order_pay_name","title":"Статус оплаты","search":true,"sort":true}],"autoload":false,"view":"list","limit":10,"click":"true"},"instance":{"name":"orders_local_list","tpl":"grid.default.html","class":"admin_shop","method":"getOrdersList"}}', true);
global $core;
$grid_tpl_site = ($core->is_admin())? $core->getAdminModule() : $core->site_name;
echo $this->get('grid.default.html',$grid_tpl_site);

unset($this->vars['grid'], $this->vars['instance']);
?>

<script type='text/javascript'>
 //try { 
   grid.orders_local_list = new GridInstance('orders_local_list'); 
   grid.orders_local_list.params = JSON.parse('{"name":"orders_local_list","tpl":"grid.default.html","class":"admin_shop","method":"getOrdersList"}'); 
   grid.orders_local_list.settings = JSON.parse('{"cols":[{"name":"order_id","title":"№","search":true,"sort":true,"sorted":true,"sort_type":"desc"},{"name":"create_date","title":"Дата заказа","search":true,"sort":true},{"name":"sum","title":"Сумма","search":true,"sort":true,"sorted":false},{"name":"order_status_name","title":"Статус заказа","search":true,"sort":true},{"name":"order_pay_name","title":"Статус оплаты","search":true,"sort":true}],"autoload":false,"view":"list","limit":10,"click":"true"}'); 
   grid.orders_local_list.init(); 
 //} catch(err) { 
 //  alert('Error create grid instance. '+err.message); 
 //} 
</script>
   
        
      </div>
    </div>
    

    
  </div>
</div>