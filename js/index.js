$(document).ready(function(){
	console.log("Ready index");

	$("#username").keyup(function(e){
		var username = $("#username").val().trim();
		var password = $("#password").val().trim();
		if (e.which == 13) {
			if (password.length > 0) {
				login(username, password);
			} else {
				$("#password").focus();
			}
		}
	});

	$("#password").keyup(function(e){
		var username = $("#username").val().trim();
		var password = $("#password").val().trim();
		if (e.which == 13) {
			login(username, password);
		}
	});

	$("#btn-login").click(function(){
		var username = $("#username").val().trim();
		var password = $("#password").val().trim();

		if (username.length > 0 && password.length > 0) {
			login(username, password);
		}else {
			alert("Debe ingresar datos válidos");
		}

	});


	function login(username, password) {
		$.ajax({
			url: "backend/helpers/Sesion.php",
			data: {
				username: username,
				password: password,
				solicitud: "login"
			},
			dataType: "json",
			cache: false,
			type: "post",
			success: function(response) {
				if (response.estado == 'success') {
					window.location.href = 'home.php';
				} else {
					$(".licence").html(response.message);
					$(".licence").append("<br>"+response.licence);
					$(".licence").css({"font-size": "17px"});
					$(".licence").show();
					if (response.licence != ""){
						alert(response.licence);
					} else {
						alert(response.message);
					}
				}
			},
			error: function(response) {
				console.log(response);
			}
		});
	}
});