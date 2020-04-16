<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\AuditMethods;
use app\models\Departments;
use yii\web\JqueryAsset;
use yii\web\View;
use app\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $model app\models\Checklists */
/* @var $form yii\widgets\ActiveForm */


AppAsset::register($this);
View::registerCssFile(yii::$app->urlManager->createUrl('css/questionnaire.css'));

$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuChecklists").addClass("active");
', \yii\web\View::POS_END);

?>
<div class="container-fluid">
  <h2><?= $this->title;?> </h2>
</div>
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
    <p id="description-text">
       Checklist data and the corresponding questionnaire can be managed from here.
    </p>
</div>
<div class="col-md-12 col-lg col-sm-12">
    <a href="<?= yii::$app->urlManager->createUrl('check-lists'); ?>" class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
</div>
<div class="row" style="margin-top: 10px;">
  <div class="checklists-form">
       <?php $form = ActiveForm::begin(); ?>
        <div class="col-sm-12 col-lg-12 col-md-12 ">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label class = "required-label">Checklist Name :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'cl_name')->textInput(['maxlength' => true, 'class'=>'form-control charsSpecialChars', 'placeholder' => 'Checklist Name'])->label(false); ?>
                </div>
            </div>
       </div>
         <div class="col-sm-12 col-lg-12 col-md-12">
            <div class="col-sm-3 col-lg-3 col-md-3 marginTB10">
                <label class = "required-label">Audit Type :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
             <div class="input-group col-sm-6">
                 
                <?= $form->field($model, 'cl_audit_type')
                         ->radioList(
                             ['1' => 'External', '0' => 'Internal'],
                             [
                                 'item' => function($index, $label, $name, $checked, $value) {
                                 $checked = ($checked) ? 'checked' : '';
                                 $return = '<div class="col-sm-6 radio-button-padding"  id="auditspan">';
                                 $return .= '<label class="ExternalAudit">';
                                 $return .= '<input type="radio" name="' . $name . '" value="' . $value . '"' . $checked . ' tabindex="3">';
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
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label  class = "required-label">Audit Method  :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'cl_audit_method')->dropDownList(ArrayHelper::map(\app\models\AuditMethods::find()->asArray()->all(), 'audit_method_id', 'audit_method_name'),['prompt' => 'Select Audit Method'], ['class' => 'form-control'])->label(false); ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-12 col-md-12">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label  class = "required-label">Department :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'cl_department_id')->dropDownList(ArrayHelper::map(\app\models\Departments::find()->where(['is_deleted' => 0])->asArray()->all(), 'department_id', 'department_name'), ['prompt' => 'Select Department','disabled'=>($model->isNewRecord) ? false:true,], ['disabled'=>($model->isNewRecord) ? false:true,'class' => 'form-control'])->label(false); ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-12 col-md-12">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label  class = "required-label">Frequency :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                 <div class="input-group col-sm-6">
                    <div class="col-sm-12  radio-button-padding"  id="auditspan">
                    	<?= $form->field($model, 'cl_frequency_value')->dropDownList(ArrayHelper::map(\app\models\Interval::find()->asArray()->all(), 'interval_id', 'interval_name'), ['prompt' => 'Select Frequency'], ['class' => 'form-control'])->label(false); ?>
                     </div>                       
                </div>
            </div>
        </div>
         <div class="col-sm-12 col-lg-12 col-md-12">
            <div class="col-sm-3 col-lg-3 col-md-3 marginTB10">
                <label  class = "required-label">Audit Span :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="input-group col-md-6 col-sm-12">
                      <?php if($model->isNewRecord){?>
                      <?= $form->field($model, 'cl_audit_span')
                         ->radioList(
                             ['1' => 'Section Specific', '2' => 'Across Sections'],
                             [
                                 'item' => function($index, $label, $name, $checked, $value) {
                                 $return = '<div class="col-sm-6 radio-button-padding"  id="auditspan">';
                                 $return .= '<label class="ExternalAudit">';
                                 $return .= '<input type="radio" name="' . $name . '" value="' . $value . '">';
                                 $return .= '<i></i>';
                                 $return .= '&nbsp;&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                                 $return .= '</label>';
                                 $return .= '</div>';
                                 return $return;
                                 }
                                 ,['disabled'=>true]
                              ]
                              )
                         ->label(false); 
                        ?>
                        <?php }else {?>
                        <?= $form->field($model, 'cl_audit_span')
                         ->radioList(
                             ['1' => 'Section Specific', '2' => 'Across Sections'],
                             [
                                 'item' => function($index, $label, $name, $checked, $value) {
                                 $disabled = 'disabled';
                                 $checked = ($checked) ? 'checked' : '';
                                 $return = '<div class="col-sm-6 radio-button-padding"  id="auditspan">';
                                 $return .= '<label class="ExternalAudit">';
                                 $return .= '<input type="radio" name="' . $name . '" value="' . $value . '"  ' . $checked . ' tabindex="3"'.$disabled.'>';
                                 $return .= '<i></i>';
                                 $return .= '&nbsp;&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                                 $return .= '</label>';
                                 $return .= '</div>';
                                 return $return;
                                 }
                                 ,['disabled'=>true]
                              ]
                              )
                         ->label(false); 
                        ?>
                        <?php }?>
                </div>
            </div>
         </div>     
         <div class="col-sm-12 col-lg-12 col-md-12">
            <div class="col-sm-3 col-lg-3 col-md-3 marginTB10">
                <label  class = "required-label">Status :</label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="input-group col-sm-6">
                      <?= $form->field($model, 'cl_status')
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
          </div>  
        <div class="col-sm-12 col-lg-12 col-md-12">
            <div class="col-sm-3 col-lg-3 col-md-3">
                <label></label>
            </div>
            <div class="col-sm-9 col-lg-9 col-md-9">
                <div class="col-sm-9 col-lg-9 col-md-9 input-group">
                    <?= Html::submitButton($model->isNewRecord ? 'Save & Proceed to Questionnaire' : 'Update & Proceed to Questionnaire', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
                    <?= Html::a( 'Cancel',['/check-lists'],['class'=>'btn btn-default']); ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
     </div>  
</div>

