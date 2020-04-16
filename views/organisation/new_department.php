<?php ?>
<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title organisation_setup_title"><i class="fa fa-home"></i> <?= $departmentModel->isNewRecord ? 'Add' : 'Edit'; ?> Department</h3>
            <h5 class="text-muted organisation_setup_title">
                <?=$hotelModel->location->locationCity->name; ?>  <i class="fa fa-angle-double-right text-primary" aria-hidden="true"></i>  <?= $hotelModel->hotel_name; ?> 
            </h5>
        </div>
        <div class="modal-body">
            <?php if($departmentModel->isNewRecord){ ?>
            <?= $this->render("department_form", ['hotelModel' => $hotelModel, "departmentModel" => $departmentModel, "hotelDepartmentModel"=>$hotelDepartmentModel]); ?>
            <?php }else{ ?>
                <?= $this->render("department_edit", ['hotelModel' => $hotelModel, "departmentModel" => $departmentModel, "hotelDepartmentModel"=>$hotelDepartmentModel]); ?>
           <?php } ?>
        </div>
    </div>
</div>
