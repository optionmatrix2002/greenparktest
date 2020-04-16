<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Checklists */

$this->title = 'Update Checklist';

$this->params['breadcrumbs'][] = ['label' => 'Checklists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->checklist_id, 'url' => ['view', 'id' => $model->checklist_id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="checklists-update">
  <?= $this->render('_form', [
        'model' => $model,
       
    ]) ?>
</div>
