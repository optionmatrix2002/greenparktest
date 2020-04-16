

$("#AddSelectedClients").click(function () {
    $("#ddlAllClients option:selected").each(function () {
        var $this = $(this);
        if ($this.length) {
            var selvalue = $this.val();
            var selText = $this.text();
            $("#ddlSelectedClients").append($("<option></option>").val
            (selvalue).html(selText));
            $this.remove();
        }
    });
});
$("#RemoveSelectedClients").click(function () {
    $("#ddlSelectedClients option:selected").each(function () {
        var $this = $(this);
        if ($this.length) {
            var selvalue = $this.val();
            var selText = $this.text();
            $("#ddlAllClients").append($("<option></option>").val
            (selvalue).html(selText));
            $this.remove();
        }
    });
});
$('#tree_25').jstree({
    'plugins': ["checkbox", "types"]
});

/* Date: 07 Nov, 2017 Author Shesharao Puli*/
$(document).on("beforeSubmit", "#add_role_form", function (e) {
    $("#role_add_submit_btn").attr("disabled", true).find("i").addClass("ld ld-ring ld-cycle");
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
        },
        complete: function () {
            $("#role_add_submit_btn").attr("disabled", false).find("i").removeClass("ld ld-ring ld-cycle");
        }
    });
    return false;
});

$("#new_role_btn, .edit_role_info_btn").click(function () {
    $("#addrolespop").modal("show").load($(this).data("action"));
});

$(document).on('click', ".load_permissions_link", function () {
    $.post({
        url: $("#load_permission_url").val(),
        data: {role_token: $(this).data("token")},
        success: function (response) {
            $("#addPermissionsModal").modal("show").html(response);
        }
    });
});
$(".delete_role_btn").click(function () {
    $("#deletepopup #deletable_role_id").val($(this).data("token"));
    $("#deletepopup").modal("show");
});

$(document).on("beforeSubmit", "#selected_permissions_form", function (e) {
    $.post({
        url: $(this).attr('action'),
        data: $(this).serialize(),
        success: function (data) {
            console.log(data);
            if (data) {
                response = JSON.parse(data);
                if (response.success) {
                    toastr.success(response.success);
                    setTimeout(function () {
                        location.reload();
                    }, 700);
                } else if (response.error) {
                    toastr.error(response.error)
                }
            }
        }
    });

    return false;
});
$(document).on("beforeSubmit", "#delete_role_form", function (e) {
    $.post({
        url: $(this).attr('action'),
        data: $(this).serialize(),
        success: function (data) {
            if (data) {
                response = JSON.parse(data);
                if (response.success) {
                    toastr.success(response.success);
                    setTimeout(function () {
                        location.reload();
                    }, 700);
                } else if (response.error) {
                    toastr.error(response.error)
                }
            }
        }
    });

    return false;
});
