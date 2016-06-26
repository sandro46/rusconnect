<?php
############################################################################
#          This controller was created automatically core system           #
#                                                                          #
# ------------------------------------------------------------------------ #
# @Creator module version 1.2598 b                                         #
# @Author: Alexey Pshenichniy                                              #
# ------------------------------------------------------------------------ #
# Alpari CMS v.1 Beta   $17.06.2008                                        #
############################################################################


$controller->id = 38;
$controller->cached = 0;
$controller->init();

$controller->load('resizer.php');


if(empty($_POST) || empty($_POST['image'])) {
    echo json_encode(array('error'=>1, 'message'=>'Image not found'));
    die();
}

if(!isset($_POST['x']) || !isset($_POST['y']) || empty($_POST['width']) || empty($_POST['height'])) {
    echo json_encode(array('error'=>2, 'message'=>'Crop parameters not set!'));
    die();
}

if(!file_exists(CORE_PATH.$_POST['image'])) {
    echo json_encode(array('error'=>3, 'message'=>'Image not exists'));
    die();
}

$core->lib->load('images');
$core->images = new images();

if(images::checkMime(CORE_PATH.$_POST['image']) !== true) {
    echo json_encode(array('error'=>4, 'message'=>'File is not image'));
    die();
}

$core->images->fname = CORE_PATH.$_POST['image'];
if($core->images->open() === false) {
    echo json_encode(array('error'=>5, 'message'=>'Image file can not be opened'));
    die();
}


$destination = 'cropped-'.basename(CORE_PATH.$_POST['image']);
$destinationLocal = '/vars/files/images/'.$destination;
$destinationFull = CORE_PATH.'vars/files/images/'.$destination;

$operations = array();

if(!empty($_POST['rotate']) && floatval($_POST['rotate']) != 0) {
    $degrees = floatval($_POST['rotate']);
    $degrees = ($degrees < 0)? abs($degrees) : -$degrees;
    $imgsize = array(
        'width'=>$core->images->info['width'],
        'height'=>$core->images->info['height'],
    );
    $rotationres = $core->images->rotate($degrees);
    $state1 = ($rotationres !== false)? true : false;
    $core->images->writeFile($rotationres, $destinationFull, 100);
    $core->images->close(false);
    $core->images->fname = $destinationFull;
    $state2 = $core->images->open();
    
    $operations[] = array(
        'rotate'=>floatval($_POST['rotate']),
        'result'=>$state1,
        'post'=>$state2,
        'old_size'=>$imgsize,
        'new_size'=>array(
            'width'=>$core->images->info['width'],
            'height'=>$core->images->info['height'],
        )
    );
}

$result = $core->images->crop(floatval($_POST['x']), floatval($_POST['y']), floatval($_POST['width']), floatval($_POST['height']));

$operations[] = array(
    'crop'=>array(floatval($_POST['x']), floatval($_POST['y']), floatval($_POST['width']), floatval($_POST['height'])),
    'result'=>($result !== false)? true : false
);

if($result === false) {
    echo json_encode(array('error'=>6, 'message'=>'Crop fail', 'log'=>$operations));
    die();
} 

$core->images->writeFile($result, $destinationFull, 100);
$core->images->close(false);

//

if(!empty($_POST['resize']) && is_array($_POST['resize'])) {
    $operations[] = array(
        'resize'=>$_POST['resize']
    );
    
    $result = modernUploaderProcess(array('name'=>$destinationFull), array(
        'maxsize'=>false,
        'resize'=>$_POST['resize'],
        'alradyuploaded'=>true
    ));
    
    $result['log'] = $operations;
    
    echo json_encode($result);
    die();
} else {
    echo json_encode(array('status'=>'ok', 'fname'=>$destinationLocal, 'log'=>$operations));
    die();
}


echo json_encode(array('status'=>'ok', 'fname'=>$destinationLocal, 'log'=>$operations));
die();



?>