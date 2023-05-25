/**
* Written by: Agus Prawoto Hadi
* Year		: 2020
* Website	: jagowebdev.com
*/

jQuery(document).ready(function () {
	
	column = $.parseJSON($('#dataTables-column').html());
	url = $('#dataTables-url').html();
	
	 var settings = {
        "processing": true,
        "serverSide": true,
		"scrollX": true,
		"ajax": {
            "url": url,
            "type": "POST"
        },
		"bAutoWidth": false,
		responsive: true,
        "columns": column,
		"initComplete": function( settings, json ) {
			table.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
				$row = $(this.node());
				/* this
					.child(
						$(
							'<tr>'+
								'<td>'+rowIdx+'.1</td>'+
								'<td>'+rowIdx+'.2</td>'+
								'<td>'+rowIdx+'.3</td>'+
								'<td>'+rowIdx+'.4</td>'+
							'</tr>'
						)
					)
					.show(); */
			} );
		 }
    }
	
	$add_setting = $('#dataTables-setting');
	if ($add_setting.length > 0) {
		add_setting = $.parseJSON($('#dataTables-setting').html());
		for (k in add_setting) {
			settings[k] = add_setting[k];
		}
	}
	
	table =  $('#table-result').DataTable( settings );
	setInterval(function () {
		table.ajax.reload( null, false ); // user paging is not reset on reload
	}, 36000);
	
});