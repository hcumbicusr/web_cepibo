$(document).ready(function () {
  $(".numero").numeric();
  $(".decimal").numeric(".");

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
  var tblCajas = $('#datatable-cajas').DataTable({
        //ajax: $.PATH + "/alumno?function=getAlumnosAll",
        columns: [
          {"data": null, "defaultContent": ""},
          {"data": "nombre", "defaultContent": ""},
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
        order: [[1, "asc"]],// ordenado por apellidos
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
          $('td:eq(0)', nRow).parent().css({"color":"#000"});
          $('td:eq(2)', nRow).attr("data-id",aData.id);
          $('td:eq(2) a', nRow).attr("data-id",aData.id);
          $('td:eq(2) a', nRow).attr("nombre",aData.nombre);
          
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

  listarCajas();

  function listarCajas() {
    var url = $.PATH + "almacen?function=listTipoCaja";
    console.log(url);
    tblCajas.ajax.url(url).load();
  }

  $('.table').on( 'click', 'a', function () {
      var id = $(this).parent().data("id");
      var btn = $(this).attr("name");
      var nombre = $(this).attr("nombre");
      
      switch(btn) {
        case 'btn-detalle': 
          console.log(id);
          showModal("Materiales/Insumos de caja: ", id, nombre);
          break;
      }
      
    } );

  $.addMaterial = 0;

  function showModal(accion, id, nombre) {
    getMaterialesCaja(id);
    $("#modal-detalle .modal-title").text(accion + nombre);
    $("#id_tipo_caja").val(id);
    $("#btn-plus").attr("data-id",id);
    $("#addItem").html("");
    $.addMaterial = 0;
    $("#modal-detalle").modal();
  }

  function getMaterialesCaja(id_tipo_caja) {
    $.ajax({
      url: $.PATH + "almacen?function=getMaterialesCaja",
      data: {id_tipo_caja: id_tipo_caja},
      dataType: "json",
      cache: false,
      type: "get",
      success: function(response) {
        console.log(response);
        var html = '';

        if (response.data != null) {
         var response = response.data;
         var color = '';
          for (var i = 0; i < response.length; i++) {
            html += "<tr style='background-color: "+color+"'>";
            html += "<td>"+response[i].codigo+"</td>";
            html += "<td>"+response[i].nombre+"</td>";
            html += "<td>"+response[i].unidad_medida+"</td>";
            html += "<td>"+response[i].tipo+"</td>";
            html += "<td><a data-id='"+response[i].id+"' class='btn btn-xs btn-danger btn-quitar'><i class='fa fa-minus'></i></a></td>";
            html += "</tr>";
          }
          
        } else {
          $.Notify("warning", "Aún no ha seleccionado materiales/insumos", "warning");
        }
          //------------
          $("#tblMaterialesCaja tbody").html(html);
          $("#tblMaterialesCaja tbody").css({"color":"#000"});

          $(".btn-quitar").bind('click', function (e) {
            e.preventDefault();
            var id = $(this).data("id");
            quitarMaterial(id, id_tipo_caja);
          });

        //$.Notify(response.estado, response.message, response.estado);
      },
      error: function(response) {
        console.log(response);
      }
    });
  }

  function quitarMaterial(id_material_tipo_caja, id_tipo_caja) {
    console.log("quita", id_material_tipo_caja);
    var c = confirm("¿Seguro que desea quitar el material?");
    if (!c) return;

    $.ajax({
      url: $.PATH + "almacen?function=quitarMaterial",
      data: {id_material_tipo_caja: id_material_tipo_caja, usuario_reg: $.USERNAME},
      dataType: "json",
      cache: false,
      type: "post",
      success: function(response) {
        if (response.estado == 'success') {
          getMaterialesCaja(id_tipo_caja);
        }
        $.Notify(response.estado, response.message, response.estado);
      },
      error: function(response) {
        console.log(response);
      }
    });
  }

  function listMateriales(id_select, id_tipo_caja) {
    $.ajax({
      url: $.PATH + "almacen",
      data: {function: 'listMaterialesSinCaja', id_tipo_caja: id_tipo_caja},
      dataType: "json",
      cache: false,
      type: "get",
      success: function(response) {
        var html = "<option value='X'>Selecciona</option>";
        if (response.data != null) {
          var response = response.data
          for (var i = 0; i < response.length; i++) {
            html += "<option value='"+response[i].id+"'>"+response[i].codigo +" - "+response[i].nombre+" ("+response[i].unidad_medida+")"+"</option>";
          }
        } else {
          html = "<option value='X'>No disponible</option>";
        }
        $("#"+id_select).html(html);
      },
      error: function(response) {
        console.log(response);
      }
    });
  }

  

  $("#btn-plus").click(function (e) {
    e.preventDefault();
    $.addMaterial++;
    var id_tipo_caja = $(this).attr("data-id");
    console.log("id_tipo_caja",id_tipo_caja);
    var html = '';
    html += '<form id="form_additem_'+$.addMaterial+'" method="post" enctype="multipart/form-data" class="form-horizontal form-label-left col-md-12 col-sm-12 col-xs-12" style="border-radius: 4px; border: solid; border-width: 1px; padding-top: 2px; margin-bottom: 2px;">';
    html += '<input type="hidden" id="id_tipo_caja" name="id_tipo_caja" value="'+id_tipo_caja+'">';
    html += '<div class="form-group">';
    html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Material/Insumo</label>';
    html += '<div class="col-md-9 col-sm-9 col-xs-12">';
    html += '<select id="id_material_'+$.addMaterial+'" name="id_material" required class="form-control select2">';
    html += '</select>';
    html += '</div>';
    html += '</div>';
    html += '<div class="form-group">';
    html += '<label class="control-label col-md-2 col-sm-2 col-xs-12">Cantidad</label>';
    html += '<div class="col-md-3 col-sm-3 col-xs-12">';
    html += '<input id="multiplo_'+$.addMaterial+'" name="multiplo" required class="form-control decimal">';
    html += '</div>';
    html += '<label class="control-label col-md-2 col-sm-2 col-xs-12">Calcular</label>';
    html += '<div class="col-md-3 col-sm-3 col-xs-12">';
    html += '<select id="calcular_'+$.addMaterial+'" name="calcular" required class="form-control">';
    html += '<option value="1" selected>SI</option>';
    html += '<option value="0">NO</option>';
    html += '</select>';
    html += '</div>';
    html += '<button type="submit" class="btn btn-primary" style="border-radius: 25px;" ><i class="glyphicon glyphicon-ok"></i></button>';
    html += '</div>';
    html += '</form>';
    $("#addItem").append(html);
    envioForm('form_additem_'+$.addMaterial, id_tipo_caja);

    listMateriales('id_material_'+$.addMaterial, id_tipo_caja);

    $(".select2").select2({
      placeholder: "Selecciona",
      allowClear: true
    });
    $(".decimal").numeric(".");

  });

  function envioForm(id_form, id_tipo_caja) {
    $("#"+id_form).submit(function(e){
      e.preventDefault();
    }).validate({
      rules: {
        id_material: {
          required: true,
          positivenumber: true
        },
        multiplo: {
          required: true,
          min: 0
        },
        calcular: {
          required: true,
          min: 0,
          max: 1
        }
      }, 
      messages: {

      },
      submitHandler: function () {
        console.log("envio de form: "+id_form);
        $.ajax({
          url: $.PATH + "almacen?function=saveMaterialCaja",
          data: $("#"+id_form).serialize(),
          dataType: "json",
          cache: false,
          type: "post",
          success: function(response) {
            console.log(response);
            if (response.estado == 'success') {
              $("#"+id_form).remove(); // quitar input
              getMaterialesCaja(id_tipo_caja);
            } else {

            }
            $.Notify(response.estado, response.message, response.estado);
          },
          error: function(response) {
            console.log(response);
          }
        });
      }
    });
  }

  

  


  //-------------------------------------- select2
  

}); // fin JQuery
