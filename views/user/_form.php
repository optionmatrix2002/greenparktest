<?php

use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use app\models\Hotels;
use app\models\Locations;
use app\models\Departments;
use \app\models\Roles;
use \app\models\UserTypes;
use app\models\UserInfo;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\Url;

AppAsset::register($this);
$this->registerJsFile(yii::$app->urlManager->createUrl('js/user.js?version=' . time()), [
    'depends' => JqueryAsset::className()
]);
$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuSystemAdmin").addClass("active");
$("#settings-users").addClass("active");
', \yii\web\View::POS_END);
?>
<div class="container-fluid">
    <h2><?= $this->title; ?></h2>
</div>
<div class="wa-notification wa-notification-alt">
	<span class="wa-iconBoxed"> <span class="fa fa-file-text-o header-icon-fontcolor"></span>
	</span>
    <p id="description-text">User details and assignments can be managed
        from here.</p>
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

                    <?= $form->field($model, 'first_name')->textInput(['class' => 'form-control onlyChars', 'maxlength' => 30])->label(false) ?>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label class="required-label">Last Name :</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'last_name')->textInput(['class' => 'form-control onlyChars', 'maxlength' => 30])->label(false) ?>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label class="required-label">Email Address:</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'disabled' => $model->isNewRecord ? false : true])->label(false) ?>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label class="required-label">Phone Number :</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'phone')->widget(MaskedInput::className(), ['mask' => $this->context->phoneNumbermask, 'clientOptions' => ['removeMaskOnSubmit' => true]])->label(false) ?>
                </div>
            </div>
        </div>


        <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label class="required-label">Location :</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($userLocationsModel, 'location_id')
                        ->widget(Select2::classname(), ['data' => ArrayHelper::map(\app\models\Locations::find()
                            ->where(['is_deleted' => 0])->all(), 'location_id', 'locationCity.name'), 'showToggleAll' => false, 'language' => 'en', 'options' => ['multiple' => true, 'placeholder' => 'Select Location'], 'pluginOptions' => ['showToggleAll' => false, 'allowClear' => true]])
                        ->label(false); ?>
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

                    //'initialize' => ($model->isNewRecord) ? false : true,


                    echo Html::hiddenInput('selectedHotel', json_encode($userHotelsModel->hotel_id), [
                        'id' => 'selectedHotel'
                    ]);

                    echo $form->field($userHotelsModel, 'hotel_id')
                        ->widget(DepDrop::classname(), [
                            'pluginOptions' => [
                                'initialize' => ($userHotelsModel->hotel_id) ? true : false,
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
                                    'allowClear' => false
                                ],
                                'showToggleAll' => false
                            ],

                            'pluginEvents' => [],
                            'options' => [
                                'multiple' => true
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
                                'initialize' => ($userDepartmentsModel->hotel_department_id) ? true : false,
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
                                    'allowClear' => false
                                ],
                                'showToggleAll' => false
                            ],
                            'options' => [
                                'multiple' => true
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
                    <?= $form->field($model, 'role_id')->dropDownList(ArrayHelper::map(\app\models\Roles::find()->where(['is_deleted' => 0])->asArray()->all(), 'role_id', 'role_name'), ['prompt' => 'Select Role'], ['class' => 'form-control'])->label(false); ?>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 margintop10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label class="required-label">User Type:</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6 radio-button-padding">
                    <?php
                    $userType = $model->user_type == 1 ? 0 : 1;
                    ?>
                    <?= $form->field($model, 'user_type')->radioList(ArrayHelper::map(UserTypes::find()->select(['user_type_id', 'CONCAT(UCASE(LEFT(ut_name, 1)), 
                             SUBSTRING(ut_name, 2)) as ut_name'])->where(['!=', 'user_type_id', $userType])->all(), 'user_type_id', 'ut_name'), ['itemOptions' => ['disabled' => $model->isNewRecord ? false : true]])->label(false); ?>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <label>Department Head for:</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?php
                    echo Html::hiddenInput('selected_hod_department', json_encode($userDepartmentsModel->hodDepartmentList), [
                        'id' => 'selected_hod_department'
                    ]);
                    echo $form->field($userDepartmentsModel, 'hodDepartmentList')
                        ->widget(DepDrop::classname(), [
                            'pluginOptions' => [
                                'initialize' => ($userDepartmentsModel->hodDepartmentList) ? true : false,
                                'depends' => [
                                    'userdepartments-hotel_department_id'
                                ],
                                'placeholder' => 'Select Departments',
                                'url' => Url::to([
                                    'user/hod-departments'
                                ]),
                                'params' => [
                                    'selected_hod_department'
                                ]
                            ],
                            'select2Options' => [
                                'pluginOptions' => [
                                    'allowClear' => false
                                ],
                                'showToggleAll' => false
                            ],
                            'options' => [
                                'multiple' => true
                            ],
                            'type' => DepDrop::TYPE_SELECT2
                        ])
                        ->label(false);

                    ?>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="col-lg-3 col-md-3 col-sm-3 marginTB10">
                <label class="required-label">Status :</label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="input-group col-sm-6">
                    <?= $form->field($model, 'is_active')->radioList(['1' => 'Active', '0' => 'Inactive'], ['item' => function ($index, $label, $name, $checked, $value) {
                        $checked = ($checked) ? 'checked' : '';
                        $return = '<div class="col-sm-6 radio-button-padding"  id="auditspan">';
                        $return .= '<label class="ExternalAudit">';
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
                    <?= Html::submitButton($model->isNewRecord ? 'Save' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
                    <?= Html::a('Cancel', ['/user'], ['class' => 'btn btn-default']); ?>
                </div>
            </div>
        </div>

        <div class="col-sm-12"></div>
        <?php ActiveForm::end(); ?>
    </div>
</div>