<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SubSections */

$this->title = $model->sub_section_id;
$this->params['breadcrumbs'][] = ['label' => 'Sub Sections', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-sections-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->sub_section_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->sub_section_id], [
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
            'sub_section_id',
            'ss_section_id',
            'ss_subsection_name',
            'ss_subsection_remarks',
            'created_by',
            'modified_by',
            'created_date',
            'modified_date',
            'is_deleted',
            'is_active',
        ],
    ]) ?>

</div>
