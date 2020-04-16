<?php ?>
<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title organisation_setup_title"><i class="fa fa-map-marker"></i> <?= $locationsModel->isNewRecord ? 'Add' :'Edit'; ?> Location</h3>
        </div>
        <div class="modal-body">
            <div class="row">
                <?= $this->render("location_form", ['locationsModel' => $locationsModel]); ?>
            </div>
        </div>
    </div>
</div>