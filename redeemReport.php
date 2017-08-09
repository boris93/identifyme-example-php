<?php

// Read the encrypted report from POST request
$report = $_POST['encryptedReport'];
$secret = "<YOUR_CLIENT_SECRET>";

$postdata = http_build_query(
    array(
        'report' => $report,
        'clientSecret' => $secret
    )
);

$opts = [
    "http" => [
        "method" => "POST",
        "header" => "content-type: application/x-www-form-urlencoded\r\n",
        "content" => $postdata
    ]
];
$context = stream_context_create($opts);
$response = json_decode(file_get_contents('https://identifyme.net/api/getreport', false, $context));

$apiResponseObj = new stdClass;

if($response->isValid){
	// Successfullly verified
	$userCountryCode = $response->countryCode;
	$userPhone = $response->phone;
	$userEmail = $response->email;
	$userEmailVerified = $response->emailVerified;
	$verificationTimestampMilliSeconds = $response->generatedAt;
	// TODO Add the details to your database and return session data to the frontend
	$apiResponseObj->userId = "<USER_ID>";
	$response->status = "success";
}
else{
	// Error in verification, ask the frontend to retry
	$apiResponseObj->status = "failure";
	$apiResponseObj->statusMessage = "Invalid OTP";
}

echo json_encode($response);
