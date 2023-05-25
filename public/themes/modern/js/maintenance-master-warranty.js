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
		orderCellsTop: true,
        fixedHeader: false,
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
		"rowCallback": function( row, data ) {
			if(data['wty_status']=='Out'){
				$('td', row).css('background-color', '#FF0000'); 
				$('td', row).css('color', '#fff'); 
			}else{
				if (data['case_no'] !=null){ 
					$('td', row).css('background-color', '#A9A9A9'); 
					$('td', row).css('color', '#fff'); 
					
				}
			}
		},
        "columns": column,
		/*
		"initComplete": function( settings, json ) {
			// Apply the search
            this.api().columns().every( function () {
                var that = this;
 
                $( 'input', this.footer() ).on( 'keyup change clear', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
            } );
		 }
		*/
    }
	
	$add_setting = $('#dataTables-setting');
	if ($add_setting.length > 0) {
		add_setting = $.parseJSON($('#dataTables-setting').html());
		for (k in add_setting) {
			settings[k] = add_setting[k];
		}
	}
	/*
	$('#table-result tfoot th').each( function () {
        var title = $(this).text();
		if(title!='Action' && title!='No.'){
			$(this).html( '<input type="text" placeholder="Search '+title+'" />' );
		}
    } );
	*/
	/*
	// Setup - add a text input to each footer cell
    $('#table-result thead tr').clone(true).appendTo( '#table-result thead' );	
	
	$('#table-result thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
		if(title!='Action' && title!='No.'){
			$(this).html( '<input type="text" placeholder="Search '+title+'" />' );
	 
			$( 'input', this ).on( 'keyup change', function () {
				if ( table.column(i).search() !== this.value ) {
					table
						.column(i)
						.search( this.value )
						.draw();
				}
			} );
		}else{
			$(this).html( '' );
		}
    } );	
	*/
	table =  $('#table-result').DataTable( settings );
	
	table.columns( [12] ).visible( false );
});