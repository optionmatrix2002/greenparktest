<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\JqueryAsset;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EventsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Event Master';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuSystemAdmin").addClass("active");
$("#MenuEvent").addClass("active");
', \yii\web\View::POS_END);

?>


<div class="container-fluid">
    <h2><?= $this->title; ?> </h2>
</div>
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
    <p id="description-text">
        Key events of the application are logged here along with user names and timestamps.
    </p>
</div>
<?php echo $this->render('_search', ['model' => $searchModel]); ?>

<div class="events-index">
    <div class="row">
        <div class="col-sm-12 margintop10">
            <?php Pjax::begin(); ?>    <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{items} {pager}',
                ///'filterModel' => $searchModel,
                'columns' => [

                    [
                        'attribute' => 'message',
                        'header' => 'Event Name',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'theadcolor'],
                    ],
                    [
                        'attribute' => 'event_type',
                        'header' => 'Event Type',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'value' => function ($data)
                        {
                            return ucwords($data['event_type']);
                        }
                    ],
                    [
                        'attribute' => 'module',
                        'header' => 'Module',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'value' => function($data)
                        {
                            return ucwords($data['module']);
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'header' => 'Timestamp',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'value' => function ($data) {
                            return date('d-m-Y H:i:s A', strtotime($data['created_at']));
                        }
                    ],
                ],
            ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>


