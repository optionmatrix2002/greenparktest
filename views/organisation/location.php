<?php

use app\models\Cities;
use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
?>
<?php $form = ActiveForm::begin(['id' => 'location_form', 'action' => yii::$app->urlManager->createAbsoluteUrl('organisation/manage-location')]); ?>
<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Add Locations</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-3 ">
                        <label>States :</label>
                    </div>
                    <div class="col-sm-9 ">
                        <div class="col-sm-10 ">
                            <?=
                             $form->field($statesModel, 'id')->label(false)->widget(Select2::classname(), [
                                'data' => ArrayHelper::map((new yii\db\Query())
                ->select('s.*')
                ->from('{{%states}} s')               
                ->join("INNER JOIN", '{{%countries}} cnt','cnt.id = s.country_id')
                ->where('s.country_id=:country_id',[':country_id'=>101])
                ->all(), 'id', 'name'),
                                'options' => ['placeholder' => 'Select a city ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-3 ">
                        <label>Location :</label>
                    </div>
                    <div class="col-sm-9 ">
                        <div class="col-sm-10 ">
                            <?=
                             $form->field($locationModel, 'location_city_id')->label(false)->widget(Select2::classname(), [
                                'data' => ArrayHelper::map((new yii\db\Query())
                ->select('c.*')
                ->from('{{%cities}} c')
                ->join('INNER JOIN', "{{%states}} s", 's.id = c.state_id ')
                ->join("INNER JOIN", '{{%countries}} cnt','cnt.id = s.country_id')
                ->groupBy('id')
                ->where('s.country_id=:country_id',[':country_id'=>101])
                ->all(), 'id', 'name'),
                                'options' => ['placeholder' => 'Select a city ...'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12" style="margin-top: 20px;">
                    <div class="col-sm-3 ">
                        <label>Description :</label>
                    </div>
                    <div class="col-sm-9 ">
                        <div class="col-sm-10 ">
                           <?=
                             $form->field($locationModel, 'location_description')->label(false);
                            ?>
                        </div>
                    </div>
                </div>
                <div class='col-sm-12' style="margin-top: 20px;">
                    <div class="col-sm-3 ">                                          
                    </div>
                    <div class="col-sm-9 ">
                        <div class="col-sm-10">
                            <button id="locationsave" type="submit" class="btn btn-success">Save</button>
                            <a class="btn btn-default" data-dismiss="modal">Close</a>
                        </div>
                    </div>                          
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>