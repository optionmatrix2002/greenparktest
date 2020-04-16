<?php
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;
$action = "";
if ($subSectionsModel->isNewRecord) {
    $action = yii::$app->urlManager->createAbsoluteUrl('organisation/manage-subsection');
} else {
    $action = yii::$app->urlManager->createAbsoluteUrl([
        'organisation/manage-subsection',
        'subsection_id' => yii::$app->utils->encryptSetUp($subSectionsModel->sub_section_id, 'subsection')
    ]);
}
?>
<?php $form = ActiveForm::begin(['id' => 'subsection_form', 'action' => $action]); ?>
<div class="col-sm-12 text-center text-bold">
	<h3 class="text-success ">
		<i class="fa fa-users"></i> <?= $sectionsModel->section->s_section_name; ?>
    </h3>
	<small>(selected section) </small>
</div>
<div class="col-sm-12">
 <?= Html::hiddenInput("encrypted_section_id", yii::$app->utils->encryptSetUp($sectionsModel->section_id, 'section')); ?>
	<div class="textField">
			<?=$form->field($subSectionsModel, 'ss_subsection_name')->textInput(['maxlength' => 50,'class' => 'form-control departmentForm  departmentName alphanumeric'])->label('Subsection Name'. Html::tag('span', '*',['class'=>'required']));?>
			<?= $form->field($subSectionsModel, 'ss_subsection_remarks')->textarea(['rows' => 3,'class' => 'form-control  departmentForm departmentTextArea'])->label('Description'); ?>
		</div>

	<div class='col-sm-12 savenewmultipledep' style="margin-top: 20px;">
		<div class="col-sm-12 text-center">
			<button id="save_subsection_submit_btn" type="submit"
				class="btn btn-success"><?= $subSectionsModel->isNewRecord ? 'Save' : 'Update'; ?></button>
			<button class="btn btn-default" data-dismiss="modal">Close</button>
		</div>
	</div>

</div>
<?php ActiveForm::end() ?>


