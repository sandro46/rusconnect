<? $this->md5_compil='08959cd0e576b66404557fb4864b408d'; ?>
<div class="catHeaderItem">
  <div class="catIcon">
    <?php if (isset($this->vars['category_info']['ext_char1']) && $this->vars['category_info']['ext_char1']  ): ?>
    <img src="<?php if(isset($this->vars['category_info']['ext_char1'])) echo $this->vars['category_info']['ext_char1']; ?>" alt="<?php if(isset($this->vars['category_info']['name'])) echo $this->vars['category_info']['name']; ?>" />
    <?php else: ?>
    <img src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>samples/krepezh.png" alt="<?php if(isset($this->vars['category_info']['name'])) echo $this->vars['category_info']['name']; ?>" />
    <?php endif; ?>
  </div>
  <h3><?php if(isset($this->vars['category_info']['name'])) echo $this->vars['category_info']['name']; ?></h3>
</div>

<div class="catListsHolder">
  <?php if($core->lib->widget("ProductGroups")): ?>
<?php $core->widgets->ProductGroups()->setting("parent_id", "1374"); ?>
<?php $core->widgets->ProductGroups()->setting("current_id", "".$this->vars['category_id'].""); ?>

<?php $core->widgets->ProductGroups()->appendSettings(); $core->widgets->ProductGroups()->main(); endif; ?>
  <ul class="categoryProductsList scroll-pane">
    <?php if(count($this->vars['ProductGroupsOut'])) : 
							   		 $this->vars['foreach']['ProductGroupsOut']['count'] = count($this->vars['ProductGroupsOut']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['ProductGroupsOut']);
							   		 foreach($this->vars['ProductGroupsOut'] as $this->vars['foreach']['ProductGroupsOut']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['ProductGroupsOut']['key']?>
    <li <?php if (isset($this->vars['category_id']) && $this->vars['category_id'] == $this->vars['item']['id']): ?>class="active"<?php endif; ?>><a href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></a></li>
    <?php endforeach; ?><?php endif; ?>
  </ul>
</div>