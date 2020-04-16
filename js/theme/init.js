Metronic.init(); // init metronic core componets
Layout.init(); // init layout

$('[data-tooltip="tooltip"]').tooltip();
$('[data-toggle="popover"]').popover({
    trigger: "hover"
});
$("#example1").DataTable();
