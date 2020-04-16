<?php

namespace app\components;

use yii\base\Component;
use yii;
use app\models\Hotels;
use yii\helpers\Json;
use app\models\Departments;
use app\models\HotelDepartments;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class UtilsComponent extends Component
{

    public function convertToSpacedString($camelCasedString)
    { // Input Ex: actionMethodName
        return ucwords(implode(' ', preg_split('/(?=[A-Z])/', $camelCasedString))); // Output Ex: Action Method Name - convert to string by applying spaces and make first letter of each word capital
    }

    /*
     * Encrypting
     */

    public function encryptData($data)
    {
        $encrypt_method = "AES-256-CBC";
        // hash
        $key = hash('sha256', yii::$app->params['unique_hashing_secret_key']);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', yii::$app->params['unique_hashing_secret_iv']), 0, 16);
        $crypt = openssl_encrypt($data, $encrypt_method, $key, 0, $iv);
        return base64_encode($crypt);
    }

    /*
     * Decrypting
     */

    public function decryptData($encryptedData)
    {
        $encrypt_method = "AES-256-CBC";
        // hash
        $key = hash('sha256', yii::$app->params['unique_hashing_secret_key']);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', yii::$app->params['unique_hashing_secret_iv']), 0, 16);
        // decrypt the given text/string/number
        $decryptedString = openssl_decrypt(base64_decode($encryptedData), $encrypt_method, $key, 0, $iv);
        return $decryptedString;
    }

    /**
     * Exclusive function for setup page for encryption
     *
     * @param type $id
     *            is the Primary key for the concerned record for setup
     * @param type $type
     *            String can be root,hotel,location,department ,section ,subsection
     * @return type encrypted string
     */
    public function encryptSetUp($id, $type)
    {
        return $this->encryptData($type . "," . $id);
    }

    /**
     * Exclusive function for setup page for decryption
     *
     * @param
     *            String type $id is the Primary key for the concerned record for setup
     * @return int type decrypted record ID
     */
    public function decryptSetUp($encryptedString)
    {
        $output = explode(",", $this->decryptData($encryptedString));
        return end($output);
    }

    // action to get the Hotel name from the dropdown
    public function selectHotel()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $location_id = $parents[0];
                header('Content-type: application/json');
                $out = Hotels::find()->where([
                    'location_id' => $location_id
                ])
                    ->select([
                        'id' => 'hotel_id',
                        'name' => 'hotel_name'
                    ])
                    ->asArray()
                    ->all();

                echo Json::encode([
                    'output' => $out,
                    'selected' => ''
                ]);
                return;
            }
        }
        echo Json::encode([
            'output' => '',
            'selected' => ''
        ]);
    }

    // action to get the Department name from the dropdown
    public function selectDepartment()
    {
        $out = [];
        $postData = Yii::$app->request->post();
        if (isset($postData['depdrop_parents'])) {
            $parents = $postData['depdrop_parents'];
            $selectedData = @json_decode($postData['depdrop_all_params']['selectedDepartment']);
            if ($parents != null) {
                $hotels_arr = $parents[0];

                if (!empty($hotels_arr)) {
                    foreach ($hotels_arr as $hotel_id) {
                        $hotel_name = Hotels::findOne(['hotel_id' => $hotel_id, 'is_deleted' => 0])->hotel_name;
                        $out[$hotel_name] = self::getDeparmentsFromHotels($hotel_id);
                    }
                }

                header('Content-type: application/json');

                echo Json::encode([
                    'output' => $out,
                    'selected' => $selectedData
                ]);
                return;
            }
        }
        echo Json::encode([
            'output' => '',
            'selected' => ''
        ]);
    }

    /**
     */
    public function getDeparmentsFromHotels($hotel_id)
    {
        $deparments = HotelDepartments::find()->joinWith([
            'department' => function ($query) {
                $query->select([
                    'department_name',
                    'department_id'
                ]);
            },
            'hotel'
        ])
            ->where([
                HotelDepartments::tableName() . '.hotel_id' => $hotel_id,
                HotelDepartments::tableName() . '.is_deleted' => 0
            ])
            ->select([
                HotelDepartments::tableName() . '.id',
                HotelDepartments::tableName() . '.department_id',
                HotelDepartments::tableName() . '.hotel_id'
            ])
            ->asArray()
            ->all();

        $resultArray = [];

        if (!empty($deparments)) {
            foreach ($deparments as $department) {
                $list = [];
                if (isset($department['department'])) {
                    $list['id'] = $department['id'];
                    $list['name'] = $department['hotel']['hotel_name'] . '-' . $department['department']['department_name'];
                    $resultArray[] = $list;
                }
            }
        } else {
            $list['id'] = '';
            $list['name'] = 'No Departments';
            $resultArray[] = $list;
        }
        return $resultArray;
    }

    // action to get the Department name from the dropdown
    public function selectHodDepartment()
    {
        $out = [];
        $post = yii::$app->request->post();
        if (isset($post['depdrop_parents'])) {
            $parents = $post['depdrop_parents'];
            $selectedData = json_decode($post['depdrop_all_params']['selected_hod_department']);
            if ($parents != null) {
                $hotelDepartments = $parents[0];
                header('Content-type: application/json');
                $out = HotelDepartments::find()->joinWith(["department", "hotel"])->where(['IN',
                    'tbl_gp_hotel_departments.id', $hotelDepartments
                ])
                    ->asArray()
                    ->all();
                $outPut = [];
                foreach ($out as $department) {
                    $list = [];
                    if (isset($department['department'])) {
                        $list['id'] = $department['id'];
                        $list['name'] = $department['hotel']['hotel_name'] . '-' . $department['department']['department_name'];
                        $outPut[] = $list;
                    }
                }
                echo Json::encode([
                    'output' => $outPut,
                    'selected' => $selectedData
                ]);
                return;
            }
        }
        echo Json::encode([
            'output' => '',
            'selected' => ''
        ]);
    }

}
