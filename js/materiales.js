$(document).ready(function () {
  $(".numero").numeric();
  $(".decimal").numeric(".");

  //$("#btn-guardar").click(guardarTrabajador);

  listarUnidadMedida();

  function listarUnidadMedida() {
    $.ajax({
      url: $.PATH + "almacen?function=listUnidadMedida",
      dataType: "json",
      cache: false,
      type: "get",
      success: function(response) {
        var html = "<option value='X' disabled selected >Selecciona</option>";
        if (response.data != null) {
          var response = response.data;
          for (var i = 0; i < response.length; i++) {
            html += "<option value='"+response[i].unidad+"'>"+response[i].nombre+"</option>";
          }
        } else {
          html = "<option value='X' disabled selected >No disponible</option>";
        }
        $("#unidad_medida").html(html);
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
      id_almacen: {
        required: true,
        positivenumber: true
      },
      codigo: {
        required: true,
        minlength: 1,
        maxlength: 5,
        remote: {
          url: $.PATH + "almacen",
          type: 'post',
          data: {
            function: 'verificaDatos',
            dato: 'cod_material',
            valor: function () {
              return $("#codigo").val();
            }
          }
        }
      },
      nombre: {
        required: true,
        minlength: 2,
        remote: {
          url: $.PATH + "almacen",
          type: 'post',
          data: {
            function: 'verificaDatos',
            dato: 'nomb_material',
            valor: function () {
              return $("#nombre").val();
            }
          }
        }
      },
      stock_minimo:{
        required: true
      },
      stock_requerido: {
        required: true
      },
      tipo: {
        required: true
      }
    }, 
    messages: {

    },
    submitHandler: guardarMaterial
  });
  ;

  function guardarMaterial() {
    console.log("envio de form");
    var unidad_medida = $("#unidad_medida").val();
    if (unidad_medida == 'X' || unidad_medida == '') {
      alert("Seleccione unidad de medida");
      $("#unidad_medida").focus();
      return;
    }
    $.ajax({
      url: $.PATH + "almacen?function=saveMaterial",
      data: $("#form-guardar").serialize(),
      dataType: "json",
      cache: false,
      type: "post",
      success: function(response) {
        console.log(response);
        if (response.estado == 'success') {
          limpiarForm("form-guardar");
          listarMateriales(); 
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
  var tblMateriales = $('#datatable-materiales').DataTable({
        //ajax: $.PATH + "/alumno?function=getAlumnosAll",
        columns: [
          {"data": null, "defaultContent": ""},
          {"data": "codigo", "defaultContent": ""},
          {"data": "nombre", "defaultContent": ""},
          {"data": "stock", "defaultContent": ""},
          {"data": "unidad_medida", "defaultContent": ""},
          {"data": "tipo", "defaultContent": ""},
          {"data": null, "defaultContent": 
                            //"<a name='btn-edit' class='btn btn-xs btn-primary'><i class='fa fa-pencil'></i></a>"
                            ""+"<a name='btn-stock' class='btn btn-xs btn-success'><i class='fa fa-plus-circle'></i></a>"}
        ],
        rowId: "id",
        fixedHeader: true,
        responsive: true,
        iDisplayLength: 25,
        aLengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: "Bfrtip",
        order: [[2, "asc"]],// ordenado por apellidos
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
          $('td:eq(6)', nRow).attr("data-id",aData.id);
          $('td:eq(6) a', nRow).attr("codigo",aData.codigo);
          $('td:eq(6) a', nRow).attr("nombre",aData.nombre);
          $('td:eq(6) a', nRow).attr("material",aData);
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

  listarMateriales(); 

  function listarMateriales() {
    var url = $.PATH + "almacen?function=listMateriales";
    console.log(url);
    tblMateriales.ajax.url(url).load();
  }

  $('.table').on( 'click', 'a', function () {
      var id = $(this).parent().data("id");
      var btn = $(this).attr("name");
      var nombre = $(this).attr("nombre");
      var codigo = $(this).attr("codigo");
      var material = $(this).attr("material");

      switch(btn) {
        case 'btn-edit': 
          console.log(id);
          showModal("Editar", id, codigo, nombre);
          break;
        case 'btn-stock': 
          console.log(id);
          showModal("Stock", id, codigo, nombre);
          break;
      }
      
    } );

  function showModal(accion, id, codigo, nombre) {
    getDetalleMaterial(id);
    $("#modal-materiales .modal-title").text(accion + " " + codigo +" - "+ nombre);
    $("#id-material").val(id);
    $("#modal-materiales").modal();
    $("#stock-nuevo").focus();
  }

  function getDetalleMaterial(id) {
    $.ajax({
      url: $.PATH + "almacen?function=getDetalleMaterial",
      data: {id: id},
      dataType: "json",
      cache: false,
      type: "get",
      success: function(response) {
        console.log(response);
        if (response != null) {
          $("#stock-actual").text(response.stock);
          $("#stock-minimo").text(response.stock_minimo);
          $(".unidad").text(response.unidad_medida);
          //$("#stock-nuevo").text(stock_minimo);
        } else {
          alert("Error de carga.");
        }
        //$.Notify(response.estado, response.message, response.estado);
      },
      error: function(response) {
        console.log(response);
      }
    });
  }

  $("#btn-guardar-stock").click(function (e) {
    e.preventDefault();
    var stock = $("#stock-nuevo").val().trim();
    var proveedor = $("#proveedor").val().trim();
    var observacion = $("#observacion").val().trim();
    if (stock <= 0) {
      alert("Ingrese un valor válido.");
      $("#stock-nuevo").focus();
      return;
    }
    if (proveedor.length == 0) {
      alert("Ingrese nombre de proveedor.");
      $("#proveedor").focus();
      return;
    }
    $.ajax({
      url: $.PATH + "almacen?function=addStock",
      data: {id: $("#id-material").val(), stock: stock, proveedor: proveedor, observacion: observacion},
      dataType: "json",
      cache: false,
      type: "post",
      success: function(response) {
        console.log(response);
        if (response.estado == "success") {
          $("#modal-materiales").modal("hide");
          listarMateriales();
        }
        $.Notify(response.estado, response.message, response.estado);
      },
      error: function(response) {
        console.log(response);
      }
    });
  });

  //-------------------------------------- select2
  $(".select2").select2({
      placeholder: "Selecciona",
      allowClear: true
    });

}); // fin JQuery
