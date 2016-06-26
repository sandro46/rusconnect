<?php
################################################################################
# This file was created by M-cms core.                                         #
# If you want create a new controller files,                                   #
# look at modules section in admin interface.                                  #
#                                                                              #
# If you want modify this header, look at /modules/modules/class/modules.php   #
# ---------------------------------------------------------------------------- #
# In this controlle you can use all core api through $core variable            #
# also there is other components api:                                          #
#     $controller = Controller object. Look at /classes/controllers.php        #
#     $ajax = Ajax api object. Look as /classes/ajax.php                       #
# In this file you must to specify the action id and set cached flag           #
# and call ini method.                                                         #
# If you can use template in this controole, please specify variable "tpl".    #
# Example:                                                                     #
#     $controller->id = 1; Controller action id = 1. Look at database.         #
#     $controller->cached = 0; Cache system is off                             #
#     $controller->init(); Call controller initiated method                    #
#     $controller->tpl = 'filename'; Template name.                            #
# You can specify the template in any line of controller, but                  #
# if you want to use caching, you must specify the template to call            #
# the method of checking the cache.                                            #
# If you can break controoler logic, to call $core->footer()                   #
# If you need help, look at api documentation.                                 #
# ---------------------------------------------------------------------------- #
# @Core: 4.205                                                                 #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 01.07.2009                                                            #
# ---------------------------------------------------------------------------- #
# M-cms v4.2 (core build - 4.205)                                              #
################################################################################

//echo 'sdfdsfdsfdsfdsfds';die();

$controller->id = 7;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);
$controller->cache_expire = 30;
$controller->init();
$controller->tpl = 'show.html';
$controller->cached();



$core->title = 'M-cms control panel - '.$core->modules->this['describe'].'';
if(isset($_GET['id']) && intval($_GET['id']) && intval($_GET['id']) == 98){
	$core->tpl->assign('sociallock',true);
}

if($_GET['id'] == 308) {
	$s = "<meta property=\"og:title\" content=\"Вы готовы увеличить свои продажи на 70%?\" />\n<meta property=\"og:image\" content=\"http://www.ncity.biz/vars/files/books/saas_future_of_corporate_market.png\" />\n<meta property=\"og:description\" content=\"Скачайте бесплатно электронную книгу &quot;Saas - будущее корпоративного рынка&quot;, чтобы узнать реальный метод увеличения дохода за счет использования системы электронной коммерции\" />\n";
	$core->tpl->assign('extraMeta', $s);
}


$controller->load('admin.catalog.php');
$root = new admin_catalog();


if(isset($_POST['preview'])){

	$controller->tpl = 'cupon.html';
	$categories = array(0=>'new',1=>'Красота и здоровье',2=>'Рестораны и бары',3=>'Отдых и развлечения',4=>'Товары и услуги',5=>'Афиша',6=>'Мебель');
	$data[0] = array(
			'id' => 266,
			'company_id' => 260,
			'category_id' => 4,
			'title' => $_POST['cupon-title'],
			'description' => $_POST['cupon-description'],
			'text' => $_POST['cupon-text-tmp'],
			'date' => 'Добавлен: '.date('d-m-Y',time()),
			'count' => 0,
			'summ' => $_POST['cupon-summ'],
			'procent' => $_POST['cupon-discont'],
			'cover' => $_POST['imageFilenameCover'],
			'category' => $categories[$_POST['cupon-category']],
			'brand' => 'ЭНСИТИ',
			'cat_link' => '/'.$categories[$_POST['cupon-category']]
			);
	//print_r($data);
	$core->tpl->assign('cupons',$data);
	
}else{



if((isset($_GET['id']) && intval($_GET['id'])) || (isset($_GET['type']) &&$_GET['type'] == 'list')) {	
	if(isset($_GET['type']) && ($_GET['type'] == 'cupon' || $_GET['type'] == 'list')) {
		
		$id = intval($_GET['id']);
	
		$sql = "
			SELECT
				c.id, c.company_id, c.category_id, c.title, c.description, c.text, c.date,
				(SELECT COUNT(*) FROM site_cupon_sended as cs WHERE cs.cupon_id = c.id) as `count`,
				
				
				c.summ, c.procent, c.cover, 
				(SELECT cat.name FROM site_cupon_categories as cat WHERE cat.id = c.category_id) as category,
				(SELECT cc.brand FROM sv_companies as cc WHERE cc.id = c.company_id) as brand
			FROM 
				site_cupons as c
			WHERE 
				c.id = {$id}";
		//print_r($core->cookie);
		if(isset($core->cookie) && is_array($core->cookie) && intval($core->cookie['city_id'])) 
			$sql .= " OR c.region_id = 0 OR c.region_id = ".$core->cookie['city_id'];

		$core->db->query($sql);
		$core->db->get_rows();
		$categories = array(0=>'new',1=>'beauty',2=>'restaurants',3=>'entertainment',4=>'goods',5=>'poster'); 
		foreach ($core->db->rows as $item=>$val){
			
			$core->db->rows[$item]['cat_link'] = '/'.$categories[$core->db->rows[$item]['category_id']];
			$core->db->rows[$item]['date'] = 'Добавлен: '.date('d-m-Y',$core->db->rows[$item]['date']);
		}
		
		$controller->tpl = 'cupon.html';
		
//		/print_r($core->db->rows);
		$list = $core->db->rows;
		if(count($list)>1)
			$core->tpl->assign('isList',true);
		//print_r($core->db->rows);
		$core->tpl->assign('cupons',$core->db->rows);
		
	} else {
		if(isset($_GET['id'])) {
			$data = $root->get(intval($_GET['id']));
	
			if($data['id'] == 43) {
				$controller->tpl = 'news-list.html';
			}
			
			if($data['id'] == 44) {
				$controller->tpl = 'adv-list.html';
			}
			
			if($data['id'] == 45) {
				$controller->tpl = 'evn-list.html';
			}
		
			
			
			if($data['id']==306 || $data['parent_id']==306){

				$core->tpl->assign('capabilities_mode',true);
				
				if($data['id']==306){
					$core->tpl->assign('right_side', $data);
				}else{ 
					$core->tpl->assign('right_side', $root->get(306, $data['id']));
				}
			}
			
		 	if($data['id']==773 || $data['parent_id']==773){
		 		$aaa = array_reverse($data);
		 		$core->tpl->assign('page',$aaa);
				$core->tpl->assign('capabilities_mode',true);
				
				if($data['id']==773){
					$core->tpl->assign('right_side', array_reverse($data));
				}else{ 
					$core->tpl->assign('right_side', array_reverse($root->get(773, $data['id'])));
				}
			}else{
				$core->tpl->assign('page',$data);
			}
			$core->tpl->assign('crumbs', $root->getCrumbs(intval($_GET['id'])));
			
		}
				
	}
	
	
	
	
}


}

?>
