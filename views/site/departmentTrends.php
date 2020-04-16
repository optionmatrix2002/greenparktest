<?php
/* @var $this View */

/* @var $content string */

use app\assets\AppAsset;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

AppAsset::register($this);
View::registerJsFile(yii::$app->urlManager->createUrl('js/departmentTrends.js'), ['depends' => JqueryAsset::className()]);
$this->registerJs("var AuditAvgData = " . json_encode($deptAuditAvgData['data']) . ";", View::POS_HEAD);
$this->registerJs("var AuditAvgDataB = " . json_encode($deptAuditAvgData['ballon']) . ";", View::POS_HEAD);
$this->registerJs("var AuditData = " . json_encode($deptAuditData['data']) . ";", View::POS_HEAD);
$this->registerJs("var AuditDataB = " . json_encode($deptAuditData['ballon']) . ";", View::POS_HEAD);
$this->registerJs("var AuditOverdueData = " . json_encode($deptAuditOverdueData['data']) . ";", View::POS_HEAD);
$this->registerJs("var AuditOerdueDataB = " . json_encode($deptAuditOverdueData['ballon']) . ";", View::POS_HEAD);
$this->registerJs("var AuditChrData = " . json_encode($deptAuditChronicData['data']) . ";", View::POS_HEAD);
$this->registerJs("var AuditChrDataB = " . json_encode($deptAuditChronicData['ballon']) . ";", View::POS_HEAD);
$this->registerJs("var AuditTikData = " . json_encode($deptAuditTicketData['data']) . ";", View::POS_HEAD);
$this->registerJs("var AuditTikDataB = " . json_encode($deptAuditTicketData['ballon']) . ";", View::POS_HEAD);

$this->registerJs('
 $(".clear-all-btn-trends2").click(function()
 { 
    window.location.href="dashboard#trends2";
 });
');
$this->registerJs('
        $("#departmentStartDate,#departmentendDate").datetimepicker({
            format: "MM-YYYY",
            viewMode: "months",
         });
            $("#departmentStartDate").datetimepicker().on("dp.change", function (e) {
                var incrementDay = new Date(e.date);
                var maxDate = incrementDay;
                var endDateMoment = moment(maxDate);
                endDateMoment.add(12, "months");
                $("#departmentendDate").data("DateTimePicker").minDate(incrementDay);
                $("#departmentendDate").data("DateTimePicker").maxDate(endDateMoment);
                $("#departmentendDate").val("");
                $(this).data("DateTimePicker").hide();
            });
            var startDate = $("#departmentStartDate").val();
            if(startDate){
            var date = $("#departmentStartDate").val();
              var incrementDay = new Date(01+"-"+startDate);
                
                var endDateMoment = moment(incrementDay);
                endDateMoment.add(12, "months");
                $("#departmentendDate").data("DateTimePicker").minDate(incrementDay);
                $("#departmentendDate").data("DateTimePicker").maxDate(endDateMoment);
 
            }
  
    ', \yii\web\View::POS_END);
?>

<div class="col-md-12 col-sm-12 col-lg-12 showfilter">
    <form id="department-trends" action="dashboard#trends2" method="get">
        <div class="col-lg-3 col-sm-3 col-md-3">
            <?php

            if (Yii::$app->user && Yii::$app->user->identity->user_type != 1) {
                $return = \app\models\User::getUserAssingemnts();
                $userHotels = $return['userHotels'];
                $dimensionTypes = ArrayHelper::map(\app\models\Hotels::find()->andFilterWhere(['hotel_id' => $userHotels, 'hotel_status' => 1, 'is_deleted' => 0])->orderBy('hotel_name')->all(), 'hotel_id', 'hotel_name');
            } else {
                $dimensionTypes = ArrayHelper::map(\app\models\Hotels::find()->andFilterWhere(['hotel_status' => 1, 'is_deleted' => 0])->orderBy('hotel_name')->all(), 'hotel_id', 'hotel_name');
            }

            ?>
            <?= Html::dropDownList('department_hotel_id', null, $dimensionTypes, array(
                'class' => 'form-control', 'prompt' => 'Hotel', 'id' => 'department_hotel_id', 'options' => array(isset($_GET['department_hotel_id']) ? $_GET['department_hotel_id'] : '' => array('selected' => true)))) ?>
        </div>
        <div class="col-lg-3 col-sm-3 col-md-3  ">
            <input name="departmentStartDate" id="departmentStartDate" class="form-control hasDatepicker"
                   placeholder="From Month"
                   value="<?php echo isset($_GET['departmentStartDate']) ? $_GET['departmentStartDate'] : '' ?>">
        </div>

        <div class="col-lg-3 col-sm-3 col-md-3 ">
            <input name="departmentendDate" id="departmentendDate" class="form-control hasDatepicker"
                   placeholder="To Month"
                   value="<?php echo isset($_GET['departmentendDate']) ? $_GET['departmentendDate'] : '' ?>">
        </div>
        <!--
        <div class="input-group">
            <button class="btn btn-success form-control searchBtn" style="color:white;" >GO</button>
        </div>
        -->

        <div class="col-lg-3 col-md-3 col-sm-3 pull-right text-right">
            <?= Html::submitButton('Go', ['class' => 'btn btn-success']) ?>

            <?= Html::a('Clear', Yii::$app->urlManager->createUrl(['/site/dashboard#trends2']), ['class' => 'btn btn-default clear-all-btn-trends2']) ?>


        </div>

    </form>
</div>
<div class="col-md-12">
    <div id="chartdivtrends21" style="width: 100%; height: 400px; background-color: #FFFFFF;"></div>
</div>
<div class="col-md-12">
    <div id="chartdivtrends22" style="width: 100%; height: 400px; background-color: #FFFFFF;"></div>
</div>

<div class="col-md-12">
    <div id="chartdivtrends23" style="width: 100%; height: 400px; background-color: #FFFFFF;"></div>
</div>
<div class="col-md-12">
    <div id="chartdivtrends24" style="width: 100%; height: 400px; background-color: #FFFFFF;"></div>
</div>
<div class="col-md-12">
    <div id="chartdivtrends25" style="width: 100%; height: 400px; background-color: #FFFFFF;"></div>
</div>