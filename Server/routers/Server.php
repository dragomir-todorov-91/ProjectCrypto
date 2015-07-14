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
		$publicKey = "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDOrqMtoHDnzWbJw2wkyzS2IzWc\njpSlZKNZJj2JcGlo6Cz4fOFaQK3bhJ9wI+O9lH\/oQXIZivSTq0C\/4yj4\/4JDp3oR\nfKjorGTxyRwqkE6H73AwKwFNkyRY8SorsYs62zv1LRPeuKjx\/1QHHR7Kn83lWbZp\nsbtZ8X1qjAMp5\/xD0QIDAQAB\n-----END PUBLIC KEY-----\n";	
		
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
		$privateKey =   "-----BEGIN PRIVATE KEY-----\nMIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAM6uoy2gcOfNZsnD\nbCTLNLYjNZyOlKVko1kmPYlwaWjoLPh84VpArduEn3Aj472Uf+hBchmK9JOrQL\/j\nKPj\/gkOnehF8qOisZPHJHCqQTofvcDArAU2TJFjxKiuxizrbO\/UtE964qPH\/VAcd\nHsqfzeVZtmmxu1nxfWqMAynn\/EPRAgMBAAECgYAYiRrN+9HrcrLVGOHprEXhqWRf\n5X039S5\/Es8TVcjtr0JHbWvDLxJ0kNXslhYfF1Xn2H0XEyvdq+LP5MOka5CnCMmY\ntzdfk0ihLDhLuznocpJEI1Vc8qvxherQboFvtCUX1XedHsJdiCBWop5GFgyvd6p9\n79SRXRsM0A4JVK9kUQJBAOYin1Jgc\/LaVffKRf68f\/RIv7lKiOewmyXPT5fdMEeC\n60WygvDz3fyqqOj74rHSnjuLX9FzZHMzd9fjEo+E6\/sCQQDl6TsfF6o03LlncxAE\n0LFcEYBRSUBkFcI1TCw2kx5El98LET+qxJmFsXABsjdqzBur\/VBZBoE329nbYsbW\nI5mjAkAPPXO3jNOg3Xyb8DU6bVk74CVyK9FtJnDfzB7FO8yar7zM3Ml3E2878EFH\nTIbyp7P61Xv+7tlUF7B0B4GPJ4pJAkEA1gMmGoo\/0RdJqqdr2N+eyVItPP1P6Bps\n\/onaei62NSmd4MJ8nmyFb\/K8jYekmFBIze9t9IDele2bvOEUr87RuwJBAM7lzPxy\nLXQZkzlKoM8b3g4CDm1wcBFPjsSab1Z09Di32KdPb2nXtaAdCOhGGqg6gARiDDCK\nwB2EwITW7o7dqnk=\n-----END PRIVATE KEY-----\n";
		return $privateKey;
	}
	
}