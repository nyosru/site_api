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
   $params = Array('nic_hdl'=>$_POST['nic_hdl'],
                  'fiorus'=>iconv("KOI8-R", "UTF-8",$_POST['fiorus']),
                  'fioeng'=>$_POST['fioeng'],
                  'is_org'=>$_POST['is_org'],
                  'e_mail'=>$_POST['e_mail'],
                  'simplech'=>$_POST['simplech'],
                  'default'=>$_POST['default']); 
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

   try 
   {         
      // Внимание, все передаваемые через SOAP данные должны быть в кодировке UTF-8, поэтому если в передаваемом поле возможны
      // русские символы, необходимо явно конвертировать это поле в UTF-8.
      $getdadmins_result = $client->getDadmins($params,$_POST['strict'],$_POST['sort_field'],$_POST['sort_dir'],
                                               $_POST['limit'],$_POST['pagenum']);

   }
   catch(SoapFault $fault)
   {
      // Не удалось вызвать функцию getdadmins на сервере или она отработала неправильно.
      echo $format1."Couldnt execute getDadmins".$format2;
      echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
      logout();
      exit();
   }
   if ($getdadmins_result->status->code != '1')
   {
      // функция отработала, возникла обработанная ошибка с идентификатором $getdadmins_result->status->name, выдаем сообщение.
      echo $format1."Failed to get dadmin list.".$format2;
      echo "Error name: ".$getdadmins_result->status->name." <br>Error message: ".$getdadmins_result->status->message;
      logout();
      exit();
   }
   else
   {
      // Успешно 
      echo "<br><b>getDadmins status ID:</b> ".$getdadmins_result->status->name;          
      echo "<br><b>getDadmins message:</b> ".$getdadmins_result->status->message;
      echo "<br><b>total dadmins found:</b> ".$getdadmins_result->data->listinfo->total;          
      echo "<br><b>total pages:</b> ".$getdadmins_result->data->listinfo->totalpages;          
      echo "<br><b>current limit:</b> ".$getdadmins_result->data->listinfo->limit;          
      echo "<br><b>current page:</b> ".$getdadmins_result->data->listinfo->page;         

      $print = '<table border=1><tr><td>N</td><td>Nic-hdl</td><td>Организация</td><td>ФИО&nbsp;по-русски</td><td>ФИО&nbsp;по-английски</td>';
      $print .= '<td>Наименование по-русски</td><td>Наименование по-английски</td>';
      $print .= '<td>Паспортные&nbsp;данные</td><td>Дата&nbsp;рождения</td><td>Юридический&nbsp;адрес</td><td>Почтовый&nbsp;адрес</td>';
      $print .= '<td>Телефон</td><td>Факс</td><td>E-Mail</td><td>Скрыть персональные данные</td>';
      $print .= '<td>ИНН</td><td>КПП</td><td>ОГРН</td><td>ФИО&nbsp;директора</td>';
      $print .= '<td>Название банка</td><td>Расчетный счет</td><td>Корреспондентский счет</td><td>БИК</td>';
      $print .= '<td>Администратор по-умолчанию</td><td>Информация проверена</td></tr>';
      
      foreach($getdadmins_result->data->dadminarray as $key => $dadmin)
      {
         $dadmin->fiorus         = iconv('UTF-8','KOI8-R',$dadmin->fiorus);
         $dadmin->orgname_ru     = iconv('UTF-8','KOI8-R',$dadmin->orgname_ru);
         $dadmin->passport       = iconv('UTF-8','KOI8-R',$dadmin->passport);
         $dadmin->legal_addr     = iconv('UTF-8','KOI8-R',$dadmin->legal_addr);
         $dadmin->postal_addr    = iconv('UTF-8','KOI8-R',$dadmin->postal_addr);
         $dadmin->director_name  = iconv('UTF-8','KOI8-R',$dadmin->director_name);
         $dadmin->bank           = iconv('UTF-8','KOI8-R',$dadmin->bank);
         $protect_data  = ( $dadmin->isprotected==1 ) ? 'Да' : 'Нет';
         $default       = ( $dadmin->default==1 ) ? 'Да' : 'Нет';
         $info_checked  = ( $dadmin->info_checked==1 ) ? 'Да' : 'Нет';
         $is_org        = ( $dadmin->is_org==1 ) ? 'Да' : 'Нет';
         
         $print .= '<tr><td>'.($key+1).'</td>';
         $print .= '<td>'.$dadmin->nic_hdl.'</td><td>'.$is_org.'</td><td>'.$dadmin->fiorus.'</td><td>'.$dadmin->fioeng.'</td>';
         $print .= '<td>'.$dadmin->orgname_ru.'</td><td>'.$dadmin->orgname_en.'</td>';
         $print .= '<td>'.$dadmin->passport.'</td><td>'.$dadmin->birth_date.'</td><td>'.$dadmin->legal_addr.'</td><td>'.$dadmin->postal_addr.'</td>';
         $print .= '<td>'.$dadmin->phone.'</td><td>'.$dadmin->fax.'</td><td>'.$dadmin->e_mail.'</td><td>'.$protect_data.'</td>';
         $print .= '<td>'.$dadmin->inn.'</td><td>'.$dadmin->kpp.'</td><td>'.$dadmin->ogrn.'</td><td>'.$dadmin->director_name.'</td>';
         $print .= '<td>'.$dadmin->bank.'</td><td>'.$dadmin->ras_schet.'</td><td>'.$dadmin->kor_schet.'</td><td>'.$dadmin->bik.'</td>';
         $print .= '<td>'.$default.'</td><td>'.$info_checked.'</td>';
         $print .= '</tr>';
      }
      $print .= '</table>';
      echo $print;

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
