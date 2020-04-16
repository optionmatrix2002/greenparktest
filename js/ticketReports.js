/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$('.description').hide();
$('.attachments').hide();
$('.history').hide();
$('a[data-toggle="tab"][href="#details"]').on('shown.bs.tab', function (e) {
    $('.details').show();
    $('.description').hide();
    $('.attachments').hide();
    $('.history').hide();
})
$('a[data-toggle="tab"][href="#description"]').on('shown.bs.tab', function (e) {
    $('.description').show();
    $('.details').hide();
    $('.attachments').hide();
    $('.history').hide();

})
$('a[data-toggle="tab"][href="#attachments"]').on('shown.bs.tab', function (e) {
    $('.attachments').show();
    $('.description').hide();
    $('.details').hide();
    $('.history').hide();

})
$('a[data-toggle="tab"][href="#history"]').on('shown.bs.tab', function (e) {
    $('.history').show();
    $('.description').hide();
    $('.attachments').hide();
    $('.details').hide();
})

$(document).on('change', '#changeAssignedUser', function () {
    var beforeValue = $('#changeAssignedUserHidden').val();
    $('button#saveAssingedUser').attr('disabled', 'disabled');
    var currentValue = $(this).val();
    if (beforeValue != currentValue) {
        $('button#saveAssingedUser').removeAttr('disabled');
    }
});

$(document).on('click', 'button#saveAssingedUser', function () {
    $(this).attr('disabled', 'disabled').find("i").addClass("ld ld-ring ld-cycle");
    var currentValue = $('#changeAssignedUser').val();
    var ticketId = $('#selectedTicketId').val();
    $.ajax({
        url: ajaxUrl + '/tickets/update-ticket-assigned',
        type: 'POST',
        data: {userId: currentValue, ticketId: ticketId},
        success: function (result) {
            toastr.success('Ticket assigned successfully.');
            setTimeout(function () {
                location.reload();
            }, 700);
        }
    });
});
var JSONdateEnabledModules = $("#dateEnabledModules").data("date-enabled-modules");
function isDateEnabledModule() {
    if (JSONdateEnabledModules && $.inArray($("#ticketprocesscritical-improve_plan_module_id").val(), JSONdateEnabledModules) != -1) {
        return true;
    }
    return false;
}

$("#ticketprocesscritical-improve_plan_module_id").change("change", function () {
    if (isDateEnabledModule()) {
        $("#stop_notification_date_block").removeClass("hidden");
    } else {
        $("#stop_notification_date_block").addClass("hidden");
        $('#ticketprocesscritical-stop_notifications_until_date').val("");
    }
});

$(document).ready(function () {
    $('#ticketprocesscritical-stop_notifications_until_date').datetimepicker({
        format: 'DD-MM-YYYY',
        minDate: new Date(),
        maxDate: new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() + 180)
    });
});

$('form#root_cause_analysis_form').on('beforeSubmit', function (e) {
    if (isDateEnabledModule() && !$("#ticketprocesscritical-stop_notifications_until_date").val()) {
        toastr.error("Stop Notification Until Date is mandatory");
    } else {
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
    }

    return false;
});



