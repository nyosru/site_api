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
   echo $format1."Couldn`t connect to SOAP server $soap_server_address".$format2;
   echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
   echo $format3;
   exit();
}

if ( (isset($_POST['login']) && isset($_POST['password'])) && (!empty($_POST['login']) && !empty($_POST['password'])) )
{
   // Если форма заполнена, пытаемся идти дальше
   $params = Array('domain'=>$_POST['domain'],'state'=>$_POST['state'],'date_from'=>$_POST['date_from'],
                   'date_to'=>$_POST['date_to'],'admin-o'=>$_POST['admin-o'],
                   'isorg'=>$_POST['isorg'],'name_rus'=>iconv("KOI8-R", "UTF-8",$_POST['name_rus']),'name_eng'=>$_POST['name_eng']); 
   try 
   {
      // Логинимся 
      $loginresult = $client->logIn($_POST['login'],$_POST['password']);
   }
   catch(SoapFault $fault)
   {
      // Не удалось вызвать функцию LogIn на SOAP-сервере или она не выполнилась нормально
      echo $format1."Can`t log in.".$format2;
      echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring."<br>server: $soap_server_address";
      echo $format3;
      exit();
   }
   if ($loginresult->status->code == '0') 
   {
      // Функция LogIn на сервере отработала нормально, но авторизация не прошла (например, неправильный логин с паролем)
      echo $format1."Invalid login/password".$format2;
      echo 'Code: '.$loginresult->status->code.'<br>Message: '.$loginresult->status->message."<br>server: $soap_server_address";
      echo $format3;
      exit();
   }
   else
   {
      // Залогинились, ставим SOAP-клиенту cookie, которая будет использована при вызове следующих функций.
      $client->__setCookie('SOAPClient',$loginresult->status->message);
      echo "Successfully logged in as ".$_POST['login']."<br>server: $soap_server_address";
   }

   if (isset($_POST['domain']))
   {
      try 
      {         
         // Внимание, все передаваемые через SOAP данные должны быть в кодировке UTF-8, поэтому если в передаваемом поле возможны
         // русские символы, необходимо явно конвертировать это поле в UTF-8.
         $getdomains_result = $client->getDomains($params,$_POST['strict'],$_POST['sort_field'],$_POST['sort_dir'],
                                                  $_POST['limit'],$_POST['pagenum']);
      }
      catch(SoapFault $fault)
      {
         // Не удалось вызвать функцию getDomains на сервере или она отработала неправильно.
         echo $format1."Couldnt execute getDomains".$format2;
         echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
         logout();
         exit();
      }
      if ($getdomains_result->status->code != '1')
      {
         // функция отработала, возникла обработанная ошибка с идентификатором $getDomains_result->status->name, выдаем сообщение.
         echo $format1."Failed to get domain list.".$format2;
         echo "Error name: ".$getdomains_result->status->name." <br>Error message: ".$getdomains_result->status->message;
         logout();
         exit();
      }
      else
      {
         // Успешно 
         echo "<br><b>getDomains status ID:</b> ".$getdomains_result->status->name;          
         echo "<br><b>getDomains message:</b> ".$getdomains_result->status->message;
         echo "<br><b>total domains found:</b> ".$getdomains_result->data->listinfo->total;          
         echo "<br><b>total pages:</b> ".$getdomains_result->data->listinfo->totalpages;          
         echo "<br><b>current limit:</b> ".$getdomains_result->data->listinfo->limit;          
         echo "<br><b>current page:</b> ".$getdomains_result->data->listinfo->page;         
         echo "<table border=1><tr><td>N</td><td>name</td><td>agr_number</td><td>reg-till</td><td>state</td><td>nserver</td>
               <td>admin-o</td><td>isorg</td><td>org_r</td><td>person_r</td><td>org</td><td>person</td></tr>";
         $reg_till = "reg-till"; 
         $admin_o = "admin-o";
         foreach($getdomains_result->data->domainarray as $key => $domain)
         {
            echo "<tr><td>".($key+1)."</td><td>".$domain->name."</td><td>".$domain->agr_number."</td><td>".$domain->$reg_till."</td><td>".$domain->state
                ."</td><td>".$domain->nserver."</td><td>".$domain->$admin_o."</td><td>".$domain->isorg."</td><td>"
                .iconv("UTF-8", "KOI8-R",$domain->org_r)."</td><td>".iconv("UTF-8", "KOI8-R",$domain->person_r)
                ."</td><td>".$domain->org."</td><td>".$domain->person."</td></tr>";
         }
         echo '</table>';
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
