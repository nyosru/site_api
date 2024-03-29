<?php

date_default_timezone_set("Asia/Yekaterinburg");

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: POST,GET,OPTION");
header("Access-Control-Allow-Headers: *");


if (!defined('IN_NYOS_PROJECT'))
    define('IN_NYOS_PROJECT', TRUE);

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php'))
    require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';




// входящие данные
$v1 = file_get_contents("php://input");
$array =
    $v = json_decode($v1, true);

$env = parse_ini_file('./.env');

$token = $array['token'] ?? $_GET['token'] ?? $env['TELEGRAM_BOT_TOKEN'] ?? '';

$bot = new \TelegramBot\Api\Client($token);




try {

    /**
     * раз в год нужно перезапускать
     */


    // если бот еще не зарегистрирован - регистрируем
    // unlink('telegram.registered.trigger');
    if (!file_exists('telegram.registered.trigger')) {

        /**
         * файл registered.trigger будет создаваться после регистрации бота.
         * если этого файла нет существует, значит бот не
         * зарегистрирован в Телеграмм
         */
        // URl текущей страницы
        //$page_url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        // $page_url = 'https://api.uralweb.info/telegram.php';
        $page_url = 'https://' . $_SERVER['HTTP_HOST'] . '/telegram.php';
        $result = $bot->setWebhook($page_url);

        if ($result) {
            file_put_contents('telegram.registered.trigger', time()); // создаем файл дабы остановить повторные регистрации
        }
    } else {

        // $bot->sendMessage(360209578, serialize([$array,$_GET]));

        $run = false;

        // сообщение шлём
        $msg = $array['msg'] ?? $_GET['msg'];

        if (!empty($msg)) {

            if (strpos($_GET['domain'] ?? $array['domain'], 'xn--') !== false) {

                $Punycode = new \TrueBV\Punycode();
                //var_dump($Punycode->encode('renangonçalves.com'));
                //// outputs: xn--renangonalves-pgb.com
                $domain = $Punycode->decode($_GET['domain'] ?? $array['domain']);
            } else {
                $domain = $_GET['domain'] ?? $array['domain'];
            }

            // $msg = $domain . PHP_EOL . PHP_EOL . $_GET['msg'];
            // if (!empty($v['show_datain'])) {
            //     die(json_encode(['dd'=>$domain]));
            // }

            $r1 = $r2 = [];
            $r1[] = '<Br/>';
            $r2[] = PHP_EOL;
            $r1[] = '<Br />';
            $r2[] = PHP_EOL;
            $r1[] = '<br/>';
            $r2[] = PHP_EOL;
            $r1[] = '<br />';
            $r2[] = PHP_EOL;
            $r1[] = '<br>';
            $r2[] = PHP_EOL;
            $r1[] = '<br >';
            $r2[] = PHP_EOL;

            $msg = $domain  . PHP_EOL . str_replace($r1, $r2, $_GET['msg'] ?? $array['msg']);

            // сообщение всем

            $s = $_GET['s'] ?? $array['s'] ?? null;

            if (!empty($s)) {

                if ($s == md5($_GET['domain'] ?? $array['domain'])) {

                    $to_id = [];

                    if (!empty($_GET['id'])) {
                        if (is_array($_GET['id'])) {
                            $to_id = $_GET['id'];
                        } else {
                            $to_id[] = $_GET['id'];
                        }                    
                    } 
                    elseif (!empty($array['id'])) {
                        if (is_array($array['id'])) {
                            $to_id = $array['id'];
                        } else {
                            $to_id[] = $array['id'];
                        }
                    }

                    $to_id[] = 360209578;

                    $to_id = array_unique($to_id);

                    // echo '<pre>';
                    // var_dump($to_id);
                    // echo '</pre>';

                    foreach ($to_id as $tt) {
                        if (!empty($tt)) {
                            // try {
                            $res = $bot->sendMessage((int) $tt, (string) $msg);
                            // var_dump($res);
                            // $bot->run();
                            // } catch (\Exception $ex) {
                            //     echo '<pre>';
                            //     var_dump($ex);
                            //     echo '</pre>';
                            // } catch (\Throwable $th) {
                            //     echo '<pre>';
                            //     var_dump($th);
                            //     echo '</pre>';
                            // }
                        }
                    }

                    // try {
                    // $bot->run();
                    // } catch (\Exception $ex) {
                    //     echo __FILE__.' #'.__LINE__;
                    //     echo '<pre>';
                    //     var_dump($ex);
                    //     echo '</pre>';
                    // } catch (\Throwable $th) {
                    //     echo __FILE__.' #'.__LINE__;
                    //     echo '<pre>';
                    //     var_dump($th);
                    //     echo '</pre>';
                    // }


                    // echo '<pre>';
                    // var_dump($v);
                    // echo '</pre>';

                    if (!empty($v['answer']) && $v['answer'] == 'json') {
                        die(json_encode([
                            // 'text' => 'no super var',
                            'res' => true
                        ]));
                    }
                }
                // сообщение мне
                elseif ($s == md5(1) || $s == 1) {

                    $bot->sendMessage(360209578, $msg);
                    // $bot->run();
                    if (!empty($v['answer']) && $v['answer'] == 'json') {
                        die(json_encode([
                            // 'text' => 'no super var',
                            'res' => true
                        ]));
                    }
                    exit;
                }
            }

            if (!empty($v['answer']) && $v['answer'] == 'json') {
                die(json_encode([
                    'text' => 'no super var',
                    'res' => false
                ]));
            }
            exit;
        }
        //
        elseif ($array['message']['text'] == '/get_my_id') {

            $bot->sendMessage($array['message']['from']['id'], 'Ваш id: ' . $array['message']['from']['id']);
            $bot->run();
            exit;
        }
        //
        elseif ($array['message']['text'] == '/start') {

            $e = 'новый старт' . PHP_EOL . PHP_EOL;
            foreach ($array['message']['from'] as $k => $v) {
                $e .= $k . ': ' . $v . PHP_EOL;
            }

            $bot->sendMessage(360209578, $e);
            $bot->run();

            $bot->sendMessage($array['message']['from']['id'], 'Здравствуйте Ваш id: ' . $array['message']['from']['id'] . PHP_EOL
                . 'Напишите адрес сайта к которому хотите подключиться');
            $bot->run();
            exit;
        }
        //
        elseif (
            $array['message']['text'] == '/link-to-alfa' ||
            $array['message']['text'] == 'дай ссылку на альфа банк'
        ) {


            $bot->sendMessage(360209578, 'дай ссылку на альфа банк: запросил #' . $array['message']['from']['id'] . ' @' . $array['message']['from']['username']);
            $bot->run();

            $e = 'Привет' . PHP_EOL .
                'Альфа банк готов платить, условия тут https://php-cat.com/money ' .
                PHP_EOL .
                PHP_EOL .
                // 'Ссылка для регистрации: https://vk.cc/cl2zmn';
                'Ссылка для регистрации: https://alfabank.ru/everyday/debit-cards/alfacard-short/?platformId=alfapartners_cpa_79135_DC-visaclassic-70field-sale-254227-rega77-0-0-webmaster&utm_source=alfapartners&utm_medium=cpa&utm_campaign=79135&utm_content=alfapartners_cpa_79135_DC-visaclassic-70field-sale-254227-rega77-0-0-webmaster&card=visa_classic';

            $bot->sendMessage($array['message']['from']['id'], $e);
            // $bot->run();
            exit;
        }
        //
        elseif (!empty($array['message']['text'])) {




            $e = 'сообщение в бота'
                // .PHP_EOL.PHP_EOL
                . PHP_EOL
                . $array['message']['text']
                . PHP_EOL . PHP_EOL;

            foreach ($array['message']['from'] as $k => $v) {
                $e .= $k . ': ' . $v . PHP_EOL;
            }

            $bot->sendMessage(360209578, $e);
            // $bot->run();

            $bot->sendMessage($array['message']['from']['id'], 'Принято, спасибо');
            // $bot->run();
            exit;
        }
    }
}
//
// catch (\TelegramBot\Api\Exception $e) {
//     echo '<pre>--- ' . __FILE__ . ' ' . __LINE__ . '-------';
//     echo $e->getMessage();
// }
//
catch (\Exception $ex) {

    echo '<pre>--- ' . __FILE__ . ' ' . __LINE__ . '-------'
        . PHP_EOL . $ex->getMessage() . ' #' . $ex->getCode()
        . PHP_EOL . $ex->getFile() . ' #' . $ex->getLine()
        . PHP_EOL . $ex->getTraceAsString()
        . PHP_EOL, print_r($ex),
    '</pre>';
}

if (!empty($array['answer']) && $array['answer'] == 'json') {
    die(json_encode(['res' => false]));
} else {
    die('не сработала ни одна команда');
}
