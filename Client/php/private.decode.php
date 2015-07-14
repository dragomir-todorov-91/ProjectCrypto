<?php

/*
$message = $_GET['message'];
$privkey = $_GET['privatekey'];

openssl_get_privatekey($privkey);

// Encrypt the data to $decrypted using the public key
openssl_private_decrypt(base64_decode($message), $decrypted, $privkey);

echo $decrypted;
*/

// $data = json_decode(detectRequestBody());

$json = file_get_contents('php://input');

// var_dump($json);
$data_back = json_decode($json);

// var_dump($data_back);
$message = $data_back->{"data"};
$privkey = $data_back->{"privatekey"};
openssl_private_decrypt(base64_decode($message), $decrypted, openssl_get_privatekey($privkey));
// ($message, $decrypted, $privkey);

header("Access-Control-Allow-Origin: *");

// var_dump($decrypted);
echo $decrypted;