<?php
/* @var $this View */

/* @var $content string */

use app\assets\AppAsset;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;


AppAsset::register($this);
View::registerJsFile(yii::$app->urlManager->createUrl('js/ticketdistribution.js'), ['depends' => JqueryAsset::className()]);
$this->registerJs("var ticketLocData = " . json_encode($ticketLocationData) . ";", View::POS_HEAD);
$this->registerJs("var ticketHotelData = " . json_encode($ticketHotelData) . ";", View::POS_HEAD);
$this->registerJs("var ticketDeptData = " . json_encode($ticketDepartmentData) . ";", View::POS_HEAD);

$this->registerJs('
 $(".clear-all-btn-distribution").click(function()
 {
      window.location.href="dashboard#distribution";
 });
');

$this->registerJs('
    var startdate = $("#ticektsStartDate").val();
    if ((startdate == "")) {
        var startdate = $("#ticektsStartDate").val();
        $("#ticektsStartDate,#ticketsEndDate").datetimepicker({
            format: "DD-MM-YYYY",
         });
            $("#ticektsStartDate").datetimepicker().on("dp.change", function (e) {
    
                var incrementDay = moment(new Date(e.date));
                incrementDay.add(1, "days");
                $("#ticketsEndDate").data("DateTimePicker").minDate(incrementDay);
                $("#ticketsEndDate").val("");
                $(this).data("DateTimePicker").hide();
            });
    
    }
    ', \yii\web\View::POS_END);
?>

    <form id="distriution" action="dashboard#distribution" method="get">
        <div class="col-md-12 col-sm-12 col-lg-12 showfilter">
            <div class="col-md-3 col-sm-3 col-lg-3 ">
                <?php
                $result = \app\models\Tickets::getTicketUsers();
                $usersIds = ArrayHelper::getColumn($result, 'assigned_user_id');
                ?>
                <?php $dimensionTypes = [];
                if ($usersIds) {
                    $dimensionTypes = ArrayHelper::map(\app\models\User::find()->andFilterWhere(['user_id' => $usersIds, 'is_active' => 1, 'user_type' => '3', 'is_deleted' => 0])->orderBy('user_id')->all(), 'user_id', function ($element) {
                        return $element['first_name'] . ' ' . $element['last_name'];
                    });
                }
                ?>
                <?= Html::dropDownList('ticket_auditor_id', null, $dimensionTypes, array(
                    'class' => 'form-control', 'prompt' => 'Staff User', 'id' => 'ticket_auditor_id', 'options' => array(isset($_GET['ticket_auditor_id']) ? $_GET['ticket_auditor_id'] : '' => array('selected' => true)))) ?>
            </div>
            <div class="col-md-3 col-sm-3 col-lg-3 ">
                <?= Html::dropDownList('ticket_status', null, array('1' => 'Assigned', '2' => 'Resolved', '3' => 'Closed', '4' => 'Rejected'), array('class' => 'form-control', 'prompt' => 'Status', 'id' => 'ticket_status', 'options' => array(isset($_GET['ticket_status']) ? $_GET['ticket_status'] : '' => array('selected' => true)))) ?>
            </div>
            <div class="col-md-3 col-sm-3 col-lg-3 ">
                <?= Html::dropDownList('ticket_chronic', null, array('1' => 'Yes', '0' => 'No'), array('class' => 'form-control', 'prompt' => 'Chronicity', 'id' => 'ticket_chronic', 'options' => array(isset($_GET['ticket_chronic']) ? $_GET['ticket_chronic'] : '' => array('selected' => true)))) ?>
            </div>
            <div class="col-md-3 col-sm-3 col-lg-3">
                <input name="ticektsStartDate" id="ticektsStartDate" class="form-control datetimepicker hasDatepicker"
                       placeholder="From Date"
                       value="<?php echo isset($_GET['ticektsStartDate']) ? $_GET['ticektsStartDate'] : '' ?>">
            </div>
            <div class="col-md-3 col-sm-3 col-lg-3 margin-top-5">
                <input name="ticketsEndDate" id="ticketsEndDate" class="form-control datetimepicker hasDatepicker"
                       placeholder="To Date"
                       value="<?php echo isset($_GET['ticketsEndDate']) ? $_GET['ticketsEndDate'] : '' ?>">
            </div>
            <!--  <div class=" col-md-1 input-group">
            <button class="btn btn-success form-control" type="submit" style="color:white;" >GO
            </button>
            <?= Html::a('Clear', Yii::$app->urlManager->createUrl(['/site/dashboard']), ['class' => 'btn btn-default']) ?>
        </div> -->

            <div class="col-lg-3 col-md-3 col-sm-3 pull-right text-right margin-top-5">
                <?= Html::submitButton('Go', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Clear', Yii::$app->urlManager->createUrl(['/site/dashboard#distribution']), ['class' => 'btn btn-default clear-all-btn-distribution']) ?>

            </div>
        </div>
    </form>

    <div class="row">


    </div>
    <div class="col-lg-4 col-xs-12 col-md-4 text-center">
        <div style="border-top: 3px solid red;padding: 5px;">
            <h4 class="headstyle">Locations</h4>
            <div id="chartdivpie1" style="width: 100%; height: 300px; background-color: #FFFFFF;"></div>
        </div>
    </div>
    <div class="col-lg-4 col-xs-12 col-md-4 text-center">
        <div style="border-top: 3px solid red;padding: 5px;">
            <h4 class="headstyle">Hotels</h4>
            <div id="chartdivpie2" style="width: 100%; height: 300px; background-color: #FFFFFF;"></div>
        </div>
    </div>
    <div class="col-lg-4 col-xs-12 col-md-4 text-center">
        <div style="border-top: 3px solid red;padding: 5px;">
            <h4 class="headstyle">Departments</h4>
            <div id="chartdivpie3" style="width: 100%; height: 300px; background-color: #FFFFFF;"></div>
        </div>
    </div>

    <div class="col-md-12 nopadding">
        <h4>Recent Tickets</h4>

        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{items}',
            'columns' => [
                [
                    'attribute' => 'name',
                    'header' => 'Ticket ID',
                    'value' => function ($model) {
                        return '<a href="' . yii::$app->urlManager->createUrl('tickets/reports?id=' . Yii::$app->utils->encryptData($model->ticket_id)) . '" title="View Tickets" >' . $model->ticket_name . '</a>';

                    },
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'theadcolor']
                ],
                [
                    'attribute' => 'hotel_id',
                    'header' => 'Hotel Name',
                    'value' => function ($model) {
                        return ($model->hotel_id) ? $model->hotel->hotel_name : '--';
                    },
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'theadcolor']
                ],
                [
                    'attribute' => 'department_id',
                    'header' => 'Department',
                    'value' => function ($model) {
                        return ($model->department_id) ? $model->department->department_name : '--';
                    },
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'theadcolor']
                ],
                [
                    'attribute' => 'priority_type_id',
                    'header' => 'Priority',
                    'value' => function ($model) {
                        return $model->priorityType->priority_name;
                    },
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'theadcolor']
                ],
                [
                    'attribute' => 'status',
                    'header' => 'Status',
                    'value' => function ($model) {
                        switch ($model->status) {
                            case 0:
                                $status = 'Open';
                                break;
                            case 1:
                                $status = 'Assigned';
                                break;
                            case 2:
                                $status = 'Resolved';
                                break;
                            case 3:
                                $status = 'Closed';
                                break;
                            case 4:
                                $status = 'Rejected';
                                break;
                            case 5:
                                $status = 'Cancelled';
                                break;
                        }
                        return $status;
                    },
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'theadcolor']
                ],
                [
                    'attribute' => 'assigned_id',
                    'header' => 'Assigned To',
                    'value' => function ($model) {
                        return ucfirst($model->assignedUser->first_name) . ' ' . ucfirst($model->assignedUser->last_name);
                    },
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'theadcolor']
                ],


                [
                    'attribute' => 'overdue',
                    'header' => 'Overdue',
                    'value' => function ($model) {
                        if (strtotime($model->due_date) < strtotime(date('Y-m-d'))) {
                            $is_overdue = 'Yes';
                        } else {
                            $is_overdue = 'No';
                        }
                        return $is_overdue;
                    },
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'theadcolor']
                ],
                [
                    'attribute' => 'due_date',
                    'header' => 'Due Date',
                    'value' => function ($model) {
                        $timestamp = strtotime($model->due_date);
                        return Yii::$app->formatter->asDate($timestamp, 'php:d-m-Y');
                    },
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'theadcolor']
                ],
                [
                    'attribute' => 'chronic',
                    'header' => 'Chronic',
                    'value' => function ($model) {
                        switch ($model->chronicity) {
                            case 0:
                                $chronicity = 'No';
                                break;
                            case 1:
                                $chronicity = 'Yes';
                                break;
                        }
                        return $chronicity;
                    },
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'theadcolor']
                ],


            ],
        ]);
        ?>
        <?php Pjax::end(); ?>
    </div>
<?php
$this->registerJs("   
    $('.clear-all-btn').click(function() {
    location.reload();
});");

?>