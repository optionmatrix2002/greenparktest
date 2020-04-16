/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

AmCharts.addInitHandler(function(chart) {

    // check if data is mepty
    if (chart.dataProvider === undefined || chart.dataProvider.length === 0) {
        // add some bogus data


        var dp = {};
        dp[chart.titleField] = "";
        dp[chart.valueField] = 0;
        chart.dataProvider.push(dp)

        // disable slice labels
       // chart.labelsEnabled = false;

        // add label to let users know the chart is empty
        chart.balloonText = "";
        chart.addLabel("50%", "50%", "No tickets available", "middle", 15);

        // dim the whole chart
       // chart.alpha = 0.3;
    }

}, ["pie"]);

var chart = AmCharts.makeChart("chartdivpie1", {
    "type": "pie",
    "theme": "light",
    "labelRadius": -25,
    "labelText": "[[value]]",
    "legend": {
        "markerType": "circle",
        "position": "right",
        "valueText": '',
        "font-size": "10"
    },
    "dataProvider": ticketLocData,
    "valueField": "ticket_count",
    "titleField": "name",
    "colorField": "color",
    "colors" :['#8aa830','#4677af','#F08080','#FFFACD','#FFD700','#FFFFE0'],
    "labelColorField": "fcolor",
    "depth3D": 8,
});
var chart = AmCharts.makeChart("chartdivpie2", {
    "type": "pie",
    "labelRadius": -25,
    "labelText": "[[value]]",
    "legend": {
        "markerType": "circle",
        "position": "right",
        "valueText": '',
        "font-size": "10"
    },
    "dataProvider": ticketHotelData,
    "valueField": "ticket_count",
    "titleField": "hotel_name",
    "colorField": "color",
    "colors" :['#8aa830','#4677af','#F08080','#FFFACD','#FFD700','#FFFFE0'],
    "labelColorField": "fcolor",
    "depth3D": 8,
});
var chart = AmCharts.makeChart("chartdivpie3", {
    "type": "pie",
    "labelRadius": -25,
    "labelText": "[[value]]",
    "legend": {
        "markerType": "circle",
        "position": "right",
        "valueText": '',
        "font-size": "10"
    },
    "dataProvider": ticketDeptData,
    "valueField": "ticket_count",
    "titleField": "department_name",
    "colorField": "color",
    "colors" :['#8aa830','#4677af','#F08080','#FFFACD','#FFD700','#FFFFE0'],
    "labelColorField": "fcolor",
    "depth3D": 8,
});


