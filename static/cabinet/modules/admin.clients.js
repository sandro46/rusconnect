
var site_clients = {
	manager : null,
	
	list : function() {
		$page.show(['clients_list.html', 'clients'], false, function(current){
			//$('#grid-orders_list-filters').hide();
			//grid.clients_list.removeFilter('group_id');
			//grid.clients_list.start();
		});
	},
	
	partnerList : function() {
		$page.show(['partners_list.html', 'clients'], false, function(current){
			//$('#grid-orders_list-filters').hide();
			//grid.partners_list.removeFilter('group_id');
			//grid.partners_list.start();
		});
	},
	
	partnerAdd : function() {
		var preloadTplData = [{partnerTypes:['admin_clients','getPartnerTypes']}];
		
		$page.show(['partner_add.html', 'clients', preloadTplData], false, function(current){
			site_clients.partnerAddFormUI(false);
		});
	},
	
	gotoClient : function(id) {
		$page.lock();
		admin_clients.cabinetFakeAuth(id, function(){
			$page.unlock();
			document.location.href = '/';
		});
	},
	
	addClient : function() {
		var preloadTplData = [{partnerTypes:['admin_clients','getPartnerTypes']}];
		
		$page.show(['client_add.html', 'clients', preloadTplData], false, function(current){
			site_clients.clientAddFormUI(false);
			
			$page.bind('save', function(){
				var form = $page.getForm(current);
				if(form.check) {
					$page.lock();
					admin_clients.addClient(form.data, function(data){
						$page.unlock();
						//console.log(data);
						
						$page.alert('Клиент успешно добавлен!', 'Логин: <b>'+data.user.login+'</b></br>Пароль: <b>'+data.user.password+'</b></br>Клиент: <b>'+data.client.label+'</b></br>',function(){
							$page.back();
						});
						
					});
				}
			});
			
			$page.bind('back', function(){
				$page.back();
			});
			
		});
	},
	
	editClient : function(id) {
		var preloadTplData = [{clientInfo:['admin_clients','getclientInfo',[id]]}];
		
		$page.show(['client_edit.html', 'clients', preloadTplData], false, function(current){
			site_clients.clientAddFormUI();
			
			$page.bind('save', function(){
				var form = $page.getForm(current);
				if(form.check) {
					$page.lock();
					admin_clients.editClient(id, form.data, function(){
						$page.unlock();						
						$page.back();
					});
				}
			});
			
			$page.bind('back', function(){
				$page.back();
			});
		});
	},
	
	showClientInfo : function(id) {
		var preloadTplData = [{clientInfo:['admin_clients','getclientInfo']}];
		
		$page.show(['client_show.html', 'clients', preloadTplData], false, function(current){
			
		});
	},
	
	delClient : function(id) {
		$page.confirm('Подтвердите удаление','Вы действительно хотите удалить этого клиента, все его магазины, и всех его пользователей? Данные вернуть не получится!',function(){
			admin_clients.deleteClient(id, function(){
				grid.clients_list.start();
				$page.sticky('Клиент удален','Клиент и все данные связанные с ним были удалены.');
			})
		});
	},
	
	partnerEdit : function(id) {
		var preloadTplData = [{partnerInfo:['admin_clients','getPartnerInfo',[id]]}, {partnerTypes:['admin_clients','getPartnerTypes']}];
				
		$page.show(['partner_add.html', 'clients', preloadTplData], false, function(current){
			site_clients.partnerAddFormUI(id);
		});
	},
	
	deletePrtner : function(id) {
		$page.confirm('Удаление партнера','Вы действительно хотите удалить этого партнера?', function(){
			$page.lock();
			admin_clients.deletePartner(id, function(){
				$page.unlock();
				$page.sticky('Изменение данных','Был удален партнер из системы.');
				grid.partners_list.start();
			});
		});
	},
	
	clientAddFormUI : function(id) {
		$page.bind('passwordChange', function(){
			$page.current.find('input[name="password"]').val($page.makeUniqId(8));
		});
				
		$('.client_is_active', container).toggleButtons({
            label: {
                enabled: "Да",
                disabled: "Нет"
            }
        });
		
		$('.client_notice', container).toggleButtons({
            label: {
                enabled: "Да",
                disabled: "Нет"
            }
        });
		
		$page.current.find('[action="passwordChange"]').trigger('click');
		
		
		
		/*
		 
		var emp = $page.current.find('[name="city_autocomplete"]').eq(0);
		var context = this;
		emp.bind('keyup mouseup', function(){
			context[$(this).attr('func')]($(this).val(),function(){
				
			})
		});
		* 
		 *  
		 */
		/*
		<div class="chzn-container" style="width: 220px;">
        <div style="left: 0px; width: 218px; top: -20px;" class="chzn-drop">
          <ul class="chzn-results">
            
            <li style="" class="active-result group-option">Dallas Cowboys</li>
            <li style="" class="active-result group-option">New York Giants</li>
            <li style="" class="active-result group-option">Philadelphia Eagles</li>
            <li style="" class="active-result group-option">Washington Redskins</li>
          </ul>
        </div>
      </div>
		*/
		//$page.current.find('[name="city_autocomplete"]').chosen();
		
	},
	
	partnerAddFormUI : function(id) {
		$page.bind('passwordChange', function(){
			$page.current.find('input[name="email_password"]').val($page.makeUniqId(8));
		});
		
		$page.bind('referalChange', function(){
			$page.current.find('input[name="referal"]').val(hex_md5($page.current.find('input[name="email"]').val()));
		});
		
		$page.bind('back', function(){
			$page.back();
		});
		
		$page.bind('save', function(){
			var form = $page.getForm($page.current);
			if(form.check == true) {
				$page.lock();
				admin_clients.addPartner(id, form.data, function(result){
					$page.unlock();
					
					if(typeof(result) == 'object') {
						$page.alert('Партнер успешно добавлен!', 'Email: <b>'+result.email+'</b></br>Пароль от почты: <b>'+result.mail.password+'</b></br>Реферал: <b>http://ncity.biz/?ref='+result.referal+'</b></br>',function(){
							grid.partners_list.start();
						});
						$page.back();
					}
				});
			}
			
		});
		
		if(!id) {
			$page.current.find('input[name="name"]').bind('change keyup mouseup', function(){
				var name = ($(this).val()).split(' ');
				if(name.length < 2) return;
				var email = $page.transliteral(name[0])+'.'+$page.transliteral(name[1]);
				$page.current.find('input[name="email"]').val(email);
			});
		}
		
		
		
	}
	
}






$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Клиенты ',
			always_reload: true,
			delete_unload: true,
			on : site_clients.list,
			
			'/add/' : {
				
				name: 'Новый клиент',
				on : site_clients.addClient,
				always_reload: true,
				delete_unload: true,
			},
			
			'/edit/:id' : {
				name: 'Редактирование клиента',
				on : site_clients.editClient,
				always_reload: true,
				delete_unload: true,
			},
			
			'/show/:id' : {
				name: 'Просмотр клиента',
				on : site_clients.showClientInfo,
				always_reload: true,
				delete_unload: true,
			},
			
			'/partners/' : {
				name: 'Партнеры',
				on : site_clients.partnerList,
				
				
				'/edit/:id' : {
					always_reload: true,
					delete_unload: true,
					name: 'Редактирование партнера',
					on : site_clients.partnerEdit,
				},
				
				'/add/' : {
					always_reload: true,
					delete_unload: true,
					name: 'Новый партнер',
					on : site_clients.partnerAdd,
				},
			},
		}
	};
	
	$page.init(routes);
});