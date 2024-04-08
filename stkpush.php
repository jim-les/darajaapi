<?php
include 'accessToken.php';

// Constants
$processRequestUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$callbackUrl = 'https://eminently-rare-pegasus.ngrok-free.app/Ace-cleaners/cleaning-services-website/darajaapi/callback.php';
$passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
$businessShortCode = '174379';
$phone = '254799118166';
$money = '1';
$partyA = $phone;
$partyB = '174379'; // Business shortcode as PartyB
$accountReference = 'Transline';
$transactionDesc = 'stkpush test';
$amount = $money;

// Timestamp
$timestamp = date('YmdHis');
date_default_timezone_set('Africa/Nairobi');

// Encrypt data to get password
$password = base64_encode($businessShortCode . $passkey . $timestamp);

// Initiate cURL
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $processRequestUrl);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
    'BusinessShortCode' => $businessShortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $partyA,
    'PartyB' => $partyB,
    'PhoneNumber' => $partyA,
    'CallBackURL' => $callbackUrl,
    'AccountReference' => $accountReference,
    'TransactionDesc' => $transactionDesc,
]));

// Execute cURL request
$curlResponse = curl_exec($curl);

// Handle response
$data = json_decode($curlResponse);
$checkoutRequestID = $data->CheckoutRequestID;
$responseCode = $data->ResponseCode;

// Echo response
if ($responseCode == "0") {
    echo "The CheckoutRequestID for this transaction is: " . $checkoutRequestID;
} else {
    echo "Error: " . $responseCode . " - " . $data->errorMessage;
}

// Close cURL
curl_close($curl);
?>
