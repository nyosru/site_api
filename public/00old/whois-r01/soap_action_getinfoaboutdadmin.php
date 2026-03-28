<?
//ini_set('display_errors',1);
//error_reporting(E_ALL);
ini_set('soap.wsdl_cache_enabled', 0); 
ini_set('session.name', 'SOAPClient');
require_once("soap_client_settings.php");

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
      echo "Successfully logged in as ".$_POST['login'];
   }

   if ( isset($_POST['nic_hdl']) )
   {
      try 
      {  
         $getinfo = $client->getInfoAboutDadmin($_POST['nic_hdl']);       
      }
      catch(SoapFault $fault)
      {
         // Не удалось вызвать функцию getInfoAboutDadmin на сервере или она отработала неправильно.
         echo $format1."Couldnt execute getInfoAboutDadmin".$format2;
         echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
         logout();
         exit();
      }
      if ($getinfo->status->code != '1')
      {
         // функция отработала, возникла обработанная ошибка с идентификатором $getInfoAboutDadmin->status->name, выдаем сообщение.
         echo $format1."Failed to get information about administrator ".$format2;
         echo "Error name: ".$getinfo->status->name." <br>Error message: ".$getinfo->status->message;
         logout();
         exit();
      }
      else
      {
         // Успех
         echo "<br>getInfoAboutDadmin status ID: ".$getinfo->status->name; 
         echo "<br>getInfoAboutDadmin message: ".$getinfo->status->message;
         
         $getinfo->data->orgname_ru = iconv('UTF-8'.'KOI8-R',$getinfo->data->orgname_ru);
         $getinfo->data->legal_addr = iconv('UTF-8'.'KOI8-R',$getinfo->data->legal_addr);
         $getinfo->data->postal_addr = iconv('UTF-8'.'KOI8-R',$getinfo->data->postal_addr);
         $getinfo->data->director_name = iconv('UTF-8'.'KOI8-R',$getinfo->data->director_name);
         $getinfo->data->bank = iconv('UTF-8'.'KOI8-R',$getinfo->data->bank);
         $getinfo->data->fiorus = iconv('UTF-8'.'KOI8-R',$getinfo->data->fiorus);
         $getinfo->data->passport = iconv('UTF-8'.'KOI8-R',$getinfo->data->passport);
         
         $protect_data  = ( $getinfo->data->isprotected==1 )  ? 'Да' : 'Нет';
         $default       = ( $getinfo->data->default==1 )       ? 'Да' : 'Нет';
         $info_checked  = ( $getinfo->data->info_checked==1 )  ? 'Да' : 'Нет';

          
         echo "<table border=1>";
         if ( $getinfo->data->is_org == 1 )
         {
            echo "<tr><td colspan=2> Информация об администраторе (организация)</td></tr>";
            echo "<tr><td>Nic-hdl</td><td>".$getinfo->data->nic_hdl."</td></tr>";
            echo "<tr><td>Наименование по-русски</td><td>".$getinfo->data->orgname_ru."</td></tr>";
            echo "<tr><td>Наименование по-английски</td><td>".$getinfo->data->orgname_en."</td></tr>";
            echo "<tr><td>ИНН</td><td>".$getinfo->data->inn."</td></tr>";
            echo "<tr><td>КПП</td><td>".$getinfo->data->kpp."</td></tr>";
            echo "<tr><td>ОГРН</td><td>".$getinfo->data->ogrn."</td></tr>";
            echo "<tr><td>Юридический адрес</td><td>".$getinfo->data->legal_addr."</td></tr>";
            echo "<tr><td>Почтовый адрес</td><td>".$getinfo->data->postal_addr."</td></tr>";
            echo "<tr><td>Телефон</td><td>".$getinfo->data->phone."</td></tr>";
            echo "<tr><td>Факс</td><td>".$getinfo->data->fax."</td></tr>";
            echo "<tr><td>E-Mail</td><td>".$getinfo->data->e_mail."</td></tr>";
            echo "<tr><td>ФИО директора</td><td>".$getinfo->data->director_name."</td></tr>";
            echo "<tr><td>Название банка</td><td>".$getinfo->data->bank."</td></tr>";
            echo "<tr><td>Расчетный счет</td><td>".$getinfo->data->ras_schet."</td></tr>";
            echo "<tr><td>Корреспондентский счет</td><td>".$getinfo->data->kor_schet."</td></tr>";
            echo "<tr><td>БИК</td><td>".$getinfo->data->bik."</td></tr>";
            echo "<tr><td>Скрыть персональные данные</td><td>".$protect_data."</td></tr>";
            echo "<tr><td>Администратор по-умолчанию</td><td>".$default."</td></tr>";
            echo "<tr><td>Информация проверена</td><td>".$info_checked."</td></tr>";
         }
         else
         {
            echo "<tr><td colspan=2> Информация об администраторе (персона)</td></tr>";
            echo "<tr><td>Nic-hdl</td><td>".$getinfo->data->nic_hdl."</td></tr>";
            echo "<tr><td>ФИО по-русски</td><td>".$getinfo->data->fiorus."</td></tr>";
            echo "<tr><td>ФИО по-английски</td><td>".$getinfo->data->fioeng."</td></tr>";
            echo "<tr><td>Паспортные данные</td><td>".$getinfo->data->passport."</td></tr>";
            echo "<tr><td>Дата рождения</td><td>".$getinfo->data->birth_date."</td></tr>";
            echo "<tr><td>Почтовый адрес</td><td>".$getinfo->data->postal_addr."</td></tr>";
            echo "<tr><td>Телефон</td><td>".$getinfo->data->phone."</td></tr>";
            echo "<tr><td>Факс</td><td>".$getinfo->data->fax."</td></tr>";
            echo "<tr><td>E-Mail</td><td>".$getinfo->data->e_mail."</td></tr>";
            echo "<tr><td>Скрыть персональные данные</td><td>".$protect_data."</td></tr>";
            echo "<tr><td>Администратор по-умолчанию</td><td>".$default."</td></tr>";
            echo "<tr><td>Информация проверена</td><td>".$info_checked."</td></tr>";
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
