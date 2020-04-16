<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\assets\AppAsset;
use yii\bootstrap\ActiveForm;
use yii\web\JqueryAsset;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\DepartmentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Departments';
$this->params['breadcrumbs'][] = $this->title;



AppAsset::register($this);
View::registerCssFile(yii::$app->urlManager->createUrl('css/roles.css'));
View::registerCssFile(yii::$app->urlManager->createUrl('js/jstree/dist/themes/default/style.css'));
View::registerJsFile(yii::$app->urlManager->createUrl('js/jstree/dist/jstree.min.js'), ['depends' => JqueryAsset::className()]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/modelpopup.js'), ['depends' => JqueryAsset::className()]);

$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuMasterData").addClass("active");
$("#settings-department").addClass("active");
', \yii\web\View::POS_END);

?>
<?php
$buttons = '';
if (Yii::$app->authManager->checkPermissionAccess('departments/update')) {
    $buttons .= '{update}';
}
if (Yii::$app->authManager->checkPermissionAccess('departments/delete')) {
    $buttons .= '&nbsp;&nbsp;{delete}';
}
?>
<div class="container-fluid">
    <h2>Manage Department</h2>
</div> 
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
   <p id="description-text">
       Master Data of Departments can be managed from here.
    </p>
</div>
  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="row">
    <div class="col-sm-12 nopadding">
        
        <div class="pull-right">
        <?php 
        if(Yii::$app->authManager->checkPermissionAccess('departments/create'))
        {
        ?>
         <a href="<?= yii::$app->urlManager->createUrl('departments/create'); ?>" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;Add Department</a>
        
        
        <?php }?>
        </div>
       
    </div>
</div>
<div class="departments-index">
<div class="row">
    <!-- table -->
    <div class="col-sm-12 nopadding">


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => '-'],
         'columns' => [
          
                [
                'attribute' => 'department_name',
                'format' => 'raw',
                'header' => 'Department',
                'headerOptions' => ['class' => 'theadcolor'],
                ],
              [
                 'attribute' => 'department_description',
                 'format' => 'raw',
                 'header' => 'Description',
                 'headerOptions' => ['class' => 'theadcolor'],
             ],
             /*[
                 'attribute' => 'department_email',
                 'format' => 'raw',
                 'header' => 'Department Email',
                 'value'=>function ($model){
                 return str_replace(',', ', ', $model->department_email);
                 },
                 'headerOptions' => ['class' => 'theadcolor'],
             ],*/
                
                
              
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Actions',
                    
                    'headerOptions' => ['class' => 'theadcolor'],
                    'template' => $buttons,
                    'buttons' => [
                        'update' => function ($url, $model)
                        {
                            return Html::a('<i class="fa fa-edit"></i>', ['departments/update','id'=>Yii::$app->utils->encryptData($model->department_id)], [
                                'title' => Yii::t('yii', 'Edit'),
                            ]);
                    },
                    
                    'delete' => function ($url, $model)
                    {
                        return '<a href="javascript:void(0)" title="Delete" class="delete_department_btn" data-token ='.yii::$app->utils->encryptData($model->department_id).'><i class="fa fa-trash-o" title="Delete"></i></a>';
                        
                    },
                    
                    
                    ]
                    ],
                
            
           
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
</div>
</div>

<!----------------------Delete Popup Start hare -->
<div id="deletepopup" class="modal fade" role="dialog">
    <?php ActiveForm::begin(['id' => 'delete_department_form', 'action' => yii::$app->urlManager->createUrl('departments/delete'), 'method' => 'post',]) ?>
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="color: #fff!important; opacity: 1;" aria-hidden="true">
                    �
                </button>
                <h4 class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body clearfix">
                <input type="hidden" name="deletable_department_id" id="deletable_department_id" value="" />
                <div class="col-sm-12" style="margin-top: 20px;">
                    <label>
                        Are you sure you want to delete this department? You can't undo this action.
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
