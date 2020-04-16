<?php

use app\models\Cities;
use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
?>
<?php $form = ActiveForm::begin(['id' => 'hotel_form', 'action' => yii::$app->urlManager->createAbsoluteUrl('organisation/manage-hotel')]); ?>
<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Add Hotel</h4>
        </div>
        <div class="modal-body">
            <div class="row" >

                    <div class="col-sm-12">
                        <div class="col-sm-12 ">
                            <label>Location :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                               <?=
                             $form->field($hotelModel, 'location_id')->label(false)->widget(Select2::classname(), [
                                'data' => ArrayHelper::map((new yii\db\Query())
                ->select('c.*,l.*')
                ->from('{{%locations}} l')
                ->join('INNER JOIN', "{{%cities}} c", 'c.id = l.location_city_id ')
                ->where('l.location_city_id=:location_city_id',[':location_city_id'=>4460])
                ->all(), 'location_id', 'name'),
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
                        <div class="col-sm-12 ">
                            <label>Hotel Name :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                 <?= $form->field($hotelModel, 'hotel_name')->label(false); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Contact :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <?= $form->field($hotelModel, 'hotel_phone_number')->label(false); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Address :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                               <?= $form->field($hotelModel, 'hotel_address')->label(false); ?>
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-12' style="margin-top: 20px;">                                       
                        <div class="col-sm-12  pull-right">
                            <button id="hotelsave" type="submit" class="btn btn-success">Save</button>
                            <a class="btn btn-default">Close</a>      
                        </div>                    
                    </div>
                </div>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>