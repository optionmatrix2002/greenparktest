<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Sections */
/* @var $form yii\widgets\ActiveForm */


$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuMasterData").addClass("active");
$("#settings-sections").addClass("active");
', \yii\web\View::POS_END);
$action_type = Yii::$app->controller->action->id;
?>
<div class="container-fluid">
    <h2><?=$this->title; ?></h2>
</div>
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
    <?php if($action_type=="create"){?>
    <p id="description-text">Master Data of Sections can be create from here.</p>
    <?php } else if($action_type=="update"){?>
    <p id="description-text">Master Data of Sections can be update from here.</p>
    <?php } else{?>
    <p id="description-text">Master Data of Sections can be manage from here.</p>
    <?php } ?>
</div>                
<div class="col-md-12">
    <a href="<?= yii::$app->urlManager->createUrl('sections'); ?>" class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
</div>
<div class="row" style="margin-top: 10px;">
<div class="user-form">
 <?php $form = ActiveForm::begin(); ?>
 
         <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label>Department:<span class="span-star">*</span></label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                   <?= $form->field($model, 's_department_id')->widget(Select2::classname(), ['data' => ArrayHelper::map(\app\models\Departments::find()->where(['is_deleted' => 0])->all(), 'department_id', 'department_name'),'showToggleAll' => false,'language' => 'en','options' => ['placeholder' => 'Select Department'],'pluginOptions' => ['allowClear' => true]])->label(false); ?>
                </div>
            </div>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label>Section Name:<span class="span-star">*</span></label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    
                    <?= $form->field($model, 's_section_name')->textInput(['maxlength' => true,'class'=>'form-control charsSpecialChars'])->label(false) ?>
                </div>
            </div>
          </div>
           <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <label>Description:</label>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9">
                    <div class="input-group col-sm-6">
                        <?= $form->field($model, 's_section_remarks')->textarea(['rows' => 6])->label(false) ?>
                    </div>
                </div>
            </div>
            <?php /*?>
            <div class="col-sm-12 margintop10">
                <div class="col-sm-2">
                    <label>Status:<span class="span-star">*</span></label>
                </div>
            <div class="col-sm-10">
                <div class="input-group col-sm-6">
                      <?= $form->field($model, 'is_active')
                         ->radioList(
                             ['1' => 'Active', '0' => 'Inactive'],
                             [
                                 'item' => function($index, $label, $name, $checked, $value) {
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
            </div> <?php */?>
           <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <label></label>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9">
                    <div class="col-sm-6 input-group">                
                        <?= Html::submitButton($model->isNewRecord ? 'Save' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
                        <?= Html::a( 'Cancel',['/sections'],['class'=>'btn btn-default']); ?>
                    </div>
                </div>
            </div>

    <?php ActiveForm::end(); ?>
</div>
</div>