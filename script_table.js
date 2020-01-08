$(document).ready( function () {
	var table = $('#dir').DataTable( {
		paging: false,
		dom: 'Bfrtip',
		stateSave: true,
		buttons: [
			{
				extend: 'print',
			 	text: '<em>S</em>tampa',
				exportOptions: {
                    columns: ':visible'
                },
				customize: function ( win ) {
                	$(win.document.body)
                    	.css( 'font-size', '10pt' );
		$(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', '8px' );
                }
			},
			{ 
				extend: 'colvis',
				text: '<em>C</em>olonne',
			}
		],
    } );
} );

