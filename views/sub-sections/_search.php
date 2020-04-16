<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\SubSectionsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sub-sections-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'fieldConfig' => [
            'template' => "{input}",
            'options' => [
                'tag' => 'false'
            ]
        ],
        'method' => 'get',
    ]); ?>
    <div class="col-lg-12 col-md-12 col-sm-12 showfilter">

        <div class="col-lg-4 col-md-4 col-sm-4">

            <?php
            /*
            echo $form->field($model, 'ss_section_id')
                ->dropDownList(ArrayHelper::map(\app\models\Sections::find()->where(['is_deleted' => 0])->all(), 'section_id', 's_section_name'), [
                'prompt' => 'Select Section'
            ], [
                'class',
                'form-control'
            ])
                ->label(false);
                */
            ?>
            <?= $form->field($model, 'ss_section_id')->widget(Select2::classname(), ['data' => ArrayHelper::map(\app\models\Sections::find()->where(['is_deleted' => 0])->all(), 'section_id', 's_section_name'), 'showToggleAll' => false, 'language' => 'en', 'options' => ['placeholder' => 'Select Section'], 'pluginOptions' => ['allowClear' => true]])->label(false); ?>


        </div>

        <div class="col-lg-4 col-md-4 col-sm-4">

            <?php
            /*
            echo $form->field($model, 'sub_section_id')
                ->dropDownList(ArrayHelper::map(\app\models\SubSections::find()->where(['is_deleted' => 0])->all(), 'sub_section_id', 'ss_subsection_name'), [
                'prompt' => 'Select Subsections'
            ], [
                'class',
                'form-control'
            ])
                ->label(false);
                */
            ?>
            <?= $form->field($model, 'sub_section_id')->widget(Select2::classname(), ['data' => ArrayHelper::map(\app\models\SubSections::find()->where(['is_deleted' => 0])->all(), 'sub_section_id', 'ss_subsection_name'), 'showToggleAll' => false, 'language' => 'en', 'options' => ['placeholder' => 'Select Subsection'], 'pluginOptions' => ['allowClear' => true]])->label(false); ?>


        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 pull-right text-right">
            <?= Html::submitButton('Go', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Clear', Yii::$app->urlManager->createUrl(['/sub-sections']), ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>