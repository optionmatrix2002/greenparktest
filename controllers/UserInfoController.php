<?php

namespace app\controllers;

use app\components\UtilsComponent;

class UserInfoController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
    
   
    public function actionHotel()
    {
       $hotel =  new UtilsComponent();
       return $hotel->selecthotel();
    }
    
    
    public function actionDepartment()
    {
        $department = new UtilsComponent();
        return $department->selectDepartment();
    }
}
