<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Preferences */

$this->title = 'Update Preferences: ' . $model->preferences_id;
$this->params['breadcrumbs'][] = ['label' => 'Preferences', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->preferences_id, 'url' => ['view', 'id' => $model->preferences_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="preferences-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
