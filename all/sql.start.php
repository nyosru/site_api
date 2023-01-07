<?php

if (!extension_loaded('PDO')) {
    throw new \Exception(' pdo bd не доступен ');
}

if (file_exists(DR . dir_site . 'config.db.php'))
    require_once DR . dir_site . 'config.db.php';

if (isset($db_cfg['type']) && $db_cfg['type'] == 'mysql') {

    $db = new \PDO('mysql:host=' . $db_cfg['host'] . ';charset=UTF8;dbname=' . $db_cfg['db'], $db_cfg['login'], $db_cfg['pass'], array(
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
//        \PDO::ATTR_TIMEOUT => 2,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC ,
        // \PDO::ATTR_PERSISTENT=>true // постоянное соединение без отключений при перезагрузке
    ));

    /*
     * 
      echo '<br/>' . __FILE__ . ' ' . __LINE__;

      if ($_SERVER['HTTP_HOST'] == 'yapdomik.uralweb.info') {

      try {

      $ff2 = $db->prepare('CREATE TABLE IF NOT EXISTS `gm_user2233` ( '
      // наверное в MySQL .' `id` int NOT NULL AUTO_INCREMENT, '
      // в SQLlite
      . ' `id` INTEGER PRIMARY KEY AUTOINCREMENT
      );
      ');
      //$ff->execute([$domain]);
      $ff2->execute();
      } catch (\PDOException $ex) {
      echo ' ---<Br/>---<Br/> ' . __FILE__ . ' ' . __LINE__ . ' <Br/>-------<Br/> '
      . '<Br/>' . $ex->getMessage() . ' #' . $ex->getCode()
      . '<Br/>' . $ex->getFile() . ' #' . $ex->getLine()
      . '<Br/>'
      . '<pre>'
      . PHP_EOL . $ex->getTraceAsString()
      . '</pre>';
      }

      die('<br/>123');
      }

     */
} else {

    //echo '<br/>' . __FILE__ . ' ' . __LINE__;

    $db = new \PDO('sqlite:' . DR . dir_site . 'db.sqllite.sl3', null, null, array(
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
    ));
    $db->exec('PRAGMA journal_mode = WAL;');
}