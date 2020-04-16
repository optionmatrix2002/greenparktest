<?php
/* @var $this View */

/* @var $content string */

use app\assets\AppAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Roles;

AppAsset::register($this);
View::registerCssFile(yii::$app->urlManager->createUrl('css/roles.css'));
View::registerCssFile(yii::$app->urlManager->createUrl('js/jstree/dist/themes/default/style.css'));
View::registerJsFile(yii::$app->urlManager->createUrl('js/jstree/dist/jstree.min.js'), ['depends' => JqueryAsset::className()]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/roles.js'), ['depends' => JqueryAsset::className()]);

$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuSystemAdmin").addClass("active");
$("#settings-roles").addClass("active");
', \yii\web\View::POS_END);
$this->title = 'Roles';

?>
<div class="container-fluid">
    <h2>Manage Roles</h2>
</div>
<!-- notification text -->
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
    <p id="description-text">
        Roles and their respective permissions set can be configured here.

    </p>
</div>
<?php echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="row">
    <div class="col-sm-12 nopadding">
        <?php
        if (Yii::$app->authManager->checkPermissionAccess('roles/add-role')) {
            ?>
            <div class="pull-right">
                <button class="btn btn-success" id="new_role_btn"
                        data-action="<?= yii::$app->urlManager->createUrl('roles/manage-role'); ?>"><i
                            class="fa fa-plus"></i>&nbsp;Add Role
                </button>
            </div>
        <?php } ?>
    </div>
</div>
<?php Pjax::begin(); ?>
<?php $buttons = '';

if (Yii::$app->authManager->checkPermissionAccess('roles/manage-role')) {
    $buttons .= '{update}';
}

if (Yii::$app->authManager->checkPermissionAccess('roles/delete-role')) {
    $buttons .= '&nbsp;&nbsp;{delete}';
}

?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'role_name',
            'header' => 'Role',
            'value' => function ($model) {
                return $model->role_name;
            },
            'format' => 'raw',
            'headerOptions' => ['class' => 'theadcolor']
        ],
        [
            'attribute' => 'role_name',
            'header' => 'Features - Access',
            'format' => 'raw',
            'headerOptions' => ['class' => 'theadcolor'],
            'value' => function ($model) {
                echo Html::hiddenInput('name', yii::$app->urlManager->createUrl('roles/load-permissions-by-role'), array('id' => 'load_permission_url'));
                $token = Html::encode(yii::$app->utils->encryptData($model->role_main));


                if (Yii::$app->authManager->checkPermissionAccess('roles/load-permissions-by-role')) {
                    return '<a class="load_permissions_link" data-token=' . $token . '">Configure
                            </a>';
                } else {
                    return "--";
                }
            }


        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'headerOptions' => ['class' => 'theadcolor'],
            'template' => $buttons,
            'buttons' => [
                'update' => function ($url, $model) {
                    return '<a class="clickable_btn edit_role_info_btn" title="Edit"  data-action="' . yii::$app->urlManager->createUrl(["roles/manage-role", "id" => yii::$app->utils->encryptData($model->role_main)]) . '">
                               <i class="fa fa-edit"></i>
                            </a>';
                },

                'delete' => function ($url, $model) {
                    return '<a title="Delete" class="delete_role_btn clickable_btn" data-token="' . yii::$app->utils->encryptData($model->role_main) . '">
                                <i class="fa fa-trash-o" title="Delete"></i>
                            </a>';
                },


            ]


        ],
    ],
]); ?>
<?php Pjax::end(); ?>

<div id="AssignUserToRoleModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <h4 class="modal-title">

            </h4>
            <input type="hidden" id="hdnProject">
            <div class="modal-header">
                <button type="button" class="close modaltitlebutton" data-dismiss="modal" aria-hidden="true">�</button>
                <h5><strong>Assign User(s) </strong></h5>
            </div>
            <div class="modal-body clearfix">
                <div class="col-sm-12 martop10px">
                    <div class="row txtboxpadding">
                        <div class="col-sm-5 add-mem">
                            <label class="lblfnt">Available Users:</label><br>
                            <select class="form-control add-member-input fldbrder col-xs-6 height200px"
                                    id="ddlAllClients" multiple="multiple" name="ClientUsersList">
                                <option value="Amulya Singh">Amulya Singh</option>
                                <option value="Bharadwaj Reddy">Bharadwaj Reddy</option>
                                <option value="Edward Karani">Edward Karani</option>
                                <option value="Hannah jain">Hannah jain</option>

                            </select>
                        </div>
                        <div class="col-sm-2 martop85px">
                            <a href="javascript:;"><i class="fa fa-arrow-right addassociate"
                                                      id="AddSelectedClients"></i></a>
                            <br>
                            <a href="javascript:;"><i class="fa fa-arrow-left removeassociate"
                                                      id="RemoveSelectedClients"></i></a>
                        </div>
                        <div class="col-sm-5 add-mem">
                            <label class="lblfnt">Selected Users:</label><br>
                            <select id="ddlSelectedClients" class="form-control height200px" multiple=""></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bordertopnone">
                <div class="col-sm-12 nopadding">
                    <div class="col-sm-3 add-mem">
                    </div>
                    <div class="col-sm-9">
                        <div class="editmodal">
                            <button class="btn btn-success" data-dismiss="modal" id="btnUpdateProjectClientDetails"
                                    title="Update">Update
                            </button>&nbsp;&nbsp;<button type="button" class="btn btn-default" data-dismiss="modal"
                                                         title="Close">Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="addrolespop" class="modal fade" role="dialog"></div>

<div id="deletepopup" class="modal fade" role="dialog">
    <?php ActiveForm::begin(['id' => 'delete_role_form', 'action' => yii::$app->urlManager->createUrl('roles/delete-role')]) ?>
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="color: #fff!important; opacity: 1;"
                        aria-hidden="true">
                    �
                </button>
                <h4 class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body clearfix">
                <input type="hidden" name="deletable_role_id" id="deletable_role_id" value=""/>
                <div class="col-sm-12" style="margin-top: 20px;">
                    <label>
                        Are you sure you want to delete this Role? You can't undo this action.
                    </label>
                </div>
            </div>
            <div class="modal-footer clearfix" style="border-top: none; margin-top: 5px;">
                <div class="col-sm-12">
                    <input class="btn btn-danger" type="submit" value="Delete">
                    <button type="button" class="btn btn-Clear" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>
<div id="editrolespop" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Role</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-2 nopadding">
                            <label>Role :</label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <div class="col-sm-10 nopadding">
                                <input type="text" class="form-control" value="Executive Director"/>
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-12' style="margin-top: 20px;">
                        <div class="col-sm-2 nopadding">
                        </div>
                        <div class="col-sm-9 nopadding">
                            <a class="btn btn-success" data-dismiss="modal">Update</a>
                            <a class="btn btn-default" data-dismiss="modal">Close</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- property modal-->
<div id="propertymodal" class="modal fade in" role="dialog" aria-hidden="false">
    <div class="modal-backdrop fade in"></div>
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">�</button>
                <h4><strong>Properties - Access</strong></h4>
            </div>
            <div class="modal-body">
                <div class="col-sm-12">
                    <div class="input-group col-sm-6">
                        <div id="tree_25" class="">
                            <ul class="">
                                <li class="jstree-open">
                                    <label id="editlocation" class="changeEditdiv">Hyderabad</label>
                                    <ul>
                                        <li class="jstree-open">
                                            <label id="edithotel" class="changeEditdiv">GreenPark - Ameerpet</label>
                                            <ul>
                                                <li class="jstree-open">
                                                    <label id="editdepartment" class="changeEditdiv">Engineering</label>
                                                    <ul>
                                                        <li class="jstree">
                                                            <label id="editsection" class="changeEditdiv">Preventive
                                                                Maintenance Schedule</label>
                                                            <ul>
                                                                <li id="editsubsection" class="changeEditdiv">Boilers
                                                                </li>
                                                                <li>AC</li>
                                                            </ul>
                                                        </li>
                                                        <li>Performance Of Machinaries</li>
                                                        <li>Annual Maintenance Contracts</li>
                                                        <li>Monthly Information System</li>
                                                        <li>Heat Light, R&M Expenditure</li>
                                                        <li>Ken Fixit Rooms</li>
                                                        <li>Capex / Rennovations</li>
                                                    </ul>
                                                </li>
                                                <li>Finance</li>
                                                <li>Human Resource</li>
                                                <li>F&B</li>
                                                <li>House Keeping</li>
                                                <li>Security</li>
                                                <li>Front Desk</li>
                                                <li>Administration</li>
                                            </ul>
                                        </li>
                                        <li class="jstree">
                                            <label>AVASA - HiTech</label>
                                            <ul>
                                                <li>Engineering</li>
                                                <li>Finance</li>
                                                <li>Human Resource</li>
                                                <li>F&B</li>
                                                <li>House Keeping</li>
                                                <li>Security</li>
                                                <li>Front Desk</li>
                                                <li>Administration</li>

                                            </ul>
                                        </li>
                                    </ul>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-success" data-toggle="modal" data-target="#DocUploadApprove" data-dismiss="modal">Submit</a>
                <a class="btn btn-default" data-dismiss="modal">Close</a>
            </div>
        </div>
    </div>
</div>
<!--  popup adding for permissions -->
<div id="addPermissionsModal" class="modal fade" role="dialog"></div>