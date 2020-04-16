<?php

namespace app\modules\api\controllers;

use yii;
use yii\web\Controller;
use yii\rest\ActiveController;
use app\models;
use app\components\EmailsComponent;
use app\models\User;
use app\models\LoginForm;
use app\models\UserInfo;
use app\models\UserTypes;

/**
 * Default controller for the `api` module
 */
class UserController extends ActiveController
{

    public $modelClass = 'app\models\User';

    /**
     * Renders the index view for the module
     *
     * @return string
     */
    public function actionLogin()
    {
        $output = [];
        $model = new LoginForm();
        $email = $model->username = Yii::$app->request->post('email');
        $password_hash = $model->password = Yii::$app->request->post('password');

        if (!empty($email) && !empty($password_hash)) {
            if ($model->login()) {
                $id = \Yii::$app->user->identity->id;
                $usermodel = User::findOne($id);
                $authKey = $usermodel->generateKey();
                $usermodel->auth_token = $authKey;

                if (!empty(Yii::$app->request->post('device_token'))) {
                    $usermodel->device_token = Yii::$app->request->post('device_token');
                }

                if ($usermodel->save()) {
                  
                    if ($usermodel->image) {
                        $image = "/imageuploads/" . $usermodel->image;
                    } else {
                        $image = null;
                    }
                    $output = [
                        'response' => 'success',
                        'message' => 'Successfully loggedin',
                        'user_id' => $usermodel->user_id,
                        'username' => $usermodel->first_name . ' ' . $usermodel->last_name,
                        'email' => $usermodel->email,
                        'auth_token' => $usermodel->auth_token,
                        'user_type' => $usermodel->uiUserType->ut_name,
                        'user_role' => $usermodel->uiRole->role_name,
                        'image_path' => $image
                    ];
                    Yii::$app->response->statusCode = 200;
                } else {
                    $output = [
                        'response' => 'fail1',
                        'message' => 'Failed to update record'
                    ];
                    Yii::$app->response->statusCode = 400;
                }
            } else {
                $usermodel = $model->getActiveUser();
                if ($usermodel && !$usermodel->is_active) {
                    $output = [
                        'response' => 'fail',
                        'message' => 'User is inactive, please contact admin.'
                    ];
                } else {
                    $output = [
                        'response' => 'fail1',
                        'message' => 'Invalid email or password'
                    ];
                }

                Yii::$app->response->statusCode = 400;
            }
        } else {
            return $output = [
                'response' => 'fail',
                'message' => 'Email or Password empty'
            ];
            Yii::$app->response->statusCode = 204;
        }
        return $output;
    }

    public function actionForgotPassword()
    {
        $output = [];

        $email = Yii::$app->request->post('email');
        if (!empty($email)) {
            $usermodel = User::findOne([
                'email' => $email
            ]);
            if ($usermodel && $usermodel->findByUsername($email)) {

                $recipientMail = $usermodel->email;
                $usermodel->confirmation_token = $usermodel->generateKey();

                if ($usermodel) {
                    if ($usermodel->save()) {
                        $getUserId = Yii::$app->utils->encryptData($usermodel->user_id);
                        $link = '<a href="' . \Yii::$app->urlManager->createAbsoluteUrl('/site/set-password') . '?user_id=' . $getUserId . '&token=' . $usermodel->confirmation_token . '">Click Here</a>';
                        $result = EmailsComponent::sendUserVerificationLinkEmail($usermodel->first_name, $recipientMail, $link, $action = "forgot");
                        if ($result) {
                            $output = [
                                '200' => 'Email sent',
                                'response' => 'success',
                                'message' => 'Please check email to reset password'
                            ];
                        } else {
                            $output = [
                                '200' => 'Email sent',
                                'response' => 'success',
                                'message' => 'Please check email to reset password'
                            ];
                        }
                    } else {
                        $output = [
                            '200' => 'Email sent',
                            'response' => 'success',
                            'message' => 'Please check email to reset password'
                        ];
                    }
                } else {
                    $output = [
                        '200' => 'Email sent',
                        'response' => 'success',
                        'message' => 'Please check email to reset password'
                    ];
                }

                $output = [
                    '200' => 'Email sent',
                    'response' => 'success',
                    'message' => 'Please check email to reset password'
                ];
                Yii::$app->response->statusCode = 200;
            } else {
                $output = [
                    '400' => 'Email not exists',
                    'response' => 'fail',
                    'message' => 'Please check email to reset password'
                ];
                Yii::$app->response->statusCode = 400;
            }
        } else {
            $output = [
                '404' => 'Email not found',
                'response' => 'fail',
                'message' => 'Please check email to reset password'
            ];
            Yii::$app->response->statusCode = 404;
        }
        return $output;
    }
}
