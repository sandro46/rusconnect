var admin_currency = {
	wizardData : {},
	symbols : false,
	info : function() {
		$page.show(['currency.info.html', 'site'], false, function(container){
			$('.show_zeroes', container).toggleButtons({
	            label: {
	                enabled: "Да",
	                disabled: "Нет"
	            }
	        }).find('input').bind('change', function(){
	        	$page.lock();
	        	admin_site.cahngeZerroesShow($(this).is(':checked')? 1 : 0, function(){
	        		$page.unlock();
	        		$page.sticky('Изменены настройки отображения цены','Изменен параметр: "Показывать дробную часть цены"');
	        	});
	        });
			
			$page.bind('clearCurrency', function(){
				$page.confirm('Отключение поддержки конвертации валют','Вы действительно хотите отключить поддержку конвертации валюты?', function() {
					$page.lock();
					admin_site.clearCurrencySetting(function(){
						$page.unlock();
						$page.sticky(' ', 'Конвертация валют отключена!');
						$page.update();
					})
				});
			});
			
			$page.bind('runWizard', function(){
				$page.confirm('Запуск мастера настройки валют','Это действие приведет к сбросу текущих настроек валюты. <br>Вы действительно хотите продолжить?', function(){
					$page.go('/wizard/');
				});
			})
		});
	},
		
	add : function() {
		var preloadTplData = [
		      	{currencys:['admin_site','getSiteCurrencyNotUsed']}
		];
		
		$page.show(['currency.add.html', 'site', preloadTplData], false, function(container){
			$('.show_zerroes', container).toggleButtons({
	            label: {
	                enabled: "Да",
	                disabled: "Нет"
	            }
	        });
			
			container.find('input[name="conversiontype"]').bind('change', function(){
				var type = $(this).val();
				container.find('div[type]').hide();
				container.find('div[type="'+type+'"]').slideDown();
			});
			
			$page.bind('back', function(){
				$page.back()
			});
			
			$page.bind('save', function(){
				var form = $page.getForm(current);
				console.log(form.data);
				/*
				if(form.check) {
					
					$page.lock();
					var info = {
						user_name : form.data.user_name,
						text : form.data.text,
						approved : form.data.approved
					};
					
					$page.lock();
					admin_site.addReview(false, info, function(){
						$page.unlock();
						$page.sticky(' ', 'Отзыв добавлен.');
						$page.back();
						//grid.reviews.start();
					});
				}*/
			});
		});
	},
	
	wizard : function() {
		$page.lock();
		admin_site.getAllCurrencys(function(curencyes){
			$page.unlock();
			admin_currency.symbols = curencyes;
			$page.show(['currency.wizard.html', 'site', [{allCurencys:['admin_site','getAllCurrencys']}]], false, function(current){
				current.find('select[name="admin_currency"]').bind('change', function(){
					admin_currency.wizardData.currency = $(this).val();
				}).trigger('change').chosen();
				
				$('.siteWizard', current).find('.button-submit').bind('call-show', function(){
					$(this).show();
				});
				
				$('.siteWizard', current).find('.button-submit').bind('call-hide', function(){
					$(this).hide();
				});
				
				$page.bind('wizardDone', function(){
					$page.confirm('Изменение настроек цен и валюты','Вы уверены, что хотите применить эти настройки?<br>Все настройки вступят в силу сразу после нажатия на кнопку Ok!<br>Рекомендуем внимательно проверить настройки, прежде чем применять их!',function(){
						$page.lock();
						admin_currency.wizardData.admin_default_currency = $page.current.find('select[name="adminDefaultCurrency"]').val();
						admin_currency.wizardData.postaction = $page.current.find('input[name="postaction"]:checked').val();
						admin_site.setCurrencySetting(admin_currency.wizardData, function(){
							$page.unlock();
							$page.sticky('Изменение настроек цен и валюты','Новые настройки успешно сохранены и вступили в силу.');
							$page.back();
						});
					});
				});
				
				$('.siteWizard',current).bootstrapWizard({
					'nextSelector': '.button-next',
			        'previousSelector': '.button-previous',
			         onTabClick : function() {
			        	return false;
			         },
			         onNext: function (tab, navigation, index) {
			        	if(!(function(){
			        		current.find('div[name="errorbox"]').remove();
			        		if(index == 1) {
			        			admin_currency.wizardData.site_currency = current.find('select[name="site_currency"]').val();
			        			return true;
			        		}
			        		if(index == 2) {
			        			if(typeof(admin_currency.wizardData.currency) == 'undefined' || !admin_currency.wizardData.currency || admin_currency.wizardData.currency.length == 0) {
			        				var error = $('<div name="errorbox" class="alert alert-error"><strong>Ошибка!</strong>Необходимо выбрать хотя бы одну валюту!</div>');
			        				current.find('select[name="admin_currency"]').before(error);
			        			} else {
			        				return true;
			        			}
			        		}
			        		if(index == 3) {
			        			admin_currency.wizardData.site_currency_settings = [];
			        			current.find('table[name="tableListCurrencys"] tbody tr').each(function(){
			        				admin_currency.wizardData.site_currency_settings.push({
			        					id : $(this).attr('currency_id'),
			        					type : ($(this).find('select[name="conversionType"]').length)? $(this).find('select[name="conversionType"]').val() : 0,
			        					add : $(this).find('input[name="quoteadd"]').val(),
			        					fix : $(this).find('input[name="fixedvalue"]').val(),
			        					zero : $(this).find('input[name="showsource"]').is(':checked') ? 1 : 0
			        				});
			        			});
			        			
			        			return true;
			        		}
			        		
			        		return false;
			        	})()) return false;
			        	
		                var total = navigation.find('li').length;
		                var cur = index + 1;
		                $('.step-title', current).text('Шаг ' + (index + 1) + ' из ' + total);
		                $('li', $('.siteWizard', current)).removeClass("done");
		                var li_list = navigation.find('li');
		                for (var i = 0; i < index; i++) {
		                    $(li_list[i]).addClass("done");
		                }

		                if (cur == 1) {
		                	$('.siteWizard', current).find('.button-previous').hide();
		                } else {
		                	$('.siteWizard', current).find('.button-previous').show();
		                }

		                if (cur >= total) {
		                	$('.siteWizard', current).find('.button-next').hide();
		                	$('.siteWizard', current).find('.button-submit').trigger('call-show');
		                } else {
		                	$('.siteWizard', current).find('.button-next').show();
		                	$('.siteWizard', current).find('.button-submit').trigger('call-hide');
		                }
			          },
			         onPrevious: function (tab, navigation, index) {
			                var total = navigation.find('li').length;
			                var cur = index + 1;
			                $('.step-title', current).text('Шаг ' + (cur) + ' из ' + total);
			                $('li', $('.siteWizard', current)).removeClass("done");
			                var li_list = navigation.find('li');
			                for (var i = 0; i < index; i++) {
			                    $(li_list[i]).addClass("done");
			                }
		
			                if (cur == 1) {
			                	$('.siteWizard', current).find('.button-previous').hide();
			                } else {
			                	$('.siteWizard', current).find('.button-previous').show();
			                }
		
			                if (cur >= total) {
			                	$('.siteWizard', current).find('.button-next').hide();
			                	$('.siteWizard', current).find('.button-submit').trigger('call-show');
			                } else {
			                	
			                	$('.siteWizard', current).find('.button-next').show();
			                	$('.siteWizard', current).find('.button-submit').trigger('call-hide');
			                }
			            },
			            onTabShow: function (tab, navigation, index) {
			            	var total = navigation.find('li').length;
				            var cur = index + 1;

				            if(cur == 3) {				            	
				            	var table = current.find('table[name="tableListCurrencys"]');
				            	table.find('tbody').html('');
				            	
				            	
				            	for(var i in admin_currency.wizardData.currency) {
				            		var currencyInfo = admin_currency.symbols[admin_currency.wizardData.currency[i]];
				            		var tpl = new templates(table.find('tfoot')[0].innerHTML, true);
					            		tpl.assign('code', currencyInfo.code);
					            		tpl.assign('title', currencyInfo.title);
					            		tpl.assign('show_name', (currencyInfo.symbol)? currencyInfo.symbol : currencyInfo.name);
					            		tpl.render();
					            	tpl = $(tpl.out);
					            	tpl.attr('currency_id', currencyInfo.id);
					            	tpl.find('div.show_zerroes_currency').toggleButtons({
					    	            label: {
					    	                enabled: "Да",
					    	                disabled: "Нет"
					    	            }
					    	        });
					            	
				            		if(currencyInfo.id != 5) {
				            			tpl.find('select[name="conversionType"]').bind('change', function(){
						            		var type = $(this).val();
						            		$(this).closest('tr').find('div[conversiontype]').hide();
						            		$(this).closest('tr').find('div[conversiontype="'+type+'"]').show();
						            	}).trigger('change');
					            	} else {
					            		tpl.find('select[name="conversionType"]').remove();
					            	}

				            		
					            	table.find('tbody').append(tpl);
					            	tpl.find('select[name="conversionType"]').chosen()
				            	}
				            }
				            
				            if(cur == 4) {
				            	current.find('div[name="currencyDefaultSelectorContainer"]').html();
				            	var tpl = $('<select class="chosen" name="adminDefaultCurrency"></select>');
				            	for(var i in admin_currency.wizardData.currency) {
				            		var currencyInfo = admin_currency.symbols[admin_currency.wizardData.currency[i]];
				            		var selected = (i == 0)? 'selected="selected"' : '';
				            		tpl.append('<option '+selected+' value="'+currencyInfo.id+'">('+currencyInfo.code+') '+currencyInfo.title+'</option>');
				            	}
				            	
				            	current.find('div[name="currencyDefaultSelectorContainer"]').append(tpl);
				            	tpl.css({width:'240px'});
				            	tpl.chosen();
				            }
			        			
			            	if(cur == total) {
			            		var lastDone = navigation.find('li.done');
			            		if(lastDone.length == 3) {
			            			$('.siteWizard', current).find('.button-next').hide();
			            			$('.siteWizard', current).find('.button-submit').trigger('call-show');
			            		} else {
			            			$('.siteWizard', current).find('.button-submit').trigger('call-hide');
			            			$('.siteWizard', current).find('.button-next').hide();
			            		}
			            	} else {
			            		 var lastIndex = navigation.find('li.done').length;
			            		 if(cur-1 <= lastIndex) {
			            			 $('.siteWizard', current).find('.button-next').show();
			            		 } else {
			            			 $('.siteWizard', current).find('.button-next').hide();
			            		 }
			            	}
			            	
			                var percent = (cur / total) * 100;
			                $('.siteWizard', current).find('.bar').css({
			                    width: percent + '%'
			                });
			                
			                if(cur == 1) {
			                	$('.siteWizard', current).find('.button-submit').trigger('call-hide');
			                }
			            }
				 });
				
			});
			
		});
		
		
	}
}


$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Настройка параметров валюты и конвертации',
			always_reload: true,
			on : admin_currency.info,
			
			'/add/' : {
				name: 'Настройка валютной конвертации',
				always_reload: true,
				on : admin_currency.add,
			},
			
			'/wizard/' : {
				name: 'Мастер настройки валют',
				always_reload: true,
				on : admin_currency.wizard,
			}
		}
	};
	
	$page.init(routes);
});
