<?php
/* @var $this View */
/* @var $content string */
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\base\Widget;

AppAsset::register($this);


View::registerJsFile(yii::$app->urlManager->createUrl('js/jquery-ui.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/bootstrap.min.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/jquery.dataTables.min.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/theme/bootstrap-tour.min.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/theme/metronic.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/theme/layout.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/theme/init.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile("//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js", [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/toastr_custom.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/moment-with-locales.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/bootstrap-datetimepicker.min.js'), [
    'depends' => JqueryAsset::className()
]);

View::registerJsFile(yii::$app->urlManager->createUrl('js/theme/icon-action-script.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/theme/bootstrap-tour.min.js'), [
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
	href="<?= yii::$app->urlManager->createUrl('img/greenpark_logos.png'); ?>" />
<link
	href="<?= yii::$app->urlManager->createUrl('css/components.css'); ?>"
	rel="stylesheet" type="text/css" />
<link href="<?= yii::$app->urlManager->createUrl('css/layout.css'); ?>"
	rel="stylesheet" type="text/css" />
<link href="<?= yii::$app->urlManager->createUrl('css/default.css'); ?>"
	rel="stylesheet" type="text/css" />
<link href="<?= yii::$app->urlManager->createUrl('css/admin.css'); ?>"
	rel="stylesheet" type="text/css" />
<link href="<?= yii::$app->urlManager->createUrl('css/bootstrap-datetimepicker.min.css'); ?>"
     rel="stylesheet" type="text/css" />
<link href="<?= yii::$app->urlManager->createUrl('css/jquery.dataTables.min.css'); ?>"
          rel="stylesheet" type="text/css" />
<link href="<?= yii::$app->urlManager->createUrl('css/bootstrap-tour.min.css'); ?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://loading.io/css/loading.css" />
<link rel="stylesheet" href="https://loading.io/css/transition.css" />
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
	type="text/css" />
<link rel="stylesheet"
	href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
        <?php $this->head() ?>
    </head>
<body
	class="page-header-fixed page-quick-sidebar-over-content page-style-square">
        <?php $this->beginBody() ?>
        
        <?= $this->render('dashboard_header'); ?>
  
<?php
$js = 'function removeToaster() {
     $("#toast-container-block").delay(5000).fadeOut("slow");
 }
    $(document).on("click",".toast-close-button",function(){
        $("#toast-container-block").hide();
    })
 removeToaster();
';
$this->registerJs($js, $this::POS_READY);
$this->registerJs('var ajaxUrl = "' . Url::home(true) . '";', View::POS_HEAD, 'ajaxUrl');
?>
        <div class="page-container">
            <?= $this->render('dashboard_sidebar'); ?>
            <div class="page-content-wrapper">
			<div class="page-content clearfix " style="background-color: #ffffff;">

				<div id="toast-container-block" class="toast-top-right">
				
                    <?php if(Yii::$app->session->hasFlash('success')): ?>
                        <div class="toast toast-success"
						aria-live="polite">
						<button type="button" class="toast-close-button" role="button">x</button>
						<div class="toast-message"> <?= Yii::$app->session->getFlash('success') ?></div>
					</div>
                    <?php endif; ?>
                    <?php if(Yii::$app->session->hasFlash('error')): ?>
                        <div class="toast toast-error"
						aria-live="assertive">
						<button type="button" class="toast-close-button" role="button">x</button>
						<div class="toast-message"> <?= Yii::$app->session->getFlash('error') ?></div>
					</div>
                    <?php endif; ?>
                    <?php if(Yii::$app->session->hasFlash('info')): ?>
                        <div class="toast toast-info"
						aria-live="assertive">
						<button type="button" class="toast-close-button" role="button">x</button>
						<div class="toast-message"> <?= Yii::$app->session->getFlash('info') ?></div>
					</div>
                    <?php endif; ?>
				</div>

				  <?= $content; ?>
                </div>
		</div>
	</div>
        
        <?= $this->render('dashboard_footer'); ?>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>

<style>
    .popover-title{
        font-weight: bolder;
    }
    .help-modal-title {
        font-weight: bolder;
        margin-left: 0px !important;
    }
</style>

