<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
?>
<?php $form = ActiveForm::begin(['id' => 'delete_form', 'action' => yii::$app->urlManager->createUrl('organisation/delete-node')]); ?>
<!-- Modal content-->
<div class="modal-dialog">
	<!-- Modal content-->
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"
				style="color: #fff !important; opacity: 1;" aria-hidden="true">×</button>
			<h4 class="modal-title organisation_setup_title">Confirmation</h4>
		</div>
		<div class="modal-body clearfix">
			<div class="col-sm-12" style="margin-top: 20px;">
                <?= Html::hiddenInput('node_type', $node_type); ?>
                <?= Html::hiddenInput('node_id', $node_id); ?>
                <?= Html::hiddenInput('hotelId', isset($hotelId) ? $hotelId : '' ); ?>
                <?= Html::hiddenInput('departmentId', isset($departmentId) ? $departmentId : ''); ?>
<?php if ($node_type == 'location') { ?>

                    <label>
                        Are you sure you want to delete this <?= ucwords($node_type); ?>? You can't undo this action.

                    </label> <br>
				<!-- <p>Note: This will delete all Hotels, Departments, Sections and
					Sub Sections under it.</p> -->

<?php } else if ($node_type == 'hotel') { ?>
                    <label>
                        Are you sure you want to delete this <?= ucwords($node_type); ?>? You can't undo this action.

                    </label> <br>
				<!-- <p>Note: This will delete all Departmens, Sections and Subsections
					under it.</p>  -->

<?php } else if ($node_type == 'department') { ?>
                    <label>
                        Are you sure you want to delete this <?= ucwords($node_type); ?>? You can't undo this action.

                    </label> <br>
				<!-- <p>Note: This will delete all Sections and Subsections under it.</p> -->

<?php } else if ($node_type == 'section') { ?>

                    <label>
                        Are you sure you want to delete this <?= ucwords($node_type); ?>? You can't undo this action.

                    </label> <br>
			<!-- 	<p>Note: This will delete all Subsections under it.</p> -->

<?php } else { ?>
                    <label>
                        Are you sure you want to delete this <?= ucwords($node_type); ?>? You can't undo this action.

                    </label> 
<?php } ?>

            </div>
		</div>
		<div class="modal-footer clearfix"
			style="border-top: none; margin-top: 5px;">
			<div class="col-sm-12">
				<input id="delete_node_btn" class="btn btn-danger" type="submit"
					value="Delete">
				<button type="button" class="btn btn-Clear" data-dismiss="modal">
					Close</button>
			</div>
		</div>
	</div>
</div>
<?php ActiveForm::end() ?>