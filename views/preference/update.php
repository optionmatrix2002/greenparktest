<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProcessCriticalPreferences */

$this->title = 'Update Process Critical Preferences: ' . $model->critical_preference_id;
$this->params['breadcrumbs'][] = ['label' => 'Process Critical Preferences', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->critical_preference_id, 'url' => ['view', 'id' => $model->critical_preference_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="process-critical-preferences-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
