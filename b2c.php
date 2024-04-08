<?php
include 'accessToken.php';
include 'securitycridential.php';
$b2c_url = 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';
$InitiatorName = 'testapi';
$pass = "Safaricom999!*!";
$BusinessShortCode = "600999";
$phone = "254708374149";
$amountsend = '1';
//$SecurityCredential ='PNjRJokY3TvoPlerXTM1t1gGJsSVpfQVaOtpSQ6XRoTxCGa7OaW5Io9xKWPDs+8Q/PKspUL5AsBMRJOo+1gviBKbLvnUyntZlcWaHV34mZs65OTXUM9Xk0X/Ji2E7yLCTOiXmJUlDdvXHjIezipkxCwXu93gHcYQINqAzSz6JhVx/jLV94iDIvb/XHT4NsLltCYcgclVXaAmx1M5aGobYpkjmaUDiKTPX0FHJmWlEBgmeD/OpTbhiDevAE4WMPQyRMBt7H62RYvMErBPJcdNBK5jvtDd6Q5iWMkVoujSbjD8qaN/GDZWpfp9s1C1iFr50I0BP+nGWv3Y1r30v3W92w==';
$CommandID = 'SalaryPayment'; // SalaryPayment, BusinessPayment, PromotionPayment
$Amount = $amountsend;
$PartyA = $BusinessShortCode;
$PartyB = $phone;
$Remarks = 'Umeskia Withdrawal';
$QueueTimeOutURL = 'https://1c95-105-161-14-223.ngrok-free.app/MPEsa-Daraja-Api/b2cCallbackurl.php';
$ResultURL = 'https://1c95-105-161-14-223.ngrok-free.app/MPEsa-Daraja-Api/dataMaxcallbackurl.php';
$Occasion = 'Online Payment';
/* Main B2C Request to the API */
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $b2c_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token]);
$curl_post_data = array(
    'InitiatorName' => $InitiatorName,
    'SecurityCredential' => $SecurityCredential,
    'CommandID' => $CommandID,
    'Amount' => $Amount,
    'PartyA' => $PartyA,
    'PartyB' => $PartyB,
    'Remarks' => $Remarks,
    'QueueTimeOutURL' => $QueueTimeOutURL,
    'ResultURL' => $ResultURL,
    'Occasion' => $Occasion
);
$data_string = json_encode($curl_post_data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
$curl_response = curl_exec($curl);
echo $curl_response;