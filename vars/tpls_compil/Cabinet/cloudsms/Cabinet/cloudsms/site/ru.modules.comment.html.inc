<? $this->md5_compil='6b999274d60570d255bac6d844833592'; ?>

<div class="control-group">
  <label class="control-label">Использовать каптчу?</label>
  <div class="controls">
   	<div class="toggleButtonContainer"><input <?php if (isset($this->vars['moduleInfo']['use_captcha']) && $this->vars['moduleInfo']['use_captcha']  ): ?>checked="checked"<?php endif; ?> type="checkbox" class="toggle" name="use_captcha" module-id="<?php if(isset($this->vars['moduleId'])) echo $this->vars['moduleId']; ?>" value="1"></div>
  </div>
</div>

<div class="control-group">
  <label class="control-label">Отправлять уведомления мне на email?</label>
  <div class="controls">
   	<div class="toggleButtonContainer"><input <?php if (isset($this->vars['moduleInfo']['use_notifier']) && $this->vars['moduleInfo']['use_notifier']  ): ?>checked="checked"<?php endif; ?> type="checkbox" class="toggle" name="use_notifier" module-id="<?php if(isset($this->vars['moduleId'])) echo $this->vars['moduleId']; ?>" value="1"></div>
  </div>
</div>