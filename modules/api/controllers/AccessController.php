<?php

namespace app\modules\api\controllers;

use yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use app\models;
use app\models\User;
use app\models\LoginForm;
use app\models\Locations;
use app\models\UserHotels;

class AccessController extends ActiveController
{

    public $modelClass = 'app\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticate'] = [
            'class' => HttpBearerAuth::className(),
        ];
        return $behaviors;
    }

    public function actionChangePassword()
    {
        $output = [];

        $user_id = Yii::$app->request->post('user_id');
        $oldpassword = Yii::$app->request->post('oldpassword');
        $newpassword = Yii::$app->request->post('newpassword');
        if (!empty($oldpassword) && !empty($newpassword) && !empty($user_id)) {

            $usermodel = User::findOne($user_id);
            if ($usermodel->validatePassword($oldpassword)) {

                $usermodel->password_hash = Yii::$app->getSecurity()->generatePasswordHash($newpassword);
                if ($usermodel->save()) {
                    $output = [
                        '200' => 'success',
                        'response' => 'success',
                        'message' => 'Password Successfully changed'
                    ];
                    Yii::$app->response->statusCode = 200;
                } else {
                    $output = [
                        '404' => 'fail',
                        'response' => 'fail',
                        'message' => 'Failed to update password'
                    ];
                    Yii::$app->response->statusCode = 404;
                }
            } else {
                $output = [
                    '400' => 'fail',
                    'response' => 'fail',
                    'message' => "Current password is incorrect"
                ];
                Yii::$app->response->statusCode = 400;
            }

        } else {
            $output = [
                '404' => 'fail',
                'response' => 'fail',
                'message' => 'No data found'
            ];
            Yii::$app->response->statusCode = 404;
        }
        return $output;
    }

    public function actionLogout()
    {
        $output = [];
        $user_id = Yii::$app->request->post('user_id');
        if (!empty($user_id)) {
            $usermodel = User::findOne($user_id);

            $usermodel->auth_token = "";
            if ($usermodel->save()) {
                $output = [
                    '200' => 'LogOut',
                    'response' => 'success',
                    'message' => 'User Logged out'
                ];
            } else {
                $output = [
                    '404' => 'LogOut error',
                    'response' => 'fail',
                    'message' => 'Error in setting data in db'
                ];
                Yii::$app->response->statusCode = 404;
            }
        } else {
            $output = [
                '404' => 'LogOut error',
                'response' => 'fail',
                'message' => 'No data found'
            ];
            Yii::$app->response->statusCode = 404;
        }
        return $output;
    }

    public function actionProfile()
    {
        $output = [];
        $user_id = Yii::$app->request->post('user_id');
        $usermodel = User::findOne($user_id);
        if ($usermodel) {


            $locations = [];
            foreach ($usermodel->userLocations as $location) {
                $locations[] = $location->location->locationCity->name;
            }
            $locationsList = implode(',', $locations);

            $hotelsList = [];

            foreach ($usermodel->userHotels as $hotels) {
                $hotelsList[] = $hotels->hotel->hotel_name;
            }
            $hotelsList = implode(', ', $hotelsList);

            $departments = [];

            foreach ($usermodel->userDepartments as $userDepartments) {
                if ($userDepartments->department) {
                    $departments[] = $userDepartments->department->department->department_name;
                }
            }
            $departmentsList = implode(', ', $departments);

            if ($usermodel->image) {
                $image = "/imageuploads/" . $usermodel->image;

            } else {
                $image = null;
            }

            $output = [
                'response' => 'success',
                'message' => 'Successfull',
                'image_path' => $image,
                'user_id' => $usermodel->user_id,
                'username' => $usermodel->first_name . ' ' . $usermodel->last_name,
                'email' => $usermodel->email,
                'mobile_number' => $usermodel->phone,
                'user_type' => $usermodel->uiUserType->ut_name,
                'user_role' => $usermodel->uiRole->role_name,
                'location' => $locationsList,
                'hotel' => $hotelsList,
                'department' => $departmentsList,
            ];
            Yii::$app->response->statusCode = 200;

        } else {
            $output = [
                '404' => 'Profile error',
                'response' => 'fail',
                'message' => 'No data found'
            ];
            Yii::$app->response->statusCode = 404;
        }
        return $output;
    }

    public function decodeBase64Image($base64_string, $path, $user_id)
    {

        $output = [];
        $usermodel = User::findOne($user_id);
        if (!empty($usermodel->image)) {
            $newFile = $usermodel->image;
        } else {
            $newFile = $usermodel->email . '-' . $usermodel->user_id . '.png';
        }
        $usermodel->image = $base64_string ? $newFile : '';
        if ($usermodel->save()) {
            if ($base64_string) {
                $output_file = $path . $newFile;
                $ifp = fopen($output_file, 'w');
                chmod($output_file, 0777);
                $data = explode(',', $base64_string);
                if (fwrite($ifp, base64_decode($data[1]))) {
                    return true;
                } else {
                    return false;
                }
                fclose($ifp);
            }

            return true;
        } else {
            return false;
        }

    }


    public function actionUploadImage()
    {
        $output = [];
        $image = Yii::$app->request->post('image');
        $user_id = Yii::$app->request->post('user_id');
        $path = Yii::getAlias('@webroot/') . "imageuploads/";
        if (!file_exists($path)) {
            $folder = mkdir("imageuploads", 0777);
        }
        $result = $this->decodeBase64Image($image, $path, $user_id);
        $message = $image ? 'Image uploaded Successfully' : 'Image removed Successfully';
        if ($result) {
            $output = [
                '200' => $message,
                'response' => 'success',
                'message' => $message
            ];
        } else {
            $output = [
                '400' => 'Failed to Upload Image',
                'response' => 'fail',
                'message' => 'Error in Image Upload'
            ];
        }
        return $output;
    }

}
