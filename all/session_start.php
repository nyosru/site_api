<?php

if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/0.cash'))
    mkdir($_SERVER['DOCUMENT_ROOT'] . '/0.cash', 0755);

session_set_cookie_params(86400);
ini_set("session.gc_maxlifetime", 86400);


if (1 == 2 && $_SERVER['HTTP_HOST'] == 'invest.uralweb.info' || $_SERVER['HTTP_HOST'] == 'limon-invest.ru') {

    $sp = $_SERVER['DOCUMENT_ROOT'] . '/0.cash/sessions-limon';

} else {
    
    $sp = $_SERVER['DOCUMENT_ROOT'] . '/0.cash/sessions-all';
    
}

if (!is_dir($sp))
    mkdir($sp, 0755);

ini_set('session.save_path', $sp);

session_start();
$_sstart = true;

// заглушка если id есть но в data его нет
if (isset($_SESSION['now_user']['id']{0}) && !isset($_SESSION['now_user']['data']['id']{0}))
    $_SESSION['now_user']['data']['id'] = $_SESSION['now_user']['id'];

