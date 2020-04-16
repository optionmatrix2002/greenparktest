<?php

namespace app\components;

use Yii;

/**
 * Author: Naveen Rayuni
 */

/**
 * Component to send push notifications and messages to devices
 *
 * Using FCM for push notification. ref: https://stackoverflow.com/questions/37371990/how-can-i-send-a-firebase-cloud-messaging-notification-without-use-the-firebase
 */
class MessagingComponent extends \yii\base\Component
{

    /**
     * @param $mobileNumber
     * @param $message
     * @return bool
     */
    public static function sendTextMessage($mobileNumber, $message)
    {
        $url = yii::$app->params['send_text_message_url'];

        $url = str_replace("mbl_num", $mobileNumber, $url);
        $url = str_replace("msg_url", urlencode($message), $url);

        //$response = file_get_contents($url);


        $curlIMG = curl_init();
        curl_setopt($curlIMG, CURLOPT_URL, $url);
        curl_setopt($curlIMG, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlIMG, CURLOPT_HEADER, false);
        $imgBinary = curl_exec($curlIMG);
        curl_close($curlIMG);

        if (strpos($imgBinary, 'Status:Delivered') !== false) {
            return true;
        } else {
            return $imgBinary;
        }
    }

    /**
     * @param $registrationIds
     * @param string $type Ticket, Audit Assign, Audit Completed
     * @param $title
     * @param $message
     * @return bool
     */
    public static function createPushMessage($user, $type, $title, $message, $userType = 2, $notificationName = '')
    {

        // prep the bundle
        //if (Yii::$app->user->identity) {
        $registrationIds = isset($user['device_token']) ? $user['device_token'] : '';
	$userType = $userType ? $userType : 2;
        $notification = array(
            'body' => $title,
            'type' => $type,
            'user_type' => $userType,
            'title' => $notificationName,
            'user_id' => isset($user['user_id']) ? $user['user_id'] : '',
            'icon' => 'myicon',/*Default Icon*/
            'sound' => 'mySound' /* Default sound */
        );
        $data = array(
            'type' => $type,
            'message' => $message,
            'title' => $title,
            'user_type' => $userType,
            'date' => date('Y-m-d H:i:s'),
            'user_id' => isset($user['user_id']) ? $user['user_id'] : '',
        );
        $fields = array(
            'to' => $registrationIds,
            'notification' => $notification,
            'data' => $data
        );

        $pushMessage = json_encode($fields);

        return MessagingComponent::sendNotification($pushMessage);
        //}

    }

    /**
     * @param $message
     * @return bool
     */
    public static function sendNotification($message)
    {
        $api_key = yii::$app->params['message_notification_api_key'];

        $url = yii::$app->params['message_send_url'];

        $headers = array(
            'Authorization: key=' . $api_key,
            'Content-Type: application/json'
        );

        // Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        $result = curl_exec($ch);
        curl_close($ch);
        // Echo Result Of FireBase Server
        $result = json_decode($result, 1);

        if (json_last_error() === JSON_ERROR_NONE) {
            // JSON is valid
            if ($result['success'] == 1) {
                return true;
            }
        }
        return json_encode($result);
    }
}
