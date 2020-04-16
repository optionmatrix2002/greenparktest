
<?php
/* @var $this View */
/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\View;
?>

<div class="col-md-12 col-sm-12 col-lg-12 showfilter">
    <form id="w1" action="dashboard#trends" method="get">
        <div class="col-lg-3 col-sm-3 col-md-3 ">
            <input name="hotelStartDate" id="hotelStartDate" class="form-control date-picker1 hasDatepicker" placeholder="From Month" value="<?php echo isset($_GET['hotelStartDate'])?$_GET['hotelStartDate']:''?>">
        </div>

        <div class="col-lg-3 col-sm-3 col-md-3">
            <input name="hotelendDate" id="hotelendDate" class="form-control date-picker1 hasDatepicker" placeholder="To Month" value="<?php echo isset($_GET['hotelendDate'])?$_GET['hotelendDate']:''?>">
        </div>
        <!--   <div class="col-md-2  form-group ">
        <button class="btn btn-success  searchBtn" style="color:white;" >GO</button>

    </div>
     <div class="col-md-2 form-group ">
      <?= Html::a('Clear', Yii::$app->urlManager->createUrl(['/site/dashboard#trends']), ['class' => 'btn btn-default clear-all-btn']) ?>
      </div>

    -->
        <div class="col-lg-3 col-md-3 col-sm-3 pull-right text-right">
            <?= Html::submitButton('Go', ['class' => 'btn btn-success searchBtn']) ?>
            <?= Html::a('Clear', Yii::$app->urlManager->createUrl(['/site/dashboard#trends']), ['class' => 'btn btn-default clear-all-btn']) ?>

        </div>
    </form>
</div>
<?php
AppAsset::register($this);
View::registerJsFile(yii::$app->urlManager->createUrl('js/hotelTrends.js'), ['depends' => JqueryAsset::className()]);
$this->registerJs("var hotelAuditData = ". json_encode($hotelAuditData).";",View::POS_HEAD);
$this->registerJs("var hotelAuditDataprovider = ". json_encode($hotelAvgData1['data']).";",View::POS_HEAD);
$this->registerJs("var hotelAuditDataGraph = ". json_encode($hotelAvgData1['ballon']).";",View::POS_HEAD);
$this->registerJs("var hotelTicketData = ". json_encode($hotelTicketData).";",View::POS_HEAD);
$this->registerJs("var hotelChronicData = ". json_encode($hotelChronicData).";",View::POS_HEAD);
$this->registerJs("var hotelOverdueTicketData = ". json_encode($hotelOverdueTicketData).";",View::POS_HEAD);
$this->registerJs("var hotelAvgData = ". json_encode($hotelAvgData).";",View::POS_HEAD);

$this->registerJs('
 $(".clear-all-btn").click(function()
 { 
    
      window.location.href="dashboard#trends";
 });
');
?>

<div class="row">

</div>
<div id="main-hotel-content">
    <div class="col-md-12">
        <div id="chartdiv1" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
    </div>
    <div class="col-md-12">
        <div id="chartdiv" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
    </div>
    <div class="col-md-12">
        <div id="chartdiv2" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
    </div>
    <div class="col-md-12">
        <div id="chartdiv3" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
    </div>
    <div class="col-md-12">
        <div id="chartdiv4" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
    </div>
</div>

