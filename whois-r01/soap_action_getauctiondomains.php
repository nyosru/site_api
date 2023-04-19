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
   $params = Array('domain'=>$_POST['domain'],'state'=>$_POST['state'],'start_from'=>$_POST['start_from'],
                   'start_to'=>$_POST['start_to'],'end_from'=>$_POST['end_from'],'end_to'=>$_POST['end_to'],
                   'price_from'=>$_POST['price_from'],'price_to'=>$_POST['price_to'],'min_symb'=>$_POST['min_symb'],
                   'max_symb'=>$_POST['max_symb'],'tyc_from'=>$_POST['tyc_from'],'pr_from'=>$_POST['pr_from'],
                   'mask'=>$_POST['mask'],'first_reg_date'=>$_POST['first_reg_date']); 
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
         $getauctiondomains_result = $client->getAuctionDomains($params,$_POST['strict'],$_POST['sort_field'],$_POST['sort_dir'],
                                                  $_POST['limit'],$_POST['pagenum']);
          
      }
      catch(SoapFault $fault)
      {
         // Не удалось вызвать функцию getAuctionDomains на сервере или она отработала неправильно.
         echo $format1."Couldnt execute getAuctionDomains".$format2;
         echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
         logout();
         exit();
      }
      if ($getauctiondomains_result->status->code != '1')
      {
         // функция отработала, возникла обработанная ошибка с идентификатором $getAuctionDomains_result->status->name, выдаем сообщение.
         echo $format1."Failed to get auction domain list.".$format2;
         echo "Error name: ".$getauctiondomains_result->status->name." <br>Error message: ".$getauctiondomains_result->status->message;
         logout();
         exit();
      }
      else
      {
         // Успешно 
         echo "<br><b>getAuctionDomains status ID:</b> ".$getauctiondomains_result->status->name;          
         echo "<br><b>getAuctionDomains message:</b> ".$getauctiondomains_result->status->message;
         echo "<br><b>total domains found:</b> ".$getauctiondomains_result->data->listinfo->total;          
         echo "<br><b>total pages:</b> ".$getauctiondomains_result->data->listinfo->totalpages;          
         echo "<br><b>current limit:</b> ".$getauctiondomains_result->data->listinfo->limit;          
         echo "<br><b>current page:</b> ".$getauctiondomains_result->data->listinfo->page;          
         echo "<table border=1><tr><td>N</td><td>name</td><td>tyc</td><td>tyc is mirror</td><td>pr</td><td>state</td>
               <td>state name</td><td>start time</td><td>finish time</td><td>time to funish (timestamp)</td>
               <td>formatted time to funish</td><td>time to pay (timestamp)</td><td>formatted time to pay</td>
               <td>price</td><td>next bid</td><td>participant count</td><td>first reg date</td></tr>";
         foreach($getauctiondomains_result->data->au_domainarray as $key => $domain)
         {
            echo "<tr><td>".($key+1)."</td><td>".$domain->name."</td><td>".$domain->tyc."</td><td>".$domain->tyc_ismirror."</td><td>"
                .$domain->pr."</td><td>".$domain->state."</td><td>".$domain->state_name."</td><td>".$domain->start_time."</td><td>"
                .$domain->finish_time."</td><td>".$domain->time_to_finish."</td><td>".$domain->format_time_to_finish."</td><td>"
                .$domain->time_to_pay."</td><td>".$domain->format_time_to_pay."</td><td>"
                .$domain->price.' '.$domain->currency."</td><td>".$domain->next_bid."</td><td>".$domain->participant_count."</td><td>"
                .$domain->first_reg_date."</td></tr>";
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
