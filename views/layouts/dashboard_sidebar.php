<?php $item = Yii::$app->controller->id; ?>
<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">

        <ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">

            <li class="sidebar-toggler-wrapper">
                <div class="sidebar-toggler text-center">
                    <i title="Toggle Menu" class="fa fa-align-justify"></i>
                </div>
            </li>
            <?php
            if (Yii::$app->authManager->checkPermissionAccess('site/dashboard')) {
                ?>
                <li id="MenuDashboard" class="nav-bids  <?php if ($item == "site") {
                    echo "active"; ?>  <?php } ?>">
                    <a href="<?= yii::$app->urlManager->createUrl('site/dashboard'); ?>" class="nav-link ">
                        <div class="floatleft clsuser clsbackground"><i class="glyphicon glyphicon-dashboard"
                                                                        aria-hidden="true"></i></div>
                        <span class="title">Dashboard</span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php }
            if (Yii::$app->authManager->checkPermissionAccess('check-lists')) {
                ?>
                <li id="MenuChecklists" class="nav-bids <?php if ($item == "check-lists") {
                    echo "active"; ?>  <?php } ?>">
                    <a href="<?= yii::$app->urlManager->createUrl('check-lists'); ?>" class="nav-link ">
                        <div class="floatleft clsprojects clsbackground"><i class="glyphicon glyphicon-check"
                                                                            aria-hidden="true"></i></div>
                        <span class="title">Checklists</span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php }
            if (Yii::$app->authManager->checkPermissionAccess('audits')) { ?>
                <li id="MenuAudits" class="nav-bids <?php if ($item == "audits") {
                    echo "active"; ?>  <?php } ?>">
                    <a href="<?= yii::$app->urlManager->createUrl('audits'); ?>" class="nav-link notactive">
                        <div class="floatleft clsprojects clsbackground"><i class="glyphicon glyphicon-calendar"
                                                                            aria-hidden="true"></i></div>
                        <span class="title">Audits</span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php }
            if (Yii::$app->authManager->checkPermissionAccess('tickets')) { ?>
                <li id="tickets" class="nav-bids <?php if ($item == "tickets") {
                    echo "active"; ?>  <?php } ?>">
                    <a href="<?= yii::$app->urlManager->createUrl('tickets'); ?>" class="nav-link">
                        <div class="floatleft clsprojects clsbackground"><i class="fa fa-ticket" aria-hidden="true"></i>
                        </div>
                        <span class="title">Tickets</span>
                        <span class="selected"></span>
                    </a>
                </li>
            <?php } ?>


            <?php if (Yii::$app->authManager->checkPermissionAccess('user') || Yii::$app->authManager->checkPermissionAccess('roles') || Yii::$app->authManager->checkPermissionAccess('organisation') || Yii::$app->authManager->checkPermissionAccess('preferences') || Yii::$app->authManager->checkPermissionAccess('eventmaster') || Yii::$app->authManager->checkPermissionAccess('errorlogs')) { ?>
                <li id="MenuSystemAdmin" class="nav-bids <?php if ($item == "System Admin") {
                    echo "active"; ?>  <?php } ?>">
                    <a href="javascript:;" class="nav-link ">
                        <div class="floatleft clsprojects clsbackground"><i class="glyphicon glyphicon-user"
                                                                            aria-hidden="true"></i></div>
                        <span class="title">System Admin</span>
                        <span class="selected"></span>
                        <span class="arrow open"></span>
                    </a>
                    <ul class="sub-menu" id="">
                        <?php
                        if (Yii::$app->authManager->checkPermissionAccess('user')) {
                            ?>
                            <li class="" id="settings-users">
                                <a href="<?= yii::$app->urlManager->createUrl('user'); ?>" class="">
                                    <i class="fa fa-users"></i>
                                    Manage Users
                                </a>
                            </li>
                        <?php }
                        if (Yii::$app->authManager->checkPermissionAccess('roles')) {
                            ?>
                            <li class="" id="settings-roles">
                                <a href="<?= yii::$app->urlManager->createUrl('roles'); ?>" class="">
                                    <i class="fa fa-tasks"></i>
                                    Manage Roles
                                </a>
                            </li>
                        <?php }
                        if (Yii::$app->authManager->checkPermissionAccess('organisation')) { ?>
                            <li class="" id="MenuLookupOptions">
                                <a href="<?= yii::$app->urlManager->createUrl('organisation'); ?>" class="">
                                    <i class="fa fa-pencil-square-o"></i>
                                    Setup
                                </a>
                            </li>
                        <?php }
                        if (Yii::$app->authManager->checkPermissionAccess('preferences')) { ?>
                            <li class="" id="settings-preferences">
                                <a href="<?= yii::$app->urlManager->createUrl('preferences'); ?>" class="">
                                    <i class="fa fa-info"></i>
                                    Preferences
                                </a>
                            </li>
                        <?php }
                        if (Yii::$app->authManager->checkPermissionAccess('eventmaster')) { ?>
                            <li class="" id="MenuEvent">
                                <a href="<?= yii::$app->urlManager->createUrl('events'); ?>" class="">
                                    <i class="fa fa-clock-o"></i>
                                    Event Master
                                </a>
                            </li>
                        <?php }
                        if (Yii::$app->authManager->checkPermissionAccess('errorlogs')) { ?>
                            <li class="" id="MenuErrorlog">
                                <a href="<?= yii::$app->urlManager->createUrl('errorlogs'); ?>" class="">
                                    <i class="fa fa-warning"></i>
                                    Error Logs
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>



            <?php if (Yii::$app->authManager->checkPermissionAccess('departments')) { ?>
                <li id="MenuMasterData" class="nav-bids">
                    <a href="javascript:;" class="nav-link ">
                        <div class="floatleft clsprojects clsbackground"><i class="fa fa-database"
                                                                            aria-hidden="true"></i></div>
                        <span class="title">Master Data</span>
                        <span class="selected"></span>
                        <span class="arrow open"></span>
                    </a>
                    <ul class="sub-menu" id="">
                        <?php if (Yii::$app->authManager->checkPermissionAccess('departments')) { ?>
                            <li class="MenuDeoartment" id="settings-department">
                                <a href="<?= yii::$app->urlManager->createUrl('departments'); ?>" class="">
                                    <i class="fa fa-building-o"></i>
                                    Manage Department
                                </a>
                            </li>
                        <?php }
                        if (Yii::$app->authManager->checkPermissionAccess('departments')) { ?>
                            <li class="" id="settings-sections">
                                <a href="<?= yii::$app->urlManager->createUrl('sections'); ?>" class="">
                                    <i class="fa fa-home"></i>
                                    Manage Section
                                </a>
                            </li>
                        <?php }
                        if (Yii::$app->authManager->checkPermissionAccess('departments')) { ?>
                            <li class="" id="settings-sub-sections">
                                <a href="<?= yii::$app->urlManager->createUrl('sub-sections'); ?>" class="">
                                    <i class="fa fa-users"></i>
                                    Manage Subsection
                                </a>
                            </li>
                        <?php } ?>
						
						<?php if (Yii::$app->authManager->checkPermissionAccess('departments')) { ?>
                            <li class="" id="settings-sub-sections">
                                <a href="<?= yii::$app->urlManager->createUrl('views/cause/'); ?>" class="">
                                    <i class="fa fa-users"></i>
                                    RCA Report
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
			
			
        </ul>
    </div>
</div>