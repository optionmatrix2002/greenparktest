<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\assets\AppAsset;
use yii\web\View;
use yii\web\JqueryAsset;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use app\models\Hotels;
use app\models\Departments;
use app\models\Checklists;
use app\models\User;
use app\models\Interval;

/* @var $this yii\web\View */
/* @var $model app\models\Audits */

$this->title = 'View Audit: ' . $model->audit_name;
$this->params['breadcrumbs'][] = ['label' => 'Audits', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->audit_id, 'url' => ['view', 'id' => $model->audit_id]];

AppAsset::register($this);
View::registerJsFile(yii::$app->urlManager->createUrl('js/modelpopup.js'), ['depends' => JqueryAsset::className()]);
$this->params['breadcrumbs'][] = 'Update';

$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuAudits").addClass("active");
', \yii\web\View::POS_END);

?>
<?php
$this->registerJs('
 $("document").ready(function()
 {
 
  $(\'.datetimepicker\').datetimepicker({
   format: \'YYYY-MM-DD\',
  });

 });
');

$this->registerJsFile(
    'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js', ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
    'https://cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/d004434a5ff76e7b97c8b07c01f34ca69e635d97/src/js/bootstrap-datetimepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerCssFile("https://cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/d004434a5ff76e7b97c8b07c01f34ca69e635d97/build/css/bootstrap-datetimepicker.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()]
]);
?>

<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
    <p id="description-text">
        Audits can be viewed from here.
    </p>
</div>

<div class="col-sm-12 col-lg-12 col-md-12">
    <a href="<?= yii::$app->urlManager->createUrl('audits'); ?>" class="btn btn-default pull-right"><i
                class="fa fa-arrow-left"></i>&nbsp;Back</a>
</div>

<h2>Scheduled Audits </h2>
<div class="row">
    <div class="tab-content">
        <div id="activeaudits" class="tab-pane fade in active">
            <div class="col-sm-12 col-lg-12 col-md-12 nopadding">
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
                                'header' => 'Auditor Name',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($auditScheduleSearch) {

                                    return $auditScheduleSearch->auditor->first_name . ' ' . $auditScheduleSearch->auditor->last_name;
                                }

                            ],
                            [
                                'attribute' => 'deligation_user_id',
                                'format' => 'raw',
                                'header' => 'Delegated Auditor',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($auditScheduleSearch) {
                                    return $auditScheduleSearch->deligation_user_id == 0 ? '--' : $auditScheduleSearch->deligationUser->first_name;
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

                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>


<h2><?= $this->title ?></h2>

<div class="audits-update">


    <div class="row" style="margin-top: 10px;">
        <div class="user-form"
        ">

        <?php $form = ActiveForm::begin(); ?>
        <div class="col-sm-12 col-lg-12 col-md-12 margintop10">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label class="required-label">Location :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'location_id')->dropDownList(ArrayHelper::map(\app\models\Locations::find()->where(['is_deleted' => 0])->all(), 'location_id', 'locationCity.name'), ['prompt' => 'Select Location', 'disabled' => !$model->isNewRecord])->label(false);
                    ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-12 col-md-12 margintop10">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label class="required-label">Hotel :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="input-group col-sm-6">

                    <?php
                    if ($model->isNewRecord) {

                        echo $form->field($model, 'hotel_id')->widget(DepDrop::classname(), [
                            'options' => ['id' => 'hotel_id'],
                            'pluginOptions' => [
                                'depends' => ['audits-location_id'],
                                'placeholder' => 'Select Hotel',

                                'url' => Url::to(['audits/hotel'])
                            ]
                        ])->label(false);

                    } else {
                        $hotel_data = empty($model->hotel_id) ? [] : [$model->hotel_id => $model->hotel->hotel_name];
                        echo $form->field($model, 'hotel_id')->dropDownList($hotel_data, ['disabled' => 'disabled'])->label(false);
                    }

                    ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-12 col-md-12 margintop10">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label class="required-label">Department :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="input-group col-sm-6">
                    <?php
                    if ($model->isNewRecord) {
                        echo $form->field($model, 'department_id')->widget(DepDrop::classname(), [
                            'options' => ['id' => 'department_id'],
                            'pluginOptions' => [
                                'depends' => ['hotel_id'],
                                'placeholder' => 'Select Hotel',
                                'url' => Url::to(['audits/department'])
                            ]
                        ])->label(false);
                    } else {
                        $department_data = empty($model->department_id) ? [] : [$model->department_id => $model->department->department_name];
                        echo $form->field($model, 'department_id')->dropDownList($department_data, ['disabled' => 'disabled'])->label(false);
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-12 col-md-12 margintop10">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label class="required-label">Checklist :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="input-group col-sm-6">
                    <?php
                    if ($model->isNewRecord) {
                        echo $form->field($model, 'checklist_id')->widget(DepDrop::classname(), [
                            'options' => ['id' => 'checklist_id'],
                            'pluginOptions' => [
                                'depends' => ['department_id'],
                                'placeholder' => 'Select Checklist',
                                'url' => Url::to(['audits/check-list'])
                            ]
                        ])->label(false);
                    } else {
                        $checklist_data = empty($model->checklist_id) ? [] : [$model->checklist_id => $model->checklist->cl_name];
                        echo $form->field($model, 'checklist_id')->dropDownList($checklist_data, ['disabled' => 'disabled'])->label(false);
                    }

                    ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-12 col-md-12 margintop10">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label class="required-label">Frequency :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="input-group col-sm-6">
                    <?php
                    $frequency = Checklists::find()->where(['checklist_id' => $model->checklist_id])->one();
                    $frequencyName = Interval::find()->where(['interval_id' => $frequency->cl_frequency_value])->one();
                    echo $form->field($model, 'checklistfrequency')->textInput(['value' => $frequencyName->interval_name, 'class' => 'form-control', 'disabled' => 'disabled'])->label(false);
                    ?>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-lg-12 col-md-12 margintop10">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label>Delegation Flag :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="input-group col-sm-6 textbox-padding">
                    <?= $form->field($model, 'deligation_flag')->checkbox(['disabled' => 'disabled'], false)->label(false); ?>

                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>


</div>

