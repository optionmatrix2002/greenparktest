<?php

use Html;
use GridView;
use AppAsset;
use ActiveForm;
use JqueryAsset;
use View;
use Pjax;
use Tickets;
use TicketProcessCritical;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\TicketsSearch */

$this->title = 'Reports';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
View::registerJsFile(yii::$app->urlManager->createUrl('js/modelpopup.js'), ['depends' => JqueryAsset::className()]);
$this->registerJs('
$(".nav-bids").removeClass("active");
$("#tickets").addClass("active");
$(".dropdown-toggle").dropdown();
', \yii\web\View::POS_END);
?>

<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<div class="row">
  <div class="col-md-6 mb-3">
  	<label for="validationCustom03">Category:</label>
      <select class="form-control form-control-lg" name="category" id="validationCustom03" onchange="ChangecatList()" required>
        <option value="">Choose... </option>
        <option value="Classroom Instruction and Assessment">Classroom Instruction and Assessment</option>
        <option value="Curriculum Development and Alignment">Curriculum Development and Alignment</option>
        <option value="District Committee">District Committee</option>
        <option value="Meeting">Meeting</option>
        <option value="Other Category">Other Category</option>
        <option value="Professional Conference">Professional Conference</option>
        <option value="Professional Workshop / Training">Professional Workshop / Training</option>
        <option value="Pupil Services">Pupil Services</option>
      </select>
	<div class="invalid-feedback">
		Please provide a category.
	</div>
  </div>
  <div class="col-md-6 mb-3">
  	<label for="validationCustom04">Activity:</label>
     <select class="form-control form-control-lg" id="validationCustom04" name="activity" required></select>
    <div class="invalid-feedback">
		Please provide an activity.
	</div>
  </div>
</div>