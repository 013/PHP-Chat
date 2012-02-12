$(document).ready( function() {
	window.allowed_to_request = 1;
	request_message();
	setInterval("request_message()", 6000);

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
	// Check cookies for a nick name
	if ($("#nick").val().length < 1) {
		$("#nick").val($.cookie('nick'));
	}
	
	window.allowed_to_request = 0;
	$.ajax({
		url: 'ajax.php',
		type: 'POST',
		data: {
			get_message: "true",
			old_message: $("#lastmsg").html()
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
	if (text != "0") {
		// Make a new array to split the lastmsg info from the messages
		var msgnum = new Array();
		msgnum = text.split('<');
		$("#lastmsg").html(msgnum[0]);
		
		msgnum = msgnum[0].toString();
		text = text.substr(msgnum.length, text.length);
		
		$("#dataDisplay").append(text);
		
		// Scroll to the bottom automatically (WIP)
		$("#dataDisplay").prop({ scrollTop: $("#dataDisplay").prop("scrollHeight") });
	}
}

function send_message() {
	if (!validate()) {
		//If not valid
		return 0;
	}
	var message = $("#message").val();
	$("#message").val("");
	$("#message").focus();
	// Check if the nick name has been changed, if so update the cookie 
	if (!$.cookie('nick') || $.cookie('nick') != $("#nick").val()) {
		$.cookie('nick', $("#nick").val(), {expires: 7});
	}
	$.ajax({
		url: 'ajax.php',
		type: 'POST',
		data: {
			new_message: "true",
			nick: $("#nick").val(),
			lastmsg: $("#lastmsg").html(),
			newmsg: message
		},
		dataType: 'text'
	}).done(function (text) {
		/* Once the message was sent
		   return all new messages */
		request_message();
	})
}

function validate() {
	if($("#nick").val().length < 1 || $("#message").val().length < 1) {
		return 0;
	}
	// Clear message box and set focus
	
	return 1;
}
