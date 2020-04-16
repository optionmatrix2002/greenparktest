<?php

use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\models\Checklists */

$this->title = 'Add Checklist';
$this->params['breadcrumbs'][] = ['label' => 'Checklists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="checklists-create">
  <?= $this->render('_form', [
        'model' => $model,
        'modelAuditMethods' => $modelAuditMethods,
     
    ]) ?>
</div>
