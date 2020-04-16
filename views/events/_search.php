<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\EventsSearch */
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

$this->registerJsFile(
    'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js', ['depends' => [\yii\web\JqueryAsset::className()]]
    );

?>
<div class="events-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{input}",
            'options' => [
                'tag' => 'false'
            ]
        ],
    ]); ?>

    <div class="col-md-12 col-sm-12 col-lg-12 showfilter">
        <div class="col-lg-3 col-md-3 col-sm-3">
                <?= $form->field($model, 'message')->textInput(['class'=>'form-control','placeholder' => 'Keyword search'])->label(false); ?>

        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">

                <?= $form->field($model, 'event_type')->dropDownList([''=>'All','create'=>'Create', 'update'=>'Update', 'delete'=>'Delete', 'assigned'=>'Assigned', 'started'=>'Started', 'Delegated'=>'Delegated', 'resolved'=>'Resolved'], ['prompt' => 'Select Event'], ['class', 'form-control'])->label(false);?>

        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
                <?= $form->field($model, 'module')->dropDownList([''=>'All','checklist'=>'Checklist', 'audit'=>'Audit' , 'ticket'=>'Ticket', 'report'=>'Report'], ['prompt' => 'Select Event'], ['class', 'form-control'])->label(false);?>
        </div>
        
        <div class="col-lg-3 col-md-3 col-sm-3">
           <?= $form->field($model, 'start_date')->textInput(['class'=>'datetimepicker form-control','placeholder' => 'From Date'])->label(false); ?>
      
   </div>
  
   <div class="col-lg-3 col-md-3 col-sm-3 margin-top-5">
           <?= $form->field($model, 'end_date')->textInput(['class'=>'datetimepicker form-control','placeholder' => 'To Date'])->label(false); ?>
      
   </div>

        <div class="col-lg-3 col-md-3 col-sm-3 pull-right text-right margin-top-5">
                <?= Html::submitButton('Go', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Clear', Yii::$app->urlManager->createUrl(['/events']),['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>

</div>


