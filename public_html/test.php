<?php
$consumer_key = "9D/HcSLmjiOBO5YnVjImAS5ALWbzFmx/";
$consumer_secret = "4QZPR7EkQeIMpIpIC+3BMNzK+Gw=";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://pay.pesapal.com/v3/api/Auth/RequestToken", // change to pay.pesapal.com if live
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode([
        "consumer_key" => $consumer_key,
        "consumer_secret" => $consumer_secret
    ]),
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
]);

$response = curl_exec($curl);
curl_close($curl);

echo $response;
