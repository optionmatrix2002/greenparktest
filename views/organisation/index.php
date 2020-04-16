<?php
/* @var $this View */
/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use kartik\widgets\Select2;
$this->title = "Organisation Setup";

AppAsset::register($this);
View::registerJs("hierarchy_url = '". yii::$app->urlManager->createUrl('organisation/load-hierarchy')."';",View::POS_HEAD);
View::registerJs("icons_location = '". yii::$app->urlManager->createUrl('img/icons/')."';",View::POS_HEAD);
View::registerCssFile(yii::$app->urlManager->createUrl('js/jstree/dist/themes/default/style.css'));
View::registerJsFile(yii::$app->urlManager->createUrl('js/jstree/dist/jstree.min.js'), ['depends' => JqueryAsset::className()]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/organisation.js?version='. time()), ['depends' => JqueryAsset::className()]);
?>
<div class="container-fluid">
    <h2>Organization Setup</h2>
</div>                
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
    <p id="description-text">
        Organization hierarchy which includes Locations, Hotels, Departments, Sections and Subsections can be managed from here.
    </p>
</div>

<div class="col-md-12 ">
    <button id="add_location_btn" class="btn btn-link" data-action="<?= yii::$app->urlManager->createUrl('organisation/load-new-location'); ?>"><i class="fa fa-plus" aria-hidden="true"></i>  Add Location</button>
</div>
<div class="col-sm-12 margintop10" >
    <div class="col-sm-12" style="height: 30%; overflow-y: scroll;">
        <div class="col-sm-12">
            <h4>Organization Hierarchy</h4>
            <h6 class="text-muted"> (Right Click for more options)</h6>
            
            <div class=" col-sm-6">
                <div id="organisation_hierarchy" class=""></div>
            </div>
        </div>
    </div>
    <!-- add locations modal -->
    <div id="popup_model" class="modal fade" role="dialog"></div>
