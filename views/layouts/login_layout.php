<?php
/* @var $this View */

/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\View;
use kartik\growl\Growl;

AppAsset::register($this);
View::registerJsFile(yii::$app->urlManager->createUrl('js/login/login_index.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile("//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js", [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/toastr_custom.js'), [
    'depends' => JqueryAsset::className()
]);
?>


<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel='shortcut icon' type="image/x-icon"
          href="<?= yii::$app->urlManager->createUrl('img/greenpark_logos.png'); ?>"/>
    <link rel="stylesheet"
          href="<?= yii::$app->urlManager->createUrl('css/login/login-soft.css'); ?>"/>
    <link rel="stylesheet"
          href="<?= yii::$app->urlManager->createUrl('css/components.css'); ?>"/>

    <link rel="stylesheet"
          href="<?= yii::$app->urlManager->createUrl('css/admin.css'); ?>"/>
    <link rel="stylesheet" href="https://loading.io/css/loading.css"/>
    <link rel="stylesheet" href="https://loading.io/css/transition.css"/>
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
          type="text/css"/>
    <link rel="stylesheet"
          href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
    <?php $this->head() ?>
</head>
<?php
$js = 'function removeToaster() {
     $("#toast-container-block").delay(2000).fadeOut("slow");
 }
 $(document).on("click",".toast-close-button",function(){
        $("#toast-container-block").hide();
    })

 removeToaster();
';
$this->registerJs($js, $this::POS_READY);
?>
<body class="login">
<?php $this->beginBody() ?>

<div id="toast-container-block" class="toast-top-right">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="toast toast-success"
             aria-live="polite">
            <button type="button" class="toast-close-button" role="button">x</button>
            <div class="toast-message"> <?= Yii::$app->session->getFlash('success') ?></div>
        </div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="toast toast-error"
             aria-live="assertive">
            <button type="button" class="toast-close-button" role="button">x</button>
            <div class="toast-message"> <?= Yii::$app->session->getFlash('error') ?></div>
        </div>
    <?php endif; ?>
</div>


<?= $content ?>
<div class="lgnfooter text-center"><?php echo date('Y'); ?> - Option Matrix InfoTech
    Pvt Ltd &#174; All Rights Reserved.
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

