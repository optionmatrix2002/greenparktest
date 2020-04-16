/*
 * author Shesharao Puli 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(".nav-bids").removeClass("active");
$("#MenuSystemAdmin").addClass("active");
$("#MenuLookupOptions").addClass("active");

$('#organisation_hierarchy')
        .jstree(
                {
                    "core": {
                        'data': {
                            'url': hierarchy_url,
                            'data': function (node) {
                                var nodeData = typeof node.original != 'undefined' ? node.original
                                        : node;
                                return {
                                    "id": nodeData.id,
                                    "type": nodeData.type,
                                    'hotelId': nodeData.hotelId,
                                    'departmentId': nodeData.departmentId
                                };
                            }
                        },
                        "check_callback": true,
                        "multiple": false,
                        "themes": {
                            "variant": "large"
                        }
                    },
                    'contextmenu': {
                        'items': function (node) {
                            var items = {};
                            switch (node.type) {
                                case "root":
                                    items = {
                                        createLocation: {// The "rename" menu
                                            // item
                                            label: "Add Location",
                                            "icon": "fa fa-plus text-success",
                                            action: function () {
                                                openPopup(node.original.action_url);
                                            }
                                        }
                                    };
                                    break;
                                case "location":
                                    items = {
                                        showAddHotelWindow: {// The "rename" menu
                                            // item
                                            label: "Add Hotel",
                                            "icon": "fa fa-plus text-success",
                                            action: function () {

                                                openPopup(node.original.action_url);
                                            }
                                        },
                                        editLocation: {// The "rename" menu item
                                            label: "Edit",
                                            "icon": "fa fa-pencil-square-o text-info",
                                            action: function () {
                                                openPopup(node.original.edit_url);
                                            }
                                        },
                                        deleteLocation: {// The "delete" menu
                                            // item
                                            label: "Delete",
                                            "icon": "fa fa-times text-danger",
                                            action: function () {
                                                openPopup(node.original.delete_url);
                                            }
                                        }
                                    };
                                    break;
                                case "hotel":
                                    items = {
                                        createDepartment: {// The "rename" menu
                                            // item
                                            label: "Add Department",
                                            "icon": "fa fa-plus text-success",
                                            action: function () {
                                                openPopup(node.original.action_url);
                                            }
                                        },
                                        editHotel: {// The "delete" menu item
                                            label: "Edit",
                                            "icon": "fa fa-pencil-square-o text-info",
                                            action: function () {
                                                openPopup(node.original.edit_url);
                                            }
                                        },
                                        deleteHotel: {// The "delete" menu item
                                            label: "Delete",
                                            "icon": "fa fa-times text-danger",
                                            action: function () {
                                                openPopup(node.original.delete_url);
                                            }
                                        },
                                        cloneHotel: {
                                            label: "Clone",
                                            "icon": "fa fa-copy text-primary",
                                            action: function () {
                                                openPopup(node.original.clone_url);
                                            }
                                        },
                                    };
                                    break;
                                case "department":
                                    items = {
                                        configureEmails: {// The "rename" menu
                                            // item
                                            label: "Configure Emails",
                                            "icon": "fa fa-envelope-o text-primary",
                                            action: function () {
                                                openPopup(node.original.configure_email_url);
                                            }
                                        },
                                        createSection: {// The "rename" menu
                                            // item
                                            label: "Add Section",
                                            "icon": "fa fa-plus text-success",
                                            action: function () {
                                                openPopup(node.original.action_url);
                                            }
                                        },
                                        editDepartment: {// The "delete" menu
                                            // item
                                            label: "Edit",
                                            "icon": "fa fa-pencil-square-o text-info",
                                            action: function () {
                                                openPopup(node.original.edit_url);
                                            }
                                        },
                                        deleteDepartment: {// The "delete" menu
                                            // item
                                            label: "Delete",
                                            "icon": "fa fa-times text-danger",
                                            action: function () {
                                                openPopup(node.original.delete_url);
                                            }
                                        }
                                    };
                                    break;
                                case "section":
                                    items = {
                                        createSubsection: {// The "rename" menu
                                            // item
                                            label: "Add Subsection",
                                            "icon": "fa fa-plus text-success",
                                            action: function () {
                                                openPopup(node.original.action_url);
                                            }
                                        },
                                        editSection: {// The "delete" menu item
                                            label: "Edit",
                                            "icon": "fa fa-pencil-square-o text-info",
                                            action: function () {
                                                openPopup(node.original.edit_url);
                                            }
                                        },
                                        deleteSection: {// The "delete" menu
                                            // item
                                            label: "Delete",
                                            "icon": "fa fa-times text-danger",
                                            action: function () {
                                                openPopup(node.original.delete_url);
                                            }
                                        }
                                    };
                                    break;
                                case "subsection":
                                    items = {
                                        editSubsection: {// The "delete" menu
                                            // item
                                            label: "Edit",
                                            "icon": "fa fa-pencil-square-o text-info",
                                            action: function () {
                                                openPopup(node.original.edit_url);
                                            }
                                        },
                                        deleteSubsection: {// The "delete" menu
                                            // item
                                            label: "Delete",
                                            "icon": "fa fa-times text-danger",
                                            action: function () {
                                                openPopup(node.original.delete_url);
                                            }
                                        }
                                    };
                                    break;

                                default:

                                    break;
                            }

                            return items;
                        }
                    },
                    "types": {
                        "root": {
                            "icon": icons_location
                                    + "/icons8_Active_Directory_18px.png",
                            "valid_children": ["location"]
                        },
                        "location": {
                            "icon": icons_location + "/icons8_Marker_18px.png",
                            "valid_children": ["hotel"]
                        },
                        "hotel": {
                            "icon": icons_location
                                    + "/icons8_Hotel_Building_18px.png",
                            "valid_children": ["department"]
                        },
                        "department": {
                            "icon": icons_location
                                    + "/icons8_Department_18px.png",
                            "valid_children": ["section"]
                        },
                        "section": {
                            "icon": icons_location
                                    + "/icons8_User_Groups_18px.png",
                            "valid_children": ["subsection"]
                        },
                        "subsection": {
                            "icon": icons_location
                                    + "/icons8_Section_18px.png",
                            "valid_children": []
                        }
                    },
                    "plugins": ["contextmenu", "unique", "json_data",
                        "state", "types", "sort"]
                }
        );

$(document).on(
        "beforeSubmit",
        "#new_location_form",
        function () {
            inProcessBtn("save_location_submit_btn");
            $.post({
                url: $(this).attr("action"),
                data: $(this).serializeArray(),
                success: function (data) {
                    response = JSON.parse(data);
                    if (response.success) {
                        toastr.success(response.success);
                        $("#popup_model").modal("hide");
                        var jsTreeInstance = $('#organisation_hierarchy')
                                .jstree(true);
                        if (response.parent_node) {
                            jsTreeInstance.create_node(response.parent_node,
                                    response.node);
                            jsTreeInstance.open_node(response.parent_node);
                        } else if (response.node) {
                            jsTreeInstance.rename_node(jsTreeInstance
                                    .get_node(response.node),
                                    response.node.text);
                        }
                    } else if (response.error) {
                        toastr.error(response.error);
                    }
                },
                complete: function () {
                    outProcessBtn("save_location_submit_btn");
                }
            });
            return false;
        });
$(document).on(
        "beforeSubmit",
        "#hotel_form",
        function () {
            inProcessBtn("save_hotel_submit_btn");
            $.post({
                url: $(this).attr("action"),
                data: $(this).serializeArray(),
                success: function (data) {
                    response = JSON.parse(data);
                    if (response.success) {
                        toastr.success(response.success);
                        $("#popup_model").modal("hide");
                        var jsTreeInstance = $('#organisation_hierarchy')
                                .jstree(true);
                        if (response.parent_node) {
                            jsTreeInstance.create_node(response.parent_node,
                                    response.node);
                            jsTreeInstance.open_node(response.parent_node);
                            jsTreeInstance.refresh();
                        } else if (response.node) {
                            jsTreeInstance.rename_node(jsTreeInstance
                                    .get_node(response.node),
                                    response.node.text);
                        }
                    } else if (response.error) {
                        toastr.error(response.error);
                    }
                },
                complete: function () {
                    outProcessBtn("save_hotel_submit_btn");
                }
            });
            return false;
        });
$(document).on(
        "beforeSubmit",
        "#hoteldepartment_form",
        function () {
            inProcessBtn("save_multipledepartment_submit_btn");
            $.post({
                url: $(this).attr("action"),
                data: $(this).serializeArray(),
                success: function (data) {
                    response = JSON.parse(data);
                    if (response.success) {
                        toastr.success(response.success);
                        $("#popup_model").modal("hide");
                        var jsTreeInstance = $('#organisation_hierarchy')
                                .jstree(true);
                        if (response.parent_node) {
                            jsTreeInstance.create_node(response.parent_node,
                                    response.node);
                            jsTreeInstance.open_node(response.parent_node);
                            jsTreeInstance.refresh();
                        } else if (response.node) {
                            jsTreeInstance.rename_node(jsTreeInstance
                                    .get_node(response.node),
                                    response.node.text);
                        }
                    } else if (response.error) {
                        toastr.error(response.error);
                    }
                },
                complete: function () {
                    outProcessBtn("save_multipledepartment_submit_btn");
                }
            });
            return false;
        });
$(document).on("beforeSubmit", "#newdepartment_form", function () {
    inProcessBtn("save_newdepartment_submit_btn");
    $.post({
        url: $(this).attr("action"),
        data: $(this).serializeArray(),
        success: function (data) {
            response = JSON.parse(data);
            if (response.success) {
                toastr.success(response.success);
                // $("#popup_model").modal("hide");
                // alert(response.url);
                openPopup(response.url);

            } else if (response.error) {
                toastr.error(response.error);
            }
        },
        complete: function () {
            outProcessBtn("save_department_submit_btn");
        }
    });
    return false;
});

$(document)
        .on(
                "beforeSubmit",
                "#subsection_form",
                function () {
                    inProcessBtn("save_subsection_submit_btn");
                    $
                            .post({
                                url: $(this).attr("action"),
                                data: $(this).serializeArray(),
                                success: function (data) {
                                    response = JSON.parse(data);
                                    if (response.success) {
                                        toastr.success(response.success);
                                        $("#popup_model").modal("hide");
                                        if (response.nodes) {
                                            var nodesList = response.nodes;
                                            renameNodes(nodesList);
                                        }
                                    } else if (response.error) {
                                        // toastr.error(response.error);
                                        var errors = response.error;
                                        if (errors['ss_subsection_name']) {
                                            var deparmentNameField = $('.field-subsections-ss_subsection_name');
                                            deparmentNameField
                                                    .addClass("has-error");
                                            deparmentNameField
                                                    .find('p')
                                                    .html(
                                                            errors['ss_subsection_name']);
                                        }
                                        if (errors['ss_subsection_remarks']) {
                                            var deparmentNameField = $('.field-subsections-ss_subsection_remarks');
                                            deparmentNameField
                                                    .addClass("has-error");
                                            deparmentNameField
                                                    .find('p')
                                                    .html(
                                                            errors['ss_subsection_remarks']);
                                        }
                                    }
                                },
                                complete: function () {
                                    outProcessBtn("save_subsection_submit_btn");
                                }
                            });
                    return false;
                });

$("#add_location_btn").click(function () {
    openPopup($(this).data("action"));
});

function openPopup(url) {
    $("#popup_model").html("").modal("show").load(url);
}

$(document).on(
        "beforeSubmit",
        "#delete_form",
        function () {
            inProcessBtn("delete_node_btn");
            $.post({
                url: $(this).attr("action"),
                data: $(this).serializeArray(),
                success: function (data) {
                    response = JSON.parse(data);
                    if (response.success) {
                        toastr.success(response.success);
                        $("#popup_model").modal("hide");
                        if (response.node) {
                            var jsTreeInstance = $('#organisation_hierarchy')
                                    .jstree(true);
                            jsTreeInstance.delete_node(jsTreeInstance
                                    .get_node(response.node));
                        }
                    } else if (response.error) {
                        toastr.error(response.error);
                    }
                },
                complete: function () {
                    outProcessBtn("delete_node_btn");
                }
            });
            return false;
        });
$(document).on("beforeSubmit", "#clone_hotel_form", function () {
    inProcessBtn("clone_hotel_btn");
    $.post({
        url: $(this).attr("action"),
        data: $(this).serializeArray(),
        success: function (data) {
            response = JSON.parse(data);
            if (response.success) {
                toastr.success(response.success);
                $("#popup_model").modal("hide");
                var jsTreeInstance = $('#organisation_hierarchy').jstree(true);
                jsTreeInstance.refresh();
            } else if (response.error) {
                toastr.error(response.error);
            }
        },
        complete: function () {
            outProcessBtn("clone_hotel_btn");
        }
    });
    return false;
});

$('#popup_model').on('hidden.bs.modal', function () {
    $(this).html("");
})

$(document)
        .on(
                "beforeSubmit",
                "#edit_department_form",
                function () {
                    inProcessBtn("save_multipledepartment_submit_btn");
                    $
                            .post({
                                url: $(this).attr("action"),
                                data: $(this).serializeArray(),
                                success: function (data) {
                                    response = JSON.parse(data);
                                    if (response.success) {
                                        toastr.success(response.success);
                                        $("#popup_model").modal("hide");
                                        if (response.nodes) {
                                            var nodesList = response.nodes;
                                            renameNodes(nodesList);
                                        }
                                    } else if (response.error) {
                                        // toastr.error(response.error);
                                        var errors = response.error;
                                        if (errors['department_name']) {
                                            var deparmentNameField = $('.field-departments-department_name');
                                            deparmentNameField
                                                    .addClass("has-error");
                                            deparmentNameField.find('p').html(
                                                    errors['department_name']);
                                        }
                                        if (errors['department_description']) {
                                            var deparmentNameField = $('.field-departments-department_description');
                                            deparmentNameField
                                                    .addClass("has-error");
                                            deparmentNameField
                                                    .find('p')
                                                    .html(
                                                            errors['department_description']);
                                        }

                                    }
                                },
                                complete: function () {
                                    outProcessBtn("save_multipledepartment_submit_btn");
                                }
                            });
                    return false;
                });

/**
 *
 */
renameNodes = function (list) {
    var jsTreeInstance = $('#organisation_hierarchy').jstree(true);
    $.each(list, function (i, value) {
        jsTreeInstance.rename_node(jsTreeInstance.get_node(value.node),
                value.node.text);

    });
}
$(document)
        .on(
                "beforeSubmit",
                "#section_form",
                function () {
                    inProcessBtn("save_section_submit_btn");

                    $
                            .post({
                                url: $(this).attr("action"),
                                data: $(this).serializeArray(),
                                success: function (data) {
                                    response = JSON.parse(data);
                                    if (response.success) {
                                        toastr.success(response.success);
                                        $("#popup_model").modal("hide");
                                        if (response.nodes) {
                                            var nodesList = response.nodes;
                                            renameNodes(nodesList);
                                        }
                                    } else if (response.error) {
                                        // toastr.error(response.error);
                                        var errors = response.error;
                                        if (errors['s_section_name']) {
                                            var deparmentNameField = $('.field-sections-s_section_name');
                                            deparmentNameField
                                                    .addClass("has-error");
                                            deparmentNameField.find('p').html(
                                                    errors['s_section_name']);
                                        }
                                        if (errors['department_description']) {
                                            var deparmentNameField = $('.field-sections-s_section_remarks');
                                            deparmentNameField
                                                    .addClass("has-error");
                                            deparmentNameField
                                                    .find('p')
                                                    .html(
                                                            errors['s_section_remarks']);
                                        }
                                    }
                                },
                                complete: function () {
                                    outProcessBtn("save_section_submit_btn");
                                }
                            });
                    return false;
                });

$(document).on("beforeSubmit", "#dept_hotel_configure_email_form", function () {
    inProcessBtn("dept_hotel_configure_email_submit_btn");
    $.post({
        url: $(this).attr("action"),
        data: $(this).serializeArray(),
        success: function (data) {
            response = JSON.parse(data);
            if (response.success) {
                toastr.success(response.success);
                //$("#popup_model").modal("hide");
            } else if (response.error) {
                toastr.error(response.error);
            }
        },
        complete: function () {
            outProcessBtn("dept_hotel_configure_email_submit_btn");
        }
    });
    return false;
});