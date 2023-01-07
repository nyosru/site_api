<?php

if (!defined('IN_NYOS_PROJECT'))
    die('Сработала защита <b>class MySQL</b> от злостных розовых хакеров.
	<br>Приготовтесь к DOS атаке (6 поколения на ip-' . $_SERVER["REMOTE_ADDR"] . ') в течении 30 минут... ..');

/**
 * обозначаем класс который будет работать когда случается это исключение 
 * ( вызываем называние класса throw new \NoFoundDataFile(' message ', int code ); )
 */

if( !defined('domain') )
define('domain', str_replace ('www.', '', mb_strtolower ( $_SERVER['HTTP_HOST']) ));

/**
 * класс обработки исключений
 */
class NyosEx extends \Exception {

    public static $folder = null;

// Переопределим исключение так, что параметр message станет обязательным
    public function __construct($message, $code = 0, Exception $previous = null) {

        // некоторый код выполняем
        // $this->sendMsg($message);
        // здесь можем что то делать если пошла обработка этого исключения
        // echo __FILE__ . ' [' . __LINE__ . '] началась обработка исключения ';

        $logDir = $_SERVER['DOCUMENT_ROOT'] . '/logs';
        $logFile = date('Y-m-d', $_SERVER['REQUEST_TIME']) . '.sqlite.log.sl3';

        try {

            if (!is_dir($logDir))
                mkdir($logDir, 0755);

            if (!file_exists($logDir . '/' . $logFile) || ( file_exists($logDir . '/' . $logFile) && filesize($logDir . '/' . $logFile) == 0 )) {

                $dbl = new \PDO('sqlite:' . $logDir . '/' . $logFile);
                // Set errormode to exceptions
                $dbl->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $dbl->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

                // Create table messages
                $dbl->exec("CREATE TABLE IF NOT EXISTS logs (
                    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 
                    folder TEXT, 
                    domain TEXT, 
                    message TEXT, 
                    code INTEGER, 
                    file TEXT, 
                    line INTEGER, 
                    trace TEXT,
                    d TEXT,
                    t TEXT
                    )");
            } else {

                $dbl = new \PDO('sqlite:' . $logDir . '/' . $logFile);
                // Set errormode to exceptions
                $dbl->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $dbl->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            }

            $db2 = $dbl->prepare('INSERT INTO `logs` (message,code,folder,domain,file,line,trace,d,t )
                    VALUES ( :msg, :code, :folder, :domain, :file, :line, :trace, :d, :t );');
            $db2->execute(array(
                ':msg' => $message,
                ':code' => $code,
                ':folder' => isset(\Nyos\Nyos::$folder_now{0}) ? \Nyos\Nyos::$folder_now : '',
                ':domain' => domain,
                ':file' => $this->file,
                ':line' => $this->line,
                ':trace' => $this->getTraceAsString(),
                ':d' => date('Y-m-d', $_SERVER['REQUEST_TIME']),
                ':t' => date('H:i:s', $_SERVER['REQUEST_TIME']),
            ));
            $db2 = null;
// показ всех данных из таблицы
//            $r = $dbl->prepare("SELECT * FROM logs ");
//            $r->execute();
//            
//            echo '<br/>'.__FILE__.' #'.__LINE__;
//            \f\pa($r->fetchall(),2);
//            unset($r);

            unset($dbl);
        } catch (\PDOException $ex) {

            echo '<pre>--- ' . __FILE__ . ' ' . __LINE__ . '-------'
            . PHP_EOL . $ex->getMessage() . ' #' . $ex->getCode()
            . PHP_EOL . $ex->getFile() . ' #' . $ex->getLine()
            . PHP_EOL . $ex->getTraceAsString()
            . '</pre>';
        } catch (Exception $ex) {

//            echo 'E: ' . $ex->getMessage();
//            echo '<br/>';
//            echo '<br/>';
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * сообщение при возникновении такого исключения
     * Переопределим строковое представление объекта.
     * @return type 
     */
    public function __toString() {

        return ' ' . __CLASS__ . ' [code#' . $this->code . '] ' . $this->message;
        //return $this->message;
    }

//    public function sendMsg($msg) {
//        echo '<br/>'.__FILE__.' ['.__LINE__.']';
//        echo '<br/>Мы можем определять новые методы в наследуемом классе';
//        echo '<br/>';
//    }
}

/*
try {
    // какая то ситуация когда вылетает исключение
    throw new \NoFoundDataFile(' текст сообщение ', 1);
} catch (NoFoundDataFile $e) {
    // и мы ловим наше исключение .. внутри класса делаем что нужно ... 
    // у каждого названия своё функционал который можно регулировать номерами ошибок Code передаваемыми в вызове исключения
    echo '<fieldset><legend>сработало исключение фирменное NoFoundDataFile</legend>'
    . 'message: ' . $e->getMessage()
    . '<br/>code: #' . $e->getCode()
    . '<br/>где ' . $e->getFile() . ' [' . $e->getLine() . ']'
    . '</fieldset>';
} catch (Exception $e) {
    // и обработка другого исключения если было не наше первое а это универсальное
    echo '<fieldset><legend>сработало исключение Exception</legend>'
    . 'message: ' . $e->getMessage()
    . '<br/>code: #' . $e->getCode()
    . '<br/>где ' . $e->getFile() . ' [' . $e->getLine() . ']'
    . '</fieldset>';
}
*/