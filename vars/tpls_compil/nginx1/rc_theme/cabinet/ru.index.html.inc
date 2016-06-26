<? $this->md5_compil='9bdb9df493ba62ebc58bf72b8193d110'; ?>
<h2>Добро пожаловать</h2>
<p>В панели управления вы можете просматривать и изменять общую информацию об аккаунте,<br> просматривать ваши недавние заказы.</p>
<h1>Общая информация</h1>
<div class="index-block">
  <div class="personal block-left" style="border:2px solid #D5D5D5; border-width:2px 1px 2px 0;">
    <div>
      <div class="widget-button"><a href="#/my" class="action-icon edit"></a></div>
      <h3 class="cabinet-block-icon">Персональные данные</h3>
      
      <div class="strong-data">
        <div name="last_name">{{user.contact.surname}}</div>
        <div name="name">{{user.contact.name}} {{user.contact.lastname}}</div>
        <div name="email">{{user.contact.email}}</div>
      </div>
      <a href="#/my/changepassword" class="small_button ">Изменить пароль</a>
    </div>
  </div>
  
  <!--<div class="address block-right">
    <div class="widget-button"><a href="#/address/add" class="action-icon add"></a><a href="#/address/edit" class="action-icon edit"></a></div>
    <h3 class="cabinet-block-icon">Адреса доставки</h3>
    <div class="normal-data"> 
      {% if address|length > 0 %}
      	<div name="address1">{{address.zip}} г.{{address.city}} {{address.region}}</div>
      	<div name="address2">{{address.street}} {{address.house}} {{address.building}}</div>
      	<div name="address3">кв. {{address.flat}}</div>
      {% else %}
      	<div name="address1">У Вас нет адресов</div>
      {% endif %}
      <div name="phone">{{user.contact.phone}}</div>
      {% if address|length > 1 %}
      	<a href="#/address" class="small_button">Все адреса</a>
      {% endif %}
    </div>
  </div>-->
  
  <div class="discount block-left" style="border:2px solid #D5D5D5; border-width:2px 0px 2px 1px;">
    <h3 class="cabinet-block-icon">Ценовая категория</h3>
    <div class="normal-data">
      <div name="price_category"><strong>Текущая категория:</strong> <span class="prica-category1">1 колонка</span></div>
      <br>
      <div>Чтобы перейти в следующую ценовую категорию Вам нужно в этом месяце приобрести товаров на сумму: 100 000руб.</div>
    </div>
  </div>
  
  <!--<div class="billing block-right">
    <h3 class="cabinet-block-icon">Реквизиты </h3>
    <a href="#/billing_information" class="small_button ">Полние реквизиты</a>
  </div>-->
  <br style="clear:both">
</div>

<div class="orders">
  <h1>Недавние заказы</h1>
  
  {% if orders|length %}
  
  <a href="#/history" class="small_button right_button">Все заказы</a>
  <table class="orders">
    <thead>
      <tr>
        <th>Заказ #</th>
        <th>Дата</th>
        <th>Адрес доставки</th>
        <th>Стоимость заказа</th>
        <th>Статус</th>
      </tr>
    </thead>
    <tbody>
      {% for order in orders %}
      
      <tr>
        <td class="num">{{order.order_id}}</td>
        <td>{{order.create_date}}</td>
        <td style="text-align:right">{{order.sum}}  руб.</td>
        <td>{{order.address}}</td>
        <td>
          <span class="orderstatus">{{order.order_status_name}}</span> | 
          <span class="paystatus">{{order.order_pay_name}}</span>
        </td>
      </tr>
      
      {% endfor %}
    </tbody>
  </table>
  {% else %}
  <h2>Вы еще не делали заказы.</h2>
  
  {% endif %}
</div>