<? $this->md5_compil='d1436d52521c832de1e2f1bf1cbc43ed'; ?>

<link rel="stylesheet" type="text/css" href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/fancybox/source/jquery.fancybox.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>css/nodechat.css" media="screen" />

<script src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>modules/director.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>modules/uploader.js"></script>
<script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/fancybox/source/jquery.fancybox.pack.js"></script>
<script type="text/javascript">  
  $(document).ready(function(){
    nodechat.init();
  });
</script>

<div class="row-fluid">
   <div class="span12">
     <h3 class="page-title">Чат <small>Чат с клиентами</small></h3>
     <ul class="breadcrumb" id="crumbs"></ul>
   </div>
</div>