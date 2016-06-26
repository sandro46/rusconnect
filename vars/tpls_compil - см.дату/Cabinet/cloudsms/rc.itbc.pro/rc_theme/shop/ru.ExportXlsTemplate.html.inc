<? $this->md5_compil='ca11467924020e540e2cf6169337d9a4'; ?>
<table>
  <thead>
    <tr>
      <?php if(count($this->vars['tbldata']['header'])) : 
							   		 $this->vars['foreach']['tbldata.header']['count'] = count($this->vars['tbldata']['header']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['tbldata']['header']);
							   		 foreach($this->vars['tbldata']['header'] as $this->vars['foreach']['tbldata_header']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['tbldata_header']['key']?>
      <th><?php if(isset($this->vars['item'])) echo $this->vars['item']; ?></th>
      <?php endforeach; ?><?php endif; ?>
    </tr>
  </thead>
  <tbody>
    <?php if(count($this->vars['tbldata']['rows'])) : 
							   		 $this->vars['foreach']['tbldata.rows']['count'] = count($this->vars['tbldata']['rows']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['tbldata']['rows']);
							   		 foreach($this->vars['tbldata']['rows'] as $this->vars['foreach']['tbldata_rows']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['tbldata_rows']['key']?>
    	<tr>
      		<?php if(count($this->vars['item'])) : 
							   		 $this->vars['foreach']['item']['count'] = count($this->vars['item']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['item']);
							   		 foreach($this->vars['item'] as $this->vars['foreach']['item']['key'] => $this->vars['entry']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['item']['key']?>
          <td><?php if(isset($this->vars['entry'])) echo $this->vars['entry']; ?></td>
          <?php endforeach; ?><?php endif; ?>
    	</tr>
    <?php endforeach; ?><?php endif; ?>
  </tbody>
</table>