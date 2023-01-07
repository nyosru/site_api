<!doctype html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Описание API интерфейсов для проектов uralweb">
    <title>API uralweb</title>

    <link rel="icon" href="https://uralweb.info/9.site/my1807uralweb/download/img/fav.png" type="image/x-icon">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        .jumbotron {
            xbackground-size: cover;
            xbackground-image: url('img/bg1.jpg');
        }

        main {
            min-height: 80vh;
        }
    </style>

</head>

<body>

    <main role="main">
        <section class="jumbotron text-center">
            <div class="container">
                <h1 class="jumbotron-heading">API</h1>
                <p class="lead text-muted">Собрание API интерфейсов для работы сайтов и служб</p>
                <p>
                    <a href="https://uralweb.info" target="_blank" class="btn btn-primary my-2">Заказать создание сайта</a>
                    <a href="https://uralweb.info" target="_blank" class="btn btn-secondary my-2">Записаться на консультацию</a>
                </p>
            </div>
        </section>

        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-xl-12">
                    <h1>Telegram</h1>

                    <div id="t1" style='margin-left: 20px; padding-left:20px;border-left: 3px solid rgba(0,255,0,0.2);'>

                        <h3>шлём сообщение кому либо подписанному на бота @UralwebBot</h3>
                        <pre>
<code>
    file_get_contents('https://api.uralweb.info/telegram.php?' . http_build_query(
        array(
            's' => 'секрет',
            'id' => $to, // id кому пишем
            'msg' => $text // текст сообщения
        ) 
    ));
</code>
                            </pre>
                        <pre>
<code>

const sendToTelegramm = async (msg) => {

    loading.value = true;

    let res = await axios
        .post('https://api.uralweb.info/telegram.php',
            {
                domain: window.location.hostname,
                // show_datain: 1,
                answer: 'json',

                // s: md5('1'),
                s: md5(секрет),

                // id: 1,
                id: [
                    111, // я
                    222 // ещё
                ],
                msg
            })
        .catch((error) => {
            console.log("error", error);
            return 'errored';
        });

    if (res.data.res === true) {
        sended.value = true;
        return 'sended';
    } else {
        errored.value = true;
        return 'errored';
    }

}</code>
                            </pre>
                        <h3>шлём тех оповещение в бот @UralwebBot</h3>
                        <pre>
<code>
    file_get_contents('https://api.uralweb.info/telegram.php?' . http_build_query(
        array(
            's' => 'секрет',
            'msg' => $text // текст сообщения
        ) 
    ));
</code>
                            </pre>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-xl-12">
                    <h1>VK</h1>

                    <div id="t1" style='margin-left: 20px; padding-left:20px;border-left: 3px solid rgba(0,255,0,0.2);'>

                        <h3>отправка сообщения подписанному от группы</h3>
                        <pre>
<code>
    file_get_contents('https://api.uralweb.info/vk.php?' . http_build_query(
        array(
            's' => 'секрет',
            'to_user' => $to, // id получателя сообщения
            'group' => $name_group, // название группы от которой шлём
            'msg' => $text // текст сообщения
        ) 
    ));
</code>
                            </pre>

                    </div>
                </div>
            </div>
        </div>

    </main>

    <footer class="text-muted" style="margin-top:3em; padding-top:3em; padding-bottom:3em; border-top: 3px solid rgba(0,0,0,0.1);">
        <div class="container">
            <p class="float-right">
                Все права защищены
            </p>
            <p>Можете копировать всякое с сайта и публиковать везде где получится</p>
        </div>
    </footer>

</body>

</html>