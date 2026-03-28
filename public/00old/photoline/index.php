<?php

echo '<form action="" method="POST" enctype="multipart/form-data" >';
echo '<input type="file" name="photo" ><br/>';
echo '<input type="submit" >';
echo '</form>';

if (!empty($_FILES['photo'])) {

    require 'class.php';

    echo '<pre style="font-size: 10px;" >', print_r($_FILES['photo']), '</pre>';
    //echo __DIR__;
    $nn = date('Ymdhis') . rand(654) . '.jpg';
    $fnn = __DIR__ . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . $nn;
    makeGrayPic($_FILES['photo']['tmp_name'], $fnn);

    $ar = getListGrayPoints($fnn);
    echo '<pre style="font-size: 10px; max-height: 150px; overflow: auto;" >', print_r($ar), '</pre>';

    echo '<img src="/photoline/photos/' . $nn . '" />';
}
