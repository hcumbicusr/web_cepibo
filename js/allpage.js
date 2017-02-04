$(document).ready(function(){
	console.log("Ready allpage");

	$(".btn-logout").click(function(){
		var conf = confirm("Seguro que desea salir");

		if (conf) {
			logout();
		}

	});


	function logout() {
		$.ajax({
			url: "backend/helpers/Sesion.php",
			data: {
				solicitud: "logout"
			},
			dataType: "json",
			cache: false,
			type: "post",
			success: function(response) {
				if (response.estado == 'success') {
					window.location.href = 'index.php';
				} else {
					alert(response.message);
				}
			},
			error: function(response) {
				console.log(response);
			}
		});
	}


	$.displayAlert = function(css, type, message) {
		var html = "<strong>"+type+"</strong> "+message;
		var cls = "info";
		switch(type) {
			case 'success' : cls = 'alert-success';
				break;
			case 'error' : cls = 'alert-danger';
				break;
			default : cls = 'alert-warning'

		}
		$("."+css).addClass(cls);
		$("."+css).html(html);
		$("."+css).fadeIn(1000).delay(3000).fadeOut(1000);
	}

	/**
	* @var time_ tiempo de duración en milisegundos
	* @var title título de la notify
	* @var message cuerpo de la notify
	* @var type_ tipo (success, info, warning, error)
	*/
	$.Notify = function (title, message, type_, time_) {
		var type = (type_ == null || type_ == 'undefined')? '' : type_;
		var time = (time_ == null || time_ == 'undefined' || time_ == 0)? 3000 : time_; //3 seg
		PNotify.prototype.options.delay = time;
		if (title.trim() != '' && message.trim() != ''){
			new PNotify({
              title: (title).toUpperCase(),
              text: message,
              type: type,
              styling: 'bootstrap3'
          	});
		}
	}


	$.encodeB64 = function(string) {
		return btoa(string);
	}

	$.decodeB64 = function(string) {
		return atob(string);
	}

	$.validateEmail = function (email) {
	  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	  return re.test(email);
	}

});
