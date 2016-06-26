//
// This is a simple javascript uploader. 
// Used Nginx upload_progress plugin
//
// Copyright 2010-2014 by Alexey Pshenichniy / "CloudServices Framework"
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//   http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
var UploadGlobalObject = (function(){
	return {
		items : {},
		add: function(object) {
			this.items[object.config.uploaderId] = object;
		},
		complete: function(id, data) {
			console.log('complete:', id, data);
			if(typeof(this.items[id]) == 'object') {
				this.items[id].uploadComplete(data);
			}
		},
		instance: function(id) {
			if(typeof(this.items[id]) == 'object') {
				return this.items[id];
			}
		}
	}
})();


function UploaderClass() {
	this.configDefault = {
		container : false,
		formCaption : 'Прикрепить файлы:',
		buttonCaption : 'Загрузить файл',
		uploaderId: '',
		uploadUrl : '/ru/files/upload/source/jsuploader/application/simple_upload/',
		resize : [],
		maxsize : false,
		hideUploaded : false,
		buttonElement : false,
		multiple : false,
		postupload : false
	};
	this.config = {};
	this.uploadFileCounter = 0;
	this.uploadFiles = [];
	this.container = false;
	this.tpl = '';
	
	this.emit = function(name, data) {
		if(typeof(this.config[name]) == 'function') {
			this.config[name](data);
		}
	}
	
	this.instance = function() {
		return new UploaderClass();
	}
	
	this.getRandomId = function() {
		var id = "";
		var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		for( var i=0; i < 12; i++ )
			id += possible.charAt(Math.floor(Math.random() * possible.length));
		return id;
	};
	
	this.init = function(config){
		var self = this.instance();
		
		self.config = $.extend({}, self.configDefault, config);
		self.config.uploaderId = self.getRandomId();
		self.container = self.config.container;
		
		if(!self.container) return false;
		if(self.config.postupload) {
			self.config.uploadUrl += 'process/'+self.config.postupload+'/';
		}
		
		
		self.tpl = '<div class="upload-zone">'+
						'<b>'+self.config.formCaption+'</b>'+ 
						'<form id="'+self.config.uploaderId+'" enctype="multipart/form-data" action="'+self.config.uploadUrl+'" method="post">'+
							'<input type="hidden" skipformdata="1" name="callback" value="UploadGlobalObject.complete" />'+
							'<input type="hidden" skipformdata="1" name="session_id" value="'+self.config.uploaderId+'" />'+
							'<input type="hidden" skipformdata="1" name="resize" value="'+escape(JSON.stringify(self.config.resize))+'" />';
		
		if(self.config.maxsize) {
			self.tpl += '<input type="hidden" skipformdata="1" name="maxsize" value="'+escape(JSON.stringify(self.config.maxsize))+'" />'
		}
								
		self.tpl += 		'<div class="">'+
								'<div class="fileupload fileupload-new" data-provides="fileupload">'+
									'<span class="btn btn-file">';
		
		if(self.config.buttonElement) {
			self.tpl += self.config.buttonElement.html();
			self.config.buttonElement.remove();
		} else {
			self.tpl += '<span class="fileupload-new selectFile">'+self.config.buttonCaption+'</span>';
		}
		
		var multiple = (self.config.multiple == true)? 'multiple' : '';
		var inputName = (self.config.multiple == true)? 'file[]' : 'file';
		
		self.tpl += '<span class="fileupload-new uploadStart" onclick=" $(this).closest(\'form\').submit();" style="display:none">Загрузить</span>'+
										'<input type="file" skipformdata="1" session_id="'+ self.config.uploaderId +'" name="'+inputName+'"  class="default" '+multiple+' />'+
									'</span>'+
									'<br>'+
									'<span class="fileupload-preview-title">Идет загрузка файла: </span>'+
									'<span class="fileupload-preview" style="font-weight:bold"></span>'+
									'<a href="javascript:void(0)" class="close fileupload-exists" data-dismiss="fileupload" style="float: none; margin-left:10px">×</a>'+
								'</div>'+
							'</div>'+
						'</form>'+
						'<div id="'+self.config.uploaderId+'-files" style="display:none"></div>'+
						'<div id="'+self.config.uploaderId+'-progress" style="display:none">'+
							'<div class="progress progress-striped progress-success" style="width: 100%;"><div class="bar" id="'+self.config.uploaderId+'-progress-bar" style="width: 0%;"></div></div>'+
						'</div>'+
					'</div>';
		
		self.tpl = $(self.tpl);
		self.container.append(self.tpl);
		
		self.tpl.find('input[type="file"]').bind('change',function(){
			var id = $(this).attr('session_id');
			console.log('change file. id:  ' + id, $(this));
			UploadGlobalObject.instance(id).startUpload(this);
		});
		
		$('#'+self.config.uploaderId).uploadProgress({
			jqueryPath: window['static']+'js/jquery-1.8.3.min.js',
			uploadProgressPath: window['static']+"modules/uploader.js",
			uploading: function(upload) {
				$('#'+self.config.uploaderId+'-progress span').html(upload.percents+'%');
			},
			progressBar: '#'+self.config.uploaderId+'-progress-bar',
			progressUrl: "/progress",
			interval: 500
		});
		
		self.tpl.find('span.fileupload-preview-title').hide();
		
		UploadGlobalObject.add(self);
		
		return self;
	};
	
	
	this.startUpload = function(obj) {
		$('#'+this.config.uploaderId+'-files').show();
		$('#'+this.config.uploaderId+'-progress').show();
		$('#'+this.config.uploaderId+'-progress-bar').css({width:0});
		
		this.tpl.find('.alert').remove();
		
		
		var filename = $(obj).val();
		var iframeid = this.config.uploaderId+'-iframe-' +(this.uploadFileCounter += 1);
		$('<iframe src="javascript:false;" style="display:none" name="'+iframeid+'" id="'+iframeid+'"></iframe>').appendTo(document.body);
		
		$('#'+this.config.uploaderId+' span.fileupload-preview').html(filename);
		$('#'+this.config.uploaderId+' span.fileupload-preview-title').show();
		var self = this;
		$('#'+this.config.uploaderId+' a.close')
			.css({display:'inline-table'})
			.unbind('click')
			.bind('click',function(){
				var interval = $('#'+self.config.uploaderId).uploadProgress().data('timer');
				clearInterval(interval);
				$('#'+self.config.uploaderId+' span.fileupload-preview').html('');
				$('#'+self.config.uploaderId+' span.fileupload-preview-title').hide();
				$('#'+self.config.uploaderId+' a.close').hide();
				$('#'+self.config.uploaderId+'-progress-bar').css({width:'100%'});
				$('#'+self.config.uploaderId+'-progress').hide();
				
				document.getElementById(iframeid).contentWindow.stop();
				$('#'+iframeid).attr('src', 'javascript:false;');
				$('#'+iframeid).remove();
			});
		
		$('#'+this.config.uploaderId).attr('target', iframeid);
		$('#'+this.config.uploaderId).trigger('submit');

		this.emit('start',{name:filename});
	};
	
	this.uploadComplete = function(info) {
		if(!this.config.hideUploaded) {
			$('#'+this.config.uploaderId+'-files').append($('<div onclick="uploader.removeUploadedFile(\''+info.url+'\')"><i class="icon-remove"></i> '+info.name+'</div>'));
		}
		$('#'+this.config.uploaderId+' span.fileupload-preview').html('');
		$('#'+this.config.uploaderId+' span.fileupload-preview-title').hide();
		$('#'+this.config.uploaderId+' a.close').hide();
		$('#'+this.config.uploaderId+'-progress-bar').css({width:0});
		$('#'+this.config.uploaderId+'-progress').hide();
		if(typeof(info.error) != 'undefined' && info.error) {
			if(info.code == 145 || info.code == 146) {
				var dimension = (info.code == 145)? 'Ширина' : 'Высота'; 
				this.container.append('<div class="alert alert-danger">Изображение слишком большое. '+dimension+' загружаемого изображения '+info.size+'px, Допустамая '+dimension+': '+info.limit+'px</div>');
			} else {
				this.container.append('<div class="alert alert-danger">'+info.message+'</div>');
			}
		}
		this.uploadFiles.push(info);
		this.emit('done',info);
	};
	
	this.completeHandler = function(info) {
		
	};
};

var uploader = new UploaderClass();


/*
* jquery.uploadProgress
*
* Copyright (c) 2008 Piotr Sarnacki (drogomir.com)
*
* Licensed under the MIT license:
* http://www.opensource.org/licenses/mit-license.php
*
*/
(function($) {
  $.fn.uploadProgress = function(options) {
  options = $.extend({
    dataType: "json",
    interval: 2000,
    progressBar: "#progressbar",
    progressUrl: "/progress",
    start: function() {},
    uploading: function() {},
    complete: function() {},
    success: function() {},
    error: function() {},
    preloadImages: [],
    uploadProgressPath: './',
    jqueryPath: './',
    timer: ""
  }, options);
  
  $(function() {
    //preload images
    for(var i = 0; i<options.preloadImages.length; i++)
    {
     options.preloadImages[i] = $("<img>").attr("src", options.preloadImages[i]);
    }
    /* tried to add iframe after submit (to not always load it) but it won't work.
    safari can't get scripts properly while submitting files */
    if($.browser.safari && top.document == document) {
      /* iframe to send ajax requests in safari
       thanks to Michele Finotto for idea */
      iframe = document.createElement('iframe');
      iframe.name = "progressFrame";
      $(iframe).css({width: '0', height: '0', position: 'absolute', top: '-3000px'});
      document.body.appendChild(iframe);
      
      var d = iframe.contentWindow.document;
      d.open();
      /* weird - safari won't load scripts without this lines... */
      d.write('<html><head></head><body></body></html>');
      d.close();
      
      var b = d.body;
      var s = d.createElement('script');
      s.src = options.jqueryPath;
      /* must be sure that jquery is loaded */
      s.onload = function() {
        var s1 = d.createElement('script');
        s1.src = options.uploadProgressPath;
        b.appendChild(s1);
      }
      b.appendChild(s);
    }
  });
  
  return this.each(function(){	
    $(this).bind('submit', function() {
      var uuid = "";
      for (i = 0; i < 32; i++) { uuid += Math.floor(Math.random() * 16).toString(16); }
      
      /* update uuid */
      options.uuid = uuid;
      /* start callback */
      options.start();
 
      /* patch the form-action tag to include the progress-id if X-Progress-ID has been already added just replace it */
      if(old_id = /X-Progress-ID=([^&]+)/.exec($(this).attr("action"))) {
        var action = $(this).attr("action").replace(old_id[1], uuid);
        $(this).attr("action", action);
      } else {
    	 
       var action = jQuery(this).attr("action");
       var action_sep = (action.lastIndexOf("?") != -1) ? "&": "?";
       $(this).attr("action", action + action_sep + "X-Progress-ID=" + uuid);
      }
      var uploadProgress = $.browser.safari ? progressFrame.jQuery.uploadProgress : jQuery.uploadProgress;
      options.timer = window.setInterval(function() { uploadProgress(this, options) }, options.interval);
      $(this).data('timer', options.timer);
    });
  });
  };
 
jQuery.uploadProgress = function(e, options) {
  jQuery.ajax({
    type: "GET",
    url: options.progressUrl + "?X-Progress-ID=" + options.uuid,
    dataType: options.dataType,
  }).done(function(upload) {
      if (upload) {
        switch (upload.state) {
	       case 'uploading':
	            upload.percents = Math.floor((upload.received / upload.size)*1000)/10;

	            var bar = $.browser.safari ? $(options.progressBar, parent.document) : $(options.progressBar);
	            bar.css({width: upload.percents+'%'});
	            options.uploading(upload);
	       break;
	      
	       case 'done':
	          
	          upload.percents = '100';
	          options.uploading(upload);
	          
	          window.clearInterval(options.timer);
	          
	          var bar = $.browser.safari ? $(options.progressBar, parent.document) : $(options.progressBar);
	          bar.css({width: upload.percents+'%'});
	          options.success(upload);
	       break;
	          
	       case 'error':
	          window.clearInterval(options.timer);
	          options.complete(upload);
	       break;
    }
      
        if (upload.state == 'error') {
          options.error(upload);
        }
      } else {
        // Null/false/empty response, assume we're out of process
        options.success(upload);
      }
  });
};
 
})(jQuery);


