<? $this->md5_compil='0799e9dd98e1ada944e09d88ce074d66'; ?>
<div class="control-group">
  <label class="control-label">Режим работы колл-центра</label>
  <div class="controls">
    C: <input type="text"  module-id="<?php if(isset($this->vars['moduleId'])) echo $this->vars['moduleId']; ?>" name="callback_from" value="<?php if(isset($this->vars['moduleInfo']['callback_from'])) echo $this->vars['moduleInfo']['callback_from']; ?>">
     По: <input type="text"  module-id="<?php if(isset($this->vars['moduleId'])) echo $this->vars['moduleId']; ?>" name="callback_to" value="<?php if(isset($this->vars['moduleInfo']['callback_to'])) echo $this->vars['moduleInfo']['callback_to']; ?>">
  </div>
</div>