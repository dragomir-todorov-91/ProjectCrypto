<?php

use models\User;
use models\Program;

//Помощна функция за изпращане на email
function smtpmailer($to, $from, $from_name, $subject, $body) { 
	require_once("../vendor/PHPMailer/PHPMailerAutoload.php");
	
	global $error;
	$mail = new PHPMailer();  // create a new object
	$mail->IsSMTP(); // enable SMTP
	$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
	$mail->SMTPAuth = true;  // authentication enabled
	$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 465;
	$mail->Username = "trainingdaybot@gmail.com";  
	$mail->Password = "";           
	$mail->SetFrom($from, $from_name);
	$mail->Subject = $subject;
	$mail->Body = $body;
	$mail->AddAddress($to);
	if(!$mail->Send()) {
		$error = 'Mail error: '.$mail->ErrorInfo; 
		return false;
	} else {
		$error = 'Message sent!';
		return true;
	}
}


// Помощна функция за изпращане на кода за потвърждение на потребителя
function sendConfirmCode($email, $confirmCode)
{
	// ДА СЕ смени email - всичко се праща на личния !!!
	
	if (smtpmailer('dragoto91@gmail.com', 'TrainingDayBot@mail.com', 'TrainingDay', 'Training Day Registration', 'Здравейте! Това е кода, който да използвате в приложението: '.$confirmCode)) {
			return 0;
		}
	if (!empty($error)) return $error;
	
}


$app->post('/user/register', function () use ($app) 
{	
	// Този контролер очаква следната информация
	// ( username , email, password )
	// Очаква се тази информация да е валидирана от клиента, но се прави и проверка.... КЪДЕ? - в модела или контролера....
	
	// Присвояваме разкодираните стойности от получения текст в json формат
	$data = json_decode($app->request()->getBody(),true);
 	
	// Създаваме нов модел на потребителя
	$oUser = new User();	
	
	//Извикваме метод за въвеждане на потребител от модела User
	$id = $oUser->insertUser($data);
	
	if($id !== null)
	{
		$confirmCode = substr(hash("sha1", $id), 0, 5); // Използваме първите 5 символа от кодираното ID за потвърждаване на профила
		
		sendConfirmCode($data['email'], $confirmCode);
		
		$result = array(
		(object)array(
			'username' => $data['username'],
			'id' => $id,
		));
		
		echo json_encode($result);
	}
	else 
	{
		echo null;
	}
}); 


$app->post('/user/confirm', function () use ($app) 
{	
	// Този контролер очаква следната информация
	// ( email , enteredCode )
	// Подава се заявка към модела за откриване на id на потребител с потребителско име - username, 
	// след това се прави хеширане и се проверява дали първите 5 символа съвпадат с подадените
	// Ако е така, потвърждаваме потребителя чрез друга функция предоставен ни от модела
	
	// Присвояваме разкодираните стойности от получения текст в json формат
	$data = json_decode($app->request()->getBody(),true);
	
	//Нов модел
	$oUser = new User();
	
	$id = $oUser->getUserIDByEmail($data['email']);
	if($id != null)
	{
		$userCode = substr(hash("sha1", $id[0]['id']), 0, 5);
		if($userCode == $data['confirmCode'])
		{
			$oUser->confirmUser($id[0]['id']);
			echo("confirm");
		}
	}
	else echo(null);
});


$app->post('/user/forgotten', function () use ($app) 
{	
	// Този контролер очаква следната информация
	// ( email )
	// Подава се заявка към модела за откриване на id на потребител с потребителско име - username, 
	// след това се прави хеширане и се проверява дали първите 5 символа съвпадат с подадените
	// Ако е така, потвърждаваме потребителя чрез друга функция предоставен ни от модела
	
	// Присвояваме разкодираните стойности от получения текст в json формат
	$data = json_decode($app->request()->getBody(),true);
	
	//Нов модел
	$oUser = new User();
	
	$id = $oUser->getUserIDByEmail($data['email']);
	if($id != null)
	{
		$confirmCode = substr(hash("sha1", $id[0]['id']), 0, 5); // Използваме първите 5 символа от кодираното ID за възтановяване на парола
		
		sendConfirmCode($data['email'], $confirmCode);
		
		echo('confirm');
	}
	else echo(null);
});


$app->post('/user/changepass', function () use ($app) 
{	
	// Този контролер очаква следната информация
	// ( email , password, confirmCode)
	// Подава се заявка към модела за откриване на id на потребител с потребителско име - username, 
	// след това се прави хеширане и се проверява дали първите 5 символа съвпадат с подадените
	// Ако е така, потвърждаваме потребителя чрез друга функция предоставен ни от модела
	
	// Присвояваме разкодираните стойности от получения текст в json формат
	$data = json_decode($app->request()->getBody(),true);
	
	//Нов модел
	$oUser = new User();
	
	
	
	$id = $oUser->getUserIDByEmail($data['email']);
	if($id != null)
	{
		$userCode = substr(hash("sha1", $id[0]['id']), 0, 5);
		
		if($userCode == $data['confirmCode'])
		{
			if($oUser->changepass($data) == 0)
			{
					echo("confirm");
			}
		}
		else echo("null");
	}
	else echo(null);
});


$app->post('/user/login', function () use ($app) 
{	
	// POST заявка към public/user/login връща в json формат, ако потребителя е наличен и изряден всичката информация за него
	// ако има грешка то тя се интерпретира по следния начин: 1 - няма такъв потребител, 2 - потребителя не е потвърдил своя акаунт
	$data = json_decode($app->request()->getBody(),true);
	
	//Нов модел
	$oUser = new User();
	
	// Проверка за съществуващ потребител
	$result['profile'] = $oUser->getUserByLogin($data);
	
	if($result['profile'] == null)
		echo(1);
	else
	{
		$result = $result['profile'][0];
		if($result['ConfirmedAccount'] == 0)
		{
			$confirmCode = substr(hash("sha1", $result['ID']), 0, 5);
			sendConfirmCode($result['Email'], $confirmCode);
			echo(2);
		}
		else 
		{
			// Вземаме всичките данни на потребителя, за представяне в потребителския интерфейс на мобилното ни приложение
			$result['Data'] = $oUser->getUserData($result['ID']);
			
			echo(json_encode($result));
		}
	}
});


$app->post('/user/updateprofile', function () use ($app) 
{
	// Този контролер очаква следната информация
	// ( id, gender, skipexercisen)
	// Подава се заявка към модела за задаване на двете стойности на потребителя за предоставяне 
	// на програми специфични за този потребител 
	// Ако всичко мине както трябва връщаме confirm
	
	// Присвояваме разкодираните стойности от получения текст в json формат
	$data = json_decode($app->request()->getBody(),true);
	
	//Нов модел
	$oUser = new User();
	
	if($oUser->updateUser($data)==0)
		echo("confirm");
	else echo("null");
});


$app->post('/user/startprogram', function() use ($app)
{
	// Този контролер очаква следната информация
	// ( userid, programid )
	// Подава се заявка към модела за създаване на нови данни за потребителя
	$data = json_decode($app->request()->getBody(),true);
	
	$oUser = new User();
	
	if($oUser->newUserData($data) == 0)
	{
		echo('c'.json_encode($oUser->getUserData($data['userid'])));
	}
	else echo('error');
});


$app->post('/user/advanceprogram', function() use ($app)
{
	// Този контролер очаква следната информация
	// ( userid, programid )
	// Увеличава тренировките на програмата или при достигане на края на програмата 
	// (TrainingProgramTBL.DurationInWeeks * sum(WorkoutTBL.WorkoutInWeeks))
	// Ако всичко мине както трябва връщаме confirm
	$data = json_decode($app->request()->getBody(),true);
	
	$oUser = new User();
	$oProgram = new Program();
	
	// Вземаме данните на потребителя и сравняваме коректността на данните с получените	
	// ( дали последната програма на потребителя е с trainingprogramid, което постъпва на входа )
	$UserData = $oUser->getUserData($data['userid']);
	$lastData = end($UserData);
	if($lastData['UserID'] != $data['userid'] || $lastData['TrainingProgramID'] != $data['trainingprogramid'])
	{
		echo('Error');
		return 1;
	}
	
	// Изчисляваме броя на всички тренировки след което програмата се обявява за приключена	
	$ActiveProgram = $oProgram->getProgram($data['trainingprogramid']);
	$ProgramWorkouts = $oProgram->getAllWorkouts($data['trainingprogramid']);
	
	$numWorkoutsWeek = 0;
	for($i = 0; $i < count($ProgramWorkouts); $i++)
		$numWorkoutsWeek += $ProgramWorkouts[$i]['WorkoutsInWeek'];
	
	// Проверяваме информацията и настройваме новата информация на потребителя
	$lastData['WorkoutsCompleted'] = $lastData['WorkoutsCompleted'] + 1;
	if($lastData['WorkoutsCompleted'] >= ($ActiveProgram[0]['DurationInWeeks'] * $numWorkoutsWeek))
		$lastData['ProgramFinished'] = 1;
	else
		$lastData['ProgramFinished'] = 0;
	
	// Обновяваме информацията на потребителя и изпращаме потвърждение към клиента
	if(($flag = $oUser->updateUserData($lastData)) == 0)
		echo('confirm');
	else echo('error');	
});