<?php

namespace app\controllers;

use yii\web\Controller;

class UsersController extends Controller {

    public function actionIndex() {
        $this->layout = 'dashboard_layout';
        return $this->render('index');
    }
    public function actionAdd() {
        $this->layout = 'dashboard_layout';
        return $this->render('add');
    }

}
