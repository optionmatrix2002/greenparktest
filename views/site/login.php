<?php
use yii\bootstrap\ActiveForm;

$this->title = 'Corporate Audit';
?>

<div class="content">
	<div class="logo">
		<img alt="Corporate Audit"
			src="<?=yii::$app->urlManager->createUrl("img/loginlogo.png"); ?>" />
	</div>
    <?php $form = ActiveForm::begin(['id' => 'login-form', 'action' => yii::$app->urlManager->createUrl('site/verify-login'), 'options' => ['class' => 'login-form']]) ?>
    <h4 class="form-title text-center">Login to your account</h4> 

    <?=$form->field($loginModel, 'username', ['template' => "<div class='input-icon'><i class='fa fa-user' style='line-height:22px;'></i>{input}</div>{hint}"])->textInput(['placeholder' => 'Email Address'])->label(false);?>
    <?=$form->field($loginModel, 'password', ['template' => "<div class='input-icon'><i class='fa fa-lock' style='line-height:22px;'></i>{input}</div>{hint}"])->passwordInput(['placeholder' => 'Password','autocomplete' => "off"])->label(false);?>

    <div class="row">
		<div class="col-md-6">
            <?=$form->field($loginModel, 'rememberMe')->checkbox(['template' => '{input} {label}']);?> 
        </div>
		<div class="col-md-6 text-right">
			<div class="form-group">
				<p>
					<a
						href="<?= yii::$app->urlManager->createUrl('site/forgot-password') ?>"
						id="forget-password"> Forgot password? </a>
				</p>
			</div>

		</div>
	</div>
	<div class="row text-center">
		<button type="submit" id="submit_login_btn"
			class="btn green-haze min-width-150">
			Login <i class="m-icon-swapright m-icon-white"></i>
		</button>
	</div>
    <?php ActiveForm::end() ?>
</div>
