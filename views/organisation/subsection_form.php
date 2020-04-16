<?php
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;
use app\models\HotelDepartmentSections;
use app\models\HotelDepartmentSubSections;
use app\models\SubSections;

$action = "";
if ($subSectionsModel->isNewRecord) {
    $action = yii::$app->urlManager->createAbsoluteUrl('organisation/manage-subsection');
} else {
    $action = yii::$app->urlManager->createAbsoluteUrl([
        'organisation/manage-subsection',
        'subsection_id' => yii::$app->utils->encryptSetUp($subSectionsModel->sub_section_id, 'subsection')
    ]);
}
?>

<?php $form = ActiveForm::begin(['id' => 'subsection_form', 'action' => $action]); ?>
<div class="col-sm-12 text-center text-bold">
	<h3 class="text-success ">
		<i class="fa fa-users"></i> <?= $sectionsModel->section->s_section_name; ?>
    </h3>
	<small>(selected section)</small>
</div>
<div class="col-sm-12">
    <?= Html::hiddenInput("encrypted_section_id", yii::$app->utils->encryptSetUp($sectionsModel->section_id, 'section')); ?>
    <?= Html::hiddenInput("encrypted_hotel_id", yii::$app->utils->encryptSetUp($sectionsModel->hotel_id, 'section')); ?>
    <?= Html::hiddenInput("new-department-action", Yii::$app->urlManager->createAbsoluteUrl('organisation/manage-new-sub-section'),['class'=>'new-department-action']); ?>
    <?= Html::hiddenInput("add-department-action", Yii::$app->urlManager->createAbsoluteUrl('organisation/add-sub-section'),['class'=>'add-department-action']); ?>
    
    <?php
    
    $existingSubSections = HotelDepartmentSubSections::find()->where([
        'hotel_id' => $sectionsModel->hotel_id,
        'section_id' => $sectionsModel->section_id,
        'is_deleted' => 0
    ])
        ->asArray()
        ->all();
    $existingSubSections = ArrayHelper::getColumn($existingSubSections, 'sub_section_id');
    $subSsectionsList = ArrayHelper::map(SubSections::find()->where([
        'not in',
        'sub_section_id',
        $existingSubSections
    ])
        ->andWhere([
        'ss_section_id' => $sectionsModel->section_id,
        'is_deleted' => 0
    ])
        ->all(), 'sub_section_id', 'ss_subsection_name');
    $subSsectionsList = $subSsectionsList ? $subSsectionsList : [];
    ?>
    
    
     <div class="dropdownField">
		<div class="col-sm-8 ">
    		<?=$form->field($hotelSubSectionsModel, 'sub_section_id')->widget(Select2::classname(), ['data' => $subSsectionsList,'options' => ['class'=>'departmentId','placeholder' => 'Select Subsection ...'],'pluginOptions' => ['tokenSeparators' => [',',' ']]])->label('Subsection Name'. Html::tag('span', '*',['class'=>'required'])); ?>
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
			<?=$form->field($subSectionsModel, 'ss_subsection_name')->textInput(['maxlength' => 50,'class' => 'form-control departmentForm  departmentName charsSpecialChars'])->label('Subsection Name'. Html::tag('span', '*',['class'=>'required']));?>
			<?= $form->field($subSectionsModel, 'ss_subsection_remarks')->textarea(['rows' => 3,'class' => 'form-control  departmentForm departmentTextArea'])->label('Description');; ?>
		</div>

	<div class='col-sm-12 savenewmultipledep' style="margin-top: 20px;">
		<div class="col-sm-12 text-center">
			<button id="save_multipledepartment_submit_btn" type="button"
				disabled="disabled" class="btn btn-success">Add Subsection</button>
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
       $('button#add_new_department_submit_btn').hide();
       $('.savenewmultipledep').hide();
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
        if(deparmentName){
        	$.ajax({
    			url: actionUrl,
    			data: $('form#subsection_form').serializeArray(),
    			type: 'POST',
    			success:function(result){
    				result = JSON.parse(result);
    				if(result.status){
    					var list = $("#hoteldepartmentsubsections-sub_section_id");
    					$.each(result.data, function(key, value) {
        				  list.append(new Option(value,key));
        				});
        				$('form#subsection_form')[0].reset();
        				showDropDown();
        				toastr.success("Subsection created successfully");
            		}else{
							var errors = result.error;
							if(errors['ss_subsection_name']){
								var deparmentNameField = $('.field-subsections-ss_subsection_name');
								deparmentNameField.addClass("has-error");
								deparmentNameField.find('p').html(errors['ss_subsection_name']);
							}
							if(errors['ss_subsection_remarks']){
								var deparmentNameField = $('.field-subsections-ss_subsection_remarks');
								deparmentNameField.addClass("has-error");
								deparmentNameField.find('p').html(errors['ss_subsection_remarks']);
							}
                	}
        			
    			},
    			error:function(){
    			},
            });
        }
        
    }

    $('.departmentName').on('change keyup',function(){
    	 if($('.departmentName').val()){
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
    			data: $('form#subsection_form').serializeArray(),
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