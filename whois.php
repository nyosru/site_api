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

use Iodev\Whois\Factory;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;


if (isset($_REQUEST['return']) && $_REQUEST['return'] == 'json') {
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


if (!empty($_REQUEST['domain']) && isset($_REQUEST['return']) && $_REQUEST['return'] == 'json') {

    $return = ['status' => 0];

    // require_once('./whois-r01/soap_action_checkdomainavailable2.php');
    // ssoap::start();
    // ssoap::login('BAklAnOvSS','123_Uralweb');

    // exec("whois ".$domain,$output,$return_var); 
    // exec("whois ".$_REQUEST['domain'],$output,$return_var); 

    // foreach ($output as $line){
    // echo $line.'<br>';
    // }


    // // Creating default configured client
    // $whois = Factory::get()->createWhois();

    // // Checking availability
    // if ($whois->isDomainAvailable("google.com")) {
    //     print "Bingo! Domain is available! :)";
    // }

    // // Supports Unicode (converts to punycode)
    // if ($whois->isDomainAvailable("почта.рф")) {
    //     print "Bingo! Domain is available! :)";
    // }

    // // Getting raw-text lookup
    // $response = $whois->lookupDomain("google.com");
    // print $response->text;

    // // Getting parsed domain info
    // $info = $whois->loadDomainInfo("google.com");
    // print_r([
    //     'Domain created' => date("Y-m-d", $info->creationDate),
    //     'Domain expires' => date("Y-m-d", $info->expirationDate),
    //     'Domain owner' => $info->owner,
    // ]);


    try {
        $whois = Factory::get()->createWhois();

        $return['domain'] = $_REQUEST['domain'];
        $return['status'] = 1;

        if ($whois->isDomainAvailable($_REQUEST['domain'])) {
            $return['available'] = true;
            // print "Bingo! Domain is available! :)";

            if (!empty($_REQUEST['sendTelegramm'])) {
                file_get_contents('https://api.php-cat.com/telegram.php?' . http_build_query(
                    array(
                        // order ura bot
                        'token' => '5960307100:AAHshaEf6WXw4rKbDg-JCeAyOEsFoHqZmNA',
                        's' => '1',
                        // 'id' => $to, // id кому пишем
                        'msg' => 'Домен ' . $_REQUEST['domain'] . ' доступен дял регистрации' // текст сообщения
                    )
                ));
            }
            
        } else {
            $return['available'] = false;
            // print "NO Bingo! Domain is unavailable! :)";

        }

        if (1 == 2) {
            // $info = $whois->loadDomainInfo("google.com");
            $info = $whois->loadDomainInfo($_REQUEST['domain']);
            if (!$info) {
                print "Null if domain available";
                exit;
            }

            print $info->domainName . " expires at: " . date("d.m.Y H:i:s", $info->expirationDate);
            // echo '<pre>', print_r($info), '</pre>';
        }
    } catch (ConnectionException $e) {
        print "Disconnect or connection timeout";
    } catch (ServerMismatchException $e) {
        print "TLD server (.com for google.com) not found in current server hosts";
    } catch (WhoisException $e) {
        print "Whois server responded with error '{$e->getMessage()}'";
    }


    die(json_encode($return));
}

die(__FILE__ . ' #' . __LINE__);
