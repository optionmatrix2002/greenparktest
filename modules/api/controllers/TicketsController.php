<?php

namespace app\modules\api\controllers;

use yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use app\models;
use yii\web\HttpException;
use app\models\Tickets;
use app\models\TicketAttachments;
use yii\helpers\Json;
use app\models\TicketComments;
use app\models\TicketHistory;

class TicketsController extends ActiveController
{

    public $modelClass = 'app\models\Tickets';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticate'] = [
            'class' => HttpBearerAuth::className()
        ];
        return $behaviors;
    }

    public function actionAllTickets()
    {
        try {
            $output = [];
            $assigned_user_id = Yii::$app->request->post('user_id');
            $userType = Yii::$app->user->identity->user_type;
            $statusList = $userType == '3' ? [0, 1, 4] : [2, 3];
            if ($assigned_user_id) {
                $allTickets = [];
                $results = (new yii\db\Query())->select('t.ticket_id,t.priority_type_id,t.ticket_name,t.status as ticketstatus,t.due_date,acq.q_text as question,t.subject')
                    ->from('{{%tickets}} t')
                    ->join('LEFT JOIN', "{{%audits_schedules}} as", 'as.audit_schedule_id = t.audit_schedule_id')
                    ->join('LEFT JOIN', "{{%audits_checklist_questions}} acq", 'acq.audit_id = t.audit_schedule_id')
                    ->where([
                        't.assigned_user_id' => $assigned_user_id,
                    ])
                    ->orWhere([
                        'or',
                        [
                            't.created_by' => Yii::$app->user->identity->id
                        ]
                    ])
                    ->andWhere([
                        't.status' => $statusList, 't.is_deleted' => 0
                    ])
                    ->groupBy(['t.ticket_id'])
                    ->all();

                foreach ($results as $result) {
                    $allTickets[] = [
                        'ticket_id' => $result['ticket_id'],
                        'ticket_number' => $result['ticket_name'],
                        'status' => $result['ticketstatus'],
                        'due_date' => $result['due_date'],
                        'priority' => $result['priority_type_id'],
                        'question' => ($result['question']) ? $result['question'] : '',
                        'subject' => ($result['subject']) ? $result['subject'] : ''
                    ];
                }

                $tickets = ($allTickets) ? $allTickets : [];
                $output = [
                    '200' => 'Success',
                    'response' => 'success',
                    'message' => 'Successfull',
                    'tickets' => $tickets
                ];
            } else {
                $output = [
                    '404' => 'Fail',
                    'response' => 'fail',
                    'message' => 'No post data'
                ];
            }
            return $output;
        } catch (\Exception $ex) {
            throw new HttpException(422, $ex->getMessage());
        }
    }

    public function actionTickets()
    {
        try {
            $output = [];
            $ticket_id = Yii::$app->request->post('ticket_id');
            if ($ticket_id) {
                $result = (new yii\db\Query())->select('t.ticket_id,t.ticket_name,qpt.priority_name,t.status as ticketstatus,CONCAT_WS(" ", u.`first_name`, u.`last_name`) as assignedto,t.due_date,as.audit_schedule_id,as.audit_schedule_id,cl.cl_name,as.updated_at as start_date,CONCAT_WS(" ", ua.`first_name`, ua.`last_name`) as auditor,c.name as location_name,h.hotel_name,d.department_name,cl.cl_audit_type,s.s_section_name,t.sub_section_id,ss.ss_subsection_name,as.audit_schedule_name,t.description as observations,t.subject,a.options_values,a.answer_value,que.q_response_type,que.options')
                    ->from('{{%tickets}} t')
                    ->join('LEFT JOIN', "{{%question_priority_types}} qpt", 'qpt.priority_type_id = t.priority_type_id')
                    ->join('LEFT JOIN', "{{%user}} u", 'u.user_id = t.assigned_user_id')
                    ->join('LEFT JOIN', "{{%answers}} a", 'a.answer_id = t.answer_id')
                    ->join('LEFT JOIN', "tbl_gp_audits_checklist_questions que", 'que.audits_checklist_questions_id = a.question_id')
                    ->join('LEFT JOIN', "{{%audits_schedules}} as", 'as.audit_schedule_id = t.audit_schedule_id')
                    ->join('LEFT JOIN', "{{%audits}} au", 'au.audit_id = as.audit_id')
                    ->join('LEFT JOIN', "{{%user}} ua", 'ua.user_id = as.auditor_id')
                    ->join('LEFT JOIN', "{{%checklists}} cl", 'cl.checklist_id = au.checklist_id')
                    ->join("LEFT JOIN", '{{%hotels}} h', 'h.hotel_id = t.hotel_id')
                    ->join("LEFT JOIN", '{{%locations}} l', 'l.location_id = h.location_id')
                    ->join("LEFT JOIN", '{{%cities}} c', 'c.id = l.location_city_id')
                    ->join("LEFT JOIN", '{{%departments}} d', 'd.department_id = t.department_id')
                    ->join("LEFT JOIN", '{{%sections}} s', 's.section_id = t.section_id')
                    ->join("LEFT JOIN", '{{%sub_sections}} ss', 'ss.sub_section_id = t.sub_section_id')
                    ->where([
                        't.ticket_id' => $ticket_id,
                        't.is_deleted' => 0,
                        'u.is_deleted' => 0,
                        's.is_deleted' => 0
                    ])
                    ->one();
                if ($result) {
                    $auditTypes = ['0' => 'Internal', 1 => "External"];
                    $answer = '';
                    if (isset($result['q_response_type'])) {
                        switch ($result['q_response_type']) {
                            case '1';
                                $answerArray = [0 => 'False', 1 => 'True'];
                                $answer = $answerArray[$result['answer_value']];
                                break;
                            case '2';

                                $answerArray = [0 => 'No', 1 => 'Yes'];
                                $answer = $answerArray[$result['answer_value']];
                                break;
                            case '3';
                                $ratingValue = @unserialize($result['options_values']);
                                if (is_array($ratingValue) && isset($ratingValue[0])) {
                                    $answer = $ratingValue[0];
                                } else {
                                    $answer = 0;
                                }
                                break;
                            case '4';
                            case '5';
                                $options = @unserialize($result['options']);
                                $selectedOptions = @unserialize($result['options_values']);

                                $optionsNames = array_filter($options, function ($key) use ($selectedOptions) {
                                    if (in_array($key, $selectedOptions)) {
                                        return true;
                                    }
                                }, ARRAY_FILTER_USE_KEY);
                                $optionsNames = $optionsNames ? $optionsNames : [];
                                $answer = implode(', ', $optionsNames);
                                break;
                        }
                    }


                    $ticket_result = [
                        'ticket_id' => $result['ticket_id'],
                        'ticket_number' => $result['ticket_name'],
                        'priority' => $result['priority_name'],
                        'status' => $result['ticketstatus'],
                        'assigned_to' => $result['assignedto'],
                        'due_date' => $result['due_date'],
                        'audit_id' => $result['audit_schedule_name'],
                        'checklist_name' => $result['cl_name'],
                        'audit_date' => $result['start_date'],
                        'auditor' => $result['auditor'],
                        'location_name' => $result['location_name'],
                        'hotel_name' => $result['hotel_name'],
                        'audit_type' => isset($auditTypes[$result['cl_audit_type']]) ? $auditTypes[$result['cl_audit_type']] : '',
                        'department_name' => $result['department_name'],
                        'section_name' => $result['s_section_name'],
                        'subsection_name' => $result['ss_subsection_name'] ? $result['ss_subsection_name'] : $result['sub_section_id'],
                        'observations' => strip_tags($result['observations']),
                        'subject' => $result['subject'],
                        'answer' => $answer
                    ];

                    $ticket_result['comments'] = $this->getTicketComments($ticket_id);
                    $ticket_result['attachments'] = $this->getTicketAttachments($ticket_id);
                    $ticket_result['history'] = $this->getTicketHistory($ticket_id);

                    $tickets = ($ticket_result) ? $ticket_result : [];
                    $output = [
                        '200' => 'Success',
                        'response' => 'success',
                        'message' => 'Successfull',
                        'details' => $tickets
                    ];
                } else {
                    $output = [
                        '400' => 'fail',
                        'response' => 'fail',
                        'message' => 'No tickets found'
                    ];
                }
            } else {
                $output = [
                    '400' => 'fail',
                    'response' => 'fail',
                    'message' => 'No post data'
                ];
            }

            return $output;
        } catch (\Exception $ex) {
            throw new HttpException(422, $ex->getMessage());
        }
    }

    private function getTicketAttachments($ticket_id = '')
    {

        $attachments = (new yii\db\Query())->select('ta.ticket_attachment_path,CONCAT_WS(" ", createdBy.`first_name`, createdBy.`last_name`) as createdby,CONCAT_WS(" ", u.`first_name`, u.`last_name`) as assignedto,ta.created_at')
            ->from('{{%ticket_attachments}} ta')
            ->join('LEFT JOIN', "{{%tickets}} t", 't.ticket_id = ta.ticket_id')
            ->join('LEFT JOIN', "{{%user}} u", 'u.user_id = t.assigned_user_id')
            ->join('LEFT JOIN', "{{%user}} createdBy", 'createdBy.user_id = ta.created_by')
            ->where([
                'ta.ticket_id' => $ticket_id,
                'ta.is_deleted' => 0
            ])
            ->all();
        $ticket_attachments = ($attachments) ? $attachments : [];
        $answerAttachmentsPath = [];
        if ($ticket_attachments) {

            $absoluteBaseUrl = yii\helpers\Url::base(true);
            $path = $absoluteBaseUrl . Yii::$app->params['attachments_save_url'];
            foreach ($ticket_attachments as $attachment) {
                $alias = Yii::getAlias('@webroot');
                if (file_exists($alias . Yii::$app->params['attachments_save_url'] . $attachment['ticket_attachment_path'])) {
                    $answerAttachmentsPath[] = [
                        'attachment_path' => $path . $attachment['ticket_attachment_path'],
                        'created_at' => $attachment['created_at'],
                        'created_by' => $attachment['createdby']
                    ];
                }
            }
        }
        return $answerAttachmentsPath;
    }

    private function getTicketComments($ticket_id = '')
    {
        $ticket_comments = [];
        $comments = (new yii\db\Query())->select('tc.ticket_comment,tc.ticket_comment_status,CONCAT_WS(" ", u.`first_name`, u.`last_name`) as commentedby,tc.created_at')
            ->from('{{%ticket_comments}} tc')
            ->join('LEFT JOIN', "{{%tickets}} t", 't.ticket_id = tc.ticket_id')
            ->join('LEFT JOIN', "{{%user}} u", 'u.user_id = tc.created_by')
            ->where([
                'tc.ticket_id' => $ticket_id
            ])
            ->all();
        $ticket_comments = ($comments) ? $comments : [];
        return $ticket_comments;
    }

    public function getTicketHistory($ticket_id = '')
    {

        $history = models\TicketHistory::find()
            ->select('ticket_message,created_at')->where(['ticket_id' => $ticket_id])->all();
        $ticket_history = ($history) ? $history : [];
        return $ticket_history;
    }

    public function actionUpdateTicketComments()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {

            $request = Yii::$app->request;
            $params = $request->getBodyParams();

            if (!empty($params) && !empty(json_decode($tickets = $params['tickets'], true))) {
                $tickeData = json_decode($tickets);
                $ticketsModel = Tickets::findOne(['ticket_id' => $tickeData->ticket_id]);
                if (!$ticketsModel) {
                    throw new HttpException(422, "Ticket is not exists for ticket id : " . $tickeData->ticket_id);
                }

                $oldStatus = $ticketsModel->status;
                $ticketCommentsModel = new TicketComments();
                $ticketCommentsModel->ticket_id = $tickeData->ticket_id;
                $ticketCommentsModel->ticket_comment = $tickeData->comment;
                $ticketCommentsModel->ticket_comment_status = $tickeData->status;

                $ticketsModel->status = $tickeData->status;
                if (!$ticketsModel->save()) {
                    throw new HttpException(422, Json::encode($ticketsModel->errors));
                }

                if (!$ticketCommentsModel->save()) {
                    $transaction->rollBack();
                    return [
                        'status' => false,
                        'message' => Json::encode($ticketCommentsModel->getErrors())
                    ];
                }
                $ticketAttachments = new TicketAttachments();

                if (!empty($tickeData->attachments)) {
                    foreach ($tickeData->attachments as $attachment) {
                        $ticketAttachments->saveTicketAttachments($attachment, $tickeData->ticket_id);
                    }
                }
                $ticketHistoryModel = new TicketHistory();
                $user_id = \Yii::$app->user->id;
                $getUserName = Tickets::getUserName($user_id);
                $message = "Ticket Updated By " . ucfirst($getUserName->first_name) . ' ' . $getUserName->last_name;
                $ticketHistoryModel->ticket_id = $tickeData->ticket_id;
                $ticketHistoryModel->ticket_message = $message;
                $ticketHistoryModel->ticket_history_id = null;
                $ticketHistoryModel->isNewRecord = true;
                if (!$ticketHistoryModel->save()) {
                    return [
                        'status' => false,
                        'message' => Json::encode($ticketHistoryModel->getErrors())
                    ];
                }

                if ($oldStatus != $ticketsModel->status) {
                    Tickets::sendStatusChangeNotification($ticketsModel->ticket_id);
                }


                $data = [];
                $data['module'] = 'ticket';
                $data['type'] = 'update';
                $data['message'] = "Ticket - <b>" . $ticketsModel->ticket_name . '</b> is status changed by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name;

                Yii::$app->events->createEvent($data);

                $transaction->commit();
                $output = [
                    '200' => 'success',
                    'response' => 'success',
                    'message' => 'Ticket Updated Successfully'
                ];
            } else {
                $transaction->rollBack();
                $output = [
                    '404' => 'fail',
                    'response' => 'fail',
                    'message' => 'No post data'
                ];
            }
            return $output;
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw new HttpException(422, Json::encode($ex->getMessage()));
        }
    }

    /**
     * @return array
     * @throws HttpException
     * @throws yii\db\Exception
     */
    public function actionRaiseDynamicTicket()
    {
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $result = [];

            $request = Yii::$app->request;
            $params = $request->getBodyParams();

            if (!empty($params['input_answer']) && (!empty($input_answer = json_decode($params['input_answer'], true)))) {
                $ticketsModel = new Tickets();

                if ($ticketsModel->saveTicket($input_answer)) {

                    $transaction->commit();
                    $result = [
                        '200' => 'Success',
                        'response' => 'Success',
                        'message' => 'Ticket saved successfully'
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
}
