<?php

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ErrorlogsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Error Logs';
$this->params['breadcrumbs'][] = $this->title;


AppAsset::register($this);
View::registerCssFile(yii::$app->urlManager->createUrl('css/roles.css'));
View::registerCssFile(yii::$app->urlManager->createUrl('js/jstree/dist/themes/default/style.css'));
View::registerJsFile(yii::$app->urlManager->createUrl('js/jstree/dist/jstree.min.js'), ['depends' => JqueryAsset::className()]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/audits.js'), [
    'depends' => JqueryAsset::className()
]);


$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuSystemAdmin").addClass("active");
$("#MenuErrorlog").addClass("active");
', \yii\web\View::POS_END);
?>
<div class="container-fluid">
    <h2>Error Log</h2>
</div>
<!-- notification text -->
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
    <p id="description-text">
        Unexpected technical errors that occur in the application can be viewed here.

    </p>
</div>
<!-- -------------------------Start Search here------------------------- -->
<?php echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="errorlogs-index">
    <?php $buttons = '';
    if (Yii::$app->authManager->checkPermissionAccess('errorlogs/delete')) {
        $buttons .= '&nbsp;&nbsp;{delete}';
    }

    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items}{pager}',
        'columns' => [
            [
                'attribute' => 'log_time',
                'header' => 'Time',
                'format' => 'raw',
                'headerOptions' => ['class' => 'theadcolor'],
                'value' => function ($model) {
                    return $model->log_time;

                    //return Yii::$app->formatter->asDate($model->log_time, 'php:Y-m-d');
                },

            ],


            [
                'attribute' => 'prefix',
                'header' => 'Url',
                'format' => 'raw',
                'headerOptions' => ['class' => 'theadcolor'],
                'contentOptions' => ['style' => 'width:10px;'],
                'value' => function ($model) {
                    return $model->prefix;
                },

            ],
            [
                'attribute' => 'message',
                'header' => 'Message',
                'format' => 'raw',
                'headerOptions' => ['class' => 'theadcolor'],
                'value' => function ($model) {
                    return substr($model->description, 0, 200);
                },

            ],

        ],
    ]); ?>
</div>

