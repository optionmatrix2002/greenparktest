<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Tickets */

$this->title = 'Create Tickets';
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tickets-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelTicketAttachment'=>$modelTicketAttachment,
    ]) ?>

</div>
