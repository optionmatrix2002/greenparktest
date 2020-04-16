<?php

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
    <h2>Update Question</h2>
</div>
<div class="wa-notification wa-notification-alt">
	<span class="wa-iconBoxed"> <span class="fa fa-file-text-o header-icon-fontcolor"></span>
	</span>
    <p id="description-text">Question can be updated here.</p>
</div>
<div class="col-sm-12 col-md-12 col-lg-12">
    <a href="<?= yii::$app->urlManager->createUrl('/check-lists/add-questionnaire?id= ' . Yii::$app->utils->encryptData($checklist_id)); ?>"
       class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
</div>

<div class="col-sm-12 questionnaire-form">
    <?php $form = ActiveForm::begin(['id' => 'add-questions']); ?>
    <?= $form->field($model, 'q_checklist_id')->hiddenInput(['value' => $checklist_id])->label(false); ?>

    <div class="card fl  marginTB10 ">
        <div class="col-sm-12 marginTB10">
            <div class="col-sm-2">
                <label class="required-label">Template :</label>
            </div>
            <div class="col-sm-8">
                <?= $form->field($model, 'q_text')->textarea(['rows' => 2, 'class' => 'form-control', 'placeholder' => 'Type your audit question here...'])->label(false); ?>
            </div>

        </div>
        <div class="col-sm-12 margintop10">
            <div class="col-sm-2">
                <label class="required-label">Section :</label>
            </div>
            <div class="col-sm-8">
                <?= $form->field($model, 'q_section')->widget(Select2::classname(), ['data' => ArrayHelper::map(Sections::find()->where(['s_department_id' => $department_id, 'is_deleted' => 0])->all(), 'section_id', 's_section_name'), 'language' => 'en', 'options' => ['placeholder' => 'Select Section', 'disabled' => 'disabled'], 'pluginOptions' => ['allowClear' => true]])->label(false); ?>
            </div>
        </div>
        <div class="col-sm-12 margintop10">
            <div class="col-sm-2">
                <label>Subsection :</label>
            </div>
            <div class="col-sm-8 ">
                <?php
                if ($cl_audit_span == 1) {
                    $mutiple = false;
                } else {
                    $mutiple = true;
                }
                echo Html::hiddenInput('selectedSubsection', json_encode($subSectionsModel->sub_section_id), [
                    'id' => 'selectedSubsection'
                ]);

                $section_data = [
                    $model->q_sub_section => $model->q_sub_section
                ];

                echo $form->field($model, 'q_sub_section')
                    ->widget(DepDrop::classname(), [
                        'data' => $section_data,
                        'pluginOptions' => [

                            'initialize' => true,
                            'depends' => [
                                'questions-q_section'
                            ],
                            'placeholder' => 'Select Sub Section',
                            'url' => Url::to([
                                'check-lists/sub-section?&checklistId=' . $checklist_id . '&cl_audit_span=' . $cl_audit_span
                            ]),
                            'params' => [
                                'selectedSubsections'
                            ]
                        ],
                        'select2Options' => [
                            'pluginOptions' => [
                                'allowClear' => false
                            ]
                        ],
                        'pluginEvents' => [ // "depdrop:change" => "function(event, id, value, count) { var selctedValue = $(this).val();if(selctedValue != null && typeof selctedValue != 'undefined'){ $('#questions-q_sub_section_is_dynamic').attr('disabled','disabled')} }"
                        ],
                        'options' => [
                            'multiple' => $mutiple,
                            // 'disabled' => 'disabled'
                        ],
                        'type' => DepDrop::TYPE_SELECT2
                    ])
                    ->label(false);

                ?>
            </div>
            <div class="col-sm-2" style="margin-top: -13px;">

                <?= $form->field($model, 'q_sub_section_is_dynamic')->checkbox(array('disabled' => 'disabled'))->label('Dynamic'); ?>

            </div>
        </div>
        <div class="col-sm-12 marginTB10">
            <div class="col-sm-2"><label>Access :</label></div>
            <div class="col-sm-10">

                <?= $form->field($model, 'q_access_type')->inline(true)->checkboxList(ArrayHelper::map(\app\models\QuestionAccessTypes::find()->select(['access_type_id', 'CONCAT(UCASE(LEFT(access_name, 1)), 
                             SUBSTRING(access_name, 2)) as access_name'])->where(['!=', 'access_type_id', 0])->all(), 'access_type_id', 'access_name'), ['item' => function ($index, $label, $name, $checked, $value) {
                    $checked = $checked ? 'checked' : '';
                    return "<div class='col-md-4'><label class=''><input class = 'checkcheckbox' type='checkbox' {$checked} name='{$name}'  value='{$value}' > <span>{$label}</span></label></div>";
                }])->label(false); ?>
            </div>
        </div>
        <div class="col-sm-12 marginTB10">
            <div class="col-sm-2">
                <label class="required-label">Priority :</label>
            </div>
            <div class="col-sm-10">
                <?= $form->field($model, 'q_priority_type')->inline(true)->radioList(ArrayHelper::map(app\models\QuestionPriorityTypes::find()->select(['priority_type_id', 'CONCAT(UCASE(LEFT(priority_name, 1)), 
                             SUBSTRING(priority_name, 2)) as priority_name'])->all(), 'priority_type_id', 'priority_name'), ['item' => function ($index, $label, $name, $checked, $value) {
                    $checked = $checked ? 'checked' : '';
                    return "<div class='col-md-4'><label class=''><input type='radio' name='{$name}' {$checked}  value='{$value}' > <span>{$label}</span></label></div>";
                }])->label(false); ?>
            </div>
        </div>
        <div class="col-sm-12 marginTB10">
            <div class="col-sm-2">
                <label class="required-label">Response Type :</label>
            </div>
            <div class="col-sm-10">
                <?= $form->field($model, 'q_response_type')->radioList(ArrayHelper::map(app\models\QuestionResponseTypes::find()->select(['response_type_id', 'CONCAT(UCASE(LEFT(response_name, 1)), 
                             SUBSTRING(response_name, 2)) as response_name'])->where(['!=', 'response_type_id', 0])->all(), 'response_type_id', 'response_name'), ['item' => function ($index, $label, $name, $checked, $value) {
                    $checked = $checked ? 'checked' : '';
                    return "<div class='col-md-4'><label class=''><input type='radio' name='{$name}'  {$checked} value='{$value}' > <span>{$label}</span></label></div>";
                }])->label(false); ?>

            </div>
        </div>
        <div class="col-sm-12 marginTB10 optionstab"
             style="<?php if (empty($selectedOptions)) { ?>display: none;<?php } ?>">
            <div class="col-sm-2">Options:</div>
            <div class="col-sm-8">


                <div class="col-sm-6">

                    <div class="col-sm-10">
                        <input class="form-control" name="options[]" placeholder="Option"
                               type="text"
                               value="<?php if (!empty($selectedOptions['0'])) {
                                   echo $selectedOptions['0'];
                               } ?>">
                    </div>
                </div>
                <div class="col-sm-6">

                    <div class="col-sm-10">
                        <input class="form-control" name="options[]" placeholder="Option"
                               type="text"
                               value="<?php if (!empty($selectedOptions['1'])) {
                                   echo $selectedOptions['1'];
                               } ?>">
                    </div>
                </div>
                <div class="col-sm-6 margintop10">

                    <div class="col-sm-10">
                        <input class="form-control" name="options[]" placeholder="Option"
                               type="text"
                               value="<?php if (!empty($selectedOptions['2'])) {
                                   echo $selectedOptions['2'];
                               } ?>">
                    </div>
                </div>
                <div class="col-sm-6 margintop10 newcheckbox">

                    <div class="col-sm-10">
                        <input class="form-control" name="options[]" placeholder="Option"
                               type="text"
                               value="<?php if (!empty($selectedOptions['3'])) {
                                   echo $selectedOptions['3'];
                               } ?>">
                    </div>
                </div>
                <?php

                if (!empty($selectedOptions) && count($selectedOptions) > 4) {
                    for ($i = 4; $i < count($selectedOptions); $i++) {
                        ?>
                        <div class="col-sm-6 margintop10 additional-options">

                            <div class="col-sm-10">
                                <input class="form-control" name="options[]" placeholder="Option"
                                       type="text"
                                       value="<?php if (!empty($selectedOptions[$i])) {
                                           echo $selectedOptions[$i];
                                       } ?>">
                            </div>
                        </div>

                    <?php }
                } ?>
                <div id="textboxDiv"></div>
                <div class="col-lg-12  margintop10">
                    <input
                            class="add-more-button btn btn-success" type="button"
                            name="add_item" value="Add More" id="add">
                    <input
                            class="btn btn-danger remove-item" type="button" id="remove"
                            name="remove_item" value="Remove Option">
                </div>
                <span id="error_option" class="PAD_ERR pull-left"></span>
            </div>
        </div>
        
        <div class="col-sm-12 marginTB10">
        <div class="col-sm-2">
                <label class="">Process Critical :</label>
            </div>
                                        
                                        <div class="col-sm-10">
                                            <div class="col-sm-4">
                                            <?= $form->field($model, 'process_critical')->inline(true)->checkbox(['class'=>' mr-left-0 checkcheckbox'])->label(false); ?>
                                                
                                            </div>
                                            
                                        </div>
                                    </div>
        
        
        
        <div class="col-sm-12  ">
            <div class="col-sm-12 marginTB10 text-center" style="margin-left: 15px;">
                <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>


    <?php ActiveForm::end(); ?>
</div>

