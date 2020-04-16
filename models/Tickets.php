<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\HttpException;

/**
 * This is the model class for table "tbl_gp_tickets".
 *
 * @property integer $ticket_id
 * @property integer $audit_schedule_id
 * @property integer $hotel_id
 * @property integer $department_id
 * @property integer $section_id
 * @property integer $sub_section_id
 * @property integer $priority_type_id
 * @property integer $assigned_user_id
 * @property integer $answer_id
 * @property string $ticket_name
 * @property integer $chronicity
 * @property string $due_date
 * @property string $subject
 * @property string $description
 * @property integer $status
 * @property integer $is_deleted
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Hotels $hotel
 * @property Departments $department
 * @property Sections $section
 * @property QuestionPriorityTypes $priorityType
 * @property User $assignedUser
 * @property User $createdBy
 * @property User $updatedBy
 */
class Tickets extends \yii\db\ActiveRecord
{

    public $dateAssignedType;

    public $startDate;

    public $endDate;

    public $overdue;

    public $audit_id;

    const TICKET_DEFAULT_NUMBER = 1;

    public $overDueTicket;

    public static $statusList = [0 => 'Open', 1 => 'Assigned', 2 => 'Resolved', 3 => 'Closed', 4 => 'Rejected', 5 => 'Cancelled'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_gp_tickets';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'audit_schedule_id',
                    'hotel_id',
                    'department_id',
                    'section_id',
                    'priority_type_id',
                    'assigned_user_id',
                    'answer_id',
                    'chronicity',
                    'process_critical_dynamic',
                    'status',
                    'is_deleted',
                    'created_by',
                    'updated_by',
                    'location_id',
                    //'sub_section_id'
                ],
                'integer'
            ],
            [
                [
                    'hotel_id',
                    'department_id',
                    'section_id',
                    'priority_type_id',
                    'assigned_user_id',
                    'ticket_name',
                    'chronicity',
                    'due_date',
                    'is_deleted',
                    'location_id',
                    'status',
                    'subject'
                ],
                'required'
            ],
            [
                [
                    'due_date',
                    'created_at',
                    'updated_at', 'sub_section_id'
                ],
                'safe'
            ],
            [
                [
                    'description'
                ],
                'string'
            ],
            [
                [
                    'ticket_name'
                ],
                'string',
                'max' => 100
            ],
            [
                [
                    'subject'
                ],
                'string',
                'max' => 500
            ],
            [
                [
                    'hotel_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => Hotels::className(),
                'targetAttribute' => [
                    'hotel_id' => 'hotel_id'
                ]
            ],
            [
                [
                    'department_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => Departments::className(),
                'targetAttribute' => [
                    'department_id' => 'department_id'
                ]
            ],
            [
                [
                    'section_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => Sections::className(),
                'targetAttribute' => [
                    'section_id' => 'section_id'
                ]
            ],
            [
                [
                    'priority_type_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => QuestionPriorityTypes::className(),
                'targetAttribute' => [
                    'priority_type_id' => 'priority_type_id'
                ]
            ],
            [
                [
                    'assigned_user_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => [
                    'assigned_user_id' => 'user_id'
                ]
            ],
            [
                [
                    'created_by'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => [
                    'created_by' => 'user_id'
                ]
            ],
            [
                [
                    'updated_by'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => [
                    'updated_by' => 'user_id'
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ticket_id' => Yii::t('app', 'Ticket ID'),
            'location_id' => Yii::t('app', 'Location'),
            'audit_schedule_id' => Yii::t('app', 'Audit Schedule ID'),
            'hotel_id' => Yii::t('app', 'Hotel'),
            'department_id' => Yii::t('app', 'Department'),
            'section_id' => Yii::t('app', 'Section'),
            'sub_section_id' => Yii::t('app', 'Sub Section'),
            'priority_type_id' => Yii::t('app', 'Priority Type'),
            'assigned_user_id' => Yii::t('app', 'Assigned User'),
            'answer_id' => Yii::t('app', 'Answer'),
            'ticket_name' => Yii::t('app', 'Ticket Name'),
            'chronicity' => Yii::t('app', 'Chronicity'),
            'due_date' => Yii::t('app', 'Due Date'),
            'subject' => Yii::t('app', 'Subject'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'process_critical_dynamic' => Yii::t('app', 'Process Critical (Dynamic)'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At')
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        parent::init();
        // TimestampBehavior also provides a method named touch() that allows you to assign the current timestamp to the specified attribute(s) and save them to the database. For example,
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => date('Y-m-d H:i:s')
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
                'value' => isset(Yii::$app->user) && isset(Yii::$app->user->id) ? Yii::$app->user->id : 1
            ]
        ];
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHotel()
    {
        return $this->hasOne(Hotels::className(), [
            'hotel_id' => 'hotel_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Departments::className(), [
            'department_id' => 'department_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHotelDepartment()
    {
        return $this->hasOne(HotelDepartments::className(), [
            'department_id' => 'department_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSection()
    {
        return $this->hasOne(Sections::className(), [
            'section_id' => 'section_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubSection()
    {
        return $this->hasOne(SubSections::className(), [
            'sub_section_id' => 'sub_section_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPriorityType()
    {
        return $this->hasOne(QuestionPriorityTypes::className(), [
            'priority_type_id' => 'priority_type_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssignedUser()
    {
        return $this->hasOne(User::className(), [
            'user_id' => 'assigned_user_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), [
            'user_id' => 'created_by'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), [
            'user_id' => 'updated_by'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasOne(Locations::className(), [
            'location_id' => 'location_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicketAttachments()
    {
        return $this->hasMany(TicketAttachments::className(), [
            'ticket_id' => 'ticket_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicketComments()
    {
        return $this->hasMany(TicketComments::className(), [
            'ticket_id' => 'ticket_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicketHistories()
    {
        return $this->hasMany(TicketHistory::className(), [
            'ticket_id' => 'ticket_id'
        ]);
    }

    // For get Audit Id and Checklist Id
    public static function getAuditsSchedules($audit_schedule_id)
    {
        return $modelAuditSchedule = AuditsSchedules::find()->joinWith('audit as a')
            ->where([
                'audit_schedule_id' => $audit_schedule_id
            ])
            ->one();
    }

    // For get Answer details
    public static function getAnswers($answer_id)
    {
        return Answers::find()->joinWith('question')
            ->where([
                'answer_id' => $answer_id
            ])
            ->asArray()
            ->one();
    }

    // For Tickets Comments
    public static function getTicketsComments($ticketId)
    {
        return TicketComments::find()->where([
            'ticket_id' => $ticketId
        ])
            //->asArray()
            ->all();
    }

    // For Tickets Attachments
    public static function getTicketsAttachments($ticketId)
    {
        return TicketAttachments::find()->where([
            'ticket_id' => $ticketId
        ])->all();
    }

    // For Ticket Attachment Count
    public static function getTicketsAttachmentsCount($ticketId)
    {
        return TicketAttachments::find()->where([
            'ticket_id' => $ticketId
        ])->count();
    }

    // For Tickets History
    public static function getTicketHistory($ticketId)
    {
        return TicketHistory::find()->where([
            'ticket_id' => $ticketId
        ])->all();
    }

    public static function getgetAuditsSchedulesName($audit_schedule_id)
    {
        return AuditsSchedules::find()->select('audit_schedule_name')
            ->where([
                'audit_schedule_id' => $audit_schedule_id
            ])
            ->one();
    }

    /**
     * getMaxTicketNumber Returns max Ticket number
     *
     * @method getMaxTicketNumber
     * @return [int] Returns max client id
     */
    public static function getMaxTicketNumber()
    {
        $ticketNumber = self::TICKET_DEFAULT_NUMBER;
        $sql = "SELECT MAX(SUBSTR(TRIM(ticket_name),4)) as ticketNumber FROM " . self::tableName();
        $maxClientNumber = Yii::$app->db->createCommand($sql)->queryOne();
        if ($maxClientNumber['ticketNumber'] != null) {
            $ticketNumber = $maxClientNumber['ticketNumber'];
            $ticketNumber++;
        }
        $token = 'TKT';
        $tkt_length = strlen((string)$ticketNumber);
        if ($tkt_length < 5) {

            for ($i = 0; $i < (5 - $tkt_length); $i++) {
                $token .= '0';
            }
        }
        return $ticketNumber = $token . $ticketNumber;
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChecklist()
    {
        $query = new Query();
        $query->select([
            'tbl_gp_checklists.cl_name as name'
        ])
            ->from('tbl_gp_audits')
            ->join('INNER JOIN', 'tbl_gp_audits_schedules', 'tbl_gp_audits_schedules.audit_id = tbl_gp_audits.audit_id')
            ->join('INNER JOIN', 'tbl_gp_checklists', 'tbl_gp_checklists.checklist_id = tbl_gp_audits.checklist_id')
            ->andFilterWhere([
                'tbl_gp_audits_schedules.audit_schedule_id' => $this->audit_schedule_id
            ]);
        $command = $query->createCommand();
        $data = $command->queryOne();

        return $data['name'];
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditorName()
    {
        $query = new Query();
        $query->select([
            'tbl_gp_audits.audit_id',
            'tbl_gp_user.first_name'
        ])
            ->from('tbl_gp_audits')
            ->join('INNER JOIN', 'tbl_gp_audits_schedules', 'tbl_gp_audits_schedules.audit_id = tbl_gp_audits.audit_id')
            ->join('INNER JOIN', 'tbl_gp_user', 'tbl_gp_user.user_id = tbl_gp_audits.user_id')
            ->andFilterWhere([
                'tbl_gp_audits_schedules.audit_schedule_id' => $this->audit_schedule_id
            ]);
        $command = $query->createCommand();

        $data = $command->queryOne();
        return $data['first_name'];
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditId()
    {
        $query = new Query();
        $query->select([
            'tbl_gp_audits.audit_name'
        ])
            ->from('tbl_gp_audits')
            ->join('INNER JOIN', 'tbl_gp_audits_schedules', 'tbl_gp_audits_schedules.audit_id = tbl_gp_audits.audit_id')
            ->andFilterWhere([
                'tbl_gp_audits_schedules.audit_schedule_id' => $this->audit_schedule_id
            ]);
        $command = $query->createCommand();

        $data = $command->queryOne();

        return $data['audit_name'];
    }

    /**
     * Get tickets data by location
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getTicketLocationData()
    {
        $query = new Query();
        $query->select([
            'tbl_gp_cities.name',
            'tbl_gp_tickets.assigned_user_id',
            'count(ticket_id) as ticket_count'
        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_locations', 'tbl_gp_locations.location_id = tbl_gp_tickets.location_id')
            ->join('INNER JOIN', 'tbl_gp_cities', 'tbl_gp_cities.id =tbl_gp_locations.location_city_id');
        $params = Yii::$app->request->queryParams;
        if (isset($params['ticket_auditor_id'])) {
            $query->andFilterWhere(['tbl_gp_tickets.assigned_user_id' => $params['ticket_auditor_id']]);
        }
        if (isset($params['ticket_status'])) {
            $query->andFilterWhere(['tbl_gp_tickets.status' => $params['ticket_status']]);
        }
        if (isset($params['ticket_chronic'])) {
            $query->andFilterWhere(['tbl_gp_tickets.chronicity' => $params['ticket_chronic']]);
        }
        if ((isset($params['ticektsStartDate']) && $params['ticektsStartDate'] != '') && isset($params['ticketsEndDate'])) {
            $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime($params['ticektsStartDate']))])
                ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime($params['ticketsEndDate']))]);
        }
        $query->andFilterWhere(['tbl_gp_tickets.status' => [1, 2, 3, 4]]);

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        if ($userType != 1) {
            $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
            $query->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
        }

        $query->groupBy('tbl_gp_tickets.location_id');
        $command = $query->createCommand();
        $data = $command->queryAll();

        return $data;
    }

    /**
     * Get ticket data by hotel data
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getTicketHotelData()
    {
        $query = new Query();
        $query->select([
            'tbl_gp_hotels.hotel_name',
            'tbl_gp_tickets.assigned_user_id',
            'count(ticket_id) as ticket_count'
        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id = tbl_gp_tickets.hotel_id');
        $params = Yii::$app->request->queryParams;
        if (isset($params['ticket_auditor_id'])) {
            $query->andFilterWhere(['tbl_gp_tickets.assigned_user_id' => $params['ticket_auditor_id']]);
        }
        if (isset($params['ticket_status'])) {
            $query->andFilterWhere(['tbl_gp_tickets.status' => $params['ticket_status']]);
        }
        if (isset($params['ticket_chronic'])) {
            $query->andFilterWhere(['tbl_gp_tickets.chronicity' => $params['ticket_chronic']]);
        }
        if ((isset($params['ticektsStartDate']) && $params['ticektsStartDate'] != '') && isset($params['ticketsEndDate'])) {
            $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime($params['ticektsStartDate']))])
                ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime($params['ticketsEndDate']))]);
        }
        $query->andFilterWhere(['tbl_gp_tickets.status' => [1, 2, 3, 4]]);

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        if ($userType != 1) {
            $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
            $query->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
        }

        $query->groupBy('tbl_gp_tickets.hotel_id');
        $command = $query->createCommand();
        $data = $command->queryAll();

        return $data;
    }

    /**
     * Get ticket list by department data
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getTicketDeptData()
    {
        $time = new \DateTime('now');
        $today = $time->format('Y-m-d');

        $query = new Query();
        $query->select([
            'tbl_gp_departments.department_name',
            'tbl_gp_tickets.assigned_user_id',
            'count(ticket_id) as ticket_count'
        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_tickets.department_id');

        $params = Yii::$app->request->queryParams;
        if (isset($params['ticket_auditor_id'])) {
            $query->andFilterWhere(['tbl_gp_tickets.assigned_user_id' => $params['ticket_auditor_id']]);
        }
        if (isset($params['ticket_status'])) {
            $query->andFilterWhere(['tbl_gp_tickets.status' => $params['ticket_status']]);
        }
        if (isset($params['ticket_chronic'])) {
            $query->andFilterWhere(['tbl_gp_tickets.chronicity' => $params['ticket_chronic']]);
        }
        if ((isset($params['ticektsStartDate']) && $params['ticektsStartDate'] != '') && isset($params['ticketsEndDate'])) {
            $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime($params['ticektsStartDate']))])
                ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime($params['ticketsEndDate']))]);
        }
        $query->andFilterWhere(['tbl_gp_tickets.status' => [1, 2, 3, 4]]);

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        if ($userType != 1) {
            $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
            $query->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
        }

        $query->groupBy('tbl_gp_tickets.department_id');
        // /->andFilterWhere(['<','tbl_gp_audits_schedules.end_date',$today]);
        $command = $query->createCommand();
        $data = $command->queryAll();

        return $data;
    }

    /**
     * Get hotel data by tickets
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getHotelTicketData()
    {
        $query = new Query();
        $query->select([
            'tbl_gp_hotels.hotel_name as category',
            'count(tbl_gp_tickets.hotel_id) as ticket_count'
        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id =tbl_gp_tickets.hotel_id');

        $params = Yii::$app->request->queryParams;
        /* if (isset($params['hotelStartDate']) && isset($params['hotelendDate'])) {
             $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                 ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01-' . $params['hotelendDate']))]);
         }*/


        if ((isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') || (isset($params['hotelStartDate']) && $params['hotelStartDate'] != '')) {

            if ((isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') && (isset($params['hotelendDate']) && $params['hotelendDate'] != '')) {
                $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01-' . $params['hotelendDate']))]);
            } else if (isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') {
                $toDate = explode('-', $params['hotelStartDate']);
                $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', $totDays]);
            } else if (isset($params['hotelendDate']) && $params['hotelendDate'] != '') {
                $toDate = explode('-', $params['hotelendDate']);
                $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', $fromDays])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01' . $params['hotelendDate']))]);
            }
        }

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        if ($userType != 1) {
            $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
            $query->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
        }

        $query->groupBy('tbl_gp_hotels.hotel_id');
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }

    /**
     * Get hotel data by chronical tickets
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getHotelChronicData()
    {
        $query = new Query();
        $query->select([
            'tbl_gp_hotels.hotel_name as category',
            'count(tbl_gp_tickets.hotel_id) as ticket_count'
        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id =tbl_gp_tickets.hotel_id')
            ->andFilterWhere([
                'chronicity' => 1
            ]);
        $params = Yii::$app->request->queryParams;
        /* if (isset($params['hotelStartDate']) && isset($params['hotelendDate'])) {
             $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                 ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01-' . $params['hotelendDate']))]);
         }*/

        if ((isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') || (isset($params['hotelStartDate']) && $params['hotelStartDate'] != '')) {

            if ((isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') && (isset($params['hotelendDate']) && $params['hotelendDate'] != '')) {
                $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01-' . $params['hotelendDate']))]);
            } else if (isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') {
                $toDate = explode('-', $params['hotelStartDate']);
                $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', $totDays]);
            } else if (isset($params['hotelendDate']) && $params['hotelendDate'] != '') {
                $toDate = explode('-', $params['hotelendDate']);
                $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', $fromDays])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01' . $params['hotelendDate']))]);
            }
        }
        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        if ($userType != 1) {
            $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
            $query->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
        }

        $query->groupBy('tbl_gp_hotels.hotel_id');
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }

    /**
     * Get hotel data by overdue tickets
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getHotelOverdueTicketData()
    {
        $time = new \DateTime('now');
        $today = $time->format('Y-m-d');
        $query = new Query();
        $query->select([
            'tbl_gp_hotels.hotel_name as category',
            'count(tbl_gp_tickets.hotel_id) as ticket_count'
        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id =tbl_gp_tickets.hotel_id')
            ->andFilterWhere([
                '<',
                'tbl_gp_tickets.due_date',
                $today
            ])->andFilterWhere([
                'tbl_gp_tickets.status' => [0, 1, 4]
            ]);
        $params = Yii::$app->request->queryParams;
        /* if (isset($params['hotelStartDate']) && isset($params['hotelendDate'])) {
             $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                 ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01-' . $params['hotelendDate']))]);
         }*/

        if ((isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') || (isset($params['hotelStartDate']) && $params['hotelStartDate'] != '')) {

            if ((isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') && (isset($params['hotelendDate']) && $params['hotelendDate'] != '')) {
                $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01-' . $params['hotelendDate']))]);
            } else if (isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') {
                $toDate = explode('-', $params['hotelStartDate']);
                $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', $totDays]);
            } else if (isset($params['hotelendDate']) && $params['hotelendDate'] != '') {
                $toDate = explode('-', $params['hotelendDate']);
                $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', $fromDays])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01' . $params['hotelendDate']))]);
            }
        }

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        if ($userType != 1) {
            $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
            $query->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
        }

        $query->groupBy('tbl_gp_hotels.hotel_id');

        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }

    public static function getUserName($uesrId)
    {
        return User::find()->where([
            'user_id' => $uesrId
        ])->one();
    }

    public static function sendNotifications($ticketId, $type, $ticketsArray = [])
    {
        if ($ticketsArray) {
            foreach ($ticketsArray as $ticket) {
                self::sendNotification($ticket, $type);
            }
        } elseif ($ticketId) {
            $ticket = Tickets::find()
                ->joinWith(['assignedUser', 'department', 'hotel'])
                ->where(['ticket_id' => $ticketId])
                ->asArray()
                ->one();
            self::sendNotification($ticket, $type);
        }
    }

    /**
     * @param $ticket
     */
    public static function sendNotification($ticket, $type, $mailTrue = true)
    {
        $user = $ticket['assignedUser'];
        $notifications = [];
        $notifications['type'] = $type;
        $notifications['toEmail'] = $mailTrue ? $user['email'] : '';
        $notifications['mobileNumber'] = $user['phone'];
        $notifications['deviceToken'] = $user['device_token'];

        $attributes = $ticket;
        $attributes['department'] = isset($ticket['department']['department_name']) ? $ticket['department']['department_name'] : '';
        $attributes['hotel'] = isset($ticket['hotel']['hotel_name']) ? $ticket['hotel']['hotel_name'] : '';
        $notifications['data'] = $attributes;
        $notifications['userId'] = $user['user_id'];
        Yii::$app->scheduler->triggerNotifications($notifications);
    }

    /**
     * @param $status
     * @return array
     */
    public static function getTicketStatus($status)
    {
        $statusList = self::$statusList;
        unset($statusList[0]);
        switch ($status) {
            case '2':
                unset($statusList[1], $statusList[5]);
                break;
            case '0':
                unset($statusList[2], $statusList[3], $statusList[4], $statusList[5]);
            case '4':
                unset($statusList[2], $statusList[1]);
                break;
        }
        return $statusList;
    }

    public static function sendStatusChangeNotification($ticketId, $userType = '')
    {
        $ticket = Tickets::find()
            ->joinWith(['assignedUser', 'department', 'hotel'])
            ->where(['ticket_id' => $ticketId])
            ->asArray()
            ->one();
        $userType = $userType ? $userType : Yii::$app->user->identity->user_type;
        $user = '';
        if ($userType == '2') {// auditor
            if ($ticket['status'] == 4) {
                $user = User::findOne($ticket['assigned_user_id']);
            }
            $type = 'ticketStatusChangedByAuditor';
        } elseif ($userType == '3') { //staff
            $auditId = $ticket['audit_schedule_id'];
            $user_id = '';
            $type = 'ticketStatusChangedByStaff';
            if ($auditId) {
                $scheduleAudit = AuditsSchedules::findOne($auditId);
                if ($scheduleAudit) {
                    $user_id = $scheduleAudit->auditor_id;
                }
            } else {
                $hoteldepartmentModel = HotelDepartments::find()->joinWith('userDepartmentHod as u')
                    ->where([
                        'hotel_id' => $ticket['hotel_id'],
                        'department_id' => $ticket['department_id'],
                        'u.is_hod' => 1
                    ])
                    ->asArray()
                    ->one();
                if ($hoteldepartmentModel['userDepartment']) {
                    $user_id = $hoteldepartmentModel['userDepartment']['user_id'];
                }else{
                    $user_id = Answers::DEFAULT_ASSIGNEE;
		}
            }
            $user = User::findOne($user_id);
        }

        if ($user) {
            $user = $user->attributes;
            $notifications = [];
            $notifications['type'] = $type;
            $notifications['toEmail'] = $user['email'];
            $notifications['mobileNumber'] = $user['phone'];
            $notifications['deviceToken'] = $user['device_token'];

            $attributes = $ticket;
            $attributes['department'] = isset($ticket['department']['department_name']) ? $ticket['department']['department_name'] : '';
            $attributes['hotel'] = isset($ticket['hotel']['hotel_name']) ? $ticket['hotel']['hotel_name'] : '';
            $notifications['data'] = $attributes;
            $notifications['userId'] = $user['user_id'];

            Yii::$app->scheduler->triggerNotifications($notifications);
        }

    }


    public function saveTicket($input_answer)
    {
        // changing the status of the Audit Schedule as draft if new input is received
        if (($auditsScheduleModel = AuditsSchedules::findOne($input_answer['audit_id'])) !== null) {

            $ids = HotelDepartments::find()->joinWith('userDepartment as u')
                ->where([
                    'hotel_id' => $auditsScheduleModel->audit->hotel_id,
                    'department_id' => $auditsScheduleModel->audit->department_id,
                ])
                ->andWhere(['u.is_hod' => 1])
                ->asArray()
                ->all();
            $ids = ArrayHelper::getColumn($ids, 'id');
            $user = UserDepartments::find()->where(['is_hod' => 1, 'hotel_department_id' => $ids])->asArray()->one();

            $user_id = '';
            if ($user) {
                $user_id = $user['user_id'];
            }

            $due_days_count = 0;
            $due_date_pref = Preferences::find()->where(['preferences_name' => 'ticket_medium_priority_due_date'])->one();
            if (!empty($due_date_pref)) {
                $due_days_count = $due_date_pref->preferences_value;
            }

            $ticketModel = new Tickets();
            $ticketModel->location_id = $auditsScheduleModel->audit->location_id;
            $ticketModel->hotel_id = $auditsScheduleModel->audit->hotel_id;
            $ticketModel->department_id = $auditsScheduleModel->audit->department_id;
            $ticketModel->section_id = $input_answer['section_id'];
            $ticketModel->sub_section_id = $input_answer['subsection_id'];
            $ticketModel->priority_type_id = 2;
            $ticketModel->assigned_user_id = ($user_id) ? $user_id : Answers::DEFAULT_ASSIGNEE;
            $ticketModel->answer_id = null;
            //$ticketModel->ticket_name = self::getMaxTicketNumber();
            $ticketModel->ticket_name = "TKT000";
            $ticketModel->chronicity = 0;
            $ticketModel->due_date = date('Y-m-d H:i:s', strtotime("+" . $due_days_count . " days"));
            $ticketModel->subject = $input_answer['subject'] ? $input_answer['subject'] : $input_answer['observation_text'];
            $ticketModel->description = $input_answer['observation_text'];
            $ticketModel->status = 1;
            $ticketModel->is_deleted = 0;

            if ($ticketModel->save()) {
                $ticketModel->ticket_name = $ticketModel->ticket_name . $ticketModel->ticket_id;

                Tickets::updateAll([
                    'ticket_name' => $ticketModel->ticket_name
                ], 'ticket_id=' . $ticketModel->ticket_id);

                if ($input_answer['attachments_count'] != 0 && !empty($input_answer['attachments'])) {
                    foreach ($input_answer['attachments'] as $attachment) {
                        $attachmentSaveStatus = $this->saveAttachment($attachment, $ticketModel);
                        if (!$attachmentSaveStatus['status']) {
                            throw new HttpException(422, $attachmentSaveStatus['message']);
                        }
                    }
                }

                $ticket_id = $ticketModel->ticket_id;
                $ticketHistory = new TicketHistory();
                $ticketHistory->ticket_id = $ticket_id;
                $userName = Tickets::getUserName($user_id);
                $userName = $userName ? ucfirst($userName->first_name) . ' ' . ucfirst($userName->last_name) : '';
                $message = "Ticket created and assigned to " . $userName;
                $ticketHistory->ticket_message = $message;
                if (!$ticketHistory->save()) {
                    return [
                        'status' => false,
                        'message' => Json::encode($ticketHistory->getErrors())

                    ];
                }
                self::sendNotifications($ticket_id, 'ticketAssigned');
                return true;
            } else {
                return Json::encode($ticketModel->getErrors());
            }

        } else {
            throw new HttpException(422, 'Audit schedule not found');
        }
    }

    private function saveAttachment($attachment, $ticketModel)
    {
        $uploadedFile = \yii\web\UploadedFile::getInstanceByName($attachment);

        if ($uploadedFile) {
            $ext = pathinfo($uploadedFile->name, PATHINFO_EXTENSION);
            $file_name = $ticketModel->ticket_name . '_' . $uploadedFile->name;
            $complete_path = \Yii::$app->basePath . Yii::$app->params['attachments_save_url'] . $file_name;
            $path = $file_name;

            if (copy($uploadedFile->tempName, $complete_path)) {
                $ticketAttachmentModel = new TicketAttachments();
                $ticketAttachmentModel->ticket_attachment_id = NULL; // primary key(auto increment id) id
                $ticketAttachmentModel->isNewRecord = true;
                $ticketAttachmentModel->ticket_id = $ticketModel->ticket_id;
                $ticketAttachmentModel->ticket_attachment_path = $path;

                if ($ticketAttachmentModel->save()) {
                    return [
                        'status' => true,
                        'message' => 'Attachment saved successfully'
                    ];
                } else {
                    return [
                        'status' => false,
                        'message' => Json::encode($ticketAttachmentModel->getErrors())
                        // 'message' => 'Trouble saving the attachments. Please try later.'
                    ];
                }
            } else {
                return [
                    'status' => false,
                    'message' => 'Error saving the attachment'
                ];
            }
        } else {
            return [
                'status' => false,
                'message' => 'Attachment not received'
            ];
        }
    }

    /**
     * @param $hotelId
     * @param $departmentId
     */
    public static function getHotelSections($hotelId, $departmentId)
    {
        $sections = [];
        if ($hotelId && $departmentId) {

            $sections = HotelDepartmentSections::find()->joinWith([
                'section' => function ($query) {
                    $query->select([
                        's_section_name',
                        'section_id'
                    ]);
                },
                'department'
            ])
                ->where([
                    HotelDepartmentSections::tableName() . '.department_id' => $departmentId,
                    HotelDepartmentSections::tableName() . '.hotel_id' => $hotelId,
                    HotelDepartmentSections::tableName() . '.is_deleted' => 0
                ])
                ->select([
                    HotelDepartmentSections::tableName() . '.id',
                    HotelDepartmentSections::tableName() . '.section_id',
                    HotelDepartmentSections::tableName() . '.department_id'
                ])
                ->asArray()
                ->all();
        }

        return $sections;

    }

    public static function getHotelSubSections($sectionId, $departmentId, $hotelId)
    {
        $subsections = [];
        if ($sectionId && $departmentId && $hotelId) {
            $subsections = HotelDepartmentSubSections::find()->joinWith([
                'subSection' => function ($query) {
                    $query->select([
                        'ss_subsection_name',
                        'sub_section_id'
                    ]);
                },
                'section'
            ])
                ->where([
                    HotelDepartmentSubSections::tableName() . '.section_id' => $sectionId,
                    HotelDepartmentSubSections::tableName() . '.department_id' => $departmentId,
                    HotelDepartmentSubSections::tableName() . '.hotel_id' => $hotelId,
                    HotelDepartmentSubSections::tableName() . '.is_deleted' => 0
                ])
                ->select([
                    HotelDepartmentSubSections::tableName() . '.id',
                    HotelDepartmentSubSections::tableName() . '.sub_section_id',
                    HotelDepartmentSubSections::tableName() . '.section_id'
                ])
                ->asArray()
                ->all();
        }
        return $subsections;
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getTicketUsers()
    {
        $query = new Query();
        $query->select([
            'tbl_gp_tickets.assigned_user_id',
        ])
            ->from('tbl_gp_tickets');
        $query->andFilterWhere(['tbl_gp_tickets.status' => [1, 2, 3, 4]]);
        $userType = Yii::$app->user->identity->user_type;

        if ($userType != 1) {
            $return = User::getUserAssingemnts();
            $userHotels = $return['userHotels'];
            $userDepartments = $return['userdepartments'];
            $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
            $query->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
        }
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }

}
