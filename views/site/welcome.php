<?php
/* @var $this View */
/* @var $content string */
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

AppAsset::register($this);

View::registerJsFile("https://www.amcharts.com/lib/3/amcharts.js", [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile("https://www.amcharts.com/lib/3/serial.js", [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile("https://www.amcharts.com/lib/3/pie.js", [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/dashboard.js'), [
    'depends' => JqueryAsset::className()
]);

?>
<div class="container-fluid">
	<h2>Green Park Corporate Audit Application</h2>
</div>
<div class="wa-notification wa-notification-alt">
	<span class="wa-iconBoxed"> <span class="wa-icon wa-icon-notification"></span>
	</span>
	<p id="statistics-text">Welcome, please use the menu options on the
		left to navigate through the application.</p>
</div>
<div class="" style="margin-top: 10px;">
	<div class="col-lg-12 col-md-12">
		<div class="row"></div>
	</div>
</div>