<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Tickets */

$this->title = 'Update Ticket: '. $model->ticket_name;
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ticket_id, 'url' => ['view', 'id' => $model->ticket_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tickets-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
