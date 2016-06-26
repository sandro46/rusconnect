<? $this->md5_compil='dba2eb048d9b3b80e017acce1d46afaa'; ?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="ru" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="ru" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="ru"> <!--<![endif]-->
<head>
   <meta charset="utf-8" />
   <title>Ncity.CloudShop</title>
   <meta content="width=device-width, initial-scale=1.0" name="viewport" />
   <meta content="Ncity.CloudShop - cloud platform for eCommerce" name="description" />
   <meta content="Ncity LLC" name="author" />
  

   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" />
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/bootstrap/css/bootstrap-fileupload.css" rel="stylesheet" />
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>css/style.css" rel="stylesheet" />
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>css/style_responsive.css" rel="stylesheet" />
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>css/style_default.css" rel="stylesheet" id="style_color" />
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>css/ncity_admin.css" rel="stylesheet" type="text/css"/>

    
    <!-- plugins css -->
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/bootstrap-toggle-buttons/static/stylesheets/bootstrap-toggle-buttons.css" rel="stylesheet">
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/chosen-bootstrap/chosen/chosen.css" type="text/css" rel="stylesheet">
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/bootstrap-datepicker/css/datepicker.css" type="text/css" rel="stylesheet">
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/bootstrap-timepicker/compiled/timepicker.css" type="text/css" rel="stylesheet">
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/bootstrap-daterangepicker/daterangepicker.css" type="text/css" rel="stylesheet">
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/bootstrap-toggle-buttons/static/stylesheets/bootstrap-toggle-buttons.css" rel="stylesheet">
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/jslider/bin/jquery.slider.min.css" type="text/css" rel="stylesheet">  
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"  />
   <link href="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/gritter/css/jquery.gritter.css" rel="stylesheet" type="text/css"/>
  
   <!-- base UI script -->
   <script src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/jquery-1.8.3.min.js"></script>
   <script src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/bootstrap/js/bootstrap.min.js"></script>
   <script src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/jquery.blockui.js"></script>
   <script src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/jquery-ui/jquery-ui-1.10.1.custom.min.js"></script>
 

   <!--[if lt IE 9]>
     <script src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/excanvas.js"></script>
     <script src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/respond.js"></script>
   <![endif]-->
  
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/bootstrap-toggle-buttons/static/js/jquery.toggle.buttons.js"></script>
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/uniform/jquery.uniform.min.js"></script> 
  
  
  
   
  <!-- /base UI script -->
  
   <!-- core script -->
   <script type="text/javascript">
     var static = '<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>';
   </script>
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>modules/common.js"></script>
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>modules/templates.js"></script>
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>modules/ajax.js"></script>
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>modules/grid.js"></script>
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>modules/uploader.js"></script>
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>modules/admin.main.js"></script>
   <?php if(isset($this->vars['majax'])) echo $this->vars['majax']; ?>
   <!-- /core script -->
  
</head>
<body class="fixed-top">
  <?php echo $this->get("header.html"); ?>
  
  <div id="container" class="row-fluid">
    
    
    <?php if (isset($this->vars['userInfo']['system_user_id']) && $this->vars['userInfo']['system_user_id'] == 4): ?><?php echo $this->get("left_menu_admin_user.html"); ?><?php endif; ?>
    <?php if (isset($this->vars['userInfo']['system_user_id']) && $this->vars['userInfo']['system_user_id'] == 2): ?><?php echo $this->get("left_menu_shop_user.html"); ?><?php endif; ?>
    
    <div id="main-content"> 
      <div class="container-fluid">
        <?php if(isset($this->vars['content'])) echo $this->vars['content']; ?>      
      </div>
    </div>
  </div>
  
  <div id="footer">
       2003-2014 &copy; ITBC CloudShop. &copy; Itbc LLC. 
      <div class="span pull-right" onclick="$page.top()">
         <span class="go-top"><i class="icon-arrow-up"></i></span>
      </div>
   </div>
  
   <!-- plugins js -->
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>   
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/bootstrap-daterangepicker/date.js"></script>
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/bootstrap-daterangepicker/daterangepicker.js"></script>
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/jslider/bin/jquery.slider.min.js"></script>
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>assets/gritter/js/jquery.gritter.js"></script>
  
   <!-- custom main.js / in production use compiled main.min.js -->
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>js/main.js"></script>
  
  
  
   <!-- web chat
   <script type="text/javascript" src="http://<?php if(isset($this->vars['SERVER_NAME'])) echo $this->vars['SERVER_NAME']; ?>:8080/socket.io/socket.io.js" id="socketio"></script>
   <script type="text/javascript" src="<?php if(isset($this->vars['static'])) echo $this->vars['static']; ?>modules/chat.js"></script>
   -->

  
</body>
</html>
