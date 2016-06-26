$(document).ready(function() {  
	
	
	//App.init();

             
      
     $('#sidebar .has-sub > a').click(function () {
         var last = jQuery('.has-sub.open', $('#sidebar'));
         last.removeClass("open");
         $('.arrow', last).removeClass("open");
         $('.sub', last).slideUp(200);
         var sub = jQuery(this).next();
         if (sub.is(":visible")) {
             $('.arrow', jQuery(this)).removeClass("open");
             $(this).parent().removeClass("open");
             sub.slideUp(200);
         } else {
             $('.arrow', jQuery(this)).addClass("open");
             $(this).parent().addClass("open");
             sub.slideDown(200);
         }
     });

     
     $('.sidebar-toggler').click(function () {
         if ($("#container").is('.sidebar-closed')) {
        	 $('#main-content').animate({
                 'margin-left': '215px'
             });
             
        	 $('#sidebar ul').animate({
            	 'width': '215px'
             }, {
                 complete: function () {
                     $("#container").removeClass("sidebar-closed");
                 }
             });
        	 
        	 $('#sidebar').animate({
            	 'width': '215px'
             });
        	 
        	 $('#sidebar ul li .menutext').animate({
                 'margin-left': '0px'
             });
        	 
        	 /*
             $('#sidebar').animate({
                 'margin-left': '0'
             }, {
                 complete: function () {
                     $("#container").removeClass("sidebar-closed");
                 }
             }); */
         } else {
        	 $('#main-content').animate({
                 'margin-left': '50px'
             });
        	 
        	 $('#sidebar ul li .menutext').animate({
        		 'position' : 'absolute',
                 'margin-left': '-250px'
             });
        	 
        	 $('#sidebar').animate({
            	 'width': '50px'
             });
        	 
             $('#sidebar ul').animate({
            	 'width': '50px'
             }, {
                 complete: function () {
                     $("#container").addClass("sidebar-closed");
                 }
             });
         }
     })
     
     
     setInterval(function(){
		 globalApi.sessionCheck(function(sid){
			
		 });
	 }, 1*60*1000);
});