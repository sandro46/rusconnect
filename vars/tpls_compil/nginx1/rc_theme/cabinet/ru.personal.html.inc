<? $this->md5_compil='00bcdc4dea943d005dd51337ce4a97bd'; ?>
<h1>Персональные данные</h1>

<div class="small-form">
  <form class="form-horizontal">
    <div class="control-group">
      <label class="control-label">Фамилия <span>*</span></label>
      <div class="controls"><input type="text" name="surname" class="span8 require" value="{{contact.surname}}"></div>
    </div>
    <div class="control-group">
      <label class="control-label">Имя <span>*</span></label>
      <div class="controls"><input type="text" name="name" class="span8 require"  value="{{contact.name}}"></div>
    </div>
    <div class="control-group">
      <label class="control-label">Отчество <span>*</span></label>
      <div class="controls"><input type="text" name="lastname" class="span8 require"  value="{{contact.lastname}}"></div>
    </div>

    <div class="control-group">
      <label class="control-label">Телефон <span>*</span></label>
      <div class="controls"><input type="text" name="phone" class="span8 require"  value="{{contact.phone}}"></div>
    </div>
    
    <div class="control-group">
      <label class="control-label">Email <span>*</span></label>
      <div class="controls"><input type="text" name="email" class="span8 require"  value="{{contact.email}}"></div>
    </div>
    
    
    <div class="control-group">
      <label class="control-label">
      <a href="javascript:void(0)" action="back" class="btnGray" style="font-weight: normal;">Назад</a>
      
      </label>
      <div class="controls"> 
        <a href="#/my/changepassword">Изменить пароль</a> <br> <br> <br>
       
        <a href="javascript:void(0)" action="save" class="btnBlue">Сохранить</a>
      </div>
    </div>
  </form>
</div>