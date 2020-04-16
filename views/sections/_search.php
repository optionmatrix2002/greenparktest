<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

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

    <div class="col-lg-12 col-md-12 col-sm-12 showfilter">
        <div class="col-lg-4 col-md-4 col-sm-4">
            <?php
            /*
            echo $form->field($model, 's_department_id')
                ->dropDownList(ArrayHelper::map(\app\models\Departments::find()->where(['is_deleted' => 0])->all(), 'department_id', 'department_name'), [
                'prompt' => 'Select Department'
            ], [
                'class',
                'form-control'
            ])
                ->label(false);
                */
            ?>
            <?= $form->field($model, 's_department_id')->widget(Select2::classname(), ['data' => ArrayHelper::map(\app\models\Departments::find()->where(['is_deleted' => 0])->all(), 'department_id', 'department_name'), 'showToggleAll' => false, 'language' => 'en', 'options' => ['placeholder' => 'Select Department'], 'pluginOptions' => ['allowClear' => true]])->label(false); ?>

        </div>
        <div class="col-lg-4 col-md-4 col-sm-4">
            <?php
            /*
            echo $form->field($model, 'section_id')
                ->dropDownList(ArrayHelper::map(\app\models\Sections::find()->where(['is_deleted' => 0])->all(), 'section_id', 's_section_name'), [
                'prompt' => 'Select Section'
            ], [
                'class',
                'form-control'
            ])
                ->label(false);
                */
            ?>
            <?= $form->field($model, 'section_id')->widget(Select2::classname(), ['data' => ArrayHelper::map(\app\models\Sections::find()->where(['is_deleted' => 0])->all(), 'section_id', 's_section_name'), 'showToggleAll' => false, 'language' => 'en', 'options' => ['placeholder' => 'Select Section'], 'pluginOptions' => ['allowClear' => true]])->label(false); ?>

        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 pull-right text-right">
            <?= Html::submitButton('Go', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Clear', Yii::$app->urlManager->createUrl(['/sections']), ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>
</div>