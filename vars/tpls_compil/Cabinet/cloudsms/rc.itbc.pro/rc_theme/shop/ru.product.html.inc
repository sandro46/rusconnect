<? $this->md5_compil='e63eae583d4df558abcfb2475aba577b'; ?>
<div class="mainContentBlock" >
    <?php echo $this->get("breadcrumbs.html", "shop"); ?>
  <div class="flex flex__product">
    <div class="leftPanel" >
      <div class="leftLogo"><a href="/"><img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/logo.png" alt=""/></a></div>
      <div class="catListsContainer fluid2">
        
        <div class="catHeaderItemBlock">
          <a href="<?php if(isset($this->vars['parent_category_url'])) echo $this->vars['parent_category_url']; ?>" class="catHeaderBack"></a> 
          <div class="catHeaderItem">          
  			
            <div class="catIcon">
              <?php if (isset($this->vars['category_info']['ext_char1']) && $this->vars['category_info']['ext_char1']  ): ?>
              <img src="<?php if(isset($this->vars['category_info']['ext_char1'])) echo $this->vars['category_info']['ext_char1']; ?>" alt="<?php if(isset($this->vars['category_info']['name'])) echo $this->vars['category_info']['name']; ?>" />
              <?php else: ?>
              <img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>samples/krepezh.png" alt="<?php if(isset($this->vars['category_info']['name'])) echo $this->vars['category_info']['name']; ?>" />
              <?php endif; ?>
            </div>
            <h3><a href="<?php if(isset($this->vars['category_info']['url'])) echo $this->vars['category_info']['url']; ?>"><?php if(isset($this->vars['category_info']['name'])) echo $this->vars['category_info']['name']; ?></a></h3>
          </div>
        </div>
        
        <div class="catListsHolder">
          <?php if($core->lib->widget("ProductList")): ?>
<?php $core->widgets->ProductList()->setting("type_name", "products"); ?>
<?php $core->widgets->ProductList()->setting("param_id", "".$this->vars['product']['group_id'].""); ?>
<?php $core->widgets->ProductList()->setting("limit", "9999"); ?>

<?php $core->widgets->ProductList()->appendSettings(); $core->widgets->ProductList()->main(); endif; ?>
          <ul class="categoryProductsList scroll-pane">
            <?php if(count($this->vars['ProductListOut'])) : 
							   		 $this->vars['foreach']['ProductListOut']['count'] = count($this->vars['ProductListOut']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['ProductListOut']);
							   		 foreach($this->vars['ProductListOut'] as $this->vars['foreach']['ProductListOut']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['ProductListOut']['key']; ?>
            <li <?php if (isset($this->vars['product']['id']) && $this->vars['product']['id'] == $this->vars['item']['id']): ?>class="active"<?php endif; ?>><a href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></a></li>
            <?php endforeach; ?><?php endif; ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="rightPanel" >
      <div class="productContainer" id="productContainer">
        <div class="productContent" >
          <div class="container">
            <div class="productMainInfo" itemscope itemtype="http://schema.org/Product">
              <a href="<?php if(isset($this->vars['product']['url'])) echo $this->vars['product']['url']; ?>" itemprop="url"><h1 itemprop="name"><?php if(isset($this->vars['product']['title'])) echo $this->vars['product']['title']; ?></h1></a>
              
              <?php if (isset($this->vars['product']['features']) && $this->vars['product']['features']  ): ?>
              	<div class="features">
                    <h3>Характеристики:</h3>  
              	<?php if(count($this->vars['product']['features'])) : 
							   		 $this->vars['foreach']['product.features']['count'] = count($this->vars['product']['features']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['product']['features']);
							   		 foreach($this->vars['product']['features'] as $this->vars['foreach']['product_features']['key'] => $this->vars['feature']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['product_features']['key']; ?>
              		<p><strong><?php if(isset($this->vars['feature']['name'])) echo $this->vars['feature']['name']; ?>:</strong> <?php if (isset($this->vars['feature']['type']) && $this->vars['feature']['type'] == 2): ?><?php if (isset($this->vars['feature']['value']) && $this->vars['feature']['value'] == 1): ?>Да<?php else: ?>Нет<?php endif; ?><?php else: ?><?php if (isset($this->vars['feature']['variant']) && $this->vars['feature']['variant']  ): ?><?php if(isset($this->vars['feature']['variant'])) echo $this->vars['feature']['variant']; ?><?php else: ?><?php if(isset($this->vars['feature']['value'])) echo $this->vars['feature']['value']; ?><?php endif; ?><?php if (isset($this->vars['feature']['unit']) && $this->vars['feature']['unit']  ): ?><?php if(isset($this->vars['feature']['unit'])) echo $this->vars['feature']['unit']; ?><?php endif; ?><?php endif; ?></p>
              	<?php endforeach; ?><?php endif; ?>
              	</div>
              <?php endif; ?>
              
              <div class="description">
              	<p><?php if(isset($this->vars['product']['description'])) echo $this->vars['product']['description']; ?></p>
              </div>
              <br><br>
              <?php if (isset($this->vars['vendorInfo']) && $this->vars['vendorInfo']  ): ?>
              	<a href="/ru/shop/vendor/id/<?php if(isset($this->vars['vendorInfo']['id'])) echo $this->vars['vendorInfo']['id']; ?>/"><img src="<?php if(isset($this->vars['vendorInfo']['image_original'])) echo $this->vars['vendorInfo']['image_original']; ?>" alt="<?php if(isset($this->vars['vendorInfo']['name'])) echo $this->vars['vendorInfo']['name']; ?> - <?php if(isset($this->vars['vendorInfo']['sub_description'])) echo $this->vars['vendorInfo']['sub_description']; ?>" /></a>
              <?php endif; ?>
              
              <!---->
            </div>
            <div class="productImages">
              <div class="productLargeImage" style="background-image:url('<?php if(isset($this->vars['product']['images']['0']['2']['name'])) echo $this->vars['product']['images']['0']['2']['name']; ?>')">
                <div class="container">
                  <?php if (isset($this->vars['product']['images']['0']['2']['name']) && $this->vars['product']['images']['0']['2']['name']  ): ?>
                  <a href="<?php if(isset($this->vars['product']['images']['0']['3']['name'])) echo $this->vars['product']['images']['0']['3']['name']; ?>" class="fancybox"><img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/clear.gif" style="width:274px; height:274px;"></a>
                  <?php endif; ?>
                </div>
                <p class="imageText"><span id="prodTitle"></span><span></span>  <i id="prodChars"></i></p>
              </div>
              <div class="productGallery">
                <?php if(count($this->vars['product']['images'])) : 
							   		 $this->vars['foreach']['product.images']['count'] = count($this->vars['product']['images']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['product']['images']);
							   		 foreach($this->vars['product']['images'] as $this->vars['foreach']['product_images']['key'] => $this->vars['img']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['product_images']['key']; ?><?php endforeach; ?><?php endif; ?>
                <?php if (isset($this->vars['foreach']['product_images']['key']) && $this->vars['foreach']['product_images']['key'] > 4): ?><a href="#" name="hello" class="prev">&nbsp;</a><?php endif; ?>
                <?php if (isset($this->vars['foreach']['product_images']['key']) && $this->vars['foreach']['product_images']['key'] > 4): ?><a href="#" name="hello" class="next">&nbsp;</a><?php endif; ?>
                <div class="jcarousel">
                  <ul class="galleryList">
                    <?php $this->vars['first'] = 1; ?>
                    <?php if(count($this->vars['product']['images'])) : 
							   		 $this->vars['foreach']['product.images']['count'] = count($this->vars['product']['images']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['product']['images']);
							   		 foreach($this->vars['product']['images'] as $this->vars['foreach']['product_images']['key'] => $this->vars['img']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['product_images']['key']; ?>
                    
                    <?php if (isset($this->vars['img']['2']['name']) && $this->vars['img']['2']['name']  ): ?>
                    <li>
                      <a href="<?php if(isset($this->vars['img']['2']['name'])) echo $this->vars['img']['2']['name']; ?>" data-big="<?php if(isset($this->vars['img']['3']['name'])) echo $this->vars['img']['3']['name']; ?>" data-title="" class="<?php if (isset($this->vars['first']) && $this->vars['first']  ): ?>active<?php endif; ?>">
                        <img alt="" src="<?php if(isset($this->vars['img']['2']['name'])) echo $this->vars['img']['2']['name']; ?>" />
                      </a>
                    </li>
                    <?php $this->vars['first'] = 0; ?>
                    <?php endif; ?>
                    <?php endforeach; ?><?php endif; ?>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="productSpecification">
            <div class="tabsControl">
              <?php if (isset($this->vars['product']['modifications']) && $this->vars['product']['modifications']  ): ?><a href="#sizes" class="tab active">Товар</a><?php endif; ?>
              <a href="#description" class="tab <?php if (isset($this->vars['product']['modifications']) && $this->vars['product']['modifications']  ): ?><?php else: ?>active<?php endif; ?>">Описание</a> 
              <?php if (isset($this->vars['product']['full_description2']) && $this->vars['product']['full_description2']  ): ?><a href="#video" class="tab">Видео</a><?php endif; ?>
              <?php if (isset($this->vars['product']['full_description3']) && $this->vars['product']['full_description3']  ): ?><a href="#schemes" class="tab">Схемы</a><?php endif; ?>
            </div>
            <div class="tabsList">
              <?php if (isset($this->vars['product']['modifications']) && $this->vars['product']['modifications']  ): ?>
              <div id="sizes" class="tabSizes activeTab">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Обозначение</th>
                      <th>Артикул</th>
                      <th>Размер</th>
                      <th>Упаковка</th>
                      <th>Кол-во</th>
                      <th></th>
                    </tr> 
                  </thead>
                    <tbody>
                    <?php if(count($this->vars['product']['modifications'])) : 
							   		 $this->vars['foreach']['product.modifications']['count'] = count($this->vars['product']['modifications']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['product']['modifications']);
							   		 foreach($this->vars['product']['modifications'] as $this->vars['foreach']['product_modifications']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['product_modifications']['key']; ?>
                    <tr data-product_id="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>">
                      <td name="title" class="title"><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></td>
                      <td><?php if(isset($this->vars['item']['article'])) echo $this->vars['item']['article']; ?></td>
                      <td><?php if(isset($this->vars['item']['description'])) echo $this->vars['item']['description']; ?></td>
                      <td><?php if(isset($this->vars['item']['pack_size'])) echo $this->vars['item']['pack_size']; ?> <?php if(isset($this->vars['item']['pack_unit_name'])) echo $this->vars['item']['pack_unit_name']; ?></td>
                      <td><input type="text" name="amount" class="buy_small_input" value="1"></td>
                      <td class="smalltd"><a class="button orange small" name="addToCartSmallButtonMod" href="javascript:void(0)">В корзину</a></td>
                    </tr> 
                    <?php endforeach; ?><?php endif; ?>
                  </tbody>
                </table>
              </div>
              <?php endif; ?>
              <div id="description" class="tabDescription"  itemprop="description">
                <?php if(isset($this->vars['product']['full_description'])) echo $this->vars['product']['full_description']; ?>
              </div>
              <?php if (isset($this->vars['product']['full_description3']) && $this->vars['product']['full_description3']  ): ?>
              <div id="schemes" class="tabSchemes">
                <?php if(isset($this->vars['product']['full_description3'])) echo $this->vars['product']['full_description3']; ?>
              </div>
              <?php endif; ?>
              <?php if (isset($this->vars['product']['full_description2']) && $this->vars['product']['full_description2']  ): ?>
              <div id="video" class="tabVideo">
                <?php if(isset($this->vars['product']['full_description2'])) echo $this->vars['product']['full_description2']; ?>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="productOptions" id="productBuyForm">
          <div class="sellingBlock">
            <?php if (isset($this->vars['product']['modifications']) && $this->vars['product']['modifications']  ): ?>
            <h3><?php if(isset($this->vars['product']['title'])) echo $this->vars['product']['title']; ?></h3>   
            <div class="select_outer model">
              <p></p>
              <select size="1" id="modificationSelect">
                <?php if(count($this->vars['product']['modifications'])) : 
							   		 $this->vars['foreach']['product.modifications']['count'] = count($this->vars['product']['modifications']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['product']['modifications']);
							   		 foreach($this->vars['product']['modifications'] as $this->vars['foreach']['product_modifications']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['product_modifications']['key']; ?>
                <option value="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>"><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?> (<?php if(isset($this->vars['item']['description'])) echo $this->vars['item']['description']; ?>)</option>
                <?php endforeach; ?><?php endif; ?>
              </select>
            </div> 
            <?php endif; ?>
                   
            
            <div class="modelInfo bottomLine">
              <span class="modelSize"><?php if(isset($this->vars['product']['modifications']['0']['description'])) echo $this->vars['product']['modifications']['0']['description']; ?></span>
         
              <ul>
                <li>Артикул: <i name="article"><?php if(isset($this->vars['product']['modifications']['0']['article'])) echo $this->vars['product']['modifications']['0']['article']; ?></i></li>
                <li>Упаковка: <i name="package"><?php if(isset($this->vars['product']['modifications']['0']['pack_size'])) echo $this->vars['product']['modifications']['0']['pack_size']; ?> <?php if(isset($this->vars['product']['modifications']['0']['pack_unit_name'])) echo $this->vars['product']['modifications']['0']['pack_unit_name']; ?></i></li>
                <li>Наличие: 
                  <?php if (isset($this->vars['product']['avaliable_type']) && $this->vars['product']['avaliable_type'] == 1): ?>
                  <i name="exists">на складе</i>
                  <?php endif; ?>
                  <?php if (isset($this->vars['product']['avaliable_type']) && $this->vars['product']['avaliable_type'] == 2): ?>
                  <i name="exists">Под заказ</i>
                  <?php endif; ?>
                  
                  <?php if (isset($this->vars['product']['avaliable_type']) && $this->vars['product']['avaliable_type'] == 3): ?>
                  <i name="exists">Нет в наличии</i>
                  <?php endif; ?>
                  
                </li>
              </ul>
              <!--<a href="javascript:void(0)" class="questIcon" title="Текст 1"></a>-->
            </div>                       
            <div class="modelPrice bottomLine">
              <?php $this->vars['useProductPrice'] = 1; ?>
              <?php if (isset($this->vars['product']['modifications']) && $this->vars['product']['modifications']  ): ?><?php $this->vars['productItem'] = $this->vars['product']['modifications']['0']; ?><?php else: ?><?php $this->vars['productItem'] = $this->vars['product']; ?><?php endif; ?>            
              <span class="price price<?php if(isset($this->vars['useProductPrice'])) echo $this->vars['useProductPrice']; ?>" price-type="<?php if(isset($this->vars['useProductPrice'])) echo $this->vars['useProductPrice']; ?>"><span><?php if(isset($this->vars['productItem']['price_sf'])) echo parce_digits($this->vars['productItem']['price_sf']); ?></span> руб./<i><?php if(isset($this->vars['productItem']['pack_unit_name'])) echo $this->vars['productItem']['pack_unit_name']; ?></i></span>
              <ul name="priceList">
                <li class="discount1" data-type="1" <?php if (isset($this->vars['useProductPrice']) && $this->vars['useProductPrice'] == 1): ?>style="display:none"<?php endif; ?> data-price-pack="<?php if(isset($this->vars['productItem']['price'])) echo $this->vars['productItem']['price']; ?>"  data-price-unit="<?php if(isset($this->vars['productItem']['pack_unit_name'])) echo $this->vars['productItem']['pack_unit_name']; ?>" data-price-pack-size="<?php if(isset($this->vars['productItem']['pack_size'])) echo $this->vars['productItem']['pack_size']; ?>" data-price-float-per-unit="<?php if(isset($this->vars['productItem']['price_sf'])) echo parce_digits($this->vars['productItem']['price_sf']); ?>"><sup>1</sup><span><?php if(isset($this->vars['productItem']['price_s'])) echo $this->vars['productItem']['price_s']; ?></span></li>
                <li class="discount2" data-type="2" <?php if (isset($this->vars['useProductPrice']) && $this->vars['useProductPrice'] == 2): ?>style="display:none"<?php endif; ?> data-price-pack="<?php if(isset($this->vars['productItem']['price2'])) echo $this->vars['productItem']['price2']; ?>" data-price-unit="<?php if(isset($this->vars['productItem']['pack_unit_name'])) echo $this->vars['productItem']['pack_unit_name']; ?>" data-price-pack-size="<?php if(isset($this->vars['productItem']['pack_size'])) echo $this->vars['productItem']['pack_size']; ?>" data-price-float-per-unit="<?php if(isset($this->vars['productItem']['price2_sf'])) echo parce_digits($this->vars['productItem']['price2_sf']); ?>"><sup>2</sup><span><?php if(isset($this->vars['productItem']['price2_s'])) echo $this->vars['productItem']['price2_s']; ?></span></li>
                <li class="discount3" data-type="3" <?php if (isset($this->vars['useProductPrice']) && $this->vars['useProductPrice'] == 3): ?>style="display:none"<?php endif; ?> data-price-pack="<?php if(isset($this->vars['productItem']['price3'])) echo $this->vars['productItem']['price3']; ?>" data-price-unit="<?php if(isset($this->vars['productItem']['pack_unit_name'])) echo $this->vars['productItem']['pack_unit_name']; ?>" data-price-pack-size="<?php if(isset($this->vars['productItem']['pack_size'])) echo $this->vars['productItem']['pack_size']; ?>" data-price-float-per-unit="<?php if(isset($this->vars['productItem']['price3_sf'])) echo parce_digits($this->vars['productItem']['price3_sf']); ?>"><sup>3</sup><span><?php if(isset($this->vars['productItem']['price3_s'])) echo $this->vars['productItem']['price3_s']; ?></span></li>
                <li class="discount4" data-type="4" <?php if (isset($this->vars['useProductPrice']) && $this->vars['useProductPrice'] == 4): ?>style="display:none"<?php endif; ?> data-price-pack="<?php if(isset($this->vars['productItem']['price4'])) echo $this->vars['productItem']['price4']; ?>" data-price-unit="<?php if(isset($this->vars['productItem']['pack_unit_name'])) echo $this->vars['productItem']['pack_unit_name']; ?>" data-price-pack-size="<?php if(isset($this->vars['productItem']['pack_size'])) echo $this->vars['productItem']['pack_size']; ?>" data-price-float-per-unit="<?php if(isset($this->vars['productItem']['price4_sf'])) echo parce_digits($this->vars['productItem']['price4_sf']); ?>"><sup>4</sup><span><?php if(isset($this->vars['productItem']['price4_s'])) echo $this->vars['productItem']['price4_s']; ?></span></li>
              </ul>
              <a href="javascript:void(0)" class="link">Условия дисконтной программы</a>
            </div>
            <div class="addToCart">
              <form>
                <div class="row">
                  <div style="float: left; width: 140px;">
                    <p style="font-weight: bold; font-size: 14px; padding-bottom: 4px;">Количество <?php if (isset($this->vars['product']['modifications']['0']['min_order']) && $this->vars['product']['modifications']['0']['min_order'] == 1): ?>(уп.)<?php else: ?>(шт.)<?php endif; ?></p>
                    <span><?php if (isset($this->vars['product']['modifications']['0']['min_order']) && $this->vars['product']['modifications']['0']['min_order'] == 1): ?>Товар поставляется упаковками<?php else: ?>Товар поставляется штучно<?php endif; ?></span>
                  </div>
                  <!--
                  <div class="select_outer model" style="float: left; margin-right: 0px; width: 87px; background-position: 57px center;">
                    <p></p>
                    <select size="1" name="amount" style="width:85px;">
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                      <option value="6">6</option>
                      <option value="7">7</option>
                      <option value="8">8</option>
                      <option value="9">9</option>
                      <option value="10">10</option>
                      <option value="11">11</option>
                      <option value="12">12</option>
                      <option value="13">13</option>
                      <option value="14">14</option>
                      <option value="15">15</option>
                      <option value="16">16</option>
                      <option value="17">17</option>
                      <option value="18">18</option>
                      <option value="19">19</option>
                      <option value="20">20</option>
                    </select>
                  </div>
                  -->
                  <div class="productQtAdd">
                    <div class="count quantity">
                      <span class="down">-</span>
                      <input type="text" prod-id="1454" name="amount" value="1" maxlength="2">
                      <span class="up">+</span>
                    </div>
                  </div>
                  <br style="clear:both">
                  
                  
                  <!--<label for="amount">Количество <span><?php if (isset($this->vars['product']['modifications']['0']['min_order']) && $this->vars['product']['modifications']['0']['min_order'] == 1): ?>(уп.)<?php else: ?>(шт.)<?php endif; ?></span></label>-->
                  <!--<a href="javascript:void(0)" class="questIcon"  title="Минимальный заказ"></a>-->
                </div>
                <input type="hidden" name="product_id" value="<?php if(isset($this->vars['product']['modifications']['0']['id'])) echo $this->vars['product']['modifications']['0']['id']; ?>">
                <?php if (isset($this->vars['product']['avaliable_type']) && $this->vars['product']['avaliable_type'] == 1): ?><a href="javascript:void(0)" name="addToCartButtonMod" class="button orange large">В корзину</a><?php endif; ?>
              </form>
            </div>
          </div>
          
          <div class="forBusiness">
            <h3>Идеи для бизнеса</h3>
            <p>
              Экспозиция для вашего магазина, адаптированная к ассортименту вашего магазина! Более 2000предлагаемых товаров. Взаимовыгодное
              сотрудничество!

            </p>
            <a href="/idei_dlya_biznesa.html" class="button grey">перейти</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="featuredGoods">
  <div class="featureCatsNavigation">
    <a href="#similars" class="similars tab active">Похожие товары</a>
    <a href="#recently" class="recently tab ">Недавно просматривали</a>
    <a href="#featured" class="featured tab">С этим товаром покупают</a>
  </div>
  <div class="featuredLists">
    <div class="goodsLineContainer activeTab" id="similars">
      <div class="mainCatsGoods">
        <div class="controls">
          <a href="#" class="prev">&nbsp;</a>
          <a href="#" class="next">&nbsp;</a>
        </div>
        <div class="container jcarousel itemsSlider">
          <ul class="goodsList">
            <?php if (isset($this->vars['similarProducts']) && $this->vars['similarProducts']  ): ?>
            <?php if(count($this->vars['similarProducts'])) : 
							   		 $this->vars['foreach']['similarProducts']['count'] = count($this->vars['similarProducts']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['similarProducts']);
							   		 foreach($this->vars['similarProducts'] as $this->vars['foreach']['similarProducts']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['similarProducts']['key']; ?>
            <li>
              <a href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>">
                <!--<span class="marker">-20%</span>-->
                <div class="itemimage">
                  <img alt="" src="<?php if(isset($this->vars['item']['img'])) echo $this->vars['item']['img']; ?>" />
                </div>
                <span class="title"><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></span>
                <span class="price">
                  <?php if (isset($this->vars['item']['mod']) && $this->vars['item']['mod']  ): ?>
                  	<?php if(isset($this->vars['item']['mod']['price'])) echo $this->vars['item']['mod']['price']; ?><i>/ за <?php if (isset($this->vars['product']['modifications']['0']['price_type']) && $this->vars['product']['modifications']['0']['price_type'] == 1): ?>шт.<?php else: ?>уп.<?php endif; ?></i>
                  <?php else: ?>
                  	<?php if(isset($this->vars['item']['price'])) echo $this->vars['item']['price']; ?><i>/ за <?php if(isset($this->vars['item']['pack_unit_name'])) echo $this->vars['item']['pack_unit_name']; ?></i>
                  <?php endif; ?>
                </span>
              </a>
            </li>                    
            <?php endforeach; ?><?php endif; ?>                   
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="goodsLineContainer" id="recently">
      <div class="mainCatsGoods">
        <div class="controls">
          <a href="#" class="prev">&nbsp;</a>
          <a href="#" class="next">&nbsp;</a>
        </div>
        <div class="container jcarousel itemsSlider">
          <ul class="goodsList">                      
            <?php if (isset($this->vars['lastViewed']) && $this->vars['lastViewed']  ): ?>
            <?php if(count($this->vars['lastViewed'])) : 
							   		 $this->vars['foreach']['lastViewed']['count'] = count($this->vars['lastViewed']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['lastViewed']);
							   		 foreach($this->vars['lastViewed'] as $this->vars['foreach']['lastViewed']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['lastViewed']['key']; ?>
            <li>
              <a href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>">
                <!--<span class="marker">-20%</span>-->
                <div class="itemimage">
                  <img alt="" src="<?php if(isset($this->vars['item']['img'])) echo $this->vars['item']['img']; ?>" />
                </div>
                <span class="title"><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></span>
                <span class="price">
                  <?php if (isset($this->vars['item']['mod']) && $this->vars['item']['mod']  ): ?>
                  	<?php if(isset($this->vars['item']['mod']['price'])) echo $this->vars['item']['mod']['price']; ?><i>/ за <?php if (isset($this->vars['product']['modifications']['0']['price_type']) && $this->vars['product']['modifications']['0']['price_type'] == 1): ?>шт.<?php else: ?>уп.<?php endif; ?></i>
                  <?php else: ?>
                  	<?php if(isset($this->vars['item']['price'])) echo $this->vars['item']['price']; ?><i>/ за <?php if(isset($this->vars['item']['pack_unit'])) echo $this->vars['item']['pack_unit']; ?></i>
                  <?php endif; ?>
                </span>
              </a>
            </li>                    
            <?php endforeach; ?><?php endif; ?>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="goodsLineContainer" id="featured">
      <div class="mainCatsGoods">
        <div class="controls">
          <a href="#" class="prev">&nbsp;</a>
          <a href="#" class="next">&nbsp;</a>
        </div>
        <div class="container jcarousel itemsSlider">
          <ul class="goodsList">
            <?php if (isset($this->vars['crossSelling']) && $this->vars['crossSelling']  ): ?>
            <?php if(count($this->vars['crossSelling'])) : 
							   		 $this->vars['foreach']['crossSelling']['count'] = count($this->vars['crossSelling']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['crossSelling']);
							   		 foreach($this->vars['crossSelling'] as $this->vars['foreach']['crossSelling']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['crossSelling']['key']; ?>
            <li>
              <a href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>">
                <!--<span class="marker">-20%</span>-->
                <div class="itemimage">
                  <img alt="" src="<?php if(isset($this->vars['item']['img'])) echo $this->vars['item']['img']; ?>" />
                </div>
                <span class="title"><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></span>
                <span class="price">
                  <?php if (isset($this->vars['item']['mod']) && $this->vars['item']['mod']  ): ?>
                  	<?php if(isset($this->vars['item']['mod']['price'])) echo $this->vars['item']['mod']['price']; ?><i>/ за <?php if(isset($this->vars['item']['mod']['pack_unit'])) echo $this->vars['item']['mod']['pack_unit']; ?></i>
                  <?php else: ?>
                  	<?php if(isset($this->vars['item']['price'])) echo $this->vars['item']['price']; ?><i>/ за <?php if(isset($this->vars['item']['pack_unit'])) echo $this->vars['item']['pack_unit']; ?></i>
                  <?php endif; ?>
                </span>
              </a>
            </li>                    
            <?php endforeach; ?><?php endif; ?>
            <?php endif; ?>                  
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
