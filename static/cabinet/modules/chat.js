
var nodechat = (function(){
	
	var self = this;
	
	self.chatOpened = false;
	self.opened = 0;
	self.connected = false;
	self._socket = null;
	self._readTimeout = false;
	self._myInfo = {};
	self._uploadfileprogress = false;
	self._uploadFileCounter = 0;
	self._filesUploaded = [];
	
	self.setSocket = function(socket) {
		self.connected = true;
		self._socket = socket;
	};
	
	self.openUploadFile = function(obj) {
		var url = $(obj).attr('data-url');
		var mime = $(obj).attr('data-mime');
		var name = $(obj).html();
		var imageMime = ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/tiff'];
		
		if(imageMime.indexOf(mime) !== -1) {
			$.fancybox({
				'padding'		: 0,
				'href'			: url,
				'title'   		: name,
				'transitionIn'	: 'elastic',
				'transitionOut'	: 'elastic'
			});			
		} else {
			$('<iframe src="'+url+'" style="display:none" download></iframe>').appendTo(document.body);
		}
	};
	
	self.startUpload = function(obj){
		$('#nodechat-UploadProgress').show();
		$('#nodechat-UploadFiles').show();
		$('#nodechat-UploadProgress-bar').css({width:0});
		
		var filename = $(obj).val();
		var iframeid = 'nodechat-upload-iframe-' +(self._uploadFileCounter += 1);
		$('<iframe src="javascript:false;" style="display:none" name="'+iframeid+'" id="'+iframeid+'"></iframe>').appendTo(document.body);
		
		$('#nodechat-upload span.fileupload-preview').html(filename);
		$('#nodechat-upload a.close')
			.css({display:'inline-table'})
			.unbind('click')
			.bind('click',function(){
				var interval = $('#nodechat-upload').uploadProgress().data('timer');
				clearInterval(interval);
				$('#nodechat-upload span.fileupload-preview').html('');
				$('#nodechat-upload a.close').hide();
				$('#nodechat-UploadProgress-bar').css({width:'100%'});
				$('#nodechat-UploadProgress').hide();
				
				document.getElementById(iframeid).contentWindow.stop();
				$('#'+iframeid).attr('src', 'javascript:false;');
				$('#'+iframeid).remove();
				
				console.log('stop upload');
			});
		
		$('#nodechat-upload').attr('target', iframeid);
		$('#nodechat-upload').trigger('submit');
	};
	
	self.uploadComplete = function(info) {		
		$('#nodechat-UploadFiles').append($('<div onclick="nodechat.removeUploadedFile(\''+info.url+'\')"><i class="icon-remove"></i> '+info.name+'</div>'));
		$('#nodechat-upload span.fileupload-preview').html('');
		$('#nodechat-upload a.close').hide();
		$('#nodechat-UploadProgress-bar').css({width:0});
		$('#nodechat-UploadProgress').hide();
		
		self._filesUploaded.push(info);
	};
	
	self.init = function(){
		$page.init({
			'/' : {
				name: 'Чат',
				always_reload: true,
				delete_unload: true,
				on : nodechat.loadChatMainWindow
			}
		});
	};
	
	self.loadChatMainWindow = function() {
		$page.lock();
		
		$page.show(['chat.full.html', 'nodechat'], false, function(current){
			
			$page.unlock();
			/*
			 uploader = $page.current.find('#nodechat-upload').uploadProgress({
				jqueryPath: window['static']+'js/jquery-1.8.3.min.js',
				uploadProgressPath: window['static']+"modules/uploader.js",
				uploading: function(upload) {
					$('#nodechat-UploadProgress span').html(upload.percents+'%');
				},
				progressBar: "#nodechat-UploadProgress-bar",
				progressUrl: "/progress",
				interval: 200
			});*/
			
			
			
			self.chatOpened = true;
			$page.unlock();
			var startOpen = parseInt($page.cookie.get('nodechat_opened_room'));
			
			self._myInfo = {
					id:$('#auth_user_info_block').data('uid'),
					name:$('#auth_user_info_block').data('name')
			};
			
			if(isNaN(startOpen) || ! startOpen) {
				var _users = $('#nodechat-usersList li[data-exists=1]');
				if(!_users.length) {
					startOpen = false;
				} else {
					self.open($('#nodechat-usersList li[data-exists=1]').eq(0));
				}
			} else {
				self.open($('#nodechat-usersList li[data-id='+startOpen+']'));
			}					
		});
	};
	
	self.makeReaded = function(uid) {
		var readedMessages = $('#header_inbox_bar').find('li[data-fromuid='+uid+']');
		var oldCount = parseInt($('#new_messages_counter').html());
		var readedCount = $('#nodechat-usersList').find('li[data-id='+uid+']').find('i');
		
		if(!readedCount.length) {
			return;
		} else {
			readedCount = (readedCount.html().toString()+'').replace('(','').replace(')','');
			readedCount = parseInt(readedCount);
			readedCount = (!readedCount || isNaN(readedCount))? 0 : readedCount;
			
			if(!readedCount) return;
		}

		if(isNaN(oldCount)) oldCount = 0;
		
		self._socket.emit('makeReaded', uid);
		var newCount = oldCount - readedCount;
		
		if(!newCount) {
			$('#new_messages_counter').html('0');
			$('#new_messages_counter').hide();
			$('#chatInboxCounter').html('У вас нет новых сообщений.');
			
		} else {
			$('#new_messages_counter').html(newCount);
			$('#new_messages_counter').show();
			$('#chatInboxCounter').html('У вас '+(newCount).toString()+' '+plural_str(newCount, 'новое','новых','новых')+' '+plural_str(newCount, 'сообщение','сообщения','сообщений'));		
		}
		
		readedMessages.remove();
		
		if(self.chatOpened) {
			$('#nodechat-usersList').find('li[data-id='+uid+']')
			.removeClass('new-msg')
			.removeClass('opened-dialog')
			.addClass('opened-dialog')
			.find('i')
			.remove();
			
		}
	};
	
	self.open = function(obj){
		var chatRoomId = $(obj).attr('data-id');
		var userRoomName = $("#nodechat-usersList").find('li[data-id='+chatRoomId+']').attr('data-name');
		
		self.opened = {
				name:userRoomName,
				id:chatRoomId
		}
		
		self._filesUploaded = [];
		
		$page.cookie.erase('nodechat_opened_room');
		$page.cookie.create('nodechat_opened_room', chatRoomId)

		$('#nodechat-headerTitleUser').html('<i class="icon-reorder"></i>'+userRoomName);
		$("#nodechat-usersList").find('li.active').removeClass('active');
		$("#nodechat-usersList").find('li[data-id='+chatRoomId+']').addClass('active');
		$('#nodechat-UploadFiles').html('');

		clearTimeout(self._readTimeout);
		
		self._readTimeout = setTimeout(function(){
			self.makeReaded(chatRoomId);
		}, 2000);
		
		$('#nodechat-chatRommContainer').find('ul.chats').hide();
		
		var room = $('#nodechat-chatRommContainer').find('ul[data-user_to='+chatRoomId+']');
		if(!room.length) {
			var tpl = new templates('nodechat-chatRoomClearTpl');
				tpl.assign('to_user', chatRoomId).render();
			
			$('#nodechat-chatRommContainer').prepend(tpl.out);
			$('#nodechat-InputForm').find('textarea').val('');
			$('#nodechat-InputForm').show();
		} else {
			room.show();
			$('#nodechat-InputForm').find('textarea').val('');
			$('#nodechat-InputForm').show();
			room.scrollTop(room[0].scrollHeight);
		}
		
		
	};
	
	self.makeText = function(text) {
		var text = htmlEncode(text);
			text = text.replace(/\n/g,'<br>');
		
		return text;
	};
	
	self.send = function(msg) {
		var message = {};

		if(!msg || typeof(msg) == 'undefined') {
			message = {
					from_name : self._myInfo.name,
					to:self.opened.id,
					text:self.makeText($('#nodechat-InputForm').find('textarea').val()),
					timestamp:(new Date()).getTime(),
			};
		
			if(!message.text || !message.text.length) {
				return;
			}
			
			// store message to cookie for autosend after reopen browser
			var notSended = $page.cookie.get('nodechat_notSended');
			if(typeof(notSended) != 'undefined' && notSended && notSended.length > 0) {
				notSended = JSON.parse(notSended);
				if(typeof(notSended) != 'object') {
					notSended = {};
				}
			} else {
				notSended = {};
			}
					
			notSended[message.timestamp] = message;
			$page.cookie.erase('nodechat_not_sended');
			$page.cookie.create('nodechat_not_sended', JSON.stringify(notSended), 2*24*60*60);
		
		} else {
			message = msg;
		}
		
		if(!self.connected) {
			console.log('not connected!!!!!');
		} else {			
			$('#nodechat-InputForm').find('textarea').val('');
			
			var text = message.text;
			var timestamp = new Date();
			var tpl = new templates('nodechat-chatMessageClearTpl');
				tpl.assign('message_type', 'out')
					.assign('username', self._myInfo.name)
					.assign('timestamp', timestamp.format('d.m.Y H:i:s'))
					.assign('text', text)
					.render();
				
			$('#nodechat-chatRommContainer').find('ul[data-user_to='+message.to+']').append(tpl.out);	
			$('#nodechat-chatRommContainer').find('ul[data-user_to='+message.to+']').scrollTop($('#nodechat-chatRommContainer').find('ul[data-user_to='+message.to+']')[0].scrollHeight);
			self._socket.emit('message', message);
		}
		
		$('#nodechat-UploadFiles').html('');
		self._filesUploaded = [];
	};
	
	self.reciveMessage = function(message) {
		//console.log(message);
		var timestamp = new Date();	
			message.fromId = (message.type == 1)? message.user_id : message.contact_id;
		
		$page.sticky('Новое сообщение', 'От кого: '+message.from_username+'<br>'+message.text);
		
		if(self.chatOpened && self.opened.id == message.from) {
			self.makeReaded(message.from);
		} else {
			var oldCount = parseInt($('#new_messages_counter').html());
			if(isNaN(oldCount)) oldCount = 0;
			var newCount = oldCount + 1;
			
			$('#new_messages_counter').html(newCount);
			$('#new_messages_counter').show();
			$('#chatInboxCounter').html('У вас '+(newCount).toString()+' '+plural_str(newCount, 'новое','новых','новых')+' '+plural_str(newCount, 'сообщение','сообщения','сообщений'));		

			if(newCount < 4) {
				var tpl = new templates('chatNewMessageTpl');
				
				tpl.assign('fromuid', message.fromId);
				tpl.assign('msgid', message.id);
				tpl.assign('from', message.from_username_full);
				tpl.assign('time', dateAgo(message.timestamp));
				tpl.assign('text', message.text);
				tpl.render();
				
				$('#chatInboxShowAll').before(tpl.out);
			}
			
			if(self.chatOpened) {
				$('#nodechat-usersList').find('li[data-id='+message.fromId+']')
					.removeClass('new-msg')
					.removeClass('opened-dialog')
					.addClass('opened-dialog')
					.addClass('new-msg');
									
				$('#nodechat-usersList').find('li[data-id='+message.fromId+']').find('i').remove();
				$('#nodechat-usersList').find('li[data-id='+message.fromId+']').append('<i>('+newCount+')</i>');
			}
		}
		
		if(self.chatOpened) {
			var isNewDialog = false;	
			
			var tpl = new templates('nodechat-chatMessageClearTpl');
			tpl.assign('message_type', 'in')
				.assign('username', message.from_username_full)
				.assign('timestamp', timestamp.format('d.m.Y H:i:s'))
				.assign('text', message.text)
				.render();
			
			if(!$('#nodechat-chatRommContainer').find('ul[data-user_to='+message.fromId+']').length) {				
				var tplchat = new templates('nodechat-chatRoomClearTpl');
				tplchat.assign('to_user', message.from).assign('room_name', message.from_username_full).render();
			
				$('#nodechat-chatRommContainer').prepend(tplchat.out);
				$('#nodechat-InputForm').find('textarea').val('');
				$('#nodechat-chatRommContainer').find('ul[data-user_to='+message.fromId+']').hide();
				
				isNewDialog = true;
			}
			
			$('#nodechat-chatRommContainer').find('ul[data-user_to='+message.fromId+']').append(tpl.out);	
			if(!isNewDialog) {
				$('#nodechat-chatRommContainer').find('ul[data-user_to='+message.fromId+']').scrollTop($('#nodechat-chatRommContainer').find('ul[data-user_to='+message.fromId+']')[0].scrollHeight);
			}
			
			
		}
	}
	
	return this;
})();

$(document).ready(function(){
	if(typeof(io) == 'undefined') return false;
	
	var chatUrl, socket;
	
	chatUrl = getChatUrl();
	
	if(!chatUrl) {
		console.log('Chat url not found!');
		return false;
	}
		
	if (navigator.userAgent.toLowerCase().indexOf('chrome') != -1) {
        socket = io.connect('http://'+chatUrl, {'transports': ['xhr-polling']});
    } else {
        socket = io.connect('http://'+chatUrl);
    }
	
	socket.on('session_strarted', function(data) {
		//console.log('session start data', data);
		$("#new_messages_counter").html(data.new_count);
		
		if(data.new_count > 0) {
			$('#new_messages_counter').show();
		}
		
		nodechat.setSocket(socket);
		
		if(data.messages.length) {
			for(var i in data.messages) {
				var tpl = new templates('chatNewMessageTpl');
				var message = data.messages[i];
				
				tpl.assign('fromuid', (message.type == 1)? message.user_id : message.contact_id);
				tpl.assign('msgid', message.id);
				tpl.assign('from', message.from_username);
				tpl.assign('time', dateAgo(message.timestamp));
				tpl.assign('text', message.text);
				tpl.render();
				
				$('#chatInboxShowAll').before(tpl.out);
			}
			
			
			$('#chatInboxCounter').html('У вас '+(data.new_count).toString()+' '+plural_str(data.new_count, 'новое','новых','новых')+' '+plural_str(data.new_count, 'сообщение','сообщения','сообщений'));
		} else {
			$('#new_messages_counter').html('0');
			$('#new_messages_counter').hide();
			$('#chatInboxCounter').html('У вас нет новых сообщений.');
		}
		
		$('#header_inbox_bar').show();

		// send not sended messages
		var notSended = $page.cookie.get('nodechat_notSended');
		if(typeof(notSended) != 'undefined' && notSended && notSended.length) {
			notSended = JSON.parse(notSended);
			if(typeof(notSended) == 'object') {
				//console.log('not sended: ');
				//console.log(notSended);
				
				for(var i in notSended) {
					self.send(notSended[i]);
				}
			}
		} 
		
	});
	
	
	socket.on('new_message', function(data) {
		nodechat.reciveMessage(data);
	});
	
	socket.on('message_send_ok', function(timestamp) {
		var notSended = $page.cookie.get('nodechat_notSended');
		
		if(typeof(notSended) != 'undefined' && notSended && notSended.length) {
			notSended = JSON.parse(notSended);
			if(typeof(notSended) == 'object') {
				if(typeof(notSended[timestamp]) != 'undefined') {
					delete notSended[timestamp];
					$page.cookie.erase('nodechat_not_sended');
					$page.cookie.create('nodechat_not_sended', JSON.stringify(notSended), 2*24*60*60);
				}
			}
		}	
	});
});

function plural_str(i, str1, str2, str3){
	if(typeof(str1) == 'object' || typeof(str1) == 'array') {
		str2 = str1[1];
		str3 = str1[2];
		str1 = str1[0];
	}
	
	function plural (a){
		if ( a % 10 == 1 && a % 100 != 11 ) return 0
		else if ( a % 10 >= 2 && a % 10 <= 4 && ( a % 100 < 10 || a % 100 >= 20)) return 1
		else return 2;
	}

	switch (plural(i)) {
		case 0: return str1;
		case 1: return str2;
		default: return str3;
	}
}

function getChatUrl() {
	var rexp = /\/\/([a-zA-Z\.\:0-9\-]{0,})\//;
	var url = $('#socketio').attr('src');
		url = rexp.exec(url);
		
	if(typeof(url) == 'object' && typeof(url[1]) != 'undefined') {
		return url[1];
	} 
	
	return false;
}

function dateAgo(time) {
	var date = new Date(time*1000);
	var dateNow = new Date();
	
	var dayNow = dateNow.getDate(),
		timeDay = date.getDate(),
		timeMonth = date.getMonth()+1,
		timeYear = date.getFullYear(),
		NowMonth = dateNow.getMonth()+1,
		NowYear = dateNow.getFullYear(),
		secondsAgo = Math.ceil((dateNow.getTime() - date.getTime()) / 1000),
		minust = ['Минуту','Минуты', 'Минут'],
		hours = ['Час','Часа', 'Часов'],
		seconds = ['Секунду','Секунды', 'Секунд'],
		month = ['Месяц','Месяца', 'Месяцев'],
		agoText = 'назад',
		todayText = 'Сегодня в',
		tomorow = 'Вчера в';

	//редактирование было вчера
	if(timeDay == dayNow-1 && timeMonth == NowMonth && timeYear == NowYear) {
		return tomorow+' '+date.format('H:i');
	}
	
	
	if(dayNow == timeDay && timeMonth == NowMonth && timeYear == NowYear) {
		// редактирование было сегодня 
		
		if(secondsAgo == 60) {
			// минуту назад
			return minust[0]+' '+agoText;
		}
		
		if(secondsAgo > 60) {
			// больше минуты назад
			var text = '';
			
			if(secondsAgo > 3600) {
				// больше часа назад
				
				
				/*
				// целые часы
				hourAgo = Math.ceil(secondsAgo/3600);
				// целые минуты
				minustAgo = Math.ceil((secondsAgo%3600)/60);
				
				text += hourAgo+' ';
				text += plural_str(hourAgo, hours);
				text += ' '+minustAgo+' ';
				text += plural_str(minustAgo, minust);
				text += ' '+agoText;
				*/
				text += todayText+' '+date.format('H:i');
				
				return text;
			}
			
			if(secondsAgo > 45 * 60 && secondsAgo < 70 * 60) {
				text += hours[0];
				text += ' '+agoText;
				
				return text;
			}
			
			if(secondsAgo > 9 * 60) {
				minustAgo = Math.ceil(secondsAgo / 60);
				text += minustAgo+' ';
				text += plural_str(minustAgo, minust);
				text += ' '+agoText;
				
				return text;
			} 
			
			minustAgo = Math.ceil(secondsAgo / 60);
			secondsA = Math.ceil(secondsAgo % 60);
			
			text += minustAgo+' ';
			text += plural_str(minustAgo, minust);
			text += ' '+secondsA+' ';
			text += plural_str(secondsA, seconds);
			text += ' '+agoText;
				
			return text;
		}
		
		if(secondsAgo < 60) {
			// меньше минуты назад
			text = secondsAgo+' '+plural_str(secondsAgo, seconds)+' '+agoText;
			
			return text;
		}	
	}
	
	if(dayNow == timeDay && timeMonth < NowMonth && timeYear == NowYear) {
		var MonthCount = Math.ceil(NowMonth) - Math.ceil(timeMonth);
		var MonthCountText = (MonthCount == 1)? '': MonthCount+' ';
		
		text = MonthCountText+plural_str(MonthCount, month)+' '+agoText;
		
		return text;
	}
	return date.toString();
	//return date.format("d.m.Y H:i");	
}

function htmlEncode(value){
	  //create a in-memory div, set it's inner text(which jQuery automatically encodes)
	  //then grab the encoded contents back out.  The div never exists on the page.
	  return $('<div/>').text(value).html();
}

function htmlDecode(value){
	  return $('<div/>').html(value).text();
}

	Date.prototype.format = function(format) //author: meizz
{
  var o = {
    "m+" : this.getMonth()+1, //month
    "d+" : this.getDate(),    //day
    "H+" : this.getHours(),   //hour
    "i+" : this.getMinutes(), //minute
    "s+" : this.getSeconds(), //second
    "Y+" : this.getFullYear()
  }

  //if(/(Y+)/.test(format)) format=format.replace(RegExp.$1,
  //d  (this.getFullYear()+"").substr(4 - RegExp.$1.length));
  for(var k in o)if(new RegExp("("+ k +")").test(format))
    format = format.replace(RegExp.$1,
      RegExp.$1.length==1 ? o[k] :
        ("00"+ o[k]).substr((""+ o[k]).length));
  return format;
}