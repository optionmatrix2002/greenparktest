
$(document).ready(function () {

    $("#add_location_btn").click(function () {
        openPopup($(this).data("action"));
    });

    function openPopup(url) {
        $("#popup_model").html("").modal("show").load(url);
    }

});
