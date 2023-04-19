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
   
   if ( isset($_POST['id']) && !empty($_POST['id'] ))
   {
      try 
      {  
         // �������� ������� �������������� RR ������   
         $data = array(
            'owner'           => ( isset($_POST['owner']) ) ? $_POST['owner'] : '',
            'data'            => ( isset($_POST['data']) ) ? $_POST['data'] : '',
            'pri'             => ( isset($_POST['pri']) ) ? $_POST['pri']  : '',
            'weight'          => ( isset($_POST['weight']) ) ? $_POST['weight'] : '',
            'port'            => ( isset($_POST['port']) ) ? $_POST['port'] : '',
            'sshfp_algorithm' => ( isset($_POST['sshfp_algorithm']) ) ? $_POST['sshfp_algorithm'] : '',
            'sshfp_type'      => ( isset($_POST['sshfp_type']) ) ? $_POST['sshfp_type'] : '',
            'info'            => ( isset($_POST['info']) ) ? $_POST['info'] : '',
         );    
         $editrecord = $client->editRrRecord($_POST['id'], $data ); 
      }
      catch(SoapFault $fault)
      {
         // �� ������� ������� ������� editRrRecord �� ������� ��� ��� ���������� �����������.
         echo $format1."Couldnt execute editRrRecord".$format2;
         echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
         logout();
         exit();
      }
      if ($editrecord->status->code != '1')
      {
         // ������� ����������, �������� ������������ ������ � ��������������� $editrecord->status->name, ������ ���������.
         echo $format1."Failed to edit RR record ".$format2;
         echo "Error name: ".$editrecord->status->name." <br>Error message: ".$editrecord->status->message;
         logout();
         exit();
      }
      else
      {
      print_r($editrecord->status->message);
         // RR ������ ������� ���������������.
         echo "<br><b>editRrRecord status:</b> ".$editrecord->status->name;          
         echo "<br><b>editRrRecord message:</b> ".$editrecord->status->message;         
      }
   }
   else
   {
      // ������ ����� ��� �� ������
      echo $format1."Invalid (blank) id".$format2;
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