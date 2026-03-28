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
      // вызываем функцию проверки состояния лицевого счета       
      $getbalanceinfo_result = $client->getBalanceInfo(); 
   }
   catch(SoapFault $fault)
   {
      // Не удалось вызвать функцию getBalanceInfo на сервере или она отработала неправильно.
      echo $format1."Couldnt execute getBalanceInfo".$format2;
      echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
      logout();
      exit();
   }
   if ($getbalanceinfo_result->status->code != '1')
   {
      // функция отработала, возникла обработанная ошибка с идентификатором $getBalanceInfo_result->status->name, выдаем сообщение.
      echo $format1."Failed to get balance information ".$format2;
      echo "Error name: ".$getbalanceinfo_result->status->name." <br>Error message: ".$getbalanceinfo_result->status->message;
      logout();
      exit();
   }
   else
   {
      // Успешно проверили cостояние лицевого счета.
      echo "<br><b>getBalanceInfo status ID:</b> ".$getbalanceinfo_result->status->name;          
      echo "<br><b>getBalanceInfo message:</b> ".$getbalanceinfo_result->status->message;         
      
      // ------------
      echo "<p><b>Информация по лицевому счету:</b></p>";
      echo "<p>Всего зачислено платежей на сумму: ".$getbalanceinfo_result->data->pays_all.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>Условно зачислено платежей на сумму: ".$getbalanceinfo_result->data->vpays.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>Закрыто счетами-фактурами услуг на сумму: ".$getbalanceinfo_result->data->pays_closed.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>Заблокировано средств на услуги: ".$getbalanceinfo_result->data->blocked.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>Доступно для блокировки: ".$getbalanceinfo_result->data->free.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>Баланс лицевого счета: ".$getbalanceinfo_result->data->balance.' '.$getbalanceinfo_result->data->currency."</p>"; 
      //----------
      echo "<p><b>Информация по бонусному счету:</b></p>";
      echo "<p>Всего зачислено платежей на сумму: ".$getbalanceinfo_result->data->bonus_pays.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>Заблокировано средств на услуги: ".$getbalanceinfo_result->data->bonus_blocked.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>Закрыто счетами-фактурами услуг на сумму: ".$getbalanceinfo_result->data->bonus_closed.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>Доступно для блокировки: ".$getbalanceinfo_result->data->bonus_free.' '.$getbalanceinfo_result->data->currency."</p>"; 
      //----------
      echo "<p><b>Информация о ведении счетов:</b></p>";
      echo "<p>Валюта: ".$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>Включены ли налоги: ".$getbalanceinfo_result->data->with_taxes."</p>"; 

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
