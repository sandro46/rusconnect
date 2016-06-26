<? $this->md5_compil='b13ed59578f805fabe5544f985b743c6'; ?>
<div class="row-fluid">
	<div class="widget smallWidget">
		<div class="widget-title">
          <h4><i class="icon-reorder"></i>Карточка пользователя</h4>
      	</div>
      	<div class="widget-body">
          <form class="form-horizontal">
            <div class="control-group">
				<label class="control-label">Имя: <span style="color:red">*</span></label>
              	<div class="controls">
                  <input type="text" name="name_first" value="" class="span12 require">
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Фамилия: </label>
              	<div class="controls">
                  <input  type="text" name="name_last" value="" class="span12">
              	</div>
            </div>
                        
            <div class="control-group">
				<label class="control-label">Отчество:</label>
              	<div class="controls">
                  <input type="text" name="name_second" value="" class="span12">
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Телефон:</label>
              	<div class="controls">
                  <input type="text" name="phone" value="" class="span12">
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Email: <span style="color:red">*</span></label>
              	<div class="controls">
                  <input type="text" name="email" value="" class="span12 require">
              	</div>
            </div>
            
            <div class="control-group">
				<label class="control-label">Аватар</label>
              	<div class="controls">
                 	<div class="AvatarUploadButton"></div>
                  	<div class="AvatarContainer"></div>
              	</div>
            </div>
            
          </form>
      	</div>
            
        <div class="widget-title">
          <h4><i class="icon-reorder"></i>Учетные данные</h4>
        </div>
 
        <input type="hidden" name="user_id" value="">
      
      	<div class="widget-body">
          <form class="form-horizontal">
            
          	<div class="control-group" name="passrow">
				<label class="control-label">Пароль: <span style="color:red">*</span></label>
              	<div class="controls">
                  <input class="require" type="text" name="passwd" value="">
                  <a class="btn" action="passwordChange"><i class="icon-repeat"></i></a>
              	</div>
            </div>
            
            <div class="control-group" name="noticerow">
              <label class="control-label">Отправить уведомление на почту:</label>
              <div class="controls">
                <div class="sendnotify"><input type="checkbox" <?php if (isset($this->vars['clientInfo']['client']['enabled']) && $this->vars['clientInfo']['client']['enabled']  ): ?>checked="checked"<?php endif; ?> name="notify" value="1" class="toggle" checked="checked" /></div>
              </div>
            </div> 
            
            <div class="control-group">
              <label class="control-label">Роль пользователя:</label>
              <div class="controls">
                <select class="chosen require" name="access_group" data-placeholder="Выберите роль пользователя">
                  <option></option>
                
                  
                  <?php if(count($this->vars['accessGroups'])) : 
							   		 $this->vars['foreach']['accessGroups']['count'] = count($this->vars['accessGroups']);
							   		 $this->vars['foreach']['last']['count'] = count($this->vars['accessGroups']);
							   		 foreach($this->vars['accessGroups'] as $this->vars['foreach']['accessGroups']['key'] => $this->vars['item']):
							            $this->vars['foreach']['key'] = $this->vars['foreach']['accessGroups']['key']?>
                  <option value="<?php if(isset($this->vars['item']['group_id'])) echo $this->vars['item']['group_id']; ?>" <?php if (isset($this->vars['userGroupId']) && $this->vars['userGroupId']  ): ?><?php if (isset($this->vars['item']['group_id']) && $this->vars['item']['group_id'] == $this->vars['userGroupId']): ?>selected="selected"<?php endif; ?><?php endif; ?>><?php if(isset($this->vars['item']['name'])) echo $this->vars['item']['name']; ?></option>
                  <?php endforeach; ?><?php endif; ?>
                  <optgroup label="--------"><option value="0">Создать новую группу</option></optgroup>
                </select>
              </div>
            </div> 
            
            <div class="control-group" style="display:none" name="rolename-control">
				<label class="control-label">Название новой роли: <span style="color:red">*</span></label>
              	<div class="controls">
                  <input type="text" name="rolename" value="" class="span12">
              	</div>
            </div>
                                       
           
            <table class="table table-hover" name="accesRulesTable"><tr><td style="text-align:center">Роль пользователя не выбрана.</td></tr></table>
            <br><br>
           
            
            <button type="button" action="save" class="btn btn-success">Сохранить</button>
			<button type="button" action="back" class="btn">Назад</button>
          </form>
      	</div>
  	</div>
</div>