<?php
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;
use app\models\HotelDepartmentSections;
use app\models\Sections;

$action = "";
if ($sectionsModel->isNewRecord) {
    $action = yii::$app->urlManager->createAbsoluteUrl('organisation/manage-section');
} else {
    $action = yii::$app->urlManager->createAbsoluteUrl([
        'organisation/manage-section',
        'section_id' => yii::$app->utils->encryptSetUp($sectionsModel->section_id, 'section')
    ]);
}
?>

<?php $form = ActiveForm::begin(['id' => 'section_form', 'action' => $action]); ?>
<div class="col-sm-12 text-center text-bold">
	<h3 class="text-success ">
		<i class="fa fa-home"></i> <?= $hotelDepartmentModel->department->department_name; ?>
    </h3>
	<small>(selected department)</small>
</div>

<div class="col-sm-12">
    <?= Html::hiddenInput("encrypted_department_id", yii::$app->utils->encryptSetUp($hotelDepartmentModel->department_id,'department')); ?>
    <?= Html::hiddenInput("encrypted_hotel_id", yii::$app->utils->encryptSetUp($hotelDepartmentModel->hotel_id,'department')); ?>
    <?= Html::hiddenInput("add-section", yii::$app->urlManager->createAbsoluteUrl('organisation/create-new-section'),['class'=>'add-section']); ?>
    <?= Html::hiddenInput("assign-section", yii::$app->urlManager->createAbsoluteUrl('organisation/assign-section'),['class'=>'assign-section']); ?>
    
    <?php
    
    $existingSections = HotelDepartmentSections::find()->where([
        'hotel_id' => $hotelDepartmentModel->hotel_id,
        'department_id' => $hotelDepartmentModel->department_id,
        'is_deleted' => 0
    ])
        ->asArray()
        ->all();
    
    
    $existingSections = ArrayHelper::getColumn($existingSections, 'section_id');
    $sectionsList = ArrayHelper::map(Sections::find()->where([
        'not in',
        'section_id',
        $existingSections
    ])
        ->andWhere([
            's_department_id' => $hotelDepartmentModel->department_id, 'is_deleted' => 0
        ])
        ->all(), 'section_id', 's_section_name');
    
    $sectionsList = $sectionsList ? $sectionsList : [];
    ?>
    
    
     <div class="dropdownField">
		<div class="col-sm-8 ">
			<?=$form->field($departmentSectionModel, 'section_id')->widget(Select2::classname(), ['data' => $sectionsList,'options' => ['class'=>'sectionId','placeholder' => 'Select Section ...'],'pluginOptions' => ['tokenSeparators' => [',',' ']]])->label('Section Name'. Html::tag('span', '*',['class'=>'required']));?>
		</div>
		<div class="col-sm-4">
			<div style="margin-top: 40px;">
				<button id="add_new_section_submit_btn"
					onclick="return changeAddSectionDiv();" type="button"
					class="btn btn-success">Add New</button>
			</div>
		</div>
	</div>

	<div class="textField" style="display: none">
		<?= $form->field($sectionsModel, 's_section_name')->textInput(['maxlength' => 50,'class'=>'sectionName form-control charsSpecialChars'])->label('Section Name'. Html::tag('span', '*',['class'=>'required'])); ?>
			<?= $form->field($sectionsModel, 's_section_remarks')->textarea(['class'=>'form-control  sectionData sectionDescription','rows' => 3])->label('Description');  ?>
		</div>
	<div class='col-sm-12 savenewmultipledep' style="margin-top: 20px;">
		<div class="col-sm-12 text-center">
			<button id="save_multiplesection_submit_btn" type="button"
				disabled="disabled" class="btn btn-success">Add Section</button>
			<button class="btn btn-default" data-dismiss="modal">Close</button>
		</div>
	</div>

</div>

<div class="textField col-sm-12"
	style="display: none; margin-top: 20px;">
	<div class="text-center" style="margin-top: 3px;">
		<button id="add_new_section_save_btn"
			onclick="return saveNewSectionName();" type="button"
			disabled="disabled" class="btn btn-success">Save</button>
		<button id="add_new_section_cancel_btn"
			onclick="return showDropDown();" type="button"
			class="btn btn-default">Cancel</button>
	</div>
</div>


<?php ActiveForm::end() ?>


<script>

changeAddSectionDiv  = function (){
       $('.dropdownField').hide();
       $('.textField').show();
       $('button#save_multiplesection_submit_btn').attr('disabled','disabled');
       $('button#add_new_section_submit_btn').hide();
       $('.savenewmultipledep').hide();
    }
    
    showDropDown = function (){
    	$('.dropdownField').show();
    	$('.savenewmultipledep').show();
        $('.textField').hide();
        $('button#save_multiplesection_submit_btn').removeAttr('disabled');
        $('button#add_new_section_submit_btn').show();
    }

    saveNewSectionName=function ()
    {
        var deparmentName = $('.sectionName').val();
        var departmentTextArea = $('.sectionDescription').val();
        var actionUrl = $('.add-section').val();
        if(deparmentName){
        	$.ajax({
    			url: actionUrl,
    			data: $('form#section_form').serializeArray(),
    			type: 'POST',
    			success:function(result){
    				result = JSON.parse(result);
    				if(result.status){
    					var list = $("#hoteldepartmentsections-section_id");
    					$.each(result.data, function(key, value) {
        				  list.append(new Option(value,key));
        				});
        				$('form#section_form')[0].reset();
        				showDropDown();
        				toastr.success("Section created successfully");
            		}else{
							var errors = result.error;
							if(errors['s_section_name']){
								var deparmentNameField = $('.field-sections-s_section_name');
								deparmentNameField.addClass("has-error");
								deparmentNameField.find('p').html(errors['s_section_name']);
							}
							if(errors['department_description']){
								var deparmentNameField = $('.field-sections-s_section_remarks');
								deparmentNameField.addClass("has-error");
								deparmentNameField.find('p').html(errors['s_section_remarks']);
							}
                	}
        			
    			},
    			error:function(){
    			},
            });
        }
        
    }

     $('.sectionName').on('change keyup',function(){
    	 if($('.sectionName').val()){
    		 $('button#add_new_section_save_btn').removeAttr('disabled');
       	 }
     });
 
    $('.sectionId').on('change',function(){
   	 $('button#save_multiplesection_submit_btn').attr('disabled','disabled');
	   	 if($(this).val()){
   			 $('button#save_multiplesection_submit_btn').removeAttr('disabled');
      	 }
    });
    $('button#save_multiplesection_submit_btn').on('click',function(){
    	var actionUrl = $('.assign-section').val()
    	if( $('.sectionId').val()){
        	$.ajax({
    			url: actionUrl,
    			data: $('form#section_form').serializeArray(),
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
