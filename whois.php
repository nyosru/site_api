<?php

date_default_timezone_set("Asia/Yekaterinburg");

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: POST,GET,OPTION");
header("Access-Control-Allow-Headers: *");

if (!defined('IN_NYOS_PROJECT'))
    define('IN_NYOS_PROJECT', TRUE);

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php'))
    require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

require_once('./whois-r01/soap.class.php');


if (isset($_GET['return']) && $_GET['return'] == 'json') {
} else {

    echo '<h2>Whois</h2>' .
        '<form action="" method="GET" >' .
        'Домен <input type="text" name="domain" value="" />' .
        '<br/>' .
        'показать ответ в формате JSON <input type="checkbox" name="return" value="json" />' .
        '<br/>' .
        '<button type="submit" >Отправиить</button>' .
        '</form>';
}


if (isset($_GET['return']) && $_GET['return'] == 'json') {

    $return = [ 'status' => 0 ];

    // require_once('./whois-r01/soap_action_checkdomainavailable2.php');

    ssoap::start();
    ssoap::login('BAklAnOvSS','123_Uralweb');

    die(json_encode($return));

}

die(__FILE__ . ' #' . __LINE__);

