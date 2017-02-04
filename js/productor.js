$(document).ready(function () {
  $(".numero").numeric();

  //$("#btn-guardar").click(guardarTrabajador);

  listarAsociaciones();
  //listarCargos();

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
/*
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
  */

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
          url: $.PATH + "productor",
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
        genero: {
          required: true
        },
        id_asociacion: {
          required: true,
          positivenumber: true,
        }
    }, 
    messages: {

    },
    submitHandler: guardarProductor
  });
  ;

  function guardarProductor() {
    console.log("envio de form");
    $.ajax({
      url: $.PATH + "productor",
      data: $("#form-guardar").serialize(),
      dataType: "json",
      cache: false,
      type: "post",
      success: function(response) {
        console.log(response);
        if (response.estado == 'success') {
          limpiarForm("form-guardar");
          listarProductores(); 
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

//----------------------------------------- table asistencia ------------------------------------------------
  var tblProductor = $('#datatable-productor').DataTable({
        //ajax: $.PATH + "/alumno?function=getAlumnosAll",
        columns: [
          {"data": null, "defaultContent": ""},
          {"data": "codigo", "defaultContent": ""},
          {"data": "nombres", "defaultContent": ""},
          {"data": "apellidos", "defaultContent": ""},
          {"data": "dni", "defaultContent": ""},
          {"data": "genero", "defaultContent": ""},
          {"data": "asociacion", "defaultContent": ""}
        ],
        rowId: "id",
        fixedHeader: true,
        responsive: true,
        iDisplayLength: 25,
        aLengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: "Bfrtip",
        order: [[ 1, "asc" ],[6, "asc"],[3, "asc"]],// ordenado por apellidos
        buttons: [
            {
              extend: "copy",
              className: "btn-sm"
            },
            {
              extend: "csv",
              className: "btn-sm"
            },
            {
              extend: "excel",
              className: "btn-sm"
            },
            {
              extend: "pdfHtml5",
              className: "btn-sm"
            },
            {
              extend: "print",
              className: "btn-sm"
            },
          ],
        fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          // Bold the grade for all 'A' grade browsers
          $('td:eq(0)', nRow).html(iDisplayIndex+1);
          /*
          var estado = $('td:eq(4)', nRow).html(); 
          if (estado.trim() == 'OK') {
            $('td:eq(4)', nRow).parent().css({"background-color":"#BCEE82"});
          } else if (estado.trim() == 'TOLERANCIA') {
            $('td:eq(4)', nRow).parent().css({"background-color":"#D1F0AE"});
          } else {
            $('td:eq(4)', nRow).parent().css({"background-color":"#FF7B4F"});
          }
          
          $('td:eq(4)', nRow).parent().css({"font-weigth":"bold"});
          $('td:eq(4)', nRow).parent().css({"color":"#000"});
          */
        },
        bProcessing: false,
        language: {
          emptyTable:     "Sin registros"
        }

    });
  // ------------------------------------------------- fin table asistencia -------------------------------------------

  listarProductores(); 

  function listarProductores() {
    var url = $.PATH + "productor";
    console.log(url);
    tblProductor.ajax.url(url).load();
  }

}); // fin JQuery
