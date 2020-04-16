<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProcessCriticalPreferences */
/* @var $form yii\widgets\ActiveForm */

$action = '';
if (!$model->isNewRecord) {
    $action = yii::$app->urlManager->createUrl(['preference/update','id'=> yii::$app->utils->encryptData($model->critical_preference_id)]);
} else {
    $action = yii::$app->urlManager->createUrl('preference/create');
}
?>
?>





    <?php $form =ActiveForm::begin(['id' => 'add_pref_form', 'action' => $action]); ?>
    
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
    
    <div class="modal-header">
                <h4 class="modal-title">
                    <button type="button" class="close modaltitlebutton"
                            data-dismiss="modal" aria-hidden="true">&times;
                    </button>
                    <strong>Create Process Critical Preferences </strong>
                </h4>
            </div>
    
    
    
     <div class="modal-body">
            <div class="row">
                <div class="col-sm-12 no-padding">
                    <div class="col-sm-3 nopadding">
                        <label class="required-label">Module</label>
                    </div>
                    <div class="col-sm-9 nopadding">
                        <?= $form->field($model, 'module_id')->dropDownList(ArrayHelper::map(\app\models\ProcessCriticalModule::find()->asArray()->all(), 'module_id', 'module_name'), ['prompt' => 'Select module'], ['class' => 'form-control'])->label(false); ?>
                    </div>
                </div>
                
                <div class="col-sm-12 no-padding">
                    <div class="col-sm-3 nopadding">
                        <label class="required-label">Option</label>
                    </div>
                    <div class="col-sm-9 nopadding">
                        <?= $form->field($model, 'module_option')->textInput(['maxlength' => 150])->label(false) ?>
                    </div>
                </div>
                
                <div class='col-sm-12' style="margin-top: 20px;">
                    <div class="col-sm-2 nopadding">
                    </div>
                    <div class="col-sm-9 nopadding text-center">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-success','id'=>'pref_submit_btn']) ?>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    
    
    




</div>
</div>
    <?php ActiveForm::end(); ?>


