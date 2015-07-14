<?php

use models\Server;
use models\Storage;

// Get заявка за получаване на публичен ключ на сървъра
$app->get('/server/key', function () use ($app) 
{
	//Нов модел
	$oServer = new Server();
	
	$result = $oServer->getServerPublicKey();
	
	echo($result);
	//echo("MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCXzOnhXQrS4LSZ9HV4qje8L3hG5G5Un0YlTscwf6CussguAogTsQLmnroN6XpWLq50Wal/Bpm2UyWUHzoKBZV6YyQAVlRXgzuIibcBFoTsB7xdo//NjOeHHdpv1WDudgFfi41W7hTVzXcFWLEjBthUq2I/NkU8tGG2i5Bp12hTCwIDAQAB");
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
	// ( encryptedmessage , ip?, [array of storage id's] )
	
	// Използваме следния алгоритъм:
	// 1. Извличаме всички параметри като променливи
	// 2. Обхождаме Storage елементите поетапно
	// 3. Намираме publickey на клиент със съответно IP
	// 4. Разшифроваме съобщението
	// 5. Шифроваме съобщението с публичния ключ на клиента
	// 6. Връщаме резултат, ако няма такъв връщаме код за грешка {
	//															1 - липсващ ключ на клиент
	//															2 - неотговарящ storage елемент
	//															3 - друга грешка }
	
	
	// 1. Извличаме данните като параметри
	// Присвояваме разкодираните стойности от получения текст в json формат
	$data = json_decode($app->request()->getBody(),true);
	$message_encrypted = $data['encryptedmessage'];
	$client_ip = $data['ip'];
	$storages = $data['storageids'];
	
	
	
	// Създаваме нов модел на storage за достъп до БД
	$oStorage = new Storage();	
	
	// Проверка за съществуващ потребител и негов update при наличие
	$oClient = $oStorage->getClientByIP($data['ip']);
	if($oClient === null)
	{
		//Извикваме метод за въвеждане на нов клиент от модела Storage
		$id = $oStorage->insertNewClient($data);
	}
	else
	{
		$oClient['publickey'] = $data['publickey'];
		$id = $oStorage->updateClientByIP($oClient);
	}
	
	
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

/*

// Get заявка за получаване на данните за всички програми
$app->get('/program/', function () use ($app) 
{	
	//Нов модел
	$oProgram = new Program();
	
	$result = $oProgram->getAllPrograms();
	
	if( $result == null )
		echo(0);
	else
		echo(json_safe($result));

	
});


// Get Заявка за получаване на информация за конкретен тренировъчен ден (Workout) със всички прилежащи упражнения и мускулни групи
$app->get('/program/workout/:trainingprogramid/:doneexercises', function ($trainingprogramid, $doneexercises) use ($app) 
{	
	// Контролера очаква workoutid, и чрез функиите getWorkout и getExercises взема и йерархично подрежда цялата информация
	//Нов модел
	$oProgram = new Program();
	
	$workIDs = $oProgram->getAllWorkouts($trainingprogramid);
	
	$workoutid = $workIDs[ $doneexercises % count($workIDs)];
	$workoutid = $workoutid['ID'];
	
	// Вече разполагаме със съответно ID на упражнение, извличаме данните и предоставяме на клиента
	$result = $oProgram->getWorkout($workoutid);
	
	if($result == 0)
		echo(1);
	else
	{
		// Вземаме всичките данни на потребителя, за представяне в потребителския интерфейс на мобилното ни приложение
		$result = $result[0];
		
		//array_push ( $result[0] , $result[0] );
		
		$result['Exercise'] = $oProgram->getExercises($workoutid);
		
		echo(json_safe($result));
		
	}
	
});

*/



