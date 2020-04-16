<?php

use app\models\Checklists;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ChecklistsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manage Checklists';
$this->params['breadcrumbs'][] = $this->title;
View::registerJsFile(yii::$app->urlManager->createUrl('js/modelpopup.js'), ['depends' => JqueryAsset::className()]);

$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuChecklists").addClass("active");
', \yii\web\View::POS_END);

?>
<?php
$this->registerJs('
$("document").ready(function(){ $( "#layout-client-li" ).addClass( "active" );  });
function changeStatus(checklist_id) 
{
   
 url = "' . Yii::$app->getUrlManager()->createUrl('check-lists/update-status') . '";

    $.ajax({
        url: url,
        type: "POST",
        data: {status: checklist_id},
        success: function (status)
        {
        location.reload();
			toastr.success("Status Updated Successfully");
        }
    }); 
}
', View::POS_END);
?>
<div class="container-fluid">
    <h2><?= $this->title; ?> </h2>
</div>
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
    <p id="description-text">
        Audit checklists can be configured here.
    </p>
</div>
<?php echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="row">
    <div class="col-sm-12">
        <?php if (Yii::$app->authManager->checkPermissionAccess('check-lists/create')) { ?>
            <div class="pull-right">
                <a href="<?= yii::$app->urlManager->createUrl('check-lists/create'); ?>" class="btn btn-success"><i
                            class="fa fa-plus"></i>&nbsp;Add Checklist</a>
            </div>
        <?php } ?>
    </div>
</div>
<div class="checklists-index">
    <div class="row">
        <div class="col-sm-12 margintop10">

            <?php $buttons = '';

            if (Yii::$app->authManager->checkPermissionAccess('check-lists/delete')) {
                $buttons .= '{delete}';
            }

            if (Yii::$app->authManager->checkPermissionAccess('check-lists/update')) {
                $buttons .= '&nbsp;&nbsp;{update}';
            }
            ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [

                    [
                        'attribute' => 'cl_name',
                        'header' => 'Checklist Name',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'theadcolor'],
                    ],

                    [
                        'attribute' => 'cl_audit_type',
                        'header' => 'Audit Type',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'value' => function ($model) {

                            return $model->cl_audit_type == 0 ? "Internal" : "External";
                        },
                    ],
                    [
                        'attribute' => 'cl_department_id',
                        'header' => 'Department',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'value' => function ($model) {
                            return $model->clDepartment->department_name;
                        },
                    ],
                    [
                        'attribute' => 'cl_audit_span',
                        'header' => 'Audit Span',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'value' => function ($model) {
                            return $model->cl_audit_span == 1 ? ' Section Specific' : 'Across Sections';

                        },

                    ],
                    [
                        'attribute' => 'cl_frequency_value',
                        'header' => 'Frequency',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'value' => function ($model) {
                            switch ($model->cl_frequency_value) {
                                case 1:
                                    $frequency = "Weekly";
                                    break;
                                case 2:
                                    $frequency = "Bi-Weekly";
                                    break;
                                case 3:
                                    $frequency = "Monthly";
                                    break;
                                case 4:
                                    $frequency = "Quarterly";
                                    break;
                                case 5:
                                    $frequency = "Half-Yearly";
                                    break;
                                case 6:
                                    $frequency = "Yearly";
                                    break;
                            }

                            return $frequency;

                        },

                    ],
                    [
                        'attribute' => 'cl_status',
                        'format' => 'raw',
                        'header' => 'Status',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'filter' => array("0" => "Inactive", "1" => "Active"),
                        'value' => function ($model) {
                            if ($model->cl_status == 0) {
                                $status = "Inactive";
                                $color = "color:red;";
                            } else if ($model->cl_status == 1) {
                                $status = "Active";
                                $color = null;
                            }
                            if (Yii::$app->authManager->checkPermissionAccess('check-lists/update')) {
                                return '<a  href="javascript:void(0)" id="' . $model->checklist_id . '" style="text-decoration: none;' . $color . '" onclick="changeStatus(' . $model->checklist_id . ');">' . $status . '</a>';
                            } else {
                                return $status;
                            }
                        }
                    ],
                    [
                        'attribute' => 'checklist_id',
                        'format' => 'raw',
                        'header' => 'Questions',
                        'headerOptions' => ['class' => 'theadcolor'],

                        'value' => function ($model) {
                            $color = "color:red;";
                            if($model->cl_audit_span == 1)
                            {
                                $getQuestionsCount = Checklists::getCheckListQuestionsCount($model->checklist_id);
                                return $getQuestionsCount == 0 ? '<span style="' . $color . '">No Questions</span>' : '<a href="' . yii::$app->urlManager->createUrl('check-lists/view-questionnaire?id=' . Yii::$app->utils->encryptData($model->checklist_id)) . '" title="View Quations">View</a>' .' '.'<a title="Total Questions">('.$getQuestionsCount.')</a>';
                            }
                            else
                            {
                                $getAcrossSectionQuestions = Checklists::getCheckListAcrossSectionQuestionsCount($model->checklist_id);
                                return $getAcrossSectionQuestions == 0 ? '<span style="' . $color . '">No Questions</span>' : '<a href="' . yii::$app->urlManager->createUrl('check-lists/view-questionnaire?id=' . Yii::$app->utils->encryptData($model->checklist_id)) . '" title="View Quations">View</a>' .' '.'<a title="Total Questions">('.$getAcrossSectionQuestions.')</a>';
                            }
                        }
                    ],
                    [

                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'template' => $buttons,
                        'buttons' => [
                            'delete' => function ($url, $model) {
                                return '<a href="javascript:void(0)" title="Delete" class="delete_checklist_btn" data-token =' . yii::$app->utils->encryptData($model->checklist_id) . '><i class="fa fa-trash-o" title="Delete"></i></a>';
                            },

                            'update' => function ($url, $model) {
                                if (!$model->cl_status) {
                                    return Html::a('<i class="fa fa-edit"></i>', ['check-lists/update', 'id' => Yii::$app->utils->encryptData($model->checklist_id)], [
                                        'title' => Yii::t('yii', 'Edit'),
                                    ]);
                                }

                            },


                        ]


                    ],
                ],
            ]); ?>

        </div>
    </div>
</div>

<div id="deletepopup" class="modal fade" role="dialog">
    <?php ActiveForm::begin(['id' => 'delete_checklist_form', 'action' => yii::$app->urlManager->createUrl('check-lists/delete'), 'method' => 'post',]) ?>
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
                <input type="hidden" name="deletable_checklist_id" id="deletable_checklist_id" value=""/>
                <div class="col-sm-12" style="margin-top: 20px;">
                    <label>
                        Are you sure you want to delete this checklist? You can't undo this action.
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