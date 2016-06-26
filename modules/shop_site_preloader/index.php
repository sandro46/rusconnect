<?php 


$core->loader->load('client.shop.php', $this->core->siteObject['custom_preloader']);
$core->shop = new client_shop();
$core->shop->checkAuthUser();


$core->tpl->replaceFindTplSiteName = 'Frontend';
$core->tpl->assign('category_id',false);
$core->tpl->assign('show_sidebar',true);
$core->tpl->assign('show_content_container',true);
$core->tpl->assign('cart_summary', $core->shop->getCartSummary());

if(defined('MAINTENANCE') && MAINTENANCE) {
    $core->tpl->assign('content', $core->tpl->get('maintenances.html', 'Cabinet'));
    $core->module_name = ($this->core->is_admin())? $core->getAdminModule() : $core->site_name;
    $core->tpl->assign('title', 'Технические работы.');
    $core->tpl->display($core->main_tpl);
    die();
}

require_once CORE_PATH . 'plugins/unisender/request_limiter.php';
$csrf = new RequestLimiter();
$csrf->setCSRFtoken(function($token) use($core){
    $core->tpl->assign('csrftoken',$token);
    
});

//$core->loader->load('tracker.php', 'analytics');
//$core->tpl->assign('compare', $core->shop->getCompareSummary());

//$tracker = new mAnalyticTracker();
//$tracker->init();
//$core->tpl->assign('ncityAnalyticModule', $tracker->getCounterCode());





?>