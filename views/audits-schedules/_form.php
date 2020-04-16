<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AuditsSchedules */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="audits-schedules-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'audit_schedule_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'audit_id')->textInput() ?>

    <?= $form->field($model, 'auditor_id')->textInput() ?>

    <?= $form->field($model, 'start_date')->textInput() ?>

    <?= $form->field($model, 'end_date')->textInput() ?>

    <?= $form->field($model, 'deligation_user_id')->textInput() ?>

    <?= $form->field($model, 'deligation_status')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'is_deleted')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
