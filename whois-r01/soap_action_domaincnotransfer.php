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

   if (isset($_POST['domainname']) && isset($_POST['nservers']) && isset($_POST['codeword']) && isset($_POST['nic_hdl']) )
   {
      // ���� ������ ���� ����, ���������� �������� ������
      try 
      {         
         $domaintransfer = $client->domainCnoTransfer($_POST['domainname'],$_POST['codeword'], $_POST['nic_hdl'],$_POST['nservers'],$_POST['hide_data']); 
      }
      catch(SoapFault $fault)
      {
         // �� ������� ������� ������� domainCnoTransfer �� ������� ��� ��� ���������� �����������.
         echo $format1."Couldnt execute domainCnoTransfer".$format2;
         echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
         logout();
         exit();
      }
      if ($domaintransfer->status->code != '1')
      {
         // ������� ����������, �������� ������������ ������ � ��������������� $domaintransfer->status->name, ������ ���������.
         echo $format1."Failed to transfer domain ".$format2;
         echo "Error name: ".$domaintransfer->status->name." <br>Error message: ".$domaintransfer->status->message;
         logout();
         exit();
      }
      else
      {
         // ������� ��������� ������� �� �������� ������ � ������� � ��������������� ������� $domaintransfer->taskid
         echo "<br><b>domainCnoTransfer message:</b> ".$domaintransfer->status->message."<br>Task ID: ".$domaintransfer->taskid; 
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
