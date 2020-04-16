<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Errorlogs */

$this->title = 'Update Errorlogs: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Errorlogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="errorlogs-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
