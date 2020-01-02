$(document).ready(function(){
	$("#show_mail")
	.change(function() {
		if ($(this).is(":checked"))
			$(".mail").show(); 	
		else
			$(".mail").hide(); 	
	}).change();
	$("#show_mobile")
        .change(function() {
                if ($(this).is(":checked"))
                        $(".mobile").show();
                else
                        $(".mobile").hide();
        }).change();
	$("#show_department")
        .change(function() {
                if ($(this).is(":checked"))
                        $(".department").show();
                else
                        $(".department").hide();
        }).change();


});
