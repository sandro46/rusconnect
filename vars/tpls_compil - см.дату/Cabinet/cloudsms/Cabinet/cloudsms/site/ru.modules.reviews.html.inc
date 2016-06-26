<? $this->md5_compil='319d990b33342bef44010f6575432d72'; ?>
<div class="control-group">
  <label class="control-label">Сколько выводить коментариев?</label>
  <div class="controls">
    <input type="text" value="<?php if(isset($this->vars['moduleInfo']['rewiev_show_items'])) echo $this->vars['moduleInfo']['rewiev_show_items']; ?>" module-id="<?php if(isset($this->vars['moduleId'])) echo $this->vars['moduleId']; ?>" name="rewiev_show_items">
  </div>
</div>