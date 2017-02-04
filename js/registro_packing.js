$(document).ready(function () {
  $(".numero").numeric();
  $(".decimal").numeric(".");

  console.log("ready registro_paking", 0);

  $.PACKING_SELECT = '';
  $.addPacking = 0;
  $.tipo_caja_select = '';
  
  $(".f_llegada_contenedor").datetimepicker({
  	locale: 'es',
  	ignoreReadonly: true,
  	showClear: true,
  	showClose: true,
  	format: 'DD/MM/YYYY hh:mm A',
  	sideBySide: true,
  	//defaultDate: moment(),
  	minDate: moment().subtract(5, 'year'),
  	maxDate: moment()
  }).on("dp.change", function (e) {
     $(".f_salida_contenedor").data("DateTimePicker").minDate(e.date);
     $(".f_inicio_llenado").data("DateTimePicker").minDate(e.date);
     var nro_semana = moment(e.date).week();
     $("#nro_semana").val(nro_semana);
     $("#f_inicio_llenado").val("");
     $("#f_salida_contenedor").val("");
     //console.log("semana", e.date, moment(e.date).week())
	});

  $(".f_salida_contenedor").datetimepicker({
  	locale: 'es',
  	ignoreReadonly: true,
  	showClear: true,
  	showClose: true,
  	format: 'DD/MM/YYYY hh:mm A',
  	sideBySide: true,
  	maxDate: moment()
  }).on("dp.change", function (e) {
     $(".f_fin_llenado").data("DateTimePicker").maxDate(e.date);
     $("#f_fin_llenado").val("");
	});

  $(".f_inicio_llenado").datetimepicker({
  	locale: 'es',
  	ignoreReadonly: true,
  	showClear: true,
  	showClose: true,
  	format: 'DD/MM/YYYY hh:mm A',
  	sideBySide: true,
  	maxDate: moment()
  }).on("dp.change", function (e) {
     $(".f_fin_llenado").data("DateTimePicker").minDate(e.date);
     $("#f_fin_llenado").val("");
	});

  $(".f_fin_llenado").datetimepicker({
  	locale: 'es',
  	ignoreReadonly: true,
  	showClear: true,
  	showClose: true,
  	format: 'DD/MM/YYYY hh:mm A',
  	sideBySide: true
  });

  $("#f_llegada_contenedor").val("");
  $("#f_salida_contenedor").val("");
  $("#f_inicio_llenado").val("");
  $("#f_fin_llenado").val("");

  listarSelect("packing", "getVapor", "#id_vapor", true);
  listarSelect("packing", "getClientes", "#id_cliente", true);
  listarSelect("packing", "getContenedores", "#id_contenedor", true);
  listarSelect("packing", "getTipoFunda", "#id_tipo_funda", true);
  listarSelect("packing", "getPuertos", "#id_puerto_origen", true);
  listarSelect("packing", "getPuertos", "#id_puerto_destino", true);
  //listarSelect("packing", "", "#id_packing", true);

  function listarSelect(ctrl, fn, element, add, selected) {
    $.ajax({
      url: $.PATH + ctrl +"?function="+fn,
      //data: {function: fn},
      dataType: "json",
      cache: false,
      type: "get",
      success: function(response) {
        var html = "<option value='X'>Selecciona</option>";
        if (response.data != null) {
        	var response = response.data;
        	var val = "";
          for (var i = 0; i < response.length; i++) {
          	if (selected == response[i].nombre || response.length == 1) {
            	html += "<option value='"+response[i].id+"' selected>"+response[i].nombre+"</option>";
            	val = response[i].id;
            } else {
            	html += "<option value='"+response[i].id+"'>"+response[i].nombre+"</option>";
            }
          }
        } else {
          html = "<option value='X' disabled>No disponible</option>";
        }
        if (add == true) {
        	html += "<option value='O'>Otro</option>";
        }
        $(element).html(html);
        $(element).val(val).trigger("change"); // cambiar opcion select2
      },
      error: function(response) {
        console.log(response);
      }
    });
  }

  $("#id_vapor").change(function (e) {
  	var val = $(this).val();
  	if (val == 'O') {
  		showModalAddItem("packing", "saveItemVapor", "#id_vapor", "Vapor", "getVapor");
  	}
  });

  $("#id_cliente").change(function (e) {
  	var val = $(this).val();
  	if (val == 'O') {
  		showModalAddItem("packing", "saveItemCliente", "#id_cliente", "Cliente", "getClientes");
  	}
  });

  $("#id_contenedor").change(function (e) {
  	var val = $(this).val();
  	if (val == 'O') {
  		showModalAddItem("packing", "saveItemContenedor", "#id_contenedor", "Contenedor", "getContenedores");
  	}
  });

  $("#id_tipo_funda").change(function (e) {
  	var val = $(this).val();
  	if (val == 'O') {
  		showModalAddItem("packing", "saveItemTipoFunda", "#id_tipo_funda", "Tipo de Funda", "getTipoFunda");
  	}
  });

  $("#id_puerto_origen").change(function (e) {
  	var val = $(this).val();
  	if (val == 'O') {
  		showModalAddItem("packing", "saveItemPuerto", "#id_puerto_origen", "Puerto Origen", "getPuertos");
  	} else {
  		disableOption("#id_puerto_destino", $(this).val());
  	}
  });

  $("#id_puerto_destino").change(function (e) {
  	var val = $(this).val();
  	if (val == 'O') {
  		showModalAddItem("packing", "saveItemPuerto", "#id_puerto_destino", "Puerto Destino", "getPuertos");
  	} else {
  		disableOption("#id_puerto_origen", $(this).val());
  	}
  });

  function disableOption(select, value) {
  	$(select + " option").each(function (e) {
  		//console.log("val", value, $(this).val());
  		if ($(this).val() == value) {
  			$(this).attr("disabled", true);
  			return;
  		}
  	});
  }

  function showModalAddItem(ctrl, fn, element, title, fnSuccess) {
  	$("#id_element").val(element);
  	$("#ctrl").val(ctrl);
  	$("#fnSuccess").val(fnSuccess);

  	$("#nombre_item").val("");
  	$("#nombre_item").focus();
	$("#modal-add-item h4").text(title);
	$("#form-guardar-item").attr("action", $.PATH+ctrl+"?function="+fn);
	$("#form-guardar-item").attr("method", "post");
	$("#modal-add-item").modal();
  }

  // validate de form item
  $("#form-guardar-item").submit(function(e){
    e.preventDefault();
  }).validate({
    rules: {
      nombre_item: {
        required: true,
        minlength: 2,
        remote: {
          url: $.PATH + "packing",
          type: 'post',
          data: {
            function: 'verificaDatos',
            dato: function () {
              return $("#id_element").val();
            },
            valor: function () {
              return $("#nombre_item").val();
            }
          }
        }
      }
    }, 
    messages: {

    },
    submitHandler: guardarItem
  });

  function guardarItem() {
    console.log("envio de form-item");
    $.ajax({
      url: $("#form-guardar-item").attr("action"),
      data: $("#form-guardar-item").serialize(),
      dataType: "json",
      cache: false,
      type: $("#form-guardar-item").attr("method"),
      success: function(response) {
        if (response.estado == 'success') {
          var id_element = $("#id_element").val();
          var id_parent = "#" + $(id_element).parent().attr("id");
          var item = $("#nombre_item").val().trim();
          var ctrl = $("#ctrl").val();
  		  var fnSuccess = $("#fnSuccess").val();
  		  console.log(id_element, id_parent);

          listarSelect(ctrl, fnSuccess, id_element, true, item.toUpperCase()); // vuelve a listar el combo con el selected

          $(id_parent + " .select2-selection__rendered").text(item.toUpperCase());
		  $(id_parent + " .select2-selection__rendered").attr("title",item.toUpperCase());

		  limpiarForm("form-guardar-item");
          $("#modal-add-item").modal("hide");
        }
        $.Notify(response.estado, response.message, response.estado);
      },
      error: function(response) {
        console.log(response);
      }
    });
  }

  //validate de formulario principal
  $("#form-guardar").submit(function(e){
    e.preventDefault();
  }).validate({
    rules: {
      id_vapor: {
        required: true,
        positivenumber: true,
      },
      id_cliente: {
        required: true,
        positivenumber: true,
      },
      id_contenedor: {
        required: true,
        positivenumber: true,
      },
      id_tipo_funda: {
        required: true,
        positivenumber: true,
      },
      nro_termoregistro: {
        required: true,
        minlength: 2,
      },
      nro_guia: {
        required: true,
        minlength: 2,
      },
      nro_semana: {
        required: true,
        minlength: 1,
        mayorquecero: true,
      },
      f_llegada_contenedor: {
        required: true,
        minlength: 17,
      },
    }, 
    messages: {

    },
    submitHandler: guardarPacking
  });

  function guardarPacking() {
    console.log("envio de form-packing");
    $.ajax({
      url: $.PATH+"packing",
      data: $("#form-guardar").serialize(),
      dataType: "json",
      cache: false,
      type: "post",
      success: function(response) {
        if (response.estado == 'success') {
          //listarSelect(ctrl, fnSuccess, id_element, true, item.toUpperCase()); // vuelve a listar el combo con el selected
          listPackingPendientes();
          listAllPacking();
		  limpiarForm("form-guardar");
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
    $("#"+id_form+".select2").each(function (e) {
    	$(this).select2("val", "");
    });
    $("#" + id_form + " .select2-selection__rendered").text("");
	$("#" + id_form + " .select2-selection__rendered").attr("title","");

  }



  //-------------------  wizard para llenado de packing list ------------------
  $('#wizard').smartWizard({
  	onLeaveStep: handlerChangeStep,
  	onFinish: onFinishCallBack
  });

  function handlerChangeStep(obj, context) {
  	console.log("Saliendo de paso " + context.fromStep + " para ir a paso " + context.toStep);
  	return validateSteps(context.toStep);
  }

  function validateSteps(stepnumber){ // paso siguiente
        var isStepValid = true;
        // validate step 1
        switch (stepnumber) {
        	case 1: var c = confirm("¿Seguro que desea volver? Se perderá toda la información registrada sin guardar.");
        			if (!c) {
        				isStepValid = false;
        			} else {
        				$("#btn-plus").show();//btn plus
        				$("#addItem").empty();
        			}
        	case 2: if ($.PACKING_SELECT == '') {
			        	isStepValid = false;
			        	$.Notify("warning", "Primero debe seleccionar un Packing", "warning");
			        } else {
			        	listItemsSaved($.PACKING_SELECT);
			        	$("#addItem").empty();
			        }
        	break;
        }
        return isStepValid;
    }

  function onFinishCallBack(objs, context) {
  	console.log("onFinish");
  	if (validateAllSteps()) {
  		saveRegistroPacking();
  	} else {
  		$.Notify("warning", "No se ha seleccionado un Packing", "warning");
  	}
  }

  function validateAllSteps(){
        var isStepValid = true;
        // si se desea poner alguna validación antes de finalizar
        if ($.PACKING_SELECT == '') {
        	isStepValid = false;
        }
        return isStepValid;
    }



  $('.buttonNext').addClass('btn btn-primary');
  $('.buttonPrevious').addClass('btn btn-default');
  $('.buttonFinish').addClass('btn btn-success');

  $('.buttonNext').text("Siguiente");
  $('.buttonPrevious').text("Anterior");
  $('.buttonFinish').text("Finalizar");
  $('.buttonFinish').hide();

listPackingPendientes();
function listPackingPendientes() {
	$.ajax({
		url: $.PATH + "packing",
		dataType: "json",
		cache: false,
		type: "get",
		success: function(response) {
			var html = "";
			if (response.data != null) {
				var response = response.data;
				for (var i = 0; i < response.length; i++) {
					html += "<tr class='color-select'>";
					html += "<td><input type='radio' name='rb_packing' value='"+response[i].id+"' data-id='"+response[i].id+"'></td>";
					html += "<td>"+response[i].codigo+"</td>";
					html += "<td>"+response[i].cliente+"</td>";
					html += "<td>"+response[i].contenedor+"</td>";
					html += "<td>"+response[i].tipo_funda+"</td>";
					html += "<td>"+response[i].vapor+"</td>";
					html += "<td>"+response[i].puerto_origen+"</td>";
					html += "<td>"+response[i].puerto_destino+"</td>";
					html += "<td>"+response[i].nro_guia+"</td>";
					html += "<td>"+response[i].nro_termoregistro+"</td>";
					html += "<td>"+response[i].nro_semana+"</td>";
					html += "</tr>";
				}
			} else {
				html = "";
			}
			$("#datatable-packing tbody").html(html);
			$("#datatable-packing tr").bind("click", function(e){
				var td = $(this).children();
				var rb = td.children();
				rb.prop("checked", true);
				if (rb.val() != null) {
					$("#datatable-packing tr").each(function (v) {
						$(this).removeClass("color-selected");
					});
					$(this).addClass("color-selected");
					$.PACKING_SELECT = rb.val();
				}
				//console.log(rb.val());
			});

			$("#datatable-packing tr").bind("dblclick", function(e){
				var td = $(this).children();
				var rb = td.children();
				if (rb.val() != null) {
					$.Notify("Finalizar el Packing", "Usted está a punto de finalizar el packing seleccionado.", "success", 5000);
					var id_packing = rb.val();
					showModalFinalizar(id_packing);
				}
			});
		},
		error: function(response) {
			console.log(response);
		}
	});
}

// add items for paking list
$("#btn-plus").click(function (e) {
    e.preventDefault();
    $.addPacking++;
    var id_packing = $.PACKING_SELECT;
    console.log("id_packing",id_packing);
    var html = '';
    var h = $("#addItem").html();
    var margin_top = "";
    if (h.trim() == "") {
    	margin_top = "margin-top: 5px;"
    }
    html += '<form id="form_additem_'+$.addPacking+'" method="post" enctype="multipart/form-data" class="form-horizontal form-label-left col-md-12 col-sm-12 col-xs-12" style="border-radius: 4px; border: solid; border-width: 1px; padding-top: 3px; margin-bottom: 2px; '+margin_top+'">';
    html += '<input type="hidden" id="id_packing" name="id_packing" value="'+id_packing+'">';

    html += '<div class="form-group col-md-6 col-sm-6 col-xs-12">';
    html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Asociación</label>';
    html += '<div class="input-group col-md-9 col-sm-9 col-xs-12">';
    html += '<select id="id_asociacion_'+$.addPacking+'" name="id_asociacion" required class="form-control select2">';
    html += '</select>';
    html += '</div>';
    html += '</div>';

    html += '<div class="form-group col-md-6 col-sm-6 col-xs-12">';
    html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Productor</label>';
    html += '<div class="input-group col-md-9 col-sm-9 col-xs-12">';
    html += '<select id="id_productor_'+$.addPacking+'" name="id_productor" required class="form-control select2">';
    html += '</select>';
    html += '</div>';
    html += '</div>';

    html += '<div class="form-group col-md-6 col-sm-6 col-xs-12">';
    html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Terreno</label>';
    html += '<div class="input-group col-md-9 col-sm-9 col-xs-12">';
    html += '<select id="id_productor_terreno_'+$.addPacking+'" name="id_productor_terreno" required class="form-control select2">';
    html += '</select>';
    html += '</div>';
    html += '</div>';

    html += '<div class="form-group col-md-6 col-sm-6 col-xs-12">';
    html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Empacadora</label>';
    html += '<div class="input-group col-md-9 col-sm-9 col-xs-12">';
    html += '<select id="id_asociacion_empacadora_'+$.addPacking+'" name="id_asociacion_empacadora" required class="form-control select2">';
    html += '</select>';
    html += '</div>';
    html += '</div>';

    html += '<div class="form-group col-md-6 col-sm-6 col-xs-12">';
    html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Tipo caja</label>';
    html += '<div class="input-group col-md-9 col-sm-9 col-xs-12">';
    html += '<select id="id_tipo_caja_'+$.addPacking+'" name="id_tipo_caja" required class="form-control select2">';
    html += '</select>';
    html += '</div>';
    html += '</div>';

    html += '<div class="form-group col-md-6 col-sm-6 col-xs-12">';
    html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Fecha de Corte</label>';    
	html += '<div class="input-group date col-md-9 col-sm-9 col-xs-12 f_corte">';
	html += '<input id="f_corte_'+$.addPacking+'" name="f_corte" required class="form-control" type="text">';
	html += '<span class="input-group-addon btn-info">';
	html += '<span class="glyphicon glyphicon-calendar"></span>';
	html += '</span>';
	html += '</div>';
    html += '</div>';

    html += '<div class="form-group col-md-12 col-sm-12 col-xs-12">';
    //html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Fecha de Corte</label>';    
	html += '<div class="input-group col-md-12 col-sm-12 col-xs-12">';
	html += '<div class="table-responsive">';
    html += '<table id="datatable_pallets_'+$.addPacking+'" class="table table-striped table-bordered">';
    html += '<thead>';
    html += '<tr><th colspan="20" align="center" style="text-align: center;"><label>Pallets </label> - Total Cajas:<b id="n_cajas_'+$.addPacking+'">0</b> </th></tr>';
    html += '<tr>';
    for (var i = 0; i < 20; i++) {
    	html += '<th>'+(i+1)+'</th>';
    }
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';
    html += '<tr>';
    for (var i = 0; i < 20; i++) {
    	html += '<td><input type="text" class="numero nro_cajas_'+$.addPacking+'" cajas="0" style="width: 22px;margin: 0 auto; padding: 0px; font-size: 10px;" name="pallets[]" value="0" ></td>';
    }

    html += '</tr>';
    html += '</tbody>';
    html += '</table>';
    html += '</div>';
    html += '</div>';
    html += '</div>';

    html += '<div class="form-group col-md-12 col-sm-12 col-xs-12" style="text-align: center;">';
    html += '<button type="submit" class="btn btn-success" style="border-radius: 25px;" title="Guardar" ><i class="glyphicon glyphicon-ok"></i></button>';
    html += '<a data-id="'+$.addPacking+'" class="btn btn-danger btn-remove" style="border-radius: 25px;" title="Eliminar" ><i class="glyphicon glyphicon-remove"></i></button>';
    html += '</div>';

    html += '</form>';
    $("#addItem").append(html);

    // llena combos
    listarSelect("asociacion", "getAsociaciones", "#id_asociacion_"+$.addPacking, false);
    listarSelect("almacen", "listTipoCaja", "#id_tipo_caja_"+$.addPacking, false, $.tipo_caja_select);

    envioFormItem('form_additem_'+$.addPacking, id_packing, $.addPacking);

    $(".btn-remove").bind("click", function (e) {
    	e.preventDefault();
    	var id = $(this).data("id");
    	var c = confirm("Seguro que dese eliminar este item?");
    	if (c) {
    		$("#form_additem_"+id).remove();
    		$("#btn-plus").show();//btn plus
    	}
    });

    $(".nro_cajas_"+$.addPacking).blur(function (e) {
    	var n = $(this).val();
    	n = (n==null || n==undefined || n=='undefined' || n=="")? 0:n;
    	$(this).attr("cajas",n);
    	$(this).val(n);
    	//console.log(n);
    	var total = 0;
    	$(".nro_cajas_"+$.addPacking).each(function (d) {
    		total += parseInt($(this).attr("cajas"));
    		//console.log($(this).attr("cajas"));
    	});
    	$("#n_cajas_"+$.addPacking).text(total);
    });

    $("#addItem .select2").select2({
      placeholder: "Selecciona",
      allowClear: true
    });

    $(".f_corte").datetimepicker({
	  	locale: 'es',
	  	ignoreReadonly: true,
	  	showClear: true,
	  	showClose: true,
	  	format: 'DD/MM/YYYY',
	  	maxDate: moment(),
	  	widgetPositioning: {'vertical': 'bottom'}
	  	//sideBySide: true
	  });
    $(".numero").numeric();

    $("#id_tipo_caja_"+$.addPacking).change(function (e) {
    	var v = $(this).val();
    	if (v != 'X') {
    		$.tipo_caja_select = $("#id_tipo_caja_"+$.addPacking+" option[value='"+v+"']").text();
    	} else {
    		$.tipo_caja_select = "";
    	}
    });
    $("#id_asociacion_"+$.addPacking).change(function (e) {
    	var v = $(this).val();
    	if (v != 'X') {
    		listarSelect("productor", "getProductorByAsociacion&id_asociacion="+v, "#id_productor_"+$.addPacking, false);
    		listarSelect("asociacion", "getEmpacadoraByAsociacion&id_asociacion="+v, "#id_asociacion_empacadora_"+$.addPacking, false);
    	}
    });

    $("#id_productor_"+$.addPacking).change(function (e) {
    	var id_productor = $(this).val();
    	var id_asociacion = $("#id_asociacion_"+$.addPacking).val();
    	if (id_productor != 'X') {
    		listarSelect("productor", "getTerrenosByProductorAsociacion&id_productor="+id_productor+"&id_asociacion="+id_asociacion, "#id_productor_terreno_"+$.addPacking, false);
    	}
    });

    // desactiva el btn plus
    $("#btn-plus").hide();


  });


function envioFormItem(id_form, id_packing, id_item) {
    $("#"+id_form).submit(function(e){
      e.preventDefault();
    }).validate({
      rules: {
        id_asociacion: {
          required: true,
          positivenumber: true
        },
        id_productor: {
          required: true,
          positivenumber: true
        },
        id_productor_terreno: {
          required: true,
          positivenumber: true
        },
        id_asociacion_empacadora: {
          required: true,
          positivenumber: true
        },
        id_tipo_caja: {
          required: true,
          positivenumber: true
        },
        f_corte: {
          required: true,
          validDate: true
        }
      }, 
      messages: {

      },
      submitHandler: function () {
        console.log("envio de form: "+id_form);
        var total_cajas = $("#n_cajas_"+id_item).text();
        if (parseInt(total_cajas) == 0) {
        	$.Notify("warning", "Debe ingresar el número de cajas", "warning");
        	return;
        }

        var c = confirm("¿Guardar?");
        if (!c) return;
        
        $.ajax({
          url: $.PATH + "packing?function=savePackingList",
          data: $("#"+id_form).serialize(),
          dataType: "json",
          cache: false,
          type: "post",
          beforeSend: function () {
          	$("#modal-loading").modal();
          },
          success: function(response) {
            $("#modal-loading").modal("hide");
            if (response.estado == 'success') {
              $("#"+id_form).remove(); // quitar input
              listItemsSaved($.PACKING_SELECT);
              $("#btn-plus").show();//btn plus
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

//enviar formulario de completado de packing - wizard - SIN USAR
function saveRegistroPacking() { // SIN USAR
	
	$("#form-guardar-packinglist").submit(function (e) {
		e.preventDefault();
	}).validate({
    rules: {
      
    }, 
    messages: {

    },
    submitHandler: saveRegistroPackingList
  });
}// fin fn

function saveRegistroPackingList() {
	console.log("saveRegistroPackingList");
}


// modal para finalizar el packing seleccionado 
function showModalFinalizar(id_packing, mode) { // mode => finalizar, exportar
	$("#modal-finalizar-packing").modal();
	$("#id_packing_f").val(id_packing);
	getDatosPacking(id_packing, mode);
	if (mode == 'impresion') {
		$("#btn-clean-packinglist").hide();
		$("#btn-guardar-packinglist").hide();
		$("#btn-exportar-packinglist").show();
		$("#modal-finalizar-packing h4").text("Packinglist");
	} else {
		$("#btn-clean-packinglist").show();
		$("#btn-guardar-packinglist").show();
		$("#btn-exportar-packinglist").hide();
		$("#modal-finalizar-packing h4").text("Finalizar Packing");
	}
	listPackingListByID(id_packing);
}

$(".f_llegada_contenedor_f").datetimepicker({
  	locale: 'es',
  	ignoreReadonly: true,
  	showClear: true,
  	showClose: true,
  	format: 'DD/MM/YYYY hh:mm A',
  	sideBySide: true,
  	//defaultDate: moment(),
  	minDate: moment().subtract(5, 'year'),
  	maxDate: moment()
  }).on("dp.change", function (e) {
     $(".f_salida_contenedor_f").data("DateTimePicker").minDate(e.date);
     $(".f_inicio_llenado_f").data("DateTimePicker").minDate(e.date);
     var nro_semana = moment(e.date).week();
     $("#nro_semana_f").text(nro_semana);
     $("#f_inicio_llenado_f").val("");
     $("#f_salida_contenedor_f").val("");
     //console.log("semana", e.date, moment(e.date).week())
	});

  $(".f_salida_contenedor_f").datetimepicker({
  	locale: 'es',
  	ignoreReadonly: true,
  	showClear: true,
  	showClose: true,
  	format: 'DD/MM/YYYY hh:mm A',
  	sideBySide: true,
  	maxDate: moment()
  }).on("dp.change", function (e) {
     $(".f_fin_llenado_f").data("DateTimePicker").maxDate(e.date);
     $("#f_fin_llenado_f").val("");
	});

  $(".f_inicio_llenado_f").datetimepicker({
  	locale: 'es',
  	ignoreReadonly: true,
  	showClear: true,
  	showClose: true,
  	format: 'DD/MM/YYYY hh:mm A',
  	sideBySide: true,
  	maxDate: moment()
  }).on("dp.change", function (e) {
     $(".f_fin_llenado_f").data("DateTimePicker").minDate(e.date);
     $("#f_fin_llenado_f").val("");
	});

  $(".f_fin_llenado_f").datetimepicker({
  	locale: 'es',
  	ignoreReadonly: true,
  	showClear: true,
  	showClose: true,
  	format: 'DD/MM/YYYY hh:mm A',
  	sideBySide: true
  });

  $("#f_llegada_contenedor_f").val("");
  $("#f_salida_contenedor_f").val("");
  $("#f_inicio_llenado_f").val("");
  $("#f_fin_llenado_f").val("");

  function getDatosPacking(id_packing, mode) { // mode= finalizar, impresion
  	$.ajax({
      url: $.PATH + "packing?function=getDatosPackingById",
      data: {id_packing: id_packing},
      dataType: "json",
      cache: false,
      type: "get",
      beforeSend: function () {
      	$("#modal-loading").modal();
      },
      success: function(response) {
        $("#modal-loading").modal("hide");
        setValuesDefaultPacking("");
        if (response.data != null) {
          var p = response.data;
          setValuesDefaultPacking(p, mode);
        } else {
        	$.Notify("error", "Ocurrió un error.", "error");
        }
        
      },
      error: function(response) {
        console.log(response);
      }
    });
  }

  function setValuesDefaultPacking(value, mode) {// mode= finalizar, impresion
  	if (value instanceof Object) {
  		$("#id_vapor_f").text(value.vapor);
  		$("#id_cliente_f").text(value.cliente);
  		$("#id_contenedor_f").text(value.contenedor);
  		$("#id_tipo_funda_f").text(value.tipo_funda);
  		$("#id_puerto_origen_f").text(value.puerto_origen);
  		$("#id_puerto_destino_f").text(value.puerto_destino);
  		$("#nro_termoregistro_f").text(value.nro_termoregistro);
  		$("#nro_guia_f").text(value.nro_guia);
  		$("#nro_semana_f").text(value.nro_semana);
  		
  		$("#f_llegada_contenedor_f").val((value.f_llegada_contenedor_format == '00/00/0000 12:00 AM')?'':value.f_llegada_contenedor_format);
  		$(".f_salida_contenedor_f").data("DateTimePicker").minDate(value.f_llegada_contenedor_format);
  		$(".f_inicio_llenado_f").data("DateTimePicker").minDate(value.f_llegada_contenedor_format);
  		$("#f_salida_contenedor_f").val((value.f_salida_contenedor_format == '00/00/0000 12:00 AM')?'':value.f_salida_contenedor_format);
  		$("#f_inicio_llenado_f").val((value.f_inicio_llenado_format == '00/00/0000 12:00 AM')?'':value.f_inicio_llenado_format);
  		$("#f_fin_llenado_f").val((value.f_fin_llenado_format == '00/00/0000 12:00 AM')?'':value.f_fin_llenado_format);
  	} else {
  		$("#id_vapor_f").text(value);
  		$("#id_cliente_f").text(value);
  		$("#id_contenedor_f").text(value);
  		$("#id_tipo_funda_f").text(value);
  		$("#id_puerto_origen_f").text(value);
  		$("#id_puerto_destino_f").text(value);
  		$("#nro_termoregistro_f").text(value);
  		$("#nro_guia_f").text(value);
  		$("#nro_semana_f").text(value);
  		$("#f_llegada_contenedor_f").val(value);
  		$("#f_salida_contenedor_f").val(value);
  		$("#f_inicio_llenado_f").val(value);
  		$("#f_fin_llenado_f").val(value);
  	}
  	if (mode == 'impresion') {
  		$("#f_llegada_contenedor_f").attr("disabled", true);
  		$("#f_salida_contenedor_f").attr("disabled", true);
  		$("#f_inicio_llenado_f").attr("disabled", true);
  		$("#f_fin_llenado_f").attr("disabled", true);
  	} else {
  		$("#f_llegada_contenedor_f").attr("disabled", false);
  		$("#f_salida_contenedor_f").attr("disabled", false);
  		$("#f_inicio_llenado_f").attr("disabled", false);
  		$("#f_fin_llenado_f").attr("disabled", false);
  	}
  	
  }

  $("#form-finalizar-packing").submit(function (e) {
  	e.preventDefault();
  }).validate({
  	rules: {
        f_llegada_contenedor: {
          required: true,
          minlength: 17,
        },
        f_salida_contenedor: {
          required: true,
          minlength: 17,
        },
        f_inicio_llenado: {
          required: true,
          minlength: 17,
        },
        f_fin_llenado: {
          required: true,
          minlength: 17,
        },
      }, 
      messages: {

      },
      submitHandler: function () {
        console.log("envio de form: ");
        var c = confirm("Finalizar packing?");
        if (!c) return;
        
        $.ajax({
          url: $.PATH + "packing?function=finalizarPacking",
          data: $("#form-finalizar-packing").serialize(),
          dataType: "json",
          cache: false,
          type: "post",
          beforeSend: function () {
          	$("#modal-loading").modal();
          },
          success: function(response) {
            $("#modal-loading").modal("hide");
            if (response.estado == 'success') {
              listPackingPendientes();
              $("#modal-finalizar-packing").modal("hide");
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


console.log("ready registro_paking", 1);
//-----------------------------------------------------------------------------------------------------------

//----------------------------------------- table asistencia ------------------------------------------------
  var tblDetallePacking = $('#datatable-detalle-packing').DataTable({
        //ajax: $.PATH + "/alumno?function=getAlumnosAll",
        columns: [
          {"data": "pl.asociacion", "defaultContent": ""},
          {"data": "pl.tipo_caja", "defaultContent": ""},
          {"data": "pl.f_corte_format", "defaultContent": ""},
          {"data": "pl.empacadora", "defaultContent": ""},
          {"data": "pl.codigo_terreno", "defaultContent": ""},
          {"data": "pl.nombre_productor", "defaultContent": ""},
          {"data": "pl.nro_cajas", "defaultContent": ""}
          //{"data": "", "defaultContent": ""}
          //{"data": null, "defaultContent": ""}
        ],
        rowId: "id",
        fixedHeader: true,
        responsive: true,
        iDisplayLength: 25,
        aLengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: "Bfrtip",
        order: [[ 2, "asc" ],[1, "asc"]],// ordenado por apellidos
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
          
        },
        bProcessing: false,
        language: {
          emptyTable:     "Sin registros"
        }

    });
  // ------------------------------------------------- fin table asistencia -------------------------------------------
// listar los items ya guardados
  function listItemsSaved(id_packing) {
    var url = $.PATH + "packing?function=getDetallePacking&id_packing="+id_packing;
    console.log(url);
    tblDetallePacking.ajax.url(url).load();
  }


//----------------------------------------- table asistencia ------------------------------------------------
  var tblFinalizarPacking = $('#datatable-finalizar-packinglist').DataTable({
        //ajax: $.PATH + "/alumno?function=getAlumnosAll",
        columns: [
          {"data": "pl.asociacion", "defaultContent": ""},
          {"data": "pl.tipo_caja", "defaultContent": ""},
          {"data": "pl.f_corte_format", "defaultContent": ""},
          {"data": "pl.empacadora", "defaultContent": ""},
          {"data": "pl.codigo_terreno", "defaultContent": ""},
          {"data": "pl.nombre_productor", "defaultContent": ""},
          {"data": "pl.nro_cajas", "defaultContent": ""},
          {"data": "pallets.0.cantidad", "defaultContent": "0"},
          {"data": "pallets.1.cantidad", "defaultContent": "0"},
          {"data": "pallets.2.cantidad", "defaultContent": "0"},
          {"data": "pallets.3.cantidad", "defaultContent": "0"},
          {"data": "pallets.4.cantidad", "defaultContent": "0"},
          {"data": "pallets.5.cantidad", "defaultContent": "0"},
          {"data": "pallets.6.cantidad", "defaultContent": "0"},
          {"data": "pallets.7.cantidad", "defaultContent": "0"},
          {"data": "pallets.8.cantidad", "defaultContent": "0"},
          {"data": "pallets.9.cantidad", "defaultContent": "0"},
          {"data": "pallets.10.cantidad", "defaultContent": "0"},
          {"data": "pallets.11.cantidad", "defaultContent": "0"},
          {"data": "pallets.12.cantidad", "defaultContent": "0"},
          {"data": "pallets.13.cantidad", "defaultContent": "0"},
          {"data": "pallets.14.cantidad", "defaultContent": "0"},
          {"data": "pallets.15.cantidad", "defaultContent": "0"},
          {"data": "pallets.16.cantidad", "defaultContent": "0"},
          {"data": "pallets.17.cantidad", "defaultContent": "0"},
          {"data": "pallets.18.cantidad", "defaultContent": "0"},
          {"data": "pallets.19.cantidad", "defaultContent": "0"}
          //{"data": null, "defaultContent": ""}
        ],
        columnDefs: [
			{ "orderable": false, "targets": 6 },
			{ "orderable": false, "targets": 7 },
			{ "orderable": false, "targets": 8 },
			{ "orderable": false, "targets": 9 },
			{ "orderable": false, "targets": 10 },
			{ "orderable": false, "targets": 11 },
			{ "orderable": false, "targets": 12 },
			{ "orderable": false, "targets": 13 },
			{ "orderable": false, "targets": 14 },
			{ "orderable": false, "targets": 15 },
			{ "orderable": false, "targets": 16 },
			{ "orderable": false, "targets": 17 },
			{ "orderable": false, "targets": 18 },
			{ "orderable": false, "targets": 19 },
			{ "orderable": false, "targets": 20 },
			{ "orderable": false, "targets": 21 },
			{ "orderable": false, "targets": 22 },
			{ "orderable": false, "targets": 23 },
			{ "orderable": false, "targets": 24 },
			{ "orderable": false, "targets": 25 },
			{ "orderable": false, "targets": 26 }
		],
        rowId: "id",
        fixedHeader: true,
        responsive: true,
        iDisplayLength: 25,
        aLengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: "Bfrtip",
        order: [[ 2, "asc" ],[1, "asc"]],// ordenado por apellidos
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
          
        },
        bProcessing: false,
        language: {
          emptyTable:     "Sin registros"
        }

    });
  // ------------------------------------------------- fin table asistencia -------------------------------------------
  function listPackingListByID(id_packing) {
    var url = $.PATH + "packing?function=listPackingListByID&id_packing="+id_packing;
    console.log(url);
    tblFinalizarPacking.ajax.url(url).load();
  }


// listar el historial de packing
listAllPacking();
function listAllPacking() {
	$.ajax({
		url: $.PATH + "packing?function=getAllPackingList",
		dataType: "json",
		cache: false,
		type: "get",
		success: function(response) {
			var html = "";
			if (response.data != null) {
				var response = response.data;
				for (var i = 0; i < response.length; i++) {
					html += "<tr class='color-select' data-id='"+response[i].id+"'>";
					//html += "<td><input type='radio' name='rb_packing' value='"+response[i].id+"' data-id='"+response[i].id+"'></td>";
					html += "<td>"+response[i].codigo+"</td>";
					html += "<td>"+response[i].cliente+"</td>";
					html += "<td>"+response[i].contenedor+"</td>";
					html += "<td>"+response[i].tipo_funda+"</td>";
					html += "<td>"+response[i].vapor+"</td>";
					html += "<td>"+response[i].puerto_origen+"</td>";
					html += "<td>"+response[i].puerto_destino+"</td>";
					html += "<td>"+response[i].nro_guia+"</td>";
					html += "<td>"+response[i].nro_termoregistro+"</td>";
					html += "<td>"+response[i].nro_semana+"</td>";
					html += "<td>";
					html += "<a class='btn btn-primary btn-xs btn-ver' data-id='"+response[i].id+"'><i class='fa fa-eye'></i></a>";
					html += "</td>";
					html += "</tr>";
				}
			} else {
				html = "";
			}
			$("#datatable-packinglist tbody").html(html);
			$("#datatable-packinglist tr").bind("click", function(e){
				var id_packing = $(this).data("id");
				if (id_packing != null) {
					$("#datatable-packinglist tr").each(function (v) {
						$(this).removeClass("color-selected");
					});
					$(this).addClass("color-selected");
				}
				//console.log(rb.val());
			});

			$("#datatable-packinglist tr").bind("dblclick", function(e){
				var id_packing = $(this).data("id");
				if (id_packing != null) {
					//$.Notify("Finalizar el Packing", "Usted está a punto de finalizar el packing seleccionado.", "success", 5000);
					showModalFinalizar(id_packing, 'impresion'); // en modo impresion
					$("#id_export").val(id_packing);
				}
			});

			$(".btn-ver").bind("click", function(e){
				var id_packing = $(this).data("id");
				if (id_packing != null) {
					//$.Notify("Finalizar el Packing", "Usted está a punto de finalizar el packing seleccionado.", "success", 5000);
					showModalFinalizar(id_packing, 'impresion'); // en modo impresion
					$("#id_export").val(id_packing);
				}
			});
		},
		error: function(response) {
			console.log(response);
		}
	});
}

$("#btn-exportar-packinglist").click(function (e) {
	e.preventDefault();
	$("#form-exportar").submit();

});


  //-------------------------------------- select2
  $(".select2").select2({
      placeholder: "Selecciona",
      allowClear: true
    });

}); // fin JQuery