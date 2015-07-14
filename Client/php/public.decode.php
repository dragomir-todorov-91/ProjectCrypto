<?php

// Вземаме входящите данни
$json = file_get_contents('php://input');

//var_dump($json);

// Преобразуваме в променлива
$data_back = json_decode($json);

//var_dump($data_back);

// Задаваме двете променливи за по-лесна работа с тях
$message = $data_back->{"data"};
$pubkey = $data_back->{"publickey"};
openssl_public_decrypt(base64_decode($message), $encrypted, openssl_get_publickey($pubkey));
// ($message, $encrypted, $pubkey);

header("Access-Control-Allow-Origin: *");

echo $encrypted;