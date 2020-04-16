<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AuditsSchedules */

$this->title = 'Create Audits Schedules';
$this->params['breadcrumbs'][] = ['label' => 'Audits Schedules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audits-schedules-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
