<? $this->md5_compil='4aeb78ce547b246b576205440f0ccb7c'; ?>

<div class="row-fluid">
  <div class="span12" id="contactlist">
    <div class="widget">
      <div class="widget-title">
        <h4><i class="icon-reorder"></i>Меню сайта</h4>
      </div>
      <div class="widget-body">
        <div class="row-fluid">         
          <div class="span8">
            <a href="#/add/" class="btn btn-success"><i class="icon-plus"></i> Добавить пункт меню</a>
          </div>
        </div>   
        <br>
        <div class="dd" id="main_menu_nestable">
          <ol class="dd-list">
            <?php if(count($this->vars['menuList'])) : 
							   		 $this->vars['foreach']['menuList']['count'] = count($this->vars['menuList']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['menuList']);
							   		 foreach($this->vars['menuList'] as $this->vars['foreach']['menuList']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['menuList']['key']?>
            	<li class="dd-item " data-id="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>">
              		<div class="dd-handle dd-nodrag <?php if(isset($this->vars['sub']['type_name'])) echo $this->vars['sub']['type_name']; ?>">
                      <?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?>
                      	<?php if (isset($this->vars['item']['locked']) && $this->vars['item']['locked']  ): ?><?php else: ?>
                   			<a href="javascript:void(0)"><i class="icon-edit"></i></a>
                            <a href="javascript:void(0)"><i class="icon-trash"></i></a>
                            <a href="javascript:void(0)"><i class="icon-cog"></i></a>
                      <?php endif; ?>
                                  
                  	</div>
                  	<?php if (isset($this->vars['item']['sub']) && $this->vars['item']['sub']  ): ?>
                  		<ol class="dd-list">
                          <?php if(count($this->vars['item']['sub'])) : 
							   		 $this->vars['foreach']['item.sub']['count'] = count($this->vars['item']['sub']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['item']['sub']);
							   		 foreach($this->vars['item']['sub'] as $this->vars['foreach']['item_sub']['key'] => $this->vars['sub']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['item_sub']['key']?>
                          		<li class="dd-item" data-id="<?php if(isset($this->vars['sub']['id'])) echo $this->vars['sub']['id']; ?>">
                                	  <div class="dd-handle ">
                                        <i class="<?php if(isset($this->vars['sub']['type_icon'])) echo $this->vars['sub']['type_icon']; ?>"></i>
                                        <?php if(isset($this->vars['sub']['title'])) echo $this->vars['sub']['title']; ?>
                                  
                                        <a href="javascript:void(0)" onmousedown=" evt = event || window.event; evt.cancelBubble = true;" onclick="site_control.editMenuItem(<?php if(isset($this->vars['sub']['id'])) echo $this->vars['sub']['id']; ?>); evt = event || window.event; evt.cancelBubble = true;"><i class="icon-edit"></i></a>
                                        <a href="javascript:void(0)" onmousedown=" evt = event || window.event; evt.cancelBubble = true;" onclick="site_control.delItem(<?php if(isset($this->vars['sub']['id'])) echo $this->vars['sub']['id']; ?>, this); evt = event || window.event; evt.cancelBubble = true;"><i class="icon-trash"></i></a>
                                        <a <?php if (isset($this->vars['sub']['hidden']) && $this->vars['sub']['hidden']  ): ?>class="eye_hidden"<?php endif; ?> href="javascript:void(0)" onmousedown=" evt = event || window.event; evt.cancelBubble = true;" onclick="site_control.visibleMenuItem(<?php if(isset($this->vars['sub']['id'])) echo $this->vars['sub']['id']; ?>, this); evt = event || window.event; evt.cancelBubble = true;"><i name="visMenuIcon" class="<?php if (isset($this->vars['sub']['hidden']) && $this->vars['sub']['hidden']  ): ?>icon-eye-close<?php else: ?> icon-eye-open<?php endif; ?>"></i></a>
                                  
                                  		</div>
                          		</li>
                          <?php endforeach; ?><?php endif; ?>
                  		</ol>
                  	<?php endif; ?>
            	</li>
            <?php endforeach; ?><?php endif; ?>
           </ol>
        </div>
        
        
 


       </div>
     </div>
  </div>
</div>


