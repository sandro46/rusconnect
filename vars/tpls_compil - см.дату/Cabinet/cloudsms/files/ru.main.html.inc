<? $this->md5_compil='c707bd64086fa6c6c8952f080cf0f0ae'; ?>

<script src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>modules/director.js" type="text/javascript"></script>
<script src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>modules/admin.files.js" type="text/javascript"></script>

<script  src="/plugins/elfinder/js/elfinder.min.js" type="text/javascript"></script>
<script  src="/plugins/elfinder/js/i18n/elfinder.ru.js" type="text/javascript"></script>

<link  href="/plugins/elfinder/css/elfinder.min.css" rel="stylesheet">
<link  href="/plugins/elfinder/css/theme.css" rel="stylesheet">

<script  src="/plugins/codemirror/lib/codemirror-compressed.js?v2" type="text/javascript"></script>

<link  href="/plugins/codemirror/lib/codemirror.css?v2" rel="stylesheet">
<link rel="stylesheet" type="text/css" media="screen" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css">

<div class="row-fluid">
   <div class="span12">
     <h3 class="page-title">Файловый менеджер <small></small></h3>
     <ul class="breadcrumb" id="crumbs"></ul>
   </div>
</div>
<?php if(isset($this->vars['controller_content'])) echo $this->vars['controller_content']; ?>
