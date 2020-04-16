<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<div class="content">
    <!-- BEGIN LOGO -->
    <div class="logo">
        <img src="<?=yii::$app->urlManager->createUrl("img/greenpark_textlogo.png"); ?>"  width="200"/>

        <h1 style="color:white">Forgot Password</h1>

        <div class="menu-toggler sidebar-toggler hide">
            <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
        </div>
    </div>

    <?php $form = ActiveForm::begin(['id' => 'forgot-form','action' => yii::$app->urlManager->createUrl('site/forgot-password'), 'options' => ['class' => 'login-form']]) ?>


    <?=
    $form->field($model, 'email', ['template' =>
        "<div class='input-icon'><i class='fa fa-user' style='line-height:22px;'></i>{input}</div>{hint}"])->Input(['placeholder' => 'Email ID'])->label(false);
    ?>

    <div class="form-actions">
        <?= Html::submitButton('Submit <i class="m-icon-swapright m-icon-white"></i>', ['class' => 'btn green-haze pull-right btn btn-success']) ?>
    </div>
    <br><br>
    <?php ActiveForm::end(); ?>
</div>