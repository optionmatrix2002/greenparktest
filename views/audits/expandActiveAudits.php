<div class="row">
    <div class="tab-content">
        <div id="activeaudits" class="tab-pane fade in active">
            <div class="col-sm-12 nopadding">
                <div class="audits-index">
                    <?php
                    $buttons = '';
                    if (Yii::$app->authManager->checkPermissionAccess('audits/delete')) {
                        $buttons .= '{cancel}&nbsp;{delete}';
                    }
                    ?>
                    <?= \yii\grid\GridView::widget([
                        'dataProvider' => $dataProvider,
                        'layout' => '{items}{pager}',
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
                                'attribute' => 'audit_id',
                                'format' => 'raw',
                                'header' => 'Audit ID',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($auditScheduleSearch) {

                                    return $auditScheduleSearch->audit->audit_name;
                                }

                            ],

                            [
                                'attribute' => 'auditor_id',
                                'format' => 'raw',
                                'header' => 'Scheduled Auditor',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($auditScheduleSearch) {

                                    return $auditScheduleSearch->auditor ? $auditScheduleSearch->auditor->first_name . ' ' . $auditScheduleSearch->auditor->last_name : '--';
                                }

                            ],
                            [
                                'attribute' => 'deligation_user_id',
                                'format' => 'raw',
                                'header' => 'Delegated Auditor',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($auditScheduleSearch) {
                                    return $auditScheduleSearch->deligation_user_id == 0 ? '--' : (($auditScheduleSearch->deligationUser) ? $auditScheduleSearch->deligationUser->first_name : '');
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
                                'header' => 'Audit Submission Date',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($model) {
                                    if ($model->status == 3) {
                                        $timestamp = strtotime($model->updated_at);
                                        return Yii::$app->formatter->asDate($timestamp, 'php:d-m-Y');
                                    }
                                    return '---';
                                }
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'header' => 'Status',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($auditScheduleSearch) {
                                    switch ($auditScheduleSearch->status) {
                                        case 0:
                                            $status = 'Scheduled';
                                            break;
                                        case 1:
                                            $status = 'In-Progress';
                                            break;
                                        case 2:
                                            $status = 'Draft';
                                            break;
                                        case 3:
                                            $status = 'Completed';
                                            break;
                                        case 4:
                                            $status = 'Cancelled';
                                            break;
                                    }
                                    return $status;
                                }
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Actions',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'template' => $buttons,
                                'buttons' => [
                                    'cancel' => function ($url, $model) {
                                        return (in_array($model->status, [1, 2, 3, 4])) ? '-' : '<a href="javascript:void(0)" title="Cancel" class="cancel_auditschedule_info_btn" data-token =' . yii::$app->utils->encryptData($model->audit_schedule_id) . '><i class="fa fa-close" title="Cancel"></i></a>';
                                    },
                                    'delete' => function ($url, $model) {
                                        return  '<a href="javascript:void(0)" title="Delete" class="delete_auditschedule_info_btn" data-token =' . yii::$app->utils->encryptData($model->audit_schedule_id) . '><i class="fa fa-trash" title="Delete"></i></a>';
                                    },
                                ]
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>