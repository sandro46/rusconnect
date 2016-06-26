//
// This is a simple javascript template engine
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

var templates = function(templateName, isobj) {
	this.name = '';
	this.src= '';
	this.tplPattern=new Array();
	this.tplReplaces=new Array();
	this.out='';
	this.left = "{";
	this.right = "}";
	
	if(isobj === true) {
		this.name = 'default';
		this.src  = templateName;
	} else {
		this.name = templateName;
		this.src  = document.getElementById(templateName).innerHTML;
	}
	
	this.tplPattern = new Array();
	this.tplReplaces = new Array();
	this.out = '';
	this._data = {};
	
	this.assign = function(variable, source) {
		if(typeof(source) == 'object') {
			var sub = (new RegExp(this.left+variable+this.right+'(.*)'+this.left+'/'+variable+this.right, 'gim')).exec(this.src);
			
			if(typeof(sub) == 'object' && sub != null) {
				var match = null, tpl_row = '', curRepl='', rexp = null;
				for(var i in source) {
					tpl_row = sub[1];
					rexp = new RegExp(this.left+'([a-zA-Z0-9\_\\\-\\\.]{1,})'+this.right, 'gim');
					
					while((match = rexp.exec(sub[1])) != null ){
						if(typeof(source[i][match[1]]) != 'undefined') {
							tpl_row = tpl_row.replace(match[0], source[i][match[1]]);
						} else {
							sub[1].replace(match[0], ''); // for next step!
							tpl_row = tpl_row.replace(match[0], '');
						}	
					}
					
					curRepl += tpl_row;
				}
				this.src = this.src.replace(sub[0], curRepl);
			}
		} else {
			this._data[variable] = source;
			this.tplPattern.push(this.left+variable+this.right); 
			this.tplReplaces.push(source);
		}
		
		return this;
	};
	
	this.render = function() {
		this.out = this.src;
		
		for(var index in this.tplPattern) {
			this.out = this.out.replaceAll(this.tplPattern[index], this.tplReplaces[index]);
		}
		
		var rexp = new RegExp(this.left+'([a-zA-Z0-9\_\\\-\\\.]{1,})'+this.right, 'gim');
		
		while((match = rexp.exec(this.out)) != null ){
			this.out = this.out.replaceAll(match[0], '');
		}

		this.extendedParser();
		
		return this;
	};
	
	this.extendedParser = function() {
		var spaceTpl = '[\ ]{0,}';
		var varnameTpl = '([a-zA-Z0-9\_\\\-\\\.]{1,})';
		
		// {variable || something}  -> if variable is undefined or null or == '', replace on "something" (where something - is a string, not template variable!)
		
		var match = (new RegExp(this.left+varnameTpl+spaceTpl+'\\\|\\\|'+spaceTpl+'(.*)'+this.right, 'gim')).exec(this.out);
		
		if(match && typeof(match) == 'object') {
			var variableName = match[1];
			var variableValue = (typeof(this._data[variableName]) != 'undefined' && this._data[variableName]  && this._data[variableName] !== '') ? this._data[variableName] : match[2];
			this.out = this.out.replace(match[0], variableValue);
		}
		
		// {variable (a|b)}  -> if(variable) then write(a) else  write(b) (where "a" and "b" is a string, not template variable)
		
		var match = (new RegExp(this.left+varnameTpl+spaceTpl+'\((.*)'+spaceTpl+'|'+spaceTpl+'(.*)\)'+this.right)).exec(this.out);
		
		if(match && typeof(match) == 'object') {
			var variableName = match[1];
			var variableValue = (typeof(this._data[variableName]) != 'undefined' && this._data[variableName]  && this._data[variableName] !== '') ? this._data[variableName] : match[2];
			//console.log( this._data[variableName]);
			this.out = this.out.replace(match[0], variableValue);
		}		
	};
	
	this.getDOMobject = function() {
		obj = document.createElement('p'); 
		obj.innerHTML = this.out; 
		return obj;
	};
	
	this
	
	return this;
}