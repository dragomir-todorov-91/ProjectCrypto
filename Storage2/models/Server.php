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
		$publicKey = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCXzOnhXQrS4LSZ9HV4qje8L3hG5G5Un0YlTscwf6CussguAogTsQLmnroN6XpWLq50Wal/Bpm2UyWUHzoKBZV6YyQAVlRXgzuIibcBFoTsB7xdo//NjOeHHdpv1WDudgFfi41W7hTVzXcFWLEjBthUq2I/NkU8tGG2i5Bp12hTCwIDAQAB";
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
		$privateKey =   "MIICWwIBAAKBgQCXzOnhXQrS4LSZ9HV4qje8L3hG5G5Un0YlTscwf6CussguAogTsQLmnroN6XpWLq50Wal/Bpm2UyWUHzoKBZV6YyQAVlRXgzuIibcBFoTsB7xdo//NjOeHHdpv1WDudgFfi41W7hTVzXcFWLEjBthUq2I/NkU8tGG2i5Bp12hTCwIDAQABAoGAZcKTImU55UWHLBGJUhthg4Ca84McRxQCdO4Lb4KPF613rgg6amDmHe1Xsg3K2c0vW4Xjruig0G2KqnIIeUFnAvzFU345vcZ44wOBOXv383RDI1+gx9boSFmPpwDnf99pRSvS5FHsg1ah+0Bi640M5/ixWwnhZypQVND5HzIMZvkCQQDM6W3pWjkCONz/PiVoEWSwoDd+W/a0rNpV9m9fQTmyVbimI9llqElKgBZRhJxnyTmjcwtszx45NLaTDAKCKzSVAkEAvaWhg7l3zWS4FxDeWt2Ppf5ApN30n2op9t/JGeVoYMRfa8KhvkjSWRVr6UwwwQcvviCDLGJ+R5MGaGuaS57hHwJAASdbUBIK+iE51VITQde/OmNcjKCV7gR/IjbF/8JNQdzLslvHUJQ2/W1lMsZNXyQ559qw+Mbdw7KxSHir1vHJVQJAPltwwSxBvTJp6YOtaTlC23S/crZckfJJfBhXOoGZEYBgU8F6h2PQbmRByEM6IK4pP53XI8f697PbPohrAoRqRwJAYTiGwyLpiXDIXkm7lCxKeDHhiz+Zn6NUHhT8LSX0zXPShMhrn9oWQ4S7CgW94VJTRWAcX686LvzxhSDCzFVlww==";
		return $privateKey;
	}
	
}