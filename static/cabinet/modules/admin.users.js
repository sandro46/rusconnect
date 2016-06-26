var site_users = {
		
	myProfile : function() {
		var preloadTplData = [{profile:['admin_users','getProfile']}];
		$page.show(['profile.html', 'users', preloadTplData], {profilename:'Мой профиль', my:true}, function(current){			
			var vendorImage = uploader.init({
				container : $(".AvatarUploadButton",current),
				hideUploaded : true,
				formCaption : '',
				buttonCaption : 'Изменить фото',
				resize : ['smart:220x220','smart:100x100','original'],
				done : function(info) {
					$('.AvatarContainer',current).html('');
					$('.AvatarContainer',current).append($('<img></img>').attr('src', info[0].name));
					$('.AvatarContainer',current).append($('<br style="clear:both">'));
					$('.AvatarContainer',current).append($('<button class="btn btn-small">Сохранить</button>').bind('click',function(){
						
						site_users.updateUserAvatar(info[0].name, info[1].name, function(){
							$(this).parent().find('input').remove();
							$(this).remove();
						});
					}));
				}
			});
		});
	},
	
	deleteUserAvatar : function() {
		this.updateUserAvatar('','', function(){
			$('.AvatarContainer',$page.current).html('');
			var nophotourl = $('.AvatarContainer', $page.current).attr('nophoto-url');
			$('.AvatarContainer',$page.current).append($('<img></img>').attr('src', nophotourl));
		});
	},
	
	updateUserAvatar : function(url1, url2, callback) {
		$page.lock();
		admin_users.updateUserAvatar(false, url1, url2, function(){
			$page.unlock();
			if(!url1) {
				$page.sticky('Изменение профиля','Аватар удален.');
			} else {
				$page.sticky('Изменение профиля','Аватар успешно изменен.');
			}
			
			if(typeof(callback) == 'function') {
				callback();
			}
		}) 
	},
	
	makeRulesTable : function(result) {
		$table = $page.current.find('table[name="accesRulesTable"]');
		$table.html('');
		
		if(result === false) return;
		
		if(typeof(result) == 'object') {
			for(var i in result) {
				var row = $('<tr></tr>')
							.append('<td>'+result[i].name+'</td>')
							.append('<td><input type="checkbox" name="rule" object="'+result[i].id+'" action="read" '+ ((result[i].read == true)? 'checked="checked"' : '') +' > Чтение</td>')
							.append('<td><input type="checkbox" name="rule" object="'+result[i].id+'" action="write" '+ ((result[i].write == true)? 'checked="checked"' : '') +' > Изменение</td>')
							.append('<td><input type="checkbox" name="rule" object="'+result[i].id+'" action="delete" '+ ((result[i]['delete'] == true)? 'checked="checked"' : '') +' > Удаление</td>');
				$table.append(row);
			}
		}
	},
	
	getRules : function() {
		var rules = {};
		$page.current.find('table[name="accesRulesTable"] input[type="checkbox"]').each(function(){
			var obj = $(this).attr('object');
			var act = $(this).attr('action');
			if(typeof(rules[obj]) != 'object') rules[obj] = {};
			rules[obj][act] = $(this).is(':checked');
		});
		
		return rules;
	},
	
	addUser : function() {
		var preloadTplData = [{accessGroups:['admin_users','getAccessGroups']}];
		
		$page.show(['add.html', 'users', preloadTplData], false, function(current) {
			$page.bind('save', function() {
				var form = $page.getForm(current);
				
				if(!form.check) {
					$page.top();
					return;
				}
				
				var info = {
						'name_first' : form.data.name_first,
						'name_last' : form.data.name_last,
						'name_second' : form.data.name_second,
						'notify' : (form.data.notify == '1')? true : false,
						'passwd' : form.data.passwd,
						'phone' : form.data.phone,
						'role_id' : form.data.access_group,
						'email' : form.data.email,
						'avatar' : {
							'big' : form.data.avatar_big,
							'small' : form.data.avatar_small
						},
						'rules' : site_users.getRules(),
						'role_name' : form.data.rolename
				};

				
				
				admin_users.addUser(false, info, function(result) {
					
					var message = 'Была создана новая учетная запись. Данные для входа:<br>';
						message += '<b>Логин:<b> '+result.login+'<br>';
						message += '<b>Пароль:<b> '+result.password+'<br>';
					
					$page.alert('Пользователь успешно добавлен',message, function(){
						$page.back();
						if(typeof(grid.users_list) == 'object') {
							grid.users_list.start();
						}
					});
				});
			});

			site_users.editUserUI();
		});
	},
	
	editUser : function(userId) {
		admin_users.getUserInfo(userId, function(userInfo){
			var preloadTplData = [{accessGroups:['admin_users','getAccessGroups']}];

			$page.show(['add.html', 'users', preloadTplData], {userGroupId:userInfo.group_id}, function(current) {
				$page.current.find('input[name="passwd"]').val('**********').attr('changed','');
				$page.current.find('div[name="noticerow"]').hide();
				$page.current.find('input[name="name_first"]').val(userInfo.name_first)
				$page.current.find('input[name="name_last"]').val(userInfo.name_last);
				$page.current.find('input[name="name_second"]').val(userInfo.name_second);
				$page.current.find('input[name="phone"]').val(userInfo.phone);
				$page.current.find('input[name="email"]').val(userInfo.email);
				
				if(userInfo.avatar_big) {
					$('.AvatarContainer',current).html('');
					$('.AvatarContainer',current).append($('<img></img>').attr('src', userInfo.avatar_big));
					$('.AvatarContainer',current).append($('<br style="clear:both">'));
				}
				
				site_users.makeRulesTable(userInfo.access);
				
				
				$page.bind('save', function() {
					var form = $page.getForm(current);
					
					if(!form.check) {
						$page.top();
						return;
					}
					
					var info = {
							'name_first' : form.data.name_first,
							'name_last' : form.data.name_last,
							'name_second' : form.data.name_second,
							'phone' : form.data.phone,
							'role_id' : form.data.access_group,
							'email' : form.data.email,
							'rules' : site_users.getRules(),
							'role_name' : form.data.rolename
					};

					if(typeof(form.data.avatar_big) != 'undefined') {
						info.avatar = {
							big : form.data.avatar_big,
							small : form.data.avatar_small
						}
					}
					
					if($page.current.find('input[name="passwd"]').attr('changed')) {
						info.passwd = $page.current.find('input[name="passwd"]').val();
					}
					
					admin_users.addUser(userId, info, function(result) {
						$page.sticky('Учетная запись обновлена','Была обновлена учетная запись пользователя.');
						$page.back();
						if(typeof(grid.users_list) == 'object') {
							grid.users_list.start();
						}
					});
				});

				site_users.editUserUI();
			});
		});
	},
	
	editUserUI : function() {
		$page.bind('back', function() {
			$page.back();
		});
		
		$('.sendnotify', container).toggleButtons({
            label: {
                enabled: "Да",
                disabled: "Нет"
            }
        });
		
		var vendorImage = uploader.init({
			container : $(".AvatarUploadButton",$page.current),
			hideUploaded : true,
			formCaption : '',
			buttonCaption : 'Загрузить фото',
			resize : ['smart:220x220','smart:100x100','original'],
			done : function(info) {
				$('.AvatarContainer',$page.current).html('');
				$('.AvatarContainer',$page.current).append($('<img></img>').attr('src', info[0].name));
				$('.AvatarContainer',$page.current).append($('<br style="clear:both">'));
				$('.AvatarContainer',$page.current).append($('<input type="hidden" name="avatar_big" value="'+info[0].name+'">'));
				$('.AvatarContainer',$page.current).append($('<input type="hidden" name="avatar_small" value="'+info[1].name+'">'));
			}
		});
		
		$page.current.find('[name="access_group"]').bind('change', function(){
			var groupId = $(this).val();
			admin_users.getAccessObjects(groupId, function(result) {
				site_users.makeRulesTable(result);
				if(groupId == '0') {
					$page.current.find('div[name="rolename-control"]').show();
				} else {
					$page.current.find('div[name="rolename-control"]').hide();
				}
			});
		});
		
		$page.bind('passwordChange', function(){
			$page.current.find('input[name="passwd"]').val($page.makeUniqId(8)).attr('changed',1);
		});
	},
	
	editPassword : function(userId) {
		$page.show(['password.html', 'users'], false, function(current){
			 var options = {
                onLoad: function () {
                    $('[name="passwordStrengthMessage"]', current).text('Start typing password');
                },
                onKeyUp: function (evt) {
                    $(evt.target).pwstrength("outputErrorList");
                },
                minChar : 8,
                viewports : {
                	progress : $('.passProgress', $page.current),
                	verdict : $('.passMessage', $page.current)
                },
                showVerdicts : false
            };
			 
            $('[name="wordkey"]', current).pwstrength(options);
            
            $page.bind('back', function(){
            	$page.back();
            });
            
            $page.bind('save', function(){
            	var form = $page.getForm($page.current);
            	if(!form.check) return false;
            	
            	$page.lock();
            	admin_users.updateUserPassword(false, form.data.wordkey, function(){
            		$page.unlock();
            		$page.back();
            		$page.sticky('Изменение профиля','Пароль успешно изменен.');
            	});            	
            });
		});
	},
	

	
	listUsers : function() {
		$page.show(['list.html', 'users'], false, function(current){		
			
			
		});
	},
	
	showUser : function(userId) {
		var preloadTplData = [{profile:['admin_users','getProfile',[userId]]}];
		$page.show(['profile.html', 'users', preloadTplData], {profilename:'Профиль пользователя'}, function(current){
			$page.current.find('')
			var vendorImage = uploader.init({
				container : $(".AvatarUploadButton",current),
				hideUploaded : true,
				formCaption : '',
				buttonCaption : 'Изменить фото',
				resize : ['smart:220x220','smart:100x100','original'],
				done : function(info) {
					//console.log(info);
					$('.AvatarContainer',current).html('');
					$('.AvatarContainer',current).append($('<img></img>').attr('src', info[0].name));
					$('.AvatarContainer',current).append($('<br style="clear:both">'));
					$('.AvatarContainer',current).append($('<button class="btn btn-small">Сохранить</button>').bind('click',function(){
						$page.lock();
						var em = $(this)
						admin_users.updateUserAvatar(false, info[0].name, info[1].name, function(){
							em.parent().find('input').remove();
							em.remove();
							$page.unlock();
							$page.sticky('Изменение профиля','Аватар успешно изменен.');
						}) 
					}));
				}
			});
		});
	},
	
	deleteUser : function(userId) {
		$page.confirm('Предупреждение','Вы действительно хотите удалить этого пользователя?', function(){
			$page.lock();
			admin_users.deleteUser(userId, function(result){
				$page.unlock();
				if(result == -1) {
					$page.sticky('Ошибка выполнения операции','Пользователь не найден в базе.');
				} else if(result < -1) {
					$page.sticky('Ошибка доступа','Вам запрещено выполнять это действие.');
				} else {
					$page.sticky('Пользователь удален','Пользователь удален');
					$page.back();
					if(typeof(grid.users_list) == 'object') {
						grid.users_list.start();
					}
				}
			});
		});
	},
	
	blockUser : function(userId) {
		$page.confirm('Предупреждение','Вы действительно хотите заблокировать этого пользователя?', function(){
			$page.lock();
			admin_users.blockUser(userId, {reason:'Заблокирован администратором магазина',date:false}, function(result){
				$page.unlock();
				if(result == -1) {
					$page.sticky('Ошибка выполнения операции','Пользователь не найден в базе.');
				} else if(result < -1) {
					$page.sticky('Ошибка доступа','Вам запрещено выполнять это действие.');
				} else {
					$page.sticky('Пользователь Заблокирован','Пользователь Заблокирован');
					$page.update();
				}
			});
		});
	},
	
	unblockUser : function(userId) {
		$page.confirm('Предупреждение','Вы действительно хотите разблокировать этого пользователя?', function(){
			$page.lock();
			admin_users.unblockUser(userId, function(result){
				$page.unlock();
				if(result == -1) {
					$page.sticky('Ошибка выполнения операции','Пользователь не найден в базе.');
				} else if(result < -1) {
					$page.sticky('Ошибка доступа','Вам запрещено выполнять это действие.');
				} else {
					$page.sticky('Пользователь разблокирован','Пользователь разблокирован');
					$page.update();
				}
			});
		});
	}	
};




$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Мои пользователи',
			on : site_users.listUsers,
			
			'/show/:id/' : {
				name: 'Профиль пользователя',
				on : site_users.showUser,
				always_reload: true,
				delete_unload: true
			},
			
			'/edit/:id/' : {
				name: 'Редактирование профиля',
				on : site_users.editUser,
				always_reload: true,
				delete_unload: true
			},
			
			'/add/' : {
				name : 'Добавить пользователя',
				on : site_users.addUser,
				always_reload: true,
				delete_unload: true
			},
			
			'/my/' : {
				name: 'Мой профиль ',
				always_reload: true,
				delete_unload: true,
				on : site_users.myProfile,
				
				'/pass/' : {
					name: 'Изменить пароль',
					on : site_users.editPassword,
				},
			}
		}
	};
	
	$page.init(routes);
});



(function ($) {
    "use strict";

    var options = {
            errors: [],
            // Options
            minChar: 7,
            errorMessages: {
                password_to_short: "Слишклм короткий пароль",
                same_as_username: "Пароль не должен совпадать с вашим логином"
            },
            scores: [15, 30, 50, 70],
            verdicts: ["Простой", "Нормальный", "Средний", "Сложный", "Очень сложный"],
            showVerdicts: true,
            raisePower: 1.4,
            usernameField: "#username",
            onLoad: undefined,
            onKeyUp: undefined,
            viewports: {
                progress: undefined,
                verdict: undefined,
                errors: undefined
            },

            ruleScores: {
                wordNotEmail: -100,
                wordLength: -100,
                wordSimilarToUsername: -100,
                wordLowercase: 1,
                wordUppercase: 3,
                wordOneNumber: 10,
                wordThreeNumbers: 20,
                wordOneSpecialChar: 30,
                wordTwoSpecialChar: 50,
                wordUpperLowerCombo: 2,
                wordLetterNumberCombo: 2,
                wordLetterNumberCharCombo: 2
            },
            rules: {
                wordNotEmail: true,
                wordLength: true,
                wordSimilarToUsername: true,
                wordLowercase: true,
                wordUppercase: true,
                wordOneNumber: true,
                wordThreeNumbers: true,
                wordOneSpecialChar: true,
                wordTwoSpecialChar: true,
                wordUpperLowerCombo: true,
                wordLetterNumberCombo: true,
                wordLetterNumberCharCombo: true
            },
            validationRules: {
                wordNotEmail: function (options, word, score) {
                    return word.match(/^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/i) && score;
                },
                wordLength: function (options, word, score) {
                    var wordlen = word.length,
                        lenScore = Math.pow(wordlen, options.raisePower);
                    if (wordlen < options.minChar) {
                        lenScore = (lenScore + score);
                        options.errors.push(options.errorMessages.password_to_short);
                    }
                    return lenScore;
                },
                wordSimilarToUsername: function (options, word, score) {
                    var username = $(options.usernameField).val();
                    if (username && word.toLowerCase().match(username.toLowerCase())) {
                        options.errors.push(options.errorMessages.same_as_username);
                        return score;
                    }
                    return true;
                },
                wordLowercase: function (options, word, score) {
                    return word.match(/[a-z]/) && score;
                },
                wordUppercase: function (options, word, score) {
                    return word.match(/[A-Z]/) && score;
                },
                wordOneNumber : function (options, word, score) {
                    return word.match(/\d+/) && score;
                },
                wordThreeNumbers : function (options, word, score) {
                    return word.match(/(.*[0-9].*[0-9].*[0-9])/) && score;
                },
                wordOneSpecialChar : function (options, word, score) {
                    return word.match(/.[!,@,#,$,%,\^,&,*,?,_,~]/) && score;
                },
                wordTwoSpecialChar : function (options, word, score) {
                    return word.match(/(.*[!,@,#,$,%,\^,&,*,?,_,~].*[!,@,#,$,%,\^,&,*,?,_,~])/) && score;
                },
                wordUpperLowerCombo : function (options, word, score) {
                    return word.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/) && score;
                },
                wordLetterNumberCombo : function (options, word, score) {
                    return word.match(/([a-zA-Z])/) && word.match(/([0-9])/) && score;
                },
                wordLetterNumberCharCombo : function (options, word, score) {
                    return word.match(/([a-zA-Z0-9].*[!,@,#,$,%,\^,&,*,?,_,~])|([!,@,#,$,%,\^,&,*,?,_,~].*[a-zA-Z0-9])/) && score;
                }
            }
        },

        setProgressBar = function ($el, score) {
            var options = $el.data("pwstrength"),
                progressbar = options.progressbar,
                $verdict;

            if (options.showVerdicts) {
                if (options.viewports.verdict) {
                    $verdict = $(options.viewports.verdict).find(".password-verdict");
                } else {
                    $verdict = $el.parent().find(".password-verdict");
                    if ($verdict.length === 0) {
                        $verdict = $('<span class="password-verdict"></span>');
                        $verdict.insertAfter($el);
                    }
                }
            }

            if (score < options.scores[0]) {
                progressbar.addClass("progress-danger").removeClass("progress-warning").removeClass("progress-success");
                progressbar.find(".bar").css("width", "5%");
                if (options.showVerdicts) {
                    $verdict.text(options.verdicts[0]);
                }
            } else if (score >= options.scores[0] && score < options.scores[1]) {
                progressbar.addClass("progress-danger").removeClass("progress-warning").removeClass("progress-success");
                progressbar.find(".bar").css("width", "25%");
                if (options.showVerdicts) {
                    $verdict.text(options.verdicts[1]);
                }
            } else if (score >= options.scores[1] && score < options.scores[2]) {
                progressbar.addClass("progress-warning").removeClass("progress-danger").removeClass("progress-success");
                progressbar.find(".bar").css("width", "50%");
                if (options.showVerdicts) {
                    $verdict.text(options.verdicts[2]);
                }
            } else if (score >= options.scores[2] && score < options.scores[3]) {
                progressbar.addClass("progress-warning").removeClass("progress-danger").removeClass("progress-success");
                progressbar.find(".bar").css("width", "75%");
                if (options.showVerdicts) {
                    $verdict.text(options.verdicts[3]);
                }
            } else if (score >= options.scores[3]) {
                progressbar.addClass("progress-success").removeClass("progress-warning").removeClass("progress-danger");
                progressbar.find(".bar").css("width", "100%");
                if (options.showVerdicts) {
                    $verdict.text(options.verdicts[4]);
                }
            }
        },

        calculateScore = function ($el) {
            var self = this,
                word = $el.val(),
                totalScore = 0,
                options = $el.data("pwstrength");

            $.each(options.rules, function (rule, active) {
                if (active === true) {
                    var score = options.ruleScores[rule],
                        result = options.validationRules[rule](options, word, score);
                    if (result) {
                        totalScore += result;
                    }
                }
            });
            setProgressBar($el, totalScore);
            return totalScore;
        },

        progressWidget = function () {
            return '<div class="progress"><div class="bar"></div></div>';
        },

        methods = {
            init: function (settings) {
                var self = this,
                    allOptions = $.extend(options, settings);

                return this.each(function (idx, el) {
                    var $el = $(el),
                        progressbar,
                        verdict;

                    $el.data("pwstrength", allOptions);

                    $el.on("keyup", function (event) {
                        var options = $el.data("pwstrength");
                        options.errors = [];
                        calculateScore.call(self, $el);
                        if ($.isFunction(options.onKeyUp)) {
                            options.onKeyUp(event);
                        }
                    });

                    progressbar = $(progressWidget());
                    if (allOptions.viewports.progress) {
                        $(allOptions.viewports.progress).append(progressbar);
                    } else {
                        progressbar.insertAfter($el);
                    }
                    progressbar.find(".bar").css("width", "0%");
                    $el.data("pwstrength").progressbar = progressbar;

                    if (allOptions.showVerdicts) {
                        verdict = $('<span class="password-verdict">' + allOptions.verdicts[0] + '</span>');
                        if (allOptions.viewports.verdict) {
                            $(allOptions.viewports.verdict).append(verdict);
                        } else {
                            verdict.insertAfter($el);
                        }
                    }

                    if ($.isFunction(allOptions.onLoad)) {
                        allOptions.onLoad();
                    }
                });
            },

            destroy: function () {
                this.each(function (idx, el) {
                    var $el = $(el);
                    $el.parent().find("span.password-verdict").remove();
                    $el.parent().find("div.progress").remove();
                    $el.parent().find("ul.error-list").remove();
                    $el.removeData("pwstrength");
                });
            },

            forceUpdate: function () {
                var self = this;
                this.each(function (idx, el) {
                    var $el = $(el),
                        options = $el.data("pwstrength");
                    options.errors = [];
                    calculateScore.call(self, $el);
                });
            },

            outputErrorList: function () {
                this.each(function (idx, el) {
                    var output = '<ul class="error-list">',
                        $el = $(el),
                        errors = $el.data("pwstrength").errors,
                        viewports = $el.data("pwstrength").viewports,
                        verdict;
                    $el.parent().find("ul.error-list").remove();

                    if (errors.length > 0) {
                        $.each(errors, function (i, item) {
                            output += '<li>' + item + '</li>';
                        });
                        output += '</ul>';
                        if (viewports.errors) {
                            $(viewports.errors).html(output);
                        } else {
                            output = $(output);
                            verdict = $el.parent().find("span.password-verdict");
                            if (verdict.length > 0) {
                                el = verdict;
                            }
                            output.insertAfter(el);
                        }
                    }
                });
            },

            addRule: function (name, method, score, active) {
                this.each(function (idx, el) {
                    var options = $(el).data("pwstrength");
                    options.rules[name] = active;
                    options.ruleScores[name] = score;
                    options.validationRules[name] = method;
                });
            },

            changeScore: function (rule, score) {
                this.each(function (idx, el) {
                    $(el).data("pwstrength").ruleScores[rule] = score;
                });
            },

            ruleActive: function (rule, active) {
                this.each(function (idx, el) {
                    $(el).data("pwstrength").rules[rule] = active;
                });
            }
        };

    $.fn.pwstrength = function (method) {
        var result;
        if (methods[method]) {
            result = methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === "object" || !method) {
            result = methods.init.apply(this, arguments);
        } else {
            $.error("Method " +  method + " does not exist on jQuery.pwstrength");
        }
        return result;
    };
}(jQuery));