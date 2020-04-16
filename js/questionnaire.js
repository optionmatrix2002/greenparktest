/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$('#AddGeneralQuestion')
    .click(
        function () {
            var ResponseTypeJ;
            if ($('[value="Numeric"]:radio:checked').val() == "Numeric") {
                ResponseTypeJ = 1;
            } else if ($('[value="TrueFalse"]:radio:checked').val() == "TrueFalse") {
                ResponseTypeJ = 2;
            } else if ($('[value="YesNo"]:radio:checked').val() == "YesNo") {
                ResponseTypeJ = 3;
            } else if ($('[value="SingleChoice"]:radio:checked').val() == "SingleChoice") {
                ResponseTypeJ = 4;
            } else if ($('[value="MultipleChoice"]:radio:checked')
                    .val() == "MultipleChoice") {
                ResponseTypeJ = 5;
            }
            $.ajax({
                type: "POST",
                url: "/admin/SaveQuestionnaire",
                data: {
                    GeneralTemplateText: $('#GeneralTemplateText')
                        .val(),
                    ResponseType: ResponseTypeJ
                },

                success: function (response) {
                    toastr.success('Data saved successfully');
                    location.reload();
                },
                failure: function (response) {

                },
                error: function (response) {

                }
            });
        });
$("input[name='Questions[q_response_type]']").on("click", function () {
    var optionval = $(this).val();
    if (optionval == "4" || optionval == "5") {
        $('.optionstab').show();
    } else {
        $('.optionstab').hide();
    }
});

$(function () {
    $(document).ready(function () {
        $("#add").on("click", function () {
            $("#textboxDiv").append("<div class='col-sm-6 margintop10 newcheckbox'><div class='col-sm-9'><input type='text' class='form-control' name='options[]' placeholder='Option'/></div></div>");
        });
        $("#remove").on("click", function () {
            $("#textboxDiv").children().last().remove();
        });
    });

});

$(document).on('click', '.remove-item', function () {
    $('.remove-item').parent('div').prev('div.additional-options').remove();
})
$("#questions-q_sub_section_is_dynamic").on("click", function () {
    if ($(this).is(":checked")) {
        $("#questions-q_sub_section").attr('disabled', 'disabled');
        $("#checked_value").val("1");

    } else {
        $("#questions-q_sub_section").removeAttr('disabled');
        $("#checked_value").val("0");
    }
});

$(document).on('change', "#questions-q_sub_section", function () {

    $("#questions-q_sub_section_is_dynamic").attr('disabled', 'disabled');

    if ($(this).val() == '' || $(this).val() == null || typeof $(this).val() == 'undefined') {
        var checklistId = $('#questions-q_checklist_id').val();
        var sectionId = $('#questions-q_section').val();
        var SubsectionId = $(this).val();

        $.ajax({
            url: ajaxUrl + '/check-lists/get-sub-section-count',
            type: 'POST',
            data: {
                checkListId: checklistId,
                sectionId: sectionId,
                SubsectionId: SubsectionId
            },
            success: function (result) {
                if (result == 0) {
                    $("#questions-q_sub_section_is_dynamic").removeAttr('disabled');
                } else {
                    //$("#questions-q_sub_section_is_dynamic").attr('disabled', 'disabled');
                }

            },
        });

    }

});


$(document).on(
    'change',
    '#questions-q_section',
    function () {
        var checklistId = $('#questions-q_checklist_id').val();
        var sectionId = $(this).val();
        if (sectionId && checklistId) {
            console.log(ajaxUrl)
            $.ajax({
                url: ajaxUrl + '/check-lists/get-sub-section',
                type: 'POST',
                data: {
                    checkListId: checklistId,
                    sectionId: sectionId
                },
                success: function (result) {
                    result = $.parseJSON(result)
                    var dynamicElement = $("#questions-q_sub_section_is_dynamic");
                    dynamicElement.removeAttr("disabled");
                    dynamicElement.prop("checked", false);
                    $("#checked_value").val("0");
                    setTimeout(function () {
                        if (result.dynamicStatus) {
                            $("#questions-q_sub_section").attr('disabled', 'disabled');
                            dynamicElement.prop("checked", "true");
                            $("#questions-q_sub_section_is_dynamic").prop("disabled", true);
                            $("#checked_value").val("1");
                        }
                        if (result.subSectionStatus) {
                            dynamicElement.attr('disabled', 'disabled');
                        }
                    }, 100);
                },
            });
        }

    });

$(document).on('beforeSubmit', 'form#add-questions', function () {
    var responseType = $('input[name="Questions[q_response_type]"]:checked').val();
    if (responseType == 4 || responseType == 5) {
        var options = ($('div.optionstab').find('input[type="text"]'));
        var optionsCount = 0;
        options.each(function () {
            if ($(this).val()) {
                optionsCount++;
            }
        })

        $('.options-error').hide()
        if (optionsCount == 0) {
            $("#error_option").html("Options cannot be blank.");
            return false;
        }
        return true;
    }

})

$(document).on('change', ".checkcheckbox", function () {
    console.log($(this).val());
    if ($(this).val() == 4) {
        var selector = $(this).is(':checked') ? ':not(:checked)' : ':checked';
        $('#questions-q_access_type input[type="checkbox"]' + selector).each(function () {
            $(this).trigger('click');
        });
    }
});
if ($("#questions-q_sub_section_is_dynamic").is(":checked")) {
    $("#questions-q_sub_section").attr('disabled', 'disabled');
}