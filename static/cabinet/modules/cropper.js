function cropper() {
	this.configDefault = {
		image : '',
		resize : false,
		trigger : false,
		done : false,
		cancel : false,
		imageid : false,
		showresult : false,
		api : '/ru/files/crop/',
		caption : {
			save : 'Сохранить',
			cancel : 'Отмена',
			rotate : 'Повернуть',
			mirror : 'Отразить',
			header : 'Редактор изображения'
		}
	};
	this.containerId = false;
	this.config = {};
	this.container = false;
	this.tpl = '';
	
	this.getRandomId = function() {
		var id = "cropper-";
		var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		for( var i=0; i < 12; i++ )
			id += possible.charAt(Math.floor(Math.random() * possible.length));
		return id;
	};
	
	this.init = function(config){
		this.config = $.extend({}, this.configDefault, config);
		this.containerId = this.getRandomId();
		if(typeof(this.config.trigger) == 'object' && $(this.config.trigger).length) {
			var self = this;
			$(this.config.trigger).bind('click', function(){
				self.open();
			});
		} 
	};
	
	this.open = function(){
		this.getContainer().modal('show');
	};
	
	this.getContainer = function() {
		if(this.container) return this.container;
		
		var tpl = $('<div id="'+this.containerId+'" class="mycropper-container modal hide fade" role="dialog"  aria-hidden="true" style="width:650px">'+
						'<div class="modal-header">'+
		    				'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
		    				'<h3>'+this.config.caption.header+'</h3>'+
	    				'</div>'+
	    				'<div class="modal-body">'+
							'<div class="img-container" style="margin-bottom: 24px; width:620px; height:330px"><img src="'+this.config.image+'"></div>'+
							'<div class="btn-group" name="editor-menu">'+
								'<button class="btn" name="reset"><i class="icon-screenshot"></i></button>'+
								'<button class="btn" name="rotate-l"><i class="icon-undo"></i></button>'+
								'<button class="btn" name="rotate-r"><i class="icon-repeat"></i></button>'+
								'<button class="btn" name="zoomin"><i class="icon-zoom-in"></i></button>'+
								'<button class="btn" name="zoomout"><i class="icon-zoom-out"></i></button>'+
								'<button class="btn" name="fix"><i class="icon-link"></i></button>'+
							'</div>'+
							//'<div style="display:inline-block; margin-left:25px;">Повернуть на угол &deg;</div>'+
							//'<div style="width:100px; display:inline-block;" class="input-append"><input style="text-align: right; position: relative; top: 3px; margin-left: 9px; width: 41px;" type="text" value="0" name="rotate-custom"><span class="btn" style="position:relative; top:3px;"><i class="icon-ok"></i></span></div>'+
						'</div>'+
			    		'<div class="modal-footer">'+
			    			'<button class="btn btn-cancel" data-dismiss="modal" aria-hidden="true" name="cancel">'+this.config.caption.cancel+'</button>'+
			    			'<button class="btn btn-success" data-dismiss="modal" name="save">'+this.config.caption.save+'</button>'+
			    		'</div>'+
			    	 '</div>');
		
		this.container = tpl;
		this.container.img = tpl.find('.img-container > img').cropper({
			strict : false,
			autoCropArea : 0.8,
			minContainerWidth : 620,
			minContainerHeight : 330,
		});
		
		var self = this;
						
		tpl.find('button[name="save"]').click(function(event) {	
			self.process(self.container.img.cropper('getData'), function(){
				self.container.img.cropper('reset');
				self.container.modal('hide');
			});
			
			return false;
		});
		
		tpl.find('button[name="rotate-l"]').click(function(event) {
			self.container.img.cropper('rotate', -45);
		});
		
		tpl.find('button[name="rotate-r"]').click(function(event) {
			self.container.img.cropper('rotate', 45);
		});
		
		tpl.find('button[name="reset"]').click(function(event) {
			self.container.img.cropper('reset');
		});
		
		tpl.find('button[name="zoomin"]').click(function(event) {
			self.container.img.cropper('zoom', 0.1);
		});
		
		tpl.find('button[name="zoomout"]').click(function(event) {
			self.container.img.cropper('zoom', -0.1);
		});
		
		tpl.find('button[name="fix"]').click(function(event) {
			if($(this).attr('on')) {
				self.container.img.cropper('setAspectRatio', NaN);
				$(this).removeAttr('on');
				$(this).removeClass('disabled');
			} else {
				self.container.img.cropper('setAspectRatio', 1/1);
				$(this).attr('on', 1);
				$(this).addClass('disabled');
			}
		});
			
		this.container.modal();
		
		return this.container;
	};
	
	this.process = function(data, callback) {
		data.image = this.config.image;
		data.resize = this.config.resize;
		$page.lock();
		var self = this;
		$.ajax({
			url: this.config.api,
			type : 'POST',
			data : data,
			dataType: "json"
		}).done(function(data){
			if(typeof(callback) == 'function') {
				if(self.config.showresult) {
					$page.alert('Hello', '<img src="'+data.fname+'?t='+(new Date()).getTime()+'">');
				}
				
				callback(data);
				
				if(typeof(self.config.done) == 'function') {
					self.config.done(data);
				}	
				
				$page.unlock();
			}
		});
	};
	
}
