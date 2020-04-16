<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\ActiveForm;
use app\assets\AppAsset;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;
use app\models\AuditsSchedules;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\AuditsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Audits';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
View::registerJsFile(yii::$app->urlManager->createUrl('js/modelpopup.js'), ['depends' => JqueryAsset::className()]);
$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuAudits").addClass("active");
$(".dropdown-toggle").dropdown();
', \yii\web\View::POS_END);
?>
<div class="container-fluid">
    <h2>Manage Audits</h2>
</div>
<!-- notification text -->
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
    <p id="description-text">
        Audits for departments across hotels can be scheduled and managed here.

    </p>
</div>
<!-- -------------------------Start Search here------------------------- -->
<?php echo $this->render('_search', ['model' => $searchModel]); ?>

<div class="row">
    <div class="col-lg-12 nopadding">
        <ul class="nav nav-tabs">
            <li  class="active">
                <a href="#" class="archivedaudits tabs" data-toggle="tab" name="tab" data-index="1"><i
                        class="fa fa-archive" aria-hidden="true" aria-expanded="false"></i>&nbsp;Completed Audits</a>
            </li>
            <li>
                <a href="#" class="activeaudits tabs active" data-toggle="tab" name="tab" data-index="0"><i
                        class="fa fa-folder-open" aria-hidden="true" aria-expanded="true"></i>&nbsp;Scheduled
                    Audits</a>
            </li>

            <?php
            if (Yii::$app->authManager->checkPermissionAccess('audits/create')) {
                ?>
                <li class="pull-right">
                    <a href="<?= yii::$app->urlManager->createUrl('audits/create'); ?>" class="btn btn-success"><i
                            class="fa fa-plus"></i>&nbsp;Schedule Audit</a>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>

<div class="row schedule_auditid" >
    <div class="tab-content">
        <div id="activeaudits" class="tab-pane fade in active">
            <div class="col-sm-12 nopadding">
                <div class="audits-index">
                    <?php Pjax::begin(); ?>
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProviderAuditsSchedules,
                        'layout' => '{items}{pager}',
                        'emptyText' => 'No Completed Audits found. Please Check Scheduled Audits.',
                        //'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'attribute' => 'audit_schedule_name',
                                'format' => 'raw',
                                'header' => 'Scheduled Audit ID',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($model) {
                                    return '<a  data-pjax="0" target="_blank" href="' . yii::$app->urlManager->createUrl('audits/reports?id=' . Yii::$app->utils->encryptData($model->audit_schedule_id)) . '" title="View Scheduled Audit">' . $model->audit_schedule_name . '</a>';
                                }
                            ],
                            [
                                'attribute' => 'audit.hotel_id',
                                'format' => 'raw',
                                'header' => 'Hotel',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($model) {
                                    return $model->audit->hotel->hotel_name;
                                }
                            ],
                            [
                                'attribute' => 'audit.department_id',
                                'format' => 'raw',
                                'header' => 'Department',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($model) {
                                    return $model->audit->department->department_name;
                                }
                            ],
                            [
                                'attribute' => 'audit.checklist_id',
                                'format' => 'raw',
                                'header' => 'CheckList',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($model) {
                                    return $model->audit->checklist->cl_name;
                                }
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'header' => 'Status',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($model) {
                                    $value = "";
                                    switch (intval($model->status)) {
                                        case 0:$value = 'Scheduled';
                                            break;
                                        case 1:$value = 'In Progress';
                                            break;
                                        case 2:$value = 'Draft';
                                            break;
                                        case 3:$value = 'Completed';
                                            break;
                                        case 4:$value = 'Cancelled';
                                            break;
                                    }
                                    return $value;
                                }
                            ],
                            [
                                'attribute' => 'start_date',
                                'format' => 'raw',
                                'header' => 'Start Date',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($model) {
                                    $timestamp = strtotime($model->start_date);
                                    return Yii::$app->formatter->asDate($timestamp, 'php:d-m-Y');
                                }
                            ],
                            [
                                'attribute' => 'end_date',
                                'format' => 'raw',
                                'header' => 'End Date',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($model) {
                                    $timestamp = strtotime($model->end_date);
                                    return Yii::$app->formatter->asDate($timestamp, 'php:d-m-Y');
                                }
                            ],
                            [
                                'attribute' => 'updated_at',
                                'format' => 'raw',
                                'header' => 'Submission&nbsp;Date',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($model) {
                                    if ($model->status == 3) {
                                        $timestamp = strtotime($model->updated_at);
                                        return Yii::$app->formatter->asDate($timestamp, 'php:d-m-Y');
                                    }
                                    return '---';
                                }
                            ],
                        /* [
                          'attribute' => 'audit_name',
                          'format' => 'raw',
                          'header' => 'Audit ID',
                          'headerOptions' => ['class' => 'theadcolor'],
                          'value' => function ($model) {
                          return '<a href=' . yii::$app->urlManager->createUrl('audits/view-audit?id=' . Yii::$app->utils->encryptData($model->audit_id)) . ' title="View" target="_blank">' . $model->audit_name . '</a>';
                          }
                          ],
                          [
                          'attribute' => 'location_id',
                          'format' => 'raw',
                          'header' => 'Location',
                          'headerOptions' => ['class' => 'theadcolor'],
                          'value' => function ($model) {
                          return $model->location->locationCity->name;
                          }
                          ],


                          [
                          'attribute' => 'start_date',
                          'format' => 'raw',
                          'header' => 'Start Date',
                          'headerOptions' => ['class' => 'theadcolor'],
                          'value' => function ($model) {
                          $timestamp = strtotime($model->start_date);
                          return Yii::$app->formatter->asDate($timestamp, 'php:d-m-Y');
                          }
                          ],
                          [
                          'attribute' => 'end_date',
                          'format' => 'raw',
                          'header' => 'End Date',
                          'headerOptions' => ['class' => 'theadcolor'],
                          'value' => function ($model) {
                          $timestamp = strtotime($model->end_date);
                          return Yii::$app->formatter->asDate($timestamp, 'php:d-m-Y');
                          }
                          ], */
                        /*  [
                          'class' => 'yii\grid\ActionColumn',
                          'header' => 'Actions',

                          'headerOptions' => ['class' => 'theadcolor'],
                          'template' => '{delete}',
                          'buttons' => [

                          'delete' => function ($url, $model)
                          {
                          return '<a href="javascript:void(0)" title="Delete" class="delete_audit_btn" data-token ='.yii::$app->utils->encryptData($model->audit_id).'><i class="fa fa-trash-o" title="Delete"></i></a>';

                          },


                          ]
                          ], */
                        ],
                    ]);
                    ?>
                    <?php Pjax::end(); ?>
                </div>

            </div>
        </div>
    </div>
</div>


<div class="row auditid" style="display:none">
    <div class="tab-content">
        <div id="activeaudits" class="tab-pane fade in active">
            <?php if (intval($searchModel->show_child) == 0) { ?>
                <div class="col-sm-12 nopadding">
                    <div class="audits-index">
                        <?php Pjax::begin(); ?>
                        <?php
                        $buttons = '';
                        if (Yii::$app->authManager->checkPermissionAccess('audits/update')) {
                            $buttons .= '{update}';
                        }
                        if (Yii::$app->authManager->checkPermissionAccess('audits/delete')) {
                            $buttons .= '&nbsp;{delete}';
                        }
                        ?>
                        <?=
                        GridView::widget([
                            'dataProvider' => $dataProviderAudits,
                            'layout' => '{items}{pager}',
                            'emptyText' => 'No Scheduled audits found. Please Check Completed Audits for past audits.',
                            //'filterModel' => $searchModel,
                            'columns' => [
                                [
                                    'class' => 'kartik\grid\ExpandRowColumn',
                                    'width' => '50px',
                                    'enableRowClick' => false,
                                    'value' => function ($model, $key, $index, $column) {
                                        return GridView::ROW_COLLAPSED;
                                    },
                                    'detailUrl' => yii::$app->urlManager->createUrl('audits/get-row-details'),
                                    /* 'detail' => function ($model, $key, $index, $column) {
                                      return Yii::$app->controller->renderPartial('expandActiveAudits', ['model' => $model]);
                                      }, */
                                    'headerOptions' => ['class' => 'kartik-sheet-style'],
                                    'expandOneOnly' => true
                                ],
                                [
                                    'attribute' => 'audit_name',
                                    'format' => 'raw',
                                    'header' => 'Audit ID',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'value' => function ($model) {
                                        return '<a href=' . yii::$app->urlManager->createUrl('audits/view-audit?id=' . Yii::$app->utils->encryptData($model->audit_id)) . ' title="View" target="_blank">' . $model->audit_name . '</a>';
                                    }
                                ],
                                [
                                    'attribute' => 'location_id',
                                    'format' => 'raw',
                                    'header' => 'Location',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'value' => function ($model) {

                                        return $model->location->locationCity->name;
                                    }
                                ],
                                [
                                    'attribute' => 'hotel_id',
                                    'format' => 'raw',
                                    'header' => 'Hotel',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'value' => function ($model) {
                                        return $model->hotel->hotel_name;
                                    }
                                ],
                                [
                                    'attribute' => 'department_id',
                                    'format' => 'raw',
                                    'header' => 'Department',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'value' => function ($model) {
                                        return $model->department->department_name;
                                    }
                                ],
                                [
                                    'attribute' => 'checklist_id',
                                    'format' => 'raw',
                                    'header' => 'CheckList',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'value' => function ($model) {
                                        return $model->checklist->cl_name;
                                    }
                                ],
                                [
                                    'attribute' => 'start_date',
                                    'format' => 'raw',
                                    'header' => 'Start Date',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'value' => function ($model) {
                                        $timestamp = strtotime($model->start_date);
                                        return Yii::$app->formatter->asDate($timestamp, 'php:d-m-Y');
                                    }
                                ],
                                [
                                    'attribute' => 'end_date',
                                    'format' => 'raw',
                                    'header' => 'End Date',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'value' => function ($model) {
                                        $timestamp = strtotime($model->end_date);
                                        return Yii::$app->formatter->asDate($timestamp, 'php:d-m-Y');
                                    }
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header' => 'Actions',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'template' => $buttons,
                                    'buttons' => [
                                        'update' => function ($url, $model) {
                                            return Html::a('<i class="fa fa-edit"></i>', ['audits/update', 'id' => Yii::$app->utils->encryptData($model->audit_id)], [
                                                        'title' => Yii::t('yii', 'Edit'),
                                            ]);
                                        },
                                        'delete' => function ($url, $model) {
                                            return '<a href="javascript:void(0)" title="Delete" class="delete_audit_info_btn" data-token =' . yii::$app->utils->encryptData($model->audit_id) . '><i class="fa fa-trash" title="Delete"></i></a>';
                                        },
                                    ]
                                ],
                            ],
                        ]);
                        ?>
                        <?php Pjax::end(); ?>
                    </div>
                </div>
            <?php } else if (intval($searchModel->show_child) == 1) { ?>

                <div class="col-sm-12 nopadding">
                    <div class="audits-index">
                        <?php Pjax::begin(); ?>
                        <?php
                        $buttons = '';
                        if (Yii::$app->authManager->checkPermissionAccess('audits/update')) {
                            $buttons .= '{update}';
                        }
                        if (Yii::$app->authManager->checkPermissionAccess('audits/delete')) {
                            $buttons .= '&nbsp;{delete}';
                        }
                        ?>
                        <?=
                        GridView::widget([
                            'dataProvider' => $dataProviderAuditsSchedulesChilds,
                            'layout' => '{items}{pager}',
                            'emptyText' => 'No Scheduled audits found. Please Check Completed Audits for past audits.',
                            //'filterModel' => $searchModel,
                            'columns' => [
                                [
                                    'attribute' => 'audit_schedule_name',
                                    'format' => 'raw',
                                    'header' => 'Scheduled Audit ID',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'value' => function ($model) {
                                        return '<a  data-pjax="0" target="_blank" href="' . yii::$app->urlManager->createUrl('audits/reports?id=' . Yii::$app->utils->encryptData($model->audit_schedule_id)) . '" title="View Scheduled Audit">' . $model->audit_schedule_name . '</a>';
                                    }
                                ],
                                [
                                    'attribute' => 'location_id',
                                    'format' => 'raw',
                                    'header' => 'Location',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'value' => function ($model) {
                                        return $model->audit->location->locationCity->name;
                                    }
                                ],
                                [
                                    'attribute' => 'hotel_id',
                                    'format' => 'raw',
                                    'header' => 'Hotel',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'value' => function ($model) {
                                        return $model->audit->hotel->hotel_name;
                                    }
                                ],
                                [
                                    'attribute' => 'department_id',
                                    'format' => 'raw',
                                    'header' => 'Department',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'value' => function ($model) {
                                        return $model->audit->department->department_name;
                                    }
                                ],
                                [
                                    'attribute' => 'checklist_id',
                                    'format' => 'raw',
                                    'header' => 'CheckList',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'value' => function ($model) {
                                        return $model->audit->checklist->cl_name;
                                    }
                                ],
                                [
                                    'attribute' => 'status',
                                    'format' => 'raw',
                                    'header' => 'Status',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'value' => function ($model) {
                                        $value = "";
                                        switch (intval($model->status)) {
                                            case 0:$value = 'Scheduled';
                                                break;
                                            case 1:$value = 'In Progress';
                                                break;
                                            case 2:$value = 'Draft';
                                                break;
                                            case 3:$value = 'Completed';
                                                break;
                                            case 4:$value = 'Cancelled';
                                                break;
                                        }
                                        return $value;
                                    }
                                ],
                                [
                                    'attribute' => 'start_date',
                                    'format' => 'raw',
                                    'header' => 'Start Date',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'value' => function ($model) {
                                        $timestamp = strtotime($model->start_date);
                                        return Yii::$app->formatter->asDate($timestamp, 'php:d-m-Y');
                                    }
                                ],
                                [
                                    'attribute' => 'end_date',
                                    'format' => 'raw',
                                    'header' => 'End Date',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'value' => function ($model) {
                                        $timestamp = strtotime($model->end_date);
                                        return Yii::$app->formatter->asDate($timestamp, 'php:d-m-Y');
                                    }
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header' => 'Actions',
                                    'headerOptions' => ['class' => 'theadcolor'],
                                    'template' => $buttons,
                                    'buttons' => [
                                        'update' => function ($url, $model) {
                                            return Html::a('<i class="fa fa-edit"></i>', ['audits/update', 'id' => Yii::$app->utils->encryptData($model->audit_id)], [
                                                        'title' => Yii::t('yii', 'Edit'),
                                            ]);
                                        },
                                        'delete' => function ($url, $model) {
                                            return '<a href="javascript:void(0)" title="Delete" class="delete_audit_info_btn" data-token =' . yii::$app->utils->encryptData($model->audit_id) . '><i class="fa fa-trash" title="Delete"></i></a>';
                                        },
                                    ]
                                ],
                            ],
                        ]);
                        ?>
                        <?php Pjax::end(); ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>





<!-- ------------------------------------------Modele POPup start here-------------------------->
<div id="deleteAuditPopup" class="modal fade" role="dialog">
    <?php ActiveForm::begin(['id' => 'delete_role_form', 'action' => yii::$app->urlManager->createUrl('audits/delete')]) ?>
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="color: #fff!important; opacity: 1;"
                        aria-hidden="true">
                    �
                </button>
                <h4 class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body clearfix">
                <input type="hidden" name="deletable_audit_id" id="deletable_audit_id" value=""/>
                <div class="col-sm-12" style="margin-top: 20px;">
                    <label>
                        Are you sure you want to delete this audit along with child audits? You can't undo this action.
                    </label>
                </div>
            </div>
            <div class="modal-footer clearfix" style="border-top: none; margin-top: 5px;">
                <div class="col-sm-12">
                    <input class="btn btn-danger" type="submit" value="Delete">
                    <button type="button" class="btn btn-Clear" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>



<!----------------------Cancel Schedule audit Popup Start hare -->
<div id="deletepopup" class="modal fade" role="dialog">
    <?php ActiveForm::begin(['id' => 'cancel_auditschedule_form', 'action' => yii::$app->urlManager->createUrl('audits/cancel'), 'method' => 'post',]) ?>
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="color: #fff!important; opacity: 1;"
                        aria-hidden="true">
                    �
                </button>
                <h4 class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body clearfix">
                <input type="hidden" name="update_auditschedule_id" id="update_auditschedule_id_value" value=""/>
                <input type="hidden" name="fromIndex" value="1"/>
                <div class="col-sm-12" style="margin-top: 20px;">
                    <label>
                        Are you sure you want to cancel this Audit? You can't undo this action.
                    </label>
                </div>
            </div>
            <div class="modal-footer clearfix" style="border-top: none; margin-top: 5px;">
                <div class="col-sm-12">
                    <input class="btn btn-danger" type="submit" value="Submit">
                    <button type="button" class="btn btn-Clear" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>

<div id="deletepopupModal" class="modal fade" role="dialog">
    <?php ActiveForm::begin(['id' => 'delete_auditschedule_form', 'action' => yii::$app->urlManager->createUrl('audits/delete-audit'), 'method' => 'post',]) ?>
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="color: #fff!important; opacity: 1;"
                        aria-hidden="true">
                    �
                </button>
                <h4 class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body clearfix">
                <input type="hidden" name="update_auditschedule_id" id="delete_auditschedule_id_value" value=""/>
                <input type="hidden" name="fromIndex" value="1"/>
                <div class="col-sm-12" style="margin-top: 20px;">
                    <label>
                        Are you sure you want to delete this Audit? You can't undo this action.
                    </label>
                </div>
            </div>
            <div class="modal-footer clearfix" style="border-top: none; margin-top: 5px;">
                <div class="col-sm-12">
                    <input class="btn btn-danger" type="submit" value="Submit">
                    <button type="button" class="btn btn-Clear" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>