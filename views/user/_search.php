<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Departments;
use \app\models\Roles;
use \app\models\Locations;

/* @var $this yii\web\View */
/* @var $model app\models\search\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

    <?php

    $form = ActiveForm::begin([
        'action' => [
            'index'
        ],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{input}",
            'options' => [
                'tag' => 'false'
            ]
        ],
    ]);
    ?>

    <div class="col-lg-12 col-md-12 col-sm-12 showfilter">
        <div class="col-lg-3 col-md-6 col-sm-6">
            <?= $form->field($model, 'email')->textInput(['class' => 'form-control', 'placeholder' => 'Email ID'])->label(false); ?>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <?php
            echo $form->field($model, 'departmentId')
                ->dropDownList(ArrayHelper::map(\app\models\Departments::find()->where(['is_deleted' => 0])->all(), 'department_id', 'department_name'), [
                    'prompt' => 'Select Department'
                ], [
                    'class',
                    'form-control'
                ])
                ->label(false);
            ?>
        </div>


        <div class="col-lg-3 col-md-6 col-sm-6">
            <?= $form->field($model, 'role_id')->dropDownList(ArrayHelper::map(\app\models\Roles::find()->where(['is_deleted' => 0])->all(), 'role_id', 'role_name'), ['prompt' => 'Select Role'], ['class', 'form-control'])->label(false) ?>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <?= $form->field($model, 'is_active')->dropDownList(['0' => 'InActive', '1' => 'Active'], ['prompt' => 'Select status'], ['class', 'form-control'])->label(false); ?>
        </div>


        <div class="col-lg-3 col-md-3 col-sm-3 pull-right text-right margin-top-5">
            <?= Html::submitButton('Go', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Clear', Yii::$app->urlManager->createUrl(['/user']), ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>