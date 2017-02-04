$(document).ready(function () {
	

	listarAsociaciones(); 
  function listarAsociaciones() {
    $.ajax({
      url: $.PATH + "asociacion",
      dataType: "json",
      cache: false,
      type: "get",
      success: function(response) {
        var html = "<option value='X' selected >Selecciona</option>";
        if (response != null) {
          //var response = response.data;
          for (var i = 0; i < response.length; i++) {
            html += "<option value='"+response[i].id+"'>"+response[i].nombre+"</option>";
          }
        } else {
          html = "<option value='X' disabled selected >No disponible</option>";
        }
        $("#id_asociacion").html(html);
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
      id_asociacion: {
        required: true,
        positivenumber: true
      },
      nombre: {
        required: true,
        minlength: 3,
        remote: {
          url: $.PATH + "asociacion",
          type: 'post',
          data: {
            function: 'verificaDatos',
            dato: 'empacadora',
            valor: function () {
              return $("#nombre").val();
            }
          }
        }
      }
    }, 
    messages: {

    },
    submitHandler: guardarEmpacadora
  });

  function guardarEmpacadora() {
    console.log("envio de form");
    $.ajax({
      url: $.PATH + "asociacion?function=saveEmpacadora",
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

    $("#"+id_form+" select").prop('selectedIndex', -1);

  }


	//-------------------------------------- select2
  $(".select2").select2({
      placeholder: "Selecciona",
      allowClear: true
    });
});