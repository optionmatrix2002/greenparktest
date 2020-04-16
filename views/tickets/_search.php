<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\search\AuditsSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .select2-selection__rendered {
        text-align: left !important;
    }
</style>
<?php
$this->registerJs('
 $("document").ready(function()
 {
 
  $(\'.datetimepicker\').datetimepicker({
   format: \'DD-MM-YYYY\',
  });

 });
');

?>


<div class="tickets-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{input}",
            'options' => [
                'tag' => 'false'
            ]
        ],
    ]); ?>
    <div class="col-lg-12 col-md-12 col-sm-12 showfilter">

        <div class="col-lg-3 col-md-3 col-sm-3">
            <?= $form->field($model, 'ticket_name')->textInput(['class' => 'form-control', 'placeholder' => 'Ticket ID'])->label(false); ?>
        </div>


        <div class="col-lg-3 col-md-3 col-sm-3">
            <?php
            echo $form->field($model, 'hotel_id')
                ->dropDownList(ArrayHelper::map(\app\models\Hotels::find()->where(['hotel_status' => 1, 'is_deleted' => 0])->all(), 'hotel_id', 'hotel_name'), [
                    'prompt' => 'Hotel',
                    'onchange' => '
                $.post( "' . Yii::$app->urlManager->createUrl('site/departments?id=') . '"+$(this).val(), function( data ) {
                  $( "select#ticketssearch-department_id" ).html( data );
                });
                
            '
                ], [
                    'class',
                    'form-control'
                ])
                ->label(false);
            ?>

        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">

            <?php
            echo $form->field($model, 'department_id')
                ->dropDownList([], [
                    'prompt' => 'Department',
                    'onchange' => '
                $.post( "' . Yii::$app->urlManager->createUrl('site/audits?id=') . '"+$(this).val(), function( data ) {
                  $( "select#auditssearch-audit_id" ).html( data );
                });
                    
            '], [
                    'class',
                    'form-control'
                ])
                ->label(false);
            ?>
        </div>


        <div class="col-lg-3 col-md-3 col-sm-3">
            <?php
            echo $form->field($model, 'section_id')
                ->dropDownList(ArrayHelper::map(\app\models\Sections::find()->where(['is_deleted' => 0])->all(), 'section_id', 's_section_name'), [
                    'prompt' => 'Section'
                ], [
                    'class',
                    'form-control'
                ])
                ->label(false);
            ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 margin-top-5">

            <?= $form->field($model, 'assigned_user_id')->widget(Select2::classname(), ['data' => ArrayHelper::map(\app\models\User::find()->where(['is_deleted' => 0, 'user_type' => 3, 'is_active' => 1])->all(), 'user_id', function ($element) {
                return $element['first_name'] . ' ' . $element['last_name'];
            }), 'showToggleAll' => false, 'language' => 'en', 'options' => ['placeholder' => 'Staff User'], 'pluginOptions' => ['allowClear' => true]])->label(false); ?>

        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 margin-top-5">
            <?php
            echo $form->field($model, 'priority_type_id')
                ->dropDownList(ArrayHelper::map(\app\models\QuestionPriorityTypes::find()->all(), 'priority_type_id', 'priority_name'), [
                    'prompt' => 'Priority'
                ], [
                    'class',
                    'form-control'
                ])
                ->label(false);
            ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 margin-top-5">
            <?= $form->field($model, 'status')->dropDownList(['1' => 'Assigned', '2' => 'Resolved', '3' => 'Closed','4' => 'Rejected', '5' => 'Cancelled'], ['prompt' => 'Status'], ['class', 'form-control'])->label(false); ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 margin-top-5">
            <?= $form->field($model, 'chronicity')->dropDownList(['0' => 'No', '1' => 'Yes'], ['prompt' => 'Chronicity'], ['class', 'form-control'])->label(false); ?>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 margin-top-5">
            <?= $form->field($model, 'overDueTicket')->dropDownList(['1' => 'No', '2' => 'Yes'], ['prompt' => 'Overdue'], ['class', 'form-control'])->label(false); ?>
        </div>
        <?php /* ?>
        <div class="col-lg-3 col-md-3 col-sm-3">
            <div class="form-group">
                <?= $form->field($model, 'due_date')->textInput(['class' => 'datetimepicker form-control', 'placeholder' => 'Due Date'])->label(false); ?>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
            <div class="form-group">
                <?= $form->field($model, 'updated_at')->textInput(['class' => 'datetimepicker form-control', 'placeholder' => 'Last Updated'])->label(false); ?>
            </div>
        </div>
        <?php */ ?>

        <div class="col-lg-3 col-md-3 col-sm-3 margin-top-5">
            <?= $form->field($model, 'dateAssignedType')->dropDownList(['1' => 'Assigned', '2' => 'Due Date', '3' => 'Last Updated'], ['prompt' => 'Ticket Date Range'], ['class', 'form-control'])->label(false); ?>
        </div>


        <div class="col-lg-3 col-md-3 col-sm-3 margin-top-5">
            <?= $form->field($model, 'startDate')->textInput(['class' => 'datetimepicker form-control', 'placeholder' => 'Start Date'])->label(false); ?>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-3 margin-top-5">
            <?= $form->field($model, 'endDate')->textInput(['class' => 'datetimepicker form-control', 'placeholder' => 'End Date'])->label(false); ?>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-3 margin-top-5">
            <?php
            $audits = ArrayHelper::map(\app\models\AuditsSchedules::find()->where(['status' => 3])->orderBy('audit_id,created_at')->all(), 'audit_schedule_id', 'audit_schedule_name');
            $audits[-1] = 'Dynamic Ticket';
            ?>
            <?= $form->field($model, 'audit_schedule_id')->widget(Select2::classname(), ['data' => $audits, 'showToggleAll' => false, 'language' => 'en', 'options' => ['placeholder' => 'Select Audit'], 'pluginOptions' => ['allowClear' => true]])->label(false); ?>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-3 margin-top-5">

            <?= $form->field($model, 'created_by')->widget(Select2::classname(), ['data' => ArrayHelper::map(\app\models\User::find()->where(['is_deleted' => 0, 'is_active' => 1])->all(), 'user_id', function ($element) {
                return $element['first_name'] . ' ' . $element['last_name'];
            }), 'showToggleAll' => false, 'language' => 'en', 'options' => ['placeholder' => 'Created By'], 'pluginOptions' => ['allowClear' => true]])->label(false); ?>

        </div>
        
        <div class="col-lg-3 col-md-3 col-sm-3 margin-top-5">
            <?= $form->field($model, 'process_critical')->dropDownList(['0' => 'No', '1' => 'Yes'], ['prompt' => 'Process Critical'], ['class', 'form-control'])->label(false); ?>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-3 pull-right text-right margin-top-5">
            <?= Html::submitButton('Go', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Clear', Yii::$app->urlManager->createUrl(['/tickets']), ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>
