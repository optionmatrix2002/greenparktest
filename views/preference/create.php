<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ProcessCriticalPreferences */

$this->title = 'Create Process Critical Preferences';
$this->params['breadcrumbs'][] = ['label' => 'Process Critical Preferences', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="process-critical-preferences-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
