<?php

/*
$message = $_GET['message'];
$privkey = $_GET['privatekey'];

echo($privkey);

// Encrypt the data to $encrypted using the public key
openssl_private_encrypt($message, $encrypted, $privkey);

echo $encrypted;
*/
$json = file_get_contents('php://input');


// var_dump($json);
$data_back = json_decode($json);

// var_dump($data_back);

$message = $data_back->{"data"};
$privkey = $data_back->{"privatekey"};
openssl_private_encrypt($message, $encrypted, openssl_get_privatekey($privkey));

//var_dump($encrypted);
// ($message, $encrypted, $privkey);
header("Access-Control-Allow-Origin: *");

echo base64_encode($encrypted);