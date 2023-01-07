<?php

date_default_timezone_set("Asia/Yekaterinburg");

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

if (isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] == '123') {
    header('Content-Type: text/plain');
    die($_GET['hub_challenge']);
}

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

if (isset($_REQUEST)) {
    file_put_contents('fb.' . date('YmdHis') . '.txt', json_encode($_REQUEST));
}

$env = parse_ini_file('./.env');

$fb = new \Facebook\Facebook([
    'app_id' => $env['FACEBOOK_app_id'],
    'app_secret' => $env['FACEBOOK_app_secret'],
    'default_graph_version' => 'v2.10',
        //'default_access_token' => '{access-token}', // optional
        ]);

try {
    // Returns a `FacebookFacebookResponse` object
    $response = $fb->get(
            '/me', $env['FACEBOOK_resp']
            // '{access-token}'
    );
} catch (FacebookExceptionsFacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch (FacebookExceptionsFacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}
$graphNode = $response->getGraphNode();

