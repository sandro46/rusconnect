/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/**
 * @fileOverview Image plugin based on Widgets API
 */

'use strict';




CKEDITOR.dialog.add('image', function( editor ) {
	

	var uploadContainerId = CKEDITOR.tools.getNextId();
	var	uploadPreview = CKEDITOR.tools.getNextId();
	var	uplaodFileUrl = CKEDITOR.tools.getNextId();
	var	uplaodContainer = '<div id="'+uploadContainerId+'" class="uploadZoneProgress"></div>'+
						  '<input id="'+uplaodFileUrl+'" type="hidden" value="">'+
						  '<div style="width:250px; height:250px; margin-top:8px; background-position: 50% 50%;  border: 1px solid silver; background-size: contain; background-repeat:no-repeat;" id='+uploadPreview+'></div>';
	var basepath = CKEDITOR.basePath+'plugins/image/';

	return {
        title:          'Image Upload Dialog',
        resizable:      CKEDITOR.DIALOG_RESIZE_BOTH,
        minWidth:       255,
        minHeight:      330,
        
		onShow: function() {
			console.log(editor, CKEDITOR)
			$('#'+uploadPreview).css('background-image','url("'+basepath+'images/no_image.jpg")');
			$('#'+uplaodFileUrl).val('');
			$('#'+uploadContainerId).html('');
			
			var ckuploader = uploader.init({
				container : $("#"+uploadContainerId),
				formCaption : '',
				buttonElement : $('<a href="javascript:void(0)" class="cke_dialog_ui_button"><span class="cke_dialog_ui_button">Загрузить на сервер</span></a>'),
				
				hideUploaded : true,
				resize : ['original', 'smart:300x300'],
				done : function(info) {
					
				  
					
				  $('#'+uploadPreview).css('background-image','url("'+info[1].name+'")');
  	      	   	  $('#'+uplaodFileUrl).val(info[0].name+','+info[1].name);
					
				}
			});
			
			
			/*
			var ckuploader = new qq.FileUploader({
    	        element: $('#'+uploadContainerId)[0],
    	        action: '/ru/files/upload/source/widget/application/jup/filehandler/resizeImage/return/all/',
    	        debug: true,
    	        allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
    	        sizeLimit: 20*1024*1024,
    	        lang_upload:'Загрузить',
    	        onComplete: function(id, fileName, response) {  
    	        	
    	      	  if(typeof(response['local']) == 'string') {
    	      	      $('#'+uploadPreview).css('background-image','url("'+response['local']+'")');
    	      	   	  $('#'+uplaodFileUrl).val(response['local']);
    	          } else {
    	        	  $('#'+uploadPreview).css('background-image','url("'+response[1].local+'")');
    	      	   	  $('#'+uplaodFileUrl).val(response[0].local+','+response[1].local);
    	          }
    	       }
			});*/
		},
        
		onOk: function() {
			var image = $('#'+uplaodFileUrl).val();
			image = image.split(',');
		
			if(image) {
				var url = (typeof(editor.config.imageUrlPrefix) != 'undefined')? editor.config.imageUrlPrefix : '';
				
				editor.insertHtml('<a href="'+url+image[0]+'" data-lightbox="imageGallery"><img src="'+url+image[1]+'" /></a>', 'unfiltered_html');
				//this.commitContent();
			}
		},
		
        contents: [
            {
                id:         'tab1',
                label:      'First Tab',
                title:      'First Tab Title',
                accessKey:  'Q',
                elements: [
                    {
                        type:           'html',
                        id:             'testText1',
                        html: uplaodContainer
                    }
                ]
            }
        ]
    };
});
