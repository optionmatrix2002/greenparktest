<?php
/* @var $this View */

/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use app\models\Questions;
use app\models\Checklists;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use app\models\Sections;

$this->title = 'Questionnaire for checklist';

AppAsset::register($this);
View::registerCssFile(yii::$app->urlManager->createUrl('css/questionnaire.css'));
View::registerJsFile(yii::$app->urlManager->createUrl('js/questionnaire.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/modelpopup.js'), [
    'depends' => JqueryAsset::className()
]);

$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuChecklists").addClass("active");
', \yii\web\View::POS_END);

?>
<div class="container-fluid">
    <h2>Questionnaire for checklist <?= $checkListName ?></h2>
</div>
<div class="wa-notification wa-notification-alt">
	<span class="wa-iconBoxed"> <span class="fa fa-file-text-o header-icon-fontcolor"></span>
	</span>
    <p id="description-text">Questionnaire for each audit can be defined
        here.</p>
</div>
<div class="col-md-12 col-lg-12 col-sm-12">
    <a
            href="<?= yii::$app->urlManager->createUrl('/check-lists/update?id= ' . Yii::$app->utils->encryptData($checklist_id)); ?>"
            class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
</div>
<div class="col-md-12 col-lg-12 col-sm-12 nopadding" style="margin-bottom: 10px;">
    <h4 style="text-indent: 13px;">Question Template</h4>
</div>

<div class="col-md-12 col-lg-12 col-sm-12 questionnaire-form">
    <?php $form = ActiveForm::begin(['id' => 'add-questions']); ?>
    <?= $form->field($model, 'q_checklist_id')->hiddenInput(['value' => $checklist_id])->label(false); ?>



    <?= $form->field($model, 'checkedvalue')->hiddenInput(['id' => 'checked_value'])->label(false); ?>


    <div class="card fl marginTB10 ">
        <div class="col-md-12 col-lg-12 col-sm-12 marginTB10">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label class="required-label">Template :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <?= $form->field($model, 'q_text')->textarea(['rows' => 2, 'class' => 'form-control', 'placeholder' => 'Type your audit question here...'])->label(false); ?>
            </div>

        </div>
        <div class="col-sm-12 margintop10">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label class="required-label">Section :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <?= $form->field($model, 'q_section')->widget(Select2::classname(), ['data' => ArrayHelper::map(Sections::find()->where(['s_department_id' => $department_id, 'is_deleted' => 0])->all(), 'section_id', 's_section_name'), 'language' => 'en', 'options' => ['placeholder' => 'Select Section'], 'pluginOptions' => ['allowClear' => true]])->label(false); ?>
            </div>
        </div>
        <div class="col-sm-12 margintop10">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label>Subsection :</label>
            </div>
            <div class="col-sm-6 col-lg-6 col-md-6 ">
                <?php
                if ($cl_audit_span == 1) {
                    $mutiple = false;
                } else {
                    $mutiple = true;
                }
                echo Html::hiddenInput('selectedSubsection', json_encode($subSectionsModel->sub_section_id), [
                    'id' => 'selectedSubsection'
                ]);
                echo $form->field($model, 'q_sub_section')
                    ->widget(DepDrop::classname(), [
                        'pluginOptions' => [
                            'initialize' => ($model->isNewRecord) ? false : true,
                            'depends' => [
                                'questions-q_section'
                            ],
                            'placeholder' => 'Select Subsection',
                            'url' => Url::to([
                                'check-lists/sub-section?&checklistId=' . $checklist_id . '&cl_audit_span=' . $cl_audit_span
                            ]),
                            'params' => [
                                'selectedSubsections'
                            ]
                        ],
                        'select2Options' => [
                            'pluginOptions' => [
                                'allowClear' => false,
                            ],
                            'showToggleAll' => false
                        ],
                        'pluginEvents' => [ // "depdrop:change" => "function(event, id, value, count) { var selctedValue = $(this).val();if(selctedValue != null && typeof selctedValue != 'undefined'){ $('#questions-q_sub_section_is_dynamic').attr('disabled','disabled')} }"
                        ],
                        'options' => [
                            'multiple' => $mutiple,
                        ],
                        'type' => DepDrop::TYPE_SELECT2
                    ])
                    ->label(false);

                ?>
            </div>
            <div class="col-sm-3 col-lg-3 col-md-3" style="margin-top: -13px;">

                <?= $form->field($model, 'q_sub_section_is_dynamic')->checkbox()->label('Dynamic'); ?>

            </div>
        </div>
        <div class="col-sm-12 marginTB10">
            <div class="col-sm-3 col-lg-3 col-md-3 marginTB10">
                <label>Access :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <?= $form->field($model, 'q_access_type')->inline(true)->checkboxList(ArrayHelper::map(\app\models\QuestionAccessTypes::find()->select(['access_type_id', 'CONCAT(UCASE(LEFT(access_name, 1)), 
                             SUBSTRING(access_name, 2)) as access_name'])->where(['!=', 'access_type_id', 0])->all(), 'access_type_id', 'access_name'), ['item' => function ($index, $label, $name, $checked, $value) {
                    return "<div class='col-md-3'><label class=''><input class= 'checkcheckbox' type='checkbox' name='{$name}'  value='{$value}' > <span>{$label}</span></label></div>";
                }])->label(false); ?>
            </div>
        </div>
        <div class="col-sm-12 marginTB10">
            <div class="col-sm-3 col-lg-3 col-md-3 marginTB10">
                <label class="required-label">Priority :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <?= $form->field($model, 'q_priority_type')->inline(true)->radioList(ArrayHelper::map(app\models\QuestionPriorityTypes::find()->select(['priority_type_id', 'CONCAT(UCASE(LEFT(priority_name, 1)), 
                             SUBSTRING(priority_name, 2)) as priority_name'])->all(), 'priority_type_id', 'priority_name'), ['item' => function ($index, $label, $name, $checked, $value) {
                    return "<div class='col-md-4'><label class=''><input type='radio' name='{$name}'  value='{$value}' > <span>{$label}</span></label></div>";
                }])->label(false); ?>
            </div>
        </div>
        <div class="col-sm-12 marginTB10">
            <div class="col-sm-3 col-lg-3 col-md-3 marginTB10">
                <label class="required-label">Response Type :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <?= $form->field($model, 'q_response_type')->radioList(ArrayHelper::map(app\models\QuestionResponseTypes::find()->select(['response_type_id', 'CONCAT(UCASE(LEFT(response_name, 1)), 
                             SUBSTRING(response_name, 2)) as response_name'])->where(['!=', 'response_type_id', 0])->all(), 'response_type_id', 'response_name'), ['item' => function ($index, $label, $name, $checked, $value) {
                    return "<div class='col-md-4'><label class=''><input type='radio' name='{$name}'  value='{$value}' > <span>{$label}</span></label></div>";
                }])->label(false); ?>

            </div>
        </div>
        <div class="col-sm-12 marginTB10 optionstab" style="display: none;">
            <div class="col-sm-3 col-lg-3 col-md-3">Options:</div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="col-sm-6">

                    <div class="col-sm-9 col-lg-9 col-md-9">
                        <input class="form-control" name="options[]" placeholder="Option"
                               type="text" value="">
                    </div>
                </div>
                <div class="col-sm-6">

                    <div class="col-sm-9 col-lg-9 col-md-9">
                        <input class="form-control" name="options[]" placeholder="Option"
                               type="text" value="">
                    </div>
                </div>
                <div class="col-sm-6 margintop10">

                    <div class="col-sm-9 col-lg-9 col-md-9">
                        <input class="form-control" name="options[]" placeholder="Option"
                               type="text" value="">
                    </div>
                </div>
                <div class="col-sm-6 margintop10 newcheckbox">

                    <div class="col-sm-9 col-lg-9 col-md-9">
                        <input class="form-control" name="options[]" placeholder="Option"
                               type="text" value="">
                    </div>
                </div>

                <div id="textboxDiv"></div>

                <div class="col-lg-12  margintop10">
                    <input
                            class="add-more-button btn btn-success" type="button" id="add"
                            name="add_item" value="Add More">
                    <input
                            class="btn btn-danger remove-item" type="button" id="remove"
                            name="remove_item" value="Remove Option">
                </div>

                <span id="error_option" class="PAD_ERR pull-left"></span>
            </div>
        </div>
        
        <div class="col-sm-12 marginTB10">
            <div class="col-sm-3 col-lg-3 col-md-3 ">
                <label class="">Process Critical :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <?= $form->field($model, 'process_critical')->inline(true)->checkbox(['class'=>'col-sm-1 mr-left-0 checkcheckbox'])->label(false); ?>

            </div>
        </div>
        
        
        <div class="col-sm-12">
            <div class="col-sm-12 marginTB10 text-center" style="margin-left: 15px;">
                <?= Html::submitButton('Save to Checklist', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<!-------------------------------------------------------Quations Start here -------------------------------------->
<?php

echo $this->render('across_section', [
    'model' => $model,
    'sectionsModel' => $sectionsModel,
    'subSectionsModel' => $subSectionsModel,
    'department_id' => $department_id,
    'cl_audit_span' => $cl_audit_span,
    'checkListName' => $checkListName,
    'checklist_id' => $checklist_id,
    'modelQuestionnaire' => $modelQuestionnaire
]);

?>


<!-------------------------------------------Model poup start here ----------------->
<div id="deletepopup" class="modal fade" role="dialog">
    <?php ActiveForm::begin(['id' => 'delete_questionaries_form', 'action' => yii::$app->urlManager->createUrl('check-lists/delete-questionnaire')]) ?>
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        style="color: #fff !important; opacity: 1;" aria-hidden="true">�
                </button>
                <h4 class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body clearfix">
                <input type="hidden" name="checklist_id" id="checklist_id" value=""/>
                <input type="hidden" name="section_id" id="section_id" value=""/> <input
                        type="hidden" name="auditspan_id" id="auditspan_id" value=""/> <input
                        type="hidden" name="questiontext" id="questiontext" value=""/> <input
                        type="hidden" name="deletable_questionnaire_id"
                        id="deletable_questionnaire_id" value=""/>
                <div class="col-sm-12" style="margin-top: 20px;">
                    <label> Are you sure you want to delete this Questionnaire? You
                        can't undo this action. </label>
                </div>
            </div>
            <div class="modal-footer clearfix"
                 style="border-top: none; margin-top: 5px;">
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

