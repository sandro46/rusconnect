var nodechat = (function(){
	var self = this;
	self.connected = false;
	self.socket = null;
	self.username = '';
	self.userid = 0;
	self.ready = false;
	self.message = false;
	self.iourl = false;
	
	self.setSocket = function(socket) {
		this.connected = true;
		this.socket = socket;
	};
	
	self.init = function(options){
		if(typeof(options.iourl) != 'undefined') self.iourl = options.iourl;
		if(typeof(options.username) != 'undefined') self.username = options.username;
		if(typeof(options.userid) != 'undefined') self.userid = options.userid;
		if(typeof(options.ready) == 'function') self.ready = options.ready;
		if(typeof(options.message) == 'function') self.message = options.message;
		
		if(!self.iourl) return false;
		
		if (navigator.userAgent.toLowerCase().indexOf('chrome') != -1) {
	        socket = io.connect(self.iourl, {'transports': ['xhr-polling']});
	    } else {
	        socket = io.connect(self.iourl);
	    }
		
		socket.on('session_strarted', function(data) {
			self.setSocket(socket);
			if(typeof(data) == 'object' && typeof(data.messages) == 'object' && data.messages.length > 0) {
				var data = data.messages;
				for(var i in data) {
					self.reciveMessage(data[i]);
				}
			}
		});
		
		socket.on('new_message', function(data) {
			self.reciveMessage(data);
		});
		
		socket.on('message_send_ok', function(timestamp) {
			/* delayed send */
		});
	};
	
	self.reciveMessage = function(data) {
		if(typeof(self.message) == 'function') {
			self.message(data);
		}
	};
	
	self.send = function(message, returnMessage) {
		var data = message;
		data.ret = (returnMessage === true)? true : false;
		self.socket.emit('message', data);
	}
	
	self.makeText = function(text) {
		var text = self.htmlEncode(text);
		text = text.replace(/\n/g,'<br>');
		return text;
	};
	
	self.htmlEncode = function(value) {
		 return $('<div/>').text(value).html();
	};
	
	self.htmlDecode = function(value) {
		return $('<div/>').html(value).text();
	};
});