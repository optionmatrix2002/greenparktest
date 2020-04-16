<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\widgets\MaskedInput;

$action = "";
$action = yii::$app->urlManager->createAbsoluteUrl([
    'organisation/manage-department',
    'department_id' => yii::$app->utils->encryptSetUp($departmentModel->department_id, 'department')
]);

?>


<?php $form = ActiveForm::begin(['id' => 'edit_department_form', 'action' => $action]); ?>
    <div class="col-sm-12 text-center text-bold">
        <h3 class="text-success ">
            <i class="fa fa-building-o"></i> <?= $hotelModel->hotel_name; ?>
        </h3>
        <small>(selected hotel)</small>
    </div>
    <div class="col-sm-12">
        <?= Html::hiddenInput("encrypted_hotel_id", yii::$app->utils->encryptSetUp($hotelModel->hotel_id,'hotel')); ?>
        <?= $form->field($departmentModel, 'department_name')->textInput(['maxlength' => 50,'class'=>'form-control alphanumericDepartment charsSpecialChars'])->label('Department Name'. Html::tag('span', '*',['class'=>'required'])); ?>
    </div>
    <div class="col-sm-12">
        <?= $form->field($departmentModel, 'department_description')->textarea(['rows' => 3])->label('Description'. Html::tag('span', '*',['class'=>'required'])); ?>
    </div>
    <div class='col-sm-12' style="margin-top: 20px;">
        <div class="col-sm-12 text-center">
            <button id="save_department_submit_btn" type="submit"
                    class="btn btn-success"><?=$departmentModel->isNewRecord ? 'Save':'Update'; ?> Department</button>
            <button class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
<?php ActiveForm::end() ?>