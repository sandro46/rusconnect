var MAJAX = new (function() {
	this.fromGrid = false;
	this.ds='';
	this.uri='';
	this.token='';
	this.CALL = function(o,m,a,e) {
		this.uri = window.location.pathname+window.location.search+window.location.hash;
		var fg = this.fromGrid;
		this.fromGrid = false;
		var r = this.gs(), cb=false, rq=new Object(); 
		r.open('POST',this.uri,true);
		r.setRequestHeader("Method","POST "+this.uri+" HTTP/1.0");
		r.setRequestHeader("Content-Type","application/json; charset=utf-8");
		r.setRequestHeader("X-Requested-With", "MAJAX");
		r.setRequestHeader("From",'MAJAX:'+o+':'+m+':'+this.uri);
		r.setRequestHeader("X-Majax-Token",this.token);
		//r.setRequestHeader("Node-sid",document.cookie.match(/node\-sid\=(\w+)\;/)[1]);
		
		rq={o:o,m:m,a:new Array()};
		if(typeof(e)=='object') {
			rq.e = e;
		}
	
		for(var q = 0; q < a.length; q++) {
			if(typeof(a[q])=='function') {
				cb=a[q];
			} else {
				rq.a.push(this.fs(a[q]));
			}
		}
		
		if(fg) rq.fg = 1;
		var sf=this;
		r.onreadystatechange=function(){ 
			if(r.readyState!=4) {
				return null;
			};
			re = r.responseText.replace(/^\s*|\s*$/g,"");
			if(re.substr(0,1)!='{'||re.substr(re.length-1,1)!='}') { 
				sf.er(re); 
				sf.er('Server error. Error code: 12');
				return;
			};
			
			re=JSON.parse(re);
			if(typeof(re)!='object') { 
				return sf.er('Server error. Error code: 13');
			};
			
			if(re.token) {
				sf.token = re.token;
			}
			
			if(re.status=='error') {
				if(re.code == 1002) {
					sf.er('session expired! Show iframe login form.');
					if(typeof(re.error_num) === 'number') {
						if(re.error_num == 1002) {						
							$('body').hide();
							$('body').after('<body><iframe id="ajaxIframeLogin" style="width:100%; height:100%; position:absolute; border-width:0px;" src="/?majax:true"></iframe></body>');
							return false;
						}
					}
				}
				
				if(re.code == 1874 || re.code == 1875) {
					
				}
				
				return sf.er(re.message+' Error code: '+re.code);
			};
			if(typeof(cb)=='function') {
				if(fg) {
					cb(a[0],re);
				} else {
					cb(re.data);
				}
				
			};
		};
		r.send(JSON.stringify(rq));
	};
	this.gs=function(){
		var a;
		var b=new Array('Msxml2.XMLHTTP.5.0','Msxml2.XMLHTTP.4.0','Msxml2.XMLHTTP.3.0','Msxml2.XMLHTTP','Microsoft.XMLHTTP');
		var c='function';
		var d='undefined';
		var f=null;
		for(var i=0;i<b.length;i++){
			try{
				if(typeof(ActiveXObject)!=c){
					continue;
				}else{
					a=new ActiveXObject(b[i]);
				}
			}catch(e){a=f;}
		}
		if(!a&&typeof(XMLHttpRequest)!=d) a=new XMLHttpRequest();
		if(!a)this.er("Could not create connection object.",'stop');
		return a;
	};
	this.ar=function(){ $("#ajaxIframeLogin").remove(); $('body').show(); if(typeof($page) != 'undefined' && $page.locked === true) $page.unlock(); };
	this.fs=function(v){
		if(typeof(v)=='function') return;
		if(typeof(v)!='object'&&typeof(v)!='array'){
			return escape(v);
		}
		return v;
	}
	this.er=function(e){if(typeof(console)!='undefined'&&typeof(console.log)!='undefined') { console.log(e)}  else { alert(e)} };
})();