var admin_shop_clients = {
	showList : function(route) {
		var preloadTplData = [{clientSources:['admin_shop','getContactSources']}];
		
		$page.show(['clients.html', 'shop', preloadTplData], false, function(current){
			
			$('#grid-clients_list-filters').hide();
			grid.clients_list.start();
		});
	},
	
	showInfo : function(id) {
		$page.lock();
		admin_shop.getContactInfo(id, function(result){
			var userInfo = result;
			if(typeof(grid.orders_local_list) == 'object') {
				delete(grid.orders_local_list);
			}
			
			$page.show(['contact.show.html', 'shop'], false, function(current){
				$page.unlock();
				
				var contactBox = current.find('table[name="userContactInfo"]');
				
				current.find('h4.userName').html(userInfo.surname+' '+ userInfo.name+' '+ userInfo.lastname+'<br/><small>Источник: '+userInfo.source_name+'</small>');
				contactBox.find('td[name="email"]').html(userInfo.email);
				contactBox.find('td[name="phone"]').html(userInfo.phone);
				
				var address = '';
				if(typeof(userInfo.address) == 'object') {
					if(userInfo.address.zip) address += userInfo.address.zip + '';
					if(userInfo.address.region) address += ', '+userInfo.address.region;
					if(userInfo.address.city) address += ', г. '+userInfo.address.city;
					if(userInfo.address.street) address += ', '+userInfo.address.street;
					if(userInfo.address.house) address += ' '+userInfo.address.house;
					if(userInfo.address.building) address += ' стр. '+userInfo.address.building;
					if(userInfo.address.flat) address += ' кв. '+userInfo.address.flat;
				}
				
				if(address.substr(0,1) == ',') {
					address = address.substr(2);
				}
				
				contactBox.find('td[name="address"]').html(address);
				
				var companyBox = current.find('div[name="userBillingInfoContainer"]');
				
				if(userInfo.company_id) {
					companyBox.show();
					companyBox.find('td[name="legal_address"]').html(userInfo.company.address);
					companyBox.find('td[name="inn"]').html(userInfo.company.inn);
					companyBox.find('td[name="kpp"]').html(userInfo.company.kpp);
					companyBox.find('td[name="ogrn"]').html(userInfo.company.ogrn);
					companyBox.find('td[name="bill"]').html(userInfo.company.bill);
					companyBox.find('td[name="bik"]').html(userInfo.company.bik);
					companyBox.find('td[name="bank"]').html(userInfo.company.bank);
					companyBox.find('td[name="name"]').html(userInfo.company.name);
					
				} else {
					companyBox.hide();
				}
				
				grid.orders_local_list.addFilter('contact_id', userInfo.contact_id).start();
				/*
				
				$('td.birthday', current).html('12.03.1985');
				
				$('td.phone', current).html(userInfo.phone);
				$('td.address', current).html(userInfo.address.zip+', '+userInfo.address.address1+', ' +userInfo.address.address2);
				*/
			});
			
		});
	},
	
	appendFilter : function() {
		var source = $('select[name="clietns_filter_source"]', $page.current).val();
		var special = $('select[name="clietns_filter_special"]', $page.current).val();
		
		if(!source || source == '0') {
			grid.clients_list.removeFilter('source_id');
		} else {
			grid.clients_list.addFilter('source_id', source);
		}
		
		if(!special || special == '0') {
			grid.clients_list.removeFilter('special');
		} else {
			grid.clients_list.addFilter('special', special);
		}
		
		grid.clients_list.start();
	},
	
	clearFilter : function() {
		$('select[name="clietns_filter_source"]', $page.current).parent().find('.search-choice-close').trigger('mousedown').trigger('mouseup');
		$('select[name="clietns_filter_special"]', $page.current).parent().find('.search-choice-close').trigger('mousedown').trigger('mouseup');
		grid.clients_list.removeFilter('special');
		grid.clients_list.removeFilter('source_id');
		
		$('#grid-clients_list-filters').hide();
		grid.clients_list.start();
	}
};


$(document).ready(function(){
	var routes = {
		'/' : {
			name: 'Список клиентов',
			always_reload: true,
			delete_unload: true,
			on : admin_shop_clients.showList,
			
			'/show/:id/' : {
				name: 'Информация о клиенте',
				always_reload: true,
				delete_unload: true,
				on : admin_shop_clients.showInfo,
			},
			
		}
	};
	
	$page.init(routes);
});


