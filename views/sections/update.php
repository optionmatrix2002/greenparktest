<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sections */

$this->title = 'Update Section: '. $model->s_section_name;
$this->params['breadcrumbs'][] = ['label' => 'Sections', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->section_id, 'url' => ['view', 'id' => $model->section_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sections-update">
  <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
