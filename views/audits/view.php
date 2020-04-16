<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Audits */

$this->title = $model->audit_id;
$this->params['breadcrumbs'][] = ['label' => 'Audits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audits-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->audit_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->audit_id], [
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
            'audit_id',
            'audit_name',
            'location_id',
            'hotel_id',
            'department_id',
            'checklist_id',
            'user_id',
            'start_date',
            'end_date',
            'deligation_flag',
            'status',
            'is_deleted',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
