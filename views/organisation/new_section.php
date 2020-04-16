<?php
?>
<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title organisation_setup_title"><i class="fa fa-users"></i> <?=$sectionsModel->isNewRecord ? 'Add':'Edit'; ?> Section</h3>
            <h5 class="text-muted organisation_setup_title">
                 <?= $hotelDepartmentModel->hotel->location->locationCity->name ?>  <i class="fa fa-angle-double-right text-primary" aria-hidden="true"></i>  <?= $hotelDepartmentModel->hotel->hotel_name?>  <i class="fa fa-angle-double-right text-primary" aria-hidden="true"></i>  
            </h5>
        </div>
        <div class="modal-body">
            <?php if($sectionsModel->isNewRecord){ ?>           
            <?=$this->render("section_form",['sectionsModel'=>$sectionsModel,'departmentSectionModel'=>$departmentSectionModel,"hotelDepartmentModel"=>$hotelDepartmentModel]); ?>
            <?php }else{ ?>
                <?=$this->render("section_edit",['sectionsModel'=>$sectionsModel,'departmentSectionModel'=>$departmentSectionModel,"hotelDepartmentModel"=>$hotelDepartmentModel]); ?>
            <?php } ?>
        </div>
    </div>
</div>
