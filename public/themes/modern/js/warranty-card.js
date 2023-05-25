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
		"order" : [],
		"scrollX": true,
		"ajax": {
            "url": url,
            "type": "POST"
        },		
		"bAutoWidth": false,
		responsive: true,
		liveAjax: {
			interval: 3000,
			// Do _not_ fire the DT callbacks for every XHR request made by liveAjax
			dtCallbacks: false,
			// Abort the XHR polling if one of the below errors were encountered
			abortOn: ['error', 'timeout', 'parsererror'],
			// Disable pagination resetting on updates ("true" will send the viewer
			// to the first page every update)
			resetPaging: false
		},
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
});