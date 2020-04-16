<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Checklists */

$this->title = $model->checklist_id;
$this->params['breadcrumbs'][] = ['label' => 'Checklists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="checklists-view">
   <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->checklist_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->checklist_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'checklist_id',
            'cl_name',
            'cl_audit_type',
            'cl_audit_method',
            'cl_department_id',
            'cl_frequency_value',
            'cl_frequency_duration',
            'cl_audit_span',
            'cl_status',
        ],
    ]) ?>
</div>
