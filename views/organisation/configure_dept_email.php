<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;
?>
<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title organisation_setup_title"><i class="fa fa-envelope"></i> Configure Emails </h3>
            <h5 class="text-muted organisation_setup_title">
                <?= $hotelDepartmentModel->hotel->location->locationCity->name ?>  <i class="fa fa-angle-double-right text-primary" aria-hidden="true"></i>  <?= $hotelDepartmentModel->hotel->hotel_name ?>  <i class="fa fa-angle-double-right text-primary" aria-hidden="true"></i>  
                <?= $hotelDepartmentModel->department->department_name ?> 
            </h5>
        </div>
        <div class="modal-body">
            <?php
            $configureMailsForm = ActiveForm::begin([
                        'id' => 'dept_hotel_configure_email_form',
                        'action' => yii::$app->urlManager->createUrl(['organisation/save-configure-emails', 'department_id' => yii::$app->utils->encryptSetUp($hotelDepartmentModel->department_id, 'department'), 'hotel_id' => yii::$app->utils->encryptSetUp($hotelDepartmentModel->hotel_id, 'hotel')])
            ]);
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <label>Enter Emails : <i class="fa fa-info-circle mr-top-0 padleft5px" title="Use commas(,) for adding multiple emails"></i></label>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <?= $configureMailsForm->field($hotelDepartmentModel, 'configured_emails')->textarea(['rows' => 3, 'placeholder' => 'Use commas(,) for adding multiple emails'])->label(false) ?>
                </div>
            </div>
            <div class="text-center">
                <button id="dept_hotel_configure_email_submit_btn" type="submit" class="btn btn-success">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            <?php $configureMailsForm->end(); ?>
        </div>
    </div>
</div>
