<?php

use models\Storage;

/*
	-------------------------------------------------------
	Това е входни точки на REST API на Storage компонента
	-------------------------------------------------------
	Основни компоненти:
	+ Запис на комбинаиция от Ключ + IP адрес (POST)
	+ Извличане на ред по зададен IP адрес	(GET)
	+ Update на ред по зададено ID	(PUT)
	+ Изтриване на ред по ID / IP Address (DELETE)
	-------------------------------------------------------
*/



$app->post('/storage/register', function () use ($app) 
{	
	// Този контролер очаква следната информация
	// ( ipaddress , publickey )
	
	
	// Присвояваме разкодираните стойности от получения текст в json формат
	$data = json_decode($app->request()->getBody(),true);
 	
	$publickey = $data['publickey'];
	$ipaddress = $data['ipaddress'];

	//openssl_public_encrypt("Brewmaster", $encrypted, $data['publickey']);
	//echo base64_encode($encrypted);
	
	
	// Създаваме нов модел на storage за достъп до БД
	$oStorage = new Storage();	
	
	// Проверка за съществуващ потребител и негов update при наличие
	$oClient = $oStorage->getClientByIP($ipaddress);
	
	if(empty($oClient))
	{
		//Извикваме метод за въвеждане на нов клиент от модела Storage
		$id = $oStorage->insertNewClient($data);
	}
	else
	{
		$oClient[0]['PublicKey'] = $data['publickey'];
		$id = $oStorage->updateClientByIP($oClient[0]);
	}
	
	if($id !== null)
	{
		echo 1;
	}
	else 
	{
		echo null;
	}
	
	
}); 


// GET all 
$app->get('/storage/clients', function () use ($app) 
{	

	// $ipaddress e адреса на потребителя чиито публичен ключ ще изискваме
	
	header("Source: 1");
	
	//Нов модел
	$oStorage = new Storage();
	
	$result = $oStorage->getClients();

	echo(json_encode($result));
});


// Get заявка за получаване на данните за публичен ключ със съответен IP
$app->get('/storage/:ipaddress', function ($ipaddress) use ($app) 
{	
	// $ipaddress e адреса на потребителя чиито публичен ключ ще изискваме
	
	//Нов модел
	$oStorage = new Storage();
	
	$result = $oStorage->getClientByIP($ipaddress);
	
	header("Source: 1");

	
	if( $result == null )
		echo(0);
	else
		echo(json_encode($result));

	
});



$app->put('/storage/update', function () use ($app) 
{	
	// Този контролер очаква следната информация
	// ( ipaddress , publickey )
	
	// Присвояваме разкодираните стойности от получения текст в json формат
	$data = json_decode($app->request()->getBody(),true);
 	
	// Създаваме нов модел на storage за достъп до БД
	$oStorage = new Storage();	
	
	// Опитваме да променим данните по постъпилите от входното API
	$id = $oStorage->updateClientByIP($oClient);
	
	
	if($id !== null)
	{
		// Връщаме JSON с ID на потребителя и неговото IP за тестване на валидността
		$result = array(
		(object)array(
			'ipaddress' => $data['ipaddress'],
			'id' => $id,
		));
		
		echo json_encode($result);
	}
	else 
	{
		echo null;
	}
}); 



$app->delete('/storage/:ipaddress', function ($ipaddress) {
    
	// $ipaddress e адреса на потребителя чиито публичен ключ ще изискваме
	
	//Нов модел
	$oStorage = new Storage();
	
	$result = $oStorage->deleteClientByIP($ipaddress);
	
	if( $result == null )
		echo(1); // Success
	else
		echo(0); // Error
});