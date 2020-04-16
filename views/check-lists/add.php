<div class="container-fluid">
    <h2>Add Checklist </h2>
</div>
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="wa-icon wa-icon-notification"></span>
    </span>
    <p id="description-text">
        New hotel checklists can be added here.
    </p>
</div>
<div class="col-sm-12">
    <a href="<?= yii::$app->urlManager->createUrl('check-lists'); ?>" class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="col-sm-12">
        <div class="col-sm-2">
            <label>Checklist ID :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <input type="text" class="form-control" placeholder="Checklist ID" value="" />
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Checklist Name :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <input type="text" class="form-control" placeholder="Checklist Name" value="" />
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Audit Type :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <div class="col-sm-6" style="padding: 0;" id="auditExternal">
                    <input type="radio" name="gender" id="ExternalAudit" value="External">
                    <label for="ExternalAudit">External</label>
                </div>
                <div class="col-sm-6" style="padding: 0;" id="auditInternal">
                    <input type="radio" name="gender" id="InternalAudit" value="Internal">
                    <label for="InternalAudit">Internal</label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Audit Method  :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <select class="form-control">
                    <option value="1">Routine</option>
                    <option value="2">Surprise</option>
                    <option value="3">Special</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Department :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <select class="form-control">
                    <option value="1">Human Resources</option>
                    <option value="2">Maintenance</option>
                    <option value="3">Kitchen</option>
                    <option value="4">Accounting</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Frequency :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <select class="form-control">
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="quaterly">Quarterly</option>
                    <option value="halfyearly">Half Yearly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Audit Span :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <div class="col-sm-6" style="padding: 0;" id="auditspan">
                    <input type="radio" name="gender" id="areaspecific" value="External">
                    <label for="ExternalAudit">Section Specific</label>
                </div>
                <div class="col-sm-6" style="padding: 0;" id="auditspan">
                    <input type="radio" name="gender" id="acrosssarea" value="Internal">
                    <label for="InternalAudit">Across Section</label>
                </div>                                
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Status :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <select class="form-control">
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label></label>
        </div>
        <div class="col-sm-10">
            <div class="col-sm-6 input-group">                
                <a class="btn btn-success" href="<?= yii::$app->urlManager->createUrl('check-lists/addquesionnaire'); ?>">Save & Proceed to Questionnaire</a>
                <a class="btn btn-default" href="<?= yii::$app->urlManager->createUrl('check-lists'); ?>">Cancel</a>
            </div>
        </div>
    </div>

    <div class="col-sm-12">

    </div>
</div><div class="container-fluid">
    <h2>Add Checklist </h2>
</div>
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="wa-icon wa-icon-notification"></span>
    </span>
    <p id="description-text">
        New hotel checklists can be added here.
    </p>
</div>
<div class="col-sm-12">
    <a href="<?= yii::$app->urlManager->createUrl('check-lists'); ?>" class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="col-sm-12">
        <div class="col-sm-2">
            <label>Checklist ID :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <input type="text" class="form-control" placeholder="Checklist ID" value="" />
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Checklist Name :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <input type="text" class="form-control" placeholder="Checklist Name" value="" />
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Audit Type :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <div class="col-sm-6" style="padding: 0;" id="auditExternal">
                    <input type="radio" name="gender" id="ExternalAudit" value="External">
                    <label for="ExternalAudit">External</label>
                </div>
                <div class="col-sm-6" style="padding: 0;" id="auditInternal">
                    <input type="radio" name="gender" id="InternalAudit" value="Internal">
                    <label for="InternalAudit">Internal</label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Audit Method  :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <select class="form-control">
                    <option value="1">Routine</option>
                    <option value="2">Surprise</option>
                    <option value="3">Special</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Department :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <select class="form-control">
                    <option value="1">Human Resources</option>
                    <option value="2">Maintenance</option>
                    <option value="3">Kitchen</option>
                    <option value="4">Accounting</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Frequency :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <select class="form-control">
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="quaterly">Quarterly</option>
                    <option value="halfyearly">Half Yearly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Audit Span :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <div class="col-sm-6" style="padding: 0;" id="auditspan">
                    <input type="radio" name="gender" id="areaspecific" value="External">
                    <label for="ExternalAudit">Section Specific</label>
                </div>
                <div class="col-sm-6" style="padding: 0;" id="auditspan">
                    <input type="radio" name="gender" id="acrosssarea" value="Internal">
                    <label for="InternalAudit">Across Section</label>
                </div>                                
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Status :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <select class="form-control">
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label></label>
        </div>
        <div class="col-sm-10">
            <div class="col-sm-6 input-group">                
                <a class="btn btn-success" href="<?= yii::$app->urlManager->createUrl('check-lists/addquesionnaire'); ?>">Save & Proceed to Questionnaire</a>
                <a class="btn btn-default" href="<?= yii::$app->urlManager->createUrl('check-lists'); ?>">Cancel</a>
            </div>
        </div>
    </div>

    <div class="col-sm-12">

    </div>
</div>