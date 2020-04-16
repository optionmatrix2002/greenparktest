<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'Process Critical Preferences';

?>
<style>.theadcolor>a{  color: #fff; text-decoration: underline;}</style>

<div class="process-critical-preferences-index">


    <p>
        
        <a class="btn btn-success pull-right edit_role_info_btn" title="Add RCA Option"  data-action="<?php  echo yii::$app->urlManager->createUrl(["preference/create"]); ?>">
                               Add RCA Option
                            </a>
    </p>
    
    
<?php Pjax::begin(); $buttons='{update} {delete}'; ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items}{pager}',
        'columns' => [
         //   ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'module_id',
                'format' => 'raw',
               // 'header' => 'Module',
                'headerOptions' => ['class' => 'theadcolor'],
                'value' => function ($model) {
                return $model->module->module_name;
                
                }
                ],
                
          //  'critical_preference_id',
                [
                    'attribute' => 'module_option',
                    'format' => 'raw',
                  //  'header' => 'Option',
                    'headerOptions' => ['class' => 'theadcolor'],
                    'value' => function ($model) {
                    return $model->module_option;
                    
                    }
                    ],
                    
                    [
                        'attribute' => 'stop_reminders',
                        'format' => 'raw',
                        'header' => 'Stop Reminders',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'value' => function ($model) {
                        $id=yii::$app->utils->encryptData($model->critical_preference_id);
                        return $model->module_id==2 ? Html::activeCheckbox($model, 'stop_reminders',['label'=>false,'id'=>'stop_reminders_'.$id.'_1','onchange'=>'manageCriticalPreference("'.$id.'",1)']):'';
                        }
                        ],
                        
                        [
                            'attribute' => 'stop_escalations',
                            'format' => 'raw',
                            'header' => 'Stop Escalations',
                            'headerOptions' => ['class' => 'theadcolor'],
                            'value' => function ($model) {
                            $id=yii::$app->utils->encryptData($model->critical_preference_id);
                            return $model->module_id==2 ? Html::activeCheckbox($model, 'stop_escalations',['label'=>false,'id'=>'stop_escalations_'.$id.'_2','onchange'=>'manageCriticalPreference("'.$id.'",2)']):'';
                            
                            }
                            ],
                            
                            /*[
                                'attribute' => 'escalation_period',
                                'format' => 'raw',
                                'header' => 'Escalation Period',
                                'headerOptions' => ['class' => 'theadcolor'],
                                'value' => function ($model) {
                                $id=yii::$app->utils->encryptData($model->critical_preference_id);
                                $checkboxhtml='<a class="pull-right"><i class="fa fa-save" onclick="manageEscalationPeriod(\''.$id.'\')" ></i></a>';
                                return $model->module_id==2 ? Html::textInput('escalation_period',$model->escalation_period,['label'=>false, 'class'=>'pull-left form-control datetimepicker','style'=>'width:90%', 'placeholder'=>'Select Date', 'id'=>"escperiod_".$id]).$checkboxhtml:'';
                                
                                }
                                ],*/


            ['class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
                'headerOptions' => ['class' => 'theadcolor'],
                'template' => $buttons,
                'buttons' => [
                    'update' => function ($url, $model) {
                    $id = yii::$app->utils->encryptData($model->critical_preference_id);
                    return '<a class="clickable_btn edit_role_info_btn" title="Edit"  data-action="' . yii::$app->urlManager->createUrl(["preference/update", "id" => yii::$app->utils->encryptData($model->critical_preference_id)]) . '">
                               <i class="fa fa-edit"></i>
                            </a>';
                    },
                    'delete' => function ($url, $model) {
                    
                    $id = yii::$app->utils->encryptData($model->critical_preference_id);
                    return '<a href="javascript:void(0)" title="Delete" onclick="deleteCriticalPref('."'".$id."'".');" data-token =' .$id . ' ><i class="fa fa-trash-o" title="Delete"></i></a>';
                    
                   
                    },
                    
                    
                    ]
            ],
        ],
        ]);   ?>
</div>

<?php 
$this->registerJs('
$(".edit_role_info_btn").click(function () {
        $("#addrolespop").modal("show").load($(this).data("action"));
    });
');

Pjax::end();
?>


<div id="addrolespop" class="modal fade" role="dialog"></div>


<div id="deletepopup" class="modal fade" role="dialog">
    <?php ActiveForm::begin(['id' => 'delete_process_critical_form', 'action' => yii::$app->urlManager->createUrl('preference/delete'), 'method' => 'post',]) ?>
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="color: #fff!important; opacity: 1;" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body clearfix">
                <input type="hidden" name="id" id="delete_pref_id" value=""/>
                <div class="col-sm-12" style="margin-top: 20px;">
                    <label>
                        Are you sure you want to delete this option? You can't undo this action.
                    </label>
                </div>
            </div>
            <div class="modal-footer clearfix" style="border-top: none; margin-top: 5px;">
                <div class="col-sm-12">
                    <input class="btn btn-danger" type="submit" value="Submit">
                    <button type="button" class="btn btn-Clear" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>