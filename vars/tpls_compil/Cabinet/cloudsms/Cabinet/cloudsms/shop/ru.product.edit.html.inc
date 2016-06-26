<? $this->md5_compil='81d84f506df38fefee81280a10046c0f'; ?>
<div class="crumbs" id="productEditCrumbs"></div>
<div class="row-fluid">
	<div class="widget tab-widget-product">
		<div class="widget-title">
            <span class="tools tab-header" name="main" style="float: left;"><span class="item"><i class="icon-cog"></i> Общая информация</span></span>
			<span class="tools tab-header" name="price" style="float: left;"><span class="item"><i class="icon-money"></i> Цены / Скидки / Акции</span></span>
			<span class="tools tab-header" name="features" style="float: left;"><span class="item"><i class=" icon-check"></i> Характеристики</span></span>
            <span class="tools tab-header" visible="hideifnew" name="modification" style="float: left;"><span class="item"><i class="icon-sitemap"></i> Модификации</span></span>
          <!--<span class="tools tab-header" name="cross_selling" style="float: left;"><span class="item"><i class="icon-globe"></i> Сопутствующие товары</span></span>-->
			<!--<span class="tools tab-header" name="seo" style="float: left;"><span class="item"><i class="icon-globe"></i> SEO</span></span>-->
		</div>
		<div class="widget-body">
			<div class="settings-form tab-body" section="main">
				<form class="form-horizontal ">
					<div class="control-group">
						<label class="control-label">Название продукта</label>
						<div class="controls"><input class="span12" type="text" name="p.name" /></div>
					</div>
                  	<div class="control-group">
						<label class="control-label">Артикул</label>
						<div class="controls"><input class="span12" type="text" name="p.article" /></div>
					</div>
                  	<div class="control-group">
						<label class="control-label">URL</label>
                      	<div class="controls">
                          <div class="input-prepend input-append span12">
                            <span class="add-on"><?php if(isset($this->vars['shopInfo']['url'])) echo $this->vars['shopInfo']['url']; ?>/</span>
                              <input class="span8" type="text" placeholder="" name="p.url">
                              <a href="javascript:void(0)" class="btn" name="p.updare-rewrite"><i class="icon-refresh"></i></a>
                          </div>
                      </div>
					</div>
					<div class="control-group">
						<label class="control-label">Ктегория</label>
						<div class="controls">
                          	<div id="extendedCategoryList">
                          	
                          	</div>
							<button type="button" class="btn" onclick="shopMain.addExtendedCategory()"><i class=" icon-plus"></i> Дополнительная категория</button>
						</div>
					</div>
					<div class="control-group " id="pSkladAvaliableBlock">
						<label class="control-label">Наличие на складе</label>
						

						<div class="controls" >
                          <div>
                            <label class="radio">
								<input type="radio" class="nouniform" name="p.avaliable" value="1" />
								Есть на складе
							</label>
							<label class="radio">
								<input type="radio" class="nouniform" name="p.avaliable" value="2" />
								Под заказ
							</label>  
							<label class="radio">
								<input type="radio" class="nouniform" name="p.avaliable" value="3" />
								Нет в наличии
							</label>  
                          </div>
						</div>
									
						
						<div class="controls groupradio sclad" style="margin-top:5px; display:block">
                          <span style="position: relative; top: -9px;">Единиц товара:</span> <input class="span2" type="text" name="p.avaliable_qt" placeholder="Кол-во" value="1" style="position: relative; margin-top: -21px;" />
							
							
						</div>
						
						<div class="controls groupradio fororder" style="display:none">
							Время ожидания заказа (дней): <input class="span2" type="text" name="p.avaliable_pending" placeholder="" />
						</div>										
					</div>
                  
                  	<div class="control-group">
						<label class="control-label">Опубликовать на сайте</label>
						<div class="controls">
                          <div class="product_public_in_site_switch"><input type="checkbox" checked="checked" name="p.published" value="1" class="toggle" /></div>
                      	</div>
					</div>
                  
                  	<div class="control-group">
						<label class="control-label">Где отображать товар</label>
						<div class="controls">
                          <select data-placeholder="Выберите блок в котором отображать товар" class="chosen" style="width:600px;" name="p.feeds"  multiple="multiple">
                            <option></option>
                            
                            <?php if(count($this->vars['product_feeds'])) : 
							   		 $this->vars['foreach']['product_feeds']['count'] = count($this->vars['product_feeds']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['product_feeds']);
							   		 foreach($this->vars['product_feeds'] as $this->vars['foreach']['product_feeds']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['product_feeds']['key']; ?>
                            <option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>" <?php if (isset($this->vars['item']['feed_use']) && $this->vars['item']['feed_use']  ): ?>selected=selected<?php endif; ?> <?php if (isset($this->vars['isNewProduct']) && $this->vars['isNewProduct']  ): ?><?php if (isset($this->vars['item']['id']) && $this->vars['item']['id'] == 1): ?>selected=selected<?php endif; ?><?php endif; ?>><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                            <?php endforeach; ?><?php endif; ?>
                          </select>
                      	</div>
					</div>
                  
					<div class="control-group">
						<label class="control-label">Короткое описание</label>
						<div class="controls"><textarea class="span12" name="p.description"></textarea></div>
					</div>
                  
                  	
					
					<div class="control-group">
						<h4>Изображения товара</h4>
						
                        <div id="productImageUploadBar"></div>
						<div id="productImageGallery" class="controls span12" style="min-height:0px;"></div>
					</div>
				
					<div class="control-group">
						<h4>Полное описание</h4>	
						<div class="controls" style="margin-left:0px; width:100%" id="productFormTextEditor"></div>
					</div>
                  
                  	<div class="control-group">
						<h4>Видео</h4>	
						<div class="controls" style="margin-left:0px; width:100%" id="productFormTextEditor2"></div>
					</div>
                  
                  	<div class="control-group">
						<h4>Схемы</h4>	
						<div class="controls" style="margin-left:0px; width:100%" id="productFormTextEditor3"></div>
					</div>
				
				</form>
			</div>
	
			<div class="settings-form tab-body" section="price">
				<form class="form-horizontal ">
                  
                  <div class="control-group">
                    <label class="control-label">Цена товара</label>
                    <div class="controls">
                      <p class="alert alert-info">В этом разделе цена указывается только в случае, когда у товара нет модификаций. <br>В противном случае, цена указывается для конкретной модификации.</p>
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
                            <td><input class="span12" type="text" name="p.retail_price" /></td>
                            <td><input class="span12" type="text" name="p.price2" /></td>
                            <td><input class="span12" type="text" name="p.price3" /></td>
                            <td><input class="span12" type="text" name="p.price4" /></td>
                            <td><input class="span12" type="text" name="p.price5" /></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  
                  <div class="control-group">
                    <label class="control-label">Тип цены</label> 
                    <div class="controls">
                      <label class="radio">
                        <input type="radio" name="p.price_type" value="1" checked="checked" />
                        Цена указана за единицу товара
                      </label>
                    </div>
                    <div class="controls">
                      <label class="radio">
                        <input type="radio" name="p.price_type" value="2" />
                        Цена указана за упаковку
                      </label>
                    </div>
                  </div>
                  
                  <div class="control-group">
                    <label class="control-label">Единица измерения товара</label>
                    <div class="controls">
                    	<select style="width:220px;" name="p.product_measure" data-placeholder="Выберите параметр">
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
                      <input style="width:80px;" type="text" name="p.pakage_size" />
                    </div>
                  </div>
                  
                  <div class="control-group">
                    <label class="control-label">Минимальный заказ</label>
                    <div class="controls">
                      <label class="radio">
                        <input type="radio" name="p.min_order_type" value="1" />
                        Упаковка
                      </label>
                    </div>
                    <div class="controls">
                      <label class="radio">
                        <input type="radio" name="p.min_order_type" value="2" checked="checked" />
                        Штука
                      </label>
                    </div>
                  </div>
				</form>
			</div>
          
         	<div class="settings-form tab-body" section="features">
				<form class="form-horizontal ">
                  	<div class="control-group">
						<label class="control-label">Тип товара</label>
						<div class="controls">
                          <span id="productTypeCategory" class=""></span>
                          <button class="btn" onclick="shopMain.addProductCategory()" type="button" style="margin-top: -23px; position: relative;"><i class=" icon-plus"></i> Новый тип товара</button>
                        </div>
					</div>
                     
                  	<div class="control-group">
						<label class="control-label">Производитель</label>
						<div class="controls">
                          <span id="productVendor" class=""></span>
                          <button class="btn" onclick="shopMain.addVendorToProduct()" type="button" style="margin-top: -23px; position: relative;"><i class=" icon-plus"></i> Новый производитель</button>
                      </div>
					</div>
                  
					<div class="control-group">
						<label class="control-label">Габариты в упаковке</label>
						<div class="controls">
                          	<div class="input-prepend input-append feature-num-with-unit" name="width">
                                <span class="add-on">Ширина</span>
                                <input type="text" class="basic-size" name="p.basicFuture[width][]">
                                <select class="add-on unit-select" name="p.basicFuture[width][]">
                                  <?php if(count($this->vars['feature_unit_list']['1']['list'])) : 
							   		 $this->vars['foreach']['feature_unit_list.1.list']['count'] = count($this->vars['feature_unit_list']['1']['list']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['feature_unit_list']['1']['list']);
							   		 foreach($this->vars['feature_unit_list']['1']['list'] as $this->vars['foreach']['feature_unit_list_1_list']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['feature_unit_list_1_list']['key']; ?>
                                  <option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>" <?php if (isset($this->vars['item']['id']) && $this->vars['item']['id'] == 8): ?>selected="selected"<?php endif; ?>><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                                  <?php endforeach; ?><?php endif; ?>
                                </select>
                            </div> 
         				</div>
                        <div class="controls">
                            <div class="input-prepend input-append feature-num-with-unit" name="height">
                               <span class="add-on">Высота</span> 
                               <input type="text" class="basic-size" name="p.basicFuture[height][]">
                               <select class="add-on unit-select" name="p.basicFuture[height][]">
                                  <?php if(count($this->vars['feature_unit_list']['1']['list'])) : 
							   		 $this->vars['foreach']['feature_unit_list.1.list']['count'] = count($this->vars['feature_unit_list']['1']['list']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['feature_unit_list']['1']['list']);
							   		 foreach($this->vars['feature_unit_list']['1']['list'] as $this->vars['foreach']['feature_unit_list_1_list']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['feature_unit_list_1_list']['key']; ?>
                                  <option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>" <?php if (isset($this->vars['item']['id']) && $this->vars['item']['id'] == 8): ?>selected="selected"<?php endif; ?>><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                                  <?php endforeach; ?><?php endif; ?>
                               </select>
                            </div>
						</div>
                      	<div class="controls">
                            <div class="input-prepend input-append feature-num-with-unit" name="depth">
                                <span class="add-on">Глубина</span>
                                <input type="text" class="basic-size" name="p.basicFuture[depth][]">
                                <select class="add-on unit-select" name="p.basicFuture[depth][]">
                                  <?php if(count($this->vars['feature_unit_list']['1']['list'])) : 
							   		 $this->vars['foreach']['feature_unit_list.1.list']['count'] = count($this->vars['feature_unit_list']['1']['list']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['feature_unit_list']['1']['list']);
							   		 foreach($this->vars['feature_unit_list']['1']['list'] as $this->vars['foreach']['feature_unit_list_1_list']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['feature_unit_list_1_list']['key']; ?>
                                  <option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>" <?php if (isset($this->vars['item']['id']) && $this->vars['item']['id'] == 8): ?>selected="selected"<?php endif; ?>><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                                  <?php endforeach; ?><?php endif; ?>
                              	</select>
                            </div>
                        </div>
                      	<div class="controls"> 
                            <div class="input-prepend input-append feature-num-with-unit" name="weight">
                                <span class="add-on">Вес</span>
                                <input type="text" class="basic-size" name="p.basicFuture[weight][]">
                              	<select class="add-on unit-select" name="p.basicFuture[weight][]">
                                  <?php if(count($this->vars['feature_unit_list']['55']['list'])) : 
							   		 $this->vars['foreach']['feature_unit_list.55.list']['count'] = count($this->vars['feature_unit_list']['55']['list']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['feature_unit_list']['55']['list']);
							   		 foreach($this->vars['feature_unit_list']['55']['list'] as $this->vars['foreach']['feature_unit_list_55_list']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['feature_unit_list_55_list']['key']; ?>
                                  <option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>" <?php if (isset($this->vars['item']['id']) && $this->vars['item']['id'] == 57): ?>selected="selected"<?php endif; ?>><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                                  <?php endforeach; ?><?php endif; ?>
                              	</select>                              	
                            </div>
                        </div>
                      
					</div>
                  
                  	<div class="control-group ym_features" id="YMFeatureContainer">
						<label class="control-label ymLabelTitle">Основные характеристики</label>
                      	<div class="controls">
                          <table  id="YMFeatureTable"></table>
                      	</div>
                      
                      <table style="display:none">
						<tr class="ym_feature_list_tpl">
                          	<td class="add-on name"></td>
                          	<td><select skip_get_form="true" name="variant" class="add-on variant-select"></select></td>
         				</tr>
                      	<tr class="ym_feature_radio_tpl">
                          	<td class="add-on name"></td>
                          	<td class="radio">
                          		<label class="radio"><input skip_get_form="true" name="boolean" class="nouniform" type="radio" value="1" />Да</label>
                              	<label class="radio"><input skip_get_form="true" name="boolean" class="nouniform" type="radio" value="0" />Нет</label>
                          	</td>
         				</tr>
                      
                      	<tr class="controls ym_feature_text_tpl">
                          	<td class="add-on name"></td>
                            <td>
                              <div class="input-append">
                                <input skip_get_form="true" type="text" class="string" name="string" placeholder="Значение" style="display:inline-block">
                                <span class="add-on unit" style="display: inline; position: relative; line-height: 19px; padding-top: 6px; padding-bottom: 6px; top: 5px;"></span>
                              </div>
                            </td>
         				</tr>
                      </table>
                  	</div>
                  
                    <div class="control-group">
						<label class="control-label">Другие характеристики</label>
                      
                      	<div class="controls" style="display:none" id="CustomFeatureTpl" box-number="-1">
                        	<div class="input-prepend input-append feature-num-with-unit">
                                <select is_feature_input="true" skip_get_form="true" name="type" class="add-on feture-type-select" placeholder="Тип характеристики">   
                                  <option selected value="1">Текст (строка)</option>
                                  <option value="2">Опция (да/нет)</option>
                                  <option value="3">Значение (число)</option>
                                  <option value="4" disabled>Список опций</option>
                              	</select> 
                                <input is_feature_input="true" skip_get_form="true" type="text" name="name" class="name-element" placeholder="Название">
                              	<input is_feature_input="true" skip_get_form="true" type="text" name="value" class="felement string" ftype="1" placeholder="Значение" style="display:inline-block">
                              	<span class="felement radio" ftype="2">
                                	<label class="radio"><input skip_get_form="true" is_feature_input="true" ftype="2" class="nouniform" type="radio" name="value" value="1" />Да</label>
                                  	<label class="radio"><input skip_get_form="true" is_feature_input="true" ftype="2" class="nouniform" type="radio" name="value" value="0" />Нет</label>
                              	</span>                             
                                <input is_feature_input="true" skip_get_form="true" type="text" class="felement numeric" name="value" ftype="3" placeholder="Числовое значение">
                              	<select is_feature_input="true" skip_get_form="true" class="add-on numeric-select felement" ftype="3" name="unit">
                                  <?php if(count($this->vars['feature_unit_list'])) : 
							   		 $this->vars['foreach']['feature_unit_list']['count'] = count($this->vars['feature_unit_list']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['feature_unit_list']);
							   		 foreach($this->vars['feature_unit_list'] as $this->vars['foreach']['feature_unit_list']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['feature_unit_list']['key']; ?>
                                  	<optgroup label="<?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?>">
                                      	<?php if(count($this->vars['item']['list'])) : 
							   		 $this->vars['foreach']['item.list']['count'] = count($this->vars['item']['list']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['item']['list']);
							   		 foreach($this->vars['item']['list'] as $this->vars['foreach']['item_list']['key'] => $this->vars['opt']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['item_list']['key']; ?>
                                      		<option value="<?php if(isset($this->vars['opt']['id'])) echo $this->vars['opt']['id']; ?>"><?php if(isset($this->vars['opt']['name'])) echo $this->vars['opt']['name']; ?></option>
                                      	<?php endforeach; ?><?php endif; ?>
                                  	</optgroup>
                                  <?php endforeach; ?><?php endif; ?>
                              	</select>  
                                <div ftype="4" class="felement"><input skip_get_form="true" is_feature_input="true" type="text" name="value" ftype="4"  value="" /></div>
                                <a class="btn" href="javascript:void(0)" onclick="$(this).parent().parent().remove()" style="margin-left:20px"><span class=" icon-trash"></span></a>
                            </div>
                      	</div>
                      
                      	<div class="controls otherFeatureButton"> 
                            <button class="btn" type="button" style="margin-top: 2px;" onclick="shopMain.addFeatureBox(this)"><i class=" icon-plus"></i> Добавить характеристику</button>
                        </div>
                  	</div>
				</form>
			</div>

            <div class="settings-form tab-body" section="modification">
              <form class="form-horizontal ">

                <h4>Доступные модификации товара:</h4>
                <a href="javascript:void(0)" action="add-mod" class="btn btn-success"><i class="icon-plus"></i> Добавить</a>
				<br><br>
                <table class="table table-bordered" name="modifications">
                  <thead>
                    <tr>
                      <th>&#8593; &#8595;</th>
                      <th>Артикул</th>
                      <th>Название</th>
                      <th>Цена 1</th>
                      <th>Цена 2</th>
                      <th>Цена 3</th>
                      <th>Цена 4</th>
                      <th>Цена 5</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr name="tpl" style="display:none">
                      <td><i class="icon-screenshot moveModificationIcon"></i></td>
                      <td><input name="m.article" type="text" style="width:60px;" skip-data="1"></td>
                      <td><input name="m.title" type="text" style="width:190px;"  skip-data="1"></td>
                      <td><input name="m.price1" type="text" style="width:60px;"  skip-data="1"></td>
                      <td><input name="m.price2" type="text" style="width:60px;"  skip-data="1"></td>
                      <td><input name="m.price3" type="text" style="width:60px;"  skip-data="1"></td>
                      <td><input name="m.price4" type="text" style="width:60px;"  skip-data="1"></td>
                      <td><input name="m.price5" type="text" style="width:60px;"  skip-data="1"></td>
                      <td>
                        <div class="btn-group">
                          <a href="javascript:void(0)" mod-action="edit" class="btn btn-small btn-info tooltips" data-original-title="Редактировать"><i class="icon-pencil"></i></a>
                          <!--<a href="javascript:void(0)" mod-action="copy" class="btn btn-small btn-info tooltips" data-original-title="Копировать"><i class="icon-copy"></i></a>-->
                          <a href="javascript:void(0)" mod-action="del" class="btn btn-small btn-danger tooltips" data-original-title="Удалить"><i class="icon-trash"></i></a>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
                
                <a class="btn btn-small btn-warning" onclick="shopMain.loadSyncData($(this))">Загрузить данные из 1С</a>
              </form>
            </div>
          
          	<div class="settings-form tab-body" section="cross_selling">
				<form class="form-horizontal ">
                     <div class="control-group">
                         <label class="control-label">Показывать сопутствующие товары</label>
                         <div class="controls">
                           <div class="basic-toggle-button"><input type="checkbox" class="toggle" /></div>
                         </div>
                     </div>
                 </form>
             </div>
                  
             <div class="settings-form tab-body" section="seo">
				<form class="form-horizontal ">
                    <div class="control-group">
                        <label class="control-label">Meta description</label>
                        <div class="controls">
                          <input type="text" style="width:500px" name="p.seo_description">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Meta keywords</label>
                        <div class="controls">
                          <input type="text" style="width:500px" name="p.seo_keywords">
                        </div>
                    </div>
              	</form>
              </div> 
          
		</div>

		<div class="form-actions">
			<button type="button" action="save" class="btn btn-success">Сохранить</button>
            <button type="button" action="saveback" class="btn btn-primary">Сохранить и выйти</button>
			<button type="button" action="back" class="btn">Назад</button> 
		</div>
	</div>
</div>


