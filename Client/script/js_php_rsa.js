
		// Private Encode
		ClientPrivateKey = localStorage.getItem('ClientPrivateKey');
		ClientPublicKey = localStorage.getItem('ClientPublicKey');
		
		var encryptedMessage = new Object();
		encryptedMessage.data = message;
		encryptedMessage.privatekey = ClientPrivateKey;
		
		var encryptedMessageText = JSON.stringify(encryptedMessage);
		
		$.post("php/private.encode.php", encryptedMessageText)
		.done(function( data ) 
		{
			
			// Public Decode
			var encryptedMessage = new Object();
			encryptedMessage.data = data;
			encryptedMessage.publickey = ClientPublicKey;
		
			var encryptedMessageText = JSON.stringify(encryptedMessage);	
			
			$.post("php/public.decode.php", encryptedMessageText)
			.done(function( data ) 
			{
				
				// Public Encode
				var encryptedMessage = new Object();
				encryptedMessage.data = data;
				encryptedMessage.publickey = ClientPublicKey;
			
				var encryptedMessageText = JSON.stringify(encryptedMessage);	
				
				
				$.post("php/public.encode.php", encryptedMessageText)
				.done(function( data ) 
				{
				
					// Private Decode
					var encryptedMessage = new Object();
					encryptedMessage.data = data;
					encryptedMessage.privatekey = ClientPrivateKey;
				
					var encryptedMessageText = JSON.stringify(encryptedMessage);	
					
					
					$.post("php/private.decode.php", encryptedMessageText)
					.done(function( data ) 
					{
					
					
					
						
						console.log("Recieved data from private.decode: " + data);
					
					});
				
					console.log("Recieved data from private.decode: " + data);
				
				});
				
				
				console.log("Recieved data from private.decode: " + data);
			});
			
			
			console.log("Recieved data from private.decode: " + data);
		});
		