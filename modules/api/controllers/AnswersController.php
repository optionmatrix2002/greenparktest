<?php

namespace app\modules\api\controllers;

use app\models\Preferences;
use app\models\Tickets;
use yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use app\models\Audits;
use app\models\AuditsSchedules;
use app\models\User;
use yii\web\HttpException;
use app\models\Answers;
use yii\helpers\Json;
use yii\web\UploadedFile;

class AnswersController extends ActiveController
{

    public $modelClass = 'app\models\Answers';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticate'] = [
            'class' => HttpBearerAuth::className()
        ];
        return $behaviors;
    }

    /**
     * Action to save the answers in the answers table
     *
     * @return string
     */
    public function actionSave()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $result = [];

            $request = Yii::$app->request;
            $params = $request->getBodyParams();

            if (!empty($params['input_answer']) && (!empty($input_answer = json_decode($params['input_answer'], true)))) {

                $answersModel = new Answers();

                if ($answersModel->saveAnswer($input_answer)) {

                    $transaction->commit();
                    $result = [
                        '200' => 'Success',
                        'response' => 'Success',
                        'message' => 'Answer saved successfully'
                    ];
                    return $result;
                } else {

                    $transaction->rollBack();
                    throw new HttpException(422, 'Error saving the answer');
                }
            } else {
                throw new HttpException(422, 'Input not received');
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw new HttpException(422, Json::encode($ex->getMessage()));
            // throw new HttpException(422, $ex->getMessage());
        }
    }

    /**
     * Action to change the status of the Audit after checking all the answers are valid
     * Will raise tickets, if there are any
     * @return string
     * @throws HttpException
     */

    public function actionChangeToComplete()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $result = [];

            $request = Yii::$app->request;
            $params = $request->getBodyParams();

            if (!empty($params['input_answer']) && (!empty($input_answer = json_decode($params['input_answer'], true)))) {

                $answersModel = new Answers();
                ini_set('memory_limit','1024M');
                ini_set('max_execution_time','180');
                if ($answersModel->changeToComplete($input_answer)) {

                    $auditScheduled = AuditsSchedules::find()
                        ->joinWith(['audit.checklist', 'audit.hotel', 'audit.department'])
                        ->andWhere(['audit_schedule_id' => $input_answer['audit_id']])
                        ->asArray()
                        ->one();
                    $user = User::findOne($auditScheduled['auditor_id']);
                    $name = $user->first_name . ' ' . $user->last_name;


                    $data = [];
                    $data['module'] = 'audit';
                    $data['type'] = 'update';
                    $data['message'] = "Audit - <b>" . $auditScheduled['audit_schedule_name'] . '</b> is submitted by ' . $name;
                    Yii::$app->events->createEvent($data);

                    $transaction->commit();
                    ini_set('memory_limit','128M');
                    ini_set('max_execution_time','30');
                    $result = [
                        '200' => 'Success',
                        'response' => 'Success',
                        'message' => 'Audit saved as completed'
                    ];
                    return $result;
                } else {
                    $transaction->rollBack();
                    throw new HttpException(422, 'Error saving the answer');
                }
            } else {
                throw new HttpException(422, 'Input not received');
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw new HttpException(422, Json::encode($ex->getMessage()));
            // throw new HttpException(422, $ex->getMessage());
        }
    }

    /**
     * @param $auditId
     */
    public function getAllTicketsAndTriggerNotifications($auditId)
    {
        $tickets = Tickets::find()->joinWith(['assignedUser', 'department', 'hotel'])->where(['audit_schedule_id' => $auditId, 'status' => 1])->asArray()->all();
        foreach ($tickets as $ticket) {
            Tickets::sendNotification($ticket, 'ticketAssigned');
        }
    }
}
