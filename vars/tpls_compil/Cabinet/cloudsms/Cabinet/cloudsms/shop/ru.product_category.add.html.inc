<? $this->md5_compil='343f55b70f0aa924dde16fbb9255a9dc'; ?>
<div class="row-fluid">
	<div class="widget mediumWidget">
		<div class="widget-title">
          <h4><i class="icon-reorder"></i>Новая категория</h4>
      	</div>
      	<div class="widget-body">
          <form class="form-horizontal">
            <div class="control-group">
				<label class="control-label">Название:</label>
              	<div class="controls">
                  <input class="require" type="text" name="feature_category_name">
              	</div>
            </div>
            <div class="controls" style="display:none" id="CategoryFeatureTpl" box-number="-1">
              <div class="input-prepend input-append feature-num-with-unit">
				<table>
                  <tr>
                    <td>
                      <select is_feature_input="true" skip_get_form="true" name="type" class="add-on feture-type-select" placeholder="Тип характеристики">   
                        <option value="2">Набор вариантов</option>
                        <option value="1">Опция (да/нет)</option>
                        <option value="4" selected="selected">Значение (текс)</option>
                        <option value="3" selected="selected">Значение (число)</option>
                      </select> 
                    </td>
                    <td><input is_feature_input="true" skip_get_form="true" type="text" name="name" class="name-element" placeholder="Название"></td>
                    <td class="variant-box" ftype="3" >
                      <select is_feature_input="true" skip_get_form="true" class="add-on numeric-select " name="unit" placeholder="Выберите единицу измерения">
                        <?php if(count($this->vars['feature_unit_list'])) : 
							   		 $this->vars['foreach']['feature_unit_list']['count'] = count($this->vars['feature_unit_list']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['feature_unit_list']);
							   		 foreach($this->vars['feature_unit_list'] as $this->vars['foreach']['feature_unit_list']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['feature_unit_list']['key']?>
                        <optgroup label="<?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?>">
                          <?php if(count($this->vars['item']['list'])) : 
							   		 $this->vars['foreach']['item.list']['count'] = count($this->vars['item']['list']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['item']['list']);
							   		 foreach($this->vars['item']['list'] as $this->vars['foreach']['item_list']['key'] => $this->vars['opt']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['item_list']['key']?>
                          <option value="<?php if(isset($this->vars['opt']['id'])) echo $this->vars['opt']['id']; ?>"><?php if(isset($this->vars['opt']['name'])) echo $this->vars['opt']['name']; ?></option>
                          <?php endforeach; ?><?php endif; ?>
                        </optgroup>
                        <?php endforeach; ?><?php endif; ?>
                      </select>  
                    </td>
                    <td class="variant-box" ftype="2" style="display:none">
                      <a class="btn" href="javascript:void(0)" onclick="$(this).parent().parent().parent().find('.variantsList').toggle(); $(this).parent().parent().parent().find('.variantAdd').toggle(); ">Изменить набор вариантов</a>
                    </td>
                    <td class="variant-buttons">
						<!--<a class="btn btnAddVariant" href="javascript:void(0)" style="margin-left:3px"><span class=" icon-plus"></span></a>-->
                		<a class="btn" href="javascript:void(0)" onclick="$(this).closest('div[box-number].controls').remove()" style="margin-left:3px"><span class=" icon-trash"></span></a>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="4" class="variantsList" style="display:none">
                      <!--<input type="text" is_feature_input="true" skip_get_form="true" name="varianе"><a class="btn"  href="javascript:void(0)" onclick="$(this).parent().remove()"><span class=" icon-trash"></span></a>-->
                    </td>
                  </tr>
                  <tr>
                  	<td  colspan="4" class="variantAdd" style="display:none"><button class="btn btn-small" type="button" style="margin-top: 2px;" name="addVarianTo"><i class=" icon-plus"></i> Добавить вариант</button></td>
                  </tr>
                </table>
              </div>
            </div>
            <div class="controls otherFeatureButton" id="otherFeatureButtonContainer" style="margin-top:10px"> 
              <button class="btn" type="button" style="margin-top: 2px;" onclick="shopMain.addFeatureBoxForCat(this)"><i class=" icon-plus"></i> Добавить характеристику</button>
            </div>
            <br style="clear:both">
            <button type="button" action="save" class="btn btn-success">Сохранить</button>
			<button type="button" action="back" class="btn">Назад</button>
          </form>
      	</div>
  	</div>
</div>