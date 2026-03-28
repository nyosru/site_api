<?php

require_once dirname(__FILE__) . '/session_start.php';


//
//if( class_exists('Nyos/nyos') ){
//    echo '<br/>'.__FILE__.' ('.__LINE__.')';
//}
//


/**
 * пишем данные ошибки в лог
 */
//+//require_once $_SERVER['DOCUMENT_ROOT'] . '/include/exception.php';

//try{
//    throw new \NyosEx('Ошибочка '.date('Y.m.d H:i:s'),rand(1,999));
//} catch ( \NyosEx $e ){
//    echo ' error: ' . $e->getMessage() . ' // ' . $e->getFile() .' / ' . $e->getLine();
//}
// require_once dirname(__FILE__) . '/setup_vars.php';


//    require_once $_SERVER['DOCUMENT_ROOT'] . '/include/Nyos/Nyos.php';
//    require_once $_SERVER['DOCUMENT_ROOT'] . '/include/f/txt.php';
//    require_once $_SERVER['DOCUMENT_ROOT'] . '/include/f/ajax.php';

// базы данных
//require_once(DirAll . 'class' . DS . 'mysqli.php' );
//require_once(DirAll . 'dbi.connector.php' );
//require_once(DirAll . 'f' . DS . 'dbi.php' );

// require_once $_SERVER['DOCUMENT_ROOT'] . '/include/f/db.php';
//require_once $_SERVER['DOCUMENT_ROOT'] . '/all/sql.start.php';

//$vv['db'] = $db;
//echo '<br/>'.__FILE__.'['.__LINE__.']';

\Nyos\Nyos::getFolder();

if (isset(\Nyos\Nyos::$folder_now{2}))
    $vv['folder'] = \Nyos\Nyos::$folder_now;

// echo '<br/>'.__LINE__.' '.$vv['folder'];

\Nyos\Nyos::defineVars();

//if ($_SERVER['HTTP_HOST'] == 'adomik.uralweb.info' || $_SERVER['HTTP_HOST'] == 'yapdomik.uralweb.info') {
//        die('<br/>' . __FILE__ . ' ' . __LINE__);
//    }
    
require_once $_SERVER['DOCUMENT_ROOT'] . '/all/sql.start.php';

//if ($_SERVER['HTTP_HOST'] == 'adomik.uralweb.info' || $_SERVER['HTTP_HOST'] == 'yapdomik.uralweb.info') {
//        die('<br/>' . __FILE__ . ' ' . __LINE__);
//    }

//if (is_dir($_SERVER['DOCUMENT_ROOT'] . DS . 'site' . DS)) {
//    \Nyos\Nyos::defineVars();
//} else {
//    \Nyos\Nyos::getFolder($db);
//    \Nyos\Nyos::defineVars();
//}

// require_once $_SERVER['DOCUMENT_ROOT'] . '/include/f/http.php';
//require_once $_SERVER['DOCUMENT_ROOT'] . '/include/f/smarty.php';

