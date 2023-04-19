<?
//ini_set('display_errors',1);
//error_reporting(E_ALL);
require_once("soap_client_settings.php");
ini_set('soap.wsdl_cache_enabled', 0); 
ini_set('session.name', 'SOAPClient');

function logout()
{
   // {{{
   // После того, как выполнили необходимые операции, необходимо разлогиниться, удалить данные сессии, 
   // для этого вызываем функцию LogOut на SOAP-сервере
   global $client; global $format1; global $format2; global $format3;
   try 
   {
      // Разлогинимся 
      $logoutresult = $client->logOut();
   }
   catch(SoapFault $fault)
   {
      // Не удалось вызвать функцию LogOut на сервере или она отработала неправильно.
      echo $format1."Can`t log out.".$format2;
      echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
      echo $format3;
      exit();
   }
   // Успешно вышли(разлогинились)
   echo "<br>______________________________________________________<br>Logged out.";
   echo $format3;
   // }}}
}
// Строки для форматирования сообщений
$format1 = "<p style=\"text-decoration: underline;\"><b>";
$format2 = "________________________________________</b></p>";
$format3 = "<br>___________________________________________________<br><a href=\"soap_form.html#checktask\">Go back</a>";
echo "<br><b>SOAP server address</b>: $soap_server_address <br>";
// Пытаемся создать SOAP-клиента и соединиться с SOAP-сервером.
try 
{
   $client = new SoapClient(null,array
                              (
                              'location' => $soap_server_address, // адрес SOAP-сервера - из soap_client_settings
                              'uri' => 'urn:RegbaseSoapInterface', 
                              'exceptions' => true,
                              'user_agent' => 'RegbaseSoapInterfaceClient', 
                              'trace' => 1
                              )
                           );
}
catch(SoapFault $fault)
{
   // Не смогли соединиться с сервером.
   echo $format1."Couldn`t connect to SOAP server".$format2;
   echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
   echo $format3;
   exit();
}

if ( (isset($_POST['login']) && isset($_POST['password'])) && (!empty($_POST['login']) && !empty($_POST['password'])) )
{
   // Если форма заполнена, пытаемся идти дальше
   try 
   {
      // Логинимся 
      $loginresult = $client->logIn($_POST['login'],$_POST['password']);
   }
   catch(SoapFault $fault)
   {
      // Не удалось вызвать функцию LogIn на SOAP-сервере или она не выполнилась нормально
      echo $format1."Can`t log in.".$format2;
      echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
      echo $format3;
      exit();
   }
   if ($loginresult->status->code == '0') 
   {
      // Функция LogIn на сервере отработала нормально, но авторизация не прошла (например, неправильный логин с паролем)
      echo $format1."Invalid login/password".$format2;
      echo 'Code: '.$loginresult->status->code.'<br>Message: '.$loginresult->status->message;
      echo $format3;
      exit();
   }
   else
   {
      // Залогинились, ставим SOAP-клиенту cookie, которая будет использована при вызове следующих функций.
      $client->__setCookie('SOAPClient',$loginresult->status->message);
      echo "Successfully logged in as ".$_POST['login'];
   }

   try 
   {  
      // вызываем функцию получения списка RR записей по домену   
      $getrrrecord = $client->getRrRecords($_POST['domain']); 
   }
   catch(SoapFault $fault)
   {
      // Не удалось вызвать функцию getRrRecords на сервере или она отработала неправильно.
      echo $format1."Couldnt execute getRrRecords".$format2;
      echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
      logout();
      exit();
   }
   if ($getrrrecord->status->code != '1')
   {
      // функция отработала, возникла обработанная ошибка с идентификатором $getrrrecord->status->name, выдаем сообщение.
      echo $format1."Failed to get RR records ".$format2;
      echo "Error name: ".$getrrrecord->status->name." <br>Error message: ".$getrrrecord->status->message;
      logout();
      exit();
   }
   else
   {
      // Успешно получили список записей.
      echo "<br><b>getRrRecords status:</b> ".$getrrrecord->status->name;
      echo "<br><b>getRrRecords message:</b> ".$getrrrecord->status->message;
      echo "<table border=1><tr><td>N</td><td>ID</td><td>Host</td><td>Record type</td><td>Priority</td><td>Weight</td>
      <td>Port</td><td>Value</td><td>Algorithm</td><td>Type cast</td><td>Comment</td></tr>";
      foreach($getrrrecord->data as $key => $record)
      {
         echo "<tr><td>".($key+1)."</td>
         <td>".$record->id."</td>
         <td>".$record->owner."</td>
         <td>".$record->type_record."</td>
         <td>".$record->pri."</td>
         <td>".$record->weight."</td>
         <td>".$record->port."</td>
         <td>".$record->data."</td>
         <td>".$record->sshfp_algorithm."</td>
         <td>".$record->sshfp_type."</td>
         <td>".$record->info."</td></tr>";
      }
      echo '</table>';
   }
   
   logout();   
}
else
{
   // Пустое имя или пароль в форме
   echo $format1."Invalid (blank) login/password".$format2;
   echo $format3;
   exit();
}
?>
