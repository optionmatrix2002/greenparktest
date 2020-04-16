<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\search\AuditsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$this->registerJs('
 $("document").ready(function()
 {
 
  $(\'.datetimepicker\').datetimepicker({
   format: \'DD-MM-YYYY\',
  });

 });
');



$this->registerJs('
 $(".clear-all-btn").click(function()
 {
      window.location.href="dashboard#ranking"; 
 });
');

$this->registerJs('
    var startdate = $("#rankingauditFromDate").val();
    if ((startdate == "")) {
        var startdate = $("#rankingauditFromDate").val();
        $("#rankingauditFromDate,#rankingauditstatisticsToDate").datetimepicker({
            format: "DD-MM-YYYY",
         });
            $("#rankingauditFromDate").datetimepicker().on("dp.change", function (e) {
    
                var incrementDay = moment(new Date(e.date));
                incrementDay.add(1, "days");
                $("#rankingauditstatisticsToDate").data("DateTimePicker").minDate(incrementDay);
                $("#rankingauditstatisticsToDate").val("");
                $(this).data("DateTimePicker").hide();
            });
    
    }
    ', \yii\web\View::POS_END);
?>
<div class="col-md-12 col-sm-12 col-lg-12 showfilter">
    <div class="audits-search">
        <?php $form = ActiveForm::begin([
            'id'=>'ranking_dashboard',
            'action' => ['dashboard#ranking'],
            'method' => 'get',
            'fieldConfig' => [
                'template' => "{input}",
                'options' => [
                    'tag' => 'false'
                ]
            ],
        ]); ?>
        <div class="col-lg-3 col-md-3 col-sm-3">
                <?php

                if(Yii::$app->user && Yii::$app->user->identity->user_type !=1){
                    $return = \app\models\User::getUserAssingemnts();
                    $userHotels = $return['userHotels'];
                    $dimensionTypes = ArrayHelper::map(\app\models\Hotels::find()->andFilterWhere(['hotel_id'=>$userHotels,'hotel_status' => 1, 'is_deleted' => 0])->orderBy('hotel_name')->all(), 'hotel_id', 'hotel_name');
                }else{
                    $dimensionTypes = ArrayHelper::map(\app\models\Hotels::find()->andFilterWhere(['hotel_status' => 1, 'is_deleted' => 0])->orderBy('hotel_name')->all(), 'hotel_id', 'hotel_name');
                }

                echo $form->field($model, 'hotel_id')
                    ->dropDownList($dimensionTypes, [
                        'prompt' => 'Hotel','id'=>'ranking_aduit_id'
                    ], [
                        'class',
                        'form-control'
                    ])
                    ->label(false);
                ?>

        </div>

        <div class="col-lg-3 col-md-3 col-sm-3">
                <?= $form->field($model, 'start_date')->textInput(['class'=>'datetimepicker form-control','placeholder' => 'From Date', 'id' => 'rankingauditFromDate'])->label(false); ?>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-3">
                <?= $form->field($model, 'end_date')->textInput(['class'=>'datetimepicker form-control','placeholder' => 'To Date', 'id' => 'rankingauditstatisticsToDate'])->label(false); ?>
        </div>
        <!--
        <div class="form-group">
            <?= Html::submitButton('Go', ['class' => 'btn btn-success']) ?>
        </div>
    -->
        <div class="col-lg-3 col-md-3 col-sm-3 pull-right text-right">
            <?= Html::submitButton('Go', ['class' => 'btn btn-success']) ?>
            <?= Html::submitButton('Clear', ['class' => 'btn btn-default clear-all-btn']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
