<? $this->md5_compil='d764e630ba26c18f5af9bade6f44b412'; ?>



<div class="row-fluid">
  <div class="span12" id="contactlist">
    <div class="widget">
      <div class="widget-title" id="source-tempate-editor-menu">
        <span class="tools" name="source" style="float:left; cursor:pointer"><span class="item"><i class="icon-reorder"></i> Код шаблона</span></span>
        <span class="tools" name="global" style="float:left; cursor:pointer"><span class="item"><i class="icon-certificate"></i> Общие данные</span></span>
        <span class="tools" name="history" style="float:left; cursor:pointer"><span class="item"><i class="icon-globe"></i> История изменений</span></span>
      </div>
      <div class="widget-body">
        <div class="source">
          <form>
            <textarea id="source-tempate-editor" style="height:600px;"></textarea>
              <br><br>
              <button class="btn btn-success save-button" type="button">Сохранить</button>
            	<button class="btn btn-primary savenexit-button" type="button">Сохранить и выйти</button>
              <button class="btn close-button" type="button">Назад</button>
          </form>
        </div>
        <div class="global" style="display:none">
          <form class="form-horizontal">
            <div class="control-group">
              <label class="control-label">Название шаблона</label>
              <div class="controls">
                <input class=" " type="text" name="name" value="<?php if(isset($this->vars['tplInfo']['name'])) echo $this->vars['tplInfo']['name']; ?>" style="width:400px;">
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Модуль</label>
              <div class="controls">
                <input type="text" name="name_module" value="<?php if(isset($this->vars['tplInfo']['name_module'])) echo $this->vars['tplInfo']['name_module']; ?>">
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Тема</label>
              <div class="controls">
                <input type="text" name="theme" value="<?php if(isset($this->vars['tplInfo']['theme'])) echo $this->vars['tplInfo']['theme']; ?>">
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Описание</label>
              <div class="controls">
                <input class=" " type="text" name="description" value="<?php if(isset($this->vars['tplInfo']['description'])) echo $this->vars['tplInfo']['description']; ?>" style="width:400px;">
              </div>
            </div>
          </form>
        </div>
        <div class="history" style="display:none">
           <table class="table table-striped table-hover table-bordered dataTable">
             <thead>
               <tr role="row">
                 <th>Commit</th>
                 <th>Дата</th>
                 <th>Действие</th>
                 <th>Пользователь</th>
                 <th>IP адрес</th>
                 <th>-</th>
               </tr>
             </thead>
             <tbody>
               <?php if(count($this->vars['history'])) : 
							   		 $this->vars['foreach']['history']['count'] = count($this->vars['history']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['history']);
							   		 foreach($this->vars['history'] as $this->vars['foreach']['history']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['history']['key']?>
               	<tr>
                  <?php if (isset($this->vars['item']['action']) && $this->vars['item']['action'] == 'edit'): ?>
                 	<td><?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?></td>
                  	<td><?php if(isset($this->vars['item']['date'])) echo $this->vars['item']['date']; ?></td>
                  	<td>Документ изменен</td>
                  	<td><?php if(isset($this->vars['item']['user_name'])) echo $this->vars['item']['user_name']; ?></td>
                  	<td><?php if(isset($this->vars['item']['ip_address'])) echo $this->vars['item']['ip_address']; ?></td>
                  	<td><a href="javascript:void(0)" action="rollback" revision="<?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?>">Откатить</a> <a href="javascript:void(0)">Сравнить</a></td>
                  <?php endif; ?>
                  <?php if (isset($this->vars['item']['action']) && $this->vars['item']['action'] == 'rollback'): ?>
                  	<td><?php if(isset($this->vars['item']['id'])) echo $this->vars['item']['id']; ?></td>
                  	<td><?php if(isset($this->vars['item']['date'])) echo $this->vars['item']['date']; ?></td>
                  	<td>Откат до версии №<?php if(isset($this->vars['item']['rollback_id'])) echo $this->vars['item']['rollback_id']; ?> от (<?php if(isset($this->vars['item']['parent_date'])) echo $this->vars['item']['parent_date']; ?>)</td>
                  	<td><?php if(isset($this->vars['item']['user_name'])) echo $this->vars['item']['user_name']; ?></td>
                  	<td><?php if(isset($this->vars['item']['ip_address'])) echo $this->vars['item']['ip_address']; ?></td>
                  	<td></td>
                  <?php endif; ?>
                 </tr>
               <?php endforeach; ?><?php endif; ?>
             </tbody>
          </table>
        </div>
      </div>
      
      
    </div>
  </div>
</div>
      