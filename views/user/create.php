<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Create User';
$this->params['breadcrumbs'][] = [
    'label' => 'Users',
    'url' => [
        'index'
    ]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">
    <?=$this->render('_form', ['model' => $model,'userLocationsModel' => $userLocationsModel,'userHotelsModel' => $userHotelsModel,'userDepartmentsModel' => $userDepartmentsModel,'hotelsList' => $hotelsList,'departmentList' => $departmentList]) ?>
</div>
