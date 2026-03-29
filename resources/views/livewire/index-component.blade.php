<div class="container">


    <div class="row">
        <div class="col-sm-12 col-md-12 col-xl-12">
            <h1>Whois</h1>

            <div id="t1" style='margin-left: 20px; padding-left:20px;border-left: 3px solid rgba(0,255,0,0.2);'>

                <h3>Доступен домен для реги или нет</h3>
                <pre>
<code>
http://api.php-cat.com/whois.php?domain=stu.com&return=json
</code>
                            </pre>

            </div>
        </div>
    </div>


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

}
</code>
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
                <h3>отправка сообщения подписанному от группы v2.0</h3>

                https://api.uralweb.info/api/vk/send
                <br/>
                <br/>
<b>GET:</b>
                <pre>
<code>
https://api.local/api/vk/send?group_name=--название группы--&user_id=--user_id--&message=сообщение
</code>
                            </pre>

<b>POST:</b>
                <pre>
                    <code>
{
"group_name": "--название группы--",
"user_id": --id_user|user_ids(x,x,x,x)--,
"message": "Сообщение"
}
 </code>
                            </pre>
            </div>
            <div id="t1" style='margin-left: 20px; padding-left:20px;border-left: 3px solid rgba(0,255,0,0.2);'>

                <h3>отправка сообщения подписанному от группы v1.0</h3>
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
