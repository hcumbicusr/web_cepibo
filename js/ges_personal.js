$(document).ready(function () {
	$(".numero").numeric();

	//$("#btn-guardar").click(guardarTrabajador);

	listarAsociaciones();
	listarCargos();

	function listarAsociaciones() {
		$.ajax({
			url: $.PATH + "asociacion",
			dataType: "json",
			cache: false,
			type: "get",
			success: function(response) {
				var html = "<option value='X'>Selecciona</option>";
				if (response != null) {
					for (var i = 0; i < response.length; i++) {
						html += "<option value='"+response[i].id+"'>"+response[i].nombre+"</option>";
					}
				} else {
					html = "<option value='X'>No disponible</option>";
				}
				$("#id_asociacion").html(html);
			},
			error: function(response) {
				console.log(response);
			}
		});
	}

	function listarCargos() {
		$.ajax({
			url: $.PATH + "asociacion",
			data: {function: 'getCargos'},
			dataType: "json",
			cache: false,
			type: "get",
			success: function(response) {
				var html = "<option value='X'>Selecciona</option>";
				if (response != null) {
					for (var i = 0; i < response.length; i++) {
						html += "<option value='"+response[i].id+"'>"+response[i].nombre+"</option>";
					}
				} else {
					html = "<option value='X'>No disponible</option>";
				}
				$("#id_cargo").html(html);
			},
			error: function(response) {
				console.log(response);
			}
		});
	}

	$("#form-guardar").submit(function(e){
		e.preventDefault();
	}).validate({
		rules: {
			nombres: {
				required: true,
				nombres: true
			},
			apellidos: {
				required: true,
				nombres: true
			},
			dni: {
				required: true,
				digits: true,
				minlength: 8,
				maxlength: 8,
				remote: {
					url: $.PATH + "trabajador",
					type: 'post',
					data: {
						function: 'verificaDatos',
						dato: 'dni',
						valor: function () {
							return $("#dni").val();
						}
					}
				}
			},
			celular: {
		    	required: true,
		    	digits: true,
		    	minlength: 9,
		    	maxlength: 9
		    },
			email: {
		        required: true,
				email: true,
		        remote: {
					url: $.PATH + "trabajador",
					type: 'post',
					data: {
						function: 'verificaDatos',
						dato: 'email',
						valor: function () {
							return $("#email").val();
						}
					}
				}
		    },
		    genero: {
		    	required: true
		    },
		    id_asociacion: {
		    	required: true,
		    	positivenumber: true,
		    },
		    id_cargo: {
		    	required: true,
		    	positivenumber: true,
		    }
		}, 
		messages: {

		},
		submitHandler: guardarTrabajador
	});
	;

	function guardarTrabajador() {
		console.log("envio de form");
		$.ajax({
			url: $.PATH + "trabajador",
			data: $("#form-guardar").serialize(),
			dataType: "json",
			cache: false,
			type: "post",
			success: function(response) {
				console.log(response);
				if (response.estado == 'success') {
					limpiarForm("form-guardar");
				} else {

				}
				$.Notify(response.estado, response.message, response.estado);
			},
			error: function(response) {
				console.log(response);
			}
		});
	}

	function limpiarForm(id_form) {
		$("#"+id_form+" input[type=text]").each(function(e){
			$(this).val("");
		});
		$("#"+id_form+" input[type=email]").each(function(e){
			$(this).val("");
		});
		$("#"+id_form+" input[type=number]").each(function(e){
			$(this).val("");
		});
		//$("#"+id_form+" select").empty().append("whatever");
		$("#"+id_form+" select").prop('selectedIndex', -1);
	}
});