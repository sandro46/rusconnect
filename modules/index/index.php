<?php

		$loadIndex = true;
		$dashboard = false;
		$dashboardButtons = false;
		
		$core->tpl->assign('index_page', true);
		
		if($core->site_type == 2) {
			$core->tpl->assign('title', 'рабочий стол');
			$core->tpl->assign('is_index',true);
		} 
		
		if ($core->site_type == 3) {
			$core->tpl->assign('index_page', true);
			$core->tpl->assign('feeds', $core->shop->getFeeds());
			$core->tpl->assign('feedsProduct', $core->shop->getProductByFeeds());
			
			$core->content = $core->tpl->get('index.html', 'Frontend');
			
			$core->tpl->assign('meta_description', $core->shop->shopInfo['meta_description']);
			$core->tpl->assign('meta_keywords', $core->shop->shopInfo['meta_keywords']);
			$core->title = $core->shop->shopInfo['meta_title'];
		}
		
		if($core->site_id == 1){
			if($core->user->id == 1) {
				$core->content = $core->tpl->get('index-root.html', $core->getAdminModule());
			} else {
				$core->content = $core->tpl->get('index-user.html', $core->getAdminModule());
			}
		} else if($core->site_id == 2) {
			$api = new main_module();
		} 



		
?>
