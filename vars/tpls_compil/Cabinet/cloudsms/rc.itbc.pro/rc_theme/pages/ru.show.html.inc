<? $this->md5_compil='46b1534f59bbe8d797e838908f7c6ef6'; ?>
<div class="mainContentBlockPages">
  <div class="flex flex__page">
    <div class="leftPanel">
      <div class="leftLogo"><a href="/"><img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>images/logo.png" alt=""/></a></div>
      <div class="linkList">
        
        <?php if($core->lib->widget("ShopPages")): ?>
<?php $core->widgets->ShopPages()->setting("parent_id", "0"); ?>
<?php $core->widgets->ShopPages()->setting("current_id", "".$this->vars['pageInfo']['page_id'].""); ?>

<?php $core->widgets->ShopPages()->appendSettings(); $core->widgets->ShopPages()->main(); endif; ?>
        <ul>
          <?php if(count($this->vars['ShopPagesOut'])) : 
							   		 $this->vars['foreach']['ShopPagesOut']['count'] = count($this->vars['ShopPagesOut']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['ShopPagesOut']);
							   		 foreach($this->vars['ShopPagesOut'] as $this->vars['foreach']['ShopPagesOut']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['ShopPagesOut']['key']; ?>
          <li <?php if (isset($this->vars['item']['page_id']) && $this->vars['item']['page_id'] == $this->vars['pageInfo']['page_id']): ?>class="active"<?php endif; ?>><a href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>"><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></a></li>
          <?php endforeach; ?><?php endif; ?>
        </ul>
      </div> 
    </div>
    
    
    
    <div class="rightPanel">
      <div class="content">
          <div class="header"><?php if (isset($this->vars['pageInfo']['image']) && $this->vars['pageInfo']['image']  ): ?><img class="icon" src="<?php if(isset($this->vars['pageInfo']['image'])) echo $this->vars['pageInfo']['image']; ?>"><?php endif; ?><h1 <?php if (isset($this->vars['pageInfo']['image']) && $this->vars['pageInfo']['image']  ): ?>class="inline"<?php else: ?><?php endif; ?>><?php if(isset($this->vars['pageInfo']['title'])) echo $this->vars['pageInfo']['title']; ?></h1></div>
          <div class="body">
            <?php if(isset($this->vars['pageInfo']['text'])) echo $this->vars['pageInfo']['text']; ?>
          </div>
      </div>
    </div>
  </div>
</div>
