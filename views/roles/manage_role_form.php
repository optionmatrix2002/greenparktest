<?php

use yii\widgets\ActiveForm;

$action = '';
if (!$rolesModel->isNewRecord) {
    $action = yii::$app->urlManager->createUrl(['roles/update-role','id'=> yii::$app->utils->encryptData($rolesModel->role_main)]);
} else {
    $action = yii::$app->urlManager->createUrl('roles/add-new-role');
}
?>
<?php
$newRoleform = ActiveForm::begin(['id' => 'add_role_form', 'action' => $action])
?>

<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><?=!$rolesModel->isNewRecord ? 'Edit': 'Add'; ?> Role  </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12 no-padding">
                    <div class="col-sm-3 nopadding">
                        <label class="required-label">Role Name</label>
                    </div>
                    <div class="col-sm-9 nopadding">
                        <?= $newRoleform->field($rolesModel, 'role_name')->textInput(['placeholder' => 'Enter Role Name','class'=>'form-control alphanumeric','maxlength'=>50])->label(false); ?>
                    </div>
                </div>
                <div class='col-sm-12' style="margin-top: 20px;">
                    <div class="col-sm-2 nopadding">
                    </div>
                    <div class="col-sm-9 nopadding text-center">
                        <button type="submit" id="role_add_submit_btn" class="btn btn-success"><i class=""></i>Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php ActiveForm::end() ?>