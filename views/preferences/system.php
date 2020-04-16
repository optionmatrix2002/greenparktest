<?php 

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\widgets\ActiveForm;
use app\models\Preferences;



$buttons = '';

if (Yii::$app->authManager->checkPermissionAccess('preferences/update')) {
    $buttons .= '{update}';
}
?>

<div class="preferences-index">
    <div class="row">
        <div class="col-sm-12 margintop10">

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{items}{pager}',
                'columns' => [
                    [
                        'attribute' => 'preferences_label',
                        'format' => 'raw',
                        'header' => 'Name',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'value' => function ($model) {
                            return $model->preferences_label;

                        }
                    ],
                    [
                        'attribute' => 'preferences_description',
                        'format' => 'raw',
                        'header' => 'Description',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'value' => function ($model) {
                            return $model->preferences_description;

                        }
                    ],
                    [
                        'attribute' => 'preferences_value',
                        'format' => 'raw',
                        'header' => 'Previous Value',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'value' => function ($model) {

                            if ($model->preferences_id == 4 || $model->preferences_id == 5) {

                                if ($model->preferences_value == 1) {
                                    return $model->preferences_value . " Day";
                                } else {
                                    return $model->preferences_value . " Days";
                                }


                            } else if ($model->preferences_id == 8 || $model->preferences_id == 9 || $model->preferences_id == 10) {

                                if ($model->preferences_value == 1) {
                                    return $model->preferences_value . " Day";
                                } else if ($model->preferences_value == 0) {
                                    return "--";
                                } else {
                                    return $model->preferences_value . " Days";
                                }

                            } else if ($model->preferences_id == 7) {

                                return "Audit Score " . $model->preferences_value;
                            } else {
                                return $model->preferences_value;

                            }

                        }
                    ],

                    [
                        'attribute' => 'preferences_type',
                        'format' => 'raw',
                        'header' => 'New Value',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'value' => function ($model) {
                            switch ($model->preferences_id) {
                                case 1:
                                    return Html::activeDropDownList($model, 'preferences_value', Preferences::getSelectNewValueArry(), ['class' => 'form-control preference_type', 'id' => 'preference_one']);
                                    break;
                                case 2:
                                    return Html::textInput('text', '', ['class' => 'form-control preference_type', 'max' => 55, 'id' => 'preference_two', 'placeholder' => 'Email ID']);

                                    break;
                                case 3:
                                    return Html::textInput('text', '', ['class' => 'form-control preference_type numbers', 'type' => 'text', 'id' => 'preference_three', 'placeholder' => 'Mobile Number', 'min' => 10, 'max' => 11]);
                                    break;
                                case 4:
                                    return Html::activeDropDownList($model, 'preferences_value', Preferences::getSelectAuditorReminder(), ['class' => 'form-control   preference_type', 'id' => 'preferencene_fourth']);
                                    break;
                                case 5:

                                    return Html::activeDropDownList($model, 'preferences_value', Preferences::getSelectEventReminder(), ['class' => 'form-control   preference_type', 'id' => 'preferencene_fifth']);

                                    break;
                                case 6:
                                    return Html::activeDropDownList($model, 'preferences_value', Preferences::getSelectRatingSliderArry(), ['class' => 'form-control preference_type', 'id' => 'preferencene_sixth']);
                                    break;
                                case 7:
                                    return Html::textInput('text', '', ['type' => 'text', 'class' => 'form-control preference_type ', 'id' => 'preferencene_seventh', 'placeholder' => 'Audit Score(1-99)', 'min' => 1, 'max' => 99]);
                                    break;
                                case 8:
                                    return Html::textInput('text', '', ['type' => 'text', 'class' => 'form-control preference_type ', 'id' => 'preferencene_eight', 'placeholder' => 'Days(1-99)', 'min' => 1, 'max' => 99]);
                                    break;
                                case 9:
                                    return Html::textInput('text', '', ['type' => 'text', 'class' => 'form-control preference_type ', 'id' => 'preferencene_nine', 'placeholder' => 'Days(1-99)', 'min' => 1, 'max' => 99]);
                                    break;
                                case 10:
                                    return Html::textInput('text', '', ['type' => 'text', 'class' => 'form-control preference_type ', 'id' => 'preferencene_ten', 'placeholder' => 'Days(1-99)', 'min' => 1, 'max' => 99]);
                                    break;
                            }

                        }
                    ],

                    [

                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'theadcolor'],
                        'template' => $buttons,
                        'buttons' => [
                            'update' => function ($url, $model) {

                                $id = $model->preferences_id;
                                return '<a href="javascript:void(0)" title="Save" class="update_preference_btn" data-token =' . yii::$app->utils->encryptData($id) . ' >Save</a>';

                            },


                        ]


                    ],
                ],
            ]); ?>

        </div>
    </div>
</div>

<!----------------------Update Preference Popup Start hare -->
<div id="updatepopup" class="modal fade" role="dialog">
    <?php ActiveForm::begin(['id' => 'update_user_form', 'action' => yii::$app->urlManager->createUrl('preferences/update'), 'method' => 'post',]) ?>
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="color: #fff!important; opacity: 1;"
                        aria-hidden="true">
                    ×
                </button>
                <h4 class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body clearfix">
                <input type="hidden" name="update_prferences_id" id="update_prferences_id" value=""/>
                <input type="hidden" name="preferencenewvalue_one" id="preferencenewvalue_one" value=""/>
                <input type="hidden" name="preferencenewvalue_two" id="preferencenewvalue_two" value=""/>
                <input type="hidden" name="preferencenewvalue_three" id="preferencenewvalue_three" value=""/>
                <input type="hidden" name="preferencenewvalue_fourth" id="preferencenewvalue_fourth" value=""/>
                <input type="hidden" name="preferencenewvalue_fifth" id="preferencenewvalue_fifth" value=""/>
                <input type="hidden" name="preferencenewvalue_sixth" id="preferencenewvalue_sixth" value=""/>
                <input type="hidden" name="preferencenewvalue_seventh" id="preferencenewvalue_seventh" value=""/>

                <input type="hidden" name="preferencenewvalue_eigth" id="preferencenewvalue_eigth" value=""/>
                <input type="hidden" name="preferencenewvalue_nine" id="preferencenewvalue_nine" value=""/>
                <input type="hidden" name="preferencenewvalue_ten" id="preferencenewvalue_ten" value=""/>

                <div class="col-sm-12" style="margin-top: 20px;">
                    <label>
                        Are you sure you want to make this change?
                    </label>
                </div>
            </div>
            <div class="modal-footer clearfix" style="border-top: none; margin-top: 5px;">
                <div class="col-sm-12">
                    <input class="btn btn-success" type="submit" value="Save">
                    <button type="button" class="btn btn-Clear" data-dismiss="modal">
                        Close
                    </button>

                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>