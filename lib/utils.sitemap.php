<?php 

include $core->CONFIG['module_path'].$core->siteObject['custom_preloader'].'/client.shop.php';
$core->shop = new client_shop();




$sitemap = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
$sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
$domain = $_SERVER['SERVER_NAME'];


$sql = "SELECT page_id as id, get_rewrite(rewrite_id) as rewrite, '' as url, create_date as date, last_update_date FROM site_pages WHERE site_id = {$core->shop->shopId} AND hidden = 0 ORDER BY parent_id";

$core->db->query($sql);
$core->db->filter('url', $core->shop->filters['page_url']);
$core->db->filter('date', function($field) {
    return date('c', $field);
});
$core->db->filter('last_update_date', function($field) {
    return date('c', $field);
});
$core->db->get_rows();
$pages = $core->db->rows;

foreach($pages as $item) {
    $sitemap .= '<url>'."\n";
    $sitemap .= '<loc>http://'.$domain.$item['rewrite'].'</loc>'."\n";
    $sitemap .= '<lastmod>'.$item['last_update_date'].'</lastmod>'."\n";
    $sitemap .= '<changefreq>weekly</changefreq>'."\n";
    $sitemap .= '</url>'."\n";
}


$sql = "SELECT group_id as id, get_rewrite(rewrite_id) as rewrite, '' as url FROM tp_product_group WHERE client_id = {$core->shop->clientId} AND shop_id = {$core->shop->shopId} AND hidden = 0";
$core->db->query($sql);
$core->db->filter('url', $this->filters['category_url']);
$core->db->get_rows();
$category = $core->db->rows;

foreach($category as $item) {
    $sitemap .= '<url>'."\n";
    $sitemap .= '<loc>http://'.$domain.$item['rewrite'].'</loc>'."\n";
    $sitemap .= '<changefreq>weekly</changefreq>'."\n";
    $sitemap .= '</url>'."\n";
}

$sql = "SELECT product_id as id, get_rewrite(rewrite_id) as rewrite, '' as url FROM tp_product WHERE client_id = {$core->shop->clientId} AND shop_id = {$core->shop->shopId} AND status_id = 1 AND parent_id = 0";
$core->db->query($sql);
$core->db->filter('url', $core->shop->filters['product_url']);
$core->db->get_rows();
$category = $core->db->rows;


foreach($category as $item) {
    $sitemap .= '<url>'."\n";
    $sitemap .= '<loc>http://'.$domain.$item['url'].'</loc>'."\n";
    $sitemap .= '<changefreq>weekly</changefreq>'."\n";
    $sitemap .= '</url>'."\n";
}

$sitemap .= '</urlset>';

header('Content-Type: text/xml; charset=utf-8');
echo $sitemap;
?>