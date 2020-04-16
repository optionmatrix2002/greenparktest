<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\AuditsSchedulesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Audits Schedules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audits-schedules-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Audits Schedules', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'audit_schedule_id',
            'audit_schedule_name',
            'audit_id',
            'auditor_id',
            'start_date',
            // 'end_date',
            // 'deligation_user_id',
            // 'deligation_status',
            // 'status',
            // 'is_deleted',
            // 'created_by',
            // 'updated_by',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
