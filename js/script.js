window.allowed_to_request = 1;

$(document).ready( function() {
	setInterval("request_message()", 6000);
});

$(document).ready( function() {
	$("#sendbutton").click(send_message);
});

$(document).keypress(function(event) {
	if (event.keyCode == '13' && $("#message").is(":focus")) {
		/* If the key was 'Return' and the input box #message is in focus */
		$("#sendbutton").click();
	}
});

function request_message() {
	if (window.allowed_to_request == 0) {
		return 0;
	}
	
	window.allowed_to_request = 0;
	$.ajax({
		url: 'ajax.php',
		type: 'POST',
		data: {
			get_message: "true",
			old_message: $("#lastmsg").html()
			//nick: $("#msgnum").html(),
			//lastmsg: $("#lastmsg").html()
		},
		dataType: 'text'
	}).done(function (text) {
		window.allowed_to_request = 1;
		handle_message(text);
	});
}

function get_latest() {
	$.ajax({
		url: 'ajax.php',
		type: 'POST',
		data: {
			get_message: "true"
		},
		dataType: 'text'
	}).done(function (text) {
		handle_message(text);
	});
	;
}

function handle_message(text) {
	//$("#lastmsg").html();
	if (text != "0") {
		// Make a new array to split the 
		var msgnum = new Array();
		msgnum = text.split('<');
		$("#lastmsg").html(msgnum[0]);
		
		msgnum = msgnum[0].toString();
		
		text = text.substr(msgnum.length, text.length);
		

		$("#dataDisplay").append(text);
		
		// Scroll to the bottom automatically
		$("#dataDisplay").prop({ scrollTop: $("#dataDisplay").prop("scrollHeight") });
		/*$.ajax({
			url: 'ajax.php',
			type: 'POST',
			data: {
				htmlit: text,
				msgnum: $("#msgnum").html()
			},
			dataType: 'text'
		}).done(function (text) {
			$("#dataDisplay").append(text);
		})*/
	}
}

function send_message() {
	if (!validate()) {
		//If not valid
		return 0;
	}
	$.ajax({
		url: 'ajax.php',
		type: 'POST',
		data: {
			new_message: "true",
			nick: $("#nick").val(),
			lastmsg: $("#lastmsg").html(),
			newmsg: $("#message").val()
		},
		dataType: 'text'
	}).done(function (text) {
		/* Once the message was sent
		   empty the message input box and return all new messages */
		$("#message").val("");
		request_message();
	})
}

function validate() {
	if($("#nick").val().length < 1 || $("#message").val().length < 1) {
		return 0;
	}
	return 1;
}
