$(document).ready(function () {
  $(".numero").numeric();
  $(".decimal").numeric(".");

  //$("#btn-guardar").click(guardarTrabajador);

  listarProductores();

  listarAsociaciones();

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

  function listarProductores() {
    $.ajax({
      url: $.PATH + "productor",
      dataType: "json",
      cache: false,
      type: "get",
      success: function(response) {
        var html = "<option value='X' disabled selected >Selecciona</option>";
        if (response.data != null) {
          var response = response.data;
          for (var i = 0; i < response.length; i++) {
            html += "<option value='"+response[i].id+"'>"+(response[i].dni+" - "+response[i].apellidos+", "+response[i].nombres)+"</option>";
          }
        } else {
          html = "<option value='X' disabled selected >No disponible</option>";
        }
        $("#id_productor").html(html);
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
      id_productor: {
        required: true,
        positivenumber: true
      },
      id_asociacion: {
        required: true,
        positivenumber: true
      },
      area_total: {
        required: true,
        mayorquecero: true
      },
      area_cultivo: {
        required: true,
        mayorquecero: true
      },
      condicion: {
        required: true
      }
    }, 
    messages: {

    },
    submitHandler: guardarTerreno
  });
  
  function guardarTerreno() {
    console.log("envio de form");
    var id_productor = $("#id_productor").val();
    if (id_productor == 'X' || id_productor == '') {
      alert("Seleccione el productor");
      $("#id_productor").focus();
      return;
    }
    $.ajax({
      url: $.PATH + "productor?function=saveTerreno",
      data: $("#form-guardar").serialize(),
      dataType: "json",
      cache: false,
      type: "post",
      success: function(response) {
        console.log(response);
        if (response.estado == 'success') {
          limpiarForm("form-guardar");
          $("#id_productor").select2("val","X");
          listarTerrenos(); 
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
  var tblTerrenos = $('#datatable-terrenos').DataTable({
        //ajax: $.PATH + "/alumno?function=getAlumnosAll",
        columns: [
          {"data": null, "defaultContent": ""},
          {"data": "codigo", "defaultContent": ""},
          {"data": "productor", "defaultContent": ""},
          //{"data": "dni", "defaultContent": ""},
          {"data": "condicion", "defaultContent": ""},
          {"data": "area_total", "defaultContent": ""},
          {"data": "area_cultivo", "defaultContent": ""},
          {"data": "area_desarrollo", "defaultContent": ""},
          {"data": "referencia", "defaultContent": ""},
          {"data": "certificacion", "defaultContent": ""},
          {"data": "asociacion", "defaultContent": ""}
        ],
        rowId: "id",
        fixedHeader: true,
        responsive: true,
        iDisplayLength: 25,
        aLengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: "Bfrtip",
        order: [[9, "asc"],[2, "asc"]],// ordenado por apellidos
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

  listarTerrenos(); 

  function listarTerrenos() {
    var url = $.PATH + "productor?function=listTerrenos";
    console.log(url);
    tblTerrenos.ajax.url(url).load();
  }



  //-------------------------------------- select2
  $(".select2").select2({
      placeholder: "Selecciona",
      allowClear: true
    });

}); // fin JQuery
