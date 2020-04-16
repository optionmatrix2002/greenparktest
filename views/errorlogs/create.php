<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Errorlogs */

$this->title = 'Create Errorlogs';
$this->params['breadcrumbs'][] = ['label' => 'Errorlogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="errorlogs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
