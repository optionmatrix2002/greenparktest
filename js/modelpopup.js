$(document).on('click', '.delete_department_btn', function () {
    $("#deletepopup #deletable_department_id").val($(this).data("token"));
    $("#deletepopup").modal("show");
});

// section Delete popup
$(document).on('click', '.delete_section_btn', function () {
    $("#deletepopup #deletable_section_id").val($(this).data("token"));
    $("#deletepopup").modal("show");
});

// Subsection Delete popup
$(document).on('click', ".delete_subsection_btn", function () {
    $("#deletepopup #deletable_subsection_id").val($(this).data("token"));
    $("#deletepopup").modal("show");
});
// Questionnare Delete popup
$(document).on('click', ".delete_questionnaire_btn", function () {
    $("#deletepopup #deletable_questionnaire_id").val($(this).data("token"));
    $("#deletepopup #checklist_id").val($(this).data("checklist"));
    $("#deletepopup #section_id").val($(this).data("section"));
    $("#deletepopup #auditspan_id").val($(this).data("auditspan"));
    $("#deletepopup #questiontext").val($(this).data("quation"));
    $("#deletepopup").modal("show");
});
// user Delete popup
$(document).on('click', ".delete_user_btn", function () {
    $("#deletepopup #deletable_user_id").val($(this).data("token"));
    $("#deletepopup").modal("show");
});

// checklist Delete popup
$(document).on('click', '.delete_checklist_btn', function () {
    $("#deletepopup #deletable_checklist_id").val($(this).data("token"));
    $("#deletepopup").modal("show");
});

// audit Delete popup
$(document).on('click', ".delete_audit_btn", function () {
    $("#deletepopup #deletable_audit_id").val($(this).data("token"));
    $("#deletepopup").modal("show");
});

//audit Attachment Delete popup
$(document).on('click', ".delete_auditattachment_btn", function () {
    $("#deletepopup #deletable_auditattachment_id").val($(this).data("token"));
    $("#deletepopup").modal("show");
});

// Cancel popup
$(document).on("click", ".cancel_auditschedule_info_btn", function () {
    $("#deletepopup #update_auditschedule_id_value").val($(this).data("token"));
    $("#deletepopup").modal("show");
});


//Cancel popup for Tickets
$(document).on("click", ".cancel_ticekts_btn", function () {
    $("#cancelpopup #cancel_ticekts_id").val($(this).data("token"));
    $("#cancelpopup").modal("show");
});

//Ticket Delete popup
$(document).on('click', ".delete_ticket_btn", function () {
    $("#deletepopup #deletable_ticket_id").val($(this).data("token"));
    $("#deletepopup").modal("show");
});

// Update audit schedule popup
$(document).on(
    "click",
    ".edit_auditschedule_info_btn",
    function () {
        $("#edituserspop #update_auditschedule_id").val($(this).data("token"));
        $("#edituserspop").modal("show");
        $.post({
            url: $("#edit_user_id").val(),
            type: "POST",
            data: $("#updateaudit_user_form").serialize(),
            success: function (data) {
                if (data) {
                    response = JSON.parse(data);
                    $("#auditsschedulesAuditorId").val(response.auditor_id).trigger('change');
                    $("#scheduleDateStart").val(response.start_date);
                    $('#scheduleDateEnd').data('DateTimePicker').minDate(response.start_date);
                    $("#scheduleDateEnd").val(response.end_date);
                    if (response.success) {
                        toastr.success(response.success);
                        setTimeout(function () {
                            location.reload();
                        }, 700);
                    } else if (response.error) {
                        toastr.error(response.error);
                        setTimeout(function () {
                            location.reload();
                        }, 700);
                    }

                }

            }

        });
    });


$(document).on("click", "#audit_update_user", function (e) {
    $("#audit_update_user").attr("disabled", true).find("i").addClass("ld ld-ring ld-cycle");
    $.ajax({
        url: $("#updateaudit_user_form").attr("action"),
        type: "POST",
        data: $("#updateaudit_user_form").serialize(),
        success: function (data) {
            if (data) {
                response = JSON.parse(data);
                if (response.success) {

                    toastr.success(response.success);

                    $("#updateaudit_user_form").modal("hide");
                    setTimeout(function () {
                        location.reload();
                    }, 700);

                } else if (response.error) {
                    toastr.error(response.error);
                }
            }
        },
        complete: function () {
            $("#audit_update_user").attr("disabled", false).find("i").removeClass("ld ld-ring ld-cycle");
        }
    });
    return false;
});

//Update Tickets audit schedule popup
$(document).on(
    "click",
    ".edit_tickets_info_btn",
    function () {
        $("#edituserspop #updat_ticket_id").val($(this).data("token"));
        $("#edituserspop").modal("show");
        $.post({
            url: $("#edit_user_id").val(),
            type: "POST",
            data: $("#ticket_update_user_form").serialize(),
            success: function (data) {
                if (data) {
                    response = JSON.parse(data);
                    $("#auditsschedulessearch-auditor_id").val(response)
                        .trigger('change');
                    if (response.success) {
                        toastr.success(response.success);
                        setTimeout(function () {
                            location.reload();
                        }, 100);
                    } else if (response.error) {
                        toastr.error(response.error);
                        setTimeout(function () {
                            location.reload();
                        }, 100);
                    }

                }

            }

        });

    });


$(document).on("click", "#ticket_audit_update_user", function (e) {
    $("#ticket_audit_update_user").attr("disabled", true).find("i").addClass("ld ld-ring ld-cycle");
    $.ajax({
        url: $("#ticket_update_user_form").attr("action"),
        type: "POST",
        data: $("#ticket_update_user_form").serialize(),
        success: function (data) {
            if (data) {
                response = JSON.parse(data);

                if (response.success) {
                    toastr.success(response.success);
                    //$("#ticket_update_user_form").modal("hide");
                    setTimeout(function () {
                        location.reload();
                    }, 700);

                } else if (response.error) {
                    toastr.error(response.error);
                    //$("#ticket_update_user_form").modal("hide");
                    setTimeout(function () {
                        location.reload();
                    }, 700);
                }
            }

        },

        complete: function () {
            $("#audit_update_user").attr("disabled", false).find("i").removeClass("ld ld-ring ld-cycle");
        }
    });
    return false;
});

$(document).on("beforeSubmit", "#cancel_auditschedule_form", function (e) {

    $.post({
        url: $(this).attr('action'),
        type: "POST",
        data: $(this).serialize(),
        success: function (data) {
            if (data) {
                response = JSON.parse(data);
                if (response.success) {
                    //toastr.success(response.success);
                    //setTimeout(function() {
                    //location.reload();
                    //}, 700);
                } else if (response.error) {
                    //toastr.error(response.error)
                }
            }
        }
    });
    $("#cancelpopup").modal("hide");
    return true;
});
$("a[name=tab]").on("click", function () {
    var a = $(this).data("index");
    if (a == 0) {

        $(".auditid").show();
        $(".schedule_auditid").hide();
    } else {
        $(".schedule_auditid").show();
        $(".auditid").hide();
    }
});

$("#checklist_id").change(function () {
    $checklistId = $("#checklist_id").val();
    $.ajax({
        type: "POST",
        url: $("#checklist_url").val(),
        data: {
            checklist_id: $(this).val()
        },
        success: function (data) {
            response = JSON.parse(data);
            $("#getFrequencyName").val(response.interval);

        }
    });
});

$("a[name=tab]").on("click", function () {
    var a = $(this).data("index");
    if (a == 0) {

        $(".ticketid").show();
        $(".archiveTicketsData").hide();
    } else {
        $(".archiveTicketsData").show();
        $(".ticketid").hide();
    }
});
//Tabs redirection in a page
/*
$(document).ready(function () {

    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        var sst = localStorage.setItem('activeTab', $(e.target).attr('href'));
    });
    var activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        $('#myTab a[href="' + activeTab + '"]').tab('show');
    }
});*/


$(document).on("click", ".delete_auditschedule_info_btn", function () {
    $("#deletepopupModal #delete_auditschedule_id_value").val($(this).data("token"));
    $("#deletepopupModal").modal("show");
});

$(document).on("click", ".delete_audit_info_btn", function () {
    $("#deleteAuditPopup #deletable_audit_id").val($(this).data("token"));
    $("#deleteAuditPopup").modal("show");
});
