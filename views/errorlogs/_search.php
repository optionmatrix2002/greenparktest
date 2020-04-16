<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\ErrorlogsSearch */
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

<div class="errorlogs-search">
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

    <div class="col-lg-12 col-md-12 col-sm-12 showfilter">
        <div class="col-lg-4 col-md-4 col-sm-4">
                <?= $form->field($model, 'start_date')->textInput(['class' => 'datetimepicker form-control', 'placeholder' => 'From Date'])->label(false); ?>

        </div>

        <div class="col-lg-4 col-md-4 col-sm-4">
                <?= $form->field($model, 'end_date')->textInput(['class' => 'datetimepicker form-control', 'placeholder' => 'To Date'])->label(false); ?>

        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 pull-right text-right">
                <?= Html::submitButton('Go', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Clear', Yii::$app->urlManager->createUrl(['/errorlogs']), ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>

</div>
