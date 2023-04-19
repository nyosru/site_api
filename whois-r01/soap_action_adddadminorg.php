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

   if (isset($_POST['nic_hdl']))
   {
      // Если нужное поле есть, продолжаем заведение нового администратора домена.
      try 
      {        
         
         // Внимание, все передаваемые через SOAP данные должны быть в кодировке UTF-8, поэтому если в передаваемом поле возможны
         // русские символы, необходимо явно конвертировать это поле в UTF-8.

         $adddadmin_result = $client->addDadminOrg( $_POST['nic_hdl'],    
                                                       iconv("KOI8-R", "UTF-8", $_POST['orgname_ru']), 
                                                       //$_POST['orgname_ru'],
                                                       $_POST['orgname_en'], 
                                                       $_POST['inn'], 
                                                       $_POST['kpp'], 
                                                       $_POST['ogrn'],
                                                       iconv("KOI8-R", "UTF-8", $_POST['legal_addr']), 
                                                       iconv("KOI8-R", "UTF-8", $_POST['postal_addr']), 
                                                       $_POST['phone'],  
                                                       $_POST['fax'], 
                                                       $_POST['e_mail'],
                                                       iconv("KOI8-R", "UTF-8", $_POST['director_name']), 
                                                       iconv("KOI8-R", "UTF-8", $_POST['bank']), 
                                                       $_POST['ras_schet'],
                                                       $_POST['kor_schet'], 
                                                       $_POST['bik'], 
                                                       $_POST['isresident']
                                                     ); 
      }
      catch(SoapFault $fault)
      {
         // Не удалось вызвать функцию addDadminOrg на сервере или она отработала неправильно.
         echo $format1."Couldnt execute addDadminOrg".$format2;
         echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
         logout();
         exit();
      }
      if ($adddadmin_result->status->code != '1')
      {
         // функция отработала, возникла обработанная ошибка с идентификатором $adddadmin_result->status->name, выдаем сообщение.
         echo $format1."Failed to create domain admin ".$_POST['nic_hdl'].$format2;
         echo "Error name: ".$adddadmin_result->status->name." <br>Error message: ".$adddadmin_result->status->message;
         logout();
         exit();
      }
      else
      {
         // Успешно добавили нового администратора доменов - организацию.
         echo "<br><b>addDadminOrg message:</b> ".$adddadmin_result->status->message; 
         echo "<br><b>Returned nic-hdl:</b> ".$adddadmin_result->nic_hdl; 
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
