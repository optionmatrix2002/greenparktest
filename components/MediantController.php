<?php

namespace app\components;

use Yii;
use yii\web\Controller;
use app\models\ChangePasswordForm;
use yii\web\View;

/**
 * MediantController
 */
class MediantController extends Controller
{
	public function init(){
	
	  Yii::$app->view->params['changePasswordForm'] = new ChangePasswordForm();
	
	}	
}
