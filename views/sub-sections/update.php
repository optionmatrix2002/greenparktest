<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SubSections */

$this->title = 'Update Subsection: '. $model->ss_subsection_name;
$this->params['breadcrumbs'][] = ['label' => 'Sub Sections', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->sub_section_id, 'url' => ['view', 'id' => $model->sub_section_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sub-sections-update">

   
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
