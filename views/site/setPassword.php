<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<div class="content">
	<!-- BEGIN LOGO -->
	<div class="logo">
		<!--<img src="http://optionmatrix.in/greenpark/Content/Images/Greenpark_logo.png"  width="200"/>-->
		<img
			src="<?=yii::$app->urlManager->createUrl("img/greenpark_textlogo.png"); ?>"
			width="200" />

		<h1 style="color: white">Set Password</h1>

		<div class="menu-toggler sidebar-toggler hide">
			<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
		</div>
	</div>
		  <?php $form = ActiveForm::begin(['id'=>'set_password_form']); ?>
            <?=$form->field($model, 'password', ['template' => "<div class='input-icon'><i class='fa fa-user' style='line-height:22px;'></i>{input}</div>{hint}"])->passwordInput(['placeholder' => 'New password'])->label(false);?>
             <?=$form->field($model, 'confirmPassword', ['template' => "<div class='input-icon'><i class='fa fa-user' style='line-height:22px;'></i>{input}</div>{hint}"])->passwordInput(['placeholder' => 'Confirm password'])->label(false);?>      
            <div class="form-actions">
           <?= Html::submitButton('Submit <i class="m-icon-swapright m-icon-white"></i>', ['class' => 'btn green-haze pull-right btn btn-success']) ?>
             </div>
	<br>
	<br>
           <?php ActiveForm::end(); ?>
</div>