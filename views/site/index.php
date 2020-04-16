<?php
/* @var $this View */

/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
View::registerCssFile(yii::$app->urlManager->createUrl('css/dashboard.css'));
View::registerCssFile(yii::$app->urlManager->createUrl('css/bootstrap-datepicker.css'));

View::registerJsFile("https://www.amcharts.com/lib/3/amcharts.js", ['depends' => JqueryAsset::className()]);
View::registerJsFile("https://www.amcharts.com/lib/3/serial.js", ['depends' => JqueryAsset::className()]);
View::registerJsFile("https://www.amcharts.com/lib/3/pie.js", ['depends' => JqueryAsset::className()]);

View::registerJsFile(yii::$app->urlManager->createUrl('js/bootstrap-datepicker.min.js'), ['depends' => JqueryAsset::className()]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/dashboard.js'), ['depends' => JqueryAsset::className()]);

$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuDashboard").addClass("active");
', \yii\web\View::POS_END);



?>


<div class="container-fluid">
    <h2>Dashboard</h2>
</div>
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
    <p id="statistics-text">
        This screen provides the summary of audit activity across locations.
    </p>
</div>
<div class="" style="margin-top: 10px;">
    <div class="col-lg-12 col-md-12">
        <div class="row" id="dashboard-tabs">
            <ul class="nav nav-tabs" id="myTab">
                <li class="active"><a class="tabs" data-toggle="tab" href="#statistics"><i class="fa fa-th-list"
                                                                                           aria-hidden="true"></i>&nbsp;Statistics</a>
                </li>
                <li><a class="tabs" data-toggle="tab" href="#quickview"><i class="fa fa-eye" aria-hidden="true"></i>&nbsp;Quick
                        View</a></li>
                <li><a class="tabs" data-toggle="tab" href="#distribution"><i class="fa fa-pie-chart"
                                                                              aria-hidden="true"></i>&nbsp;Ticket
                        Distribution</a></li>
                <li><a class="tabs" data-toggle="tab" href="#trends"><i class="fa fa-line-chart"
                                                                        aria-hidden="true"></i>&nbsp;Hotel
                        Trends</a></li>
                <li><a class="tabs" data-toggle="tab" href="#trends2"><i class="fa fa-line-chart"
                                                                         aria-hidden="true"></i>&nbsp;Department
                        Trends</a></li>
                <li><a class="tabs" data-toggle="tab" href="#ranking"><i class="fa fa-bar-chart"
                                                                         aria-hidden="true"></i>&nbsp;Ranking Table</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="statistics" class="tab-pane fade in active">
                    <?= $this->render('statistics', [
                        'countAudits' => $countAudits,
                        'overdueAudits' => $overdueAudits,
                        'upcomingAudits' => $upcomingAudits,
                        'auditModel' => $auditModel
                    ]); ?>
                </div>
                <div id="quickview" class="tab-pane fade">
                    <?= $this->render('quickView', ['model' => $searchAuditModel]); ?>
                </div>
                <div id="distribution" class="tab-pane fade">
                    <?= $this->render('ticketDistribution', [
                        'ticketLocationData' => $ticketLocationData,
                        'ticketHotelData' => $ticketHotelData,
                        'ticketDepartmentData' => $ticketDepartmentData,
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                    ]); ?>
                </div>
                <div id="trends" class="tab-pane fade">
                    <?= $this->render('hotelTrends', [
                        'hotelAuditData' => $hotelAuditData,
                        'hotelTicketData' => $hotelTicketData,
                        'hotelChronicData' => $hotelChronicData,
                        'hotelOverdueTicketData' => $hotelOverdueTicketData,
                        'hotelAvgData' => $hotelAvgData,
                        'hotelAvgData1' => $hotelAvgData1
                    ]); ?>
                </div>
                <div id="trends2" class="tab-pane fade">
                    <?= $this->render('departmentTrends', ['deptAuditAvgData' => $deptAuditAvgData,
                        'deptAuditData' => $deptAuditData,
                        'deptAuditOverdueData' => $deptAuditOverdueData,
                        'deptAuditChronicData' => $deptAuditChronicData,
                        'deptAuditTicketData' => $deptAuditTicketData]); ?>
                </div>
                <div id="ranking" class="tab-pane fade">
                    <?= $this->render('rankingTable', ['dataProviderRankAuditsSchedules' => $dataProviderRankAuditsSchedules, 'searchAuditModel' => $searchAuditModel]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$dashboard_url = yii::$app->urlManager->createUrl('site/dashboard');

$this->registerJs("
 $('#dashboard-tabs ul li a').click(function () {
 removeQString('');
 location.hash = $(this).attr('href');
 });
 
 function removeQString(key) {
	var urlValue=document.location.href;
	
	//Get query string value
	var searchUrl=location.search;
	
	if(key!=\"\") {
		oldValue = getParameterByName(key);
		removeVal=key+\"=\"+oldValue;
		if(searchUrl.indexOf('?'+removeVal+'&')!= \"-1\") {
			urlValue=urlValue.replace('?'+removeVal+'&','?');
		}
		else if(searchUrl.indexOf('&'+removeVal+'&')!= \"-1\") {
			urlValue=urlValue.replace('&'+removeVal+'&','&');
		}
		else if(searchUrl.indexOf('?'+removeVal)!= \"-1\") {
			urlValue=urlValue.replace('?'+removeVal,'');
		}
		else if(searchUrl.indexOf('&'+removeVal)!= \"-1\") {
			urlValue=urlValue.replace('&'+removeVal,'');
		}
	}
	else {
		var searchUrl=location.search;
		urlValue=urlValue.replace(searchUrl,'');
	}
	history.pushState({state:1, rand: Math.random()}, '', urlValue);
}
");

?>
<script>
    //Tabs redirection in a page
    /*$(document).ready(function () {

        $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
            var sst = localStorage.setItem('activeTab', $(e.target).attr('href'));
        });
        var activeTab = localStorage.getItem('activeTab');
        if (activeTab) {
            $('#myTab a[href="' + activeTab + '"]').tab('show');
        }
    });*/
</script>
