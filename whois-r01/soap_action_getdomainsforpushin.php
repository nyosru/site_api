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
$format3 = "<br>___________________________________________________<br><a href=\"soap_form.html#getpassindomains\">Go back</a>";
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
      $getdomainsforpushin_result = $client->getDomainsForPushIn('sortfield'); 
   }
   catch(SoapFault $fault)
   {
      // Не удалось вызвать функцию getDomainsForPushIn на сервере или она отработала неправильно.
      echo $format1."Couldnt execute getDomainsForPushIn".$format2;
      echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
      logout();
      exit();
   }
   if ($getdomainsforpushin_result->status->code != '1')
   {
      // функция отработала, возникла обработанная ошибка с идентификатором $getDomainsForPushIn_result->status->name, выдаем сообщение.
      echo $format1."Failed to get passing domains list ".$format2;
      echo "Error name: ".$getdomainsforpushin_result->status->name." <br>Error message: ".$getdomainsforpushin_result->status->message;
      logout();
      exit();
   }
   else
   {
      // Успешно 
      echo "<br><b>getDomainsForPushIn status ID:</b> ".$getdomainsforpushin_result->status->name;          
      echo "<br><b>getDomainsForPushIn message:</b> ".$getdomainsforpushin_result->status->message;
      echo "<br><b>total domains found:</b> ".$getdomainsforpushin_result->data->listinfo->total;          
      echo "<br><b>total pages:</b> ".$getdomainsforpushin_result->data->listinfo->totalpages;          
      echo "<br><b>current limit:</b> ".$getdomainsforpushin_result->data->listinfo->limit;          
      echo "<br><b>current page:</b> ".$getdomainsforpushin_result->data->listinfo->page;          
      echo "<table border=1><tr><td>N</td><td>name</td><td>agr_number</td><td>reg-till</td><td>state</td><td>nserver</td>
         <td>admin-o</td><td>isorg</td><td>org_r</td><td>person_r</td><td>org</td><td>person</td></tr>";
      $reg_till = "reg-till"; 
      $admin_o = "admin-o";
      foreach($getdomainsforpushin_result->data->domainarray as $key => $domain)
      {
         echo "<tr><td>".($key+1)."</td><td>".$domain->name."</td><td>".$domain->agr_number."</td><td>".$domain->$reg_till."</td><td>".$domain->state
            ."</td><td>".$domain->nserver."</td><td>".$domain->$admin_o."</td><td>".$domain->isorg."</td><td>"
            .iconv("UTF-8", "KOI8-R",$domain->org_r)."</td><td>".iconv("UTF-8", "KOI8-R",$domain->person_r)
            ."</td><td>".$domain->org."</td><td>".$domain->person."</td></tr>";
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
