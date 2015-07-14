<?php

/*
	-------------------------------------------------------
	Това е модела на Storage, чрез този модел осъществяваме 
	цялостното четене и запис в БД
	-------------------------------------------------------
	Основни компоненти:
	+ Запис на комбинаиция от Ключ + IP адрес
	+ Извличане на ред по зададен IP адрес
	+ Update на ред по зададено ID
	+ Изтриване на ред по ID / IP Address
	-------------------------------------------------------
	Колони в таблицата StorageTBL:
	| ID | IPAddress | PublicKey |
	------------------------------------------------------
*/

namespace models;
use lib\Core;
use PDO;

class Storage 
{
	protected $core;

	function __construct() 
	{
		$this->core = Core::getInstance();
		//$this->core->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	
	// Въвеждане на данните за нов клиент
	// Запис на комбинаиция от Ключ + IP адрес
	public function insertNewClient($data) 
	{
		try 
		{
			//Създаваме заявка със параметрите, които ще въведем в БД
			$sql = "INSERT INTO storagetbl (IPAddress, PublicKey) 
					VALUES (:ipaddress, :publickey)";
			
			
			//Подготвяме заявката за изпълнение, това позволява да се заместят параметрите с реалните стойности
			$stmt = $this->core->dbh->prepare($sql);
			
			//Избягване на опасните символи, даже при изкуствено подадени заявки извън клиента
			$ipaddress = $data['ipaddress'];
			$publickey = $data['publickey'];
			
			// Обвързваме получените елементи с SQL заявката
			$stmt->bindParam(':ipaddress', $ipaddress);
			$stmt->bindParam(':publickey', $publickey);
			
			if ($stmt->execute($data)) 
			{
				return $this->core->dbh->lastInsertId();
			} 
			else 
			{
				return ;
			}
		
		}
		
		catch(PDOException $e)
		{
			return $e;
		}
		
	}
	
	
	// Извличане на ред по зададен IP адрес
	public function getClientByIP($ipaddress)
	{
		$r = array();		
		
		$sql = "SELECT * FROM storagetbl WHERE ipaddress=:ipaddress";
		$stmt = $this->core->dbh->prepare($sql);
		$stmt->bindParam(':ipaddress', $ipaddress);

		if ($stmt->execute()) {
			$r = $stmt->fetchAll(PDO::FETCH_ASSOC);		   	
		} else {
			$r = 0;
		}		
		return $r;
	}
	
	
	
	// Извличане на ред по зададен IP адрес
	public function getClients()
	{
		$r = array();		
		
		$sql = "SELECT * FROM storagetbl";
		$stmt = $this->core->dbh->prepare($sql);

		if ($stmt->execute()) {
			$r = $stmt->fetchAll(PDO::FETCH_ASSOC);		   	
		} else {
			$r = 0;
		}		
		return $r;
	}
	
	
	
	// Update на ред по зададено ID
	public function updateClientByIP($data)
	{
		$sql = "UPDATE storagetbl 
				SET PublicKey = :publickey 
				WHERE IPAddress = :ipaddress";
				
		$stmt = $this->core->dbh->prepare($sql);
				
		//Избягване на опасните символи, даже при изкуствено подадени заявки извън клиента
		$ipaddress = $data['IPAddress'];
		$publickey = $data['PublicKey'];
				
		$stmt->bindParam(':ipaddress', $ipaddress);
		$stmt->bindParam(':publickey', $publickey);

		if ($stmt->execute()) 
		{
			return 0;	
		} 
		else 
		{
			return null;
		}	
		
	}
	
	// Изтриване на ред по зададен IP адрес
	public function deleteClientByIP($ipaddress)
	{
		$r = array();		
		
		
		
		$sql = "DELETE FROM storagetbl WHERE ipaddress=:ipaddress";
		$stmt = $this->core->dbh->prepare($sql);
		$stmt->bindParam(':ipaddress', $ipaddress);

		if ($stmt->execute()) {
			$r = $stmt->fetchAll(PDO::FETCH_ASSOC);		   	
		} else {
			$r = 0;
		}		
		return $r;
	}
	
}