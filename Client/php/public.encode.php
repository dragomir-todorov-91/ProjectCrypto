<?php

/*
$json = file_get_contents('php://input');

$data_back = json_decode($json);

var_dump($data_back);

$message = $data_back->{"data"};
$publickey = base64_decode($data_back->{"publickey"});

	//openssl_public_encrypt("Brewmaster", $encrypted, $data['publickey']);
	//echo base64_encode($encrypted);


var_dump($publickey);
//$publickey = openssl_get_privatekey($publickey);

$publickey = "-----BEGIN PUBLIC KEY-----MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8sz2pY4zknCD4pFmZ7aBaJOOtlL0qOYOYnZG4ZDMP+ZD4XGY1BbWWWxdpPm9gHC3q8j8wS8+Z3cysMFhDA6kG7a4YOVWuRleIM0tcIVYfHfsmMyz7pwzm0P+XScIN+xzvYk4gB3+uuN7uvfaMImfZ7HIHYtxKCYWSjen6LI4ZwQIDAQAB-----END PUBLIC KEY-----";
var_dump(openssl_pkey_get_public($publickey));
openssl_public_encrypt($message, $encrypted, openssl_pkey_get_public($publickey));

header("Access-Control-Allow-Origin: *");

var_dump($encrypted);
echo $encrypted;
*/

$json = file_get_contents('php://input');

// var_dump($json);

//var_dump($json);

// Преобразуваме в променлива
$data_back = json_decode($json);

//var_dump($data_back);

// Задаваме двете променливи за по-лесна работа с тях
$message = $data_back->{"data"};
$pubkey = $data_back->{"publickey"};

//var_dump($pubkey);

openssl_public_encrypt($message, $encrypted, openssl_get_publickey($pubkey));
// ($message, $encrypted, $pubkey);

header("Access-Control-Allow-Origin: *");

echo base64_encode($encrypted);