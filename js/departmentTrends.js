/* 
* To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/


AmCharts.checkEmptyData = function(chart,message) {
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

var chart1 = AmCharts.makeChart("chartdivtrends21", {
    "type": "serial",
    "theme": "light",
    "legend": {
        "useGraphSettings": true
    },
	"autoResize":'true',
    "titles": [
        {
            "id": "Title-1",
            "size": 15,
            "text": "Audits"
        }
    ],
    "dataProvider": AuditData,
    "valueAxes": [{
        "integersOnly": true,
       // "maximum": 1000,
      //  "minimum": 10,
        "reversed": false,
        "axisAlpha": 0,
        "dashLength": 5,
        "gridCount": 10,
        "position": "left",
        "title": "No of Audits"
    }],
    "startDuration": 0.5,
    "graphs": AuditDataB,
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

AmCharts.checkEmptyData(chart1,'No audits available');

var chart2 = AmCharts.makeChart("chartdivtrends22", {
    "type": "serial",
    "theme": "light",
    "legend": {
        "useGraphSettings": true
    },
	"autoResize":'true',

    "dataProvider": AuditTikData,
    "valueAxes": [{
        "integersOnly": true,
       // "maximum": 1000,
       // "minimum": 10,
        "reversed": false,
        "axisAlpha": 0,
        "dashLength": 5,
        "gridCount": 10,
        "position": "left",
        "title": "No of Tickets"
    }],
    "startDuration": 0.5,
    "graphs": AuditTikDataB,
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

AmCharts.checkEmptyData(chart2,'No tickets available');

var chart3 = AmCharts.makeChart("chartdivtrends23", {
    "type": "serial",
    "theme": "light",
    "legend": {
        "useGraphSettings": true
    },
	"autoResize":'true',
    "titles": [
        {
            "id": "Title-1",
            "size": 15,
            "text": "Chronic Tickets"
        }
    ],
    "dataProvider": AuditChrData,
    "valueAxes": [{
        "integersOnly": true,
       // "maximum": 100,
       // "minimum": 10,
        "reversed": false,
        "axisAlpha": 0,
        "dashLength": 5,
        "gridCount": 10,
        "position": "left",
        "title": "No of Chronic Tickets"
    }],
    "startDuration": 0.5,
    "graphs": AuditChrDataB,
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

AmCharts.checkEmptyData(chart3,'No chronic tickets available');

var chart4 = AmCharts.makeChart("chartdivtrends24", {
    "type": "serial",
    "theme": "light",
    "legend": {
        "useGraphSettings": true
    },
	"autoResize":'true',
    "titles": [
        {
            "id": "Title-1",
            "size": 15,
            "text": "Overdue Tickets"
        }
    ],
    "dataProvider": AuditOverdueData,
    "valueAxes": [{
        "integersOnly": true,
       // "maximum": 100,
      //  "minimum": 10,
        "reversed": false,
        "axisAlpha": 0,
        "dashLength": 5,
        "gridCount": 10,
        "position": "left",
        "title": "No of Overdue Tickets"
    }],
    "startDuration": 0.5,
    "graphs": AuditOerdueDataB,
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

AmCharts.checkEmptyData(chart4,'No overdue tickets available');

var chart5 = AmCharts.makeChart("chartdivtrends25", {
    "type": "serial",
    "theme": "light",
    "legend": {
        "useGraphSettings": true
    },
	"autoResize":'true',
    "titles": [
        {
            "id": "Title-1",
            "size": 15,
            "text": "Average Audit Score(%)"
        }
    ],
    "dataProvider": AuditAvgData,
    "valueAxes": [{
        "integersOnly": true,
       // "maximum": 100,
       // "minimum": 10,
        "reversed": false,
        "axisAlpha": 0,
        "dashLength": 5,
        "gridCount": 10,
        "position": "left",
        "title": "Audits Score in %"
    }],
    "startDuration": 0.5,
    "graphs": AuditAvgDataB,
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

AmCharts.checkEmptyData(chart5,'No audit score available');

$('.date-picker1').datepicker({

    format: "mm-yyyy",
    viewMode: "months",
    minViewMode: "months",
    onClose: function (dateText, inst) {
        $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
    }
});


