<?php

use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;
use app\assets\AppAsset;

use app\models\ChangePasswordForm;
use app\models\user;


AppAsset::register($this);
$this->registerJsFile(yii::$app->urlManager->createUrl('js/user.js?version=' . time()), [
    'depends' => JqueryAsset::className()
]);
?>

<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-static-top" style="margin-bottom: 5px; background-color: #102442;">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="<?= yii::$app->urlManager->createUrl('site/dashboard'); ?>">
                <img src="<?= yii::$app->urlManager->createUrl('img/greenpark_textlogo.png'); ?>"
                     style="width: 52%;margin-top:5px;"/>
            </a>
            <!-- <span class="login-custom-logo-text">Option Matrix</span> -->
            <div class="menu-toggler sidebar-toggler hide">
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse"
           data-target=".navbar-collapse"></a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <div class="top-menu">

            <ul class="nav navbar-nav pull-right" id="profile">
                <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">

                    <ul class="dropdown-menu">
                        <li>
                            <div class="slimScrollDiv"
                                 style="position: relative; overflow: hidden; width: auto; height: 250px;">
                                <ul class="dropdown-menu-list scroller"
                                    style="height: 250px; overflow: hidden; width: auto;" data-handle-color="#637283"
                                    data-initialized="1">
                                    <li>
                                        <a href="javascript:;">
                                            <span class="time">just now</span>
                                            <span class="details">
                                                    <span class="label label-sm label-icon label-success">
                                                        <i class="fa fa-plus"></i>
                                                    </span>
                                                    New user registered.
                                                </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;">
                                            <span class="time">3 mins</span>
                                            <span class="details">
                                                    <span class="label label-sm label-icon label-danger">
                                                        <i class="fa fa-bolt"></i>
                                                    </span>
                                                    Inventory usage > 95%
                                                </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;">
                                            <span class="time">10 mins</span>
                                            <span class="details">
                                                    <span class="label label-sm label-icon label-warning">
                                                        <i class="fa fa-bell-o"></i>
                                                    </span>
                                                    2 tickets escalated.
                                                </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;">
                                            <span class="time">14 hrs</span>
                                            <span class="details">
                                                    <span class="label label-sm label-icon label-info">
                                                        <i class="fa fa-bullhorn"></i>
                                                    </span>
                                                    Application error.
                                                </span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="slimScrollBar"
                                     style="background: rgb(99, 114, 131); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: block; border-radius: 7px; z-index: 99; right: 1px;"></div>
                                <div class="slimScrollRail"
                                     style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(234, 234, 234); opacity: 0.2; z-index: 90; right: 1px;"></div>
                            </div>
                        </li>
                    </ul>
                </li>
                <li class="help" style="padding:0px;">
                    <a style="padding-top: 9px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; background: none;">
                        <button class="btn"
                                style="padding: 6px 16px !important; background-color: #fff446; color: black;"
                                id="helpbtn" title="Click to view help">Help
                        </button>
                    </a>
                </li>
                <li class="dropdown dropdown-user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                       data-close-others="true" aria-expanded="false">
                        <img alt="" class="img-circle" src="http://optionmatrix.in/greenpark/Content/Images/Usr.jpg">
                        &nbsp;&nbsp;<span class="username username-hide-on-mobile"><b
                                    style=" color:#fff !important;"><?php echo (Yii::$app->user->identity) ? Yii::$app->user->identity->first_name .' '. Yii::$app->user->identity->last_name : '' ?></b></span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <li>
                            <!-- <a href="#">
                                  <i class="icon-lock"></i>Change Password
                              </a>  -->
                            <a data-toggle="modal" data-target="#myModal-change-pswd" class="">

                                Change Password
                            </a>


                        </li>
                        <li>
                            <a data-method="post"
                               href="<?= yii::$app->urlManager->createAbsoluteUrl('site/logout'); ?>">
                                Log Out
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>


        </div>

        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">

</div>

<!------------------------Model PopUp Start here-------------------------- -->
<div class="modal fade" id="myModal-change-pswd" tabindex="-1"
     role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog pswd-pop">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span> <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Change Password</h4>
            </div>
            <div class="modal-body">
                <?php $changePassword = new ChangePasswordForm(); ?>
                <?php $form = ActiveForm::begin(['id' => 'changePassword-form',
                    'action' => ['user/change-password'],
                    'method' => 'post',
                    'options' => [
                        'class' => 'login-wrapper'
                    ]]); ?>


                <div class="form-group">
                    <?= $form->field($changePassword, 'currentPassword')->passwordInput()->label('Current Password' . Html::tag('span', '*', ['class' => 'required'])) ?>

                </div>

                <div class="form-group">
                    <?= $form->field($changePassword, 'newPassword')->passwordInput()->label('New Password' . Html::tag('span', '*', ['class' => 'required newpassword_id'])) ?>

                </div>

                <div class="form-group">
                    <?= $form->field($changePassword, 'retypePassword')->passwordInput()->label('Retype Password' . Html::tag('span', '*', ['class' => 'required'])) ?>

                </div>
                <div class='col-sm-12' style="margin-top: 20px;">
                    <div class="col-sm-4 nopadding">

                    </div>
                    <div class="col-sm-4 nopadding ">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                    <div class="col-sm-4 nopadding">

                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<!------------------------Model PopUp Start here-------------------------- -->

