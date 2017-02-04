$(document).ready(function () {

$(".f_inicio").datetimepicker({
  	locale: 'es',
  	ignoreReadonly: true,
  	showClear: true,
  	showClose: true,
  	format: 'DD/MM/YYYY',
  	sideBySide: true,
  	//defaultDate: moment(),
  	maxDate: moment()
  }).on("dp.change", function (e) {
     $(".f_fin").data("DateTimePicker").minDate(e.date);
	});
  $(".f_fin").datetimepicker({
  	locale: 'es',
  	ignoreReadonly: true,
  	showClear: true,
  	showClose: true,
  	format: 'DD/MM/YYYY',
  	sideBySide: true,
  	//defaultDate: moment(),
  	maxDate: moment()
  }).on("dp.change", function (e) {
     
	});

  $("#btn-consultar").click(function (e) {
  	e.preventDefault();
  	reporte1($("#form-reporte-1").serialize());
  });

  function reporte1(data) {
  	$.ajax({
      url: $.PATH + "reporte?function=getProduccionAsociaciones",
      data: data,
      dataType: "json",
      cache: false,
      type: "post",
      success: function(response) {
        if (response != null) {
        	
        	for (var i = 0; i < response.length; i++) {
        		var obj = response[i].data;
        		for (var j = 0; j < obj.length; j++) {
        			console.log(obj[j][0], Date(obj[j][0]));
        			var d = (obj[j][0]);
        			d = d.split("-");
        			//console.log(d);
        			obj[j][0] = Date.UTC(d[0], d[1]-1, d[2]);
        		}
        	}
        	showReporte1(response);
        } 
      },
      error: function(response) {
        console.log(response);
      }
    });
  }


  function showReporte1(data) {
  	//console.log(data);
  	Highcharts.chart('reporte1', {
        chart: {
            type: 'spline'
        },
        title: {
            text: 'Cajas producidas por asociación'
        },
        subtitle: {
            text: '('+$("#f_inicio").val()+' - '+$("#f_fin").val()+')'
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: { // don't display the dummy year
                month: '%e. %b',
                year: '%b'
            },
            title: {
                text: 'Fecha'
            }
        },
        yAxis: {
            title: {
                text: 'Cajas (Und.)'
            },
            min: 0
        },
        tooltip: {
            headerFormat: '<b>{series.name}</b><br>',
            pointFormat: '{point.x:%e. %b}: {point.y:.2f} Und'
        },

        plotOptions: {
            spline: {
                marker: {
                    enabled: true
                }
            }
        },

        series: data
    });

  }




});




$(function () {
    
});