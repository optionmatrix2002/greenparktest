/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function inProcessBtn(buttonId) {
    $("#" + buttonId).attr("disabled", true).find("i").addClass(
        "ld ld-ring ld-cycle");
}

function outProcessBtn(buttonId) {
    $("#" + buttonId).attr("disabled", false).find("i").removeClass(
        "ld ld-ring ld-cycle");
}

$(document).on("keyup", ".numbers", function () {
    if (this.value.match(/[^0-9]/g)) {
        this.value = this.value.replace(/[^0-9\-\(\)\+\ ]/g, '');
    }
});
$(document).on("keyup", ".alphanumeric", function () {
    if (this.value.match(/[^0-9a-zA-Z -]/g)) {
        this.value = this.value.replace(/[^0-9a-zA-Z\ \-]/g, '');
    }
});
$(document).on("keyup", ".alphanumericDepartment", function () {
    if (this.value.match(/[^0-9a-zA-Z -&]/g)) {
        this.value = this.value.replace(/[^0-9a-zA-Z\ \-\&]/g, '');
    }
});
$(document).on("keyup", ".onlyChars", function () {
    if (this.value.match(/[^a-zA-Z ]/g)) {
        this.value = this.value.replace(/[^a-zA-Z\ ]/g, '');
    }
});
$(document).on("keyup", ".charsSpecialChars", function () {
    if (this.value.match(/[^a-zA-Z&,\-0-9 ]/g)) {
        this.value = this.value.replace(/[^a-zA-Z&,\-0-9\ ]/g, '');
    }
});

/**
 * ************************Preferences
 * validation************************************
 */
$(document).on("keyup", '#preferencene_seventh', function (event) {
    if (this.value.match(/[^0-9\-\+\.]/g)) {
        this.value = this.value.replace(/[^0-9\-\+\.]/g, '');
    } else {
        if ((pointPos = this.value.indexOf('.')) >= 0)
            $(this).attr('maxLength', pointPos + 3);
        else {
            $(this).removeAttr('maxLength');
        }
    }

});

/** ****************************************************************** */
$(document).on("keyup",
    "#preferencene_eight,#preferencene_nine,#preferencene_ten,#preferencene_seventh",
    function (event) {

        var number = parseFloat($(this).val());
        if (number >= 100) {
            $(this).val("");

        } else if (this.value.match(/[^0-9\-\+\.]/g)) {
            this.value = this.value.replace(/[^0-9\-\+\.]/g, '');
        }
    });
$(document).on("keyup", '#preference_three', function (event) {
    if ($(this).val().length > 11) {
        $(this).val($(this).val().substr(0, 10));
        event.preventDefault();
        return false;
    }
});

$(document).on("keyup", '#preference_hotelmbl', function (event) {
    if ($(this).val().length > 11) {
        $(this).val($(this).val().substr(0, 11));
        event.preventDefault();
        return false;
    }
});

//Hotel phone numver validation
$(document).on("keyup", '.hotelPhoneNumber', function () {

    var element = $(".hotelPhoneNumber");
    var phone_val = element.val();
    if (element.val().length < 10 && (phone_val != "")) {
        $(".error").html("Enter valid mobile number");

        $('.help-block-error').html("");
        $("#save_hotel_submit_btn").attr('disabled', 'disabled');
        return false;
    }
    else if (element.val().length == 11 || element.val().length == 10) {
        $("#save_hotel_submit_btn").removeAttr('disabled');
        $(".error").html("");
        return true;
    }
});

$(document).on("keyup", '#preference_two', function (event) {
    if ($(this).val().length > 55) {
        $(this).val($(this).val().substr(0, 55));
       // this.value = this.value.replace(/[^0-9\ \-\&]/g, '');
        event.preventDefault();
        return false;
    }
});

$(document).on("beforeSubmit", "#add_pref_form", function (e) {
    $("#pref_submit_btn").attr("disabled", true).find("i").addClass("ld ld-ring ld-cycle");
    $.post({
        url: $(this).attr("action"),
        data: $(this).serializeArray(),
        success: function (data) {
            var response = JSON.parse(data);
            if (response.error) {
            	$("#pref_submit_btn").attr("disabled", false).find("i").removeClass("ld ld-ring ld-cycle");
                toastr.error(response.error);
            } else if (response.success) {
                toastr.success(response.success);
                location.reload();

            }
        },
        complete: function () {
            $("#role_add_submit_btn").attr("disabled", false).find("i").removeClass("ld ld-ring ld-cycle");
        }
    });
    return false;
});

function manageCriticalPreference(id,type){
	//return false;
	var datastr='';
	if(type==1){
		datastr ='ProcessCriticalPreferences[stop_reminders]=0';
		if($('#stop_reminders_'+id+'_'+type).is(':checked')){
			datastr ='ProcessCriticalPreferences[stop_reminders]=1';
		}
	}else if(type==2){
		datastr ='ProcessCriticalPreferences[stop_escalations]=0';
		if($('#stop_escalations_'+id+'_'+type).is(':checked')){
			datastr ='ProcessCriticalPreferences[stop_escalations]=1';
		}
	}
	$.post({
        url: '/preference/update?id='+id,
        data: datastr,
        success: function (data) {
            var response = JSON.parse(data);
            if (response.error) {
            	//$("#pref_submit_btn").attr("disabled", false).find("i").removeClass("ld ld-ring ld-cycle");
                toastr.error(response.error);
            } else if (response.success) {
                toastr.success(response.success);
               // location.reload();

            }
        }
        
    });
}

function deleteCriticalPref(id){
    $("#deletepopup #delete_pref_id").val(id);
    $("#deletepopup").modal("show");
}

$(document).on("beforeSubmit", "#delete_process_critical_form", function (e) {
   // $("#pref_submit_btn").attr("disabled", true).find("i").addClass("ld ld-ring ld-cycle");
    $.post({
        url: $(this).attr("action"),
        data: $(this).serializeArray(),
        success: function (data) {
            var response = JSON.parse(data);
            if (response.error) {
                toastr.error(response.error);
            } else if (response.success) {
                toastr.success(response.success);
                location.reload();

            }
        }
    });
    return false;
});

$(document).on("beforeSubmit", "#email_template_form", function (e) {
	   // $("#pref_submit_btn").attr("disabled", true).find("i").addClass("ld ld-ring ld-cycle");
	    $.post({
	        url: $(this).attr("action"),
	        data: $(this).serializeArray(),
	        success: function (data) {
	            var response = JSON.parse(data);
	            if (response.error) {
	                toastr.error(response.error);
	            } else if (response.success) {
	                toastr.success(response.success);
	                location.reload();

	            }
	        }
	    });
	    return false;
	});
