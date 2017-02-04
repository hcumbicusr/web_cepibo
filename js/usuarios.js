$(document).ready(function(){

	$("#dni").numeric();

	listUsuarios();

	listTrabajadores();

	function listTrabajadores() {
		$.ajax({
			url: $.PATH + "usuario",
			data: {function: 'getTrabajadoresSinUsuario'},
			dataType: "json",
			cache: false,
			type: "get",
			success: function(response) {
				var html = "<option value='X'>Selecciona</option>";
				if (response != null) {
					for (var i = 0; i < response.length; i++) {
						html += "<option value='"+response[i].id+"'>"+response[i].nombres+" "+response[i].apellidos+"</option>";
					}
				} else {
					html = "<option value='X'>No disponible</option>";
				}
				$("#id_trabajador").html(html);
			},
			error: function(response) {
				console.log(response);
			}
		});
	}

	$("#id_trabajador").change(function () {
		$.ajax({
			url: $.PATH + "usuario",
			data: {function: 'sugiereUsuario', id_trabajador: $(this).val()},
			dataType: "json",
			cache: false,
			type: "get",
			success: function(response) {
				$("#username").val(response.username);
			},
			error: function(response) {
				console.log(response);
			}
		});
	});

	listTipoUsuario();

	function listTipoUsuario() {
		$.ajax({
			url: $.PATH + "usuario",
			data: {function: 'getTipoUsuario'},
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
				$("#id_tipousuario").html(html);
			},
			error: function(response) {
				console.log(response);
			}
		});
	}

	function listUsuarios() {
		$.ajax({
			url: $.PATH + "usuario",
			data: {function: 'listUsuarios'},
			dataType: "json",
			cache: false,
			type: "get",
			success: function(response) {
				var html = "";

				if (response != null) {
					for (var i = 0; i < response.length; i++) {
						html += "<tr>";
						html += "<td>"+(i+1)+"</td>";
						html += "<td>"+response[i].username+"</td>";
						html += "<td>"+response[i].email+"</td>";
						html += "<td>"+response[i].tipo_usuario+"</td>";
						html += "<td>"+response[i].cargo+"</td>";
						var check = (response[i].activo == '1')? 'checked' : '';
						html += "<td align='center'><input name='chk_user' type='checkbox' class='chk_activo' "+check+" data-id='"+response[i].id+"'></td>";
						html += "<td align='center'><a class='btn btn-primary view_permisos' data-id='"+response[i].id+"' data-name='"+response[i].username+"'><i class='fa fa-eye'></i></a></td>";
						html += "</tr>";
					}
				} else {
					html = "";
				}
				$("#datatable-usuarios tbody").html(html);
				//$("input[type=checkbox]").on('click',cambiarEstado);
				$("input[name=chk_user]").on('click',cambiarEstado);
				$(".view_permisos").on('click',verPermisos);
			},
			error: function(response) {
				console.log(response);
			}
		});
	}

	function cambiarEstado() {
		var id = $(this).data("id"); // id user
		var checked = $(this).prop("checked");
		var estado = (checked===true)? '1' : '0';
		console.log("click: "+id, checked);

		$.ajax({
			url: $.PATH + "usuario",
			data: {function: 'cambiarEstado', id_usuario: id, estado: estado},
			dataType: "json",
			cache: false,
			type: "post",
			success: function(response) {
				console.log(response);
			},
			error: function(response) {
				console.log(response);
			}
		});

	}

	function  verPermisos() {
		//modal permisos
		var id = $(this).data("id"); // id user
		var name = $(this).data("name"); // id user
		console.log(id, name);
		listPermisosByUser(id);
		$("#modal-permisos").modal();
		$("#modal-permisos .modal-title").text(name);
	}

	//listPermisosByUser(id_usuario);
	function listPermisosByUser(id_usuario) {
		$.ajax({
			url: $.PATH + "usuario",
			data: {function: 'listAdminPermisosByUser', id_usuario: id_usuario},
			dataType: "json",
			cache: false,
			type: "get",
			success: function(response) {
				var html = "";

				if (response != null) {
					for (var i = 0; i < response.length; i++) {
						var color = '';
						var tab = '';
						if (response[i].id_padre == '0') {
							color = '#BCF5A9';
						} else {
							tab = 'padding-left:5em';
						}
						html += "<tr style='background-color: "+color+"'>";
						html += "<td>"+(i+1)+"</td>";
						html += "<td><label style='"+tab+"'>"+response[i].descripcion+"</label></td>";
						var check = (response[i].permiso == '1')? 'checked' : '';
						html += "<td align='center'><input name='chk_permisos' type='checkbox' id_usuario='"+id_usuario+"' class='chk_activo_p' "+check+" data-id='"+response[i].id_permiso_menu+"' id_menu='"+response[i].id+"'></td>";
						html += "</tr>";
					}
				} else {
					html = "";
				}
				$("#datatable-permisos tbody").html(html);
				//$("input[type=checkbox]").on('click',adminPermisos);
				$("input[name=chk_permisos]").on('click',adminPermisos);
			},
			error: function(response) {
				console.log(response);
			}
		});
	}

	function adminPermisos() {
		var id = $(this).data("id");//id_permiso_menu
		var id_menu = $(this).attr("id_menu");
		var id_usuario = $(this).attr("id_usuario");
		var checked = $(this).prop("checked");
		var estado = (checked===true)? '1' : '0';

		console.log("id_permiso_menu, id_menu, id_usuario, estado");
		console.log(id, id_menu, id_usuario, estado);

		$.ajax({
			url: $.PATH + "usuario",
			data: {function: 'adminPermisos', id: id, estado: estado, id_menu: id_menu, id_usuario: id_usuario, usuario: $.USERNAME},
			dataType: "json",
			cache: false,
			type: "post",
			success: function(response) {
				console.log(response);
				listPermisosByUser(id_usuario)
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
	      id_trabajador: {
	        required: true,
	        positivenumber: true
	      },
	      username: {
	        required: true,
	        minlength: 4,
	        maxlength: 25,
	        remote: {
		          url: $.PATH + "usuario",
		          type: 'post',
		          data: {
		            function: 'verificaDatos',
		            dato: 'username',
		            valor: function () {
		              return $("#username").val();
		            }
		          }
		        }
	      },
	      password: {
	        required: true,
	        minlength: 4
	      },
	      id_tipousuario: {
	        required: true,
	        positivenumber: true
	      }
	    }, 
	    messages: {

	    },
	    submitHandler: guardarNuevoUsuario
	  });

	function guardarNuevoUsuario() {
		$.ajax({
	      url: $.PATH + "usuario",
	      data: $("#form-guardar").serialize(),
	      dataType: "json",
	      cache: false,
	      type: "post",
	      success: function(response) {
	        console.log(response);
	        if (response.estado == 'success') {
	          limpiarForm("form-guardar");
	          $("#id_trabajador").select2("val","X");
				listUsuarios();
				listTrabajadores();
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



	$(".select2").select2({
      placeholder: "Selecciona",
      allowClear: true
    });
});