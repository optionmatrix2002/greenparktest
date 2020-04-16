<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\assets\AppAsset;
use yii\bootstrap\ActiveForm;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SectionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
View::registerCssFile(yii::$app->urlManager->createUrl('css/roles.css'));
View::registerCssFile(yii::$app->urlManager->createUrl('js/jstree/dist/themes/default/style.css'));
View::registerJsFile(yii::$app->urlManager->createUrl('js/jstree/dist/jstree.min.js'), ['depends' => JqueryAsset::className()]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/modelpopup.js'), ['depends' => JqueryAsset::className()]);


$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuSystemAdmin").addClass("active");
$("#settings-users").addClass("active");
', \yii\web\View::POS_END);
?>
<div class="container-fluid">
    <h2>Manage Users</h2>
</div>
<!-- notification text -->
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
    <p id="description-text">
        User details and assignments can be managed from here.
    </p>
</div>
<?php echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="row">
    <div class="col-md-12" style="padding-top:3px;">
        <?php
        if (Yii::$app->authManager->checkPermissionAccess('user/create')) {
            ?>
            <div class="pull-right">
                <a href="<?= yii::$app->urlManager->createUrl('user/create'); ?>" class="btn btn-success"><i
                            class="fa fa-plus"></i>&nbsp;Add User</a>
            </div>
        <?php } ?>
    </div>
</div>
<div class="checklists-index">
    <div class="row">
        <div class="col-sm-12 margintop10">
            <?php $hotelsList = [];
            $departmentList = [];
            ?>
            <?php Pjax::begin(); ?>

            <?php $buttons = '';

            if (Yii::$app->authManager->checkPermissionAccess('user/update')) {
                $buttons .= '{update}';
            }

            if (Yii::$app->authManager->checkPermissionAccess('user/delete')) {
                $buttons .= '&nbsp;&nbsp;{delete}';
            }

            ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'first_name',
                        'header' => 'Full Name',
                        'value' => function ($model) {

                            return Html::a($model->first_name . ' ' . $model->last_name, ['user/user-view', 'id' => Yii::$app->utils->encryptData($model->user_id)], [
                                'title' => Yii::t('yii', 'View'),
                            ]);
                        },
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'theadcolor', 'style' => 'width:12%']
                    ],
                    [
                        'attribute' => 'email',
                        'header' => 'Email',
                        'format' => 'raw',
                        'filter' => false,
                        'headerOptions' => ['class' => 'theadcolor'],
                        'value' => function ($model) {
                            return $model->email;
                        }
                    ],
                    [
                        'attribute' => 'hoteld',
                        'header' => 'Hotel',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'value' => function ($model) {
                            return $model->getUserHotelsData();
                        }
                    ],

                    /* [
                         'attribute' => 'departmentId',
                         'header' => 'Department',
                         'format' => 'raw',
                         'filter' => false,
                         'headerOptions' => ['class' => 'theadcolor'],
                         'value' => function ($model) {
                             return $model->getUserDepartmentsData();
                         }
                     ],*/
                    [
                        'attribute' => 'role_id',
                        'header' => 'Role',
                        'format' => 'raw',
                        'filter' => false,
                        'headerOptions' => ['class' => 'theadcolor'],
                        'value' => function ($model) {
                            return ($model->uiRole) ? $model->uiRole->role_name : '';
                        }
                    ],
                    [
                        'attribute' => 'is_active',
                        'format' => 'raw',
                        'header' => 'Status',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'filter' => false,
                        'value' => function ($model) {

                            if ($model->user_type == 1) {
                                return 'Active';
                            }
                            if (!Yii::$app->authManager->checkPermissionAccess('user/update')) {
                                return $model->is_active == 0 ? 'Inactive' : 'Active';
                            }
                            if ($model->is_active == 0) {

                                return "<a onclick='updatestatus($model->user_id,1);' style='color:red'>Inactive</a>";
                            } else if ($model->is_active == 1) {

                                return "<a onclick='updatestatus($model->user_id,0);'>Active</a>";
                            }
                            return '<a href="" id="' . $model->user_id . '" style="text-decoration: none;' . $color . '" onclick="changeStatus(' . $model->user_id . ');">' . $status . '</a>';

                        }
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'template' => $buttons,
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return $model->user_type == 1 ? '--' : Html::a('<i class="fa fa-edit"></i>', ['user/update', 'id' => Yii::$app->utils->encryptData($model->user_id)], [
                                    'title' => Yii::t('yii', 'Edit'),
                                ]);
                            },

                            'delete' => function ($url, $model) {
                                return $model->user_type == 1 ? '--' : '<a href="javascript:void(0)" title="Delete" class="delete_user_btn" data-token =' . yii::$app->utils->encryptData($model->user_id) . '><i class="fa fa-trash-o" title="Delete"></i></a>';
                            },
                        ]

                    ],
                ],
            ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
<!----------------------Delete Popup Start hare -->
<div id="deletepopup" class="modal fade" role="dialog">
    <?php ActiveForm::begin(['id' => 'delete_department_form', 'action' => yii::$app->urlManager->createUrl('user/delete'), 'method' => 'post',]) ?>
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
                <input type="hidden" name="deletable_user_id" id="deletable_user_id" value=""/>
                <div class="col-sm-12" style="margin-top: 20px;">
                    <label>
                        Are you sure you want to delete this User? You can't undo this action.
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

<script language="javascript" type="text/javascript">
    function updatestatus(id, status) {
        $.ajax({
            url: "<?= Yii::$app->urlManager->createUrl(['user/updatestatus']);?>" + "/" + id,
            data: {status: status},
        }).done(function (data) {
            location.reload(true);
        });

    }
</script>
  