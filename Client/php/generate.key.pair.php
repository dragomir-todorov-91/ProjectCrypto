<?php

$config = array(
    "digest_alg" => "sha512",
    "private_key_bits" => 1024,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
);
    
// Create the private and public key
$res = openssl_pkey_new($config);

// Extract the private key from $res to $privKey
openssl_pkey_export($res, $privKey);

// Extract the public key from $res to $pubKey
$pubKey = openssl_pkey_get_details($res);
$pubKey = $pubKey["key"];

$data = 'plaintext data goes here';

// Encrypt the data to $encrypted using the public key
openssl_private_encrypt($data, $encrypted, $privKey);

// Decrypt the data using the private key and store the results in $decrypted
openssl_public_decrypt($encrypted, $decrypted, $pubKey);

// За извеждане на ключовете в текстова форма!
//file_put_contents('private.pem', $privKey);
//file_put_contents('public.pem', $pubKey);

$data = array($pubKey, $privKey); 

echo json_encode($data,JSON_UNESCAPED_SLASHES);