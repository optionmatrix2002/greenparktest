
$(document).ready(function () {
    Metronic.init(); // init metronic core componets
    Layout.init(); // init layout
   
    $('[data-tooltip="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover({
        trigger: "hover"
    });
   
   
    var today = new Date();
    today.setDate(today.getDate());
    var datePickerOptions = {
        maxDate: today + 1,
        useCurrent: true,
        format: 'MM-DD-YYYY HH:mm',
    }
 
});

function OpenAddRole() {
    $('#AddRole').modal('show');
    $('#txtRoleTitle').val('');
    $('#txtRoleCode').val('');
    $('#txtRoleDescription').val('');
    $('.required').addClass('hide');
    $('.required').removeClass('show');
    $('#AddEditRole').html('Add Role');
    $('#btnSaveUpdateRole').attr('value', 'Save');
    $('#btnSaveUpdateRole').attr('title', 'Save');
    $('#hdnroleid').val(0);
}
//Validations for Associate Users
function ValidateRoleDetails() {
    var valid = true;
    $('.required').removeClass('show');
    $('.required').addClass('hide');
    $('.filedrequired').removeClass('filedrequired');
    if (document.getElementById('txtRoleTitle').value.trim() == "") {
        document.getElementById('txtRoleTitleError').setAttribute('class', 'required show');
        $('#txtRoleTitle').addClass('filedrequired');
        valid = false;
    }
    if (document.getElementById('txtRoleCode').value.trim() == "") {
        document.getElementById('txtRoleCodeError').setAttribute('class', 'required show');
        $('#txtRoleCode').addClass('filedrequired');
        valid = false;
    }
    return valid;
}
//function to save or update the contact info
function SaveRoleDetails() {
    var valid = ValidateRoleDetails();
    if (valid) {
        var _data = {
            intRoleid: document.getElementById('hdnroleid').value.trim(),
            vcRoleTitle: document.getElementById('txtRoleTitle').value.trim(),
            vcRoleCode: document.getElementById('txtRoleCode').value.trim(),
            vcRoleDescription: document.getElementById('txtRoleDescription').value.trim(),
        }
        var message, isSuccess, userId;
        // Posting to controller from ajax post
        $.ajax({
            type: "POST",
            url: '/Admin/add-update-roles',
            content: "application/json; charset=utf-8",
            dataType: "json",
            data: _data,
            success: function (d) {
                //Assigning the response from controller
                message = d.message;
                isSuccess = d.success;
            },
            error: function (xhr, textStatus, errorThrown) {
                //Assigning the error message thrown
                message = xhr.responseText;
            }
        }).done(function () {
            //Showing message based on the response status
            if (isSuccess) {
                toastr.success(message);
                $('#AddRole').modal('hide');
                setTimeout(function () { location.reload(); }, 2000);
            }
            else {
                toastr.error(message);
            }
        });
    }
};

function EditRole(id) {
    {
        var rid = id;
        rid = parseInt(rid);
        document.getElementById("hdnroleid").value = rid;
        var message = "";
        var btSuccess = false;
        $('.required').addClass('hide');
        $('.required').removeClass('show');
        $('#txtRoleTitle').val('');
        $('#txtRoleCode').val('');
        $('#txtRoleDescription').val('');
        $('#AddEditRole').html('Edit Role');
        $('#btnSaveUpdateRole').attr('value', 'Update');
        $('#btnSaveUpdateRole').attr('title', 'Update');
        $.ajax({
            type: "POST",
            url: '/Admin/get-role-details',
            data: '{introleid: "' + rid + '"}',
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (d) {
                //console.log(d.ContactsData);
                $('#hdnroleid').val(d.data[0].intRoleid);
                $('#txtRoleTitle').val(d.data[0].vcRoleTitle);
                $('#txtRoleCode').val(d.data[0].vcRoleCode);
                $('#txtRoleDescription').val(d.data[0].vcDescription);
                $('#AddRole').modal('show');
            },
            error: function (xhr, textStatus, errorThrown) {
                message = xhr.responseText;
            }
        })
    }
}

$(".clsrolestatus").click(function () {
    var message = "";
    var btSuccess = false;
    var roleid = this.id.split('-')[1];
    var IsActive = "";
    if ($('#' + this.id).attr('checked')) {
        IsDone = 1;
    }
    else {
        IsDone = 0;
    }
    //Validating whether all the required field inputs meet expected criterion
    var _data = {
        IsDone: IsDone,
        id: roleid
    }
    //Poasting to controller from ajax post
    $.ajax({
        type: "POST",
        url: '/Admin/update-role-status',
        content: "application/json; charset=utf-8",
        dataType: "json",
        data: _data,
        success: function (d) {
            //Assigning the response from controller
            message = d.message;
            btSuccess = d.success;
        },
        error: function (xhr, textStatus, errorThrown) {
            //Assigning the error message thrown
            message = xhr.responseText;
        }
    }).done(function () {
        //Showing message based on the response status
        if (btSuccess) {
            toastr.success(message);
        }
        else
            toastr.error(message);
    });
});

