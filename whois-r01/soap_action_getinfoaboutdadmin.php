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
      try 
      {  
         $getinfo = $client->getInfoAboutDadmin($_POST['nic_hdl']);       
      }
      catch(SoapFault $fault)
      {
         // �� ������� ������� ������� getInfoAboutDadmin �� ������� ��� ��� ���������� �����������.
         echo $format1."Couldnt execute getInfoAboutDadmin".$format2;
         echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
         logout();
         exit();
      }
      if ($getinfo->status->code != '1')
      {
         // ������� ����������, �������� ������������ ������ � ��������������� $getInfoAboutDadmin->status->name, ������ ���������.
         echo $format1."Failed to get information about administrator ".$format2;
         echo "Error name: ".$getinfo->status->name." <br>Error message: ".$getinfo->status->message;
         logout();
         exit();
      }
      else
      {
         // �����
         echo "<br>getInfoAboutDadmin status ID: ".$getinfo->status->name; 
         echo "<br>getInfoAboutDadmin message: ".$getinfo->status->message;
         
         $getinfo->data->orgname_ru = iconv('UTF-8'.'KOI8-R',$getinfo->data->orgname_ru);
         $getinfo->data->legal_addr = iconv('UTF-8'.'KOI8-R',$getinfo->data->legal_addr);
         $getinfo->data->postal_addr = iconv('UTF-8'.'KOI8-R',$getinfo->data->postal_addr);
         $getinfo->data->director_name = iconv('UTF-8'.'KOI8-R',$getinfo->data->director_name);
         $getinfo->data->bank = iconv('UTF-8'.'KOI8-R',$getinfo->data->bank);
         $getinfo->data->fiorus = iconv('UTF-8'.'KOI8-R',$getinfo->data->fiorus);
         $getinfo->data->passport = iconv('UTF-8'.'KOI8-R',$getinfo->data->passport);
         
         $protect_data  = ( $getinfo->data->isprotected==1 )  ? '��' : '���';
         $default       = ( $getinfo->data->default==1 )       ? '��' : '���';
         $info_checked  = ( $getinfo->data->info_checked==1 )  ? '��' : '���';

          
         echo "<table border=1>";
         if ( $getinfo->data->is_org == 1 )
         {
            echo "<tr><td colspan=2> ���������� �� �������������� (�����������)</td></tr>";
            echo "<tr><td>Nic-hdl</td><td>".$getinfo->data->nic_hdl."</td></tr>";
            echo "<tr><td>������������ ��-������</td><td>".$getinfo->data->orgname_ru."</td></tr>";
            echo "<tr><td>������������ ��-���������</td><td>".$getinfo->data->orgname_en."</td></tr>";
            echo "<tr><td>���</td><td>".$getinfo->data->inn."</td></tr>";
            echo "<tr><td>���</td><td>".$getinfo->data->kpp."</td></tr>";
            echo "<tr><td>����</td><td>".$getinfo->data->ogrn."</td></tr>";
            echo "<tr><td>����������� �����</td><td>".$getinfo->data->legal_addr."</td></tr>";
            echo "<tr><td>�������� �����</td><td>".$getinfo->data->postal_addr."</td></tr>";
            echo "<tr><td>�������</td><td>".$getinfo->data->phone."</td></tr>";
            echo "<tr><td>����</td><td>".$getinfo->data->fax."</td></tr>";
            echo "<tr><td>E-Mail</td><td>".$getinfo->data->e_mail."</td></tr>";
            echo "<tr><td>��� ���������</td><td>".$getinfo->data->director_name."</td></tr>";
            echo "<tr><td>�������� �����</td><td>".$getinfo->data->bank."</td></tr>";
            echo "<tr><td>��������� ����</td><td>".$getinfo->data->ras_schet."</td></tr>";
            echo "<tr><td>����������������� ����</td><td>".$getinfo->data->kor_schet."</td></tr>";
            echo "<tr><td>���</td><td>".$getinfo->data->bik."</td></tr>";
            echo "<tr><td>������ ������������ ������</td><td>".$protect_data."</td></tr>";
            echo "<tr><td>������������� ��-���������</td><td>".$default."</td></tr>";
            echo "<tr><td>���������� ���������</td><td>".$info_checked."</td></tr>";
         }
         else
         {
            echo "<tr><td colspan=2> ���������� �� �������������� (�������)</td></tr>";
            echo "<tr><td>Nic-hdl</td><td>".$getinfo->data->nic_hdl."</td></tr>";
            echo "<tr><td>��� ��-������</td><td>".$getinfo->data->fiorus."</td></tr>";
            echo "<tr><td>��� ��-���������</td><td>".$getinfo->data->fioeng."</td></tr>";
            echo "<tr><td>���������� ������</td><td>".$getinfo->data->passport."</td></tr>";
            echo "<tr><td>���� ��������</td><td>".$getinfo->data->birth_date."</td></tr>";
            echo "<tr><td>�������� �����</td><td>".$getinfo->data->postal_addr."</td></tr>";
            echo "<tr><td>�������</td><td>".$getinfo->data->phone."</td></tr>";
            echo "<tr><td>����</td><td>".$getinfo->data->fax."</td></tr>";
            echo "<tr><td>E-Mail</td><td>".$getinfo->data->e_mail."</td></tr>";
            echo "<tr><td>������ ������������ ������</td><td>".$protect_data."</td></tr>";
            echo "<tr><td>������������� ��-���������</td><td>".$default."</td></tr>";
            echo "<tr><td>���������� ���������</td><td>".$info_checked."</td></tr>";
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
