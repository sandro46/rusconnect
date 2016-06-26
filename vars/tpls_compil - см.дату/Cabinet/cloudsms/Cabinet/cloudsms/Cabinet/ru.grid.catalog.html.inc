<? $this->md5_compil='91f04c353f1d860fca32122064a73837'; ?>
<table class="table table-striped table-bordered table-advance table-hover" id="grid-<?php if(isset($this->vars['instance'])) echo $this->vars['instance']; ?>">
  <thead>
    <tr>
      <?php if (isset($this->vars['grid']['settings']['group_actions']) && $this->vars['grid']['settings']['group_actions']  ): ?><th> - </th><?php endif; ?>
      <?php if (isset($this->vars['grid']['settings']['user_settings']) && $this->vars['grid']['settings']['user_settings']  ): ?><?php $this->vars['collIterator'] = 0; ?><?php endif; ?>
        <?php if(count($this->vars['grid']['settings']['cols'])) : 
							   		 $this->vars['foreach']['grid.settings.cols']['count'] = count($this->vars['grid']['settings']['cols']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['grid']['settings']['cols']);
							   		 foreach($this->vars['grid']['settings']['cols'] as $this->vars['foreach']['grid_settings_cols']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['grid_settings_cols']['key']?>
          <?php if (isset($this->vars['grid']['settings']['user_settings']) && $this->vars['grid']['settings']['user_settings']  ): ?><?php $this->vars['collIterator']++; ?><?php endif; ?>
          <th <?php if (isset($this->vars['grid']['settings']['user_settings']) && $this->vars['grid']['settings']['user_settings']  ): ?><?php if (isset($this->vars['collIterator']) && $this->vars['collIterator'] > 0): ?>hidden="true"<?php endif; ?><?php endif; ?> <?php if(isset($this->vars['item']['ext'])) echo $this->vars['item']['ext']; ?> <?php if (isset($this->vars['item']['align']) && $this->vars['item']['align']  ): ?>align="<?php if(isset($this->vars['item']['align'])) echo $this->vars['item']['align']; ?>"<?php endif; ?> name="<?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?>" class="<?php if(isset($this->vars['item']['class'])) echo $this->vars['item']['class']; ?>" <?php if (isset($this->vars['item']['sort']) && $this->vars['item']['sort']  ): ?>onclick="grid.<?php if(isset($this->vars['instance'])) echo $this->vars['instance']; ?>.sort(this)"<?php endif; ?>><?php if (isset($this->vars['item']['sort']) && $this->vars['item']['sort']  ): ?><i class="icon-sort"></i><?php endif; ?><?php if(isset($this->vars['item']['title'])) echo $this->vars['item']['title']; ?></td>
      <?php endforeach; ?><?php endif; ?>
    </tr>  
  </thead>
  <tbody class="grid-body">
    <tr local-row-id="{__grid_local_row_id}" class="<?php if (isset($this->vars['grid']['settings']['click']) && $this->vars['grid']['settings']['click']  ): ?>grid-row-clickable<?php endif; ?> {__row_class__}" row-type="{row_type}" type  <?php if (isset($this->vars['grid']['settings']['click']) && $this->vars['grid']['settings']['click']  ): ?>onclick="grid.<?php if(isset($this->vars['instance'])) echo $this->vars['instance']; ?>.click(this)"<?php endif; ?>>
      <?php if (isset($this->vars['grid']['settings']['group_actions']) && $this->vars['grid']['settings']['group_actions']  ): ?>
      <td class="<?php if(isset($this->vars['item']['ext'])) echo $this->vars['item']['ext']; ?>"><?php if(isset($this->vars['item']['ext'])) echo $this->vars['item']['ext']; ?><input type="checkbox" name="groupAction" value="<?php if (isset($this->vars['grid']['settings']['group_action_value']) && $this->vars['grid']['settings']['group_action_value']  ): ?>{<?php if(isset($this->vars['grid']['settings']['group_action_value'])) echo $this->vars['grid']['settings']['group_action_value']; ?>}<?php else: ?>{id}<?php endif; ?>"></td>
      <?php endif; ?>
      
      <?php if (isset($this->vars['grid']['settings']['user_settings']) && $this->vars['grid']['settings']['user_settings']  ): ?><?php $this->vars['collIterator'] = 0; ?><?php endif; ?>
      <?php if(count($this->vars['grid']['settings']['cols'])) : 
							   		 $this->vars['foreach']['grid.settings.cols']['count'] = count($this->vars['grid']['settings']['cols']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['grid']['settings']['cols']);
							   		 foreach($this->vars['grid']['settings']['cols'] as $this->vars['foreach']['grid_settings_cols']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['grid_settings_cols']['key']?>
      <?php if (isset($this->vars['grid']['settings']['user_settings']) && $this->vars['grid']['settings']['user_settings']  ): ?><?php $this->vars['collIterator']++; ?><?php endif; ?>

      <td  <?php if (isset($this->vars['grid']['settings']['user_settings']) && $this->vars['grid']['settings']['user_settings']  ): ?><?php if (isset($this->vars['collIterator']) && $this->vars['collIterator'] > 0): ?>hidden="true"<?php endif; ?><?php endif; ?> <?php if(isset($this->vars['item']['ext'])) echo $this->vars['item']['ext']; ?> name="<?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?>" class="<?php if(isset($this->vars['item']['class'])) echo $this->vars['item']['class']; ?> {row_type}" <?php if (isset($this->vars['item']['align']) && $this->vars['item']['align']  ): ?>style="text-align: <?php if(isset($this->vars['item']['align'])) echo $this->vars['item']['align']; ?>;"<?php endif; ?> <?php if (isset($this->vars['item']['click']) && $this->vars['item']['click']  ): ?>onclick="<?php if(isset($this->vars['item']['click'])) echo $this->vars['item']['click']; ?>; evt = event || window.event; evt.cancelBubble = true;"<?php endif; ?>>

                    {<?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?>}
            </td>
        <?php endforeach; ?><?php endif; ?>
    </tr>
    
  </tbody>
</table>
<div class="row-fluid">
    <div class="span6">
        <div id="grid-<?php if(isset($this->vars['instance'])) echo $this->vars['instance']; ?>-found-stat" class="dataTables_info"></div>
    </div>
    <div class="span6">
        <div id="grid-<?php if(isset($this->vars['instance'])) echo $this->vars['instance']; ?>-pagiation" class="dataTables_paginate paging_bootstrap pagination">
            <ul>
                <li is="prev" class="prev"><a href="javascript:void(0)">← Предыдущая</a></li>
                <li is="page_active" class="active"><a href="javascript:void(0)" >1</a></li>
                <li is="page_default"><a href="javascript:void(0)">2</a></li>
                <li is="page_dot"><a href="javascript:void(0)">...</a></li>
                <li is="next" class="next"><a href="javascript:void(0)">Следующая → </a></li>
            </ul>
        </div>
    </div>
</div>



  