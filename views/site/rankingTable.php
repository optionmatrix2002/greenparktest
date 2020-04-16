<?php
/* @var $this View */
/* @var $content string */

use app\assets\AppAsset;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
?>

<?php echo $this->render('_searchRankAudits', ['model' => $searchAuditModel]); ?>
<div class="clearfix">&nbsp;</div>

<div class="row schedule_auditid" >
    <div class="tab-content">
        <div id="activeaudits" class="tab-pane fade in active">
            <div class="col-sm-12 nopadding">
                <div class="audits-index">
                    <?php Pjax::begin(); ?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProviderRankAuditsSchedules,
                        'layout' => '{items}{pager}',
                        //'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'header' => 'Rank #',
                                // you may configure additional properties here
                            ],
                            [
                                'attribute' => 'department_name',
                                'format' => 'raw',
                                'header' => 'Department',
                                'headerOptions' => ['class' => 'theadcolor']
                            ],
                            [
                                'attribute' => 'hotel_name',
                                'format' => 'raw',
                                'header' => 'Hotel',
                                'headerOptions' => ['class' => 'theadcolor']
                            ],
                            /* [
                                'attribute' => 'end_date',
                                'format' => 'raw',
                                'header' => 'Audit Date',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function($data) {
                                    return date('d-m-Y',strtotime($data['end_date']));
                                },
                            ],
                            [
                                'attribute' => 'auditor_id',
                                'format' => 'raw',
                                'header' => 'Auditor',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function($data) {
                                    return ucfirst($data['auditor_id']);
                                },
                            ], */
                            [
                                'attribute' => 'score',
                                'format' => 'raw',
                                'header' => 'Average Audit Score',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function($data) {
                                    return $data['score'].'/100';
                                },
                            ],

                        ],
                    ]); ?>
                    <?php Pjax::end(); ?>
                </div>

            </div>
        </div>
    </div>
</div>
