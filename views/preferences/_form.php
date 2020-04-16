<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Preferences */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="preferences-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'preferences_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'preferences_lable')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'preferences_value')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'preferences_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'preferences_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'preferences_options')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'update_by')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'update_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
