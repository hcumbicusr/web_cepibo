$(document).ready(function(){

	listPermisosByUser();
	function listPermisosByUser() {
		$.ajax({
			url: $.PATH + "usuario",
			data: {function: 'listPermisosByUser', id_usuario: $.ID_USER},
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
						//html += "<td><input></td>";
						html += "</tr>";
					}
				} else {
					html = "";
				}
				$("#datatable-permisos tbody").html(html);
			},
			error: function(response) {
				console.log(response);
			}
		});
	}
});