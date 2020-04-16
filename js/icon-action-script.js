$(window).on('load', function() {
    // Animate loader off screen
    $(".se-pre-con").fadeOut("slow");;
});
$(document).ready(function () {
    $("#accordion").collapse().sortable({
        connectWith: "#dropBag",
    });
    $("#dropBag").sortable({
        connectWith: "#accordion"
    });
    $("#accordion1").collapse().sortable({
        connectWith: "#dropBag",
    });
    $("#dropBag").sortable({
        connectWith: "#accordion1"
    });
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////// Module Help /////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    var tour1 = new Tour({
        steps: [

            {
                element: '#MenuDashboard',
                title: 'Dashboard',
                content: "The dashboard helps to quickly view some of the key statistics of greenpark operations. Data shown here are in real time. The parameters can be filtered to increase the usability of the dashboard.",
                backdrop: true
            },
            {
                element: '#MenuChecklists',
                title: 'Checklists',
                content: "Audit checklists can be viewed and managed here. New audit checklists can be created and configured. Audit type, method, frequency, department and other audit parameters can be configured here.",
                backdrop: true
            },
            {
                element: '#MenuAudits',
                title: 'Audits',
                content: "Audits can be scheduled, assigned to auditors, view live-questionnaire and audit status, download and email reports, perform section-wise comparisons with previous audits.",
                backdrop: true
            },
            {
                element: '#tickets',
                title: 'Tickets',
                content: "Non compliances and chronic issues raised from the audits can be tracked here. New tasks can be created and assigned as tickets. Add new comments and attachments to tickets.",
                backdrop: true
            }
            ,
            {
                element: '#MenuSystemAdmin',
                title: 'System Admin',
                content: "All system level configurations can be done here. Organization structure can be setup. Users, roles and permissions can be configured here. Default preferences can be adjusted. Events can be logged and errors can be traced.",
                backdrop: true
            },
            {
                element: '#MenuMasterData',
                title: 'Master Data',
                content: "This is where the master data of departments, sections and subsections can be managed. Any change here would be reflected across the whole application and hence should be handled with caution.",
                backdrop: true
            }]
    });

    $('#mhelp').click(function () {
        tour1.init();
        tour1.restart();
        $('#helpModal').modal('hide');
    });

    var TourForSection = new Tour({
        steps: [
            {
                element: '#project-dashboard',
                title: 'Requisitions',
                content: "Requisition is an item requirements list that can be initiated and approved on this screen by authorized personal only. User selects type, category, item and other details and needs an approval before it is submitted for approval.",
                backdrop: true
            },
            {
                element: '#enquiry',
                title: 'Enquiries',
                content: "Approved requisitions are validated and fine tuned here. Items from a requisitions can also be available at a different project site and for this is a request to check it. If the product is available then there is no need to procure from a vendor, it can be transferred internally.",
                backdrop: true
            },
            {
                element: '#quotation-request',
                title: 'RFQ',
                content: "All approved requisitions are displayed here. The tree shows requisitions types then list of requisitions then category and list of vendors who are available in the system.",
                backdrop: true
            },
            {
                element: '#quotation-review',
                title: 'Quotation Review',
                content: "All quotations from different vendors for a particular RFQ are received and compared based on price, quantity available, vendor reviews and date of delivery in the screen below.",
                backdrop: true
            }
            ,
            {
                element: '#manage-indents',
                title: 'Indents',
                content: "List of approved quotation are listed here for final approval where the PO/WO can be created depending on the type of requisition. User needs to click on approval to enter OTP and approve as well as can click view in RFQ to view the details and justification for the requisition.",
                backdrop: true
            },
            {
                element: '#purchase-orders',
                title: 'Purchase Orders',
                content: "PO’s are generated automatically when the indents are approved. PO’s are for procurement of items mentioned in the requisitions. There can be multiple PO’s for a single approved indent.",
                backdrop: true
            },
            {
                element: '#work-orders',
                title: 'Work Orders',
                content: "WO’s are generated automatically when the indents are approved. WO’s are for services that are mentioned in the requisitions. There can be multiple WO’s for a single approved indent.",
                backdrop: true
            }
            ,
            {
                element: '#consignments',
                title: 'Consignments',
                content: "This is a graphical view of the consignments as a progress bar and allows to tag / upload / view information / scanned images such as tax invoice, way bill info, way bill copy, GRN, OSD report and payment status. Filter is provided on top.",
                backdrop: true
            },
        ]
    });

    $('#shelp').click(function () {
        TourForSection.init();
        TourForSection.restart();
        $('#helpModal').modal('hide');
    });
    ////////////////////////////////////
    $("#helpbtn").click(function () {
        var url = window.location.href;
        var segments = url.split('/');
        //alert(segments[4]);
        if (segments[4] == "Dashboard" || segments[4] == "dashboard") {
            // $(".modal-title").text("Dashboard");
            $('#module-body-text').html('This module covers submission of ident from site team leading to a material requirement sheet based on stock availability.  The indent process is store specific.  The Material Requirement Sheet is used to release purchase orders to vendors.');
        }
        else if (segments[4] == "bids") {
            $('.modal-title').html('Bids');
            $('#module-body-text').html('This module covers submission of ident from site team leading to a material requirement sheet based on stock availability.  The indent process is store specific.  The Material Requirement Sheet is used to release purchase orders to vendors.');
        }
        else if (segments[4] == "forecast") {
            $('.modal-title').html('Forecast');
            $('#module-body-text').html('This module is largely about receiving material requirements from site, inventory control, incoming material management and equipment maintenance.  The activity is organized by equipment type and hence the specific store must be selected upfront before performing any actions in this module.');
        }
        else if (segments[4] == "BillofQuantities") {
            $('.modal-title').html('Bill of Quantities');
            $('#module-body-text').html('This module covers submission of ident from site team leading to a material requirement sheet based on stock availability.  The indent process is store specific.  The Material Requirement Sheet is used to release purchase orders to vendors.');
        }
        else if (segments[4] == "timesheet") {
            $('.modal-title').html('Timesheet');
            $('#module-body-text').html('This module is largely about receiving material requirements from site, inventory control, incoming material management and equipment maintenance.  The activity is organized by equipment type and hence the specific store must be selected upfront before performing any actions in this module.');
        }
        else if (segments[4] == "claims") {
            $('.modal-title').html('Claims');
            $('#module-body-text').html('This module covers submission of ident from site team leading to a material requirement sheet based on stock availability.  The indent process is store specific.  The Material Requirement Sheet is used to release purchase orders to vendors.');
        }
        else if (segments[4] == "exceptions") {
            $('.modal-title').html('Exceptions');
            $('#module-body-text').html('This module is largely about receiving material requirements from site, inventory control, incoming material management and equipment maintenance.  The activity is organized by equipment type and hence the specific store must be selected upfront before performing any actions in this module.');
        }
        else if (segments[4] == "claims") {
            $('.modal-title').html('Claims');
            $('#module-body-text').html('This module covers submission of ident from site team leading to a material requirement sheet based on stock availability.  The indent process is store specific.  The Material Requirement Sheet is used to release purchase orders to vendors.');
        }
        else if (segments[4] == "estimates") {
            $('.modal-title').html('Invoices');
            $('#module-body-text').html('This module is largely about receiving material requirements from site, inventory control, incoming material management and equipment maintenance.  The activity is organized by equipment type and hence the specific store must be selected upfront before performing any actions in this module.');
        }
        else if (segments[4] == "approval") {
            $('.modal-title').html('Payments');
            $('#module-body-text').html('This module is largely about receiving material requirements from site, inventory control, incoming material management and equipment maintenance.  The activity is organized by equipment type and hence the specific store must be selected upfront before performing any actions in this module.');
        }
        else if (segments[4] == "receivables") {
            $('.modal-title').html('Receivables');
            $('#module-body-text').html('This module covers submission of ident from site team leading to a material requirement sheet based on stock availability.  The indent process is store specific.  The Material Requirement Sheet is used to release purchase orders to vendors.');
        }
        else if (segments[4] == "roianalysis") {
            $('.modal-title').html('ROI Analysis');
            $('#module-body-text').html('This module is largely about receiving material requirements from site, inventory control, incoming material management and equipment maintenance.  The activity is organized by equipment type and hence the specific store must be selected upfront before performing any actions in this module.');
        }
        else if (segments[4] == "details") {
            $('.modal-title').html('Breakeven analysis');
            $('#module-body-text').html('This module is largely about receiving material requirements from site, inventory control, incoming material management and equipment maintenance.  The activity is organized by equipment type and hence the specific store must be selected upfront before performing any actions in this module.');
        }
        $("#helpModal").modal("show");
    });
    $('#phelp').click(function () {
        TourForPage.restart();
        TourForPage.init();
        $('#helpModal').modal('hide');
    });
});