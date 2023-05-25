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
			if ((data['case_status'].replace(/<[^>]+>/g, '') == 'New') && (data['day'] <=1)){ 
				$('td', row).css('background-color', '#87CEFA'); 
				$('td', row).css('color', '#fff'); 
			}else if ((data['case_status'].replace(/<[^>]+>/g, '') == 'New') && (data['day'] >1)){ 
				$('td', row).css('background-color', '#FF0000'); 
				$('td', row).css('color', '#fff');
			}else if ((data['case_status'].replace(/<[^>]+>/g, '') == 'Rejected')){ 
				$('td', row).css('background-color', '#808080');   
				$('td', row).css('color', '#fff');
			}else if ((data['closed'].replace(/<[^>]+>/g, '') == '1')){ 
				$('td', row).css('background-color', '#A9A9A9');   
				$('td', row).css('color', '#fff');
			}else if ((data['case_status'].replace(/<[^>]+>/g, '') != 'New') && (data['day'] <=1)){ 
				$('td', row).css('background-color', '#FFFF00'); 
			}else if ((data['case_status'].replace(/<[^>]+>/g, '') != 'New') && (data['day'] >1)){ 
				$('td', row).css('background-color', '#FFA500'); 
			}
		},
        "columns": column,
		/*
		"initComplete": function( settings, json ) {
			
			// Apply the search
            this.api().columns().every( function () {
                var that = this;
 
                $( 'input', this.header() ).on( 'keyup change clear', function () {
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
	$('#table-result thead th').each( function () {
        var title = $(this).text();
		if(title!='Action' && title!='No.'){
			$(this).html( '<input type="text" placeholder="Search '+title+'" />' );
		}
    } );
	*/
	
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
	
		
	table =  $('#table-result').DataTable( settings );
	
	// Hide two columns
	table.columns( [10] ).visible( false );
});