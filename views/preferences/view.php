<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Preferences */

$this->title = $model->preferences_id;
$this->params['breadcrumbs'][] = ['label' => 'Preferences', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="preferences-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->preferences_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->preferences_id], [
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
            'preferences_id',
            'preferences_name',
            'preferences_lable',
            'preferences_value',
            'preferences_description:ntext',
            'preferences_type',
            'preferences_options:ntext',
            'created_by',
            'update_by',
            'created_at',
            'update_at',
        ],
    ]) ?>

</div>
