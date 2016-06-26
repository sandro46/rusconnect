<? $this->md5_compil='b8ec830d97774eb5466c3cce566892b9'; ?>
            <div class="row-fluid">
               <div class="span12">
                  <div class="widget">
                        <div class="widget-title">
                          <h4><i class="icon-edit"></i> <?php if(isset($this->vars['productInfo']['title'])) echo $this->vars['productInfo']['title']; ?></h4>                   
                        </div>
                        <div class="widget-body">
						
                            
                          <div class="imageTumbContainer" style="float:left; width:150px; height:150px; background-image:url('<?php if (isset($this->vars['productInfo']['images']['0']) && $this->vars['productInfo']['images']['0']  ): ?><?php if(isset($this->vars['productInfo']['images']['0']['2']['name'])) echo $this->vars['productInfo']['images']['0']['2']['name']; ?><?php else: ?><?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>img/no_image.png<?php endif; ?>')"></div>
                          
                          <div style="float:left; width:400px; margin-left:20px; "><?php if(isset($this->vars['productInfo']['description'])) echo $this->vars['productInfo']['description']; ?></div>
                          
                          
                          <br style="clear:both">	
                          <ul style="margin-top:25px;">
                            <?php if (isset($this->vars['productInfo']['avaliable']['type']) && $this->vars['productInfo']['avaliable']['type'] == 1): ?>
                           	 	<li><strong>Наличие:</strong> <span style="color:green"> <?php if(isset($this->vars['productInfo']['avaliable']['store']['quantity'])) echo $this->vars['productInfo']['avaliable']['store']['quantity']; ?></span></li>
                            	<li ><strong>Склад:</strong> <?php if(isset($this->vars['productInfo']['stores']['0']['name'])) echo $this->vars['productInfo']['stores']['0']['name']; ?></li>
                            <?php endif; ?>
                            
                            <?php if (isset($this->vars['productInfo']['avaliable']['type']) && $this->vars['productInfo']['avaliable']['type'] == 2): ?>
                            	<li><strong>Наличие:</strong> <span style="color:yelow"> Ожидание поставки: через <?php if(isset($this->vars['productInfo']['avaliable']['pending'])) echo $this->vars['productInfo']['avaliable']['pending']; ?> дней</span></li>
                            <?php endif; ?>
                            
                            <?php if (isset($this->vars['productInfo']['avaliable']['type']) && $this->vars['productInfo']['avaliable']['type'] == 3): ?>
                            	<li><strong>Наличие:</strong> <span style="color:red"> Нет в наличии</span></li>
                            <?php endif; ?>
                            
                            <li><strong>Рармер (ШxВxГ):</strong> <?php if(isset($this->vars['productInfo']['dimensions']['width'])) echo $this->vars['productInfo']['dimensions']['width']; ?>(<?php if(isset($this->vars['productInfo']['dimensions']['width_unit_name'])) echo $this->vars['productInfo']['dimensions']['width_unit_name']; ?>) x <?php if(isset($this->vars['productInfo']['dimensions']['height'])) echo $this->vars['productInfo']['dimensions']['height']; ?>(<?php if(isset($this->vars['productInfo']['dimensions']['height_unit_name'])) echo $this->vars['productInfo']['dimensions']['height_unit_name']; ?>) x <?php if(isset($this->vars['productInfo']['dimensions']['depth'])) echo $this->vars['productInfo']['dimensions']['depth']; ?>(<?php if(isset($this->vars['productInfo']['dimensions']['depth_unit_name'])) echo $this->vars['productInfo']['dimensions']['depth_unit_name']; ?>) </li>
                            <li><strong>Вес:</strong> <?php if(isset($this->vars['productInfo']['dimensions']['weight'])) echo $this->vars['productInfo']['dimensions']['weight']; ?>(<?php if(isset($this->vars['productInfo']['dimensions']['weight_unit_name'])) echo $this->vars['productInfo']['dimensions']['weight_unit_name']; ?>)</li>
                          </ul>
                    	 <br style="clear:both">
                          
                         <a href="javascript:void(0)" onclick="$page.back()" class="btn btn-success"><i class="icon-arrow-left"></i> Назад</a>
						 <a href="javascript:void(0)" action="removeProduct" class="btn btn-warning"><i class="icon-trash"></i> Убрать товар из заказа</a>
                    	</div>
                  </div>
               </div>
            </div>