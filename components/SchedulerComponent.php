<?php

namespace app\components;

use app\models\Alertmaster;
use app\models\Audits;
use app\models\AuditsSchedules;
use app\models\Preferences;
use app\models\Tickets;
use app\models\User;
use app\models\UserDepartments;
use app\models\UserHotels;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\Answers;
use app\models\Departments;

class SchedulerComponent extends Component
{

    const AUDIT_ASSIGN = 'Audit $_AUDIT_ID is scheduled. <br>Hotel : $_HOTEL <br> Department : $_DEPARTMENT <br> Checklist : $_CHECKLIST <br> Due Date: $_DUE_DATE.';
    const AUDIT_ASSIGN_MESSAGE = 'Hi $_FULL_NAME,
Audit $_AUDIT_ID is scheduled  .
Hotel : $_HOTEL 
Department : $_DEPARTMENT 
Checklist : $_CHECKLIST 
Due Date: $_DUE_DATE.

Best Regards, 
Green Park Corporate Audit Team.';

    const AUDIT_ASSIGN_NOTIFICATION = 'Hi $_FULL_NAME &Audit $_AUDIT_ID is scheduled. &Hotel : $_HOTEL &Department : $_DEPARTMENT &Checklist : $_CHECKLIST &Due Date: $_DUE_DATE.';


    const AUDIT_REMINDER = 'Reminder: Audit $_AUDIT_ID is pending for submission by $_DUE_DATE. <br>Hotel : $_HOTEL <br> Department : $_DEPARTMENT <br> Checklist : $_CHECKLIST';

    const AUDIT_REMINDER_MESSAGE = 'Hi $_FULL_NAME

Reminder: Audit $_AUDIT_ID is pending for submission by $_DUE_DATE.

Hotel : $_HOTEL 
Department : $_DEPARTMENT 
Checklist : $_CHECKLIST 

Best Regards, 
Green Park Corporate Audit Team.';


    const AUDIT_OVER_DUE = 'Overdue Reminder: Audit $_AUDIT_ID has breached due date $_DUE_DATE. <br>Hotel : $_HOTEL <br> Department : $_DEPARTMENT <br> Checklist : $_CHECKLIST';

    const AUDIT_OVER_DUE_MESSAGE = 'Hi $_FULL_NAME

Overdue Reminder: Audit $_AUDIT_ID has breached due date $_DUE_DATE.

Hotel : $_HOTEL 
Department : $_DEPARTMENT 
Checklist : $_CHECKLIST 

Best Regards, 
Green Park Corporate Audit Team.';


    const LOW_SCORE_AUDIT = 'Low Score Alert:<br> Audit : $_AUDIT_ID <br>Hotel : $_HOTEL <br> Department : $_DEPARTMENT<br>  Checklist : $_CHECKLIST  <br> has scored $_SCORE_PERCENTAGE.';
    const LOW_SCORE_AUDIT_NOTIFICATION = 'Low Score Alert: &Audit $_AUDIT_ID &Hotel : $_HOTEL &Department : $_DEPARTMENT Department &Checklist : $_CHECKLIST has scored $_SCORE_PERCENTAGE.';

    const LOW_SCORE_AUDIT_MESSAGE = 'Hi $_FULL_NAME

Low Score Alert:
Audit :  $_AUDIT_ID
Hotel : $_HOTEL
Department $_DEPARTMENT 
Checklist $_CHECKLIST  has scored $_SCORE_PERCENTAGE.

Best Regards,
Green Park Corporate Audit Team.';


    const AUDIT_SUBMITTED = 'Audit $_AUDIT_ID has been submitted. <br>Hotel : $_HOTEL <br> Department : $_DEPARTMENT <br> Checklist : $_CHECKLIST <br>';
    const AUDIT_SUBMITTED_NOTIFICATION = 'Audit $_AUDIT_ID has been submitted. &Hotel : $_HOTEL &Department : $_DEPARTMENT  &Checklist : $_CHECKLIST .';

    const AUDIT_SUBMITTED_MESSAGE = 'Hi $_FULL_NAME

Audit $_AUDIT_ID has been submitted. 
Hotel : $_HOTEL
Department :  $_DEPARTMENT
Checklist : $_CHECKLIST 

Best Regards, 
Green Park Corporate Audit Team.';

    const TICKET_ASSIGN = 'Ticket $_TICKET_ID is assigned to you. <br>Hotel : $_HOTEL<br>  Department : $_DEPARTMENT <br>Subject: $_QUESTION<br> Due Date: $_DUE_DATE.';
    const TICKET_ASSIGN_MESSAGE = 'Hi $_FULL_NAME

Ticket $_TICKET_ID is assigned to you .
Hotel : $_HOTEL
Department : $_DEPARTMENT
Subject: $_QUESTION
Due Date : $_DUE_DATE.

Best Regards, 
Green Park Corporate Audit Team.';


    const TICKET_ASSIGN_NOTIFICATION = 'Ticket $_TICKET_ID is assigned to you. &Hotel : $_HOTEL &Department : $_DEPARTMENT &Subject $_QUESTION &Due Date: $_DUE_DATE.';

    const TICKET_SUBMITTED = 'Ticket $_TICKET_ID has been $_STATUS. <br>Hotel : $_HOTEL <br> Department: $_DEPARTMENT <br>Subject : $_QUESTION.';

    const TICKET_SUBMITTED_MESSAGE = 'Hi $_FULL_NAME   
Ticket $_TICKET_ID  status has been changed to $_STATUS.
Hotel : $_HOTEL
Department : $_DEPARTMENT
Subject: $_QUESTION

Best Regards,
Green Park Corporate Audit Team.';

    const TICKET_SUBMITTED_NOTIFICATION = 'Ticket $_TICKET_ID has been $_STATUS. &Hotel : $_HOTEL  &Department : $_DEPARTMENT &Subject : "$_QUESTION".';

    const TICKET_REJECTED = 'Ticket $_TICKET_ID  has been rejected .<br>Hotel : $_HOTEL  <br>Department : $_DEPARTMENT <br>Subject : "$_QUESTION".';
    const TICKET_REJECTED_NOTIFICATION = 'Ticket $_TICKET_ID  has been rejected .&Hotel : $_HOTEL  &Department : $_DEPARTMENT &Subject : "$_QUESTION".';
    const TICKET_REJECTED_MESSAGE = 'Hi $_FULL_NAME
Ticket $_TICKET_ID  has been rejected .

Subject : "$_QUESTION"
Hotel : $_HOTEL 
Department : $_DEPARTMENT 

Best Regards, 
Green Park Corporate Audit Team.';


    const TICKET_REMINDER = 'Reminder: Ticket $_TICKET_ID is pending for resolution by $_DUE_DATE.<br>Subject : "$_QUESTION" <br>Hotel : $_HOTEL <br>Department : $_DEPARTMENT';
    const TICKET_REMINDER_NOTIFICATION = 'Reminder: Ticket $_TICKET_ID is pending for resolution by $_DUE_DATE.&Subject : "$_QUESTION" &Hotel : $_HOTEL &Department : $_DEPARTMENT';

    const TICKET_REMINDER_MESSAGE = 'Hi $_FULL_NAME

Reminder: Ticket $_TICKET_ID  is pending for resolution by $_DUE_DATE.

Subject : "$_QUESTION"
Hotel : $_HOTEL
Department : $_DEPARTMENT

Best Regards,
Green Park Corporate Audit Team.';


    const TICKET_OVER_DUE = 'Overdue: Ticket $_TICKET_ID has breached due date $_DUE_DATE.<br>Subject : "$_QUESTION" <br>Hotel : $_HOTEL <br>Department : $_DEPARTMENT';
    const TICKET_OVER_DUE_NOTIFICATION = 'Overdue: Ticket $_TICKET_ID has breached due date $_DUE_DATE.&Subject : "$_QUESTION" &Hotel : $_HOTEL &Department : $_DEPARTMENT';
    const TICKET_OVER_DUE_MESSAGE = 'Hi $_FULL_NAME

Overdue: Ticket $_TICKET_ID has breached due date $_DUE_DATE.

Subject : "$_QUESTION"
Hotel : $_HOTEL
Department : $_DEPARTMENT

Best Regards, 
Green Park Corporate Audit Team.';

    const TICKET_ESCALATION_ONE = 'Escalation 1: Ticket $_TICKET_ID has breached due date $_DUE_DATE. <br>Subject : "$_QUESTION" <br>Hotel : $_HOTEL <br>Department : $_DEPARTMENT';

    const TICKET_ESCALATION_ONE_MESSAGE = 'Hi $_FULL_NAME

Escalation 1: Ticket $_TICKET_ID has breached due date $_DUE_DATE.

Subject : "$_QUESTION"
Hotel : $_HOTEL
Department : $_DEPARTMENT

Best Regards, 
Green Park Corporate Audit Team.';


    const TICKET_ESCALATION_TWO = 'Escalation 2: Ticket $_TICKET_ID has breached due date $_DUE_DATE. <br>Subject : "$_QUESTION" <br>Hotel : $_HOTEL <br>Department : $_DEPARTMENT';
    const TICKET_ESCALATION_TWO_MESSAGE = 'Hi $_FULL_NAME

Escalation 2: Ticket $_TICKET_ID has breached due date $_DUE_DATE.

Subject : "$_QUESTION"
Hotel : $_HOTEL
Department : $_DEPARTMENT

Best Regards, 
Green Park Corporate Audit Team.';


    const TICKET_ESCALATION_THREE = 'Escalation 3: Ticket $_TICKET_ID has breached due date $_DUE_DATE. <br>Subject : "$_QUESTION" <br>Hotel : $_HOTEL <br>Department : $_DEPARTMENT';

    const TICKET_ESCALATION_THREE_MESSAGE = 'Hi $_FULL_NAME

Escalation 3: Ticket $_TICKET_ID has breached due date $_DUE_DATE.

Subject : "$_QUESTION"
Hotel : $_HOTEL
Department : $_DEPARTMENT

Best Regards, 
Green Park Corporate Audit Team.';

    const TICKET_ESCALATION_FOUR = 'Escalation 4: Ticket $_TICKET_ID has breached due date $_DUE_DATE. <br>Subject : "$_QUESTION" <br>Hotel : $_HOTEL <br>Department : $_DEPARTMENT';
    const TICKET_ESCALATION_FOUR_MESSAGE = 'Hi $_FULL_NAME

Escalation 4: Ticket $_TICKET_ID has breached due date $_DUE_DATE.

Subject : "$_QUESTION"
Hotel : $_HOTEL
Department : $_DEPARTMENT

Best Regards, 
Green Park Corporate Audit Team.';

    const TICKET_ESCALATION_FIVE = 'Escalation 5: Ticket $_TICKET_ID has breached due date $_DUE_DATE. <br>Subject : "$_QUESTION" <br>Hotel : $_HOTEL <br>Department : $_DEPARTMENT';
    const TICKET_ESCALATION_FIVE_MESSAGE = 'Hi $_FULL_NAME

Escalation 5: Ticket $_TICKET_ID has breached due date $_DUE_DATE.

Subject : "$_QUESTION"
Hotel : $_HOTEL
Department : $_DEPARTMENT

Best Regards, 
Green Park Corporate Audit Team.';

    const REMAINDER_AUDIT_ID = 1;
    const REMAINDER_OVERDUE_ID = 2;
    const LOW_SCROE_AUDIT_ID = 3;
    const AUDIT_SUBMIT_ID = 4;
    const REMAINDER_TICKET_ID = 5;
    const OVERDUE_TICKET_ID = 6;

    const TICKET_ESC_ONE = 7;
    const TICKET_ESC_TWO = 8;
    const TICKET_ESC_THREE = 9;
    const TICKET_ESC_FOUR = 10;
    const TICKET_ESC_FIVE = 11;


    /**
     * Trigger notifications for tickets
     */
    public function triggerTicketsMail()
    {
        try {

            /*****Ticket Remainder****/
            $reminderTimes = $this->getNotificationTimes('ticket_reminder');

            $remTickets = $this->getRemainderTickets($reminderTimes);

            $dueTickets = call_user_func_array('array_merge', $remTickets);
            $newRemTickets = array_map(function ($x) {
                $x['type'] = 'ticketRemainder';
                return $x;
            }, $dueTickets);


            $usersList = Alertmaster::getListOfAlertTypeUsers(self::REMAINDER_TICKET_ID);

            $userIds = ArrayHelper::getColumn($usersList, 'user_id');
            $userHotels = UserHotels::find()->select(['hotel_id', 'user_id'])->where(['user_id' => $userIds])->asArray()->all();
            $userHotels = ArrayHelper::index($userHotels, null, 'user_id');
            $userdepartments = UserDepartments::find()->joinWith(['department'])->where(['user_id' => $userIds])->asArray()->all();
            $userdepartments = ArrayHelper::index($userdepartments, null, 'user_id');


            $newTickets = ArrayHelper::index($newRemTickets, null, 'userId');
            foreach ($newTickets as $tickets) {

                $mailContent = '';
                $user = [];
                $ticketNames = [];

                foreach ($tickets as $ticket) {
                    $toMail = $ticket['toEmail'];
                    $userId = $ticket['userId'];
                    $user['first_name'] = $ticket['first_name'];
                    $user['last_name'] = $ticket['last_name'];
                    $attributes = $ticket;

                    $attributes['department'] = isset($ticket['department_name']) ? $ticket['department_name'] : '';
                    $attributes['hotel'] = isset($ticket['hotel_name']) ? $ticket['hotel_name'] : '';
                    $hotelName = $attributes['hotel_name'];
                    $departmentName = $attributes['department_name'];
                    $ticketNames[] = $attributes['ticket_id'];
                    $mailContent .= 'Ticket : ' . $attributes['ticket_id'] . '<br>';
                    $mailContent .= 'Subject : ' . $attributes['question'] . '<br>';
                    $mailContent .= 'Hotel : ' . $hotelName . '<br>';
                    $mailContent .= 'Department : ' . $departmentName . '<br>';
                    $mailContent .= 'Due Date : ' . date('d-m-Y', strtotime($attributes['due_date']));
                    $mailContent .= '<br><br>';
                }
                $content = 'Following Tickets are pending for resolution.<br><br>';

                $content .= $mailContent;
                $params['recipientMail'] = $toMail;

                $mailStatus = '';
                $params['subject'] = 'Tickets are pending for resolution';
                $params['message'] = $this->buildMailContent($user, $content);
                $mailStatus = EmailsComponent::sendMail($params);

                $logData['notification_name'] = implode(',', $ticketNames);
                $logData['user_id'] = $userId;
                $logData['notification_message'] = $content;
                $logData['notification_type'] = 2;
                $logData['response_status'] = $mailStatus;
                $this->logTable($logData);

            }

            foreach ($newRemTickets as $ticket) {

                $ticket['emailTrigger'] = 0;
                $ticket['smsTrigger'] = 1;
                $ticket['pushNotificationTrigger'] = 1;

                $this->triggerNotifications($ticket, true);// audtiror

                foreach ($usersList as $user) {
                    $eData[] = array();
                    $eData['type'] = 'ticketRemainder';
                    $eData['toEmail'] = $user['email'];
                    $eData['mobileNumber'] = $user['phone'];
                    $eData['deviceToken'] = $user['device_token'];

                    $eData['hotel_name'] = $ticket['hotel_name'];
                    $eData['department_name'] = $ticket['department_name'];

                    $eData['ticket_id'] = $ticket['ticket_id'];
                    $eData['question'] = $ticket['question'];
                    $eData['due_date'] = $ticket['due_date'];
                    $eData['userId'] = $user['user_id'];

                    $eData['emailTrigger'] = 0;
                    $eData['smsTrigger'] = $user['smsTrigger'];
                    $eData['pushNotificationTrigger'] = $user['pushNotificationTrigger'];

                    $hotels = isset($userHotels[$user['user_id']]) ? $userHotels[$user['user_id']] : [];
                    $departments = isset($userdepartments[$user['user_id']]) ? $userdepartments[$user['user_id']] : [];

                    if ($hotels) {
                        $hotels = ArrayHelper::getColumn($hotels, 'hotel_id');
                    }
                    if ($hotels) {
                        $departments = ArrayHelper::getColumn($departments, 'department.department_id');
                    }
                    if (in_array($ticket['hotel_id'], $hotels) && in_array($ticket['department_id'], $departments)) {
                        $this->triggerNotifications($eData, true);
                    }
                }
            }
            $this->consolidateTicketRemainder($usersList, $newRemTickets, $userHotels, $userdepartments);


            /*****Ticket Remainder****/


        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function ticketOverDues()
    {
        /*****Ticket Overdue****/
        $dueTickets = $this->getOverDueTickets();
        $dueTickets = call_user_func_array('array_merge', $dueTickets);
        $newDueTickets = array_map(function ($x) {
            $x['type'] = 'ticketOverDue';
            return $x;
        }, $dueTickets);


        $usersList = Alertmaster::getListOfAlertTypeUsers(self::OVERDUE_TICKET_ID);

        $userIds = ArrayHelper::getColumn($usersList, 'user_id');
        $userHotels = UserHotels::find()->select(['hotel_id', 'user_id'])->where(['user_id' => $userIds])->asArray()->all();
        $userHotels = ArrayHelper::index($userHotels, null, 'user_id');
        $userdepartments = UserDepartments::find()->joinWith(['department'])->where(['user_id' => $userIds])->asArray()->all();
        $userdepartments = ArrayHelper::index($userdepartments, null, 'user_id');

        $newTickets = ArrayHelper::index($newDueTickets, null, 'userId');

        foreach ($newTickets as $tickets) {

            $mailContent = '';
            $user = [];
            $ticketNames = [];

            foreach ($tickets as $ticket) {
                $toMail = $ticket['toEmail'];
                $userId = $ticket['userId'];
                $user['first_name'] = $ticket['first_name'];
                $user['last_name'] = $ticket['last_name'];

                $attributes = $ticket;

                $attributes['department'] = isset($ticket['department_name']) ? $ticket['department_name'] : '';
                $attributes['hotel'] = isset($ticket['hotel_name']) ? $ticket['hotel_name'] : '';
                $hotelName = $attributes['hotel_name'];
                $departmentName = $attributes['department_name'];
                $ticketNames[] = $attributes['ticket_id'];
                $mailContent .= 'Ticket : ' . $attributes['ticket_id'] . '<br>';
                $mailContent .= 'Subject : ' . $attributes['question'] . '<br>';
                $mailContent .= 'Hotel : ' . $hotelName . '<br>';
                $mailContent .= 'Department : ' . $departmentName . '<br>';
                $mailContent .= 'Due Date : ' . date('d-m-Y', strtotime($attributes['due_date']));
                $mailContent .= '<br><br>';
            }
            $content = 'Following Tickets breached due dates.<br><br>';

            $content .= $mailContent;
            $params['recipientMail'] = $toMail;

            $mailStatus = '';
            $params['subject'] = 'Tickets breached due dates';
            $params['message'] = $this->buildMailContent($user, $content);

            $mailStatus = EmailsComponent::sendMail($params);

            $logData['notification_name'] = implode(',', $ticketNames);
            $logData['user_id'] = $userId;
            $logData['notification_message'] = $content;
            $logData['notification_type'] = 2;
            $logData['response_status'] = $mailStatus;
            $this->logTable($logData);

        }


        foreach ($newDueTickets as $ticket) {

            $ticket['emailTrigger'] = 0;
            $ticket['smsTrigger'] = 1;
            $ticket['pushNotificationTrigger'] = 1;

            $this->triggerNotifications($ticket, true);// audtiror
            foreach ($usersList as $user) {
                $eData[] = array();
                $eData['type'] = 'ticketOverDue';

                $eData['hotel_name'] = $ticket['hotel_name'];
                $eData['department_name'] = $ticket['department_name'];

                $eData['mobileNumber'] = $user['phone'];
                $eData['deviceToken'] = $user['device_token'];
                $eData['toEmail'] = $user['email'];
                $eData['ticket_id'] = $ticket['ticket_id'];
                $eData['question'] = $ticket['question'];
                $eData['due_date'] = $ticket['due_date'];
                $eData['userId'] = $user['user_id'];

                $eData['emailTrigger'] = 0;
                $eData['smsTrigger'] = $user['smsTrigger'];
                $eData['pushNotificationTrigger'] = $user['pushNotificationTrigger'];

                $hotels = isset($userHotels[$user['user_id']]) ? $userHotels[$user['user_id']] : [];
                $departments = isset($userdepartments[$user['user_id']]) ? $userdepartments[$user['user_id']] : [];

                if ($hotels) {
                    $hotels = ArrayHelper::getColumn($hotels, 'hotel_id');
                }
                if ($hotels) {
                    $departments = ArrayHelper::getColumn($departments, 'department.department_id');
                }
                if (in_array($ticket['hotel_id'], $hotels) && in_array($ticket['department_id'], $departments)) {
                    $this->triggerNotifications($eData, true);
                }
            }
        }

        $this->consolidateTicketOverdue($usersList, $newDueTickets, $userHotels, $userdepartments);

        /*****Ticket Overdue****/
    }

    /**
     * @throws Exception
     */
    public function escalationOneTickets()
    {
        /*****Ticket Escalations one****/

        $escalationsOneTickets = $this->getEscalationTickets(1, [1, 2, 3]);
        $dueTickets = call_user_func_array('array_merge', $escalationsOneTickets);
        $newDueTickets = array_map(function ($x) {
            $x['type'] = 'ticketEscalationOne';
            return $x;
        }, $dueTickets);
        $usersList = Alertmaster::getListOfAlertTypeUsers(self::TICKET_ESC_ONE);


        $userIds = ArrayHelper::getColumn($usersList, 'user_id');
        $userHotels = UserHotels::find()->select(['hotel_id', 'user_id'])->where(['user_id' => $userIds])->asArray()->all();
        $userHotels = ArrayHelper::index($userHotels, null, 'user_id');
        $userdepartments = UserDepartments::find()->joinWith(['department'])->where(['user_id' => $userIds])->asArray()->all();
        $userdepartments = ArrayHelper::index($userdepartments, null, 'user_id');

        $this->sendEscalationConsolidate($newDueTickets, 'Escalation 1 : ');

        $this->consolidateEscalationTickets($usersList, $newDueTickets, $userHotels, $userdepartments, 'Escalation 1 :');

        foreach ($newDueTickets as $ticket) {

            $ticket['emailTrigger'] = 0;
            $ticket['smsTrigger'] = 1;
            $ticket['pushNotificationTrigger'] = 1;

            $this->triggerNotifications($ticket, true);// audtiror

            foreach ($usersList as $user) {
                $eData[] = array();
                $eData['type'] = 'ticketEscalationOne';
                $eData['toEmail'] = $user['email'];
                $eData['mobileNumber'] = $user['phone'];
                $eData['deviceToken'] = $user['device_token'];
                $eData['hotel_name'] = $ticket['hotel_name'];
                $eData['department_name'] = $ticket['department_name'];


                $eData['ticket_id'] = $ticket['ticket_id'];
                $eData['question'] = $ticket['question'];
                $eData['due_date'] = $ticket['due_date'];
                $eData['userId'] = $user['user_id'];

                $eData['emailTrigger'] = 0;
                $eData['smsTrigger'] = $user['smsTrigger'];
                $eData['pushNotificationTrigger'] = $user['pushNotificationTrigger'];

                $hotels = isset($userHotels[$user['user_id']]) ? $userHotels[$user['user_id']] : [];
                $departments = isset($userdepartments[$user['user_id']]) ? $userdepartments[$user['user_id']] : [];

                if ($hotels) {
                    $hotels = ArrayHelper::getColumn($hotels, 'hotel_id');
                }
                if ($hotels) {
                    $departments = ArrayHelper::getColumn($departments, 'department.department_id');
                }
                if (in_array($ticket['hotel_id'], $hotels) && in_array($ticket['department_id'], $departments)) {
                    $this->triggerNotifications($eData, true);
                }
            }
        }
    }

    /**
     * @param $totalTickets
     * @param $subject
     * @throws \Exception
     */
    public function sendEscalationConsolidate($totalTickets, $subject)
    {
        $newTickets = ArrayHelper::index($totalTickets, null, 'userId');

        foreach ($newTickets as $tickets) {

            $mailContent = '';
            $user = [];
            $ticketNames = [];

            foreach ($tickets as $ticket) {
                $toMail = $ticket['toEmail'];
                $userId = $ticket['userId'];
                $user['first_name'] = $ticket['first_name'];
                $user['last_name'] = $ticket['last_name'];

                $attributes = $ticket;

                $attributes['department'] = isset($ticket['department_name']) ? $ticket['department_name'] : '';
                $attributes['hotel'] = isset($ticket['hotel_name']) ? $ticket['hotel_name'] : '';
                $hotelName = $attributes['hotel_name'];
                $departmentName = $attributes['department_name'];
                $ticketNames[] = $attributes['ticket_id'];
                $mailContent .= 'Ticket : ' . $attributes['ticket_id'] . '<br>';
                $mailContent .= 'Subject : ' . $attributes['question'] . '<br>';
                $mailContent .= 'Hotel : ' . $hotelName . '<br>';
                $mailContent .= 'Department : ' . $departmentName . '<br>';
                $mailContent .= 'Due Date : ' . date('d-m-Y', strtotime($attributes['due_date']));
                $mailContent .= '<br><br>';
            }
            $content = 'Following Tickets assigned to you breached due dates.<br><br>';

            $content .= $mailContent;
            $params['recipientMail'] = $toMail;

            $mailStatus = '';
            $params['subject'] = $subject . ' Tickets breached due dates';
            $params['message'] = $this->buildMailContent($user, $content);

            $mailStatus = EmailsComponent::sendMail($params);

            $logData['notification_name'] = implode(',', $ticketNames);
            $logData['user_id'] = $userId;
            $logData['notification_message'] = $content;
            $logData['notification_type'] = 2;
            $logData['response_status'] = $mailStatus;
            $this->logTable($logData);
        }
    }

    /**
     * @throws Exception
     */
    public function escalationTwoTickets()
    {
        /*****Ticket Escalations Two****/

        $escalationsTwoTickets = $this->getEscalationTickets(3, [1, 2]);
        $dueTickets = call_user_func_array('array_merge', $escalationsTwoTickets);
        $newDueTickets = array_map(function ($x) {
            $x['type'] = 'ticketEscalationTwo';
            return $x;
        }, $dueTickets);
        $usersList = Alertmaster::getListOfAlertTypeUsers(self::TICKET_ESC_TWO);

        $userIds = ArrayHelper::getColumn($usersList, 'user_id');
        $userHotels = UserHotels::find()->select(['hotel_id', 'user_id'])->where(['user_id' => $userIds])->asArray()->all();
        $userHotels = ArrayHelper::index($userHotels, null, 'user_id');
        $userdepartments = UserDepartments::find()->joinWith(['department'])->where(['user_id' => $userIds])->asArray()->all();
        $userdepartments = ArrayHelper::index($userdepartments, null, 'user_id');

        $this->sendEscalationConsolidate($newDueTickets, 'Escalation 2 : ');
        $this->consolidateEscalationTickets($usersList, $newDueTickets, $userHotels, $userdepartments, 'Escalation 2 :');


        foreach ($newDueTickets as $ticket) {

            $ticket['emailTrigger'] = 0;
            $ticket['smsTrigger'] = 1;
            $ticket['pushNotificationTrigger'] = 1;

            $this->triggerNotifications($ticket, true);// audtiror

            foreach ($usersList as $user) {
                $eData[] = array();
                $eData['type'] = 'ticketEscalationTwo';
                $eData['toEmail'] = $user['email'];
                $eData['mobileNumber'] = $user['phone'];
                $eData['deviceToken'] = $user['device_token'];
                $eData['hotel_name'] = $ticket['hotel_name'];
                $eData['department_name'] = $ticket['department_name'];


                $eData['ticket_id'] = $ticket['ticket_id'];
                $eData['question'] = $ticket['question'];
                $eData['due_date'] = $ticket['due_date'];
                $eData['userId'] = $user['user_id'];

                $eData['emailTrigger'] = 0;
                $eData['smsTrigger'] = $user['smsTrigger'];
                $eData['pushNotificationTrigger'] = $user['pushNotificationTrigger'];


                $hotels = isset($userHotels[$user['user_id']]) ? $userHotels[$user['user_id']] : [];
                $departments = isset($userdepartments[$user['user_id']]) ? $userdepartments[$user['user_id']] : [];

                if ($hotels) {
                    $hotels = ArrayHelper::getColumn($hotels, 'hotel_id');
                }
                if ($hotels) {
                    $departments = ArrayHelper::getColumn($departments, 'department.department_id');
                }
                if (in_array($ticket['hotel_id'], $hotels) && in_array($ticket['department_id'], $departments)) {
                    $this->triggerNotifications($eData, true);
                }

            }
        }
    }

    /**
     * @throws Exception
     */
    public function escalationThreeTickets()
    {
        /*****Ticket Escalations Three****/

        $escalationsThreeTickets = $this->getEscalationTickets(5, [1, 2]);
        $dueTickets = call_user_func_array('array_merge', $escalationsThreeTickets);
        $newDueTickets = array_map(function ($x) {
            $x['type'] = 'ticketEscalationThree';
            return $x;
        }, $dueTickets);
        $usersList = Alertmaster::getListOfAlertTypeUsers(self::TICKET_ESC_THREE);

        $userIds = ArrayHelper::getColumn($usersList, 'user_id');
        $userHotels = UserHotels::find()->select(['hotel_id', 'user_id'])->where(['user_id' => $userIds])->asArray()->all();
        $userHotels = ArrayHelper::index($userHotels, null, 'user_id');
        $userdepartments = UserDepartments::find()->joinWith(['department'])->where(['user_id' => $userIds])->asArray()->all();
        $userdepartments = ArrayHelper::index($userdepartments, null, 'user_id');

        $this->sendEscalationConsolidate($newDueTickets, 'Escalation 3 : ');
        $this->consolidateEscalationTickets($usersList, $newDueTickets, $userHotels, $userdepartments, 'Escalation 3 :');

        foreach ($newDueTickets as $ticket) {

            $ticket['emailTrigger'] = 0;
            $ticket['smsTrigger'] = 1;
            $ticket['pushNotificationTrigger'] = 1;

            $this->triggerNotifications($ticket, true);// audtiror

            foreach ($usersList as $user) {
                $eData[] = array();
                $eData['type'] = 'ticketEscalationThree';
                $eData['toEmail'] = $user['email'];
                $eData['mobileNumber'] = $user['phone'];
                $eData['deviceToken'] = $user['device_token'];
                $eData['hotel_name'] = $ticket['hotel_name'];
                $eData['department_name'] = $ticket['department_name'];

                $eData['ticket_id'] = $ticket['ticket_id'];
                $eData['question'] = $ticket['question'];
                $eData['due_date'] = $ticket['due_date'];
                $eData['userId'] = $user['user_id'];

                $eData['emailTrigger'] = 0;
                $eData['smsTrigger'] = $user['smsTrigger'];
                $eData['pushNotificationTrigger'] = $user['pushNotificationTrigger'];


                $hotels = isset($userHotels[$user['user_id']]) ? $userHotels[$user['user_id']] : [];
                $departments = isset($userdepartments[$user['user_id']]) ? $userdepartments[$user['user_id']] : [];

                if ($hotels) {
                    $hotels = ArrayHelper::getColumn($hotels, 'hotel_id');
                }
                if ($hotels) {
                    $departments = ArrayHelper::getColumn($departments, 'department.department_id');
                }
                if (in_array($ticket['hotel_id'], $hotels) && in_array($ticket['department_id'], $departments)) {
                    $this->triggerNotifications($eData, true);
                }

            }
        }
    }

    /**
     * @throws Exception
     */
    public function escalationFourTickets()
    {
        /*****Ticket Escalations Four****/

        $escalationsFourTickets = $this->getEscalationTickets(7, 1);
        $dueTickets = call_user_func_array('array_merge', $escalationsFourTickets);
        $newDueTickets = array_map(function ($x) {
            $x['type'] = 'ticketEscalationFour';
            return $x;
        }, $dueTickets);
        $usersList = Alertmaster::getListOfAlertTypeUsers(self::TICKET_ESC_FOUR);


        $userIds = ArrayHelper::getColumn($usersList, 'user_id');
        $userHotels = UserHotels::find()->select(['hotel_id', 'user_id'])->where(['user_id' => $userIds])->asArray()->all();
        $userHotels = ArrayHelper::index($userHotels, null, 'user_id');
        $userdepartments = UserDepartments::find()->joinWith(['department'])->where(['user_id' => $userIds])->asArray()->all();
        $userdepartments = ArrayHelper::index($userdepartments, null, 'user_id');


        $this->sendEscalationConsolidate($newDueTickets, 'Escalation 4 : ');
        $this->consolidateEscalationTickets($usersList, $newDueTickets, $userHotels, $userdepartments, 'Escalation 4 :');

        foreach ($newDueTickets as $ticket) {

            $ticket['emailTrigger'] = 0;
            $ticket['smsTrigger'] = 1;
            $ticket['pushNotificationTrigger'] = 1;

            $this->triggerNotifications($ticket, true);// audtiror

            foreach ($usersList as $user) {

                $eData[] = array();
                $eData['type'] = 'ticketEscalationFour';
                $eData['toEmail'] = $user['email'];
                $eData['mobileNumber'] = $user['phone'];
                $eData['deviceToken'] = $user['device_token'];

                $eData['ticket_id'] = $ticket['ticket_id'];
                $eData['question'] = $ticket['question'];
                $eData['hotel_name'] = $ticket['hotel_name'];
                $eData['department_name'] = $ticket['department_name'];

                $eData['due_date'] = $ticket['due_date'];
                $eData['userId'] = $user['user_id'];

                $eData['emailTrigger'] = 0;
                $eData['smsTrigger'] = $user['smsTrigger'];
                $eData['pushNotificationTrigger'] = $user['pushNotificationTrigger'];


                $hotels = isset($userHotels[$user['user_id']]) ? $userHotels[$user['user_id']] : [];
                $departments = isset($userdepartments[$user['user_id']]) ? $userdepartments[$user['user_id']] : [];

                if ($hotels) {
                    $hotels = ArrayHelper::getColumn($hotels, 'hotel_id');
                }
                if ($hotels) {
                    $departments = ArrayHelper::getColumn($departments, 'department.department_id');
                }
                if (in_array($ticket['hotel_id'], $hotels) && in_array($ticket['department_id'], $departments)) {
                    $this->triggerNotifications($eData, true);
                }
            }

        }
    }

    /**
     * @throws Exception
     */
    public function escalationFiveTickets()
    {
        /*****Ticket Escalations Five****/
        $escalationsFiveTickets = $this->getEscalationTickets(12, 1);
        $dueTickets = call_user_func_array('array_merge', $escalationsFiveTickets);
        $newDueTickets = array_map(function ($x) {
            $x['type'] = 'ticketEscalationFive';
            return $x;
        }, $dueTickets);

        $usersList = Alertmaster::getListOfAlertTypeUsers(self::TICKET_ESC_FIVE);


        $userIds = ArrayHelper::getColumn($usersList, 'user_id');
        $userHotels = UserHotels::find()->select(['hotel_id', 'user_id'])->where(['user_id' => $userIds])->asArray()->all();
        $userHotels = ArrayHelper::index($userHotels, null, 'user_id');
        $userdepartments = UserDepartments::find()->joinWith(['department'])->where(['user_id' => $userIds])->asArray()->all();
        $userdepartments = ArrayHelper::index($userdepartments, null, 'user_id');

        $this->sendEscalationConsolidate($newDueTickets, 'Escalation 5 : ');
        $this->consolidateEscalationTickets($usersList, $newDueTickets, $userHotels, $userdepartments, 'Escalation 5 :');

        foreach ($newDueTickets as $ticket) {

            $ticket['emailTrigger'] = 0;
            $ticket['smsTrigger'] = 1;
            $ticket['pushNotificationTrigger'] = 1;

            $this->triggerNotifications($ticket, true);// audtiror

            foreach ($usersList as $user) {

                $eData[] = array();
                $eData['type'] = 'ticketEscalationFive';
                $eData['toEmail'] = $user['email'];
                $eData['mobileNumber'] = $user['phone'];
                $eData['deviceToken'] = $user['device_token'];

                $eData['ticket_id'] = $ticket['ticket_id'];
                $eData['question'] = $ticket['question'];
                $eData['hotel_name'] = $ticket['hotel_name'];
                $eData['department_name'] = $ticket['department_name'];
                $eData['due_date'] = $ticket['due_date'];
                $eData['userId'] = $user['user_id'];

                $eData['emailTrigger'] = 0;
                $eData['smsTrigger'] = $user['smsTrigger'];
                $eData['pushNotificationTrigger'] = $user['pushNotificationTrigger'];

                $hotels = isset($userHotels[$user['user_id']]) ? $userHotels[$user['user_id']] : [];
                $departments = isset($userdepartments[$user['user_id']]) ? $userdepartments[$user['user_id']] : [];

                if ($hotels) {
                    $hotels = ArrayHelper::getColumn($hotels, 'hotel_id');
                }
                if ($hotels) {
                    $departments = ArrayHelper::getColumn($departments, 'department.department_id');
                }
                if (in_array($ticket['hotel_id'], $hotels) && in_array($ticket['department_id'], $departments)) {
                    $this->triggerNotifications($eData, true);
                }
            }
        }


    }

    /**
     * @param $usersList
     * @param $newRemTickets
     * @throws \Exception
     */
    public function consolidateTicketRemainder($usersList, $newRemTickets, $userHotels, $userdepartments)
    {
        if ($newRemTickets) {


            foreach ($usersList as $userId => $user) {

                if ($user['emailTrigger']) {
                    $mailContent = '';
                    $ticketNames = [];
                    foreach ($newRemTickets as $ticket) {


                        $hotels = isset($userHotels[$user['user_id']]) ? $userHotels[$user['user_id']] : [];
                        $departments = isset($userdepartments[$user['user_id']]) ? $userdepartments[$user['user_id']] : [];

                        if ($hotels) {
                            $hotels = ArrayHelper::getColumn($hotels, 'hotel_id');
                        }
                        if ($hotels) {
                            $departments = ArrayHelper::getColumn($departments, 'department.department_id');
                        }
                        if (!(in_array($ticket['hotel_id'], $hotels) && in_array($ticket['department_id'], $departments))) {
                            continue;
                        }

                        $hotelName = $ticket['hotel_name'];
                        $departmentName = $ticket['department_name'];
                        $ticketNames[] = $ticket['ticket_id'];
                        $mailContent .= 'Ticket : ' . $ticket['ticket_id'] . '<br>';
                        $mailContent .= 'Subject : ' . $ticket['question'] . '<br>';
                        $mailContent .= 'Hotel : ' . $hotelName . '<br>';
                        $mailContent .= 'Department : ' . $departmentName . '<br>';
                        $mailContent .= 'Due Date : ' . date('d-m-Y', strtotime($ticket['due_date']));
                        $mailContent .= '<br><br>';
                    }

                    if ($mailContent) {
                        $subject = 'Tickets Remainder';
                        $content = 'Following Tickets are pending for resolution.<br><br>';
                        $content .= $mailContent;
                        $params['recipientMail'] = $user['email'];
                        $mailStatus = '';
                        $params['subject'] = $subject;
                        $params['message'] = $this->buildMailContent($user, $content);
                        $mailStatus = EmailsComponent::sendMail($params);

                        $logData['notification_name'] = implode(',', $ticketNames);
                        $logData['user_id'] = $user['user_id'];
                        $logData['notification_message'] = $content;
                        $logData['notification_type'] = 2;
                        $logData['response_status'] = $mailStatus;
                        $this->logTable($logData);
                    }
                }
            }
        }
    }

    /**
     * @param $usersList
     * @param $newDueTickets
     * @param $userHotels
     * @param $userdepartments
     * @param $subject
     * @throws \Exception
     */
    public function consolidateEscalationTickets($usersList, $newDueTickets, $userHotels, $userdepartments, $subject)
    {
        if ($newDueTickets) {
            foreach ($usersList as $userId => $user) {

                if ($user['emailTrigger']) {

                    $mailContent = '';
                    $ticketNames = [];
                    foreach ($newDueTickets as $ticket) {

                        $hotels = isset($userHotels[$user['user_id']]) ? $userHotels[$user['user_id']] : [];
                        $departments = isset($userdepartments[$user['user_id']]) ? $userdepartments[$user['user_id']] : [];

                        if ($hotels) {
                            $hotels = ArrayHelper::getColumn($hotels, 'hotel_id');
                        }
                        if ($hotels) {
                            $departments = ArrayHelper::getColumn($departments, 'department.department_id');
                        }
                        if (!(in_array($ticket['hotel_id'], $hotels) && in_array($ticket['department_id'], $departments))) {
                            continue;
                        }

                        $hotelName = $ticket['hotel_name'];
                        $departmentName = $ticket['department_name'];
                        $ticketNames[] = $ticket['ticket_id'];
                        $mailContent .= 'Ticket : ' . $ticket['ticket_id'] . '<br>';
                        $mailContent .= 'Subject : ' . $ticket['question'] . '<br>';
                        $mailContent .= 'Hotel : ' . $hotelName . '<br>';
                        $mailContent .= 'Department : ' . $departmentName . '<br>';
                        $mailContent .= 'Due Date : ' . date('d-m-Y', strtotime($ticket['due_date']));
                        $mailContent .= '<br><br>';
                    }

                    if ($mailContent) {
                        // $subject = $subject . ' Tickets breached due dates';
                        $content = 'Following Tickets breached due dates.<br><br>';
                        $content .= $mailContent;
                        $params['recipientMail'] = $user['email'];
                        $mailStatus = '';
                        $params['subject'] = $subject . ' Tickets breached due dates';
                        $params['message'] = $this->buildMailContent($user, $content);
                        $mailStatus = EmailsComponent::sendMail($params);

                        $logData['notification_name'] = implode(',', $ticketNames);
                        $logData['user_id'] = $user['user_id'];
                        $logData['notification_message'] = $content;
                        $logData['notification_type'] = 2;
                        $logData['response_status'] = $mailStatus;
                        $this->logTable($logData);
                    }

                }
            }
        }
    }

    /**
     * @param $usersList
     * @param $newDueTickets
     * @throws \Exception
     */
    public function consolidateTicketOverdue($usersList, $newDueTickets, $userHotels, $userdepartments)
    {
        if ($newDueTickets) {


            foreach ($usersList as $userId => $user) {

                if ($user['emailTrigger']) {

                    $mailContent = '';
                    $ticketNames = [];
                    foreach ($newDueTickets as $ticket) {

                        $hotels = isset($userHotels[$user['user_id']]) ? $userHotels[$user['user_id']] : [];
                        $departments = isset($userdepartments[$user['user_id']]) ? $userdepartments[$user['user_id']] : [];

                        if ($hotels) {
                            $hotels = ArrayHelper::getColumn($hotels, 'hotel_id');
                        }
                        if ($hotels) {
                            $departments = ArrayHelper::getColumn($departments, 'department.department_id');
                        }
                        if (!(in_array($ticket['hotel_id'], $hotels) && in_array($ticket['department_id'], $departments))) {
                            continue;
                        }

                        $hotelName = $ticket['hotel_name'];
                        $departmentName = $ticket['department_name'];
                        $ticketNames[] = $ticket['ticket_id'];
                        $mailContent .= 'Ticket : ' . $ticket['ticket_id'] . '<br>';
                        $mailContent .= 'Subject : ' . $ticket['question'] . '<br>';
                        $mailContent .= 'Hotel : ' . $hotelName . '<br>';
                        $mailContent .= 'Department : ' . $departmentName . '<br>';
                        $mailContent .= 'Due Date : ' . date('d-m-Y', strtotime($ticket['due_date']));
                        $mailContent .= '<br><br>';
                    }

                    if ($mailContent) {
                        $subject = 'Tickets Overdue';
                        $content = 'Following Tickets breached due dates.<br><br>';
                        $content .= $mailContent;
                        $params['recipientMail'] = $user['email'];
                        $mailStatus = '';
                        $params['subject'] = $subject;
                        $params['message'] = $this->buildMailContent($user, $content);
                        $mailStatus = EmailsComponent::sendMail($params);

                        $logData['notification_name'] = implode(',', $ticketNames);
                        $logData['user_id'] = $user['user_id'];
                        $logData['notification_message'] = $content;
                        $logData['notification_type'] = 2;
                        $logData['response_status'] = $mailStatus;
                        $this->logTable($logData);
                    }

                }
            }
        }

    }

    public function triggerAuditsMail()
    {
        try {

            /**** overdue dates *******/
            $overdueAudits = $this->getOverdueAudits();

            $newOAudits = array_map(function ($x) {
                $x['type'] = 'auditOverDue';
                return $x;
            }, $overdueAudits);
            $newOAudits = call_user_func_array('array_merge', $newOAudits);

            $usersList = Alertmaster::getListOfAlertTypeUsers(self::REMAINDER_OVERDUE_ID);

            foreach ($newOAudits as $auditData) {

                if (is_array($auditData)) {
                    $audit = $auditData;
                    $audit['type'] = 'auditOverDue';
                    $audit['emailTrigger'] = 1;
                    $audit['smsTrigger'] = 1;
                    $audit['pushNotificationTrigger'] = 1;

                    $this->triggerNotifications($audit, true);// audtiror

                    foreach ($usersList as $user) {
                        $eData[] = array();
                        $eData['type'] = 'auditOverDue';

                        $eData['mobileNumber'] = $user['phone'];
                        $eData['deviceToken'] = $user['device_token'];
                        $eData['toEmail'] = $user['email'];
                        $eData['audit_schedule_name'] = $audit['audit_schedule_name'];
                        $eData['audit_id'] = $audit['audit_id'];
                        $eData['due_date'] = $audit['due_date'];

                        $eData['userId'] = $user['user_id'];
                        $eData['hotel_name'] = $audit['hotel_name'];
                        $eData['department_name'] = $audit['department_name'];
                        $eData['cl_name'] = $audit['cl_name'];

                        $eData['emailTrigger'] = $user['emailTrigger'];
                        $eData['smsTrigger'] = $user['smsTrigger'];
                        $eData['pushNotificationTrigger'] = $user['pushNotificationTrigger'];

                        $status = $this->validateNotificationSettings($user, $audit, 'audit');
                        if (!$status) {
                            continue;
                        }

                        $this->triggerNotifications($eData, true);
                    }
                }
            }

            /**** overdue dates *******/


            $reminderTimes = $this->getNotificationTimes('audit_reminder');
            $remainderAudits = $this->getRemainderAudits($reminderTimes);
            $remainderAudits = call_user_func_array('array_merge', $remainderAudits);
            $newAudits = array_map(function ($x) {
                $x['type'] = 'auditRemainder';
                return $x;
            }, $remainderAudits);

            $usersList = Alertmaster::getListOfAlertTypeUsers(self::REMAINDER_AUDIT_ID);

            foreach ($newAudits as $audit) {

                $audit['emailTrigger'] = 1;
                $audit['smsTrigger'] = 1;
                $audit['pushNotificationTrigger'] = 1;

                $this->triggerNotifications($audit, true);// audtiror

                foreach ($usersList as $user) {

                    $eData[] = array();
                    $eData['type'] = 'auditRemainder';

                    $eData['mobileNumber'] = $user['phone'];
                    $eData['deviceToken'] = $user['device_token'];
                    $eData['toEmail'] = $user['email'];
                    $eData['audit_schedule_name'] = $audit['audit_schedule_name'];
                    $eData['audit_id'] = $audit['audit_id'];
                    $eData['due_date'] = $audit['due_date'];

                    $eData['hotel_name'] = $audit['hotel_name'];
                    $eData['department_name'] = $audit['department_name'];
                    $eData['cl_name'] = $audit['cl_name'];

                    $eData['userId'] = $user['user_id'];

                    $eData['emailTrigger'] = $user['emailTrigger'];
                    $eData['smsTrigger'] = $user['smsTrigger'];
                    $eData['pushNotificationTrigger'] = $user['pushNotificationTrigger'];

                    $status = $this->validateNotificationSettings($user, $audit, 'audit');
                    if (!$status) {
                        continue;
                    }
                    $this->triggerNotifications($eData, true);
                }
            }

            $this->triggerCurrentDateAuditsNotifications();

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getNotificationTimes($name)
    {
        $remainderTimes = Preferences::getPrefValByName($name);

        return $remainderTimes;
    }

    /**
     * Get Escalation one tickets list
     * @return array
     */
    public function getEscalationsOneTickets()
    {

        $data = array();
        $query = new Query();
        $query->select([
            'tbl_gp_tickets.ticket_name as ticket_id',
            'tbl_gp_tickets.subject as question',
            'tbl_gp_tickets.due_date as due_date',
            'tbl_gp_user.email as toEmail',
            'tbl_gp_user.user_id as userId',
            'tbl_gp_user.phone as mobileNumber',
            'tbl_gp_user.device_token as deviceToken',
            'tbl_gp_tickets.priority_type_id as priority',


        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_user', 'tbl_gp_user.user_id =tbl_gp_tickets.assigned_user_id');

        ///$query->andFilterWhere(['=','tbl_gp_audits_schedules.status',0]);
        $due_date = date('Y-m-d ', strtotime(' + 1 day'));
        $query->andFilterWhere(['>', 'tbl_gp_tickets.due_date', $due_date]);
        $query->andFilterWhere(['IN', 'tbl_gp_tickets.priority_type_id', [1, 2, 3]]);

        $command = $query->createCommand();
        $data[] = $command->queryAll();

        return $data;
    }

    /**
     * Get Escalation one tickets list
     * @return array
     */
    public function getEscalationsTwoTickets()
    {

        $data = array();
        $query = new Query();
        $query->select([
            'tbl_gp_tickets.ticket_name as ticket_id',
            'tbl_gp_tickets.subject as question',
            'tbl_gp_tickets.due_date as due_date',
            'tbl_gp_user.email as toEmail',
            'tbl_gp_user.user_id as userId',
            'tbl_gp_user.phone as mobileNumber',
            'tbl_gp_user.device_token as deviceToken',
            'tbl_gp_tickets.priority_type_id as priority',


        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_user', 'tbl_gp_user.user_id =tbl_gp_tickets.assigned_user_id');

        ///$query->andFilterWhere(['=','tbl_gp_audits_schedules.status',0]);
        $due_date = date('Y-m-d ', strtotime(' + 2 day'));
        $query->andFilterWhere(['>', 'tbl_gp_tickets.due_date', $due_date]);
        $query->andFilterWhere(['IN', 'tbl_gp_tickets.priority_type_id', [1, 2]]);

        $command = $query->createCommand();
        $data[] = $command->queryAll();

        return $data;
    }

    /**
     * Get Escalation one tickets list
     * @return array
     */
    public function getEscalationsThreeTickets()
    {

        $data = array();
        $query = new Query();
        $query->select([
            'tbl_gp_tickets.ticket_name as ticket_id',
            'tbl_gp_tickets.subject as question',
            'tbl_gp_tickets.due_date as due_date',
            'tbl_gp_user.email as toEmail',
            'tbl_gp_user.user_id as userId',
            'tbl_gp_user.phone as mobileNumber',
            'tbl_gp_user.device_token as deviceToken',
            'tbl_gp_tickets.priority_type_id as priority',


        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_user', 'tbl_gp_user.user_id =tbl_gp_tickets.assigned_user_id');

        ///$query->andFilterWhere(['=','tbl_gp_audits_schedules.status',0]);
        $due_date = date('Y-m-d ', strtotime(' + 3 day'));
        $query->andFilterWhere(['>', 'tbl_gp_tickets.due_date', $due_date]);
        $query->andFilterWhere(['IN', 'tbl_gp_tickets.priority_type_id', [1, 2]]);

        $command = $query->createCommand();
        $data[] = $command->queryAll();

        return $data;
    }

    /**
     * Get Escalation one tickets list
     * @return array
     */
    public function getEscalationsFourTickets()
    {

        $data = array();
        $query = new Query();
        $query->select([
            'tbl_gp_tickets.ticket_name as ticket_id',
            'tbl_gp_tickets.subject as question',
            'tbl_gp_tickets.due_date as due_date',
            'tbl_gp_user.email as toEmail',
            'tbl_gp_user.user_id as userId',
            'tbl_gp_user.phone as mobileNumber',
            'tbl_gp_user.device_token as deviceToken',
            'tbl_gp_tickets.priority_type_id as priority',


        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_user', 'tbl_gp_user.user_id =tbl_gp_tickets.assigned_user_id');

        ///$query->andFilterWhere(['=','tbl_gp_audits_schedules.status',0]);
        $due_date = date('Y-m-d ', strtotime(' - 4 day'));
        $query->andFilterWhere(['<', 'tbl_gp_tickets.due_date', $due_date]);
        $query->andFilterWhere(['=', 'tbl_gp_tickets.priority_type_id', 1]);

        $command = $query->createCommand();
        $data[] = $command->queryAll();

        return $data;
    }

    /**
     * Get Escalation one tickets list
     * @return array
     */
    public function getEscalationTickets($days, $type)
    {

        $data = array();
        $query = new Query();
        $query->select([
            'tbl_gp_tickets.ticket_name as ticket_id',
            'tbl_gp_tickets.hotel_id',
            'tbl_gp_tickets.department_id',
            'tbl_gp_tickets.subject as question',
            'tbl_gp_tickets.due_date as due_date',
            'tbl_gp_user.email as toEmail',
            'tbl_gp_user.user_id as userId',
            'tbl_gp_user.first_name',
            'tbl_gp_user.last_name',

            'tbl_gp_user.phone as mobileNumber',
            'tbl_gp_user.device_token as deviceToken',
            'tbl_gp_tickets.priority_type_id as priority',
            'tbl_gp_hotels.hotel_name',
            'tbl_gp_departments.department_name',
        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_user', 'tbl_gp_user.user_id = tbl_gp_tickets.assigned_user_id')
            ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id = tbl_gp_tickets.hotel_id')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_tickets.department_id')
            ->join('LEFT JOIN', 'tbl_gp_ticket_process_critical', 'tbl_gp_ticket_process_critical.ticket_id =tbl_gp_tickets.ticket_id')
            ->join('LEFT JOIN', 'tbl_gp_process_critical_preferences', 'tbl_gp_ticket_process_critical.improve_plan_module_id =tbl_gp_process_critical_preferences.critical_preference_id');
            

        $due_date = date('Y-m-d ', strtotime(' - ' . $days . ' day'));
        $query->andFilterWhere(['<', 'tbl_gp_tickets.due_date', $due_date]);
        $query->andFilterWhere(['tbl_gp_tickets.priority_type_id' => $type]);
        $query->andFilterWhere(['IN', 'tbl_gp_tickets.status', [0, 1, 4]]);
        $query->andWhere('tbl_gp_process_critical_preferences.stop_escalations=0 OR tbl_gp_process_critical_preferences.stop_escalations is NULL OR (tbl_gp_process_critical_preferences.stop_escalations=1 AND tbl_gp_ticket_process_critical.stop_notifications_until_date IS NOT NULL AND tbl_gp_ticket_process_critical.stop_notifications_until_date < :today_date ) ',[':today_date'=> date("Y-m-d")]);
        $command = $query->createCommand();
        $data[] = $command->queryAll();

        return $data;
    }


    public function getRemainderAudits($times)
    {

        $data = array();
        // foreach ($times as $time) {
        $query = new Query();
        $query->select([
            'tbl_gp_audits_schedules.audit_schedule_name',
            'tbl_gp_audits_schedules.audit_schedule_name as audit_id',
            'tbl_gp_audits_schedules.auditor_id',
            'tbl_gp_user.email as toEmail',
            'tbl_gp_user.user_id as userId',
            'tbl_gp_user.phone as mobileNumber',
            'tbl_gp_user.device_token as deviceToken',
            'tbl_gp_audits_schedules.audit_schedule_id as audit_schedule_id',
            'tbl_gp_audits_schedules.end_date as due_date',
            'tbl_gp_audits_schedules.start_date',
            'tbl_gp_departments.department_name',
            'tbl_gp_hotels.hotel_name',
            'tbl_gp_checklists.cl_name',
        ])
            ->from('tbl_gp_audits_schedules')
            ->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id =tbl_gp_audits_schedules.audit_id')
            ->join('INNER JOIN', 'tbl_gp_user', 'tbl_gp_user.user_id =tbl_gp_audits_schedules.auditor_id')
            ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id =tbl_gp_audits.hotel_id')
            ->join('INNER JOIN', 'tbl_gp_checklists', 'tbl_gp_checklists.checklist_id =tbl_gp_audits.checklist_id')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id =tbl_gp_audits.department_id');

        ///$query->andFilterWhere(['=','tbl_gp_audits_schedules.status',0]);
        $due_date = date('Y-m-d ', strtotime(' +' . $times . ' day'));
        $query->andFilterWhere(['tbl_gp_audits_schedules.end_date' => $due_date]);
        $query->andFilterWhere(['IN', 'tbl_gp_audits_schedules.status', [0]]);
        $query->andFilterWhere(['IN', 'tbl_gp_audits_schedules.is_deleted', [0]]);

        $command = $query->createCommand();
        $data[] = $command->queryAll();
        // }


        return $data;
    }

    /**
     *
     */
    public function triggerCurrentDateAuditsNotifications()
    {
        $startDate = date('Y-m-d');

        $auditScheduled = AuditsSchedules::find()
            ->joinWith(['auditor', 'audit.checklist', 'audit.hotel', 'audit.department'])
            ->andFilterWhere(['=', 'tbl_gp_audits_schedules.start_date', $startDate])
            ->andFilterWhere(['tbl_gp_audits_schedules.status' => [0]])
            ->andFilterWhere(['tbl_gp_audits_schedules.is_deleted' => [0]])
            ->asArray()
            ->all();

        foreach ($auditScheduled as $audit) {

            $notifications = [];
            $user = $audit['auditor'];
            $notifications['type'] = 'auditAssigned';
            $notifications['toEmail'] = $user['email'];
            $notifications['mobileNumber'] = $user['phone'];
            $notifications['deviceToken'] = $user['device_token'];
            $attributes = $audit;

            $attributes['department'] = isset($audit['audit']['department']['department_name']) ? $audit['audit']['department']['department_name'] : '';
            $attributes['checkList'] = isset($audit['audit']['checklist']['cl_name']) ? $audit['audit']['checklist']['cl_name'] : '';
            $attributes['hotel'] = isset($audit['audit']['hotel']['hotel_name']) ? $audit['audit']['hotel']['hotel_name'] : '';

            $notifications['data'] = $attributes;
            $notifications['userId'] = $user['user_id'];
            Yii::$app->scheduler->triggerNotifications($notifications);
        }

    }

    public function getOverdueAudits()
    {

        $data = array();
        $query = new Query();
        $query->select([
            'tbl_gp_audits_schedules.audit_schedule_name',
            'tbl_gp_audits_schedules.audit_schedule_name as audit_id',
            'tbl_gp_audits_schedules.auditor_id',
            'tbl_gp_user.email as toEmail',
            'tbl_gp_user.user_id as userId',
            'tbl_gp_user.phone as mobileNumber',
            'tbl_gp_user.device_token as deviceToken',
            'tbl_gp_audits_schedules.audit_schedule_id as audit_schedule_id',
            'tbl_gp_audits_schedules.end_date as due_date',
            'tbl_gp_audits_schedules.start_date',
            'tbl_gp_hotels.hotel_name',
            'tbl_gp_departments.department_name',
            'tbl_gp_checklists.cl_name',
        ])
            ->from('tbl_gp_audits_schedules')
            ->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id =tbl_gp_audits_schedules.audit_id')
            ->join('INNER JOIN', 'tbl_gp_user', 'tbl_gp_user.user_id =tbl_gp_audits_schedules.auditor_id')
            ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id =tbl_gp_audits.hotel_id')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id =tbl_gp_audits.department_id')
            ->join('INNER JOIN', 'tbl_gp_checklists', 'tbl_gp_checklists.checklist_id =tbl_gp_audits.checklist_id');

        ///$query->andFilterWhere(['=','tbl_gp_audits_schedules.status',0]);
        $due_date = date('Y-m-d ');
        $query->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', $due_date]);
        $query->andFilterWhere(['in', 'tbl_gp_audits_schedules.status', [0, 1]]);
        $query->andFilterWhere(['in', 'tbl_gp_audits_schedules.is_deleted', [0]]);
        $command = $query->createCommand();
        $data[] = $command->queryAll();


        return $data;
    }

    public function getOverdueTickets()
    {

        $data = array();
        $query = new Query();
        $query->select([
            'tbl_gp_tickets.ticket_name as ticket_id',
            'tbl_gp_tickets.hotel_id',
            'tbl_gp_tickets.department_id',
            'tbl_gp_tickets.subject as question',
            'tbl_gp_tickets.due_date as due_date',
            'tbl_gp_user.email as toEmail',
            'tbl_gp_user.user_id as userId',
            'tbl_gp_user.first_name',
            'tbl_gp_user.last_name',
            'tbl_gp_user.phone as mobileNumber',
            'tbl_gp_user.device_token as deviceToken',
            'tbl_gp_hotels.hotel_name',
            'tbl_gp_departments.department_name',
        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_user', 'tbl_gp_user.user_id =tbl_gp_tickets.assigned_user_id')
            ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id =tbl_gp_tickets.hotel_id')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id =tbl_gp_tickets.department_id')
            ->join('LEFT JOIN', 'tbl_gp_ticket_process_critical', 'tbl_gp_ticket_process_critical.ticket_id =tbl_gp_tickets.ticket_id')
            ->join('LEFT JOIN', 'tbl_gp_process_critical_preferences', 'tbl_gp_ticket_process_critical.improve_plan_module_id =tbl_gp_process_critical_preferences.critical_preference_id');
            
        ///$query->andFilterWhere(['=','tbl_gp_audits_schedules.status',0]);
        $due_date = date('Y-m-d ');
        $query->andFilterWhere(['<', 'tbl_gp_tickets.due_date', $due_date]);
        $query->andFilterWhere(['tbl_gp_tickets.status' => [0, 1, 4]]);
        $query->andWhere('tbl_gp_process_critical_preferences.stop_reminders=0 OR tbl_gp_process_critical_preferences.stop_reminders is NULL OR (tbl_gp_process_critical_preferences.stop_reminders=1 AND tbl_gp_ticket_process_critical.stop_notifications_until_date IS NOT NULL AND tbl_gp_ticket_process_critical.stop_notifications_until_date < :today_date ) ',[':today_date'=> date("Y-m-d")]);
        
        $command = $query->createCommand();
        $data[] = $command->queryAll();


        return $data;
    }

    public function getRemainderTickets($times)
    {

        $data = array();
        $query = new Query();
        $query->select([
            'tbl_gp_tickets.ticket_name as ticket_id',
            'tbl_gp_tickets.subject as question',
            'tbl_gp_tickets.due_date as due_date',
            'tbl_gp_user.email as toEmail',
            'tbl_gp_user.user_id as userId',

            'tbl_gp_user.first_name',
            'tbl_gp_user.last_name',

            'tbl_gp_user.phone as mobileNumber',
            'tbl_gp_user.device_token as deviceToken',
            'tbl_gp_hotels.hotel_name',
            'tbl_gp_departments.department_name',
            'tbl_gp_tickets.hotel_id',
            'tbl_gp_tickets.department_id',
            
        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_user', 'tbl_gp_user.user_id =tbl_gp_tickets.assigned_user_id')
            ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id =tbl_gp_tickets.hotel_id')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id =tbl_gp_tickets.department_id')
            ->join('LEFT JOIN', 'tbl_gp_ticket_process_critical', 'tbl_gp_ticket_process_critical.ticket_id =tbl_gp_tickets.ticket_id')
            ->join('LEFT JOIN', 'tbl_gp_process_critical_preferences', 'tbl_gp_ticket_process_critical.improve_plan_module_id =tbl_gp_process_critical_preferences.critical_preference_id');
        

        $due_date = date('Y-m-d ', strtotime(' +' . $times . ' day'));
        $query->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', $due_date]);
        $query->andFilterWhere(['>', 'tbl_gp_tickets.due_date', date('Y-m-d ')]);
        $query->andFilterWhere(['tbl_gp_tickets.status' => [0, 1, 4]]);
        $query->andWhere('tbl_gp_process_critical_preferences.stop_reminders=0 OR tbl_gp_process_critical_preferences.stop_reminders is NULL OR (tbl_gp_process_critical_preferences.stop_reminders=1 AND tbl_gp_ticket_process_critical.stop_notifications_until_date IS NOT NULL AND tbl_gp_ticket_process_critical.stop_notifications_until_date < :today_date ) ',[':today_date'=> date("Y-m-d")]);
        $command = $query->createCommand();
        $data[] = $command->queryAll();

        return $data;
    }

    /**
     * @param $data
     */
    public function triggerNotifications($data, $background = false)
    {
        try {
            $type = $data['type'];
            $email = $data['toEmail'];
            $phoneNumber = $data['mobileNumber'];
            $deviceToken = $data['deviceToken'];
            $paramsInfo = isset($data['data']) ? $data['data'] : [];

            $content = '';
            $messageContent = '';
            $notificationContent = '';
            $subject = '';
            $logId = '';
            $alertType = '';
            $userType = 0;
            $user = User::findOne($data['userId'])->attributes;
            switch ($type) {
                case 'delegation':
                case 'auditorUpdate':
                case 'auditAssigned':
                    $content = self::AUDIT_ASSIGN;
                    $messageContent = self::AUDIT_ASSIGN_MESSAGE;
                    $notificationContent = self::AUDIT_ASSIGN_NOTIFICATION;


                    $checkListName = isset($paramsInfo['checkList']) ? $paramsInfo['checkList'] : '';
                    $hotelName = isset($paramsInfo['hotel']) ? $paramsInfo['hotel'] : '';
                    $date = isset($paramsInfo['start_date']) ? $paramsInfo['start_date'] : '';
                    $date = $date ? date('M Y', strtotime($date)) : '';

                    $subject = 'Audit ' . $paramsInfo['audit_schedule_name'] . ' is assigned. - ' . $hotelName . ' ' . $checkListName . ' ' . $date;

                    $logId = $paramsInfo['audit_schedule_name'];
                    $alertType = 'Audit Assign';
                    $userType = 2;
                    $paramsInfo['email'] = $email;
                    $content = $this->replaceShortCodes($content, $paramsInfo, $user);
                    $messageContent = $this->replaceShortCodes($messageContent, $paramsInfo, $user);
                    $notificationContent = $this->replaceShortCodes($notificationContent, $paramsInfo, $user);

                    break;
                case 'auditSubmitted':

                    $content = self::AUDIT_SUBMITTED;
                    $messageContent = self::AUDIT_SUBMITTED_MESSAGE;
                    $notificationContent = self::AUDIT_SUBMITTED_NOTIFICATION;

                    $checkListName = isset($paramsInfo['checkList']) ? $paramsInfo['checkList'] : '';
                    $hotelName = isset($paramsInfo['hotel']) ? $paramsInfo['hotel'] : '';
                    $date = isset($paramsInfo['start_date']) ? $paramsInfo['start_date'] : '';
                    $date = $date ? date('M Y', strtotime($date)) : '';

                    $subject = 'Audit ' . $paramsInfo['audit_schedule_name'] . ' is submitted - ' . $hotelName . ' ' . $checkListName . ' ' . $date;
                    $logId = $paramsInfo['audit_schedule_name'];
                    $alertType = 'Audit Completed';
                    $userType = 2;

                    $content = $this->replaceShortCodes($content, $paramsInfo, $user);
                    $messageContent = $this->replaceShortCodes($messageContent, $paramsInfo, $user);
                    $notificationContent = $this->replaceShortCodes($notificationContent, $paramsInfo, $user);

                    $mailContent = $this->addAuditDetailedInformation($paramsInfo['audit_schedule_name']);
                    $content .= $mailContent;
                    $this->sendCompletedAuditsToUsers($paramsInfo, $mailContent);
                    break;
                case 'lowScoreAudit';

                    $content = self::LOW_SCORE_AUDIT;
                    $messageContent = self::LOW_SCORE_AUDIT_MESSAGE;
                    $notificationContent = self::LOW_SCORE_AUDIT_NOTIFICATION;

                    $checkListName = isset($paramsInfo['checkList']) ? $paramsInfo['checkList'] : '';
                    $hotelName = isset($paramsInfo['hotel']) ? $paramsInfo['hotel'] : '';
                    $date = isset($paramsInfo['start_date']) ? $paramsInfo['start_date'] : '';
                    $date = $date ? date('M Y', strtotime($date)) : '';

                    $alertType = 'Audit Completed';
                    $subject = 'Low Score Alert ' . $paramsInfo['audit_schedule_name'] . '  - ' . $hotelName . ' ' . $checkListName . ' ' . $date;
                    $logId = $paramsInfo['audit_schedule_name'];
                    $userType = 2;


                    $content = $this->replaceShortCodes($content, $paramsInfo, $user);
                    $messageContent = $this->replaceShortCodes($messageContent, $paramsInfo, $user);
                    $notificationContent = $this->replaceShortCodes($notificationContent, $paramsInfo, $user);

                    $mailContent = $this->addAuditDetailedInformation($paramsInfo['audit_schedule_name']);
                    $content .= $mailContent;

                    $this->sendLowScoreAuditsToUsers($paramsInfo, $mailContent);
                    break;
                case 'ticketAssigned':

                    $content = self::TICKET_ASSIGN;
                    $messageContent = self::TICKET_ASSIGN_MESSAGE;
                    $notificationContent = self::TICKET_ASSIGN_NOTIFICATION;

                    $alertType = 'Ticket';
                    $logId = $paramsInfo['ticket_name'];
                    $userType = 3;
                    $subject = 'Ticket  ' . $paramsInfo['ticket_name'] . ' is assigned.';


                    $content = $this->replaceTicketShortCodes($content, $paramsInfo, $user);
                    $messageContent = $this->replaceTicketShortCodes($messageContent, $paramsInfo, $user);
                    $notificationContent = $this->replaceTicketShortCodes($notificationContent, $paramsInfo, $user);

                    break;

                case 'ticketStatusChangedByStaff':

                    $content = self::TICKET_SUBMITTED;
                    $messageContent = self::TICKET_SUBMITTED_MESSAGE;
                    $notificationContent = self::TICKET_SUBMITTED_NOTIFICATION;

                    $userType = 2;
                    $alertType = 'Ticket';
                    $logId = $paramsInfo['ticket_name'];
                    $subject = 'Ticket  ' . $paramsInfo['ticket_name'] . ' has been resolved.';


                    $content = $this->replaceTicketShortCodes($content, $paramsInfo, $user);
                    $messageContent = $this->replaceTicketShortCodes($messageContent, $paramsInfo, $user);
                    $notificationContent = $this->replaceTicketShortCodes($notificationContent, $paramsInfo, $user);

                    break;

                case 'ticketStatusChangedByAuditor':

                    $content = self::TICKET_REJECTED;
                    $messageContent = self::TICKET_REJECTED_MESSAGE;
                    $notificationContent = self::TICKET_REJECTED_NOTIFICATION;

                    $userType = 3;
                    $alertType = 'Ticket';
                    $logId = $paramsInfo['ticket_name'];
                    $subject = 'Ticket  ' . $paramsInfo['ticket_name'] . ' has been rejected.';


                    $content = $this->replaceTicketShortCodes($content, $paramsInfo, $user);
                    $messageContent = $this->replaceTicketShortCodes($messageContent, $paramsInfo, $user);
                    $notificationContent = $this->replaceTicketShortCodes($notificationContent, $paramsInfo, $user);

                    break;
                case 'ticketRemainder':
                    $userType = 3;
                    $content = self::TICKET_REMINDER;
                    $messageContent = self::TICKET_REMINDER_MESSAGE;
                    $notificationContent = self::TICKET_REMINDER_NOTIFICATION;

                    $alertType = 'Ticket';
                    $logId = $data['ticket_id'];
                    $subject = 'Ticket  ' . $data['ticket_id'] . ' reminder.';

                    $content = $this->replaceEscalationTicketShortCode($content, $data, $user);
                    $messageContent = $this->replaceEscalationTicketShortCode($messageContent, $data, $user);
                    $notificationContent = $this->replaceEscalationTicketShortCode($notificationContent, $data, $user);

                    break;

                case 'auditRemainder':


                    $content = self::AUDIT_REMINDER;
                    $messageContent = self::AUDIT_REMINDER_MESSAGE;
                    $notificationContent = self::AUDIT_REMINDER;

                    $userType = 2;
                    $alertType = 'Audit Assign';
                    $logId = $data['audit_id'];

                    $checkListName = isset($data['cl_name']) ? $data['cl_name'] : '';
                    $hotelName = isset($data['hotel_name']) ? $data['hotel_name'] : '';
                    $date = isset($data['start_date']) ? $data['start_date'] : '';
                    $date = $date ? date('M Y', strtotime($date)) : '';

                    $subject = 'Audit  ' . $data['audit_id'] . ' remainder - ' . $hotelName . ' ' . $checkListName . ' ' . $date;

                    $content = $this->replaceRemainderShortCode($content, $data, $user);
                    $messageContent = $this->replaceRemainderShortCode($messageContent, $data, $user);
                    $notificationContent = $this->replaceRemainderShortCode($notificationContent, $data, $user);
                    break;
                case 'auditOverDue':
                    $userType = 2;

                    $content = self::AUDIT_OVER_DUE;
                    $messageContent = self::AUDIT_OVER_DUE_MESSAGE;
                    $notificationContent = self::AUDIT_OVER_DUE;

                    $alertType = 'Audit Assign';
                    $logId = $data['audit_id'];

                    $checkListName = isset($data['cl_name']) ? $data['cl_name'] : '';
                    $hotelName = isset($data['hotel_name']) ? $data['hotel_name'] : '';
                    $date = isset($data['start_date']) ? $data['start_date'] : '';
                    $date = $date ? date('M Y', strtotime($date)) : '';

                    $subject = 'Audit  ' . $data['audit_id'] . ' overdue - ' . $hotelName . ' ' . $checkListName . ' ' . $date;
                    $content = $this->replaceRemainderShortCode($content, $data, $user);
                    $messageContent = $this->replaceRemainderShortCode($messageContent, $data, $user);
                    $notificationContent = $this->replaceRemainderShortCode($notificationContent, $data, $user);


                    break;
                case 'ticketOverDue':
                    $userType = 3;

                    $content = self::TICKET_OVER_DUE;
                    $messageContent = self::TICKET_OVER_DUE_MESSAGE;
                    $notificationContent = self::TICKET_OVER_DUE_NOTIFICATION;

                    $alertType = 'Ticket';
                    $logId = $data['ticket_id'];
                    $subject = 'Ticket  ' . $data['ticket_id'] . ' overdue.';
                    $content = $this->replaceEscalationTicketShortCode($content, $data, $user);
                    $messageContent = $this->replaceEscalationTicketShortCode($messageContent, $data, $user);
                    $notificationContent = $this->replaceEscalationTicketShortCode($notificationContent, $data, $user);
                    break;
                case 'ticketEscalationOne':


                    $content = self::TICKET_ESCALATION_ONE;
                    $messageContent = self::TICKET_ESCALATION_ONE_MESSAGE;
                    $notificationContent = self::TICKET_ESCALATION_ONE;

                    $alertType = 'Ticket';
                    $logId = $data['ticket_id'];
                    $subject = 'Ticket  ' . $data['ticket_id'] . ' overdue.';
                    $content = $this->replaceEscalationTicketShortCode($content, $data, $user);
                    $messageContent = $this->replaceEscalationTicketShortCode($messageContent, $data, $user);
                    $notificationContent = $this->replaceEscalationTicketShortCode($notificationContent, $data, $user);
                    break;
                case 'ticketEscalationTwo':


                    $content = self::TICKET_ESCALATION_TWO;
                    $messageContent = self::TICKET_ESCALATION_TWO_MESSAGE;
                    $notificationContent = self::TICKET_ESCALATION_TWO;

                    $alertType = 'Ticket';
                    $logId = $data['ticket_id'];
                    $subject = 'Ticket  ' . $data['ticket_id'] . ' overdue.';
                    $content = $this->replaceEscalationTicketShortCode($content, $data, $user);
                    $messageContent = $this->replaceEscalationTicketShortCode($messageContent, $data, $user);
                    $notificationContent = $this->replaceEscalationTicketShortCode($notificationContent, $data, $user);
                    break;
                case 'ticketEscalationThree':


                    $content = self::TICKET_ESCALATION_THREE;
                    $messageContent = self::TICKET_ESCALATION_THREE_MESSAGE;
                    $notificationContent = self::TICKET_ESCALATION_THREE;

                    $alertType = 'Ticket';
                    $logId = $data['ticket_id'];
                    $subject = 'Ticket  ' . $data['ticket_id'] . ' overdue.';
                    $content = $this->replaceEscalationTicketShortCode($content, $data, $user);
                    $messageContent = $this->replaceEscalationTicketShortCode($messageContent, $data, $user);
                    $notificationContent = $this->replaceEscalationTicketShortCode($notificationContent, $data, $user);
                    break;
                case 'ticketEscalationFour':

                    $content = self::TICKET_ESCALATION_FOUR;
                    $messageContent = self::TICKET_ESCALATION_FOUR_MESSAGE;
                    $notificationContent = self::TICKET_ESCALATION_FOUR;

                    $alertType = 'Ticket';
                    $logId = $data['ticket_id'];
                    $subject = 'Ticket  ' . $data['ticket_id'] . ' overdue.';
                    $content = $this->replaceEscalationTicketShortCode($content, $data, $user);
                    $messageContent = $this->replaceEscalationTicketShortCode($messageContent, $data, $user);
                    $notificationContent = $this->replaceEscalationTicketShortCode($notificationContent, $data, $user);
                    break;
                case 'ticketEscalationFive':

                    $content = self::TICKET_ESCALATION_FIVE;
                    $messageContent = self::TICKET_ESCALATION_FIVE_MESSAGE;
                    $notificationContent = self::TICKET_ESCALATION_FIVE;
                    $alertType = 'Ticket';
                    $logId = $data['ticket_id'];
                    $subject = 'Ticket  ' . $data['ticket_id'] . ' overdue.';
                    $content = $this->replaceEscalationTicketShortCode($content, $data, $user);
                    $messageContent = $this->replaceEscalationTicketShortCode($messageContent, $data, $user);
                    $notificationContent = $this->replaceEscalationTicketShortCode($notificationContent, $data, $user);

                    break;
            }

            $user['email'] = $email;
            $user['user_id'] = $data['userId'];
            $user['phone'] = $phoneNumber;
            $user['device_token'] = $deviceToken;
            $user['userType'] = $userType;

            $user['emailTrigger'] = isset($data['emailTrigger']) ? $data['emailTrigger'] : 1;
            $user['smsTrigger'] = isset($data['smsTrigger']) ? $data['smsTrigger'] : 1;
            $user['pushNotificationTrigger'] = isset($data['pushNotificationTrigger']) ? $data['pushNotificationTrigger'] : 1;

            if ($background) {
                $user['emailTrigger'] = $data['emailTrigger'];
                $user['smsTrigger'] = $data['smsTrigger'];
                $user['pushNotificationTrigger'] = $data['pushNotificationTrigger'];
            }
            $contents['message'] = $messageContent;
            $contents['mail'] = $content;
            $contents['notification'] = $notificationContent;

            $mailSubject = '';
            $this->sendNotifications($user, $contents, $subject, $logId, $alertType);
        } catch (Exception $e) {
            throw new Exception($e->getTraceAsString());
        }
    }

    public function replaceRemainderShortCode($content, $data, $user = [])
    {

        $firstName = isset($user['first_name']) ? $user['first_name'] : '';
        $lastName = isset($user['last_name']) ? $user['last_name'] : '';

        $hotelName = isset($data['hotel_name']) ? $data['hotel_name'] : '';
        $departmentName = isset($data['department_name']) ? $data['department_name'] : '';

        $dueDate = isset($data['due_date']) ? $data['due_date'] : '';
        $audit_id = isset($data['audit_id']) ? $data['audit_id'] : '';
        $checkListName = isset($data['cl_name']) ? $data['cl_name'] : '';

        $content = str_replace('$_FULL_NAME', $firstName . ' ' . $lastName, $content);
        $content = str_replace('$_AUDIT_ID', $audit_id, $content);
        $content = str_replace('$_HOTEL', $hotelName, $content);
        $content = str_replace('$_DUE_DATE', date('d-m-Y', strtotime($dueDate)), $content);
        $content = str_replace('$_HOTEL', $hotelName, $content);
        $content = str_replace('$_DEPARTMENT', $departmentName, $content);
        $content = str_replace('$_CHECKLIST', $checkListName, $content);


        return $content;
    }


    public function replaceEscalationTicketShortCode($content, $data, $user = [])
    {

        $firstName = isset($user['first_name']) ? $user['first_name'] : '';
        $lastName = isset($user['last_name']) ? $user['last_name'] : '';

        $ticket_id = isset($data['ticket_id']) ? $data['ticket_id'] : '';
        $hotelName = isset($data['hotel_name']) ? $data['hotel_name'] : '';
        $departmentName = isset($data['department_name']) ? $data['department_name'] : '';
        $dueDate = isset($data['due_date']) ? $data['due_date'] : '';
        $question = isset($data['question']) ? $data['question'] : '';

        $content = str_replace('$_FULL_NAME', $firstName . ' ' . $lastName, $content);
        $content = str_replace('$_TICKET_ID', $ticket_id, $content);
        $content = str_replace('$_DUE_DATE', date('d-m-Y', strtotime($dueDate)), $content);
        $content = str_replace('$_HOTEL', $hotelName, $content);
        $content = str_replace('$_DEPARTMENT', $departmentName, $content);
        $content = str_replace('$_QUESTION', $question, $content);


        return $content;
    }

    /**
     * @param $content
     * @param $data
     * @return mixed
     */
    public function replaceShortCodes($content, $data, $user = [])
    {
        // $content = urldecode($content);
        $departmentName = isset($data['department']) ? $data['department'] : '';
        $checkListName = isset($data['checkList']) ? $data['checkList'] : '';
        $hotelName = isset($data['hotel']) ? $data['hotel'] : '';
        $duteDate = isset($data['end_date']) ? $data['end_date'] : '';
        if ($duteDate) {
            $duteDate = date('d-m-Y', strtotime($duteDate));
        }
        $auditName = isset($data['audit_schedule_name']) ? $data['audit_schedule_name'] : '';
        $auditScore = isset($data['auditScore']) ? $data['auditScore'] : '';
        if ($auditScore) {
            $auditScore = number_format($auditScore, 0);
        }
        $firstName = isset($user['first_name']) ? $user['first_name'] : '';
        $lastName = isset($user['last_name']) ? $user['last_name'] : '';
        $content = str_replace('$_AUDIT_ID', $auditName, $content);
        $content = str_replace('$_FULL_NAME', $firstName . ' ' . $lastName, $content);
        $content = str_replace('$_HOTEL', $hotelName, $content);
        $content = str_replace('$_DEPARTMENT', $departmentName, $content);
        $content = str_replace('$_CHECKLIST', $checkListName, $content);
        $content = str_replace('$_DUE_DATE', $duteDate, $content);
        $content = str_replace('$_SCORE_PERCENTAGE', $auditScore, $content);
        return ($content);
    }

    /**
     * @param $content
     * @param $data
     * @return mixed
     */
    public function replaceTicketShortCodes($content, $data, $user = [])
    {
        $departmentName = isset($data['department']) ? $data['department'] : '';
        $hotelName = isset($data['hotel']) ? $data['hotel'] : '';
        $duteDate = isset($data['due_date']) ? $data['due_date'] : '';
        $ticketName = isset($data['ticket_name']) ? $data['ticket_name'] : '';
        $question = isset($data['subject']) ? $data['subject'] : '';
        $status = isset($data['status']) ? $data['status'] : '';

        if ($status && isset(Tickets::$statusList[$status])) {
            $status = Tickets::$statusList[$status];
        }

        $firstName = isset($user['first_name']) ? $user['first_name'] : '';
        $lastName = isset($user['last_name']) ? $user['last_name'] : '';
        $content = str_replace('$_FULL_NAME', $firstName . ' ' . $lastName, $content);
        $content = str_replace('$_TICKET_ID', $ticketName, $content);
        $content = str_replace('$_HOTEL', $hotelName, $content);
        $content = str_replace('$_DEPARTMENT', $departmentName, $content);
        $content = str_replace('$_QUESTION', $question, $content);
        $content = str_replace('$_DUE_DATE', $duteDate, $content);
        $content = str_replace('$_STATUS', $status, $content);
        return $content;
    }

    /**
     * @param $message
     * @param $id
     */
    public function logTable($data)
    {

        $data['created_at'] = date('Y-m-d H:i:s:');
        $data['updated_at'] = date('Y-m-d H:i:s:');
        Yii::$app->db->createCommand()->insert('tbl_gp_notification_log', $data)->execute();
    }

    /**
     * @param $content
     * @param $paramsInfo
     * @param string $email
     */
    public function sendCompletedAuditsToUsers($paramsInfo, $mailContent = '')
    {
        $usersList = Alertmaster::getListOfAlertTypeUsers(self::AUDIT_SUBMIT_ID);

        foreach ($usersList as $user) {

            $user['userType'] = 2;

            $checkListName = isset($paramsInfo['checkList']) ? $paramsInfo['checkList'] : '';
            $hotelName = isset($paramsInfo['hotel']) ? $paramsInfo['hotel'] : '';
            $date = isset($paramsInfo['start_date']) ? $paramsInfo['start_date'] : '';
            $date = $date ? date('M Y', strtotime($date)) : '';

            $subject = 'Audit ' . $paramsInfo['audit_schedule_name'] . ' is submitted - ' . $hotelName . ' ' . $checkListName . ' ' . $date;

            $content = self::AUDIT_SUBMITTED;
            $messageContent = self::AUDIT_SUBMITTED_MESSAGE;
            $notificationContent = self::AUDIT_SUBMITTED_NOTIFICATION;

            $content = $this->replaceShortCodes($content, $paramsInfo, $user);
            $messageContent = $this->replaceShortCodes($messageContent, $paramsInfo, $user);
            $notificationContent = $this->replaceShortCodes($notificationContent, $paramsInfo, $user);

            $contents['message'] = $messageContent;
            $contents['mail'] = $content . $mailContent;
            $contents['notification'] = $notificationContent;
            $audit['audit_id'] = $paramsInfo['audit_schedule_name'];
            $status = $this->validateNotificationSettings($user, $audit, 'audit');
            if (!$status) {
                continue;
            }
            $this->sendNotifications($user, $contents, $subject, $paramsInfo['audit_schedule_name'], 'Audit Completed');
        }
    }

    /**
     * @param $user
     * @param $email
     */
    public function sendNotifications($user, $contents, $subject, $notificationName, $alertType)
    {

        $data['notification_name'] = $notificationName;
        $data['user_id'] = $user['user_id'];

        if ($contents) {
            if ($user['phone'] && $user['smsTrigger']) {
                $msgStatus = '';
                $content = $contents['message'];
                $data['notification_message'] = $content;
                $msgStatus = MessagingComponent::sendTextMessage($user['phone'], $content);
                $data['notification_type'] = 1;
                $data['response_status'] = $msgStatus;
                $this->logTable($data);

            }
            if ($user['email'] && $user['emailTrigger']) {
                $params['recipientMail'] = $user['email'];

                $content = $contents['mail'];
                $data['notification_message'] = $content;
                $params['subject'] = $subject;
                $mailStatus = '';
                $params['message'] = $this->buildMailContent($user, $content);
                $mailStatus = EmailsComponent::sendMail($params);

                $data['notification_type'] = 2;
                $data['response_status'] = $mailStatus;
                $this->logTable($data);
            }
            if ($user['device_token'] && $user['pushNotificationTrigger']) {
                $title = $subject;
                $content = $contents['notification'];
                $data['notification_message'] = $content;
                $notificationStatus = '';
                $notificationStatus = MessagingComponent::createPushMessage($user, $alertType, $title, $content, $user['userType'], $notificationName);
                $data['notification_type'] = 3;
                $data['response_status'] = $notificationStatus;
                $this->logTable($data);
            }
        }
    }

    /**
     * @param $content
     * @param $paramsInfo
     * @param $email
     */
    public function sendLowScoreAuditsToUsers($paramsInfo, $mailContent = '')
    {
        $usersList = Alertmaster::getListOfAlertTypeUsers(self::LOW_SCROE_AUDIT_ID);

        foreach ($usersList as $user) {

            $checkListName = isset($paramsInfo['checkList']) ? $paramsInfo['checkList'] : '';
            $hotelName = isset($paramsInfo['hotel']) ? $paramsInfo['hotel'] : '';
            $date = isset($paramsInfo['start_date']) ? $paramsInfo['start_date'] : '';
            $date = $date ? date('M Y', strtotime($date)) : '';

            $subject = 'Low Score Alert ' . $paramsInfo['audit_schedule_name'] . ' - ' . $hotelName . ' ' . $checkListName . ' ' . $date;
            $user['userType'] = 2;

            $content = self::LOW_SCORE_AUDIT;
            $messageContent = self::LOW_SCORE_AUDIT_MESSAGE;
            $notificationContent = self::LOW_SCORE_AUDIT_NOTIFICATION;

            $content = $this->replaceShortCodes($content, $paramsInfo, $user);
            $messageContent = $this->replaceShortCodes($messageContent, $paramsInfo, $user);
            $notificationContent = $this->replaceShortCodes($notificationContent, $paramsInfo, $user);

            $contents['message'] = $messageContent;
            $contents['mail'] = $content . $mailContent;
            $contents['notification'] = $notificationContent;

            $audit['audit_id'] = $paramsInfo['audit_schedule_name'];
            $status = $this->validateNotificationSettings($user, $audit, 'audit');
            if (!$status) {
                continue;
            }

            $this->sendNotifications($user, $contents, $subject, $paramsInfo['audit_schedule_name'], 'Audit Completed');
        }
    }

    /**
     * @param $mail
     * @param $content
     * @return mixed
     * @throws \Exception
     */
    public function buildMailContent($user, $content)
    {
        $name = $user['first_name'] . ' ' . $user['last_name'];
        try {
            $message = '';
            $message .= "<html><body>";
            $message .= "<table><tr><td>Hi " . $name . "</td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td>" . $content . " </td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td>Best Regards,</td></tr>";
            $message .= "<tr><td>Green Park Corporate Audit Team.</td></tr></table>";
            $message .= "</body></html>";
            return $message;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     *
     */
    public function triggerAuditSubmittedNotifications()
    {
        try {
            $auditSchedules = AuditsSchedules::find()
                ->joinWith(['auditor', 'audit.checklist', 'audit.hotel', 'audit.department'])
                ->andWhere(['notification_status' => 0, AuditsSchedules::tableName() . '.status' => 3])
                ->asArray()
                ->all();
            $auditIds = [];
            
            foreach ($auditSchedules as $auditScheduled) {
                
                $user = $auditScheduled['auditor'];
                $notifications = [];
                $notifications['type'] = 'auditSubmitted';
                $notifications['toEmail'] = $user['email'];
                $notifications['mobileNumber'] = $user['phone'];
                $notifications['deviceToken'] = $user['device_token'];

                $attributes = $auditScheduled;
                $attributes['department'] = isset($auditScheduled['audit']['department']['department_name']) ? $auditScheduled['audit']['department']['department_name'] : '';
                $attributes['checkList'] = isset($auditScheduled['audit']['checklist']['cl_name']) ? $auditScheduled['audit']['checklist']['cl_name'] : '';
                $attributes['hotel'] = isset($auditScheduled['audit']['hotel']['hotel_name']) ? $auditScheduled['audit']['hotel']['hotel_name'] : '';

                $notifications['data'] = $attributes;
                $notifications['userId'] = $user['user_id'];
                Yii::$app->scheduler->triggerNotifications($notifications);
                $settingsScore = Preferences::getPrefValByName('low_scoring_audits');
                $auditScore = AuditsSchedules::getAuditScore($auditScheduled['audit_schedule_id']);
                
                if ($auditScore <= $settingsScore) {
                    $notifications['type'] = 'lowScoreAudit';
                    $notifications['data']['auditScore'] = $auditScore;
                    $notifications['userId'] = $user['user_id'];
                    //Yii::$app->scheduler->triggerNotifications($notifications);
                }


                $auditIds[] = $auditScheduled['audit_schedule_id'];
                
               
                /***
                 * get list of tickets for sending consolidate email
                 *  
                 */
                $sql = 'Select * from tbl_gp_tickets 
                        INNER JOIN tbl_gp_answers ans on ans.answer_id = tbl_gp_tickets.answer_id 
                        INNER JOIN tbl_gp_audits_checklist_questions qus on qus.audits_checklist_questions_id = ans.question_id 
                        WHERE (tbl_gp_tickets.chronicity = 1 OR qus.process_critical=1) 
                        AND tbl_gp_tickets.audit_schedule_id = '.$auditScheduled['audit_schedule_id'];
                 $email_tickets = \Yii::$app->db->createCommand($sql)->queryAll();
                

                              
                $tickets_data=[];
                
                foreach ($email_tickets as $email_ticket){
                    $ticket_data = [];
                    $ticket_data['ticketnumber']=$email_ticket['ticket_name'];
                    $ticket_data['subject']=$email_ticket['subject'];
                    $ticket_data['observation']=$email_ticket['description'];
                    $tickets_data[]=$ticket_data;
                }
                
                if(!empty($email_tickets[0]['department_id'])){
                    $user_id = UserDepartments::getDepartmentHead($email_tickets[0]['hotel_id'], $email_tickets[0]['department_id']);
                    $deptHotelModel = \app\models\HotelDepartments::findOne(['department_id'=>$email_tickets[0]['department_id'],'hotel_id'=>$email_tickets[0]['hotel_id'],'is_deleted'=>0]);
                    if($deptHotelModel && $deptHotelModel->configured_emails){
                         EmailsComponent::sendConsolidateNonComplaintToDepartment(explode(',', $deptHotelModel->configured_emails), $tickets_data, $user_id);
                    }
                }
                
                
                
            }

            $ticketsList = Tickets::find()->joinWith(['assignedUser', 'department', 'hotel'])->where(['audit_schedule_id' => $auditIds, 'status' => 1])->asArray()->all();
            $data = ArrayHelper::index($ticketsList, null, 'assigned_user_id');

            foreach ($data as $tickets) {

                $mailContent = '';
                $hotelName = '';
                $departmentName = '';
                $user = [];
                $ticketNames = [];

                foreach ($tickets as $ticket) {
                    $attributes = $ticket;
                    $attributes['department'] = isset($ticket['department']['department_name']) ? $ticket['department']['department_name'] : '';
                    $attributes['hotel'] = isset($ticket['hotel']['hotel_name']) ? $ticket['hotel']['hotel_name'] : '';
                    $hotelName = $attributes['hotel'];
                    $departmentName = $attributes['department'];
                    $user = $ticket['assignedUser'];
                    $ticketNames[] = $attributes['ticket_name'];
                    $mailContent .= 'Ticket : ' . $attributes['ticket_name'] . '<br>';
                    $mailContent .= 'Subject : ' . $attributes['subject'] . '<br>';
                    $mailContent .= 'Due Date : ' . date('d-m-Y', strtotime($attributes['due_date']));
                    $mailContent .= '<br><br>';
                }
                $content = 'Following Tickets are assigned.<br><br>';
                $content .= '<b>Hotel</b> : ' . $hotelName . '<br>';
                $content .= '<b>Department</b> : ' . $departmentName . '<br><br>';
                $content .= $mailContent;
                $params['recipientMail'] = $user['email'];

                $mailStatus = '';
                $params['subject'] = 'Tickets Assigned.';
                $params['message'] = $this->buildMailContent($user, $content);
                $mailStatus = EmailsComponent::sendMail($params);

                $logData['notification_name'] = implode(',', $ticketNames);
                $logData['user_id'] = $user['user_id'];
                $logData['notification_message'] = $content;
                $logData['notification_type'] = 2;
                $logData['response_status'] = $mailStatus;
                $this->logTable($logData);

            }
            foreach ($ticketsList as $ticket) {
                Tickets::sendNotification($ticket, 'ticketAssigned', false);
            }

            AuditsSchedules::updateAll([
                'notification_status' => 1,
            ], [
                'audit_schedule_id' => $auditIds,
            ]);

            echo 'Messaging Done';
        } catch (Exception $e) {

            $data['notification_name'] = 'Audit Submitted';
            $data['user_id'] = $user['user_id'];
            $data['notification_message'] = $e->getMessage();
            $data['notification_type'] = 1;
            $data['response_status'] = '';
            $this->logTable($data);
        }
    }

    /**
     * @param $user
     * @param $ticket
     * @param $type
     */
    public function validateNotificationSettings($user, $model, $type)
    {
        $response = User::getUserAssingemnts($user['user_id']);
        $userHotels = $response['userHotels'];
        $userDepartments = $response['userdepartments'];
        $status = false;
        $hotel = '';
        $department = '';
        switch ($type) {
            case 'ticket':
                $ticket = Tickets::find()->where(['ticket_name' => $model['ticket_id']])->one();
                if ($ticket) {
                    $hotel = $ticket->hotel_id;
                    $department = $ticket->department_id;
                }
                break;
            case 'audit':
                $audit = AuditsSchedules::find()->joinWith(['audit'])->where(['audit_schedule_name' => $model['audit_id']])->asArray()->one();
                if ($audit && isset($audit['audit'])) {
                    $hotel = $audit['audit']['hotel_id'];
                    $department = $audit['audit']['department_id'];
                }
                break;
        }

        if (in_array($hotel, $userHotels) && in_array($department, $userDepartments)) {
            $status = true;
        }
        return $status;
    }

    /**
     * @param $auditName
     */
    public function addAuditDetailedInformation($auditName)
    {
        $content = "<br>";
        $content .= '<b>Audit Report</b> : <br>';

        $scheduledId = AuditsSchedules::find()->where(['audit_schedule_name' => $auditName])->one();

        $modelAudit = Audits::findOne($scheduledId->audit_id);
        $auditDatesLast = $modelAudit->getAuditCompareDates($scheduledId->audit_id, $scheduledId->audit_schedule_id);

        $auditChildIds = \yii\helpers\ArrayHelper::getColumn($auditDatesLast, 'audit_schedule_id');

        $auditDates = array_reverse($auditDatesLast);
        $audit_count = count($auditDates);
        $headerCount = $audit_count + 4;
        $content .= '<table border = 1 class="table" style="border: 1px solid #ddd" ><tbody><tr><th  colspan=' . $headerCount . ' style="text-align:center"><h4 style="color: orange">' . strtoupper($modelAudit->hotel->hotel_name) . '</h4><h4 style="color: orange">' . strtoupper($modelAudit->checklist->cl_name) . ' </h4></th></tr>';
        $content .= '<tr><th colspan="2" rowspan="2"class="text-center">' . strtoupper($modelAudit->checklist->cl_name) . '</th></tr>';
        $content .= '<tr>';
        foreach ($auditDates as $audits) {
            $content .= '<td  >' . date('M Y', strtotime($audits['start_date'])) . '</td>';
        }
        $content .= '<th rowspan="2">VARIANCE</th><th rowspan="2">% of Increase / Decrease (-/ +)</th>';
        $content .= '</tr>';
        $content .= '<tr style="background-color: #cfe8d0;"><td>S.No</td><td>Sections</td>';
        for ($x = 1; $x <= $audit_count; $x++) {
            $content .= ' <td  >SCORE OBTAINED</td>';
        }
        $content .= '</tr>';
        $scoreArray = array();
        foreach ($auditDates as $audits) {
            $auditData = $modelAudit->getAuditList($modelAudit->audit_id, $audits['end_date'], 2);

            foreach ($auditData as $auditlist) {
                $scoreArray[$auditlist['s_section_name']][] = $auditlist['score'];
            }
        }
        $loopC = 1;
        foreach ($scoreArray as $key => $scores) {
            $content .= '<tr><td  > ' . $loopC . '</td><td>' . $key . '</td>';
            $innerLoopC = 1;
            $scoreCount = count($scores);
            foreach ($scores as $score) {
                if ($score >= 80) {
                } elseif ($score <= 79 && $score >= 61) {
                } else {
                }
                $content .= '<td  >' . $score . '</td>';
                if ($innerLoopC > 1) {
                    $varience = $scores[1] - $scores[0];
                    if ($varience < 0) {
                    }
                    $content .= '<td  >' . $varience . '</td>';
                    if ($scores[0] != 0) {
                        $perVar = ($varience / $scores[0]) * 100;
                    } else {
                        $perVar = $scores[1];
                    }
                    $textClass = '';
                    if ($perVar < 0) {
                        $textClass = 'red';
                    }
                    $content .= '<td  >' . round($perVar, 2) . '%</td>';
                }
                if ($scoreCount == 1) {
                    $content .= '<td  >-</td>';
                    $content .= '<td   >-</td>';
                }
                $innerLoopC++;
            }
            $content .= '</tr>';
            $loopC++;
        }

        $content .= '<tr><td   style="text-align: center" colspan="2"><b>Audit Score</b></td>';
        $innerLoopC = 1;

        $finalScore = [];
        foreach ($auditChildIds as $childId) {
            $finalScore[] = \app\models\AuditsSchedules::getAuditScore($childId);

        }
        $scoreCount = count($finalScore);
        $finalScore = array_reverse($finalScore);
        foreach ($finalScore as $score) {
            $scores = $finalScore;
            if ($score >= 80) {
            } elseif ($score <= 79 && $score >= 61) {
            } else {
            }
            $content .= '<td  >' . $score . '</td>';
            if ($innerLoopC > 1) {
                $varience = $scores[1] - $scores[0];
                if ($varience && $varience < 0) {
                }
                $content .= '<td  >' . $varience . '</td>';
                if ($scores[0] != 0) {
                    $perVar = ($varience / $scores[0]) * 100;
                } else {
                    $perVar = $scores[1];
                }
                if ($perVar && $perVar < 0) {
                }
                $content .= '<td  >' . round($perVar, 2) . '%</td>';
            }

            if ($scoreCount == 1) {
                $content .= '<td  >-</td>';
                $content .= '<td  >-</td>';
            }
            $innerLoopC++;
        }
        $content .= '</tr>';
        $content .= '</tbody></table>';

        return $content;
    }
}

?>