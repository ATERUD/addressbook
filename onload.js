$(document).ready(function() {
    var table = $('#dir').DataTable({
        paging: false,
        dom: 'Bfrtip',
        stateSave: true,
		"autoWidth": false,
        buttons: [
            {
                extend: 'colvis',
                text: '<em>C</em>olonne',
            },
            {
                extend: 'print',
                text: '<em>S</em>tampa',
                exportOptions: {
                    columns: ':visible'
                },
                customize: function (win) {
                    $(win.document.body)
                        .css('font-size', '10pt');
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', '8px');
                }
            },
			{
				extend: 'excel',
				text: 'Excel'
			}
        ],
    });

	var ADEntries = gADEntries;
    for (i = 0; i < window.gADEntryCount; i++) {
        try {
            var cn = ADEntries[i]['cn'][0];
            var givenName = ADEntries[i]['givenname'][0];
            var sn = ADEntries[i]['sn'][0];
            var manager = '';
            try {
                manager = ADEntries[i]['manager'][0];
            } catch (error) {
            }
            var jobTitle = '';
            try {
                jobTitle = ADEntries[i]['title'][0];
            } catch (error) {
            }
            var initials = '';
			try {
				ADEntries[i]['initials'][0];
			} catch (error) {
			}
            var phoneNumber = ater_format_telephone_number(ADEntries[i]['telephonenumber'][0]);
            var internalNumber = ater_get_internal_number(phoneNumber);
            var department = '';
            try {
                department = ADEntries[i]['department'][0];
            } catch (error) {
            }
            var mobile = '';
            try {
                mobile = ater_format_telephone_number(ADEntries[i]['mobile'][0]);
            } catch (error) {
            }
            var mail = ADEntries[i]['mail'][0];
            if (mail) {
                mail = add_mailto(mail);
            }
            var physicalDeliveryOfficeName = '';
            try {
                physicalDeliveryOfficeName = ADEntries[i]['physicaldeliveryofficename'][0];
            } catch (Error) {
                physicalDeliveryOfficeName = 'Udine';
            }
            table.row.add([givenName, sn, internalNumber, phoneNumber, mobile, mail, initials, department, physicalDeliveryOfficeName]).draw(false);
        } catch (error) {

        }
    }
});
