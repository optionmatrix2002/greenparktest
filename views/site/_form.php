<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use app\models\Hotels;
use app\models\Departments;
use app\models\Checklists;
use app\models\User;
use app\models\Interval;
use app\assets\AppAsset;
use yii\web\JqueryAsset;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Audits */
/* @var $form yii\widgets\ActiveForm */
?>

<?php 
/*
$this->registerJs('
 $("document").ready(function()
 {
 
  $(\'.datetimepicker\').datetimepicker({
   format: \'DD-MM-YYYY\',
  });

 });
');
*/


AppAsset::register($this);
View::registerCssFile('//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
View::registerJsFile(yii::$app->urlManager->createUrl('js/audits.js'), ['depends' => JqueryAsset::className()]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/modelpopup.js'), ['depends' => JqueryAsset::className()]);
?>

<div class="container-fluid">
    <h2><?=$this->title?></h2>
</div>
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="wa-icon wa-icon-notification"></span>
    </span>
    <p id="description-text">
          New Audits can be scheduled from here. Scroll down to view and manage individual audits.
    </p>
</div>
<div class="col-sm-12">
    <a href="<?= yii::$app->urlManager->createUrl('audits'); ?>" class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
</div>
<div class="row" style="margin-top: 10px;">
   <div class="user-form"">

    <?php $form = ActiveForm::begin(); ?>
   
     
     <div class="col-sm-12 margintop10">
			<div class="col-sm-2">
				<label class="required-label">Location :</label>
			</div>
			<div class="col-sm-10">
				<div class="input-group col-sm-6">
				     <?=  $form->field($model, 'location_id')->dropDownList(ArrayHelper::map(\app\models\Locations::find()->where(['is_deleted' => 0])->all(), 'location_id', 'locationCity.name'), ['prompt'=>'Select Location','disabled' => !$model->isNewRecord] )->label(false); 
	                   ?>
                    </div>
			</div>
	 </div>
     <div class="col-sm-12 margintop10">
			<div class="col-sm-2">
				<label class="required-label">Hotel :</label>
			</div>
			<div class="col-sm-10">
				<div class="input-group col-sm-6">
				      <?php
				       $hotel_data = empty($model->hotel_id) ? [] :[$model->hotel_id=>$model->hotel->hotel_name];
                       if($model->isNewRecord)
                       {
                          echo $form->field($model, 'hotel_id')->widget(DepDrop::classname(), [
                               'options'=>['id'=>'hotel_id'],
                               'data'=>$hotel_data,
                               'pluginOptions'=>[
                                   'depends'=>['audits-location_id'],
                                   'placeholder'=>'Select Hotel',
                                   'url'=>Url::to(['audits/hotel'])
                               ]
                           ])->label(false);
                       }
                       else 
                       {
                           echo $form->field($model, 'hotel_id')->dropDownList($hotel_data, ['disabled' => !$model->isNewRecord])->label(false);
                       }
                        ?>
                    </div>
			</div>
		</div>
     	<div class="col-sm-12 margintop10">
			<div class="col-sm-2">
				<label class="required-label">Department :</label>
			</div>
			<div class="col-sm-10">
				<div class="input-group col-sm-6">
                        <?php
                        $department_data = empty($model->department_id) ? [] : [$model->department_id=>$model->department->department_name];
                        if($model->isNewRecord)
                        {
                            echo $form->field($model, 'department_id')->widget(DepDrop::classname(), [
                                'options'=>['id'=>'department_id'],
                                'data'=>$department_data,
                                'pluginOptions'=>[
                                    'depends'=>['hotel_id'],
                                    'placeholder'=>'Select Department',
                                    'url'=>Url::to(['audits/department'])
                                ]
                            ])->label(false);
                        }
                        else
                        {
                           
                            echo $form->field($model, 'department_id')->dropDownList($department_data, ['disabled'=>'disabled' ] )->label(false);
                        }
                       ?>
             </div>
			</div>
		</div>
       <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label class="required-label">Checklist :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">                
                <?php
                $checklist_data = empty($model->checklist_id) ? [] : [$model->checklist_id => $model->checklist->cl_name];
                if($model->isNewRecord)
                {
                    echo $form->field($model, 'checklist_id')->widget(DepDrop::classname(), [
                        'options'=>['id'=>'checklist_id'],
                        'data'=>$checklist_data,
                        'pluginOptions'=>[
                            'depends'=>['department_id'],
                            'placeholder'=>'Select Checklist',
                            'url'=>Url::to(['audits/check-list'])
                        ]
                        ])->label(false);
                    }
                    else
                    {
                       echo $form->field($model, 'checklist_id')->dropDownList($checklist_data, ['disabled'=>'disabled'] )->label(false);
                    }
                ?>
            </div>
        </div>
       </div>
       <!-- 
       <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label class="required-label">Checklist Frequency:</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">                
                <?php
                if($model->isNewRecord)
                {
                    echo $form->field($model, 'checklistfrequency')->widget(DepDrop::classname(), [
                        'options'=>['id'=>'checklistfrequency'],
                        'pluginOptions'=>[
                            'depends'=>['checklist_id'],
                            'placeholder'=>'Select Frequency',
                            'url'=>Url::to(['audits/get-check-list-frequency'])
                        ]
                        ])->label(false);
                }
                ?>
            </div>
        </div>
       </div> -->
       
       <input type="hidden" id="checklist_url" value="<?= yii::$app->urlManager->createUrl('audits/get-check-list-frequency');?>" >
       
            
      
       <?php if($model->checklist_id){?>
        <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label class="required-label">Frequency :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <?php
                $frequency = Checklists::find()->where(['checklist_id'=>$model->checklist_id])->one();
                $frequencyName = Interval::find()->where(['interval_id'=>$frequency->cl_frequency_value])->one();
                echo  $form->field($model, 'checklist_id')->textInput(['value'=>$frequencyName->interval_name,'class'=>'form-control','disabled'=>'disabled'])->label(false); 
               ?>
          </div>
        </div>
       </div>
       <?php } else{?>
        <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label class="required-label">Frequency :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
            
             <?php 
       
             echo   $form->field($model, 'checklistfrequency')->textInput(['class'=>'form-control','id' => 'getFrequencyName','disabled' => 'disabled'])->label(false); 
             ?>
      
        </div>
        </div>
       </div>
       <?php }?>
        <div class="col-sm-12 margintop10">
            <div class="col-sm-2">
                <label class="required-label">Auditor :</label>
            </div>
            <div class="col-sm-10">
                <div class="input-group col-sm-6">
                
                 <?= $form->field($model,'user_id')->widget(Select2::classname(), ['data' => ArrayHelper::map(\app\models\User::find()->where(['is_deleted' => 0,'user_type' => 2])->all(), 'user_id', 'first_name'),'showToggleAll' => false,'language' => 'en','options' => ['placeholder' => 'Select Auditor','disabled' => !$model->isNewRecord],'pluginOptions' => ['allowClear' => true]])->label(false); ?>
               
                </div>
            </div>
        </div>
        
        <!-- 
        <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label class="required-label">Start Date :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
             <?= 
             $form->field($model, 'start_date')->textInput(['value'=>Yii::$app->formatter->asDate($model->start_date, 'php:d-m-Y'),'class'=>'form-control datetimepicker' ,'id'=>'dateStart','disabled' => !$model->isNewRecord])->label(false); ?>
           </div>
        </div>
      </div>
        
        <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label class="required-label">End Date :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
               <?= $form->field($model, 'end_date')->textInput(['value'=>Yii::$app->formatter->asDate($model->end_date, 'php:d-m-Y'),'class'=>'form-control datetimepicker','id'=>'dateEnd'])->label(false); ?>
            </div>
        </div>
    </div>-->
    
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label class="required-label">Start Date :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
             <?= $form->field($model, 'start_date')->textInput(['value'=> $model->start_date ? Yii::$app->formatter->asDate($model->start_date, "php:d-m-Y") : '','class'=>'datetimepicker form-control' ,'id'=>'dateStart','disabled' => !$model->isNewRecord])->label(false); ?>
           </div>
        </div>
      </div>
        
        <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label class="required-label">End Date :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <?= $form->field($model, 'end_date')->textInput(['value'=>Yii::$app->formatter->asDate($model->end_date, 'php:d-m-Y'),'class'=>'datetimepicker form-control','id'=>'dateEnd',])->label(false); ?>
            </div>
        </div>
    </div>
    
    
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Delegation Flag :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6 textbox-padding">
               <?= $form->field($model, 'deligation_flag')->checkbox([], false)->label(false); ?>
              
            </div>
        </div>
    </div>
   <div class="col-sm-12 margintop10">
			<div class="col-sm-2">
				<label></label>
			</div>
			<div class="col-sm-10">
				<div class="col-sm-6 input-group">                
                        <?= Html::submitButton($model->isNewRecord ? 'Save' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
                        <?= Html::a( 'Cancel',['/audits'],['class'=>'btn btn-default']); ?>
                    </div>
			</div>
		</div>
    <?php ActiveForm::end(); ?>
  </div>
</div>
