<?php


use app\assets\AppAsset;
use yii\web\JqueryAsset;
use yii\web\View;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Preferences';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
View::registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css');
View::registerCssFile(yii::$app->urlManager->createUrl('css/preferences.css'));
View::registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js', [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/preferences.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/site.js'), [
    'depends' => JqueryAsset::className()
]);

$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuSystemAdmin").addClass("active");
$("#settings-preferences").addClass("active");
', \yii\web\View::POS_END);
?>


<div class="container-fluid">
    <h2><?= $this->title; ?> </h2>
</div>
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
    <p id="description-text">
        Default configuration parameters for the application can be managed from here.
    </p>
</div>

<div class="" style="margin-top: 10px;">
    <div class="col-lg-12 col-md-12">
        <div class="row" id="dashboard-tabs">
            <ul class="nav nav-tabs" id="myTab">
                <li class="active"><a class="tabs" data-toggle="tab" href="#system"><i class="fa fa-th-list"
                                                                                           aria-hidden="true"></i>&nbsp;System Preferences</a>
                </li>
                <li><a class="tabs" data-toggle="tab" href="#rca"><i class="fa fa-th-list" aria-hidden="true"></i>&nbsp;RCA Preferences</a></li>
              <li><a class="tabs" data-toggle="tab" href="#emailtemplate"><i class="fa fa-th-list" aria-hidden="true"></i>&nbsp;Email Template</a></li>
            </ul>
            <div class="tab-content">
                <div id="system" class="tab-pane fade in active">
                    <?= $this->render('system', [
                        'dataProvider' => $dataProvider
                    ]); ?>
                </div>
                <div id="rca" class="tab-pane fade">
                   <?= $this->render('rca-preference', [
                       'dataProvider' => $rcadataProvider
                    ]); ?>
                </div>
                
                <div id="emailtemplate" class="tab-pane fade">
                   <?= $this->render('emailtemplate', [
                       'model' => $emailtemplate
                    ]); ?>
                </div>
               
                
                
              
            </div>
        </div>
    </div>
</div>

<?php


$this->registerJs("
 $('#dashboard-tabs ul li a').click(function () {
 removeQString('');
 location.hash = $(this).attr('href');
 });
 
 function removeQString(key) {
	var urlValue=document.location.href;
	
	//Get query string value
	var searchUrl=location.search;
	
	if(key!=\"\") {
		oldValue = getParameterByName(key);
		removeVal=key+\"=\"+oldValue;
		if(searchUrl.indexOf('?'+removeVal+'&')!= \"-1\") {
			urlValue=urlValue.replace('?'+removeVal+'&','?');
		}
		else if(searchUrl.indexOf('&'+removeVal+'&')!= \"-1\") {
			urlValue=urlValue.replace('&'+removeVal+'&','&');
		}
		else if(searchUrl.indexOf('?'+removeVal)!= \"-1\") {
			urlValue=urlValue.replace('?'+removeVal,'');
		}
		else if(searchUrl.indexOf('&'+removeVal)!= \"-1\") {
			urlValue=urlValue.replace('&'+removeVal,'');
		}
	}
	else {
		var searchUrl=location.search;
		urlValue=urlValue.replace(searchUrl,'');
	}
	history.pushState({state:1, rand: Math.random()}, '', urlValue);
}
");

?>
