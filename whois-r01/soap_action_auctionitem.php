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
         $auctionitem_result = $client->auctionItem($_POST['what_do'],$_POST['name'],$_POST['bid'],
                                                  $_POST['notify_send'],$_POST['use_autobroker'],$_POST['autobroker_maxbid']);
          
      }
      catch(SoapFault $fault)
      {
         // �� ������� ������� ������� auctionItem �� ������� ��� ��� ���������� �����������.
         echo $format1."Couldnt execute auctionItem".$format2;
         echo "Fault code: ".$fault->faultcode."<br>Fault message: ".$fault->faultstring;
         logout();
         exit();
      }
      if ($auctionitem_result->status->code != '1')
      {
         // ������� ����������, �������� ������������ ������ � ��������������� $auctionItem_result->status->name, ������ ���������.
         echo $format1."Failed to get auction domain list.".$format2;
         echo "Error name: ".$auctionitem_result->status->name." <br>Error message: ".$auctionitem_result->status->message;
         logout();
         exit();
      }
      else
      {
         // ������� 
         echo "<br><b>auctionItem status ID:</b> ".$auctionitem_result->status->name;          
         echo "<br><b>auctionItem message:</b> ".$auctionitem_result->status->message;

         echo "<table border=1><tr><td>name</td><td>state</td><td>state name</td><td>send notifications</td>
               <td>use autobroker</td><td>max autobroker bid</td><td>current bid</td><td>next bid</td>
               <td>available bids</td><td>taking part</td><td>place</td><td>participant id</td><td>deposit</td><td>bid list</td></tr>";
         $domain=&$auctionitem_result->data;

         $avail_bids = "<table border='1'>";  
         foreach($domain->avail_bids as $bid)
         {
            $avail_bids.="<tr><td>".$bid."</td></tr>";
         }
         $avail_bids.= "</table>";  
         
         $bidlist = "<table border='1'><tr><td>participant ID</td><td>bid</td><td>is auto</td></tr>";  
         foreach($domain->bidlist as $participant_bid)
         {
            $bidlist.="<tr><td>".$participant_bid->participant_id."</td><td>".$participant_bid->bid."</td><td>".$participant_bid->is_auto."</td></tr>";
         }
         $bidlist.= "</table>";  
            echo "<tr><td>".$domain->name."</td><td>".$domain->state."</td><td>".$domain->state_name."</td><td>"
                .$domain->notify_send."</td><td>".$domain->use_autobroker."</td><td>".$domain->autobroker_maxbid." ".$domain->currency_iso
                ."</td><td>".$domain->current_bid." ".$domain->currency_iso."</td><td>".$domain->next_bid." ".$domain->currency_iso
                ."</td><td>".$avail_bids."</td><td>".$domain->taking_part."</td><td>".$domain->place."</td><td>"
                .$domain->participant_id."</td><td>".$domain->deposit." ".$domain->currency_iso."</td><td>"
                .$bidlist."</td></tr>";
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
