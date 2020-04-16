<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\widgets\MaskedInput;

$action = "";
if ($hotelModel->isNewRecord) {
    $action = yii::$app->urlManager->createAbsoluteUrl('organisation/manage-hotel');
} else {
    $action = yii::$app->urlManager->createAbsoluteUrl(['organisation/manage-hotel', 'hotel_id' => yii::$app->utils->encryptSetUp($hotelModel->hotel_id,'hotel')]);
}
?>

<?php $form = ActiveForm::begin(['id' => 'hotel_form', 'action' => $action]); ?>
    <div class="col-sm-12 text-center text-bold">
        <h3 class="text-success ">
            <i class="fa fa-map-marker"></i> <?= $locationsModel->locationCity->name; ?>
        </h3>
        <small>(selected location)</small>
    </div>
    <div class="col-sm-12">
        <?= Html::hiddenInput("encrypted_location_id", yii::$app->utils->encryptSetUp($locationsModel->location_id,'location')); ?>
        <?= $form->field($hotelModel, 'hotel_name')->textInput(['maxlength' => 50,'class'=>'form-control charsSpecialChars'])->label('Hotel Name'. Html::tag('span', '*',['class'=>'required'])); ?>
    </div>



    <div class="col-sm-12">
        <?=
        $form->field($hotelModel, 'hotel_phone_number')->textInput(['class'=>'form-control numbers hotelPhoneNumber charsSpecialChars','id' => 'preference_three'])->label('Phone Number'.Html::tag('span', '*',['class'=>'required'])); ?>
        <span class="error"  style="color:#a94442"></span>
    </div>


    <div class="col-sm-12">
        <?= $form->field($hotelModel, 'hotel_address')->textarea(['rows' => 3])->label('Address'. Html::tag('span', '*',['class'=>'required'])); ?>
    </div>
    <div class='col-sm-12' style="margin-top: 20px;">
        <div class="col-sm-12 text-center">
            <button id="save_hotel_submit_btn" type="submit" class="btn btn-success"><?=$hotelModel->isNewRecord ? 'Add':'Update'; ?> Hotel</button>
            <button class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
<?php ActiveForm::end() ?>