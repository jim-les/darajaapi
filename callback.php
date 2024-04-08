<?php

// INCLUDE THE DATABASE CONNECTION FILE
include 'dbcon.php';

// Get the M-Pesa callback data from the input stream
$stkCallbackResponse = file_get_contents('php://input');

// Log the callback response for debugging or auditing purposes
$logFile = "mpesastkresponse.json";
$log = fopen($logFile, "a");

if ($log) {
    fwrite($log, $stkCallbackResponse);
    fclose($log);
} else {
    error_log("Failed to open log file: " . $logFile);
}

// Decode the JSON data
$data = json_decode($stkCallbackResponse);

if ($data && isset($data->Body->stkCallback)) {
    $MerchantRequestID = $data->Body->stkCallback->MerchantRequestID;
    $CheckoutRequestID = $data->Body->stkCallback->CheckoutRequestID;
    $ResultCode = $data->Body->stkCallback->ResultCode;
    $ResultDesc = $data->Body->stkCallback->ResultDesc;

    // Check if the transaction was successful
    if ($ResultCode == 0 && isset($data->Body->stkCallback->CallbackMetadata->Item)) {
        $CallbackMetadata = $data->Body->stkCallback->CallbackMetadata->Item;

        // Extract metadata values
        $Amount = findMetadataValue($CallbackMetadata, 'Amount');
        $TransactionId = findMetadataValue($CallbackMetadata, 'MpesaReceiptNumber');
        $TransactionDate = findMetadataValue($CallbackMetadata, 'TransactionDate');
        $PhoneNumber = findMetadataValue($CallbackMetadata, 'PhoneNumber');

        // Use prepared statements to prevent SQL injection
        $query = "INSERT INTO transaction (MerchantRequestID, CheckoutRequestID, ResultCode, Amount, MpesaReceiptNumber, TransactionDate, PhoneNumber) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($db, $query);

        if ($stmt) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt, "ssissss", $MerchantRequestID, $CheckoutRequestID, $ResultCode, $Amount, $TransactionId, $TransactionDate, $PhoneNumber);

            // Execute the statement
            mysqli_stmt_execute($stmt);

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            // Handle database insertion error
            error_log("Error in database insertion: " . mysqli_error($db));
        }
    } else {
        // Handle non-successful transaction or missing metadata
        // You may log or perform additional actions based on the specific result code or missing metadata
        // For example, log "Transaction expired" or "Transaction cancelled by user"
    }
} else {
    // Handle invalid or missing JSON data
    error_log("Invalid or missing JSON data in the callback");
}

// Helper function to find metadata value by key
function findMetadataValue($metadata, $key)
{
    foreach ($metadata as $item) {
        if ($item->Name == $key && isset($item->Value)) {
            return $item->Value;
        }
    }
    return null;
}

?>
