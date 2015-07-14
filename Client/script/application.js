// Главен код от приложението
var serverPath = "http://localhost/ProjectCrypto/Server/public";
var storagesJSON; // главна информация на хранлищата
var storagesDataJSON = {}; // цялостна информация съдържаща се в хранлищата
var storagesStatus = {}; // статус на текущия клиент

var connected = 0;
var serverKey = "";

var IPAddress = "";
var ClientPrivateKey;
var ClientPublicKey;
var ServerPublicKey;


$(document).ready(function()
{
  // Глобални променливи
  var selectedItem = 0;
  var mainC = $('#mainContent');

  $(document).on("click", "#connect", function()
	{
  	// Използваме jsonip api за да разберем ip адреса ни
  	$.getJSON("http://jsonip.com/", function (data) {
	IPAddress = data.ip;
	console.log("Your IP address is " + IPAddress);  
	connect();
	});
	});
  
  
  // GUI навигация
  $(".navigation-items").on("click",function()
  {
	var tinav = $(this).index();
	if(selectedItem != tinav)
	{
		
		$(".navigation-items").removeClass("selected-item");
		// var mainC = $('#mainContent');
		if((selectedItem+0) != (tinav+0))
		{
			selectedItem = tinav;
			switch(selectedItem)
			{
				case 0: {$(this).addClass('selected-item'); connectScreen(); break; }
				case 1: {$(this).addClass('selected-item'); manageScreen(); break; }
				case 2: {$(this).addClass('selected-item'); testScreen(); break; }
			}
		}
	}
  });

  function connectScreen()
  {
		mainC.find('.content').addClass('hidden');
		mainC.find('#connectScreen').removeClass('hidden');
  }
  
  function manageScreen()
  {
		mainC.find('.content').addClass('hidden');
		mainC.find('#manageScreen').removeClass('hidden');
  }
  
  function testScreen()
  {
		mainC.find('.content').addClass('hidden');
		mainC.find('#testScreen').removeClass('hidden');
  }

  
  // Функция за получаване на информацията за 
  function connect()
  {
	  
	// За достъп до REST api-то
	$.support.cors=true;
  
	 $.get( serverPath + "/server/storages", function( data )
	{
		if(data == null)
		{
			alert("Не можахме да извлечем адресите на хранилищата!");
		}
		else
		{
			// Променяме цвета на хедъра за индикация на свързаност със сървъра
			$("header").addClass('secondaryHeaderBack');
			
			// Инициализираме променливата
			storagesJSON = $.parseJSON(data);
			
			// Съставяме таблицата по следния шаблона
			/*
			---------------------------------------------------
			| адрес на хранилището | статус | свържи | изтрий |
			---------------------------------------------------
			*/
			
			var connectionTable;
			connectionTable = "<div>Свързан към сървъра със следния IP адрес:" + IPAddress + "</div>";
			
			connectionTable += "<table class='connect-storage-row'>";
			
			for(var i = 0; i < storagesJSON['storages'].length; i++)
			{
				connectionTable += "<tr data-id='"+i+"'><td class='connect-storage-fields'>" + storagesJSON['storages'][i] + "</td>";
				
				connectionTable += "<td class='storage-status connect-storage-fields' ><img src='res/images/loading-animation.gif' width='24px' hight='24px'/></td>";
				
				connectionTable += "<td class='storage-connection storage-button connect-storage-fields'>Свържи се</td>";
				
				connectionTable += "<td class='storage-deletion storage-button connect-storage-fields'>Изтрий връзка</td></tr>";
			}
			
			connectionTable += "</table>";
			$("#response").append(connectionTable);
			
			$("#connect").addClass('hidden');
			
			for(var i=0; i < storagesJSON['storages'].length; i++)
			{	
				
				$.get(storagesJSON['storages'][i] + "storage/" + IPAddress + "").success(function(output, status, xhr)
				{
					var source = xhr.getResponseHeader("Source");

					data = output;
										
					try 
					{
						storagesDataJSON[source-1] = $.parseJSON(data);
					}
					catch (e) 
					{
						console.log("error: "+e);
					};
					
					
					// TODO Debug
					$("#response").append("</br>" + data + " from query " + (source - 1));
					
					if(storagesDataJSON[source-1] != 0)
					{
						storagesStatus[source-1] = 1;
						$('.storage-status:eq('+(source-1)+')').find("img").attr("src","res/images/available.png");
					}
					else
					{
						storagesStatus[source-1] = 0;
						$('.storage-status:eq('+(source-1)+')').find("img").attr("src","res/images/notavailable.png");
					}
					
					
				})
			}
			
			
			// Като част от свързването извършваме следните операции
			// 1. Генерираме двойка ключове
		 	// 2. Запазваме частния ключ на клиента
			// -----------------------------------------------------------------------------
			// 1. Вземаме генерирани ключове, ако имаме такива
			ClientPublicKey = localStorage.getItem('ClientPublicKey');
			ClientPrivateKey = localStorage.getItem('ClientPrivateKey');
			
			
			if(ClientPublicKey == null || ClientPublicKey == null)
			{
				console.log("Generation of new pair of keys!");
				
				// 2. При нужда генерираме двойка ключове
				$.get( "php/generate.key.pair.php", function( data )
				{
					var parsedData;
					
					try 
					{
						parsedData = $.parseJSON(data);
					
						// Присвояваме ключовете в променливи
						ClientPublicKey = parsedData[0];
						ClientPrivateKey = parsedData[1];
						
						//console.log(ClientPrivateKey);
						console.log(ClientPublicKey);
						
				
						// 3. Запазваме частния ключ във localstorage
						localStorage.setItem('ClientPrivateKey',ClientPrivateKey);
						localStorage.setItem('ClientPublicKey',ClientPublicKey);
					}
					catch (e) 
					{
						console.log("error: "+e);
					};
					
				});
			}
			else
			{
						//console.log(ClientPrivateKey);
						console.log(ClientPublicKey);
			}
			
			
			connected=1;
			showStorageManager();
			initTestScreen();
		}	
	});
  }
  
  
  // Регистрация на клиент
  $(document).on("click", ".storage-connection", function()
  {
	/*
		В тази функция правим следните неща
		1. Вземаме ID-то което сме запазили в data-id атрибута
		2. Post заявка за регистрация в съответния storage
		3. Отбелязваме регистрацията чрез клас, променлива
	*/ 
	
	var $this = this;
	
	// 1. Вземаме ID-то което сме запазили в data-id атрибута
	var id = $(this).closest('tr').data('id');
	console.log('Item with id '+id);
	
	// 2. Post заявка за регистрация в съответния storage
	var registrationData = '{"ipaddress":"' + IPAddress + '","publickey":"' + saveEncodedJson(ClientPublicKey) + '"}';
	
	console.log(registrationData);
	
	$.post(storagesJSON['storages'][id] + "storage/register", registrationData)
		  .done(function( data ) 
		{
			if(data == 1)
			{
				// 3. Отбелязваме регистрацията чрез клас, променлива
				alert("Свързването е успешно!");
				$($this).closest('tr').find("img").attr("src","res/images/available.png");
				storagesStatus[id] = 1;
				showStorageManager();
			}
			else if(data == null)
			{
				alert("Свързването е неуспешно!");
				$($this).closest('tr').find("img").attr("src","res/images/notavailable.png");
			}
			else
				alert("Настъпи неочаквана грешка в сървъра!");
					
		});	
  });
  
  
  
  $(document).on("click",".storage-deletion",function()
  {
	  
	/*
		В тази функция правим следните неща
		1. Вземаме ID
		2. Проверяваме дали съответно ID е регистрирано, ако не вадим съобщение за грешка
		3. Прозорец за потвърждение
		4. Post заявка за изтриване в съответния storage
		5. Отбелязваме липсата на регистрация чрез променлива и извеждаме съобщение
	*/ 
	
	var $this = this;
	
	// 1. Вземаме ID-то което сме запазили в data-id атрибута
	var id = $(this).closest('tr').data('id');
	console.log('Attempting deletion of item with id '+id);
	
	// 2. Проверката
	if(storagesStatus[id] === 0)
		alert('Клиента не е регистриран в съответен storage елемент');
	else
	{
		// 3. Прозорец за потвърждение
		var result = confirm("Сигурен ли сте че искате да изтриете елемента?");
	    if (result == true) 
	    {
		  
		  $.ajax({
		    url: storagesJSON['storages'][id] + "storage/"+IPAddress,
		    type: 'DELETE',
		    success: function(data) {
				
		
			if(data == 1)
			{
				// 5. Отбелязваме регистрацията чрез клас, променлива
				alert("Изтриването е успешно!");
				$($this).closest('tr').find("img").attr("src","res/images/notavailable.png");
				storagesStatus[id] = 0;
				showStorageManager();
			}
			else if(data == 0)
			{
				alert("Изтриването е неуспешно!");
				$($this).closest('tr').find("img").attr("src","res/images/available.png");
			}
			else
				alert("Настъпи неочаквана грешка в сървъра!");
				
		    }
		});
		  
		}
	}
  });
  
  
  // функция за декодиране на стринг
  function saveEncodedJson(str) 
  {
	return str.replace(/\n/g, "\\n").replace(/\r/g, "\\\\r").replace(/\t/g, "\\\\t");
  };

  
  // Показване на данните върху втори екран
  function showStorageManager()
  {
	  var $storageMainDiv = $('#storageTable');
	  
	  $('#storageTable').find('.connect-storage-row').remove();
	  $('#storageTable').find('.storageName').remove();
	  $('#storageTable').find('.storageManageElement').remove();
	  
	  var storageLength = storagesJSON['storages'].length;
	  for (var i = 0; i < storageLength; i++)
	  {
		  var html = "<div class='storageManageElement'>"+ storagesJSON['storages'][i] + "</div>";
		  $storageMainDiv.append(html);
	  }
	  
	  for(var i=0; i < storagesJSON['storages'].length; i++)
	  {			
			$.get(storagesJSON['storages'][i] + "storage/clients").success(function(output, status, xhr)
			{
				var source = xhr.getResponseHeader("Source");
		
				data = output;
					
				console.log("Clients from " + source + " recieved!");
					
				try 
				{
					storagesDataJSON[source-1] = $.parseJSON(data);
				}
				catch (e) 
				{
					console.log("error: "+e);
				};
				
				if(storagesDataJSON[source-1].length > 0)
				{
						
					var html;
					html = "<table data-source='"+ (source-1)+"' class='connect-storage-row'><tr class='connect-storage-row'><td class='connect-storage-fields'>ID</td><td class='connect-storage-fields'>IP адрес</td><td class='connect-storage-fields'>Ключ</td><td class='connect-storage-fields'>Обнови</td><td class='connect-storage-fields'>Изтрий</td></tr>";
					
					for(var k=0; k < storagesDataJSON[source-1].length; k++)
					{
						html += "<tr data-id='"+k+"' class='connect-storage-row'><td class='connect-storage-fields'>"+storagesDataJSON[source-1][k]['ID'] 
						+"</td><td class='connect-storage-fields'><input class='editable' type='text' value='"+storagesDataJSON[source-1][k].IPAddress 
						+"'/></td><td class='connect-storage-fields textareaholder'><input class='editable' type='textarea' value='"+JSON.stringify(storagesDataJSON[source-1][k].PublicKey) 
						+"'/></td><td class='connect-storage-fields update-manage-storage'>Обнови"; 
						html += "</td><td class='connect-storage-fields delete-manage-storage'>Изтрий</td></tr>";
					}
					
					html += "</table>";
					
		
					$('.storageManageElement').eq(source-1).before(html);
				}
				else
					$('.storageManageElement').eq(source-1).before('<div class="storageName"> Няма открити записи! </div>');
					
			});
		}	  
}


	// Отбелязване на променени данни от таблиците
	$(document).on("change", '.editable' , function()
	{
		// Запазваме контекста на реда
		var $field = this;
		
		// Вземаме новите данни
		var source = parseInt($($field).closest("table").data('source'));
		
		var id = parseInt($($field).closest('tr').data('id'));
		var newIP = $($field).closest('tr').find("td:nth-child(2)").find("input").val();
		var newKey = $($field).closest('tr').find("td:nth-child(3)").find("input").val();
		
		if(hash(storagesDataJSON[source][id].IPAddress) != hash(newIP))
		{
			$($field).closest('tr').addClass('edited');
		}
		else
			$($field).closest('tr').removeClass('edited');
			
		
		if(hash(storagesDataJSON[source][id].PublicKey) != hash(newKey))
		{
			$($field).closest('tr').addClass('edited');
		}
		else
			$($field).closest('tr').removeClass('edited');
		
	});

	function hash(s){
	return s.split("").reduce(function(a,b){a=((a<<5)-a)+b.charCodeAt(0);return a&a},0);              
	}



	// Обновяване на ред от хранилище
	$(document).on("click", ".update-manage-storage", function()
	{
		// Запазваме контекста на реда
		var $field = this;
		
		var source = parseInt($($field).closest("table").data('source'));
		
		// Вземаме новите данни
		var newIP = $($field).closest('tr').find("td:nth-child(2)").find("input").val();
		var PublickKey = JSON.parse($($field).closest('tr').find("td:nth-child(3)").find("input").val());
			
		// 4. Post заявка за регистрация в съответния storage
		var registrationData = '{"ipaddress":"' + newIP + '","publickey":"' + saveEncodedJson(PublickKey) + '"}';
		
		console.log(registrationData);
		
		$.post(storagesJSON['storages'][source] + "storage/register", registrationData)
			  .done(function( data ) 
			{
				if(data == 1)
				{
					alert("Обновяването е успешно!");
					$($field).closest('tr').removeClass('edited');
					showStorageManager();
					
				}
				else if(data == null)
				{
					alert("Свързването е неуспешно!");
				}
				else
					alert("Настъпи неочаквана грешка в сървъра!");
						
			});	
		
	});



	// Обновяване на ред от хранилище
	$(document).on("click", ".delete-manage-storage", function()
	{
		// Запазваме контекста на реда
		var $field = this;
		
		var source = parseInt($($field).closest("table").data('source'));
		var id = parseInt($($field).closest('tr').data('id'));
		
		// Вземаме новите данни
		var IPAddress = $($field).closest('tr').find("td:nth-child(2)").find("input").val();
			
		// 3. Прозорец за потвърждение
		var result = confirm("Сигурен ли сте че искате да изтриете елемента?");
	    if (result == true) 
	    {
		  
		  $.ajax({
		    url: storagesJSON['storages'][source] + "storage/"+IPAddress,
		    type: 'DELETE',
		    success: function(data) {
				
			if(data == 1)
			{
				// 5. Отбелязваме регистрацията чрез клас, променлива
				alert("Изтриването е успешно!");
				$($field).closest('tr').remove();
				storagesDataJSON[source].splice(id,1);
				showStorageManager();
			}
			else if(data == 0)
			{
				alert("Изтриването е неуспешно!");
			}
			else
				alert("Настъпи неочаквана грешка в сървъра!");
				
		    }
		});
	}
		
	});


	function initTestScreen()
	{
		$("#notConnectedTest").addClass('hidden');
		$("#connectedTest").removeClass('hidden');
		$("#iptest").val(IPAddress);
		var html = '</br>';
		for(var i=0; i<storagesJSON['storages'].length; i++)
		{
			html += "<input type='checkbox' value='"+ storagesJSON['storages'][i] +"'/>"+ storagesJSON['storages'][i]+"</br>";
		}
		$("#selectionStorages").append(html);
	}


	// Последна функция!!!
	$(document).on('click', '#submit', function()
	{
		
		/*
		1. Вземаме съобшението и набор от хранилища
		2. Задаваме първото съобщение с текущото
		3. Извличаме публичен ключ на сървъра
		4. Криптираме информацията с публичен ключ на сървъра (1) и визуализираме
		5. Съставяме променливата със списък на хранилища, IP на клиента и съобщение
		6. Изпращаме на сървъра информацията и проверяваме цялостта и
		7. Намираме клиента и публичния му ключ по IP
		8. Разкриптираме информацията със частен ключ на сървъра (1)
		9. Криптираме информацията със публичен ключ на клиента (3)
		10. Получаваме отговор от сървъра и разкриптираме със частен ключ на клиента (3)
		11. Визуализираме информация!
		*/
		
		
		$('#encrypted').text("Грешка!");
		$('#clientPrivateDecrypted').text("Съобщение");
		
		
		// 1...
		var message = $('#message').val();
		var IPAddress = $('#iptest').val();
		
		var choosenStorages = $("#storagesHelper input:checkbox:checked").map(function(){
	      return $(this).val();
	    }).get();
	    console.log("Selected storages are:" + choosenStorages);
		
		
		// 2...
		$("#messageOriginal").text(message);
		
		
		// 3...
		$.get( serverPath + "/server/key", function( data )
		{
			
			serverKey = $.parseJSON(data);
			
			// В случай на скоби избягваме грешки обхождайки стринга
			serverKey = serverKey.replace('\\\/','\/');
			serverKey = serverKey.replace('\\/','\/');
			serverKey = serverKey.replace('\\\/','\/');
			serverKey = serverKey.replace('\\/','\/');
			serverKey = serverKey.replace('\\\/','\/');
			serverKey = serverKey.replace('\\/','\/');
			
			// 4...
			var encryptedMessage = new Object();
			encryptedMessage.data = message;
			encryptedMessage.publickey = serverKey;
			
		
			var encryptedMessageText = JSON.stringify(encryptedMessage);	
			
			
			$.post("php/public.encode.php", encryptedMessageText)
			.done(function( data ) 
			{
				// 4+...
				$('#serverPublicEncrypted').text(data);
				console.log("Recieved data from public.encode: " + data);
				
				
				// 5. Съставяме променливата..
				
				var encryptedMessage = new Object();
				encryptedMessage.encryptedmessage = data;
				encryptedMessage.ip = IPAddress;
				encryptedMessage.storageids = choosenStorages;
				var encryptedMessageText = JSON.stringify(encryptedMessage);
				
				
				$.post(serverPath + "/server", encryptedMessageText)
				.done(function( data ) 
				{
					// 10...
					
					$('#encrypted').text("Грешка!");		
					if(data == '1')
						alert('Празни хранилища, моля регистрирайте се и изберете подне едно активно!');
					else if(data == '2')
						alert('Настъпи неочаквана грешка. Свържете се с админа на dragomir.todorov@outlook.com');
					else if(data == "3")
						alert("Невалиден ключ в хранилището");
					else
					{
						$('#encrypted').text(data);
						var encryptedMessage = new Object();
						encryptedMessage.data = data;
						encryptedMessage.privatekey = ClientPrivateKey;
					
						var encryptedMessageText = JSON.stringify(encryptedMessage);	
						
						
						$.post("php/private.decode.php", encryptedMessageText)
						.done(function( data ) 
						{	
							// 11...
							
							$('#clientPrivateDecrypted').text(data);
							
							alert('Успешна комуникация!');
							
							console.log("Recieved data from public.encode: " + data);
						});
					}
				
					
					
					
					console.log("Recieved data from public.encode: " + data);
				});
			
			});
		
		});
			
		
	});  
  
  
});