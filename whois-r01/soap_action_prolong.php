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
$format3 = "<br>___________________________________________________<br><a href=\"soap_form.html\">Go back</a>";
echo "<br><b>server</b>: $soap_server_address <br>";
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
      echo "Successfully logged in as ".$_POST['login']."<br>";
   }

   if (isset($_POST['domainname']))
   {
      // Если нужное поле есть, продолжаем - продление домена.
      if ($_POST['accounttype'] != 'real')
      {  // Меняем тип счета если надо
         try
         {
            $paytyperesult = $client->changeAccountType($_POST['accounttype']);
         }
         catch(SoapFault $fault)
         {
            // Не удалось вызвать функцию changeAccountType на сервере или она отработала неправильно.
            echo $format1."Couldnt execute changeAccountType".$format2;
            echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
            logout();
            exit();
         }
         if ($paytyperesult->status->code != '1')
         {
            // В процессе выполнения на сервере функции changeAccountType возникла обрабатываемая ошибка.
            echo $format1."Changing payment type failed".$format2;
            echo "Error name: ".$paytyperesult->status->name." <br>Error message: ".$paytyperesult->status->message;
            logout();
            exit();
         }
         else
         {
            // Успешно изменили тип счета
            echo "<br>".$paytyperesult->status->message."<br>";
         }
      }
      
      // продление   
      try 
      {         
         $prolong_result = $client->prolongDomain($_POST['domainname'], $_POST['years']); 
      }
      catch(SoapFault $fault)
      {
         // Не удалось вызвать функцию prolongDomain на сервере или она отработала неправильно.
         echo $format1."Couldnt execute prolongDomain".$format2;
         echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
         logout();
         exit();
      }
      if ($prolong_result->status->code != '1')
      {
         // функция отработала, возникла обработанная ошибка с идентификатором $prolong_result->status->name, выдаем сообщение.
         echo $format1."Failed to prolong domain ".$_POST['domainname'].$format2;
         echo "Error name: ".$prolong_result->status->name." <br>Error message: ".$prolong_result->status->message;
         logout();
         exit();
      }
      else
      {
         // Успешно поставили задание а продление в очередь с идентификатором задания $prolong_result->taskid
         echo "<br><b>prolongDomain message:</b> ".$prolong_result->status->message."<br>Task ID: ".$prolong_result->taskid; 
      }
   }
   else
   {
      // Форма не заполнена
      echo $format1."Form not filled".$format2;
      logout();
      exit();
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
