<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SubSections */

$this->title = 'Create Subsection';
$this->params['breadcrumbs'][] = ['label' => 'Sub Sections', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-sections-create">

  
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
