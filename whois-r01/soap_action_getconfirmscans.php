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

   if ( isset($_POST['nic_hdl']) )
   {
      // ��������, ��� ������������ ����� SOAP ������ ������ ���� � ��������� UTF-8, ������� ���� � ������������ ���� ��������
      // ������� �������, ���������� ���� �������������� ��� ���� � UTF-8.
      try 
      {         
         $getdocs = $client->getConfirmScans($_POST['nic_hdl']);
      }
      catch(SoapFault $fault)
      {
         // �� ������� ������� ������� getConfirmScans �� ������� ��� ��� ���������� �����������.
         echo $format1."Couldnt execute getConfirmScans".$format2;
         echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
         logout();
         exit();
      }
      if ($getdocs->status->code != '1')
      {
         // ������� ����������, �������� ������������ ������ � ��������������� $getdocs->status->name, ������ ���������.
         echo $format1."Failed to get list of documentd downloaded ".$format2;
         echo "Error name: ".$getdocs->status->name." <br>Error message: ".$getdocs->status->message;
         logout();
         exit();
      }
      else
      {
         // �����
         echo "<br>getConfirmScans name: ".$getdocs->status->name."<br>getConfirmScans message: ".$getdocs->status->message; 
         echo "<table border=1><tr><td>N</td><td>Type ID</td><td>Type name</td><td>Check sum</td></tr>";
         $i=1;
         foreach($getdocs->data as $key => $doc)
         {
            if ( is_object($doc) )
            {
               echo "<tr><td>".$i."</td>";
               echo "<td>".$doc->typeid."</td>";
               echo "<td>".iconv('UTF-8','KOI8-R',$doc->typename)."</td>";
               echo "<td>".$doc->checksum."</td></tr>";
            }
            else
            {
               echo "<tr><td>".$i."</td>";
               echo "<td>".$doc['typeid']."</td>";
               echo "<td>".iconv('UTF-8','KOI8-R',$doc['typename'])."</td>";
               echo "<td>".$doc['checksum']."</td></tr>";
            }
            $i++;
         }
         echo '</table>';
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
