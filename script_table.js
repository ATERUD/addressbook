$(document).ready( function () {
    $('#dir').DataTable( {
    	paging: false,
		dom: 'Bfrtip',
		buttons: [
			{ extend: 'print', text: '<em>S</em>tampa' }
		],
    } );
} );

