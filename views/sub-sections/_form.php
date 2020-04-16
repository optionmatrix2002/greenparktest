<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Departments;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\SubSections */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuMasterData").addClass("active");
$("#settings-sub-sections").addClass("active");
', \yii\web\View::POS_END);

$actionType = Yii::$app->controller->action->id;
?>
<div class="container-fluid">
    <h2><?=$this->title; ?></h2>
</div>
<div class="wa-notification wa-notification-alt">
	<span class="wa-iconBoxed"> <span class="fa fa-file-text-o header-icon-fontcolor"></span>
	</span>
    <?php if($actionType=="create")
    {?>
        <p id="description-text">Master Data of Subsections can be create from here.</p>
    <?php } else if($actionType=="update") {?>
        <p id="description-text">Master Data of Subsections can be update from here.</p>
    <?php } else { ?>
        <p id="description-text">Master Data of Subsections can be managed from here.</p>
    <?php } ?>
</div>
<div class="col-md-12">
    <a href="<?= yii::$app->urlManager->createUrl('sub-sections'); ?>"
       class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
</div>

<div class="row" style="margin-top: 10px;">
    <div class="user-form">
        <?php $form = ActiveForm::begin(); ?>

        <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label>Department :<span class="span-star">*</span></label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?php echo $form->field($model, 'department_id')->widget(Select2::classname(), ['data' => ArrayHelper::map(\app\models\Departments::find()->where(['is_deleted' => 0])->all(), 'department_id', 'department_name'), 'id' => 'departmentId','showToggleAll' => false,'language' => 'en','options' => ['placeholder' => 'Select Department'],'pluginOptions' => ['allowClear' => true]])->label(false); ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label>Section :<span class="span-star">*</span></label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?php
                    $section_selected = app\models\Sections::findOne($model->ss_section_id);
                    $section_data = [];
                    if (! empty($section_selected->section_id)) {
                        $section_data = [
                            $section_selected->section_id => $section_selected->section_id
                        ];
                    }
                    if ($model->isNewRecord) {
                        echo $form->field($model, 'ss_section_id')
                            ->widget(DepDrop::classname(), [
                                'options' => [
                                    'id' => 'ss_section_id'
                                ],
                                'data' => $section_data,
                                'pluginOptions' => [
                                    'initialize' => true,
                                    'depends' => [
                                        'subsections-department_id'
                                    ],
                                    'placeholder' => 'Select Section',
                                    'url' => Url::to([
                                        'sub-sections/select-section'
                                    ])
                                ]
                            ])
                            ->label(false);
                    } else {

                        echo $form->field($model, 'ss_section_id')
                            ->widget(DepDrop::classname(), [
                                'options' => [
                                    'id' => 'subcat-id'
                                ],
                                'data' => $section_data,
                                'pluginOptions' => [
                                    'initialize' => true,
                                    'depends' => [
                                        'subsections-department_id'
                                    ],
                                    'placeholder' => 'Select Section',
                                    'url' => Url::to([
                                        'sub-sections/select-section'
                                    ])
                                ]
                            ])
                            ->label(false);
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label>Subsection :<span class="span-star">*</span></label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">

                    <?= $form->field($model, 'ss_subsection_name')->textInput(['maxlength' => true,'class'=>'form-control charsSpecialChars'])->label(false) ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label>Description :</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'ss_subsection_remarks')->textarea(['rows' => 6])->label(false) ?>
                </div>
            </div>
        </div>
        <?php
        /*
         * ?> <div class="col-sm-12 margintop10">
         * <div class="col-sm-2">
         * <label>Status :<span class="span-star">*</span></label>
         * </div>
         * <div class="col-lg-9 col-md-9 col-sm-9">
         * <div class="input-group col-sm-6">
         * <?= $form->field($model, 'is_active')
         * ->radioList(
         * ['1' => 'Active', '0' => 'Inactive'],
         * [
         * 'item' => function($index, $label, $name, $checked, $value) {
         * $checked = ($checked) ? 'checked' : '';
         * $return = '<div class="col-sm-6 radio-button-padding" id="auditspan">';
         * $return .= '<label class="ExternalAudit">';
         * $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" ' . $checked . ' tabindex="3">';
         * $return .= '<i></i>';
         * $return .= '&nbsp;&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
         * $return .= '</label>';
         * $return .= '</div>';
         * return $return;
         * }
         * ]
         * )
         * ->label(false);
         * ?>
         * </div>
         * </div>
         * </div> <?php
         */
        ?>

        <div class="col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label></label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="col-sm-6 input-group">
                    <?= Html::submitButton($model->isNewRecord ? 'Save' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
                    <?= Html::a( 'Cancel',['/sub-sections'],['class'=>'btn btn-default']); ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
