<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;



$this->registerJs('
    $("#scrollable_table").on("scroll", function() {
        $("thead", this).css("transform", "translateY("+ this.scrollTop +"px)");
    });

    var startdate = $("#statisticsFromDate").val();
    if ((startdate == "")) {
        var startdate = $("#statisticsFromDate").val();
        $("#statisticsFromDate,#statisticsToDate").datetimepicker({
            // useCurrent : false,
            format: "DD-MM-YYYY",
            //minDate : moment()
        });
            $("#statisticsFromDate").datetimepicker().on("dp.change", function (e) {
                
                var incrementDay = moment(new Date(e.date));
                incrementDay.add(1, "days");
                $("#statisticsToDate").data("DateTimePicker").minDate(incrementDay);
                $("#statisticsToDate").val("");
                $(this).data("DateTimePicker").hide();
            });
                
    }
    ', \yii\web\View::POS_END);
?>

<form id="statics" action="dashboard#statistics" method="get">
    <div class="col-md-12 col-lg-12 col-sm-12 showfilter">
        <div class="col-md-3 col-lg-3  col-sm-3 form-group ">
            <?php

            if(Yii::$app->user && Yii::$app->user->identity->user_type !=1){
                $return = \app\models\User::getUserAssingemnts();
                $userHotels = $return['userHotels'];
                $dimensionTypes = ArrayHelper::map(\app\models\Hotels::find()->andFilterWhere(['hotel_id'=>$userHotels,'hotel_status' => 1, 'is_deleted' => 0])->orderBy('hotel_name')->all(), 'hotel_id', 'hotel_name');
            }else{
                $dimensionTypes = ArrayHelper::map(\app\models\Hotels::find()->andFilterWhere(['hotel_status' => 1, 'is_deleted' => 0])->orderBy('hotel_name')->all(), 'hotel_id', 'hotel_name');
            }



            ?>
            <?= Html::dropDownList('statistics_hotel_id', null, $dimensionTypes, array(
                'class' => 'form-control', 'prompt' => 'Hotel', 'id' => 'statistics_hotel_id',
                'options' => array(isset($_GET['statistics_hotel_id']) ? $_GET['statistics_hotel_id'] : '' => array('selected' => true)),
                'onchange' => '
                    $.post( "' . Yii::$app->urlManager->createUrl('site/departments?id=') . '"+$(this).val(), function( data ) {
                      $( "select#statistics_department_id" ).html( data );
                    });
                    
                ')) ?>
        </div>
        <div class="col-md-3 col-lg-3  col-sm-3 form-group ">
            <?php
            $options = [];
            if (isset($_GET['statistics_hotel_id']) && $_GET['statistics_hotel_id']) {
                $posts = \app\models\Departments::find()->select([
                    'tbl_gp_departments.department_id',
                    'tbl_gp_departments.department_name'
                ])
                    ->where([
                        'tbl_gp_audits.hotel_id' => $_GET['statistics_hotel_id']
                    ])
                    ->join('LEFT JOIN', 'tbl_gp_audits', 'tbl_gp_audits.department_id = tbl_gp_departments.department_id')
                    ->orderBy('tbl_gp_departments.department_id DESC')
                    ->all();

                $options = ArrayHelper::map($posts, 'department_id', 'department_name');
            }

            ?>

            <?= Html::dropDownList('statistics_department_id', null, $options, array('class' => 'form-control', 'prompt' => ' Department', 'id' => 'statistics_department_id', 'options' => array(isset($_GET['statistics_department_id']) ? $_GET['statistics_department_id'] : '' => array('selected' => true)),)) ?>
        </div>
        <div class="col-md-3 col-lg-3  col-sm-3 form-group ">
            <input name="statisticsStartDate" id="statisticsFromDate" class="form-control datetimepicker hasDatepicker"
                   placeholder="From Date"
                   value="<?php echo isset($_GET['statisticsStartDate']) ? $_GET['statisticsStartDate'] : '' ?>">
        </div>
        <div class="col-md-3 col-lg-3  col-sm-3 form-group ">
            <input name="statisticsEndDate" id="statisticsToDate" class="form-control datetimepicker hasDatepicker"
                   placeholder="To Date"
                   value="<?php echo isset($_GET['statisticsEndDate']) ? $_GET['statisticsEndDate'] : '' ?>">
        </div>


        <div class="col-lg-3 col-md-3 col-sm-3 pull-right text-right">
            <?= Html::submitButton('Go', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Clear', Yii::$app->urlManager->createUrl(['/site/dashboard']), ['class' => 'btn btn-default']) ?>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-lg-4 col-md-6 col-sm-12">
        <div class="mini-widget">
            <div class="mini-widget-body background-blue clearfix">
                <div class="pull-left  number">
                    <?php echo $countAudits['all']; ?>
                    <p class="bottomtext">Scheduled Audits</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12">
        <div class="mini-widget">
            <div class="mini-widget-body background-yellow clearfix">
                <div class="pull-left number">
                    <?php echo $countAudits['overdue']; ?>
                    <p class="bottomtext">Overdue Audits</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12">
        <div class="mini-widget">
            <div class="mini-widget-body background-pink clearfix">
                <div class="pull-left number">
                    <?php echo $countAudits['active']; ?>
                    <p class="bottomtext">Active Audits</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12">
        <div class="mini-widget">
            <div class="mini-widget-body background-orange clearfix">
                <div class="pull-left  number">
                    <?php echo $countAudits['completed']; ?>
                    <p class="bottomtext">Completed Audits</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 col-sm-12">
        <div class="mini-widget">
            <div class="mini-widget-body background-red clearfix">
                <div class="pull-left number">
                    <?php echo $countAudits['compliance'] . ' %'; ?>
                    <p class="bottomtext">Overall Compliance</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 col-sm-12">
        <div class="mini-widget">
            <div class="mini-widget-body background-green clearfix">
                <div class="pull-left number">
                    <?php echo $countAudits['chronic']; ?>
                    <p class="bottomtext">Chronic Issues</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 margintables">
        <div class="box">
            <div class="box-header">
                <h4 class="box-title">Overdue Audits</h4>
            </div>
            <!-- /.box-header -->
            <div id="scrollable_table"  class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="theadcolor">Due Date</th>
                            <th class="theadcolor">Audit ID</th>
                            <th class="theadcolor">Hotel</th>
                            <th class="theadcolor">Auditor</th>
                            <th class="theadcolor">Checklist</th>
                            <th class="theadcolor">Status</th>

                        </tr>
                    </thead>
                    <tbody>
                    <?php

                    if ($overdueAudits) {

                        foreach ($overdueAudits as $values) {
                            echo '<tr>';
                            echo '<td>' . date('d-M-Y', strtotime($values['end_date'])) . '</td>';
                            echo '<td>' . $values['audit_schedule_name'] . '</td>';
                            echo '<td>' . $values['hotel_name'] . '</td>';
                            echo '<td>' . $values['auditor_name'] . '  ' . $values['auditor_lname'] . '</td>';
                            echo '<td>' . $values['checklist'] . '</td>';
                            echo '<td><span class="label label-warning">' . \app\models\Audits::$statusList[$values['status']] . '</span></td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <?php if (!$overdueAudits) {
                    echo 'No Overdue audits are found';
                } ?>
            </div>


            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>
<div class="row">
    <div class="col-xs-12 margintables">
        <div class="box">
            <div class="box-header">
                <h4 class="box-title">Upcoming Audits</h4>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <th>Scheduled Date</th>
                        <th>Audit ID</th>
                        <th>Hotel</th>
                        <th>Auditor</th>
                        <th>Checklist</th>
                        <th>Status</th>

                    </tr>
                    <?php
                    if ($upcomingAudits) {
                        foreach ($upcomingAudits as $values) {
                            echo '<tr>';
                            echo '<td>' . date('d-M-Y', strtotime($values['end_date'])) . '</td>';
                            echo '<td>' . $values['audit_schedule_name'] . '</td>';
                            echo '<td>' . $values['hotel_name'] . '</td>';
                            echo '<td>' . $values['auditor_name'] . ' ' . $values['auditor_lname'] . '</td>';
                            echo '<td>' . $values['checklist'] . '</td>';
                            echo '<td><span class="label label-warning" style="background-color: #00c0ef !important;">' . \app\models\Audits::$statusList[0] . '</span></td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <?php if (!$upcomingAudits) {
                    echo 'No Upcoming audits are found';
                } ?>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>
