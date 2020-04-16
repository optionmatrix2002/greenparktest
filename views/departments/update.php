<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Departments */

$this->title = 'Update Department: '. $model->department_name;
$this->params['breadcrumbs'][] = ['label' => 'Departments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->department_id, 'url' => ['view', 'id' => $model->department_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="departments-update">
   <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
