<?php 

$order['nextstep_action'] = 'javascript:void(0)';
$order['nextstep_click'] = "printFrameForm()";


$setting = array(
    'company_name'=>'ООО "Интерсервис"',
    'bank_account_number'=>'40702810397660000109',
    'inn'=>'5010046405',
    'kpp'=>'501001001',
    'bank_name'=>'Московский филиал ОАО АКБ "РОСБАНК"',
    'bank_kor_number'=>'30101810000000000272',
    'bik'=>'044583272'
);

$core->tpl->assign('settings', $setting);
$core->tpl->assign('date', date('d.m.Y'));

$tpl = $core->tpl->get('payform.fl.html', 'shop');
$_SESSION['_printFormHtml'] = $tpl;

$core->tpl->assign('payFormResult', '<script type="text/javascript">function printFrameForm() {  window.open(\'/ru/shop/printform/\', \'Печать квитанции\', \'width=800, height=500\'); }</script>');



?>