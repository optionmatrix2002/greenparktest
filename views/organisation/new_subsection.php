<?php
?>
<div class="modal-dialog">
	<!-- Modal content-->
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h3 class="modal-title organisation_setup_title">  <?=$subSectionsModel->isNewRecord ? 'Add':'Edit'; ?> Subsection</h3>
			<h5 class="text-muted organisation_setup_title">
                 <?= $sectionsModel->hotel->location->locationCity->name ?>  <i
					class="fa fa-angle-double-right text-primary" aria-hidden="true"></i>  <?=$sectionsModel->hotel->hotel_name ?>  <i
					class="fa fa-angle-double-right text-primary" aria-hidden="true"></i>  <?=$sectionsModel->department->department_name; ?>
            </h5>
		</div>
		<div class="modal-body">
            <?=$this->render("subsection_form",['sectionsModel'=>$sectionsModel,'hotelSubSectionsModel' => $hotelSubSectionsModel,"subSectionsModel"=>$subSectionsModel]); ?>
        </div>
	</div>
</div>
