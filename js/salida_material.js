$(document).ready(function () {
  $(".numero").numeric();
  $(".decimal").numeric(".");

  //$("#btn-guardar").click(guardarTrabajador);

  listarMaterialesCbo(); 
  function listarMaterialesCbo() {
    $.ajax({
      url: $.PATH + "almacen?function=listMateriales",
      dataType: "json",
      cache: false,
      type: "get",
      success: function(response) {
        var html = "<option value='X' disabled selected >Selecciona</option>";
        if (response.data != null) {
          var response = response.data;
          for (var i = 0; i < response.length; i++) {
            html += "<option value='"+response[i].id+"'>"+response[i].codigo+" - "+response[i].nombre+"</option>";
          }
        } else {
          html = "<option value='X' disabled selected >No disponible</option>";
        }
        $("#id_material").html(html);
      },
      error: function(response) {
        console.log(response);
      }
    });
  }

  listarTipoCaja(); 
  function listarTipoCaja() {
    $.ajax({
      url: $.PATH + "almacen?function=listTipoCaja",
      dataType: "json",
      cache: false,
      type: "get",
      success: function(response) {
        var html = "<option value='X' disabled selected >Selecciona</option>";
        if (response.data != null) {
          var response = response.data;
          for (var i = 0; i < response.length; i++) {
            html += "<option value='"+response[i].id+"'>"+response[i].nombre+"</option>";
          }
        } else {
          html = "<option value='X' disabled selected >No disponible</option>";
        }
        $("#id_tipo_caja").html(html);
      },
      error: function(response) {
        console.log(response);
      }
    });
  }

  listarAsociaciones(); 
  function listarAsociaciones() {
    $.ajax({
      url: $.PATH + "asociacion",
      dataType: "json",
      cache: false,
      type: "get",
      success: function(response) {
        var html = "<option value='X' disabled selected >Selecciona</option>";
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

  $("#id_asociacion").change(function () {
    $("#id_cuadrilla").select2("val","");
    listarCuadrillas($(this).val()); 
  });

  function listarCuadrillas(id) {
    $.ajax({
      url: $.PATH + "asociacion?function=getCuadrillas",
      data: {id_asociacion: id},
      dataType: "json",
      cache: false,
      type: "get",
      success: function(response) {
        var html = "<option value='X' disabled selected >Selecciona</option>";
        if (response != null) {
          //var response = response.data;
          for (var i = 0; i < response.length; i++) {
            html += "<option value='"+response[i].id+"'>"+response[i].nombre+"</option>";
          }
        } else {
          html = "<option value='X' disabled selected >No disponible</option>";
        }
        $("#id_cuadrilla").html(html);
      },
      error: function(response) {
        console.log(response);
      }
    });
  }

  $("#id_tipo_caja").change(function () {
    listarMaterialesCaja($(this).val());
    $.flgEntrega = true; 
  });

  $("#cantidad").keydown(function (e) {
    var code = (e.keyCode ? e.keyCode : e.which);
      if (code==13) {
        e.preventDefault();
        if ($(this).val()!= "" && $("#id_tipo_caja").val() != 'X') {
          listarMaterialesCaja($("#id_tipo_caja").val());
          $.flgEntrega = true;
        }
        $("#tbl-materiales").focus();
        return false;
      }
  });
/*
  $("#cantidad").blur(function (e) {
      listarMaterialesCaja($("#id_tipo_caja").val()); 
  });
  */

  $.flgEntrega = true;
  $.flgMateriales = true;
  //listar los materiales x tipo de caja y cantidad
  function listarMaterialesCaja(id_tipo_caja) {
    var cantidad = $("#cantidad").val();
    cantidad = (cantidad=='')? 0: cantidad;
    $.ajax({
      url: $.PATH + "almacen?function=listMaterialesCaja",
      data: {id_tipo_caja: id_tipo_caja},
      dataType: "json",
      cache: false,
      type: "get",
      success: function(response) {
        var html = "";
        if (response.data != null) {
          $.flgMateriales = true; // para validar envío
          var response = response.data;
          for (var i = 0; i < response.length; i++) {
            var color = '';
            if (response[i].stock_requerido == '1') {
              if (response[i].stock <= response[i].stock_minimo) {
                color = '#FAAC58';
              }
            }
            html += "<tr style='background-color: "+color+"'>";
            html += "<td>"+response[i].codigo+"</td>";
            html += "<td>"+response[i].nombre+"</td>";
            html += "<td>"+response[i].stock+"</td>";
            html += "<td>"+response[i].unidad_medida+"</td>";            
            var valor = 0;
            if (response[i].calcular == '1') {
              valor = parseFloat(cantidad)*parseFloat(response[i].multiplo);
            } else {
              valor = response[i].multiplo;
            }
            html += "<td><input name='id_materiales[]' type='hidden' value='"+response[i].id+"'>";
            html += "<input name='materiales[]' type='text' class='decimal' style='width: 50px;' value='"+valor+"'></td>";
            html += "<td>"+response[i].tipo+"</td>";
            html += "</tr>";
          }
        }  else {
          $.flgMateriales = false; // para validar envío
        }
        $("#tbl-materiales tbody").html(html);
        $("#tbl-materiales tbody").css({"color":"#000"});
        $(".decimal").numeric(".");
        $(".decimal").blur(function (e) {
          var v = $(this).val().trim();
          v = (v == "")?0:parseFloat(v);
          var cantidad = $("#cantidad").val().trim();
          cantidad = (cantidad == "")?0:parseFloat(cantidad);
          if (v == 0 && cantidad > 0) {
            //$(this).focus();
            $.flgEntrega = false;
            $(this).css({"background-color":"#FF0000"});
          } else {
            $.flgEntrega = true;
            $(this).css({"background-color":"#FFFFFF"});
          }
        });
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
      id_cuadrilla: {
        required: true,
        positivenumber: true
      },
      id_tipo_caja: {
        required: true,
        positivenumber: true
      },
      cantidad: {
        required: true,
        mayorquecero: true
      }
    }, 
    messages: {

    },
    submitHandler: guardarSalida
  });

  function guardarSalida() {
    if (!$.flgMateriales) {
      $.Notify("warning", "El tipo de caja no tiene asignados materiales.", "warning");
      return;
    }
    if (!$.flgEntrega) {
      $.Notify("warning", "Debe ingresar datos válidos. Verifique los campos resaltados.", "warning");
      return;
    }
    console.log("envio de form");
    $.ajax({
      url: $.PATH + "almacen?function=saveSalidaMaterial",
      data: $("#form-guardar").serialize(),
      dataType: "json",
      cache: false,
      type: "post",
      success: function(response) {
        console.log(response);
        if (response.estado == 'success') {
          limpiarForm("form-guardar");
          $("#tbl-materiales tbody").empty();
          listarSalidas(); 
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

//----------------------------------------- table asistencia ------------------------------------------------
  var tblSalidas = $('#datatable-salidas').DataTable({
        //ajax: $.PATH + "/alumno?function=getAlumnosAll",
        columns: [
          {"data": null, "defaultContent": ""},
          {"data": "fecha", "defaultContent": ""},
          {"data": "semana", "defaultContent": ""},
          {"data": "asociacion", "defaultContent": ""},
          {"data": "cuadrilla", "defaultContent": ""},
          {"data": "caja", "defaultContent": ""},
          {"data": "cantidad", "defaultContent": ""},
          {"data": null, "defaultContent": 
                            //"<a name='btn-edit' class='btn btn-xs btn-primary'><i class='fa fa-pencil'></i></a>"
                            ""+"<a name='btn-detalle' class='btn btn-xs btn-success'><i class='fa fa-eye'></i></a>"}
        ],
        rowId: "id",
        fixedHeader: true,
        responsive: true,
        iDisplayLength: 25,
        aLengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: "Bfrtip",
        order: [[1, "desc"]],// ordenado por apellidos
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
              className: "btn-sm",
              exportOptions: {
                    columns: ':visible'
                }
            }
          ],
        fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          // Bold the grade for all 'A' grade browsers
          $('td:eq(0)', nRow).html(iDisplayIndex+1);
          var valida = aData.stock_requerido;
          var minimo = parseFloat(aData.stock_minimo);
          var stock = parseFloat(aData.stock);
          var color = '';
          if (valida == '1') {
            if (stock <= minimo) {
              color = '#FAAC58';
            }
          }
          $('td:eq(0)', nRow).parent().css({"background-color":color});
          $('td:eq(0)', nRow).parent().css({"color":"#000"});
          $('td:eq(7)', nRow).attr("data-id",aData.id);
          $('td:eq(7) a', nRow).attr("data-id",aData.id);
          $('td:eq(7) a', nRow).attr("asociacion",aData.asociacion);
          $('td:eq(7) a', nRow).attr("caja",aData.caja);
          $('td:eq(7) a', nRow).attr("cuadrilla",aData.cuadrilla);

          $('td:eq(6)', nRow).html(parseInt(aData.cantidad));
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

  listarSalidas(); 

  function listarSalidas() {
    var url = $.PATH + "almacen?function=listRegistroSalidas";
    console.log(url);
    tblSalidas.ajax.url(url).load();
  }

  $('.table').on( 'click', 'a', function () {
      var id = $(this).parent().data("id");
      var btn = $(this).attr("name");
      var asociacion = $(this).attr("asociacion");
      var caja = $(this).attr("caja");
      var cuadrilla = $(this).attr("cuadrilla");
      var data = {
        "id": id,
        "asociacion": asociacion,
        "caja": caja,
        "cuadrilla": cuadrilla
      };
      //var codigo = $(this).attr("codigo");
      //var material = $(this).attr("material");

      switch(btn) {
        case 'btn-detalle': 
          console.log(id);
          showModal("Detalle", data);
          break;
      }
      
    } );

  function showModal(accion, data) {
    getDetalleSalida(data);
    $("#modal-detalle .modal-title").text(accion);
    $("#id-salida").val(data.id);
    $("#btn-print-salida").hide();
    $("#modal-detalle").modal();
  }

  function getDetalleSalida(data) {
    $.ajax({
      url: $.PATH + "almacen?function=getDetalleSalida",
      data: {id_salida: data.id},
      dataType: "json",
      cache: false,
      type: "get",
      success: function(response) {
        console.log(response);
        var html = '';
        $("#dt-fecha").text("");
        $("#dt-semana").text("");
        $("#dt-nro_cajas").text("");

        if (response.data != null) {
         var response = response.data;
         var color = '';
          for (var i = 0; i < response.length; i++) {
            html += "<tr style='background-color: "+color+"'>";
            html += "<td>"+response[i].codigo+"</td>";
            html += "<td>"+response[i].nombre+"</td>";
            html += "<td>"+response[i].cantidad+"</td>";
            html += "<td>"+response[i].unidad_medida+"</td>";
            html += "<td>"+response[i].tipo+"</td>";
            html += "</tr>";
          }

          $("#dt-fecha").text(response[0].fecha);
          $("#dt-semana").text(response[0].semana);
          $("#dt-nro_cajas").text(response[0].nro_cajas);

          $("#btn-print-salida").show();

          $("#btn-print-salida").bind("click", function (e) {
            e.preventDefault();
            printSalidaMaterial(data, response);
          });
          
        } else {
          alert("Sin materiales.");
        }

        //----------------datos
          $("#dt-asociacion").text(data.asociacion);
          $("#dt-caja").text(data.caja);
          $("#dt-cuadrilla").text(data.cuadrilla);
          //------------
          $("#tblDetalleMat tbody").html(html);
          $("#tblDetalleMat tbody").css({"color":"#000"});

        //$.Notify(response.estado, response.message, response.estado);
      },
      error: function(response) {
        console.log(response);
      }
    });
  }

  //----------------------------- imprimir salida -----------------------------------------
  function printSalidaMaterial(data, response) {
    console.log("printSalidaMaterial");

    var html = '';
    for (var i = 0; i < response.length; i++) {
      html += "<tr>";
      html += "<td>"+response[i].codigo+"</td>";
      html += "<td>"+response[i].nombre+"</td>";
      html += "<td>"+response[i].cantidad+"</td>";
      html += "<td>"+response[i].unidad_medida+"</td>";
      html += "<td>"+response[i].tipo+"</td>";
      html += "</tr>";
    }

    $("#dt-fecha-print").text(response[0].fecha);
    $("#dt-semana-print").text(response[0].semana);
    $("#dt-nro_cajas-print").text(response[0].nro_cajas);

    $("#dt-asociacion-print").text(data.asociacion);
    $("#dt-caja-print").text(data.caja);
    $("#dt-cuadrilla-print").text(data.cuadrilla);
    //------------
    $("#tblDetalleMatPrint tbody").html(html);

    //----------
    var content = $("#printSalidaMeterial").html();
    var mPrint = window.open('', 'Print-Window');
    //mPrint.document.open();
    //contenido html
    mPrint.document.write('<!DOCTYPE html>');
    mPrint.document.write('<html lang="es">');
    mPrint.document.write('<head>');
    mPrint.document.write('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">');
    mPrint.document.write('<link href="'+$.PATH_PUBLIC+'vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">');
    mPrint.document.write('<link href="'+$.PATH_PUBLIC+'vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">');
    mPrint.document.write('<link href="'+$.PATH_PUBLIC+'css/custom.css" rel="stylesheet">');
    mPrint.document.write('<link href="'+$.PATH_PUBLIC+'vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">');
    mPrint.document.write('<link href="'+$.PATH_PUBLIC+'vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">');
    mPrint.document.write('<link href="'+$.PATH_PUBLIC+'css/custom.css" rel="stylesheet">');
    mPrint.document.write('</head>');
    mPrint.document.write('<body class="nav-md">');
    mPrint.document.write('<div class="container body">');
    mPrint.document.write('<div class="main_container">');
    mPrint.document.write('<div class="right_col" role="main">');
    mPrint.document.write('<div class="">');
    //contenido variable
    mPrint.document.write(content);
    //contenido html
    mPrint.document.write('</div>');
    mPrint.document.write('</div>');
    mPrint.document.write('</div>');
    mPrint.document.write('</div>');
    mPrint.document.write('</body>');

    mPrint.document.close();
    mPrint.focus();
    //necesario para cargar correctamente los datos html
    setTimeout(function(){
      mPrint.print();
      mPrint.close();
    },10);
    //return true;
  }
  //.----------------------- fin imprimir salida ------------------------------------------



  //-------------------------------------- select2
  $(".select2").select2({
      placeholder: "Selecciona",
      allowClear: true
    });

}); // fin JQuery
