<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

?>
<?php $form = ActiveForm::begin(['id' => 'selected_permissions_form', 'action' => yii::$app->urlManager->createAbsoluteUrl(['roles/save-role-assignment', 'id' => $encryptedRole])]) ?>
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <button type="button" class="close modaltitlebutton"
                            data-dismiss="modal" aria-hidden="true">�
                    </button>
                    <strong>Features - Access </strong>
                </h4>
            </div>
            <div class="modal-body modalpermissions">
                <div class="container" style="width: 100%">
                    <div class="panel-group" id="accordion">
                        <div class="panel panel-default">
                            <div class="panel-heading accordianheading">
                                <h4 class="panel-title togtab">
                                    <a class="accordion-toggle" data-toggle="collapse"
                                       data-parent="#accordion" href="#dashboard"> <i
                                                class="glyphicon glyphicon-dashboard"></i> &nbsp;Dashboard<i
                                                class="fa fa-chevron-down pull-right"></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="dashboard" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php foreach (yii::$app->authManager->getChildren('manageDashboard') as $permission) { ?>
                                        <div class="col-md-1 martop5px aligncheckbox">
                                            <?php
                                            if (in_array($permission->name, $rolePermissions)) {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', true);
                                            } else {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', false);
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-11 no-padding">
                                            <label class="martop5px aligncheckbox"><?= $permission->description; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading accordianheading">
                                <h4 class="panel-title togtab">
                                    <a class="accordion-toggle" data-toggle="collapse"
                                       data-parent="#accordion" href="#prmdashboard"> <i
                                                class="glyphicon glyphicon-check"></i> &nbsp;Checklist Permissions<i
                                                class="fa fa-chevron-down pull-right"></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="prmdashboard" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php foreach (yii::$app->authManager->getChildren('manageCheckLists') as $permission) { ?>
                                        <div class="col-md-1 aligncheckbox martop5px">
                                            <?php
                                            if (in_array($permission->name, $rolePermissions)) {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', true);
                                            } else {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', false);
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-11 no-padding">
                                            <label class="martop5px"><?= $permission->description; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading accordianheading">
                                <h4 class="panel-title togtab">
                                    <a class="accordion-toggle" data-toggle="collapse"
                                       data-parent="#accordion" href="#prmProjects"> <i
                                                class="glyphicon glyphicon-calendar"></i> &nbsp;Audit Permissions<i
                                                class="fa fa-chevron-down pull-right"></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="prmProjects" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php foreach (yii::$app->authManager->getChildren('manageAudits') as $permission) { ?>
                                        <div class="col-md-1 aligncheckbox martop5px">
                                            <?php
                                            if (in_array($permission->name, $rolePermissions)) {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', true);
                                            } else {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', false);
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-11 no-padding">
                                            <label class="martop5px"><?= $permission->description; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading accordianheading">
                                <h4 class="panel-title togtab">
                                    <a class="accordion-toggle" data-toggle="collapse"
                                       data-parent="#accordion" href="#prmSprints"> <i
                                                class="fa fa-ticket"></i> &nbsp;Tickets<i
                                                class="fa fa-chevron-down pull-right"></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="prmSprints" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php foreach (yii::$app->authManager->getChildren('manageTickets') as $permission) { ?>
                                        <div class="col-md-1 aligncheckbox martop5px">
                                            <?php
                                            if (in_array($permission->name, $rolePermissions)) {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', true);
                                            } else {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', false);
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-11 no-padding">
                                            <label class="martop5px"><?= $permission->description; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading accordianheading">
                                <h4 class="panel-title togtab">
                                    <a class="accordion-toggle" data-toggle="collapse"
                                       data-parent="#accordion" href="#prmmanageusers"> <i
                                                class="fa fa-users"></i> &nbsp;Manage Users<i
                                                class="fa fa-chevron-down pull-right"></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="prmmanageusers" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php foreach (yii::$app->authManager->getChildren('manageUsers') as $permission) { ?>
                                        <div class="col-md-1 aligncheckbox martop5px">
                                            <?php
                                            if (in_array($permission->name, $rolePermissions)) {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', true);
                                            } else {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', false);
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-11 no-padding">
                                            <label class="martop5px"><?= $permission->description; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading accordianheading">
                                <h4 class="panel-title togtab">
                                    <a class="accordion-toggle" data-toggle="collapse"
                                       data-parent="#accordion" href="#prmmanageroles"> <i
                                                class="fa fa-tasks"></i> &nbsp;Manage Roles<i
                                                class="fa fa-chevron-down pull-right"></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="prmmanageroles" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php foreach (yii::$app->authManager->getChildren('manageRoles') as $permission) { ?>
                                        <div class="col-md-1 aligncheckbox martop5px">
                                            <?php
                                            if (in_array($permission->name, $rolePermissions)) {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', true);
                                            } else {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', false);
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-11 no-padding">
                                            <label class="martop5px"><?= $permission->description; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading accordianheading">
                                <h4 class="panel-title togtab">
                                    <a class="accordion-toggle" data-toggle="collapse"
                                       data-parent="#accordion" href="#prmmanagesetup"> <i
                                                class="fa fa-pencil-square-o"></i> &nbsp;Setup<i
                                                class="fa fa-chevron-down pull-right"></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="prmmanagesetup" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php foreach (yii::$app->authManager->getChildren('manageSetup') as $permission) { ?>
                                        <div class="col-md-1 aligncheckbox martop5px">
                                            <?php
                                            if (in_array($permission->name, $rolePermissions)) {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', true);
                                            } else {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', false);
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-11 no-padding">
                                            <label class="martop5px"><?= $permission->description; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading accordianheading">
                                <h4 class="panel-title togtab">
                                    <a class="accordion-toggle" data-toggle="collapse"
                                       data-parent="#accordion" href="#prmClients"> <i
                                                class="fa fa-info"></i> &nbsp;Preferences<i
                                                class="fa fa-chevron-down pull-right"></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="prmClients" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php foreach (yii::$app->authManager->getChildren('managePreferences') as $permission) { ?>
                                        <div class="col-md-1 aligncheckbox martop5px">
                                            <?php
                                            if (in_array($permission->name, $rolePermissions)) {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', true);
                                            } else {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', false);
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-11 no-padding">
                                            <label class="martop5px"><?= $permission->description; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading accordianheading">
                                <h4 class="panel-title togtab">
                                    <a class="accordion-toggle" data-toggle="collapse"
                                       data-parent="#accordion" href="#prmEventMaster"> <i
                                                class="fa fa-clock-o"></i> &nbsp;Event Master<i
                                                class="fa fa-chevron-down pull-right"></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="prmEventMaster" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php foreach (yii::$app->authManager->getChildren('manageEventMaster') as $permission) { ?>
                                        <div class="col-md-1 aligncheckbox martop5px">
                                            <?php
                                            if (in_array($permission->name, $rolePermissions)) {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', true);
                                            } else {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', false);
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-11 no-padding">
                                            <label class="martop5px"><?= $permission->description; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading accordianheading">
                                <h4 class="panel-title togtab">
                                    <a class="accordion-toggle" data-toggle="collapse"
                                       data-parent="#accordion" href="#prmErrorLog"> <i
                                                class="fa fa-warning"></i> &nbsp;Error Log<i
                                                class="fa fa-chevron-down pull-right"></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="prmErrorLog" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php foreach (yii::$app->authManager->getChildren('manageErrorLogs') as $permission) { ?>
                                        <div class="col-md-1 aligncheckbox martop5px">
                                            <?php
                                            if (in_array($permission->name, $rolePermissions)) {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', true);
                                            } else {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', false);
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-11 no-padding">
                                            <label class="martop5px"><?= $permission->description; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading accordianheading">
                                <h4 class="panel-title togtab">
                                    <a class="accordion-toggle" data-toggle="collapse"
                                       data-parent="#accordion" href="#prmMasterData"> <i
                                                class="fa fa-building-o"></i> &nbsp;Master Data<i
                                                class="fa fa-chevron-down pull-right"></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="prmMasterData" class="panel-collapse collapse">
                                <div class="panel-body">

                                    <?php

                                    foreach (yii::$app->authManager->getChildren('manageMasterData') as $permission) { ?>
                                        <div class="col-md-1 aligncheckbox martop5px">
                                            <?php
                                            if (in_array($permission->name, $rolePermissions)) {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', true);
                                            } else {
                                                echo Html::checkbox('Permissions[' . $permission->name . ']', false);
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-11 no-padding">
                                            <label class="martop5px"><?= $permission->description; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>


                        <div class="panel panel-default">
                            <div class="panel-heading accordianheading">
                                <h4 class="panel-title togtab">
                                    <a class="accordion-toggle" data-toggle="collapse"
                                       data-parent="#accordion" href="#prmNotifications"> <i
                                                class="fa fa-users"></i> &nbsp;Notifications<i
                                                class="fa fa-chevron-down pull-right"></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="prmNotifications" class="panel-collapse collapse">
                                <div class="panel-body">

                                    <input type="hidden" name="role_id" value="<?= $encryptedRole ?>">

                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th class="">
                                                Event Name
                                            </th>
                                            <th class="text-center">
                                                Email
                                            </th>
                                            <th class="text-center">
                                                SMS
                                            </th>
                                            <th class="text-center">
                                                Notification
                                            </th>
                                        </tr>

                                        </thead>
                                        <tbody>

                                        <?php
                                        foreach ($alertMasterModel as $getAlertType) { ?>
                                            <tr>
                                                <input type="hidden" name="alert_id[]"
                                                       value="<?= $getAlertType['alert_id']; ?>">

                                                <td class="">
                                                    <label class="martop5px"><?= $getAlertType['alert_type']; ?></label>
                                                </td>

                                                <td class="text-center">
                                                    <input  style="margin-top: 16px;    margin-right: 23px;" type="checkbox" name="email_id[]"
                                                            value="<?= $getAlertType['alert_id']; ?>" <?php if ($getAlertType['email_id'] == 1) {
                                                        echo 'checked' ?><?php } ?>>
                                                </td>

                                                <td class="text-center">
                                                    <input type="checkbox" style="margin-top: 16px;    margin-right: 3px;" name="sms_id[]"
                                                           value="<?= $getAlertType['alert_id']; ?>" <?= $getAlertType['sms_id'] == 1 ? 'checked' : '' ?>>
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" style="margin-top: 16px;    margin-right: 40px;" name="notification_id[]"
                                                           value="<?= $getAlertType['alert_id']; ?>" <?= $getAlertType['notification_id'] == 1 ? 'checked' : '' ?>>
                                                </td>


                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <div class="modal-footer clearfix bordertopnone">
                <div class="col-sm-12">
                    <input id="btnSaveLookupOptions" class="btn btn-success savediv"
                           type="submit" value="Save Assignment">&nbsp; <a id="PopupClose"
                                                                           data-dismiss="modal" class="btn btn-default">Close</a>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end() ?>