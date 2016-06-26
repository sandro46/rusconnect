<? $this->md5_compil='04429025413a973e337ef928910d2090'; ?>
<div class="row-fluid">
  <div class="widget mediumWidget">
    <div class="widget-title"><h4><i class="icon-edit"></i> Редактирование модификации</h4></div>
    <div class="widget-body">
      <form class="form-horizontal">

        
        <div class="control-group">
          <label class="control-label">Наименование</label>
          <div class="controls"><input class="span12 require" type="text" name="m.name" /></div>
        </div>
        <div class="control-group">
          <label class="control-label">Основной параметр</label>
          <div class="controls"><input class="span12 " type="text" name="m.description" /></div>
        </div>
        <div class="control-group">
          <label class="control-label">Артикул</label>
          <div class="controls"><input class="span4 " type="text" name="m.article" /></div>
        </div>
        <div class="control-group">
          <label class="control-label">Цена товара</label>
          <div class="controls">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Цена 1</th>
                  <th>Цена 2</th>
                  <th>Цена 3</th>
                  <th>Цена 4</th>
                  <th>Цена 5</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><input class="span12" type="text" name="m.price1" /></td>
                  <td><input class="span12" type="text" name="m.price2" /></td>
                  <td><input class="span12" type="text" name="m.price3" /></td>
                  <td><input class="span12" type="text" name="m.price4" /></td>
                  <td><input class="span12" type="text" name="m.price5" /></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="control-group"  name="m.pricetype.container">
          <label class="control-label">Тип цены</label> 
          <div class="controls">
            <label class="radio">
              <input type="radio" name="m.price_type" class="require" value="1" checked="checked" />
              Цена указана за единицу товара
            </label>
          </div>
          <div class="controls">
            <label class="radio">
              <input type="radio" name="m.price_type" class="require"  value="2" />
              Цена указана за упаковку
            </label>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Единица измерения товара</label>
          <div class="controls">
            <select style="width:220px;" class="require"  name="m.product_measure" data-placeholder="Выберите параметр">
              <option></option>
              <option value="61">Шт</option>
              <option value="57">Кг</option>
              <option value="7">м</option>
            </select>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label">Кол-во товара в упаковке</label>
          <div class="controls">
            <input style="width:80px;" class="require"  type="text" name="m.pakage_size" />
          </div>
        </div>

        <div class="control-group" name="m.minorder.container">
          <label class="control-label">Минимальный заказ</label>
          <div class="controls">
            <label class="radio">
              <input type="radio" name="m.min_order_type" class="require"  value="1" />
              Упаковка
            </label>
          </div>
          <div class="controls">
            <label class="radio">
              <input type="radio" name="m.min_order_type" class="require"  value="2" checked="checked" />
              Штука
            </label>
          </div>
        </div>
        <div class="form-actions">
			<button type="button" action="save" class="btn btn-success">Сохранить</button>
			<button type="button" action="back" class="btn">Назад</button> 
		</div>
      </form>
    </div>
  </div>
</div>