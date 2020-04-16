<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Sections */

$this->title = 'Create Section';
$this->params['breadcrumbs'][] = ['label' => 'Sections', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sections-create">

   <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
