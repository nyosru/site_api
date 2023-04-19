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
      // �������� ������� �������� ��������� �������� �����       
      $getbalanceinfo_result = $client->getBalanceInfo(); 
   }
   catch(SoapFault $fault)
   {
      // �� ������� ������� ������� getBalanceInfo �� ������� ��� ��� ���������� �����������.
      echo $format1."Couldnt execute getBalanceInfo".$format2;
      echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
      logout();
      exit();
   }
   if ($getbalanceinfo_result->status->code != '1')
   {
      // ������� ����������, �������� ������������ ������ � ��������������� $getBalanceInfo_result->status->name, ������ ���������.
      echo $format1."Failed to get balance information ".$format2;
      echo "Error name: ".$getbalanceinfo_result->status->name." <br>Error message: ".$getbalanceinfo_result->status->message;
      logout();
      exit();
   }
   else
   {
      // ������� ��������� c�������� �������� �����.
      echo "<br><b>getBalanceInfo status ID:</b> ".$getbalanceinfo_result->status->name;          
      echo "<br><b>getBalanceInfo message:</b> ".$getbalanceinfo_result->status->message;         
      
      // ------------
      echo "<p><b>���������� �� �������� �����:</b></p>";
      echo "<p>����� ��������� �������� �� �����: ".$getbalanceinfo_result->data->pays_all.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>������� ��������� �������� �� �����: ".$getbalanceinfo_result->data->vpays.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>������� �������-��������� ����� �� �����: ".$getbalanceinfo_result->data->pays_closed.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>������������� ������� �� ������: ".$getbalanceinfo_result->data->blocked.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>�������� ��� ����������: ".$getbalanceinfo_result->data->free.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>������ �������� �����: ".$getbalanceinfo_result->data->balance.' '.$getbalanceinfo_result->data->currency."</p>"; 
      //----------
      echo "<p><b>���������� �� ��������� �����:</b></p>";
      echo "<p>����� ��������� �������� �� �����: ".$getbalanceinfo_result->data->bonus_pays.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>������������� ������� �� ������: ".$getbalanceinfo_result->data->bonus_blocked.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>������� �������-��������� ����� �� �����: ".$getbalanceinfo_result->data->bonus_closed.' '.$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>�������� ��� ����������: ".$getbalanceinfo_result->data->bonus_free.' '.$getbalanceinfo_result->data->currency."</p>"; 
      //----------
      echo "<p><b>���������� � ������� ������:</b></p>";
      echo "<p>������: ".$getbalanceinfo_result->data->currency."</p>"; 
      echo "<p>�������� �� ������: ".$getbalanceinfo_result->data->with_taxes."</p>"; 

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
