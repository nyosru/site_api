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
   echo $format1."Couldn`t connect to SOAP server $soap_server_address".$format2;
   echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
   echo $format3;
   exit();
}

if ( (isset($_POST['login']) && isset($_POST['password'])) && (!empty($_POST['login']) && !empty($_POST['password'])) )
{
   // ���� ����� ���������, �������� ���� ������
   $params = Array('domain'=>$_POST['domain'],'state'=>$_POST['state'],'start_from'=>$_POST['start_from'],
                   'start_to'=>$_POST['start_to'],'end_from'=>$_POST['end_from'],'end_to'=>$_POST['end_to'],
                   'price_from'=>$_POST['price_from'],'price_to'=>$_POST['price_to'],'min_symb'=>$_POST['min_symb'],
                   'max_symb'=>$_POST['max_symb'],'tyc_from'=>$_POST['tyc_from'],'pr_from'=>$_POST['pr_from'],
                   'mask'=>$_POST['mask'],'first_reg_date'=>$_POST['first_reg_date']); 
   try 
   {
      // ��������� 
      $loginresult = $client->logIn($_POST['login'],$_POST['password']);
   }
   catch(SoapFault $fault)
   {
      // �� ������� ������� ������� LogIn �� SOAP-������� ��� ��� �� ����������� ���������
      echo $format1."Can`t log in.".$format2;
      echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring."<br>server: $soap_server_address";
      echo $format3;
      exit();
   }
   if ($loginresult->status->code == '0') 
   {
      // ������� LogIn �� ������� ���������� ���������, �� ����������� �� ������ (��������, ������������ ����� � �������)
      echo $format1."Invalid login/password".$format2;
      echo 'Code: '.$loginresult->status->code.'<br>Message: '.$loginresult->status->message."<br>server: $soap_server_address";
      echo $format3;
      exit();
   }
   else
   {
      // ������������, ������ SOAP-������� cookie, ������� ����� ������������ ��� ������ ��������� �������.
      $client->__setCookie('SOAPClient',$loginresult->status->message);
      echo "Successfully logged in as ".$_POST['login']."<br>server: $soap_server_address";
   }

   if (isset($_POST['name']))
   {
      try 
      {         
         //**
         $takeauctiondomain_result = $client->takeAuctionDomain($_POST['name'],$_POST['nic_hdl']);
          
      }
      catch(SoapFault $fault)
      {
         // �� ������� ������� ������� takeAuctionDomain �� ������� ��� ��� ���������� �����������.
         echo $format1."Couldnt execute takeAuctionDomain".$format2;
         echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
         logout();
         exit();
      }
      if ($takeauctiondomain_result->status->code != '1')
      {
         // ������� ����������, �������� ������������ ������ � ��������������� $takeAuctionDomain_result->status->name, ������ ���������.
         echo $format1."Failed to get auction domain list.".$format2;
         echo "Error name: ".$takeauctiondomain_result->status->name." <br>Error message: ".$takeauctiondomain_result->status->message;
         logout();
         exit();
      }
      else
      {
         // ������� 
         echo "<br><b>takeAuctionDomain status ID:</b> ".$takeauctiondomain_result->status->name;          
         echo "<br><b>takeAuctionDomain message:</b> ".$takeauctiondomain_result->status->message;
         echo "<br><b>Nic-hdl:</b> ".$takeauctiondomain_result->nic_hdl;          
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
