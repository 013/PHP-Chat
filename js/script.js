get_latest();

$(document).ready( function() {
	setInterval("request_message()", 3000);
});


function request_message() {
	$.ajax({
		url: 'ajax.php',
		type: 'POST',
		data: {
			nick: $("#msgnum").html(),
			lastmsg: $("#lastmsg").html()
		},
		dataType: 'text'
	}).done(function (text) {
		handle_message(text);
	});
}


function get_latest() {
	$.ajax({
		url: 'ajax.php',
		type: 'POST',
		data: {
			getnewest: "true"
		},
		dataType: 'text'
	}).done(function (text) {
		handle_message(text);
	});
	;
}

function handle_message(text) {
	if (text != "0") {
		$("#lastmsg").html(text);

		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			data: {
				htmlit: text,
				msgnum: $("#msgnum").html()
			},
			dataType: 'text'
		}).done(function (text) {
			$("#dataDisplay").append(text);
		})
	}
}

function send_message() {
	$.ajax({
		url: 'ajax.php',
		type: 'POST',
		data: {
			nick: $("#nick").val(),
			lastmsg: $("#lastmsg").html(),
			newmsg: $("#message").val()
		},
		dataType: 'text'
	})/*.done(function (text) {
		alert("Sent");
	})*/
}
	


$(document).keypress(function(event) {
	if (event.keyCode == '13' && $("#message").is(":focus")) {
		/* If the key was 'Return' and the input box #message is in focus */
		$("#sendbutton").click(send_message);
	}
});

$(document).ready( function() {
	$("#sendbutton").click(send_message);
});
