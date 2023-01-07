<?php

//echo '<br/>'.__FILE__.' '.__LINE__;

$status = '';



/**
 * корень сервера/папкасайта/
 */
//define('DirSite', $_SERVER['DOCUMENT_ROOT'] . DirSite0);
/**
 * корень сервера/папкасайта/template/
 */
define('DirSiteTpl', DirSite . 'template' . DS);
//$vv['DirSiteTpl'] = DirSite . 'template' . DS;









/*

  define('Dir9Site', Dir . '9.site' . DS);
  define('Dir0Site', Dir . '0.site' . DS);
  define('DirCron', Dir . DS . '0.cron' . DS);
  define('DirCash', Dir . '0.cash' . DS);
  define('DirZip', Dir . '0.zip' . DS);
  define('DirDidrive', Dir . '0.didrive' . DS);
 */

// define( 'level', ( isset($_GET['level']) ) ? $_GET['level'] : '000.index' );
// echo '<pre>'; print_r($_glob2); echo '</pre>';
// require_once( Dir0Site.'0.cfg.php' );


if (isset($_REQUEST['level']) && isset(\Nyos\Nyos::$a_menu[$_REQUEST['level']])) {
    $vv['level'] = $_REQUEST['level'];
} else {
    $vv['level'] = '000.index';
}

// echo '<br/>'.__FILE__.' #'.__LINE__;
// $vv['level'] = $_REQUEST['level'];
// \f\pa(\Nyos\Nyos::$a_menu);

//\Nyos\Nyos::getMenu();
 
// активный модуль
//$vv['now_mod'] = \Nyos\Nyos::$a_menu[$vv['level']];
//\f\pa($vv['now_mod'], 2);

//
///**
// * папка модуля на сайте /папка сайта/module/=модуль=/
// */
//define('dir_mod_site', DirSite . 'module' . DS . $vv['now_mod']['cfg.level'] . DS);
///**
// * папка модуля на сайте /папка сайта/module/=модуль=/tpl/
// */
//define('dir_mod_site_tpl', DirSite . 'module' . DS . $vv['now_mod']['cfg.level'] . DS . 'tpl' . DS);
///**
// * ссылка на папку шаблонов дидрайва внутри модуля на сайте /папка сайта/module/=модуль=/tpl.di/
// */
//define('dir_mod_site_di_tpl', dir_mod_site . 'tpl.di' . DS);
///**
// * папка модуля в модулях /модули/=модуль=/
// */
//define('dir_mod', DS . 'module' . DS . $vv['now_mod']['type'] . DS);
///**
// * папка модуля c версией в модулях /модули/=модуль=/=версия=/
// */
//define('dir_mod_ver', DS . 'module' . DS . $vv['now_mod']['type'] . DS . $vv['now_mod']['version'] . DS);
///**
// * ссылки на дидрайв внутри модуля /модули/=модуль=/=версия=/didrive/
// */
//define('dir_mod_didr', dir_mod_ver . 'didrive' . DS);
///**
// * папка шаблонов ( дидрайв в модулях ) с корня сайта /модули/=модуль=/=версия=/didrive/t/
// */
//define('dir_mod_didr_tpl', dir_mod_ver . 'didrive' . DS . 't' . DS);
