<?php

use models\Server;
use models\Storage;


function multiRequest($data, $options = array()) {
 
  // array of curl handles
  $curly = array();
  // data to be returned
  $result = array();
 
  // multi handle
  $mh = curl_multi_init();
 
  // loop through $data and create curl handles
  // then add them to the multi-handle
  foreach ($data as $id => $d) {
 
    $curly[$id] = curl_init();
 
    $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
    curl_setopt($curly[$id], CURLOPT_URL,            $url);
    curl_setopt($curly[$id], CURLOPT_HEADER,         0);
    curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
 
    // post?
    if (is_array($d)) {
      if (!empty($d['post'])) {
        curl_setopt($curly[$id], CURLOPT_POST,       1);
        curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
      }
    }
 
    // extra options?
    if (!empty($options)) {
      curl_setopt_array($curly[$id], $options);
    }
 
    curl_multi_add_handle($mh, $curly[$id]);
  }
 
  // execute the handles
  $running = null;
  do {
    curl_multi_exec($mh, $running);
  } while($running > 0);
 
 
  // get content and remove handles
  foreach($curly as $id => $c) {
    $result[$id] = curl_multi_getcontent($c);
    curl_multi_remove_handle($mh, $c);
  }
 
  // all done
  curl_multi_close($mh);
 
  return $result;
}


// Get заявка за получаване на публичен ключ на сървъра
$app->get('/server/key', function () use ($app) 
{	
	//Нов модел
	$oServer = new Server();
	
	$result = $oServer->getServerPublicKey();
	
	echo(json_encode($result)); 
});


// Get заявка за получаване на Storage ID's
$app->get('/server/storages', function () use ($app) 
{
	//Нов модел
	$oServer = new Server();
	
	$result = $oServer->getStoragesInfo();
	
	echo("$result");
});


$app->post('/server', function () use ($app) 
{	
	// Този контролер очаква следната информация
	// ( encryptedmessage , ip, [array of storage id's] )
	
	// Използваме следния алгоритъм:
	// 1. Извличаме всички параметри като променливи
	// 2. Обхождаме Storage елементите поетапно
	// 3. Намираме publickey на клиент със съответно IP
	// 4. Разшифроваме съобщението
	// 5. Шифроваме съобщението с публичния ключ на клиента
	// 6. Връщаме резултат, ако няма такъв връщаме код за грешка {
	//															1 - липсващ ключ на клиент
	//															2 - друга грешка }
	
	$data = json_decode($app->request()->getBody(),true);
	
	// 1. Извличаме данните като параметри
	// Присвояваме разкодираните стойности от получения текст в json формат
	
	$encryptedmessage = $data['encryptedmessage'];
	$client_ip = $data['ip'];
	$storages = $data['storageids'];
	
	// 2. Чрез curl обхождаме елементите по $storages[i]+'/storage/:ipaddress'
	if(count($storages) == 0)
		echo 1;
	else
	{
		$curlHelperData = array();
	
		for($i = 0; $i < count($storages); $i++)
		{
			array_push($curlHelperData, $storages[$i].'storage/'.$client_ip);
		}
		
		//var_dump($curlHelperData);
		$r = multiRequest($curlHelperData);
		//var_dump($r);
		
		$resultfound = false;
		$clientPublicKey = null;
		$keyMissmatch = false;
		
		// 3. Намираме publickey на клиент със съответно IP
		for($i = 0; $i < count($r); $i++)
		{
			if($r[$i] != '0')
			{
				$jsonDecoded = json_decode($r[$i], true);
				//var_dump($jsonDecoded);
				
				//var_dump($clientPublicKey);
				//var_dump($jsonDecoded[0]['PublicKey']);
				
				$resultfound = 1;
				if($clientPublicKey == null)
					$clientPublicKey = $jsonDecoded[0]['PublicKey'];
				else if($clientPublicKey != $jsonDecoded[0]['PublicKey'])
					$keyMissmatch = true;
			}
		}
		
		if($resultfound == false)
			echo 1;
		else if($keyMissmatch == true)
			echo 2;
		else
		{
			// Разкодираме съобщение със частния ключ на сървъра
			$oServer = new Server();
			$ServerPrivKey = $oServer->getServerPrivateKey();
			
			//var_dump($clientPublicKey);
			
			openssl_private_decrypt(base64_decode($encryptedmessage), $decrypted, openssl_get_privatekey($ServerPrivKey));
			//var_dump($decrypted);
			
			if(openssl_get_publickey($clientPublicKey) == false)
				echo "3";
			else
			{
				openssl_public_encrypt($decrypted." from server", $serverEncrytped, openssl_get_publickey($clientPublicKey));
				
				echo base64_encode($serverEncrytped);
			}
					
			
		}
		
	}
}); 