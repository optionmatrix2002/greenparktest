<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\search\DepartmentsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sections-search">

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
        <div class="col-lg-4 col-md-6 col-sm-6">
            <?php
            /* echo $form->field($model, 'department_id')
                 ->dropDownList(ArrayHelper::map(\app\models\Departments::find()->where(['is_deleted' => 0])->all(), 'department_id', 'department_name'), [
                 'prompt' => 'Select Department'
             ], [
                 'class',
                 'form-control'
             ])
                 ->label(false);
                 */
            ?>
            <?= $form->field($model, 'department_id')->widget(Select2::classname(), ['data' => ArrayHelper::map(\app\models\Departments::find()->where(['is_deleted' => 0])->all(), 'department_id', 'department_name'), 'showToggleAll' => false, 'language' => 'en', 'options' => ['placeholder' => 'Select Department'], 'pluginOptions' => ['allowClear' => true]])->label(false); ?>

        </div>

        <div class="col-lg-4 col-md-6 col-sm-6 pull-right text-right">
            <?= Html::submitButton('Go', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Clear', Yii::$app->urlManager->createUrl(['/departments']), ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>
</div>