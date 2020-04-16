<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\search\SectionsSearch */
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

    <div class="col-sm-12 col-lg-12 col-md-12 showfilter">
        <div class="col-lg-3 col-md-6 col-sm-12">
                <?= $form->field($model, 'checklistname_search')->textInput(['class' => 'form-control', 'placeholder' => 'Checklist Name'])->label(false); ?>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
                <?php
                echo $form->field($model, 'cl_department_id')
                    ->dropDownList(ArrayHelper::map(\app\models\Departments::find()->where(['is_deleted' => 0])->all(), 'department_id', 'department_name'), [
                        'prompt' => 'Select Department'
                    ], [
                        'class',
                        'form-control'
                    ])
                    ->label(false);
                ?>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
                <?= $form->field($model, 'cl_audit_span')->dropDownList(['1' => 'Section Specific', '2' => 'Across Section'], ['prompt' => 'Select Audit Span'], ['class', 'form-control'])->label(false); ?>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
                <?= $form->field($model, 'cl_status')->dropDownList(['0' => 'InActive', '1' => 'Active'], ['prompt' => 'Select status'], ['class', 'form-control'])->label(false); ?>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-3 pull-right text-right margin-top-5">
                <?= Html::submitButton('Go', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Clear', Yii::$app->urlManager->createUrl(['/check-lists']), ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>
</div>