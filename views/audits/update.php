<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\assets\AppAsset;
use yii\web\View;
use yii\web\JqueryAsset;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Audits */


$this->title = 'Update  Audit : ' . $nameAudits;
$this->params['breadcrumbs'][] = ['label' => 'Audits', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->audit_id, 'url' => ['view', 'id' => $model->audit_id]];

AppAsset::register($this);
View::registerCssFile('//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
View::registerJsFile(yii::$app->urlManager->createUrl('js/audits.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/modelpopup.js'), ['depends' => JqueryAsset::className()]);
$this->params['breadcrumbs'][] = 'Update';


$action = '';
if (!$auditScheduleSearch->isNewRecord) {
    $action = yii::$app->urlManager->createUrl(['audits/update-user', 'id' => yii::$app->utils->encryptData($auditScheduleSearch->audit_schedule_id)]);
}
?>


<?php $buttons = '';

if (Yii::$app->authManager->checkPermissionAccess('audits/update')) {
    $buttons .= '{update}&nbsp;';
}
if (Yii::$app->authManager->checkPermissionAccess('audits/delete')) {
    $buttons .= '{cancel}&nbsp;{delete}';
}
?>

<div class="wa-notification wa-notification-alt">
	<span class="wa-iconBoxed"> <span class="fa fa-file-text-o header-icon-fontcolor"></span>
	</span>

    <p id="description-text">Scheduled audits can be managed or cancelled from here.</p>


</div>

<div class="col-sm-12 col-lg-12 col-md-12">
    <a href="<?= yii::$app->urlManager->createUrl('audits'); ?>"
       class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
</div>

<h2> Scheduled Audits </h2>

<div class="schedule-audit pull-right">
    <button class="btn btn-success create-child-audit"><i class="fa fa-plus"></i>&nbsp;Schedule Audit</button>
</div>

<div class="row">
    <div class="tab-content">
        <div id="activeaudits" class="tab-pane fade in active">
            <div class="col-sm-12 nopadding">
                <div class="audits-index">

                    <?= GridView::widget([
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
                                    //return '<a href="" title="View Scheduled Audit" >'.$model->audit_schedule_name.'</a>';
                                    return '<a href="' . yii::$app->urlManager->createUrl('audits/reports?id=' . Yii::$app->utils->encryptData($model->audit_schedule_id)) . '" title="View Scheduled Audit" target="_blank">' . $model->audit_schedule_name . '</a>';

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
                                    'update' => function ($url, $model) {
                                        // return '<a href="javascript:void(0)" title="Edit" class="edit_auditschedule_info_btn" data-token ='.yii::$app->utils->encryptData($model->audit_schedule_id).'><i class="fa fa-edit" title="Edit"></i></a>';
                                        return (in_array($model->status, [1, 2,3, 4])) ? '-' : '<a href="javascript:void(0)" title="Edit" class="edit_auditschedule_info_btn" data-token =' . yii::$app->utils->encryptData($model->audit_schedule_id) . '><i class="fa fa-edit" title="Edit"></i></a>';
                                    },

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
<div class="audits-update">
    <?= $this->render('_form', [
        'model' => $model,
        'auditLocationsModel' => $auditLocationsModel,
        'auditsSchedulesModel' => $auditsSchedulesModel,
        'auditScheduleSearch' => $auditScheduleSearch
    ]) ?>

</div>

<!----------------------------------------------Update auditor POPUP start here---------------------------- -->
<?php
$newRoleform = ActiveForm::begin(['id' => 'updateaudit_user_form', 'action' => yii::$app->urlManager->createUrl('audits/update-audit-user'), 'method' => 'post',])
?>
<div id="edituserspop" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Audit</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?php $auditScheduleModel = new \app\models\AuditsSchedules();
                        ?>
                        <input type="hidden" name="update_auditschedule_id" id="update_auditschedule_id" value=""/>
                        <input type="hidden" id="edit_user_id"
                               value="<?= yii::$app->urlManager->createUrl('audits/get-audit-user-id'); ?>"/>

                        <div class="col-sm-3 nopadding">
                            <label class="required-label">Start Date : </label>
                        </div>

                        <div class="col-sm-9 nopadding">

                            <div class="col-sm-10 nopadding">
                                <?php echo $newRoleform->field($auditScheduleModel, 'start_date')->textInput(['value' => $model->start_date ? Yii::$app->formatter->asDate($model->start_date, "php:d-m-Y") : '', 'class' => 'schedule-update scheduler form-control', 'id' => 'scheduleDateStart'])->label(false); ?>
                            </div>
                        </div>


                        <div class="col-sm-3 nopadding">
                            <label class="required-label">End Date : </label>
                        </div>

                        <div class="col-sm-9 nopadding">

                            <div class="col-sm-10 nopadding">
                                <?php echo $newRoleform->field($auditScheduleModel, 'end_date')->textInput(['value' => $model->end_date ? Yii::$app->formatter->asDate($model->end_date, "php:d-m-Y") : '', 'class' => 'schedule-update scheduler form-control', 'id' => 'scheduleDateEnd'])->label(false);
                                ?>
                            </div>
                        </div>


                        <div class="col-sm-3 nopadding">
                            <label class="required-label">Auditor : </label>
                        </div>
                        <div class="col-sm-9 nopadding">

                            <div class="col-sm-10 nopadding">

                                <?php
                                $users = \app\models\Audits::getAuditorsList($model->department_id, $model->hotel_id, $model->location_id);
                                $users = ArrayHelper::map($users, 'user_id', function ($element) {
                                    return $element['first_name'] . ' ' . $element['last_name'];
                                });
                                echo $newRoleform->field($auditScheduleModel, 'auditor_id')
                                    ->widget(Select2::classname(), ['data' => $users, 'showToggleAll' => false, 'language' => 'en', 'options' => ['id'=>'auditsschedulesAuditorId','class' => 'form-control schedule-update', 'placeholder' => 'Auditor'], 'pluginOptions' => ['allowClear' => true]])
                                    ->label(false);
                                ?>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer clearfix" style="border-top: none; margin-top: 5px;">
                        <div class='col-sm-12' style="margin-top: 20px;margin-left: -29px;">
                            <div class="col-sm-2 nopadding">
                            </div>
                            <div class="col-sm-9 nopadding text-center">
                                <button disabled= true type="submit" id="audit_update_user" class="btn btn-success"><i
                                            class=""></i>Update
                                </button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>


<!----------------------------------------------Create audit POPUP start here---------------------------- -->

<div id="schedule-child-aduit" class="modal fade" role="dialog">
    <?php
    $newRoleform = ActiveForm::begin(['id' => 'create_audit_form', 'action' => yii::$app->urlManager->createUrl('audits/create-audit'), 'method' => 'post',])
    ?>
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create Audit</h4>
            </div>
            <?php $auditScheduleModel = new \app\models\AuditsSchedules();
            $lastAuditEndDate = \app\models\AuditsSchedules::getLastAuditEndDate($model->audit_id);
            ?>
            <?php echo $newRoleform->field($auditScheduleModel, 'audit_id')->hiddenInput(['value' => $model->audit_id, 'class' => 'scheduler form-control'])->label(false); ?>
            <?php echo Html::hiddenInput('lastAuditDate', $lastAuditEndDate, ['class' => 'lastAuditEndDate']) ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">

                        <div class="col-sm-3 nopadding">
                            <label class="required-label">Start Date : </label>
                        </div>

                        <div class="col-sm-9 nopadding">

                            <div class="col-sm-10 nopadding">

                                <?php echo $newRoleform->field($auditScheduleModel, 'start_date')->textInput(['value' => $auditScheduleModel->start_date ? Yii::$app->formatter->asDate($auditScheduleModel->start_date, "php:d-m-Y") : '', 'class' => 'scheduler-create form-control', 'id' => 'auditDateStart'])->label(false); ?>
                            </div>
                        </div>


                        <div class="col-sm-3 nopadding">
                            <label class="required-label">End Date : </label>
                        </div>

                        <div class="col-sm-9 nopadding">

                            <div class="col-sm-10 nopadding">
                                <?php echo $newRoleform->field($auditScheduleModel, 'end_date')->textInput(['value' => $auditScheduleModel->end_date ? Yii::$app->formatter->asDate($auditScheduleModel->end_date, "php:d-m-Y") : '', 'class' => 'scheduler-create form-control', 'id' => 'auditDateEnd'])->label(false);
                                ?>
                            </div>
                        </div>


                        <div class="col-sm-3 nopadding">
                            <label class="required-label">Auditor : </label>
                        </div>
                        <div class="col-sm-9 nopadding">

                            <div class="col-sm-10 nopadding">

                                <?php
                                $users = \app\models\Audits::getAuditorsList($model->department_id, $model->hotel_id, $model->location_id);
                                $users = ArrayHelper::map($users, 'user_id', function ($element) {
                                    return $element['first_name'] . ' ' . $element['last_name'];
                                });
                                echo $newRoleform->field($auditScheduleModel, 'auditor_id')
                                    ->widget(Select2::classname(), ['data' => $users, 'showToggleAll' => false, 'language' => 'en', 'options' => ['class' => 'scheduler-create form-control', 'placeholder' => 'Auditor'], 'pluginOptions' => ['allowClear' => true]])
                                    ->label(false);
                                ?>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer clearfix" style="border-top: none; margin-top: 5px;">
                        <div class='col-sm-12' style="margin-top: 20px;margin-left: -29px;">
                            <div class="col-sm-2 nopadding">
                            </div>
                            <div class="col-sm-9 nopadding text-center">
                                <button disabled=true type="button" id="audit_create_user" class="btn btn-success"><i
                                            class=""></i>Save
                                </button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>


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