<? $this->md5_compil='7a66966e5a1ae036f343e28e646e7dbf'; ?>
<div class="row-fluid">
  <div class="span12">
    <div class="widget">
      <div class="widget-title">
        <h4><i class="icon-user"></i><?php if(isset($this->vars['profilename'])) echo $this->vars['profilename']; ?></h4>                 
      </div>
      <div class="widget-body">
        <div class="span3">
          <div class="text-center" style="margin-bottom:10px;">
            <div class="AvatarContainer" nophoto-url="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>img/profile-pic.jpg">
            	<img src="<?php if (isset($this->vars['profile']['avatar_big']) && $this->vars['profile']['avatar_big']  ): ?><?php if(isset($this->vars['profile']['avatar_big'])) echo $this->vars['profile']['avatar_big']; ?><?php else: ?><?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>img/profile-pic.jpg<?php endif; ?>" alt="">
            </div>
            <div class="AvatarUploadButton"></div>
            <?php if (isset($this->vars['profile']['avatar_big']) && $this->vars['profile']['avatar_big']  ): ?>
            <a href="javascript:void(0)" onclick="site_users.deleteUserAvatar()" class="btn">Удалить фото</a>
            <?php endif; ?>
          </div>
          

          <ul class="nav nav-tabs nav-stacked">
            <?php if (isset($this->vars['userInfo']['user_id']) && $this->vars['userInfo']['user_id'] == $this->vars['profile']['user_id']): ?><li><a href="#/my/pass/"><i class="icon-key"></i> Изменить пароль</a></li><?php endif; ?>
            <li><a href="#/edit/<?php if(isset($this->vars['profile']['user_id'])) echo $this->vars['profile']['user_id']; ?>/"><i class="icon-pencil"></i> Редактировать профиль</a></li>
            <?php if (isset($this->vars['userInfo']['user_id']) && $this->vars['userInfo']['user_id'] != $this->vars['profile']['user_id']): ?><li><a href="#/send_message/<?php if(isset($this->vars['profile']['user_id'])) echo $this->vars['profile']['user_id']; ?>/"><i class="icon-envelope"></i> Отправить сообщение</a></li><?php endif; ?>
            
          </ul>          
        </div>
        <div class="span6">
          <h4>Логин: <?php if(isset($this->vars['profile']['login'])) echo $this->vars['profile']['login']; ?> <br/><small>Группа: <?php if(isset($this->vars['profile']['group_name'])) echo $this->vars['profile']['group_name']; ?></small></h4>
          <table class="table table-borderless">
            <tbody>
              <tr>
                <td class="span2">Фамилия :</td>
                <td><?php if(isset($this->vars['profile']['name_last'])) echo $this->vars['profile']['name_last']; ?></td>
              </tr>
              <tr>
                <td class="span2">Имя :</td>
                <td><?php if(isset($this->vars['profile']['name_first'])) echo $this->vars['profile']['name_first']; ?></td>
              </tr>
              <tr>
                <td class="span2">Отчество :</td>
                <td><?php if(isset($this->vars['profile']['name_second'])) echo $this->vars['profile']['name_second']; ?></td>
              </tr>
             
              <tr>
                <td class="span2"> Email :</td>
                <td><?php if(isset($this->vars['profile']['email'])) echo $this->vars['profile']['email']; ?></td>
              </tr>
              <tr>
                <td class="span2"> Телефон :</td>
                <td><?php if(isset($this->vars['profile']['phone'])) echo $this->vars['profile']['phone']; ?> </td>
              </tr>
            </tbody>
          </table>

          
          <h4>Компания:</h4>
          <div class="well">
            <?php if (isset($this->vars['profile']['company']) && $this->vars['profile']['company']  ): ?>
              <strong>Vector Lab, Inc.</strong><br>
              Dreamland Ave, Suite 73<br>
              Bangladesh, PC 1361<br>
              <abbr title="Phone">P:</abbr> (123) 456-7891
            
              <strong>Full Name</strong><br>
              <a href="mailto:#">first.last@gmail.com</a>
            <?php else: ?>
            	<strong>Нет данных о компании</strong>
            <?php endif; ?>
          </div>
          <h4>Дополнительно:</h4>
          <div class="well">
            <?php if(isset($this->vars['profile']['comment'])) echo $this->vars['profile']['comment']; ?>
          </div>
        </div>
        
        <div class="span3">
          <h4>Информация</h4>
          <ul class="icons push">
            <li><i class="icon-hand-right"></i> <strong>Аккаунт создан:</strong><br/><em><?php if(isset($this->vars['profile']['reg_date'])) echo $this->vars['profile']['reg_date']; ?></em></li>
            <li><i class="icon-hand-right"></i> <strong>Последний вход:</strong><br/><em><?php if(isset($this->vars['profile']['last_auth'])) echo $this->vars['profile']['last_auth']; ?></em></li>
         	   
          </ul>
           <?php if (isset($this->vars['userInfo']['user_id']) && $this->vars['userInfo']['user_id'] != $this->vars['profile']['user_id']): ?>
          <h4>Текущий статус:</h4>
         
            <?php if (isset($this->vars['profile']['enabled']) && $this->vars['profile']['enabled']  ): ?>
              <div class="alert alert-success"><i class="icon-ok-sign"></i> Аккаунт активен</div>
              <button class="btn btn-warning" action="blockAccount" onclick="site_users.blockUser(<?php if(isset($this->vars['profile']['user_id'])) echo $this->vars['profile']['user_id']; ?>)">Заблокировать</button>
            <?php else: ?>
              <div class="alert alert-danger"><i class="icon-minus-sign"></i> Аккаунт заблокирован<br>&laquo;<?php if(isset($this->vars['profile']['block_reason'])) echo $this->vars['profile']['block_reason']; ?>&raquo;</div>
              <button class="btn btn-success" action="unblockAccount" onclick="site_users.unblockUser(<?php if(isset($this->vars['profile']['user_id'])) echo $this->vars['profile']['user_id']; ?>)">Разблокировать</button>
            <?php endif; ?>
          
          	<button class="btn btn-danger" action="deleteAccount" onclick="site_users.deleteUser(<?php if(isset($this->vars['profile']['user_id'])) echo $this->vars['profile']['user_id']; ?>)">Удалить аккаунт</button>
          <?php endif; ?>
        </div>
        <div class="space5"></div>
      </div>
    </div>
  </div>
</div>