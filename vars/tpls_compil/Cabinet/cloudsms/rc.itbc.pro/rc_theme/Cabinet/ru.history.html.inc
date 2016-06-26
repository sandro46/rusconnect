<? $this->md5_compil='20900d4743c191ba0dccc519cef6de29'; ?>
<h1>История заказов</h1>
{% if orders|length %}
  
<table class="orders">
  <thead>
    <tr>
      <th>Заказ #</th>
      <th>Дата</th>
      <th>Адрес доставки</th>
      <th>Стоимость заказа</th>
      <th>Статус</th>
      <th>Действия</th>
    </tr>
  </thead>
  <tbody>
    {% for order in orders %}

    <tr>
      <td class="num"><a href="#/history/show/{{order.order_id}}">{{order.order_id}}</a></td>
      <td>{{order.create_date}}</td>
      <td style="text-align:right">{{order.sum}}  руб.</td>
      <td>{{order.address}}</td>
      <td>
        <span class="orderstatus">{{order.order_status_name}}</span> <br>
        <span class="paystatus">{{order.order_pay_name}}</span>
      </td>
      <td>
        {% if order.status_id == 1 %}
        	<a href="#/history/edit/{{order.order_id}}">Редактировать</a><br>
        {% endif %}
        
        {% if order.status_id in ['1','2']  %}
        	<a href="#/history/edit/{{order.order_id}}">Отменить</a><br>
        	<a href="#/history/pay/{{order.order_id}}">Оплатить</a><br>
        {% endif %}
      </td>
    </tr>

    {% endfor %}
  </tbody>
</table>
{% else %}
<h2>Вы еще не делали заказы.</h2>
{% endif %}