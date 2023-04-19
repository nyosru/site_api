<?
//ini_set('display_errors',1);
//error_reporting(E_ALL);
ini_set('soap.wsdl_cache_enabled', 0); 
ini_set('session.name', 'SOAPClient');
require_once("soap_client_settings.php");

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
      echo "Successfully logged in as ".$_POST['login'];
   }

   if ( isset($_POST['nic_hdl']) && isset($_POST['domain_name']) && isset($_POST['accounttype']) )
   {
      // ���� ������ ���� ����, ���������� 
      if ($_POST['accounttype'] != 'real')
      {  // ������ ��� ����� ���� ����
         try 
         {
            $paytyperesult = $client->changeAccountType($_POST['accounttype']);
         }
         catch(SoapFault $fault)
         {
            // �� ������� ������� ������� changeAccountType �� ������� ��� ��� ���������� �����������.
            echo $format1."Couldnt execute changeAccountType".$format2;
            echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
            logout();
            exit();
         }         
         if ($paytyperesult->status->code != '1')
         {
            // � �������� ���������� �� ������� ������� changeAccountType �������� �������������� ������.
            echo $format1."Changing payment type failed".$format2;
            echo "Error name: ".$paytyperesult->status->name." <br>Error message: ".$paytyperesult->status->message;
            logout();
            exit();
         }
         else
         {
            // ������� �������� ��� �����
            echo "<br>".$paytyperesult->status->message."<br>";
         }
      }

      // ��������, ��� ������������ ����� SOAP ������ ������ ���� � ��������� UTF-8, 
      // ������� ��� ������� ������� ���������� ���� �������������� � UTF-8.
      try 
      {  
         $addRequest = $client->addDomainRfRequest($_POST['nic_hdl'],
                                                   iconv("KOI8-R", "UTF-8", $_POST['domain_name']));       
      }
      catch(SoapFault $fault)
      {
         // �� ������� ������� ������� addDomainRfRequest �� ������� ��� ��� ���������� �����������.
         echo $format1."Couldnt execute addDomainRfRequest".$format2;
         echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
         logout();
         exit();
      }
      if ($addRequest->status->code != '1')
      {
         // ������� ����������, �������� ������������ ������ � ��������������� $addRequest->status->name, ������ ���������.
         echo $format1."Failed to add domain RF request ".$format2;
         echo "Error name: ".$addRequest->status->name." <br>Error message: ".$addRequest->status->message;
         logout();
         exit();
      }
      else
      {
         // �����
         echo "<br>addDomainRfRequest name: ".$addRequest->status->name; 
         echo "<br>addDomainRfRequest message: ".$addRequest->status->message;
         echo "<br>addDomainRfRequest Task ID: ".$addRequest->taskid;
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
