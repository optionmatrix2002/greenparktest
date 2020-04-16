<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Audits */

$this->title = 'Create Audits';
$this->params['breadcrumbs'][] = ['label' => 'Audits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audits-create">
 <?= $this->render('_form', [
     'model' => $model,
     'auditLocationsModel' => $auditLocationsModel,
     'auditsSchedulesModel' => $auditsSchedulesModel,
     
    ]) ?>

</div>
