<? $this->md5_compil='29341532c67e8ff22febeeb3eb179ca9'; ?>
<div class="catalogAjaxContent">

	<?php if(count($this->vars['categories'])) : 
							   		 $this->vars['foreach']['categories']['count'] = count($this->vars['categories']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['categories']);
							   		 foreach($this->vars['categories'] as $this->vars['foreach']['categories']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['categories']['key']; ?>

		<div class="item">

			<a class="header" href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>">
				<div class="icon" style="background:url('<?php if(isset($this->vars['item']['icon'])) echo $this->vars['item']['icon']; ?>')"></div>
				<div class="name"><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></div>
			</a> 
			
			<div class="childs">
				<?php if(count($this->vars['item']['childs'])) : 
							   		 $this->vars['foreach']['item.childs']['count'] = count($this->vars['item']['childs']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['item']['childs']);
							   		 foreach($this->vars['item']['childs'] as $this->vars['foreach']['item_childs']['key'] => $this->vars['item2']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['item_childs']['key']; ?>
					<a href="<?php if(isset($this->vars['item2']['url'])) echo $this->vars['item2']['url']; ?>"><?php if(isset($this->vars['item2']['name'])) echo $this->vars['item2']['name']; ?></a>
				<?php endforeach; ?><?php endif; ?>
			</div>
          
            <a href="<?php if(isset($this->vars['item']['url'])) echo $this->vars['item']['url']; ?>" class="showAll">показать все (<?php if(isset($this->vars['item']['products_count'])) echo $this->vars['item']['products_count']; ?>)</a>

		</div>

	<?php endforeach; ?><?php endif; ?>
  
  <br style="clear:both;">

</div>