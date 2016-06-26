<?php 


include $core->CONFIG['module_path'].$core->siteObject['custom_preloader'].'/client.shop.php';
$core->shop = new client_shop();

error_reporting(E_ALL);
ini_set('display_errors',1);

if(!empty($_POST['category']) && intval($_POST['category'])) {
    define('use_modifications', true);
    $groupId = intval($_POST['category']);
    $page = (!empty($_POST['page']))? intval($_POST['page']) : 0;
    $limit = 999999;
    $products = $core->shop->getProducts($groupId,($page*$limit),$limit);
    $products = $products[0];
    
    $iconvconvert = function($str) {
        return iconv('utf-8', 'cp1251', $str);
    };
    
    $newiconvert = function($str) {
        //return htmlentities(iconv("utf-8", "windows-1251", $str),ENT_QUOTES, "cp1251");
        return $str;
    };
    
    $header = array(
        $newiconvert('Артикул'),
        $newiconvert('Название'),
        $newiconvert('Обозначение'),
        $newiconvert('Размеры / Модификация'),
        $newiconvert('Цена 1'),
        $newiconvert('Цена 2'),
        $newiconvert('Цена 3'),
        $newiconvert('Цена 4'),
        $newiconvert('Упаковка')
    );
    
    $filename = 'price.rusconnect.'.date('d.m.Y').'-'.$groupId.'.xls';
    $f = fopen(CORE_PATH.'vars/price_export/'.$filename, 'w+');
    $data = array('header'=>$header, 'rows' => array());
    
    

    //fputcsv($f, array_map($iconvconvert, $header));
    
    foreach($products as $item) {
        if(!empty($item['mod'])) {
            $mods = $core->shop->getModifications($item['id']);
            foreach($mods as $entry) {
                $row = array(
                    $newiconvert($entry['article']),
                    $newiconvert($item['title']),
                    $newiconvert($entry['title']),
                    $newiconvert($entry['description']),
                    $newiconvert($entry['price']),
                    $newiconvert($entry['price2']),
                    $newiconvert($entry['price3']),
                    $newiconvert($entry['price4']),
                    $newiconvert($entry['pack_size'] . ' ' .$entry['pack_unit'])
                );

                //fputcsv($f, array_map($iconvconvert, $row));
                $data['rows'][] = $row;
            }
        } else {
            $row = array(
                $newiconvert($item['article']),
                $newiconvert($item['title']),
                '',
                '',
                $newiconvert($item['price']),
                $newiconvert($item['price2']),
                $newiconvert($item['price3']),
                $newiconvert($item['price4']),
                $newiconvert($item['pack_size'] . ' ' .$item['pack_unit'])
            );
            
            //fputcsv($f, array_map($iconvconvert, $row));
            $data['rows'][] = $row;
        }
        
    }
    
    $core->setTheme();
    $core->langId = 1;
    $core->tpl->assign('tbldata', $data);
    $tpl = $core->tpl->get('ExportXlsTemplate.html', 'shop');
    //$tpl = iconv('utf-8', 'cp1251', $tpl);
    
    fwrite($f, $tpl);
    
    fclose($f);
    
    $info = '/vars/price_export/'.$filename;

    if(empty($info)) {
        echo json_encode(array('status'=>'error', 'message'=>'uknown operation', 'error'=>true));
    } else {
        echo json_encode(array('status'=>'ok', 'data'=>$info, 'error'=>false));
    }
   
} else {
	echo json_encode(array('status'=>'error', 'message'=>'uknown operation', 'error'=>true));
}



die();
?>

