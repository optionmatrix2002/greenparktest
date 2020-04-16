<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Hotels;
use app\models\Locations;
use app\models\Departments;
use \app\models\Roles;
use \app\models\UserTypes;
use app\models\UserInfo;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\Url;

$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuSystemAdmin").addClass("active");
$("#settings-users").addClass("active");
', \yii\web\View::POS_END);
$this->title = 'User Details';
?>
<div class="container-fluid">
    <h2>User Details</h2>
</div>
<div class="wa-notification wa-notification-alt">
	<span class="wa-iconBoxed"> <span class="fa fa-file-text-o header-icon-fontcolor"></span>
	</span>
    <p id="description-text">The user details can be viewed here.</p>
</div>
<div class="col-md-12">
    <a href="<?= yii::$app->urlManager->createUrl('user'); ?>"
       class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
</div>
<?php

?>
<div class="row" style="margin-top: 10px;">
    <div class="user-form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label class="required-label">First Name :</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">

                    <?= $form->field($model, 'first_name')->textInput(['readonly' => true, 'maxlength' => true])->label(false) ?>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label class="required-label">Last Name :</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'last_name')->textInput(['readonly' => true, 'maxlength' => true])->label(false) ?>
                </div>
            </div>
        </div>
        <div class="ccol-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label class="required-label">Email Address:</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'email')->textInput(['readonly' => true, 'maxlength' => true])->label(false) ?>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label class="required-label">Phone Number :</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'phone')->textInput(['readonly' => true, 'maxlength' => true])->label(false) ?>
                </div>
            </div>
        </div>


        <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label class="required-label">Location :</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($userLocationsModel, 'location_id')->widget(Select2::classname(), ['data' => ArrayHelper::map(\app\models\Locations::find()->all(), 'location_id', 'locationCity.name'), 'language' => 'de', 'options' => ['multiple' => true, 'placeholder' => 'Select Location', 'disabled' => true], 'pluginOptions' => ['allowClear' => true]])->label(false); ?>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label class="required-label">Hotel :</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">

                    <?php
                    echo Html::hiddenInput('selectedHotel', json_encode($userHotelsModel->hotel_id), [
                        'id' => 'selectedHotel'
                    ]);

                    echo $form->field($userHotelsModel, 'hotel_id')
                        ->widget(DepDrop::classname(), [
                            'pluginOptions' => [
                                'initialize' => ($model->isNewRecord) ? false : true,
                                'depends' => [
                                    'userlocations-location_id'
                                ],
                                'placeholder' => 'Select Hotel',
                                'url' => Url::to([
                                    'user/hotel'
                                ]),
                                'params' => [
                                    'selectedHotel'
                                ]
                            ],
                            'select2Options' => [
                                'pluginOptions' => [
                                    'allowClear' => true
                                ]
                            ],
                            'options' => [
                                'multiple' => true,
                                'disabled' => true
                            ],
                            'type' => DepDrop::TYPE_SELECT2
                        ])
                        ->label(false);

                    ?>
                </div>
            </div>
        </div>


        <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label class="required-label">Department :</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?php
                    echo Html::hiddenInput('selectedDepartment', json_encode($userDepartmentsModel->hotel_department_id), [
                        'id' => 'selectedDepartment'
                    ]);
                    echo $form->field($userDepartmentsModel, 'hotel_department_id')
                        ->widget(DepDrop::classname(), [
                            'pluginOptions' => [
                                'initialize' => ($model->isNewRecord) ? false : true,
                                'depends' => [
                                    'userhotels-hotel_id'
                                ],
                                'placeholder' => 'Select Department',
                                'url' => Url::to([
                                    'user/department'
                                ]),
                                'params' => [
                                    'selectedDepartment'
                                ]
                            ],
                            'select2Options' => [
                                'pluginOptions' => [
                                    'allowClear' => true
                                ]
                            ],
                            'options' => [
                                'multiple' => true,
                                'disabled' => true
                            ],
                            'type' => DepDrop::TYPE_SELECT2
                        ])
                        ->label(false);

                    ?>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label class="required-label">Role:</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'role_id')->dropDownList(ArrayHelper::map(\app\models\Roles::find()->asArray()->all(), 'role_id', 'role_name'), ['prompt' => 'Select Role', 'disabled' => true], ['class' => 'form-control'])->label(false); ?>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label class="required-label">User Type:</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'user_type')->radioList(ArrayHelper::map(UserTypes::find()->where(['!=', 'user_type_id', 1])->all(), 'user_type_id', 'ut_name'), ['itemOptions' => ['disabled' => $model->isNewRecord ? false : true]])->label(false); ?>

                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label>Department Head :</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="col-lg-1 col-md-1 col-sm-1">
                    <!-- <input type="checkbox" class="input-group  textbox-padding" style="margin-left: -5px;
      margin-top: 17px;"> -->

                    <?= $form->field($userDepartmentsModel, 'is_hod')->checkbox(array('disabled' => 'disabled', 'label' => '', 'labelOptions' => array('style' => 'margin-left: -15px;
    margin-top: 3px;')))->label(false); ?>


                </div>
                <div class="col-lg-8 col-md-8 col-sm-8 dep-names-top">
                    <?php
                    if ($userDepartmentsModel['is_hod'] == 1) {
                        echo $UserhotelAndDepartment;
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label class="required-label">Status :</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'is_active')->radioList(['1' => 'Active', '0' => 'Inactive'], ['item' => function ($index, $label, $name, $checked, $value) {
                        $checked = ($checked) ? 'checked' : '';
                        $return = '<div class="col-sm-6 radio-button-padding"  id="auditspan">';
                        $return .= '<label class="ExternalAudit disabled">';
                        $return .= '<input type="radio" name="' . $name . '" value="' . $value . '"  ' . $checked . ' tabindex="3">';
                        $return .= '<i></i>';
                        $return .= '&nbsp;&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                        $return .= '</label>';
                        $return .= '</div>';
                        return $return;
                    }])->label(false); ?>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label></label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="col-sm-6 input-group">
                    <?= Html::a('Back', ['/user'], ['class' => 'btn btn-default']); ?>
                </div>
            </div>
        </div>

        <div class="col-sm-12"></div>
        <?php ActiveForm::end(); ?>
    </div>
</div>