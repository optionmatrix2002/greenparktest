<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\AppAsset;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use kartik\select2\Select2;

AppAsset::register($this);

View::registerJsFile(yii::$app->urlManager->createUrl('js/tickets.js'), ['depends' => JqueryAsset::className()]);

/* @var $this yii\web\View */
/* @var $model app\models\Tickets */
/* @var $form yii\widgets\ActiveForm */


AppAsset::register($this);
View::registerCssFile('//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
/* View::registerJsFile(yii::$app->urlManager->createUrl('js/audits.js'), [
    'depends' => JqueryAsset::className()
]); */
View::registerJsFile(yii::$app->urlManager->createUrl('js/modelpopup.js'), [
    'depends' => JqueryAsset::className()
]);

$this->registerJs('
$(".nav-bids").removeClass("active");
$("#tickets").addClass("active");
', \yii\web\View::POS_END);

$id = Yii::$app->request->get('id');
?>

<?php
$this->registerJs('
 $("document").ready(function()
 {
 
  $(\'.datetimepicker\').datetimepicker({
   format: \'DD-MM-YYYY\',
  });

 });
');

?>
<div class="container-fluid">
    <h2><?= $this->title ?></h2>
</div>
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
    <?php if (!empty($id)) { ?>

        <p id="description-text"> Ticket details can be edited from here.</p>
    <?php } else { ?>
        <p id="description-text">All ticketing activites including creation, assignments and tracking can be managed
            here.</p>
    <?php } ?>
</div>
<div class="col-md-12">
    <a href="<?= yii::$app->urlManager->createUrl('tickets'); ?>" class="btn btn-default pull-right"><i
                class="fa fa-arrow-left"></i>&nbsp;Back</a>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="col-md-12 col-sm-12 col-lg-12">
        <div class="user-form">

            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            <div class="col-md-12 col-sm-12 col-lg-12 margintop10">
                <div class="col-sm-3 ">
                    <label class="required-label">Location :</label>
                </div>
                <div class="col-md-9 col-sm-9 col-lg-9">
                    <div class="input-group col-sm-6">
                        <?= $form->field($model, 'location_id')->dropDownList(ArrayHelper::map(\app\models\Locations::find()->where(['is_deleted' => 0])->all(), 'location_id', 'locationCity.name'), ['prompt' => 'Select Location', 'disabled' => !$model->isNewRecord])->label(false); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-lg-12 margintop10">
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <label class="required-label">Hotel :</label>
                </div>
                <div class="col-md-9 col-sm-9 col-lg-9">
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
                                            'tickets-location_id'
                                        ],
                                        'placeholder' => 'Select Hotel',
                                        'url' => Url::to([
                                            'tickets/hotel'
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
            <div class="col-md-12 col-sm-12 col-lg-12 margintop10">
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <label class="required-label">Department :</label>
                </div>
                <div class="col-md-9 col-sm-9 col-lg-9">
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
                                            'tickets/department'
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
            <div class="col-md-12">
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <label class="required-label">Section :</label>
                </div>
                <div class="col-md-9 col-sm-9 col-lg-9">
                    <div class="input-group col-md-6">
                        <?php

                        $sections = \app\models\Tickets::getHotelSections($model->hotel_id, $model->department_id);

                        $sections = ArrayHelper::map($sections, 'section_id', function ($element) {
                            return $element['department']['department_name'] . '-' . $element['section']['s_section_name'];
                        });

                        $sections = $sections ? $sections : [];

                        if ($model->isNewRecord) {
                            echo $form->field($model, 'section_id')
                                ->widget(DepDrop::classname(), [
                                    'options' => [
                                        'id' => 'section_id'
                                    ],
                                    'data' => $sections,
                                    'pluginOptions' => [
                                        'depends' => [
                                            'department_id',
                                            'hotel_id'
                                        ],
                                        'placeholder' => 'Select Section',
                                        'url' => Url::to([
                                            'tickets/section'
                                        ])
                                    ]
                                ])
                                ->label(false);
                        } else {

                            echo $form->field($model, 'section_id')
                                ->dropDownList($sections, [
                                    'disabled' => 'disabled'
                                ])
                                ->label(false);
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-12 col-md-12">
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <label class="">Subsection :</label>
                </div>
                <div class="col-md-9 col-sm-9 col-lg-9">
                    <div class="input-group col-md-6">

                        <?php

                        $subsections = \app\models\Tickets::getHotelSubSections($model->section_id, $model->department_id, $model->hotel_id);

                        $subsections = ArrayHelper::map($subsections, 'sub_section_id', function ($element) {
                            return $element['section']['s_section_name'] . '-' . $element['subSection']['ss_subsection_name'];
                        });

                        $subsections = $subsections ? $subsections : [];

                        if ($model->isNewRecord) {
                            echo $form->field($model, 'sub_section_id')
                                ->widget(DepDrop::classname(), [
                                    'options' => [
                                        'id' => 'sub_section_id'
                                    ],
                                    'data' => $subsections,
                                    'pluginOptions' => [
                                        'depends' => [
                                            'section_id',
                                            'department_id',
                                            'hotel_id'
                                        ],
                                        'placeholder' => 'Select Sub Section',
                                        'url' => Url::to([
                                            'tickets/sub-section'
                                        ])
                                    ]
                                ])
                                ->label(false);
                        } else {
                            $model->sub_section_id = isset($subsections[$model->sub_section_id]) ? $subsections[$model->sub_section_id] : $model->sub_section_id;
                            echo $form->field($model, 'sub_section_id')
                                ->textInput( [
                                    'disabled' => 'disabled'
                                ])
                                ->label(false);
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-lg-12 col-md-12">
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <label class="required-label">Priority :</label>
                </div>
                <div class="col-md-9 col-sm-9 col-lg-9">
                    <div class="input-group col-md-6">
                        <?= $form->field($model, 'priority_type_id')->dropDownList(ArrayHelper::map(\app\models\QuestionPriorityTypes::find()->all(), 'priority_type_id', 'priority_name'), ['prompt' => 'Select Priority'])->label(false); ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-12 col-md-12">
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <label class="required-label">Assigned To :</label>
                </div>
                <div class="col-md-9 col-sm-9 col-lg-9">
                    <div class="input-group col-md-6">

                        <?php

                        $users = \app\models\Audits::getAuditorsList($model->department_id, $model->hotel_id, $model->location_id,3);

                        $users = ArrayHelper::map($users, 'user_id', function ($element) {
                            return $element['first_name'] . ' ' . $element['last_name'];
                        });


                        $users = $users ? $users : [];
                        if ($model->isNewRecord) {
                            echo $form->field($model, 'assigned_user_id')
                                ->widget(DepDrop::classname(), [
                                    'options' => [
                                        'id' => 'assigned_user_id'
                                    ],
                                    'data' => $users,
                                    'pluginOptions' => [
                                        'depends' => [
                                            'department_id',
                                            'hotel_id',
                                            'tickets-location_id'
                                        ],
                                        'placeholder' => 'Select Staff',
                                        'url' => Url::to([
                                            'tickets/get-staff-list'
                                        ])
                                    ]
                                ])
                                ->label(false);
                        } else {
                            echo $form->field($model, 'assigned_user_id')->widget(Select2::classname(), ['data' => $users, 'showToggleAll' => false, 'language' => 'en', 'options' => ['placeholder' => 'Select Staff',], 'pluginOptions' => ['allowClear' => true]])->label(false);
                        }

                        ?>


                    </div>
                </div>
            </div>
            <?php if (!$model->isNewRecord) { ?>
                <div class="col-sm-12 col-lg-12 col-md-12">
                    <div class="col-md-3 col-sm-3 col-lg-3">
                        <label class="required-label">Status</label>
                    </div>
                    <div class="col-md-9 col-sm-9 col-lg-9">
                        <div class="input-group col-md-6">
                            <?php $statusList = \app\models\Tickets::getTicketStatus($model->status); ?>
                            <?= $form->field($model, 'status')->dropDownList($statusList, ['prompt' => 'Select Status', 'disabled' => $model->status == 1 ? true : false])->label(false); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="col-sm-12 col-lg-12 col-md-12">
                <div class="col-md-3 col-sm-3 col-lg-3 marginTB10">
                    <label class="required-label">Chronicity :</label>
                </div>
                <div class="col-md-9 col-sm-9 col-lg-9">
                    <div class="input-group col-md-6">
                        <?= $form->field($model, 'chronicity')
                            ->radioList(
                                ['1' => 'Yes', '0' => 'No'],
                                [
                                    'item' => function ($index, $label, $name, $checked, $value) {
                                        $checked = ($checked) ? 'checked' : '';
                                        $return = '<div class="col-sm-6 radio-button-padding"  id="auditspan">';
                                        $return .= '<label class="ExternalAudit">';
                                        $return .= '<input type="radio" name="' . $name . '" value="' . $value . '"  ' . $checked . ' tabindex="3">';
                                        $return .= '<i></i>';
                                        $return .= '&nbsp;&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                                        $return .= '</label>';
                                        $return .= '</div>';
                                        return $return;
                                    }
                                ]
                            )
                            ->label(false);
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-12 col-lg-12 col-md-12">
                <div class="col-md-3 col-sm-3 col-lg-3 marginTB10">
                    <label class="">Process Critical (Dynamic) :</label>
                </div>
                <div class="col-md-9 col-sm-9 col-lg-9">
                    <div class="input-group col-md-6">
                        <?= $form->field($model, 'process_critical_dynamic')
                        ->checkbox(array('label'=>''))
                            ->label(false);
                        ?>
                    </div>
                </div>
            </div>
            
			<!-- <div class="col-sm-12 col-lg-12 col-md-12">
                <div class="col-md-3 col-sm-3 col-lg-3 marginTB10">
                    <label class="">Root Cause  :</label>
                </div>
                <div class="col-md-9 col-sm-9 col-lg-9">
                    <div class="input-group col-md-6">
                        <?= $form->field($model, 'root_cause')
                        ->checkbox(array('label'=>''))
                            ->label(false);
                        ?>
                    </div>
                </div>
            </div> -->
			
            <div class="col-md-12 col-sm-12 col-lg-12 margintop10">
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <label class="required-label">Due Date :</label>
                </div>
                <div class="col-md-9 col-sm-9 col-lg-9">
                    <div class="input-group col-sm-6">
                        <?= $form->field($model, 'due_date')->textInput(['value' => $model->due_date ? Yii::$app->formatter->asDate($model->due_date, "php:d-m-Y") : '', 'class' => 'datetimepicker form-control', 'id' => 'dateDue'])->label(false); ?>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-lg-12 col-md-12">
                <div class="col-md-3 col-sm-3 col-lg-3">

                    <label class="required-label">Subject :</label>
                </div>
                <div class="col-md-9 col-sm-9 col-lg-9">
                    <div class="input-group col-md-6">
                        <?= $form->field($model, 'subject')->textarea(array('rows' => 2, 'cols' => 5, 'Placeholder' => 'Subject' ))->label(false) ?>
                    </div>
                </div>
            </div>
            <?php if ($model->isNewRecord) { ?>
                <div class="col-sm-12 col-lg-12 col-md-12 margintop10">
                    <div class="col-md-3 col-sm-3 col-lg-3">
                        <label class="">Attachments:</label>
                    </div>
                    <div class="col-md-9 col-sm-9 col-lg-9">
                        <div class="input-group col-md-6">
                            <?= $form->field($modelTicketAttachment, 'ticket_attachment_path')->fileInput()->label(false) ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="col-md-12 col-sm-12 col-lg-12 margintop10">
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <label>Observations:</label>
                </div>
                <div class="col-md-9 col-sm-9 col-lg-9">
                    <div class="col-sm-6 input-group">
                        <?= $form->field($model, 'description')->textarea(array('rows' => 8, 'cols' => 3, 'Placeholder' => 'Observations'))->label(false) ?>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-lg-12 margintop10">
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <label></label>
                </div>
                <div class="col-md-9 col-sm-9 col-lg-9">
                    <div class="col-sm-6 input-group">
                        <?= Html::submitButton($model->isNewRecord ? 'Save' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
                        <?= Html::a('Cancel', ['/tickets'], ['class' => 'btn btn-default']); ?>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
