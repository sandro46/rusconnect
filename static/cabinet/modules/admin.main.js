$(document).ready(function(){




});


function changeEditSite(siteId) {
	$page.lock();
	globalApi.changeSiteEditId(siteId,function(){
		$page.unlock();
		$page.sticky('Смена редактируемого сайта','Вы изменили сайт который сейчас редактируете!');
		setTimeout(function(){
			document.location.href = 'http://my.ncity.biz/ru/shop/order/';
		}, 1000);
	});
	
}