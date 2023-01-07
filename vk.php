<?php

date_default_timezone_set("Asia/Yekaterinburg");

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

//require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
// my token
//$token = "381744869:AAGADX_OJ_bMq_HUxgnJLhOGd1C66ijvwxU";
//$token = "776541435:AAFmtzTet9wA4_7lxunGNJyEKmCrCNyFnnw";
//$token = "776541435:AAGCGoo5KA8yeHfX761_ynffUpNNjt7gRqc";
//$token = "776541435:AAGCGoo5KA8yeHfX761_ynffUpNNjt7gRqc";
//$bot = new \TelegramBot\Api\Client($token);

try {


// серг бакл
    $token = 'e61748dc23779d7eb2ff24e6858f290bf2269dcfeeefe699f66b383967f7552b051e06f2e407054fa0f5a';
// успале
//    $token = '11dc7839d6ad4a0f5d46771dbd713220725889096f67faadce9866bf5ed8a4e3cd1fc0dc469b0d1e07167';
//    $token = '0e8292db7337123413e42303c5f90c534f1ef133cd7af9c2a357b153fc3f7105c71dfb559e04c2220793e';
    $token = '5c1bcfc5f24aab710f4a5893073d733b6d55b4e00d093c4ab8a0a3f2c3428bbe799e54a7a01b4460a5845';

//    $response = $vk->execute()->get($access_token, array(
//        'code' => '
//        var a = API.friends.getareFriends({"user_ids":"300304140"});
//        return a;
//        '));
//    $request = '
//        var a = API.friends.areFriends({"user_ids":"5903492","need_sign":"0"});
//        
//        if( a[0].friend_status < 2 ){
//            return { "error":"Мы не друзья, послать сообщение не получилось." };
//        }else{
//            var msg = API.messages.send({"user_id":"5903492","message":"Привет дружок","random_id":"'.date('Ymdhis').'"});
//            return { "result":"окей друзья", "msgs":msg };
//        }
//        
//        ';
// отправка от чела челу

    if (2 == 1 || ( isset($_REQUEST['from_user']) && isset($_REQUEST['to_user']) && isset($_REQUEST['s']) && $_REQUEST['s'] == md5('send' . $_REQUEST['from_user'] . $_REQUEST['to_user']) )) {

        // usplae
        $token = 'd59c29e5384c73fc4accdfc8931137f674b63b41591ce40f1a5a033e0b69f7965e051db83a5c70f4e73ca';

        // $vk_id_to_msg = '502668186';
        //$vk_id_to_msg = $_REQUEST['to_user'];

        $vk_id_to_msg = '5903492';

        $request = '
        var a = API.friends.areFriends({"user_ids":"' . $vk_id_to_msg . '"});
        
        //return a;

        if( a[0].friend_status > 1 ){

            var msg = API.messages.send({
                "user_id":"' . $vk_id_to_msg . '",
                "message":"Привет дружок",
                "random_id":"' . date('Ymdhis') . '"
                });
            return { "success":"друг, сообщение отправили", "nomer_send_msg":msg };

        }

        return { "error":"Мы не друзья, послать сообщение не получилось." };

';

        // echo '<pre>'.$request.'</pre>';

        $query = file_get_contents("https://api.vk.com/method/execute?code=" . urlencode($request) . "&v=5.8&access_token=" . $token);

        echo '<pre>';
        print_r(json_decode($query, true));
        echo '</pre>';
        // $result = json_decode($query, true);
        die();
        die($query);
    }

// отправка от группы пользователю
    elseif (isset($_REQUEST['msg']) && isset($_REQUEST['group']) && isset($_REQUEST['to_user']) && isset($_REQUEST['s']) && $_REQUEST['s'] == md5('send' . $_REQUEST['group'] . $_REQUEST['to_user'] )) {

// сообщество // uralweb_info
        if ($_REQUEST['group'] == 'uralweb_info')
            $token = '037ef3f776cffc018c9a738f4513317f865ca2e8c4dc0d914ee0da310491fea1c9f17ce7fa4f148dc0a0b';

// $vk_id_to_msg = '502668186';
        $vk_id_to_msg = $_REQUEST['to_user'];

        $request = '
        var a = API.groups.isMember({"group_id":"' . $_REQUEST['group'] . '","user_id":"' . $vk_id_to_msg . '"});
        if( a == 1 ){
            var msg = API.messages.send({
                "user_id":"' . $vk_id_to_msg . '",
                "message":"' . str_replace(PHP_EOL,' | ',$_REQUEST['msg']) . '"
                });
            return { "success":"подписчик, сообщение отправили", "nomer_send_msg":msg };
        }
        return { "error":"Мы не друзья, послать сообщение не получилось." };
        ';

        // echo '<pre>'.$request.'</pre>';
        //$request = '11111111';

        $query = file_get_contents("https://api.vk.com/method/execute?code=" . urlencode($request) . "&v=5.8&access_token=" . $token);

        // $result = json_decode($query, true);
        die($query);
    }

    //echo '<pre>';
    //print_r($result);
    //echo '</pre>';

    exit;
} catch (\Exception $ex) {

    echo '<pre>--- ' . __FILE__ . ' ' . __LINE__ . '-------'
    . PHP_EOL . $ex->getMessage() . ' #' . $ex->getCode()
    . PHP_EOL . $ex->getFile() . ' #' . $ex->getLine()
    . PHP_EOL . $ex->getTraceAsString()
    . '</pre>';
}