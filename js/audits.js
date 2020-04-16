$(document).ready(function () {
    var startdate = $('#dateStart').val();
    $('#dateEnd').datetimepicker({
        // useCurrent : false,
        format: 'DD-MM-YYYY',
        //minDate : moment()
    });
    if (startdate) {
        $('#dateStart').trigger('change');
    }

});
$(function () {
    var startdate = $('#dateStart').val();
    if ((startdate == '')) {
        var startdate = $('#dateStart').val();
        $('#dateStart,#dateEnd').datetimepicker({
            // useCurrent : false,
            format: 'DD-MM-YYYY',
            //minDate : moment()
        });

        $('#dateStart').datetimepicker().on('dp.change', function (e) {

            var incrementDay = moment(new Date(e.date));
            incrementDay.add(1, 'days');
            $('#dateEnd').data('DateTimePicker').minDate(incrementDay);
            $('#dateEnd').val('');
            $(this).data("DateTimePicker").hide();
        });

    }
});


$(document).ready(function () {

    $('#scheduleDateStart,#scheduleDateEnd , #auditDateStart,#auditDateEnd').datetimepicker({
        // useCurrent : false,
        format: 'DD-MM-YYYY',
        widgetPositioning: {
            vertical: 'bottom'
        }
        //minDate : moment()
    }).keydown(function (event) {
        if (event.keyCode != 8) {
            return false;
        }

    });

    var schedulerStartDate = $('#scheduleDateStart').val();

    if (schedulerStartDate) {
        $('#scheduleDateStart').trigger('change');
    }

    $('#scheduleDateStart,#auditDateStart').datetimepicker().on('dp.change', function (e) {
        var incrementDay = moment(new Date(e.date));
        incrementDay.add(1, 'days');

        $('#scheduleDateEnd').data('DateTimePicker').minDate(incrementDay);
        $('#scheduleDateEnd').val('');

        $('#auditDateEnd').data('DateTimePicker').minDate(incrementDay);
        $('#auditDateEnd').val('');

        $(this).data("DateTimePicker").hide();
    });

});


$(function () {
    var logdate = $('#log_time').val();
    if ((logdate == '')) {
        var logdate = $('#log_time').val();
        $('#log_time').datetimepicker({
            format: 'DD-MM-YYYY',
            minDate: moment()
        });

    } else {
        var logdate = $('#log_time').val();
        $('#log_time').datetimepicker({
            format: 'DD-MM-YYYY',
            minDate: moment()
        });
    }
});
$(function () {
    var startdate = $('#dateDue').val();
    if ((startdate == '')) {
        var startdate = $('#dateDue').val();
        $('#dateDue').datetimepicker({
            // useCurrent : false,
            format: 'DD-MM-YYYY',
            minDate: moment()
        });

    } else {
        var startdate = $('#dateDue').val();
        $('#dateDue').datetimepicker({
            // useCurrent : false,
            format: 'DD-MM-YYYY',
            minDate: moment()
        });
    }
});


$(function () {
    $('input[type="file"]').change(function (e) {
        $('.attachment_btn').attr('disabled', false);
    });
});

$(document).on('click', '.email-reports', function () {
    $('.email_report_input').val('');
    $('button#sendEmailRport').attr('disabled', 'disabled');
    $('span.email-error-text').text('');
    $("#emailAuditReport").modal("show");
});

$(document).on("keyup", '.email_report_input', function () {
    var submitElement = $('button#sendEmailRport');
    var helpText = $('span.email-error-text');
    submitElement.attr('disabled', 'disabled');

    helpText.text('');
    var email = $(this).val();

    if (!email) {
        helpText.text('Email cannot be empty');
        return false;
    }
    if (email) {
        var multipleEmails = email.split(',');
        var emailValid = true;
        if (multipleEmails.length > 1) {
            multipleEmails.forEach(function (value) {
                if (value && !validateEmail(value)) {
                    emailValid = false;
                    return false;
                }
            })
        } else {
            if (!validateEmail(email)) {
                emailValid = false;
            }
        }
        if (!emailValid) {
            helpText.text('Please enter valid email addresses');
            return false;
        } else {
            submitElement.removeAttr('disabled');
        }
    }
});
$(document).on('click', 'button#sendEmailRport', function () {
    var auditScheduleId = $('.hidden_audit_schedule_id').val();
    $("button#sendEmailRport").find("i").addClass("ld ld-ring ld-cycle");
    var auditName = auditId;
    var email = $('.email_report_input').val();
    var urls = fileList;
    $(this).attr('disabled', 'disabled');
    if (email && auditScheduleId) {
        $.ajax({
            url: ajaxUrl + '/audits/send-attachments',
            type: 'POST',
            data: {
                email: email,
                auditScheduleId: auditScheduleId,
                attachments: urls,
                auditName: auditName
            },
            success: function (result) {
                var response = $.parseJSON(result);
                $("#emailAuditReport").modal("hide");
                $("button#sendEmailRport").find("i").removeClass("ld ld-ring ld-cycle");
                if (response.status) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
        });
    }
});

/**
 *
 * @param sEmail
 * @returns {boolean}
 */
function validateEmail(sEmail) {

    var filter = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    if (filter.test(sEmail)) {
        return true;
    }
    return false;
}

$(document).on('click', 'button.create-child-audit', function () {
    var lastDate = $('.lastAuditEndDate').val();
    if (lastDate) {
        var incrementDay = moment(new Date(lastDate));
        incrementDay.add(1, 'days');
        var startDate = $('#auditDateStart').val();
        if (startDate) {
            $('#auditDateEnd').data('DateTimePicker').minDate(startDate);
        }
        //$('#auditDateStart').data('DateTimePicker').minDate(incrementDay);
        //
    }
    $("#schedule-child-aduit").modal("show");
    $('form#create_audit_form')[0].reset();
});

$(document).on('change blur', '.scheduler-create', function () {
    var enableSaveButton = true;
    var button = $('button#audit_create_user');
    button.attr('disabled', true);
    $('.scheduler-create').each(function () {
        if ($(this).val() == '' || $(this).val() == null || $(this).val() == 'undefined' || typeof $(this).val() == 'undefined') {
            enableSaveButton = false;
        }
    });
    if (enableSaveButton) {
        button.attr('disabled', false);
    }
});

$(document).on('change blur', '.schedule-update', function () {
    var enableSaveButton = true;
    var updateButton = $('button#audit_update_user');
    updateButton.attr('disabled', true);
    $('.schedule-update').each(function () {
        if ($(this).val() == '' || $(this).val() == null || $(this).val() == 'undefined' || typeof $(this).val() == 'undefined') {
            enableSaveButton = false;
        }
    });
    if (enableSaveButton) {
        updateButton.attr('disabled', false);
    }
});
/**
 *
 */
$(document).on('click', '#audit_create_user', function () {

    $.ajax({
        url: ajaxUrl + '/audits/create-audit',
        type: 'POST',
        data: $('form#create_audit_form').serialize(),
        success: function (result) {
            var response = $.parseJSON(result);
            if (!response.status) {
                toastr.error(response.message);
            }
        }
    });
});

$(document).on('change', '#enableNonComplaints', function () {
    $('.complaint-class').show();
    if ($(this).is(':checked')) {
        $('.complaint-class').hide();
    }
});


$("form#download_files_form").on("beforeSubmit", function (e) {
    $(this).find(".submitButton > i").removeClass("fa fa-download").addClass("ld ld-ring ld-cycle").text("");
    if ($("#impackSummary").is(':checked')) {
        link = document.createElement('a');
        link.href = impactSummaryPDF;
        link.download = impactSummaryPDF.replace(/^.*[\\\/]/, '');
        document.body.appendChild(link);
        link.click();
    } else if ($("#entireAudit").is(':checked')) {
        compressed_img(fileList, auditId);
    } else {
        alert("Please select atleast one file to start download");
    }
    $(this).find(".submitButton > i").removeClass("ld ld-ring ld-cycle").addClass("fa fa-download").text("");
    return false;
});

function compressed_img(urls, name) {
    if (!(urls.length > 0)) {
        alert("There are no files in the selection");
        return false;
    }
    var zip = new JSZip();
    var count = 0;
    var name = name + '.zip';
    $("#downloadAuditFilesPercentage").removeClass("hidden");
    var progressBar = new ldBar("#downloadAuditFilesPercentage", {preset: "line"});
    $.each(urls, function (index, url) {
        JSZipUtils.getBinaryContent(url, function (err, data) {
            if (err) {
                throw err;
            }
            zip.file(url, data, {binary: true});
            $("#downloadAuditFilesStatus").text("Zipping: " + (index + 1));
            progress = (index + 1) / urls.length * 100;//percentage of the file
            progressBar.set(progress);
            count++;
            if (count === urls.length) {
                zip.generateAsync({type: 'blob'}).then(function (content) {
                    $("#downloadAuditFilesStatus").text("Zipped. Downloading...");
                    progressBar = null;
                    $("#downloadAuditFilesPercentage").addClass("hidden");
                    saveAs(content, name);
                    setTimeout(function () {
                        $("#downloadAuditFilesStatus").text("");
                    }, 5000);
                });
            }

        });
    });
}