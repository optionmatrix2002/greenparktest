$('#login-form').on(
    'beforeSubmit',
    function() {
        $("#submit_login_btn").attr("disabled", true).find("i")
            .removeClass("m-icon-swapright m-icon-white").addClass(
            "ld ld-ring ld-cycle");
        $.post({
            url : $(this).attr("action"),
            data : $(this).serializeArray(),
            success : function(data) {
                response = JSON.parse(data);
                if (response.error) {
                    toastr.error(response.error);
                } else if (response.success) {
                    toastr.success(response.success);
                    window.location = response.redirect_url;
                }
            },
            complete : function() {
                $("#submit_login_btn").attr("disabled", false).find("i")
                    .removeClass("ld ld-ring ld-cycle").addClass(
                    "m-icon-swapright m-icon-white");
            }
        });
        return false;
    });
$('#login-form ,  #set_password_form, #forgot-form').on(
    'afterValidate',
    function(event, messages, errorAttributes) {

        $.each(messages, function(key, messagesArray) {
            $.each(messagesArray, function(index, errorMessage) {
                if (errorMessage) {
                    $("#" + key).addClass("ld ld-rubber no-iteration");
                    setTimeout(function() {
                        $("#" + key).removeClass(
                            "ld ld-rubber no-iteration");
                    }, 1000);
                    toastr.error(errorMessage);
                    setTimeout(function () {
                        location.reload();
                    }, 700);
                }
            });
        });
    });