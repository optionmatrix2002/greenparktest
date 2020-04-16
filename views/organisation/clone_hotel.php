<?php
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
?>


<div class="modal-dialog">
	<!-- Modal content-->
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h3 class="modal-title organisation_setup_title">
				<i class="fa fa-copy"></i> Clone Hotel
			</h3>
		</div>
		<div class="modal-body">
            <?php $form = ActiveForm::begin(['id' => 'clone_hotel_form', 'action' => yii::$app->urlManager->createUrl(['organisation/save-cloned-hotel-info','hotel_id'=> yii::$app->utils->encryptSetUp($hotelModel->hotel_id,'hotel')])]); ?>
            <div class="row">
				<div class="col-sm-12 text-center text-bold">
					<h3 class="text-success ">
						<i class="fa fa-building-o"></i> <?= $hotelModel->hotel_name; ?>
                    </h3>
					<small>(Selected Hotel for cloning)</small>
				</div>
			</div>
			<div class="row">

				<div class="col-sm-12 text-bold">
					<label class="control-label">Choose Target Locations <span class = "required">*</span></label>
                    <?= Select2::widget(['name' => 'selected_locations_list','value' => [],'data' => ArrayHelper::map($locations, 'location_id', 'name'),'maintainOrder' => true,'options' => ['placeholder' => 'Choose Locations','multiple' => true],'pluginOptions' => []]);;?>
                </div>
			</div>
			<div class='col-sm-12' style="margin-top: 20px;">
				<div class="col-sm-12 text-center">
					<button id="clone_hotel_btn" type="submit" class="btn btn-success">Start
						Cloning</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
            <?php ActiveForm::end(); ?>
        </div>
	</div>
</div>