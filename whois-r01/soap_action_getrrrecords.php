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
$format3 = "<br>___________________________________________________<br><a href=\"soap_form.html#checktask\">Go back</a>";
echo "<br><b>SOAP server address</b>: $soap_server_address <br>";
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

   try 
   {  
      // �������� ������� ��������� ������ RR ������� �� ������   
      $getrrrecord = $client->getRrRecords($_POST['domain']); 
   }
   catch(SoapFault $fault)
   {
      // �� ������� ������� ������� getRrRecords �� ������� ��� ��� ���������� �����������.
      echo $format1."Couldnt execute getRrRecords".$format2;
      echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
      logout();
      exit();
   }
   if ($getrrrecord->status->code != '1')
   {
      // ������� ����������, �������� ������������ ������ � ��������������� $getrrrecord->status->name, ������ ���������.
      echo $format1."Failed to get RR records ".$format2;
      echo "Error name: ".$getrrrecord->status->name." <br>Error message: ".$getrrrecord->status->message;
      logout();
      exit();
   }
   else
   {
      // ������� �������� ������ �������.
      echo "<br><b>getRrRecords status:</b> ".$getrrrecord->status->name;
      echo "<br><b>getRrRecords message:</b> ".$getrrrecord->status->message;
      echo "<table border=1><tr><td>N</td><td>ID</td><td>Host</td><td>Record type</td><td>Priority</td><td>Weight</td>
      <td>Port</td><td>Value</td><td>Algorithm</td><td>Type cast</td><td>Comment</td></tr>";
      foreach($getrrrecord->data as $key => $record)
      {
         echo "<tr><td>".($key+1)."</td>
         <td>".$record->id."</td>
         <td>".$record->owner."</td>
         <td>".$record->type_record."</td>
         <td>".$record->pri."</td>
         <td>".$record->weight."</td>
         <td>".$record->port."</td>
         <td>".$record->data."</td>
         <td>".$record->sshfp_algorithm."</td>
         <td>".$record->sshfp_type."</td>
         <td>".$record->info."</td></tr>";
      }
      echo '</table>';
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
