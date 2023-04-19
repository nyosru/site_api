<?
//ini_set('display_errors',1);
//error_reporting(E_ALL);
require_once("soap_client_settings.php");
ini_set('soap.wsdl_cache_enabled', 0); 
ini_set('session.name', 'SOAPClient');
function logout()
{
   // {{{
   // ����� ����, ��� ��������� ����������� ��������, ���������� �������������, ������� ������ ������, 
   // ��� ����� �������� ������� LogOut �� SOAP-�������
   global $client; global $format1; global $format2; global $format3;
   try 
   {
      // ������������ 
      $logoutresult = $client->logOut();
   }
   catch(SoapFault $fault)
   {
      // �� ������� ������� ������� LogOut �� ������� ��� ��� ���������� �����������.
      echo $format1."Can`t log out.".$format2;
      echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
      echo $format3;
      exit();
   }
   // ������� �����(�������������)
   echo "<br>______________________________________________________<br>Logged out.";
   echo $format3;
   // }}}
}
// ������ ��� �������������� ���������
$format1 = "<p style=\"text-decoration: underline;\"><b>";
$format2 = "________________________________________</b></p>";
$format3 = "<br>___________________________________________________<br><a href=\"soap_form.html\">Go back</a>";
echo "<br><b>server</b>: $soap_server_address <br>";
// �������� ������� SOAP-������� � ����������� � SOAP-��������.
try 
{
   $client = new SoapClient(null,array
                              (
                              'location' => $soap_server_address, // ����� SOAP-������� - �� soap_client_settings
                              'uri' => 'urn:RegbaseSoapInterface', 
                              'exceptions' => true,
                              'user_agent' => 'RegbaseSoapInterfaceClient',
                              'trace' => 1
                              )
                           );
}
catch(SoapFault $fault)
{
   // �� ������ ����������� � ��������.
   echo $format1."Couldn`t connect to SOAP server".$format2;
   echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
   echo $format3;
   exit();
}

if ( (isset($_POST['login']) && isset($_POST['password'])) && (!empty($_POST['login']) && !empty($_POST['password'])) )
{
   // ���� ����� ���������, �������� ���� ������
   try 
   {
      // ��������� 
      $loginresult = $client->logIn($_POST['login'],$_POST['password']);
   }
   catch(SoapFault $fault)
   {
      // �� ������� ������� ������� LogIn �� SOAP-������� ��� ��� �� ����������� ���������
      echo $format1."Can`t log in.".$format2;
      echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
      echo $format3;
      exit();
   }
   if ($loginresult->status->code == '0') 
   {
      // ������� LogIn �� ������� ���������� ���������, �� ����������� �� ������ (��������, ������������ ����� � �������)
      echo $format1."Invalid login/password".$format2;
      echo 'Code: '.$loginresult->status->code.'<br>Message: '.$loginresult->status->message;
      echo $format3;
      exit();
   }
   else
   {
      // ������������, ������ SOAP-������� cookie, ������� ����� ������������ ��� ������ ��������� �������.
      $client->__setCookie('SOAPClient',$loginresult->status->message);
      echo "Successfully logged in as ".$_POST['login']."<br>";
   }

   if (isset($_POST['domainname']) && isset($_POST['nservers']) && isset($_POST['nichdl']) && isset($_POST['description']) && isset($_POST['need_replace']))
   {
      // ���� ������ ���� ����, ���������� - �������������� ������.
      // ��������, ��� ������������ ����� SOAP ������ ������ ���� � ��������� UTF-8, ������� ���� � ������������ ���� ��������
      // ������� �������, ���������� ���� �������������� ��� ���� � UTF-8.
      try 
      {         
         $update_result = $client->updateDomain( iconv("KOI8-R", "UTF-8", $_POST['domainname']),  // ��� ������
                                           $_POST['nservers'],    // ������ � ��-��������� � ���������� ������� 
                                                                  // (nservername1 111.222.222.221\nnservername2 111.222.222.222)
                                           $_POST['nichdl'],      // NIC-handler
                                           iconv("KOI8-R", "UTF-8", $_POST['description']),
                                           $_POST['need_replace'],
                                           $_POST['hide_name_nichdl'],
                                           $_POST['hide_email'],
                                           $_POST['spam_process'],
                                           $_POST['hide_phone'],
                                           $_POST['hide_phone_email'],
                                           $_POST['dont_test_ns'] 
                                         ); 
      }
      catch(SoapFault $fault)
      {
         // �� ������� ������� ������� updateDomain �� ������� ��� ��� ���������� �����������.
         echo $format1."Couldnt execute updateDomain".$format2;
         echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
         logout();
         exit();
      }
      if ($update_result->status->code != '1')
      {
         // ������� ����������, �������� ������������ ������ � ��������������� $update_result->status->name, ������ ���������.
         echo $format1."Failed to update domain ".$_POST['domainname'].$format2;
         echo "Error name: ".$update_result->status->name." <br>Error message: ".$update_result->status->message;
         logout();
         exit();
      }
      else
      {
         // ������� ��������� ������ � ������� � ��������������� ������� $update_result->taskid
         echo "<br>updateDomain message: ".$update_result->status->message."<br>Task ID: ".$update_result->taskid; 
      }
   }
   else
   {
      // ����� �� ���������
      echo $format1."Form not filled".$format2;
      logout();
      exit();
   }
   logout();   
}
else
{
   // ������ ��� ��� ������ � �����
   echo $format1."Invalid (blank) login/password".$format2;
   echo $format3;
   exit();
}
?>
