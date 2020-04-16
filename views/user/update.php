<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Update User';
$this->params['breadcrumbs'][] = [
    'label' => 'Users',
    'url' => [
        'index'
    ]
];
$this->params['breadcrumbs'][] = [
    'label' => $model->user_id,
    'url' => [
        'view',
        'id' => $model->user_id
    ]
];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-update">
    <?=$this->render('_form', ['model' => $model,'userLocationsModel' => $userLocationsModel,'userHotelsModel' => $userHotelsModel,'userDepartmentsModel' => $userDepartmentsModel ,'hotelsList' => $hotelsList,'departmentList' => $departmentList,'UserhotelAndDepartment' => $UserhotelAndDepartment,])?>
</div>
