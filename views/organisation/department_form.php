<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\widgets\MaskedInput;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\models\HotelDepartments;

$action = "";
if ($departmentModel->isNewRecord) {
    $action = yii::$app->urlManager->createAbsoluteUrl('organisation/manage-department');
} else {
    $action = yii::$app->urlManager->createAbsoluteUrl([
        'organisation/manage-department',
        'department_id' => yii::$app->utils->encryptSetUp($departmentModel->department_id, 'department')
    ]);
}
?>

   <?php $form = ActiveForm::begin(['id' => 'hoteldepartment_form', 'action' => $action]); ?>
<div class="col-sm-12 text-center text-bold">
	<h3 class="text-success ">
		<i class="fa fa-building-o"></i> <?= $hotelModel->hotel_name; ?>
        </h3>
	<small>(selected hotel)</small>
</div>
<div class="col-sm-12">
	
    <?= Html::hiddenInput("encrypted_hotel_id", Yii::$app->utils->encryptSetUp($hotelModel->hotel_id,'hotel')); ?>
    <?= Html::hiddenInput("new-department-action", Yii::$app->urlManager->createAbsoluteUrl('organisation/manage-new-department'),['class'=>'new-department-action']); ?>
    <?= Html::hiddenInput("add-department-action", Yii::$app->urlManager->createAbsoluteUrl('organisation/add-department'),['class'=>'add-department-action']); ?>
    <?php
    
    $existingDepartments = HotelDepartments::find()->where([
        'hotel_id' => $hotelModel->hotel_id,
        'is_deleted' => 0
    ])
        ->asArray()
        ->all();
    $existingDepartments = ArrayHelper::getColumn($existingDepartments, 'department_id');
    
    $departmentsList = ArrayHelper::map(app\models\Departments::find()->where([
        'not in',
        'department_id',
        $existingDepartments
    ])
        ->andWhere([
        'is_deleted' => 0
    ])
        ->all(), 'department_id', 'department_name');
    
    $departmentsList = $departmentsList ? $departmentsList : [];
    ?>
    <div class="dropdownField">
		<div class="col-sm-8 ">
    		<?=$form->field($hotelDepartmentModel, 'department_id')->widget(Select2::classname(), ['data' => $departmentsList,'options' => ['class'=>'departmentId','placeholder' => 'Select departments ...'],'pluginOptions' => ['tokenSeparators' => [',',' ']]])->label('Department Name'. Html::tag('span', '*',['class'=>'required']));?>
    	</div>
		<div class="col-sm-4">
			<div style="margin-top: 40px;">
				<button id="add_new_department_submit_btn"
					onclick="return changeAddDepartmentDiv();" type="button"
					class="btn btn-success">Add New</button>
			</div>
		</div>
	</div>

	<div class="textField" style="display: none">
			<?=$form->field($departmentModel, 'department_name')->textInput(['maxlength' => 50,'class' => 'form-control departmentForm  departmentName charsSpecialChars'])->label('Department Name'. Html::tag('span', '*',['class'=>'required']));?>
			<?= $form->field($departmentModel, 'department_description')->textarea(['rows' => 3,'class' => 'form-control  departmentForm departmentTextArea'])->label('Description'. Html::tag('span', '*',['class'=>'required'])); ?>
		</div>

	<div class='col-sm-12 savenewmultipledep' style="margin-top: 20px;">
		<div class="col-sm-12 text-center">
			<button id="save_multipledepartment_submit_btn" type="button"
				disabled="disabled" class="btn btn-success">Add Department</button>
			<button class="btn btn-default" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>
<div class="textField col-sm-12"
	style="display: none; margin-top: 20px;">
	<div class="text-center" style="margin-top: 3px;">
		<button id="add_new_department_save_btn"
			onclick="return saveNewDepartmentName();" type="button"
			disabled="disabled" class="btn btn-success">Save</button>
		<button id="add_new_department_cancel_btn"
			onclick="return showDropDown();" type="button"
			class="btn btn-default">Cancel</button>
	</div>
</div>


<?php ActiveForm::end() ?>

<script>

	changeAddDepartmentDiv = function (){
       $('.dropdownField').hide();
       $('.textField').show();
       $('button#save_multipledepartment_submit_btn').attr('disabled','disabled');
       $('button#add_new_department_submit_btn') . hide();
       $('.savenewmultipledep') . hide();
    }
    
    showDropDown = function (){
    	$('.dropdownField').show();
    	$('.savenewmultipledep').show();
        $('.textField').hide();
        $('button#save_multipledepartment_submit_btn').removeAttr('disabled');
        $('button#add_new_department_submit_btn').show();
    }

    saveNewDepartmentName=function ()
    {
        var deparmentName = $('.departmentName').val();
        var departmentTextArea = $('.departmentTextArea').val();
        var actionUrl = $('.new-department-action').val();
        if(deparmentName && departmentTextArea){
        	$.ajax({
    			url: actionUrl,
    			data: $('form#hoteldepartment_form').serializeArray(),
    			type: 'POST',
    			success:function(result){
    				result = JSON.parse(result);
    				
    				if(result.status){
    					var list = $("#hoteldepartments-department_id");
    					$.each(result.data, function(key, value) {
        				  list.append(new Option(value,key));
        				});
        				$('form#hoteldepartment_form')[0].reset();
        				showDropDown();
        				toastr.success("Department created successfully");
            		}else{
							var errors = result.error;
							if(errors['department_name']){
								var deparmentNameField = $('.field-departments-department_name');
								deparmentNameField.addClass("has-error");
								deparmentNameField.find('p').html(errors['department_name']);
							}
							if(errors['department_description']){
								var deparmentNameField = $('.field-departments-department_description');
								deparmentNameField.addClass("has-error");
								deparmentNameField.find('p').html(errors['department_description']);
							}
                	}
        			
    			},
    			error:function(){
    			},
            });
        }
        
    }

    $('.departmentForm').on('change keyup',function(){
    	 $('button#add_new_department_save_btn').attr('disabled','disabled');
    	 if($('.departmentName').val() && $('.departmentTextArea').val()){
    		 $('button#add_new_department_save_btn').removeAttr('disabled');
       	 }
     });

    $('.departmentId').on('change',function(){
   	 $('button#save_multipledepartment_submit_btn').attr('disabled','disabled');
	   	 if($(this).val()){
   			 $('button#save_multipledepartment_submit_btn').removeAttr('disabled');
      	 }
    });
    $('button#save_multipledepartment_submit_btn').on('click',function(){
    	var actionUrl = $('.add-department-action').val()
    	if( $('.departmentId').val()){
        	$.ajax({
    			url: actionUrl,
    			data: $('form#hoteldepartment_form').serializeArray(),
    			type: 'POST',
    			success:function(data){
    				response = JSON.parse(data);
    	            if (response.success) {
    	                toastr.success(response.success);
    	                $("#popup_model").modal("hide");
    	                var jsTreeInstance = $('#organisation_hierarchy').jstree(true);
    	                if (response.parent_node) {
    	                    jsTreeInstance.create_node(response.parent_node, response.node);
    	                    jsTreeInstance.open_node(response.parent_node);
    	                    jsTreeInstance.refresh();
    	                } else if (response.node) {
    	                    jsTreeInstance.rename_node(jsTreeInstance.get_node(response.node), response.node.text);
    	                }
    	            } else if (response.error) {
    	                toastr.error(response.error);
    	            }
    			},
    			error:function(){
    			},
            });
        }
       });
    
</script>