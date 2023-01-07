<?php

function makeGrayPic($filename, $resultName)
{
    // получаем размеры исходного изображения
    $imgSize = getimagesize($filename);
    $width = $imgSize[0];
    $height = $imgSize[1];

    // создаем новое изображение
    $img = imageCreate($width, $height);

    // задаем серую палитру для нового изображения
    for ($color = 0; $color <= 255; $color++) {
        imageColorAllocate($img, $color, $color, $color);
    }

    // создаем изображение из исходного
    $img2 = imageCreateFromJpeg($filename);

    // объединяем исходное изображение и серое
    imageCopyMerge($img, $img2, 0, 0, 0, 0, $width, $height, 100);

    // сохраняем изображение
    imagejpeg($img, $resultName);

    // очищаем память
    imagedestroy($img);
}
//  // пример использования 
//  makeGrayPic('test.jpg', 'testGray.jpg');

/**
 * получить список и интенсивность черного в точках в изображении (изображение jpg ч/б)
 */
function getListGrayPoints($filename)
{

    $im = imageCreateFromJpeg($filename);

    $r = [];

    for ($x = 0; $x <= 100; $x++) {
        for ($y = 0; $y <= 100; $y++) {

            // Получаем RGB точки
            $rgb = imagecolorat($im, $x, $y);
            // Получаем массив значений RGB
            $colors = imagecolorsforindex($im, $rgb);

            // echo '<pre>', print_r($rgb), '</pre>';
            // echo '<pre>', print_r($colors), '</pre>';
            $r[$x][$y] = $colors['red'];

        }
    }

    imagedestroy($im);

    return $r;
}


function makePreview()
{

    // // получаем размеры исходного изображения
    // $imgSize = getimagesize($filename);
    // $width = $imgSize[0];
    // $height = $imgSize[1];

    // // создаем новое изображение
    // $img = imageCreate($width, $height);

    // // задаем серую палитру для нового изображения
    // for ($color = 0; $color <= 255; $color++) {
    //     imageColorAllocate($img, $color, $color, $color);
    // }

    // // создаем изображение из исходного
    // $img2 = imageCreateFromJpeg($filename);

    // // объединяем исходное изображение и серое
    // imageCopyMerge($img, $img2, 0, 0, 0, 0, $width, $height, 100);

    // // сохраняем изображение
    // imagejpeg($img, $resultName);

    // // очищаем память
    // imagedestroy($img);
    
}
