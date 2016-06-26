<? $this->md5_compil='799f2161a2caf3e4a89f5c2130b737e1'; ?>
<div class="catListsControl">
  <a href="#catSectionsList"  class="tab <?php if (isset($this->vars['category_info']['parrent_id']) && $this->vars['category_info']['parrent_id'] == 1374): ?>active<?php else: ?><?php if (isset($this->vars['category_info']['parrent_id']) && $this->vars['category_info']['parrent_id'] == 1375): ?><?php else: ?>active<?php endif; ?><?php endif; ?>">Разделы</a>
  <a href="#catCategoriesList" class="tab <?php if (isset($this->vars['category_info']['parrent_id']) && $this->vars['category_info']['parrent_id'] == 1375): ?>active<?php endif; ?>">Категории</a>
</div>



<div class="catListsHolder">
  <?php if($core->lib->widget("ProductGroups")): ?>
<?php $core->widgets->ProductGroups()->setting("parent_id", "31"); ?>
<?php $core->widgets->ProductGroups()->setting("current_id", "".$this->vars['category_id'].""); ?>
<?php $core->widgets->ProductGroups()->setting("showSubLevel", "2"); ?>

<?php $core->widgets->ProductGroups()->appendSettings(); $core->widgets->ProductGroups()->main(); endif; ?>
  <ul id="catSectionsList" <?php if (isset($this->vars['category_info']['parrent_id']) && $this->vars['category_info']['parrent_id'] == 1374): ?>class="activeTab"<?php else: ?><?php if (isset($this->vars['category_info']['parrent_id']) && $this->vars['category_info']['parrent_id'] == 1375): ?><?php else: ?>class="activeTab"<?php endif; ?><?php endif; ?>>
    <?php if(count($this->vars['ProductGroupsOut'])) : 
							   		 $this->vars['foreach']['ProductGroupsOut']['count'] = count($this->vars['ProductGroupsOut']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['ProductGroupsOut']);
							   		 foreach($this->vars['ProductGroupsOut'] as $this->vars['foreach']['ProductGroupsOut']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['ProductGroupsOut']['key']; ?>
    <li <?php if (isset($this->vars['category_id']) && $this->vars['category_id'] == $this->vars['item']['id']): ?>class="active"<?php else: ?><?php if (isset($this->vars['item']['childs']) && $this->vars['item']['childs']  ): ?><?php if (isset($this->vars['item']['sub']) && $this->vars['item']['sub']  ): ?>class="active"<?php endif; ?><?php endif; ?><?php endif; ?> ><a href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></a>
    
    <?php if (isset($this->vars['item']['childs']) && $this->vars['item']['childs']  ): ?>
      <span class="categoryPlusIcon" onclick="$(this).next().slideToggle()"></span>
      <ul>
    	<?php if(count($this->vars['item']['childs'])) : 
							   		 $this->vars['foreach']['item.childs']['count'] = count($this->vars['item']['childs']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['item']['childs']);
							   		 foreach($this->vars['item']['childs'] as $this->vars['foreach']['item_childs']['key'] => $this->vars['child']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['item_childs']['key']; ?>
      <li <?php if (isset($this->vars['category_id']) && $this->vars['category_id'] == $this->vars['child']['id']): ?>class="active"<?php endif; ?>><a href="<?php if(isset($this->vars['child']['url'])) echo $this->vars['child']['url']; ?>"><?php if(isset($this->vars['child']['name'])) echo $this->vars['child']['name']; ?></a>
      <?php endforeach; ?><?php endif; ?>
      </ul>
    <?php endif; ?>
    
    </li>
    <?php endforeach; ?><?php endif; ?>
  </ul>
  <?php if($core->lib->widget("ProductGroups")): ?>
<?php $core->widgets->ProductGroups()->setting("parent_id", "32"); ?>
<?php $core->widgets->ProductGroups()->setting("current_id", "".$this->vars['category_id'].""); ?>

<?php $core->widgets->ProductGroups()->appendSettings(); $core->widgets->ProductGroups()->main(); endif; ?>
  <ul id="catCategoriesList" <?php if (isset($this->vars['category_info']['parrent_id']) && $this->vars['category_info']['parrent_id'] == 1375): ?>class="activeTab"<?php endif; ?>>
    <?php if(count($this->vars['ProductGroupsOut'])) : 
							   		 $this->vars['foreach']['ProductGroupsOut']['count'] = count($this->vars['ProductGroupsOut']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['ProductGroupsOut']);
							   		 foreach($this->vars['ProductGroupsOut'] as $this->vars['foreach']['ProductGroupsOut']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['ProductGroupsOut']['key']; ?>
    <li <?php if (isset($this->vars['category_id']) && $this->vars['category_id'] == $this->vars['item']['id']): ?>class="active"<?php endif; ?>><a href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></a></li>
    <?php endforeach; ?><?php endif; ?>
  </ul>
</div>