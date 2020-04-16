$('.eventreminder').select2({
    placeholder: 'Select hours',
    allowClear: true
});


$(document).on('click', '.update_preference_btn', function () {

    var emailValue = $('#preference_two').val();
    var mobileNumber = $('#preference_three').val();
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;

    $preferenceNewValueOne = $("#preference_one").val();
    $preferenceNewValueTwo = $("#preference_two").val();
    $preferenceNewValueThree = $("#preference_three").val();
    $preferenceNewValueFourth = $("#preferencene_fourth").val();
    $preferenceNewValueFifth = $("#preferencene_fifth").val();
    $preferenceNewValueSixth = $("#preferencene_sixth").val();
    $preferenceNewValueSeventh = $("#preferencene_seventh").val();

    $preferenceNewValueEight = $("#preferencene_eight").val();
    $preferenceNewValueNinth = $("#preferencene_nine").val();
    $preferenceNewValueTenth = $("#preferencene_ten").val();


    if (emailValue != '') {
        if (filter.test(emailValue)) {
            $("#updatepopup").modal("show");
        }
        else {
            toastr.error("Enter valid Email Id")
        }

    }
    if (typeof mobileNumber != 'undefined' && mobileNumber != '') {
        if ($('#preference_three').val().length < 10) {
            toastr.error("Enter valid Mobile Number")
        }
        else {
            $("#updatepopup").modal("show");
        }
    }

    if (!mobileNumber && !emailValue) {
        $("#updatepopup").modal("show");
    }


    $("#updatepopup #update_prferences_id").val($(this).data("token"));

    $("#preferencenewvalue_one").val($preferenceNewValueOne);
    $("#preferencenewvalue_two").val($preferenceNewValueTwo);
    $("#preferencenewvalue_three").val($preferenceNewValueThree);
    $("#preferencenewvalue_fourth").val($preferenceNewValueFourth);
    $("#preferencenewvalue_fifth").val($preferenceNewValueFifth);
    $("#preferencenewvalue_sixth").val($preferenceNewValueSixth);
    $("#preferencenewvalue_seventh").val($preferenceNewValueSeventh);

    $("#preferencenewvalue_eigth").val($preferenceNewValueEight);
    $("#preferencenewvalue_nine").val($preferenceNewValueNinth);
    $("#preferencenewvalue_ten").val($preferenceNewValueTenth);


});


