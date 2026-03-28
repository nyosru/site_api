<!doctype html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Описание API интерфейсов для проектов uralweb">
    <title>API uralweb</title>

    <link rel="icon" href="https://uralweb.info/9.site/my1807uralweb/download/img/fav.png" type="image/x-icon">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        .jumbotron {
            xbackground-size: cover;
            xbackground-image: url('img/bg1.jpg');
        }

        main {
            min-height: 80vh;
        }
    </style>
    @livewireStyles

</head>

<body>

<header>

    <section class="jumbotron text-center">
        <div class="container">
            <h1 class="jumbotron-heading">API</h1>
            <p class="lead text-muted">Собрание API интерфейсов для работы сайтов и служб</p>
            <p>
                <a href="https://uralweb.info" target="_blank" class="btn btn-primary my-2">Заказать создание сайта</a>
                <a href="https://uralweb.info" target="_blank" class="btn btn-secondary my-2">Записаться на
                    консультацию</a>
            </p>
        </div>
    </section>

</header>
<main role="main">


    <div class="container">
        {{ $slot }}
    </div>

</main>

<footer class="text-muted"
        style="margin-top:3em; padding-top:3em; padding-bottom:3em; border-top: 3px solid rgba(0,0,0,0.1);">
    <div class="container">
        <p class="float-right">
            Все права защищены
        </p>
        <p>Можете копировать всякое с сайта и публиковать везде где получится</p>
    </div>
</footer>

@livewireScripts
</body>

</html>
