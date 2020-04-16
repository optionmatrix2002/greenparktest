<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\widgets\MaskedInput;
use app\models\HotelDepartments;

$action = "";
$action = yii::$app->urlManager->createAbsoluteUrl([
    'organisation/manage-section',
    'section_id' => yii::$app->utils->encryptSetUp($sectionsModel->section_id, 'section')
]);

?>

<?php $form = ActiveForm::begin(['id' => 'section_form', 'action' => $action]); ?>
<div class="col-sm-12 text-center text-bold">
	<h3 class="text-success ">
		<i class="fa fa-home"></i> <?= $hotelDepartmentModel->department->department_name; ?>
    </h3>
	<small>(selected department)</small>
</div>
<div class="col-sm-12">
    <?= Html::hiddenInput("encrypted_department_id", yii::$app->utils->encryptSetUp($hotelDepartmentModel->department_id,'department')); ?>
    <?= $form->field($sectionsModel, 's_section_name')->textInput(['maxlength' => 50,'class'=>'form-control alphanumeric'])->label('Section Name'. Html::tag('span', '*',['class'=>'required'])); ?>
</div>
<div class="col-sm-12">
    <?= $form->field($sectionsModel, 's_section_remarks')->textarea(['rows' => 3])->label('Description'); ?>
</div>
<div class='col-sm-12' style="margin-top: 20px;">
	<div class="col-sm-12 text-center">
		<button id="save_section_submit_btn" type="submit"
			class="btn btn-success"><?=$sectionsModel->isNewRecord ? 'Save':'Update'; ?> Section</button>
		<button class="btn btn-default" data-dismiss="modal">Close</button>
	</div>
</div>
<?php ActiveForm::end() ?>