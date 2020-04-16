<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use app\models\Hotels;
use app\models\Departments;
use app\models\Checklists;
use app\models\User;
use app\models\Interval;
use app\assets\AppAsset;
use yii\web\JqueryAsset;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Audits */
/* @var $form yii\widgets\ActiveForm */
?>

<?php


AppAsset::register($this);
View::registerCssFile('//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
View::registerJsFile(yii::$app->urlManager->createUrl('js/audits.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/modelpopup.js'), [
    'depends' => JqueryAsset::className()
]);

$id = Yii::$app->request->get('id');

$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuAudits").addClass("active");
', \yii\web\View::POS_END);
?>

<div class="container-fluid">
    <h2><?= $this->title ?></h2>
</div>
<?php if ($model->isNewRecord) { ?>
    <div class="wa-notification wa-notification-alt">
	<span class="wa-iconBoxed"> <span class="fa fa-file-text-o header-icon-fontcolor"></span>
	</span>
        <?php if (!empty($id)) { ?>
            <p id="description-text">Scheduled audits can be managed or cancelled from here.</p>
        <?php } else { ?>
            <p id="description-text">New Audits can be scheduled from here.</p>
        <?php } ?>
    </div>

    <div class="col-sm-12 col-lg-12 col-md-12">
        <a href="<?= yii::$app->urlManager->createUrl('audits'); ?>"
           class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
    </div>
<?php } ?>
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
                <?= $form->field($model, 'location_id')->dropDownList(ArrayHelper::map(\app\models\Locations::find()->where(['is_deleted' => 0])->all(), 'location_id', 'locationCity.name'), ['prompt' => 'Select Location', 'disabled' => !$model->isNewRecord])->label(false); ?>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-3 col-lg-3 col-md-3">
            <label class="required-label">Hotel :</label>
        </div>
        <div class="col-sm-9 col-lg-9 col-md-9">
            <div class="input-group col-sm-6">
                <?php

                $hotels = \app\models\Audits::getHotels($model->location_id);
                $hotels = ArrayHelper::map($hotels, 'id', 'name');
                $hotels = $hotels ? $hotels : [];

                if ($model->isNewRecord) {
                    echo $form->field($model, 'hotel_id')
                        ->widget(DepDrop::classname(), [
                            'options' => [
                                'id' => 'hotel_id'
                            ],
                            'data' => $hotels,
                            'pluginOptions' => [
                                'depends' => [
                                    'audits-location_id'
                                ],
                                'placeholder' => 'Select Hotel',
                                'url' => Url::to([
                                    'audits/hotel'
                                ])
                            ]
                        ])
                        ->label(false);
                } else {
                    echo $form->field($model, 'hotel_id')
                        ->dropDownList($hotels, [
                            'disabled' => !$model->isNewRecord
                        ])
                        ->label(false);
                }
                ?>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-3 col-lg-3 col-md-3">
            <label class="required-label">Department :</label>
        </div>
        <div class="col-sm-9 col-lg-9 col-md-9">
            <div class="input-group col-sm-6">
                <?php

                $departments = \app\models\Audits::getHotelDepartments($model->hotel_id);
                $departments = ArrayHelper::map($departments, 'department_id', function ($element) {
                    return $element['hotel']['hotel_name'] . '-' . $element['department']['department_name'];
                });

                $departments = $departments ? $departments : [];

                if ($model->isNewRecord) {
                    echo $form->field($model, 'department_id')
                        ->widget(DepDrop::classname(), [
                            'options' => [
                                'id' => 'department_id'
                            ],
                            'data' => $departments,
                            'pluginOptions' => [
                                'depends' => [
                                    'hotel_id'
                                ],
                                'placeholder' => 'Select Department',
                                'url' => Url::to([
                                    'audits/department'
                                ])
                            ]
                        ])
                        ->label(false);
                } else {

                    echo $form->field($model, 'department_id')
                        ->dropDownList($departments, [
                            'disabled' => 'disabled'
                        ])
                        ->label(false);
                }
                ?>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-3 col-lg-3 col-md-3">
            <label class="required-label">Checklist :</label>
        </div>
        <div class="col-sm-9 col-lg-9 col-md-9">
            <div class="input-group col-sm-6">
                <?php
                $checklist_data = empty($model->checklist_id) ? [] : [
                    $model->checklist_id => $model->checklist->cl_name
                ];
                if ($model->isNewRecord) {
                    echo $form->field($model, 'checklist_id')
                        ->widget(DepDrop::classname(), [
                            'options' => [
                                'id' => 'checklist_id'
                            ],
                            'data' => $checklist_data,
                            'pluginOptions' => [
                                'depends' => [
                                    'department_id',
                                    'hotel_id'
                                ],
                                'placeholder' => 'Select Checklist',
                                'url' => Url::to([
                                    'audits/check-list'
                                ])
                            ]
                        ])
                        ->label(false);
                } else {
                    echo $form->field($model, 'checklist_id')
                        ->dropDownList($checklist_data, [
                            'disabled' => 'disabled'
                        ])
                        ->label(false);
                }
                ?>
            </div>
        </div>
    </div>


    <input type="hidden" id="checklist_url"
           value="<?= yii::$app->urlManager->createUrl('audits/get-check-list-frequency'); ?>">


    <?php if ($model->checklist_id) { ?>
        <div class="col-sm-12 margintop10">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label class="required-label">Frequency :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="input-group col-sm-6">
                    <?php

                    $frequency = Checklists::find()->where([
                        'checklist_id' => $model->checklist_id
                    ])->one();
                    $frequencyName = Interval::find()->where([
                        'interval_id' => $frequency->cl_frequency_value
                    ])->one();

                    echo $form->field($model, 'checklist_id')
                        ->textInput([
                            'id' => 'getFrequencyName',
                            'value' => $frequencyName->interval_name,
                            'class' => 'form-control',
                            'disabled' => 'disabled'
                        ])
                        ->label(false);
                    ?>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="col-sm-12 margintop10">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label class="required-label">Frequency :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="input-group col-sm-6">

                    <?php

                    echo $form->field($model, 'checklistfrequency')
                        ->textInput([
                            'class' => 'form-control',
                            'id' => 'getFrequencyName',
                            'disabled' => 'disabled'
                        ])
                        ->label(false);
                    ?>

                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($model->isNewRecord) { ?>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-3 col-lg-3 col-md-3">
            <label class="required-label">Auditor :</label>
        </div>
        <div class="col-sm-9 col-lg-9 col-md-9">
            <div class="input-group col-sm-6">

                <?php
                $users = \app\models\Audits::getAuditorsList($model->department_id, $model->hotel_id, $model->location_id);

                $users = ArrayHelper::map($users, 'user_id', function ($element) {
                    return $element['first_name'] . ' ' . $element['last_name'];
                });


                $users = $users ? $users : [];
                if ($model->isNewRecord) {
                    echo $form->field($model, 'user_id')
                        ->widget(DepDrop::classname(), [
                            'options' => [
                                'id' => 'user_id'
                            ],
                            'data' => $users,
                            'pluginOptions' => [
                                'depends' => [
                                    'department_id',
                                    'hotel_id',
                                    'audits-location_id'
                                ],
                                'placeholder' => 'Select Auditor',
                                'url' => Url::to([
                                    'audits/get-auditor-list'
                                ])
                            ]
                        ])
                        ->label(false);
                } else {
                    echo $form->field($model, 'user_id')->widget(Select2::classname(), ['data' => ArrayHelper::map(\app\models\User::find()->where(['is_deleted' => 0, 'user_type' => 2, 'is_active' => 1])->all(), 'user_id', function ($element) {
                        return $element['first_name'] . ' ' . $element['last_name'];
                    }), 'showToggleAll' => false, 'language' => 'en', 'options' => ['placeholder' => 'Select Auditor', 'disabled' => !$model->isNewRecord], 'pluginOptions' => ['allowClear' => true]])->label(false);
                }

                ?>


            </div>
        </div>
    </div>



        <div class="col-sm-12 margintop10">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label class="required-label">Start Date :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'start_date')->textInput(['value' => $model->start_date ? Yii::$app->formatter->asDate($model->start_date, "php:d-m-Y") : '', 'class' => 'datetimepicker form-control', 'id' => 'dateStart', 'disabled' => !$model->isNewRecord])->label(false); ?>
                </div>
            </div>
        </div>

        <div class="col-sm-12 margintop10">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label class="required-label">End Date :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'end_date')->textInput(['value' => Yii::$app->formatter->asDate($model->end_date, 'php:d-m-Y'), 'class' => 'datetimepicker form-control', 'id' => 'dateEnd',])->label(false); ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="col-sm-12 margintop10">
        <div class="col-sm-3 col-lg-3 col-md-3">
            <label>Delegation Flag :</label>
        </div>
        <div class="col-sm-9 col-lg-9 col-md-9">
            <div class="input-group col-sm-6 textbox-padding">
                <?= $form->field($model, 'deligation_flag')->checkbox([], false)->label(false); ?>

            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-3 col-lg-3 col-md-3">
            <label></label>
        </div>
        <div class="col-sm-9 col-lg-9 col-md-9">
            <div class="col-sm-6 input-group">
                <?= Html::submitButton($model->isNewRecord ? 'Save' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
                <?= Html::a('Cancel', ['/audits'], ['class' => 'btn btn-default']); ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
</div>
