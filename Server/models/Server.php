<?php

/**
 * Server model, used to return Server Private and public key
 */

namespace models;
use lib\Core;
use PDO;

class Server 
{
	// Данните за сървъра са запаметени в този файл, използвани са демонстрационни
	protected $core;
	
	function __construct() 
	{
		$this->core = Core::getInstance();
		//$this->core->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	// Извличане на публичен ключ на сървъра
	public function getServerPublicKey()
	{
		$publicKey = "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDZCrYaVkozwJvtouwF60EAHD/V\n/SPT3PXjZADdZwfaMxnGkYg3XKDdIstbZauDYjo8CPvxKoWI2SaJTD5xf2ec2dUn\n+bnmkNkTy05F601EvquAMhpK+cmrjbVZECJvtqKTD2gw1CnYRPMlfp+m23GCV0pK\nSxROjmifWFn47lTOhQIDAQAB\n-----END PUBLIC KEY-----\n";

		return $publicKey;
	}
	
	// Извличане на локация на местата за съхранение на ключовете
	public function getStoragesInfo()
	{
		// Статични данни
		$storage1address = "http://localhost/ProjectCrypto/Server/public/";		
		$storage2address = "http://localhost/ProjectCrypto/Storage2/public/";
		
		// Поради статичната порода на броя сървъри ги задаваме по статичен начин
		$response = '{"storages": ["'.$storage1address.'","'.$storage2address.'"]}';
		
		
		return $response;
	}
	
	// Извличане на частен ключ на сървъра
	public function getServerPrivateKey()
	{
		$privateKey = "-----BEGIN PRIVATE KEY-----\nMIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBANkKthpWSjPAm+2i\n7AXrQQAcP9X9I9Pc9eNkAN1nB9ozGcaRiDdcoN0iy1tlq4NiOjwI+/EqhYjZJolM\nPnF/Z5zZ1Sf5ueaQ2RPLTkXrTUS+q4AyGkr5yauNtVkQIm+2opMPaDDUKdhE8yV+\nn6bbcYJXSkpLFE6OaJ9YWfjuVM6FAgMBAAECgYEAoD7TUsCxCmLD/YETqA8Yr78r\nto+u2ybB+QCFzvHaD1U5S615l/5w2rRs1m9LYbatblPnyBhv/Sju1eIQHHZHhWAK\nDfi6d194wz9dBlfLb0hQKjIPHxn1eUIUzEOEFKf0OHL6qBNdoaPY7gKgYb6GH1E+\nEWupL1c4mQniSTYaDGUCQQDupGnf1bzyghit6U5BEQy8gXAatORv2pH+ejZBgXi5\nq3wwmY8hTSJ9mSgAnQeAV4U2BmJJMKofMmlp0iPycZIvAkEA6NQXmXEfHenEVvtj\nY7DFoXFtFZ3jZKBaLUjOJOF/47f+8eEmEkEuFLVzbdcBEePMFravG4pfalbhFSac\nJ5vBiwJBAKNLs/rredCDvzl51QdnEE0JUVmlAE+dw5rbq7alTqPWOTPUUAGck4ZY\nP+wzh4eF2+x879JfT3nreABmQBzNJBcCQDVmR49awY9DyVNFzvLTXVB8yk8V6kxu\n9p/CPf15drKMxb1GLUwshNwdDb9Ye5dVzr464giIAQBLss3+D/yJnacCQQDYejWm\nO+NxPktAosyUSY8xDZa7jxudCjVcFBLXg7JC+wYulYIOOOARoQBGraD/AjqVxc3L\nUxVUCJJlNghC5aIN\n-----END PRIVATE KEY-----\n";

		return $privateKey;
	}
	
}