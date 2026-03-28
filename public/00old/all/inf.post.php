<?php

echo '<div style="z-index:9999999999999;background-color:#fff;color:#000;position: fixed; padding: 5px; bottom: 20px; left: 20px;" > '
    . '<a href="#" onclick="$(\'#perem111\').toggle(); return false;" '
    . ' >переменные</a>'
    . ' &nbsp; <a href="/didrive/" target=_blank" >di</a>'
    . '</div>'
    . '<div id="perem111" style="display:none" >'
    . '<br/><hr>';

if( isset($timer) )
echo 'timer -> '.round( $timer->end_timer() ,4).'с.';

echo '<Br><hr>'
    .'<Br>
    <style>
    <!--
    #post2{ font-size: 14px; }
    #post2 p{ width: 450px;padding:5px;
        background-color: #ffaaaa; color:black; margin: 2px; }
    #post2 fieldset{ margin: 2px; width: 450px;background-color:white;color:black; }
    #post2 fieldset pre{ width: 445px; overflow: auto; max-height: 400px; }
    #post2 legend{ padding:5px; width:450px;background-color:yellow;color:black }
    //-->
    </style>
    <span id="post2" >
    ';

$vv_now_level = ( isset($vv['now_level']) ? $vv['now_level'] : array() );
$vv_now_mod = ( isset($vv['now_mod']) ? $vv['now_mod'] : array() );

$postar = array(
    '_glob',
    '_GET',
    '_POST',
    '_FILES',
    '_SESSION',
    '_COOKIE',
    //'_COOCKIE',
    //'_COOCKIE',
    'vv_now_level',
    'vv_now_mod',
    'AMCfg',
    'domen_info',
    'DidraCfg',
    //'gv2',
    '_SERVER'

    );

    foreach( $postar as $k )
    {
        if( isset($$k) && sizeof($$k) > 0 )
        {
        echo '<fieldset style="float: left" ><legend> + '.$k.'</legend><div><pre>';
        print_r($$k);
        echo '</pre></div></fieldset>';
        }else{
        echo '<p style="float: left" > - '.$k.' (0)</p>';
        }
    }

echo '</span></div>';