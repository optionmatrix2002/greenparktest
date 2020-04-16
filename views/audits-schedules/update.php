<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AuditsSchedules */

$this->title = 'Update Audits Schedules: ' . $model->audit_schedule_id;
$this->params['breadcrumbs'][] = ['label' => 'Audits Schedules', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->audit_schedule_id, 'url' => ['view', 'id' => $model->audit_schedule_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="audits-schedules-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
