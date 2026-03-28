<?php

/**
 * работа с секретами
 */
//creatSecret
$function = new Twig_SimpleFunction('creatSecret', function ( $text = '' ) {
    return \Nyos\Nyos::creatSecret($text ?? '');
});
$twig->addFunction($function);

//checkSecret
$function = new Twig_SimpleFunction('checkSecret', function ( string $secret, string $text) {
    return \Nyos\Nyos::checkSecret($secret, $text);
});
$twig->addFunction($function);

//item_dir_img_uri_download
$function = new Twig_SimpleFunction('item_dir_img_uri_download', function ( ) {
    return \Nyos\mod\items::$dir_img_uri_download;
    // return \Nyos\Nyos::checkSecret($secret, $text);
});
$twig->addFunction($function);

//item_getItems
//$function = new Twig_SimpleFunction('item_getItems', function ( $db, string $folder, $mod, $status = 'show', $lim = null ) {
//    return \Nyos\mod\items::getItems( $db, $folder, $mod, $status, $lim );
//});
//$twig->addFunction($function);
//pa







/**
 * Пример использования
 */
/*
  {% set ss = creatSecret(123456) %}
  <br/>
  {{ ss }}
  <br/>
  {% if checkSecret(ss,123456) == true %}
  111
  {% else %}
  222
  {% endif %}
 */





$function = new Twig_SimpleFunction('dir_mod_inf', function ( string $module, int $ver, $file = null ) {

    \Nyos\Nyos::getMenu();
    // \f\pa(\Nyos\Nyos::$folder_now);
    // \f\pa(\Nyos\Nyos::$menu);
    foreach (\Nyos\Nyos::$menu as $k => $v) {
        if (isset($v['type']) && $v['type'] == 'lk') {

            if (
                    (!empty($file) && file_exists(DR . DS . 'sites' . DS . \Nyos\Nyos::$folder_now . DS . 'module' . DS . $k . DS . 'tpl.inf' . DS . $file . '.htm') ) ||
                    ( empty($file) && is_dir(DR . DS . 'sites' . DS . \Nyos\Nyos::$folder_now . DS . 'module' . DS . $k . DS . 'tpl.inf' . DS) )
            ) {
                return DS . 'sites' . DS . \Nyos\Nyos::$folder_now . DS . 'module' . DS . $k . DS . 'tpl.inf' . DS;
            }
        }
    }

    return '/vendor/didrive_mod/' . $module . '/' . $ver . '/tpl.inf/';
});
$twig->addFunction($function);




$function = new Twig_SimpleFunction('http_build_query', function ( array $ar ) {
    return http_build_query($ar);
});
$twig->addFunction($function);
