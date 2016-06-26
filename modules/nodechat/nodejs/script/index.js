var sys = require('util'),
	memcache = require('memcache'),
	mysql = require('mysql'),
	io = require('socket.io').listen(8080), 
	dbconfig = {user:'rusconnect', password:'xZ2KsBYPV9UE7NZK', host:'localhost', database:'shop'};



io.set('log level', 1);

var chat =  (function() {
	var self = this;
	
	self.connections = {};
	
	self._myPinger = null; 
	self._mcpinger = null;
	self._readyState = 0;
		
	self.init = function() {
		self._memcacheConnect();
		self._mysqlConnect();
	};
	
	self.globalConnections = {};
	
	self.ready = function() {
		sys.log('Ready');
		io.sockets.on('connection', function (socket) {
			var user = {
					connection_id:(socket.id).toString(),
					time: (new Date).toLocaleTimeString(),
					session: self.parseCookie(socket.handshake.headers)['PHPSESSID'],
			};
			
			self.getUserInfo(user.session, function(data) {
				if(typeof(data) != 'object' || typeof(data['shopUserInfo']) != 'object') {
					sys.log('Client session not found. Disconnect...  sessionId: '+user.session);
				} else {
					user.info = data['shopUserInfo'];
					user.id = user.info.user_id;
					user.type = (typeof(user.info.crm_contact_id) != 'undefined')? 1 : 2;
					
					if(typeof(user.info) == 'object') {
						if(user.type == 1) {
							user.contact_id = user.info.contact.contact_id;
							user.manager = user.info.contact.responsible_user_id;
						} else {
							user.contact_id = 0;
							user.manager = 0;
						}
					}
					
					user.uniq = 'type:'+ user.type +';uid:' + ((user.type == 1)? user.contact_id : user.id);
					
					sys.log('New client connected. uniq: ['+user.uniq+'] userId: ['+user.id+'] userName: ['+user.info.email+'] contactId: ['+user.contact_id+'] managerId: ['+user.manager+']');
					
					if(typeof(self.connections[user.uniq]) != 'object') {
						self.connections[user.uniq] = {};
					}
					
					self.connections[user.uniq][user.connection_id] = {
							socket:socket,
							data:user,
							uniq:user.uniq
					};
					if(user.type == 1) {
						self.sendLastMessages(user, socket);
					} else {
						self.sendLastMessages(user, socket, true);
					}
					
					socket.on('message', function (msg) {
						if(typeof(msg.text) == 'undefined' || !msg.text.length) return false;
						msg.to = (user.id == 1)? user.manager : msg.to;
						sys.log('New message. from: ['+user.uniq+'] to: ['+msg.to+']');
						self._storeMessage(user, msg, function(msgId){
							self.getMessageById(msgId, function(msgdata){
								self._sendMessage(user, socket, msgdata);
							});
						});
						
						socket.emit('message_send_ok', msg.timestamp);
						//console.log('New message!!! -->');
						//console.log(msg);
					});
					
					socket.on('makeReaded', function (uid) {
						var sql = "UPDATE chat_messages SET readed = 1 WHERE ";
						
						if(user.type == 2) {
							sql += ' user_id = '+user.id+' AND contact_id = '+uid+' AND type = 2';
						} else if(user.type == 1) {
							sql += ' contact_id = '+uid+' AND type = 1 ';
						}
						
						self.db.query(sql, function(error) {
							if(error) {
								sys.log('Make readed error --> uid: '+uid+' error: '+error);
							}
						});
					});
					
					socket.on('disconnect', function() {
				        var time = (new Date).toLocaleTimeString();
				        var connectionId = user.info.connection_id;
				        var uniq = user.uniq;
				        
				        if(typeof(self.connections[uniq]) == 'object' && typeof(self.connections[uniq][connectionId]) == 'object') {
							delete self.connections[uniq][connectionId];
							if(!(Object.keys(self.connections[uniq])).length) {
								delete  self.connections[uniq];
							}
						}
				        console.log('Client disconnect'); 
				    });
				}				
			});			
		});
	};
		
	self.getMessageById = function(msgId, callback) {
		var sql = 'SELECT '+ 
						'c.id, '+ 
						'c.timestamp as timestamp, '+
						'c.text, '+
						'c.readed, '+
						'c.user_id, '+
						'c.contact_id, '+
						'c.type,'+
						'IF(c.type = 1, '+
							'(SELECT CONCAT(u.name_first,\' \', SUBSTRING(u.name_last, 1, 1),\'.\') as uname FROM a_users as u WHERE u.user_id = c.user_id),' +
							'(SELECT CONCAT(cc.name,\' \', SUBSTRING(cc.surname, 1, 1),\'.\') as uname FROM crm_contacts as cc WHERE cc.contact_id = c.contact_id) '+
						') as from_username, '+
						'IF(c.type = 1, '+
							'(SELECT CONCAT(u.name_last,\' \', u.name_first) as uname FROM a_users as u WHERE u.user_id = c.user_id),' +
							'(SELECT CONCAT(cc.surname,\' \', cc.name) as uname FROM crm_contacts as cc WHERE cc.contact_id = c.contact_id) '+
						') as from_username_full, '+
						'IF(c.type = 1, c.user_id, c.contact_id) as local_from_id '+
					'FROM '+
						'chat_messages as c '+
					'WHERE '+
						'c.id = ?';
		
		self.db.query(sql, [msgId], function(error, data) {
			if(!error) {
				data = (typeof(data) == 'object' && data.length > 0)? data[0] : false;
				callback(data); 
			} else {
				callback(false); 
			}
		});
	};
	
	self.sendLastMessages = function(user, socket, onlyNotReaded) {
		var sql = 'SELECT '+ 
						'c.id, '+ 
						'c.timestamp as timestamp, '+
						'c.text, '+
						'c.readed, '+
						'c.user_id, '+
						'c.contact_id, '+
						'c.type,'+
						'IF(c.type = 1, '+
							'(SELECT CONCAT(u.name_first,\' \', SUBSTRING(u.name_last, 1, 1),\'.\') as uname FROM a_users as u WHERE u.user_id = c.user_id),' +
							'(SELECT CONCAT(cc.name,\' \', SUBSTRING(cc.surname, 1, 1),\'.\') as uname FROM crm_contacts as cc WHERE cc.contact_id = c.contact_id) '+
						') as from_username, '+
						'(SELECT COUNT(m.id) FROM  chat_messages as m WHERE m.readed = 0 AND m.user_id = '+user.id+') as new_count '+
					'FROM chat_messages as c '+
					'WHERE ';

		if(user.type == 1) {
			sql += ' c.contact_id = ? ';
		} else {
			sql += ' c.user_id = ? ';
		}
		
		if(onlyNotReaded === true) {
			sql += ' AND c.readed = 0 AND c.type = 2 ';
		}
					
		sql += ' ORDER BY c.timestamp ASC ';
		
		if(user.type == 2) {
			sql += ' LIMIT 6 ';
		}
					
		self.db.query(sql, [user.id], function(error, data) {
			if(!error) {
				//console.log('get last messages - ok');
				var count = (onlyNotReaded === true)? data.length : false;
				if(count === false) {
					count = (data.length > 0)? data[0].new_count : 0;
				}
				socket.emit('session_strarted', {messages:data, new_count:count});
			} else {
				console.log('get last message error: '+error);
			}
		});
	};
	
	self._memcacheConnect = function() {
		self.mc = new memcache.Client(11211, '127.0.0.1');
		self.mc.on('connect', function(){
			self._readyState++;
			if(self._readyState == 2) {
				self.ready();
			}
		});
		
		self.mc.connect();
	};
	
	self._storeMessage = function(user, message, callback) {
		var sql = "INSERT INTO chat_messages (user_id, contact_id, type, timestamp, text) VALUES(?, ?, ?, ?, ?)";
		var date = new Date(); 
		var data = [];
		
		if(user.type == 1) {
			data.push(user.manager);
			data.push(user.contact_id);
			data.push(2);
		} else {
			data.push(user.id);
			data.push(message.to);
			data.push(1);
		}
		
		data.push(Math.ceil(date.getTime()/1000));
		data.push(message.text);
		
		self.db.query(sql, data, function(error, data) {			
			if(!error) {
				if(typeof(callback) == 'function') callback(data.insertId);
			} else {
				if(typeof(callback) == 'function') callback(false);
				console.log('Store error! message: '+error);
			}
		});
	};
	
	self._sendMessage = function(user, socket, message) {
		var uniq = '';
		if(user.type == 2 && message.type == 3) {
			uniq = 'type:3;uid:'+message.to_uid;
		} else if(user.type == 2 && message.type == 1) {
			uniq = 'type:1;uid:'+message.contact_id;
		} else if(user.type == 1 && message.type == 2) {
			uniq = 'type:2;uid:'+message.user_id;
		}
		
		sys.log('System send message: from uniq: ['+user.uniq+'] to uniq: ['+uniq+']');
		//console.log('local message uniq: ', uniq, user, message); return;
		
		message.type_message = 'in';
		if(typeof(self.connections[uniq]) == 'object') {
			for(var i in self.connections[uniq]) {
				self.connections[uniq][i].socket.emit('new_message', message);
			}
		}
	};
	
	self._mysqlConnect = function() {
		self.db = mysql.createConnection(dbconfig);
		self.db.query('USE `'+dbconfig.database+'`', function(error) {
			if(error != null) {
				sys.log('Server initialize fail. Error connect to mysql server. '+error+' settings: '+sys.inspect(dbconfig));
				setTimeout(function(){
					sys.log('Reconnect to mysql after 10sec.');
					self._mysqlConnect();
				}, 10000);
			} else {
				sys.log('Mysql connected!');
				
				self._readyState++;
				if(self._readyState == 2) {
					self.ready();
				}
				
				self._myPinger = setInterval(function(){
					self.db.query('SELECT 1', function(error){
						if(error != null) {
							sys.log('Mysql ping error!');
							clearInterval(self._myPinger);
							self._mysqlConnect();
						} else {
							sys.log('Mysql ping ok!');
						}
					});
				}, 10*60*1000);
				
			}
		});
	};
	
	self.getUserInfo = function(sessionId, callback){
		mc.get(sessionId, function(error, data){
			if(error || !data || !data.length) {
				//console.log('error get session info! error: '+error+' data: '+data);
				callback(false);
			} else {
				var parsedData = false;
				
				try {
					parsedData = JSON.parse(data);
					callback(parsedData);
				} catch(e) {
					console.log('GetInfo error! Not parsed result. Data: '+data+' error: '+e.message);
					callback(false);
				}
			}
		});
	};
	
	self.parseCookie = function(headers) {
		var list = {},
        rc = headers.cookie;

	    rc && rc.split(';').forEach(function( cookie ) {
	        var parts = cookie.split('=');
	        list[parts.shift().trim()] = unescape(parts.join('='));
	    });
	
	    return list;
	};
	
	self.init();
	
	return this;
})();
