<div class="container-fluid">
    <h2>Assign Audits</h2>
</div>
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="wa-icon wa-icon-notification"></span>
    </span>
    <p id="description-text">
        The upcoming Audits can be assigned through this screen.
    </p>
</div>
<div class="col-sm-12">
    <a href="<?= yii::$app->urlManager->createUrl('audits'); ?>" class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
</div>
<div class="row" style="margin-top: 10px;">
    
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Hotel :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <select class="form-control">
                    <option value="1">GP-Greenlands</option>
                    <option value="2">GP-Waltair</option>
                    <option value="3">Aavasa-Hitech City</option>
                    <option value="4">GP-Ameerpet</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Location :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <select class="form-control">
                    <option value="1">Hyderabad</option>
                    <option value="2">Chennai</option>
                    <option value="3">Visakhapatnam</option>                                    
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
            <label>Section :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <select class="form-control">
                    <option value="1">Public Area</option>
                    <option value="2">Payables</option>
                    <option value="3">Hardware</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Checklist :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">                
                <select class="form-control" id="checklistname">
                    <option value="Engineering Audit">Engineering Audit</option>
                    <option value="Housekeeping Audit">Housekeeping Audit</option>
                    <option value="Human Resources Audit">Human Resources Audit</option>
                    <option value="Purchase Audit">Purchase Audit</option>
                    <option value="Security Audit">Security Audit</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Auditor :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <select class="form-control" id="drpAuditors">
                    <option value="">Ravi Kiran</option>
                    <option value="">Amarendra</option>
                    <option value="">Mahendra</option>
                    <option value="">Krishna Priya</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Start Date :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <input type="text" class="form-control date-picker" placeholder="Start Date" value="">
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>End Date :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <input type="text" class="form-control date-picker" placeholder="End Date" value="">
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label>Delegation Flag :</label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6">
                <input type="checkbox" name="flag" id="flag" value="">
                    <label for="flag"></label>
            </div>
        </div>
    </div>
    <div class="col-sm-12 margintop10">
        <div class="col-sm-2">
            <label></label>
        </div>
        <div class="col-sm-10">
            <div class="input-group col-sm-6 ">                
                <a class="btn btn-success" href="<?= yii::$app->urlManager->createUrl('audits'); ?>">Save</a>
                <a class="btn btn-default" href="<?= yii::$app->urlManager->createUrl('audits'); ?>">Cancel</a>
            </div>
        </div>
    </div>
    <div class="col-sm-12">

    </div>
</div>