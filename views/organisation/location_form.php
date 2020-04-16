<?php
use app\models\Cities;
use app\models\States;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$action = "";
if ($locationsModel->isNewRecord) {
    $action = yii::$app->urlManager->createAbsoluteUrl('organisation/manage-location');
} else {
    $action = yii::$app->urlManager->createAbsoluteUrl([
        'organisation/manage-location',
        'location_id' => yii::$app->utils->encryptSetUp($locationsModel->location_id, 'location')
    ]);
}
?>
<?php
$form = ActiveForm::begin([
    'id' => 'new_location_form',
    'action' => $action,
    'enableAjaxValidation' => true,
    'validationUrl' => yii::$app->urlManager->createUrl('organisation/ajax-validate-location')
]);
?>

<div class="col-sm-12">
	<div class="col-sm-3 ">
		<label class="required-label">State</label>
	</div>
	<div class="col-sm-9 ">
		<div class="col-sm-10 ">
        <?= Html::hiddenInput('locationId', $locationsModel->location_id); ?>
            <?=$form->field($locationsModel, 'location_state_id')->widget(Select2::classname(), ['data' => ArrayHelper::map(States::findAll(['country_id' => 101 /* India */]), 'id', 'name'),'options' => ['placeholder' => 'Select State','id' => 'state_id'],'pluginOptions' => ['allowClear' => true]])->label(false);?>
        </div>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-3 ">
		<label class="required-label">Location</label>
	</div>
	<div class="col-sm-9 ">
		<div class="col-sm-10 ">
            <?php
            $data = [];
            if ($locationsModel->location_state_id && $locationsModel->location_city_id) {
                $data = ArrayHelper::map(Cities::findAll([
                    'state_id' => $locationsModel->location_state_id
                ]), 'id', 'name');
            }
            ?>
            <?=$form->field($locationsModel, 'location_city_id')->widget(DepDrop::classname(), ['options' => [],'data' => $data,'type' => DepDrop::TYPE_SELECT2,'select2Options' => ['pluginOptions' => ['allowClear' => true]],'pluginOptions' => ['depends' => ['state_id'],'placeholder' => 'Select Location','url' => yii::$app->urlManager->createUrl(['organisation/get-cities-by-state'])]])->label(false);?>
        </div>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-3 ">
		<label>Description:</label>
	</div>
	<div class="col-sm-9 ">
		<div class="col-sm-10 ">
            <?= $form->field($locationsModel, 'location_description')->textarea()->label(false); ?>
        </div>
	</div>
</div>
<div class='col-sm-12'>
	<div class="col-sm-3 "></div>
	<div class="col-sm-9 ">
		<div class="col-sm-10">
			<button id="save_location_submit_btn" type="submit"
				class="btn btn-success"><?= $locationsModel->isNewRecord ? 'Add' : 'Update'; ?> Location</button>
			<button class="btn btn-default" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>
<?php ActiveForm::end() ?>