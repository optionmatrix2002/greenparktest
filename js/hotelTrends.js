/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


AmCharts.checkEmptyData = function (chart, message) {
    if (0 == chart.dataProvider.length) {
        // set min/max on the value axis
        chart.valueAxes[0].minimum = 0;
        chart.valueAxes[0].maximum = 100;

        // add dummy data point
        var dataPoint = {
            dummyValue: 0
        };
        dataPoint[chart.categoryField] = '';
        chart.dataProvider = [dataPoint];

        // add label
        chart.addLabel(0, '50%', message, 'center');

        // set opacity of the chart div
        chart.chartDiv.style.opacity = 0.5;

        // redraw it
        chart.validateNow();
    }
}

var chart1 = AmCharts.makeChart("chartdiv",
    {
        "type": "serial",
        "categoryField": "category",
        "startDuration": 1,
        "categoryAxis": {
            "gridPosition": "start"
        },
        "autoResize": 'true',
        "trendLines": [],
        "graphs": [
            {
                "balloonText": "[[title]] of [[category]]:[[value]]",
                "bullet": "round",
                "id": "AmGraph-1",
                "title": "No of Tickets",
                "valueField": "ticket_count",
                "labelPosition": "bottom",
                "labelText": "[[value]]"
            }
        ],
        "guides": [],
        "valueAxes": [
            {
                "id": "ValueAxis-1",
                "stackType": "regular",
                "title": "No of Tickets"
            }
        ],
        "allLabels": [],
        "balloon": {},
        "legend": {
            "enabled": true,
            "useGraphSettings": true
        },
        "titles": [
            {
                "id": "Title-1",
                "size": 15,
                "text": "Tickets"
            }
        ],
        "dataProvider": hotelTicketData
    }
);

AmCharts.checkEmptyData(chart1, 'No tickets available');

var chart2 = AmCharts.makeChart("chartdiv1",
    {
        "type": "serial",
        "categoryField": "category",
        "startDuration": 1,
        "categoryAxis": {
            "gridPosition": "start"
        },
        "autoResize": 'true',
        "trendLines": [],
        "graphs": [
            {
                "balloonText": "[[title]] of [[category]]:[[value]]",
                "bullet": "round",
                "id": "AmGraph-1",
                "title": "No of Audits",
                "valueField": "audit_count",
                "labelPosition": "bottom",
                "labelText": "[[value]]"
            }
        ],
        "guides": [],
        "valueAxes": [
            {
                "id": "ValueAxis-1",
                "stackType": "regular",
                "title": "No of Audits"
            }
        ],
        "allLabels": [],
        "balloon": {},
        "legend": {
            "enabled": true,
            "useGraphSettings": true
        },
        "titles": [
            {
                "id": "Title-1",
                "size": 15,
                "text": "Audits"
            }
        ],
        "dataProvider": hotelAuditData
    }
);

AmCharts.checkEmptyData(chart2, 'No audits available');

var chart3 = AmCharts.makeChart("chartdiv2",
    {
        "type": "serial",
        "theme": "none",
        "pathToImages": "https://www.amcharts.com/lib/3/images/",
        "categoryField": "category",
        "startDuration": 1,
        "categoryAxis": {
            "gridPosition": "start"
        },
        "autoResize": 'true',
        "trendLines": [],
        "graphs": [
            {
                "balloonText": "[[title]] of [[category]]:[[value]]",
                "bullet": "round",
                "id": "AmGraph-1",
                "title": "No of Chronic Tickets",
                "valueField": "ticket_count",
                "labelPosition": "bottom",
                "labelText": "[[value]]"
            }
        ],
        "guides": [],
        "valueAxes": [
            {
                "id": "ValueAxis-1",
                "stackType": "regular",
                "title": "No of Chronic Tickets"
            }
        ],
        "allLabels": [],
        "balloon": {},
        "legend": {
            "enabled": true,
            "useGraphSettings": true
        },
        "titles": [
            {
                "id": "Title-1",
                "size": 15,
                "text": "Chronic Tickets"
            }
        ],
        "dataProvider": hotelChronicData
    }
);
AmCharts.checkEmptyData(chart3, 'No chronic tickets available');
var chart4 = AmCharts.makeChart("chartdiv3",
    {
        "type": "serial",
        "theme": "none",
        "pathToImages": "https://www.amcharts.com/lib/3/images/",
        "categoryField": "category",
        "startDuration": 1,
        "categoryAxis": {
            "gridPosition": "start"
        },
        "autoResize": 'true',
        "trendLines": [],
        "graphs": [
            {
                "balloonText": "[[title]] of [[category]]:[[value]]",
                "bullet": "round",
                "id": "AmGraph-1",
                "title": "No of Overdue Tickets",
                "valueField": "ticket_count",
                "labelPosition": "bottom",
                "labelText": "[[value]]"
            }
        ],
        "guides": [],
        "valueAxes": [
            {
                "id": "ValueAxis-1",
                "stackType": "regular",
                "title": "No of Overdue Tickets"
            }
        ],
        "allLabels": [],
        "balloon": {},
        "legend": {
            "enabled": true,
            "useGraphSettings": true
        },
        "titles": [
            {
                "id": "Title-1",
                "size": 15,
                "text": "Overdue Tickets"
            }
        ],
        "dataProvider": hotelOverdueTicketData
    }
);
AmCharts.checkEmptyData(chart4, 'No overdue tickets available');

/*var chart5 =   AmCharts.makeChart("chartdiv4",
    {
        "type": "serial",
        "theme": "none",
        "pathToImages": "https://www.amcharts.com/lib/3/images/",
        "categoryField": "category",
        "startDuration": 1,
        "categoryAxis": {
            "gridPosition": "start"
        },
		"autoResize":'true',
        "trendLines": [],
        "graphs": [
            {
                "balloonText": "[[title]] of [[category]]:[[value]]",
                "bullet": "round",
                "id": "AmGraph-1",
                "title": "Average Audit Score(%)",
                "valueField": "percentage",
                "labelPosition": "bottom",
                "labelText": "[[value]]"
            }
        ],
        "guides": [],
        "valueAxes": [
            {
                "id": "ValueAxis-1",
                "stackType": "regular",
                "title": "Average Audit Score(%)"
            }
        ],
        "allLabels": [],
        "balloon": {},
        "legend": {
            "enabled": true,
            "useGraphSettings": true
        },
        "titles": [
            {
                "id": "Title-1",
                "size": 15,
                "text": "Average Audit Score(%)"
            }
        ],
        "dataProvider": hotelAuditDataprovider,
        "graphs": hotelAuditDataGraph
    }
);
AmCharts.checkEmptyData(chart5,'No average audit score available');*/


var chart5 = AmCharts.makeChart("chartdiv4", {
    "type": "serial",
    "theme": "light",
    "legend": {
        "useGraphSettings": true
    },
    "autoResize": 'true',
    "titles": [
        {
            "id": "Title-1",
            "size": 15,
            "text": "Average Audit Score(%)"
        }
    ],
    "dataProvider": hotelAuditDataprovider,
    "valueAxes": [{
        "integersOnly": true,
        // "maximum": 1000,
        //  "minimum": 10,
        "reversed": false,
        "axisAlpha": 0,
        "dashLength": 5,
        "gridCount": 10,
        "position": "left",
        "stackType": "regular",
        "title": "Average Audit Score(%)"
    }],
    "startDuration": 0.5,
    "graphs": hotelAuditDataGraph,
    "chartCursor": {
        "cursorAlpha": 0,
        "zoomable": false
    },
    "categoryField": "year",
    "categoryAxis": {
        "gridPosition": "start",
        "axisAlpha": 0,
        "fillAlpha": 0.05,
        "fillColor": "#000000",
        "gridAlpha": 0,
        "position": "bottom"
    },
    "export": {
        "enabled": true,
        "position": "right"
    }
});

AmCharts.checkEmptyData(chart5, 'No average audit score available');


/*$('.date-picker1').datepicker({
   
    format: "mm-yyyy",
    viewMode: "months",
    minViewMode: "months",
    onClose: function (dateText, inst) {
        $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
    }
});*/

