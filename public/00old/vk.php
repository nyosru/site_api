<?php

$envPath =  $_SERVER['DOCUMENT_ROOT'] . '/.env';
if (is_file($envPath) && is_readable($envPath)) {
    $envValues = parse_ini_file($envPath, false, INI_SCANNER_RAW);
    if (is_array($envValues)) {
        foreach ($envValues as $name => $value) {
            if (!is_string($name) || $name === '') {
                continue;
            }
            if (!is_scalar($value)) {
                continue;
            }
            $value = (string)$value;
            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

function envValue(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }
    return $value;
}

function requestValue(string $key, ?string $default = null): ?string
{
    if (isset($_POST[$key])) {
        return (string)$_POST[$key];
    }
    if (isset($_GET[$key])) {
        return (string)$_GET[$key];
    }
    if (isset($_REQUEST[$key])) {
        return (string)$_REQUEST[$key];
    }
    return $default;
}

date_default_timezone_set("Asia/Yekaterinburg");

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

//require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

try {


    $token = envValue('VK_TOKEN_DEFAULT');
    $vkApiVersion = envValue('VK_API_VERSION', '5.199');

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

        $token = envValue('VK_TOKEN_SEND_USER');
        if (!$token) {
            throw new \RuntimeException('VK_TOKEN_SEND_USER is not set in .env');
        }

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

        $query = file_get_contents("https://api.vk.com/method/execute?code=" . urlencode($request) . "&v=" . urlencode($vkApiVersion) . "&access_token=" . $token);

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
        if ($_REQUEST['group'] == 'uralweb_info') {
            $token = envValue('VK_TOKEN_GROUP_URALWEB_INFO');
        }
        if (!$token) {
            throw new \RuntimeException('Token for selected group is not set in .env');
        }

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

        $query = file_get_contents("https://api.vk.com/method/execute?code=" . urlencode($request) . "&v=" . urlencode($vkApiVersion) . "&access_token=" . $token);

        // $result = json_decode($query, true);
        die($query);
    }
    // новый endpoint: отправка сообщения от группы по vk_id
    // параметры: endpoint=send_group_msg, vk_id, msg, s
    // подпись: md5('send_group_msg' . vk_id . msg)
    elseif (
        requestValue('endpoint') === 'send_group_msg'
        && requestValue('vk_id') !== null
        && requestValue('msg') !== null
        && requestValue('s') !== null
    ) {

        $vkId = trim((string)requestValue('vk_id', ''));
        $msg = trim((string)requestValue('msg', ''));
        $signature = (string)requestValue('s', '');

        if (!preg_match('/^\d+$/', $vkId)) {
            header('Content-Type: application/json; charset=utf-8');
            die(json_encode(['error' => 'vk_id должен быть числом'], JSON_UNESCAPED_UNICODE));
        }
        if ($msg === '') {
            header('Content-Type: application/json; charset=utf-8');
            die(json_encode(['error' => 'msg не должен быть пустым'], JSON_UNESCAPED_UNICODE));
        }

        $expectedSignature = md5('send_group_msg' . $vkId . $msg);
//        if (!hash_equals($expectedSignature, $signature)) {
//            header('Content-Type: application/json; charset=utf-8');
//            die(json_encode(['error' => 'Неверная подпись s'], JSON_UNESCAPED_UNICODE));
//        }

        // Preferred defaults for group send endpoint.
        // 1) VK_TOKEN_GROUP_GENERIC + VK_GROUP_ID
        // 2) fallback to legacy uralweb_info config
        $groupId = envValue('VK_GROUP_ID');
        $token = envValue('VK_TOKEN_GROUP_GENERIC');

        if (!$groupId) {
            $groupId = envValue('VK_DEFAULT_GROUP', 'uralweb_info');
        }
        if (!$token && $groupId === 'uralweb_info') {
            $token = envValue('VK_TOKEN_GROUP_URALWEB_INFO');
        }

        if (!$token) {
            throw new \RuntimeException('Group token is not set in .env');
        }
        if (!$groupId) {
            throw new \RuntimeException('VK_GROUP_ID or VK_DEFAULT_GROUP is not set in .env');
        }

        $messageForVk = str_replace(array("\r\n", "\r", "\n"), ' | ', $msg);
        $groupJs = json_encode($groupId, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $vkIdJs = json_encode($vkId, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $messageJs = json_encode($messageForVk, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $request = '
        var a = API.groups.isMember({"group_id":' . $groupJs . ',"user_id":' . $vkIdJs . '});
        if( a == 1 ){
            var msg = API.messages.send({
                "user_id":' . $vkIdJs . ',
                "message":' . $messageJs . ',
                "random_id":"' . date('Ymdhis') . '"
                });
            return { "success":"подписчик, сообщение отправили", "nomer_send_msg":msg };
        }
        return { "error":"Пользователь не подписан на сообщество." };
        ';

        $query = file_get_contents("https://api.vk.com/method/execute?code=" . urlencode($request) . "&v=" . urlencode($vkApiVersion) . "&access_token=" . $token);
        header('Content-Type: application/json; charset=utf-8');
        die($query);
    }

    exit;
} catch (\Exception $ex) {

    echo '<pre>--- ' . __FILE__ . ' ' . __LINE__ . '-------'
    . PHP_EOL . $ex->getMessage() . ' #' . $ex->getCode()
    . PHP_EOL . $ex->getFile() . ' #' . $ex->getLine()
    . PHP_EOL . $ex->getTraceAsString()
    . '</pre>';
}
