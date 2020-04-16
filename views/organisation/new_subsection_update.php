<div class="modal-dialog">
	<!-- Modal content-->
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h3 class="modal-title organisation_setup_title">  <?=$subSectionsModel->isNewRecord ? 'Edit':''; ?> Subsection</h3>
			<h5 class="text-muted organisation_setup_title">
                 <?=$hotelSubSectionsModel->hotel->location->locationCity->name ?>  <i
					class="fa fa-angle-double-right text-primary" aria-hidden="true"></i>  <?=$hotelSubSectionsModel->hotel->hotel_name ?>  <i
					class="fa fa-angle-double-right text-primary" aria-hidden="true"></i>  <?=$hotelSubSectionsModel->department->department_name; ?> <i
					class="fa fa-angle-double-right text-primary" aria-hidden="true"></i>  <?=$hotelSubSectionsModel->section->s_section_name; ?>
            </h5>
		</div>
		<div class="modal-body">
             <?= $this->render("subsection_update_form",['sectionsModel' => $sectionsModel,'subSectionsModel' => $subSectionsModel,'hotelSubSectionsModel' => $hotelSubSectionsModel]); ?>
         </div>
	</div>
</div>
