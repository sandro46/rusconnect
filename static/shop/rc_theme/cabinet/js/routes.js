var routes = {
	'/' : {
		name: 'Панель управления',
		on : cabinet.main_page,
		alias: 'main_page',
		'/my' : {
			name: 'Персональные данные',
			alias: 'personal',
			on : cabinet.personal,
				
			'/changepassword' : {
				name: 'Мои данные - Изменить пароль',
				alias: 'personal',
				on : cabinet.personalEditPass,
			}
		},
		
		'/address' : {
			name: 'Адреса доставки',
			on : cabinet.addressList,
			alias: 'addresslist',
			
			'/add' : {
				name: 'Новый адрес',
				on : cabinet.addressAdd,
				alias: 'addresslist',
			},
			
			'/edit' : {
				name: 'Редактировать адрес',
				on : cabinet.addressMainEdit,
				alias: 'addresslist',
			},
			
			'/edit/:id' : {
				name: 'Редактировать адрес',
				on : cabinet.addressEdit,
				alias: 'addresslist',
			}
		},
		
		'/discount' : {
			name: 'Ценовая категория',
			on : cabinet.discount,
			alias: 'discount',
		},
		
		'/billing_information' : {
			name: 'Реквизиты юр. лица',
			on : cabinet.billing,
			alias: 'billing',
			
			'/edit' : {
				name: 'Редактировать реквизиты',
				on : cabinet.billingEdit,
				alias: 'billing',
			}
		},
		
		'/history' : {
			name: 'История заказов',
			on : cabinet.history,
			alias: 'history',
			
			'/:id' : {
				name: 'Информация о заказе',
				on : cabinet.historyInfo,
				alias: 'history',
			}
		}
	}
};
