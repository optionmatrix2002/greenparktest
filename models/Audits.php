<?php

namespace app\models;

use Yii;
use phpDocumentor\Reflection\Types\This;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\Html;

/**
 * This is the model class for table "tbl_gp_audits".
 *
 * @property integer $audit_id
 * @property string $audit_name
 * @property integer $location_id
 * @property integer $hotel_id
 * @property integer $department_id
 * @property integer $checklist_id
 * @property integer $user_id
 * @property string $start_date
 * @property string $end_date
 * @property integer $deligation_flag
 * @property integer $status
 * @property integer $is_deleted
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Checklists $checklist
 * @property Locations $location
 * @property Hotels $hotel
 * @property Departments $department
 * @property User $user
 * @property User $createdBy
 * @property User $updatedBy
 */
class Audits extends \yii\db\ActiveRecord
{

    public $checklistfrequency;
    public $audit_namesearch;

    public static $statusList = [0 => 'Scheduled', 1 => 'In-Progress', 2 => 'Draft', 3 => 'Completed', 4 => 'Cancelled'];
    public $show_child = 1 ;//1 makes all children visible by time of loading without expand column
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_gp_audits';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'audit_name',
                    'location_id',
                    'hotel_id',
                    'department_id',
                    'checklist_id',
                    'user_id',
                    'start_date',
                    'end_date',
                    'deligation_flag'
                ],
                'required'
            ],
            [
                [
                    'location_id',
                    'hotel_id',
                    'department_id',
                    'checklist_id',
                    'user_id',
                    'deligation_flag',
                    'is_deleted',
                    'created_by',
                    'updated_by',
                    'status'
                ],
                'integer'
            ],
            [
                [
                    'start_date',
                    'end_date',
                    'created_at',
                    'updated_at',
                    'checklistfrequency'
                ],
                'safe'
            ],
            [
                [
                    'audit_name'
                ],
                'string',
                'max' => 100
            ],
            [
                [
                    'checklist_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => Checklists::className(),
                'targetAttribute' => [
                    'checklist_id' => 'checklist_id'
                ]
            ],
            [
                [
                    'location_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => Locations::className(),
                'targetAttribute' => [
                    'location_id' => 'location_id'
                ]
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
                    'user_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => [
                    'user_id' => 'user_id'
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

            // ['start_date', 'check'],
            // ['start_date','validateDates'],

        ];
    }

    public function check()
    {
        if ($this->end_date <= $this->start_date) {
            // die($this->end_date);
            $this->addError('start_date', 'Please give correct Start and End dates');
            // $this->addError('end_date','Please give correct Start and End dates');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'audit_id' => Yii::t('app', 'Audit ID'),
            'audit_name' => Yii::t('app', 'Audit Name'),
            'location_id' => Yii::t('app', 'Location'),
            'hotel_id' => Yii::t('app', 'Hotel '),
            'department_id' => Yii::t('app', 'Department '),
            'checklist_id' => Yii::t('app', 'Checklist '),
            'user_id' => Yii::t('app', 'Auditor '),
            'start_date' => Yii::t('app', 'Start Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'deligation_flag' => Yii::t('app', 'Deligation Flag'),
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
    public function getChecklist()
    {
        return $this->hasOne(Checklists::className(), [
            'checklist_id' => 'checklist_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Locations::className(), [
            'location_id' => 'location_id'
        ]);
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
        return $this->hasMany(HotelDepartments::className(), [
            'department_id' => 'department_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), [
            'user_id' => 'user_id'
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
    public function getAuditsChecklistQuestions()
    {
        return $this->hasMany(AuditsChecklistQuestions::className(), [
            'audit_id' => 'audit_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditsSchedules()
    {
        return $this->hasMany(AuditsSchedules::className(), [
            'audit_id' => 'audit_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScheduleAuditStatus()
    {
        return $this->hasMany(AuditsSchedules::className(), [
            'audit_id' => 'audit_id'
        ]);
    }

    public function getStatus()
    {
        return $this->hasMany(Audits::className(), [
            'status' => 'status'
        ]);
    }

    public static function getAuditDetails($audit_id)
    {
        return Audits::find()->where([
            'audit_id' => $audit_id
        ])->one();
    }

    public static function getAuditQuestionsAndAnswers($audit_schedule_id)
    {
        return AuditsChecklistQuestions::find()->joinWith(['checkListAnswers.answerAttachments'])->where([AuditsChecklistQuestions::tableName() . '.audit_id' => $audit_schedule_id])->asArray()->all();

        /*return Answers::find()->select('q.*,a.*')
            ->alias('a')
            ->where([
                'a.audit_id' => $audit_schedule_id,
            ])
            ->join('LEFT JOIN', 'tbl_gp_audits_checklist_questions q', 'q.audits_checklist_questions_id= a.question_id')
            ->joinWith('answerAttachments')
            ->asArray()
            ->all();*/
    }

    public static function getUserName($userId)
    {
        return User::find()->where([
            'user_id' => $userId
        ])->one();
    }

    /*
     * public function getAuditDates($auditId,$limit){
     * $query = new Query();
     * $query ->select(['tbl_gp_audits_schedules.end_date'])
     * ->from('tbl_gp_audits')
     * ->join( 'INNER JOIN',
     * 'tbl_gp_audits_schedules',
     * 'tbl_gp_audits_schedules.audit_id = tbl_gp_audits.audit_id'
     * )
     * ->andFilterWhere(['tbl_gp_audits_schedules.audit_id' => $auditId])
     * ->andFilterWhere(['tbl_gp_audits_schedules.status' => 3]);
     * if($limit == 2){
     * $query->orderBy(['tbl_gp_audits_schedules.end_date'=>SORT_ASC])
     * ->limit(2);
     * }
     * $command = $query->createCommand();
     *
     * $data = $command->queryAll();
     * return $data;
     * }
     */
    public function getAuditDates($auditId)
    {
        $query = new Query();
        $query->select([
            'tbl_gp_audits_schedules.end_date',
            'tbl_gp_audits_schedules.audit_schedule_name',
            'tbl_gp_audits_schedules.updated_at',
            'tbl_gp_audits_schedules.start_date',
            'tbl_gp_audits_schedules.audit_schedule_id',
        ])
            ->from('tbl_gp_audits')
            ->join('INNER JOIN', 'tbl_gp_audits_schedules', 'tbl_gp_audits_schedules.audit_id = tbl_gp_audits.audit_id')
            ->andFilterWhere([
                'tbl_gp_audits_schedules.audit_id' => $auditId
            ])
            ->andFilterWhere([
                'tbl_gp_audits_schedules.status' => 3
            ])
            ->andFilterWhere([
                'tbl_gp_audits_schedules.is_deleted' => 0
            ]);
        $command = $query->createCommand();

        $data = $command->queryAll();
        return $data;
    }

    public function getAuditCompareDates($auditId, $auditSchId)
    {
        /*$query = new Query();
        $query->select([
            'tbl_gp_audits_schedules.end_date',
            'tbl_gp_audits_schedules.updated_at',
            'tbl_gp_audits_schedules.start_date',
            'tbl_gp_audits_schedules.audit_schedule_id',
        ])
            ->from('tbl_gp_audits')
            ->join('INNER JOIN', 'tbl_gp_audits_schedules', 'tbl_gp_audits_schedules.audit_id = tbl_gp_audits.audit_id')
            ->andFilterWhere([
                'tbl_gp_audits_schedules.audit_id' => $auditId
            ])
            ->andFilterWhere([
                'tbl_gp_audits_schedules.status' => 3
            ])
            ->andFilterWhere([
                '<=',
                'tbl_gp_audits_schedules.audit_schedule_id',
                $auditSchId
            ]);
        $query->orderBy([
            'tbl_gp_audits_schedules.audit_schedule_id' => SORT_DESC
        ])->limit(2);

        $command = $query->createCommand();

        $data = $command->queryAll();*/

        $currentAudit = AuditsSchedules::findOne($auditSchId);
        $query = new Query();
        $query->select([
            'tbl_gp_audits_schedules.end_date',
            'tbl_gp_audits_schedules.updated_at',
            'tbl_gp_audits_schedules.start_date',
            'tbl_gp_audits_schedules.audit_schedule_id',
        ])
            ->from('tbl_gp_audits')
            ->join('INNER JOIN', 'tbl_gp_audits_schedules', 'tbl_gp_audits_schedules.audit_id = tbl_gp_audits.audit_id')
            ->andFilterWhere([
                'tbl_gp_audits_schedules.audit_id' => $auditId
            ])
            ->andFilterWhere([
                'tbl_gp_audits_schedules.status' => 3
            ])
            ->andFilterWhere([
                'tbl_gp_audits_schedules.is_deleted' => 0
            ])
            ->andFilterWhere([
                '<=',
                'tbl_gp_audits_schedules.updated_at',
                $currentAudit->updated_at
            ]);
        $query->orderBy([
            'tbl_gp_audits_schedules.updated_at' => SORT_DESC
        ])->limit(2);

        $command = $query->createCommand();

        $data = $command->queryAll();
        return $data;
    }

    public static function getAuditList($auditId, $end_date = null, $limit = '', $column = 'audit_id')
    {
        $query = new Query();
        $query->select([
            'tbl_gp_audits_schedules.audit_schedule_id',
            'tbl_gp_audits_schedules.end_date as end_date',
            'tbl_gp_sections.s_section_name',
            'round(SUM(tbl_gp_answers.answer_score) / (COUNT(tbl_gp_answers.answer_value) * 10) * 100) as score'

        ])
            ->from('tbl_gp_audits_schedules')
            ->join('INNER JOIN', 'tbl_gp_audits_checklist_questions', 'tbl_gp_audits_checklist_questions.audit_id = tbl_gp_audits_schedules.audit_schedule_id')
            ->join('INNER JOIN', 'tbl_gp_answers', 'tbl_gp_answers.question_id = tbl_gp_audits_checklist_questions.audits_checklist_questions_id')
            ->join('INNER JOIN', 'tbl_gp_sections', 'tbl_gp_sections.section_id = tbl_gp_audits_checklist_questions.q_section')
            ->andFilterWhere([
                'tbl_gp_audits_schedules.' . $column => $auditId
            ]);
        if ($end_date) {
            $query->andFilterWhere([
                'tbl_gp_audits_schedules.end_date' => $end_date
            ]);
        }

        $query->andFilterWhere([
            'tbl_gp_answers.not_applicable' => 0
        ])->andFilterWhere([
            'tbl_gp_audits_schedules.is_deleted' => 0
        ])
            ->groupBy([
                'tbl_gp_audits_checklist_questions.q_section',
                'tbl_gp_audits_schedules.end_date'
            ])
            ->orderBy([
                'tbl_gp_audits_checklist_questions.audits_checklist_questions_id' => SORT_ASC
            ]);
        $command = $query->createCommand();

        $data = $command->queryAll();

        return $data;
    }

    /**
     * Get audit count list
     *
     * @param
     *            $type
     * @return array
     */
    public function getAuditCount()
    {
        $auditCountList = array();

        $time = new \DateTime('now');
        $today = $time->format('Y-m-d');
        $auditCountList['overdue'] = '';

        $params = Yii::$app->request->queryParams;

        $return = User::getUserAssingemnts();

        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;


        /***** All audits data ********/
        $auditCountList['all'] = '';
        $queryT = AuditsSchedules::find()->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id = tbl_gp_audits_schedules.audit_id');


        if (isset($params['statistics_hotel_id']) && $params['statistics_hotel_id']) {
            $queryT->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['statistics_hotel_id']]);
        }
        if (isset($params['statistics_department_id']) && $params['statistics_department_id']) {
            $queryT->andFilterWhere(['tbl_gp_audits.department_id' => $params['statistics_department_id']]);
        }
        if (isset($params['statisticsStartDate']) && $params['statisticsStartDate'] && isset($params['statisticsEndDate']) && $params['statisticsEndDate']) {
            $queryT->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($params['statisticsStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime($params['statisticsEndDate']))]);
        }
        $queryT->andFilterWhere(['tbl_gp_audits_schedules.status' => 0]);
        $queryT->andFilterWhere(['tbl_gp_audits_schedules.is_deleted' => 0]);

        if ($userType != 1) {
            $queryT->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
            $queryT->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
        }


        $auditCountList['all'] = $queryT->count();
        /***** All audits data ********/

        /***** Overdue audits data ********/

        $queryO = AuditsSchedules::find()
            ->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id = tbl_gp_audits_schedules.audit_id')
            ->where([
                '<',
                'tbl_gp_audits_schedules.end_date',
                $today
            ]);
        if (isset($params['statistics_hotel_id']) && $params['statistics_hotel_id']) {
            $queryO->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['statistics_hotel_id']]);
        }
        if (isset($params['statistics_department_id']) && $params['statistics_department_id']) {
            $queryO->andFilterWhere(['tbl_gp_audits.department_id' => $params['statistics_department_id']]);
        }
        if (isset($params['statisticsStartDate']) && $params['statisticsStartDate'] && isset($params['statisticsEndDate']) && $params['statisticsEndDate']) {
            $queryO->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($params['statisticsStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime($params['statisticsEndDate']))]);
        }
        $queryO->andFilterWhere(['tbl_gp_audits_schedules.status' => [0, 1]]);
        $queryO->andFilterWhere(['tbl_gp_audits_schedules.is_deleted' => 0]);

        if ($userType != 1) {
            $queryO->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
            $queryO->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
        }
        $auditCountList['overdue'] = $queryO->count();


        /***** Overdue audits data ********/

        /***** active audits data ********/
        $auditCountList['active'] = '';
        $queryA = AuditsSchedules::find()
            ->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id = tbl_gp_audits_schedules.audit_id')
            ->where([
                'in',
                'tbl_gp_audits_schedules.status',
                [1, 2]
            ]);
        if (isset($params['statistics_hotel_id']) && $params['statistics_hotel_id']) {
            $queryA->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['statistics_hotel_id']]);
        }
        if (isset($params['statistics_department_id']) && $params['statistics_department_id']) {
            $queryA->andFilterWhere(['tbl_gp_audits.department_id' => $params['statistics_department_id']]);
        }
        if (isset($params['statisticsStartDate']) && $params['statisticsStartDate'] && isset($params['statisticsEndDate']) && $params['statisticsEndDate']) {
            $queryA->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($params['statisticsStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime($params['statisticsEndDate']))]);
        }
        if ($userType != 1) {
            $queryA->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
            $queryA->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
        }
        $queryA->andWhere(['tbl_gp_audits_schedules.is_deleted' => 0]);
        $auditCountList['active'] = $queryA->count();
        /***** active audits data ********/

        /***** completed audits data ********/
        $auditCountList['completed'] = '';
        $queryC = AuditsSchedules::find()
            ->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id = tbl_gp_audits_schedules.audit_id')
            ->where([
                '=',
                'tbl_gp_audits_schedules.status',
                3
            ]);
        if (isset($params['statistics_hotel_id']) && $params['statistics_hotel_id']) {
            $queryC->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['statistics_hotel_id']]);
        }
        if (isset($params['statistics_department_id']) && $params['statistics_department_id']) {
            $queryC->andFilterWhere(['tbl_gp_audits.department_id' => $params['statistics_department_id']]);
        }
        if (isset($params['statisticsStartDate']) && $params['statisticsStartDate'] && isset($params['statisticsEndDate']) && $params['statisticsEndDate']) {
            $queryC->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($params['statisticsStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime($params['statisticsEndDate']))]);
        }
        if ($userType != 1) {
            $queryC->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
            $queryC->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
        }
        $queryC->andWhere(['tbl_gp_audits_schedules.is_deleted' => 0]);
        $auditCountList['completed'] = $queryC->count();
        /***** completed audits data ********/

        /***** Chronic audits data ********/
        $auditCountList['chronic'] = '';
        $queryChr = Tickets::find()->where([
            '=',
            'chronicity',
            1
        ]);
        if (isset($params['statistics_hotel_id']) && $params['statistics_hotel_id']) {
            $queryChr->andFilterWhere(['hotel_id' => $params['statistics_hotel_id']]);
        }
        if (isset($params['statistics_department_id']) && $params['statistics_department_id']) {
            $queryChr->andFilterWhere(['department_id' => $params['statistics_department_id']]);
        }
        if (isset($params['statisticsStartDate']) && $params['statisticsStartDate'] && isset($params['statisticsEndDate']) && $params['statisticsEndDate']) {
            $queryChr->andFilterWhere(['between', 'due_date', date('Y-m-d', strtotime($params['statisticsStartDate'])), date('Y-m-d', strtotime($params['statisticsEndDate']))]);
        }
        $queryChr->andFilterWhere(['!=', 'status', '3']);

        if ($userType != 1) {
            $queryChr->andFilterWhere(['hotel_id' => $userHotels]);
            $queryChr->andFilterWhere(['department_id' => $userDepartments]);
        }

        $auditCountList['chronic'] = $queryChr->count();
        /***** Chronic audits data ********/

        /***** complience audits data ********/
        $auditCountList['compliance'] = '';
        $query = new Query();
        $query->select([
            'tbl_gp_audits_schedules.audit_id', 'round(SUM(tbl_gp_answers.answer_score) /(COUNT(tbl_gp_answers.answer_value) * 10) * 100) as score'
        ])
            ->from('tbl_gp_audits_schedules')
            ->join('INNER JOIN', 'tbl_gp_audits_checklist_questions', 'tbl_gp_audits_checklist_questions.audit_id = tbl_gp_audits_schedules.audit_schedule_id')
            ->join('INNER JOIN', 'tbl_gp_answers', 'tbl_gp_answers.question_id = tbl_gp_audits_checklist_questions.audits_checklist_questions_id')
            ->join('INNER JOIN', 'tbl_gp_sections', 'tbl_gp_sections.section_id = tbl_gp_audits_checklist_questions.q_section')
            ->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id = tbl_gp_audits_schedules.audit_id')
            ->andFilterWhere(['tbl_gp_audits_schedules.status' => 3])
            ->andFilterWhere([
                'tbl_gp_answers.not_applicable' => 0
            ]);
        if (isset($params['statistics_hotel_id']) && $params['statistics_hotel_id']) {
            $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['statistics_hotel_id']]);
        }
        if (isset($params['statistics_department_id']) && $params['statistics_department_id']) {
            $query->andFilterWhere(['tbl_gp_audits.department_id' => $params['statistics_department_id']]);
        }
        if (isset($params['statisticsStartDate']) && $params['statisticsStartDate'] && isset($params['statisticsEndDate']) && $params['statisticsEndDate']) {
            $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($params['statisticsStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime($params['statisticsEndDate']))]);
        }
        $query->groupBy([
            'tbl_gp_audits_schedules.audit_id'
        ]);
        if ($userType != 1) {
            $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
            $query->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
        }
        $query->andFilterWhere(['tbl_gp_audits_schedules.is_deleted' => 0]);
        $command = $query->createCommand();


        $auditComp = $command->queryAll();
        $finalArray = array_column($auditComp, 'score');

        if (count($finalArray)) {
            $auditCountList['compliance'] = round(array_sum($finalArray) / count($finalArray), 2);
        } else {
            $auditCountList['compliance'] = round(array_sum($finalArray), 2);
        }
        /***** Compliance audits data ********/

        return $auditCountList;
    }

    /**
     * get list of overdue audits
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getOverdueAudits()
    {
        $time = new \DateTime('now');
        $today = $time->format('Y-m-d');
        $params = Yii::$app->request->queryParams;
        $query = new Query();

        $query->select([
            'tbl_gp_audits_schedules.audit_schedule_name',
            'tbl_gp_audits_schedules.end_date',
            'tbl_gp_audits_schedules.status',
            'tbl_gp_cities.name',
            'tbl_gp_hotels.hotel_name',
            'tbl_gp_checklists.cl_name as checklist',
            'tbl_gp_user.first_name as auditor_name',
            'tbl_gp_user.last_name as auditor_lname'
        ])
            ->from('tbl_gp_audits_schedules')
            ->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id =tbl_gp_audits_schedules.audit_id')
            ->join('INNER JOIN', 'tbl_gp_locations', 'tbl_gp_locations.location_id = tbl_gp_audits.location_id')
            ->join('INNER JOIN', 'tbl_gp_cities', 'tbl_gp_cities.id =tbl_gp_locations.location_city_id')
            ->join('INNER JOIN', 'tbl_gp_checklists', 'tbl_gp_checklists.checklist_id =tbl_gp_audits.checklist_id')
            ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id = tbl_gp_audits.hotel_id')
            ->join('INNER JOIN', 'tbl_gp_user', 'tbl_gp_user.user_id =tbl_gp_audits_schedules.auditor_id')
            ->andFilterWhere([
                'tbl_gp_audits_schedules.status' => [0, 1, 2]
            ])
            ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', $today]);

        if (isset($params['statistics_hotel_id']) && $params['statistics_hotel_id']) {
            $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['statistics_hotel_id']]);
        }
        if (isset($params['statistics_department_id']) && $params['statistics_department_id']) {
            $query->andFilterWhere(['tbl_gp_audits.department_id' => $params['statistics_department_id']]);
        }
        if (isset($params['statisticsStartDate']) && $params['statisticsStartDate'] && isset($params['statisticsEndDate']) && $params['statisticsEndDate']) {
            $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($params['statisticsStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime($params['statisticsEndDate']))]);
        }

        $return = User::getUserAssingemnts();

        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        if ($userType != 1) {
            $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
            $query->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
        }
        $query->andFilterWhere(['tbl_gp_audits_schedules.is_deleted' => 0]);
//        $query->limit(5);
        $query->orderBy('tbl_gp_audits_schedules.end_date ASC');
        $command = $query->createCommand();
        $data = $command->queryAll();

        return $data;
    }

    /**
     * get list of upcoming audits
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getUpcomingAudits()
    {
        $time = new \DateTime('now');
        $today = $time->format('Y-m-d');
        $params = Yii::$app->request->queryParams;
        $query = new Query();
        $query->select([
            'tbl_gp_audits_schedules.audit_schedule_name',
            'tbl_gp_audits_schedules.start_date as end_date',
            'tbl_gp_audits_schedules.status',
            'tbl_gp_cities.name',
            'tbl_gp_hotels.hotel_name',
            'tbl_gp_checklists.cl_name as checklist',
            'tbl_gp_user.first_name as auditor_name',
            'tbl_gp_user.last_name as auditor_lname'
        ])
            ->from('tbl_gp_audits_schedules')
            ->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id =tbl_gp_audits_schedules.audit_id')
            ->join('INNER JOIN', 'tbl_gp_locations', 'tbl_gp_locations.location_id =tbl_gp_audits.location_id')
            ->join('INNER JOIN', 'tbl_gp_cities', 'tbl_gp_cities.id =tbl_gp_locations.location_city_id')
            ->join('INNER JOIN', 'tbl_gp_checklists', 'tbl_gp_checklists.checklist_id =tbl_gp_audits.checklist_id')
            ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id = tbl_gp_audits.hotel_id')
            ->join('INNER JOIN', 'tbl_gp_user', 'tbl_gp_user.user_id =tbl_gp_audits_schedules.auditor_id')
            ->andFilterWhere([
                '>=',
                'tbl_gp_audits_schedules.end_date',
                $today
            ])
            ->andFilterWhere([
                '=',
                'tbl_gp_audits_schedules.status',
                0
            ])
            ->andFilterWhere([
                '=',
                'tbl_gp_audits_schedules.is_deleted',
                0
            ])
            ->limit(5);

        if (isset($params['statistics_hotel_id']) && $params['statistics_hotel_id']) {
            $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['statistics_hotel_id']]);
        }
        if (isset($params['statistics_department_id']) && $params['statistics_department_id']) {
            $query->andFilterWhere(['tbl_gp_audits.department_id' => $params['statistics_department_id']]);
        }
        if (isset($params['statisticsStartDate']) && $params['statisticsStartDate'] && isset($params['statisticsEndDate']) && $params['statisticsEndDate']) {
            $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($params['statisticsStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime($params['statisticsEndDate']))]);
        }

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        if ($userType != 1) {
            $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
            $query->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
        }

        $query->orderBy('tbl_gp_audits_schedules.start_date ASC');
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }

    /**
     * Get audit count list based hotels
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getHotelAuditData($filters = null)
    {

        $query = new Query();
        $query->select([
            'tbl_gp_hotels.hotel_name as category',
            'count(tbl_gp_audits_schedules.audit_id) as audit_count'
        ])
            ->from('tbl_gp_audits_schedules')
            ->join('RIGHT JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id =tbl_gp_audits_schedules.audit_id')
            ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id =tbl_gp_audits.hotel_id');

        $params = Yii::$app->request->queryParams;
        /*if (isset($params['hotelStartDate']) && isset($params['hotelendDate'])) {
            $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-t', strtotime('01-' . $params['hotelendDate']))]);
   }*/


        if ((isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') || (isset($params['hotelStartDate']) && $params['hotelStartDate'] != '')) {

            if ((isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') && (isset($params['hotelendDate']) && $params['hotelendDate'] != '')) {
                $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime('01-' . $params['hotelendDate']))]);
            } else if (isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') {
                $toDate = explode('-', $params['hotelStartDate']);
                $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', $totDays]);
            } else if (isset($params['hotelendDate']) && $params['hotelendDate'] != '') {
                $toDate = explode('-', $params['hotelendDate']);
                $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', $fromDays])
                    ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime('01' . $params['hotelendDate']))]);
            }
        }

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        if ($userType != 1) {
            $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
            $query->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
        }
        $query->andFilterWhere(['tbl_gp_audits_schedules.status' => 3]);
        $query->andFilterWhere(['tbl_gp_audits_schedules.is_deleted' => 0]);
        $query->groupBy('tbl_gp_hotels.hotel_id');


        $command = $query->createCommand();
        ///echo $query->createCommand()->sql; exit;
        $data = $command->queryAll();
        return $data;
    }

    /**
     * Get audit avgerage list based hotels
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getHotelAverageAuditData()
    {
        $auditCountList = AuditsSchedules::find()->count();

        $params = Yii::$app->request->queryParams;

        $query5 = new Query();
        $query5->select([
            'tbl_gp_hotels.hotel_name as category', 'tbl_gp_audits_schedules.audit_id', 'round(SUM(tbl_gp_answers.answer_score) /(COUNT(tbl_gp_answers.answer_value)* 10) * 100) as percentage'
        ])
            ->from('tbl_gp_audits_schedules')
            ->join('INNER JOIN', 'tbl_gp_audits_checklist_questions', 'tbl_gp_audits_checklist_questions.audit_id = tbl_gp_audits_schedules.audit_schedule_id')
            ->join('INNER JOIN', 'tbl_gp_answers', 'tbl_gp_answers.question_id = tbl_gp_audits_checklist_questions.audits_checklist_questions_id')
            ->join('INNER JOIN', 'tbl_gp_sections', 'tbl_gp_sections.section_id = tbl_gp_audits_checklist_questions.q_section')
            ->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id = tbl_gp_audits_schedules.audit_id')
            ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id =tbl_gp_audits.hotel_id')
            ->andFilterWhere(['tbl_gp_audits_schedules.status' => 3])
            ->andFilterWhere(['tbl_gp_answers.not_applicable' => 0]);

        if (isset($params['hotelStartDate']) && $params['hotelStartDate'] && isset($params['hotelendDate']) && $params['hotelendDate']) {
            $query5->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-t', strtotime('01-' . $params['hotelendDate']))]);
        }

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        if ($userType != 1) {
            $query5->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
            $query5->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
        }
        $query5->andFilterWhere(['tbl_gp_audits_schedules.is_deleted' => 0]);
        $query5->groupBy([
            'tbl_gp_hotels.hotel_id'
        ]);

        $command1 = $query5->createCommand();

        /* $query = new Query();
         // /$query ->select(['tbl_gp_hotels.hotel_name as category' ,'concat(round(( count(tbl_gp_audits_schedules.audit_id)/'.$auditCountList.' * 100 ),2),\'%\') as percentage','count(tbl_gp_audits_schedules.audit_id) as audit_count'])
         $query->select([
             'tbl_gp_hotels.hotel_name as category',
             'round(( count(tbl_gp_audits_schedules.audit_id)/' . $auditCountList . ' * 100 )) as percentage'
         ])
             ->from('tbl_gp_audits_schedules')
             ->join('RIGHT JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id =tbl_gp_audits_schedules.audit_id')
             ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id =tbl_gp_audits.hotel_id');

         if (isset($params['hotelStartDate']) && $params['hotelStartDate'] && isset($params['hotelendDate']) && $params['hotelendDate']) {
             $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                 ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-t', strtotime('01-' . $params['hotelendDate']))]);
         }
         $query->groupBy('tbl_gp_hotels.hotel_id');
         $command = $query->createCommand();*/

        $data = $command1->queryAll();

        return $data;
    }

    public function getAuditHotels()
    {
        $params = Yii::$app->request->queryParams;

        $query5 = new Query();
        $query5->select([
            'tbl_gp_audits.hotel_id'
        ])
            ->from('tbl_gp_audits_schedules')
            ->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id = tbl_gp_audits_schedules.audit_id')
            ->andFilterWhere(['tbl_gp_audits_schedules.status' => 3]);

        if (isset($params['hotelStartDate']) && $params['hotelStartDate'] && isset($params['hotelendDate']) && $params['hotelendDate']) {
            $query5->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime('01-' . $params['hotelendDate']))]);
        }

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        if ($userType != 1) {
            $query5->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
            $query5->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
        }
        $query5->groupBy(['tbl_gp_audits.hotel_id']);

        $command1 = $query5->createCommand();
        $data = $command1->queryAll();
        $hotels = ArrayHelper::getColumn($data, 'hotel_id');
        return $hotels;
    }

    public function getHotelAverageAuditDataDepartment()
    {

        $hotels = $this->getAuditHotels();

        $data = array();
        $params = Yii::$app->request->queryParams;

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        $hotelNames = ArrayHelper::map(Hotels::find()->where(['hotel_id' => $hotels])->asArray()->all(), 'hotel_id', 'hotel_name');

        foreach ($hotels as $hotelId) {
            $query5 = new Query();
            $query5->select([
                'tbl_gp_departments.department_name as category', 'tbl_gp_audits_schedules.audit_id', 'round(SUM(tbl_gp_answers.answer_score) /(COUNT(tbl_gp_answers.answer_value)* 10) * 100) as percentage'
            ])
                ->from('tbl_gp_audits_schedules')
                ->join('INNER JOIN', 'tbl_gp_audits_checklist_questions', 'tbl_gp_audits_checklist_questions.audit_id = tbl_gp_audits_schedules.audit_schedule_id')
                ->join('INNER JOIN', 'tbl_gp_answers', 'tbl_gp_answers.question_id = tbl_gp_audits_checklist_questions.audits_checklist_questions_id')
                ->join('INNER JOIN', 'tbl_gp_sections', 'tbl_gp_sections.section_id = tbl_gp_audits_checklist_questions.q_section')
                ->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id = tbl_gp_audits_schedules.audit_id')
                ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id =tbl_gp_audits.hotel_id')
                ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id =tbl_gp_audits.department_id')
                ->andFilterWhere(['tbl_gp_audits_schedules.status' => 3])
                ->andFilterWhere(['tbl_gp_audits_schedules.is_deleted' => 0])
                ->andFilterWhere([
                    '=',
                    'tbl_gp_audits.hotel_id',
                    $hotelId
                ])
                ->andFilterWhere(['tbl_gp_answers.not_applicable' => 0]);


            if ((isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') || (isset($params['hotelStartDate']) && $params['hotelStartDate'] != '')) {

                if ((isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') && (isset($params['hotelendDate']) && $params['hotelendDate'] != '')) {
                    $query5->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                        ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime('01-' . $params['hotelendDate']))]);
                } else if (isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') {
                    $toDate = explode('-', $params['hotelStartDate']);
                    $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                    $query5->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                        ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', $totDays]);
                } else if (isset($params['hotelendDate']) && $params['hotelendDate'] != '') {
                    $toDate = explode('-', $params['hotelendDate']);
                    $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                    $query5->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', $fromDays])
                        ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime('01' . $params['hotelendDate']))]);
                }
            } else {
                /* $query5->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime(date('Y') . '-01-01'))])
                     ->andFilterWhere(['<', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime(date('Y') . '-12-31'))]);*/
            }

            if ($userType != 1) {
                $query5->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
                $query5->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
            }
            $query5->groupBy('tbl_gp_departments.department_id');
            $command = $query5->createCommand();
            $data[$hotelNames[$hotelId]] = $command->queryAll();
        }

        $newArray = array();
        $data = array_filter($data);

        foreach ($data as $key => $list) {
            $newArray[$key]['year'] = $key;
            foreach ($list as $dataF) {
                $newArray[$key][$dataF['category']] = $dataF['percentage'];
            }
        }


        $query2 = new Query();
        $query2->select([
            'tbl_gp_departments.department_name as category', 'tbl_gp_audits_schedules.audit_id', 'round(SUM(tbl_gp_answers.answer_score) /(COUNT(tbl_gp_answers.answer_value)* 10) * 100) as percentage'
        ])
            ->from('tbl_gp_audits_schedules')
            ->join('INNER JOIN', 'tbl_gp_audits_checklist_questions', 'tbl_gp_audits_checklist_questions.audit_id = tbl_gp_audits_schedules.audit_schedule_id')
            ->join('INNER JOIN', 'tbl_gp_answers', 'tbl_gp_answers.question_id = tbl_gp_audits_checklist_questions.audits_checklist_questions_id')
            ->join('INNER JOIN', 'tbl_gp_sections', 'tbl_gp_sections.section_id = tbl_gp_audits_checklist_questions.q_section')
            ->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id = tbl_gp_audits_schedules.audit_id')
            ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id =tbl_gp_audits.hotel_id')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id =tbl_gp_audits.department_id')
            ->andFilterWhere(['tbl_gp_audits_schedules.status' => 3])
            ->andFilterWhere(['tbl_gp_audits_schedules.is_deleted' => 3])
            ->andFilterWhere(['tbl_gp_answers.not_applicable' => 0]);

        if (isset($params['department_hotel_id'])) {
            $query2->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['department_hotel_id']]);
        }

        if ((isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') || (isset($params['hotelendDate']) && $params['hotelendDate'] != '')) {

            if ((isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') && (isset($params['hotelendDate']) && $params['hotelendDate'] != '')) {
                $query2->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime('01-' . $params['hotelendDate']))]);
            } else if (isset($params['hotelStartDate']) && $params['hotelStartDate'] != '') {
                $toDate = explode('-', $params['hotelStartDate']);
                $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                $query2->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['hotelStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', $totDays]);
            } else if (isset($params['hotelendDate']) && $params['hotelendDate'] != '') {
                $toDate = explode('-', $params['hotelendDate']);
                $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                $query2->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', $fromDays])
                    ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime('01' . $params['hotelendDate']))]);
            }
        } else {
            /*$query2->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime(date('Y') . '-01-01'))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime(date('Y') . '-12-31'))]);*/
        }
        if ($userType != 1) {
            $query2->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
            $query2->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
        }
        $query2->groupBy('tbl_gp_departments.department_id');


        $command = $query2->createCommand();
        $ballonData = $command->queryAll();

        $ballonArray = [];
        foreach ($ballonData as $data) {

            $ballonArray[$data['category']] = array(
                'balloonText' => $data['category'] . ' in [[category]] : [[value]]',
                'bullet' => "round",
                // / 'hidden'=>"true",
                'title' => $data['category'],
                'valueField' => $data['category'],
                'fillAlphas' => "0",
                "labelText" => "[[value]]",
                "labelPosition" => "bottom"

            );
        }

        $result = array();
        $result['data'] = $newArray ? array_values($newArray) : [];
        $result['ballon'] = $ballonArray ? array_values($ballonArray) : [];

        return $result;

    }

    /*
     *
     * For getAuditCount depends department and hotel Id
     */
    public static function getAuditsDepartmentHotelCount($hotel_id, $department_id, $subSectionId = '')
    {
        $query = self::find()
            ->joinWith(['hotelDepartment', 'checklist.questions'])
            ->where([self::tableName() . '.hotel_id' => $hotel_id])
            ->andWhere([HotelDepartments::tableName() . '.department_id' => $department_id])
            ->andWhere(['status' => [0, 1, 2]]);
        if ($subSectionId) {
            $query->andWhere([Questions::tableName() . '.q_sub_section' => $subSectionId]);
        }
        return $query->count();
    }

    /**
     * @param $auditId
     * @param $audit_sch_id
     * @return string
     * @throws \yii\db\Exception
     */
    public static function getAuditReportAcrossSection($audit_sch_id, $auditName)
    {

        $query = new Query();
        $query->select([
            'tbl_gp_audits_checklist_questions.*', 'tbl_gp_answers.not_applicable', 'tbl_gp_answers.answer_value', 'tbl_gp_questions.q_text'
        ])
            ->from('tbl_gp_audits_checklist_questions')
            ->join('INNER JOIN', 'tbl_gp_audits_schedules', 'tbl_gp_audits_schedules.audit_schedule_id = tbl_gp_audits_checklist_questions.audit_id')
            ->join('INNER JOIN', 'tbl_gp_checklists', 'tbl_gp_checklists.checklist_id = tbl_gp_audits_checklist_questions.checklist_id')
            ->join('INNER JOIN', 'tbl_gp_answers', 'tbl_gp_answers.question_id = tbl_gp_audits_checklist_questions.audits_checklist_questions_id')
            ->join('INNER JOIN', 'tbl_gp_questions', 'tbl_gp_questions.question_id = tbl_gp_audits_checklist_questions.question_id')
            ->andFilterWhere([
                'tbl_gp_audits_schedules.status' => 3
            ])
            ->andFilterWhere([
                '=',
                'tbl_gp_audits_schedules.audit_schedule_id',
                $audit_sch_id
            ])
            ->andFilterWhere([
                '=',
                'tbl_gp_checklists.cl_audit_span',
                2
            ]);

        $query->orderBy(['tbl_gp_audits_checklist_questions.q_section' => SORT_ASC]);


        $command = $query->createCommand();

        echo '<style>
        table, th, td {
           border: 1px solid black;
        }
        </style>';

        $data = $command->queryAll();

        if (count($data)) {
            $data = ArrayHelper::index($data, null, ['q_section', 'q_sub_section']);

            $table_h[] = array();
            $table_d[] = array();
            $sectionsList = ArrayHelper::map(Sections::find()->asArray()->all(), 'section_id', 's_section_name');

            $subsectionList = ArrayHelper::map(SubSections::find()->asArray()->all(), 'sub_section_id', 'ss_subsection_name');
            foreach ($data as $key => $val) {
                $table_headers = '';

                if (is_array($val)) {
                    $table_headers .= '<tr><td><b>SNO</b></td><td><b>' . (isset($sectionsList[$key]) ? $sectionsList[$key] : $key) . '</b></td>';
                    foreach ($val as $a_key => $a_val) {
                        $table_headers .= '<td class="text-center"><b>' . (isset($subsectionList[$a_key]) ? $subsectionList[$a_key] : $a_key) . '</b></td>';
                    }
                    $table_headers .= '<td class="text-center"><b>OBTAINED SCORE</b></td></tr>';

                    $qu_data[] = array();
                    foreach ($val as $a_key => $a_val) {
                        if (is_array($a_val)) {
                            foreach ($a_val as $ss => $value_a) {
                                if ($value_a['not_applicable'] == 0) {
                                    $qu_data[$key][$value_a['q_text']][] = $value_a['answer_value'];
                                } else {
                                    $qu_data[$key][$value_a['q_text']][] = 'N/A';
                                }
                            }
                        }
                    }
                }
                $table_h[$key] = $table_headers;
            }
        }
        $qu_data = $qu_data ? $qu_data : [];
        $body_data = array_filter($qu_data);

        $body_Array[] = array();

        foreach ($body_data as $main_key => $vals) {

            if (is_array($vals)) {
                $in = 1;
                foreach ($vals as $kkk => $b_vals) {
                    $body_text = '';
                    $body_text .= '<tr><td>' . $in . '</td><td>' . $kkk . '</td>';
                    $sum = [];
                    $na = [];
                    foreach ($b_vals as $b_s_val) {

                        if ($b_s_val != 'N/A') {
                            if ($b_s_val) {
                                $sum[] = $b_s_val;
                                $body_text .= '<td class="text-center">C</td>';
                            } else {
                                $body_text .= '<td class="text-center">NC</td>';
                            }
                        } else {
                            $na = [];
                            $body_text .= '<td class="text-center">' . $b_s_val . '</td>';
                        }
                    }
                    $body_text .= '<td class="text-center">' . round(count($sum) / (count($b_vals) - count($na)) * 100) . '</td>';
                    $body_text .= '</tr>';
                    $body_Array[$main_key][] = $body_text;
                    $in++;
                }
            }
        }
        $table_h = $table_h ? $table_h : [];
        $body_Array = $body_Array ? $body_Array : [];
        $head_final = array_filter($table_h);
        $body_final = array_filter($body_Array);
        $TableContent = '';
        //$TableContent .= '<div class="text-center">';
        //$TableContent .= Html::img(Yii::getAlias('@webroot') . '/img/greenpark_textlogo.png', ['style' => 'width:25%']);;
        //$TableContent .= '</div>';
        $TableContent .= '<div style="border: 1px solid black" class="col-md-12">';

        $reportHeading = '<div class="text-center">';
        $reportHeading .= 'Audit Report for Audit ID - ' . $auditName;
        $reportHeading .= '</div><br><div class="clearfix"></div><div class="clearfix"></div>';

        $TableContent .= $reportHeading;

        foreach ($head_final as $key => $headers_v) {
            $TableContent .= '<table class="table">';
            $TableContent .= $headers_v;
            if (isset($body_final[$key])) {
                foreach ($body_final[$key] as $b_val) {
                    $TableContent .= $b_val;
                }
            }
            $TableContent .= '</table>';
            $TableContent .= '<br><div class="clearfix"></div><div class="clearfix"></div><div class="clearfix"></div>';
        }
        $TableContent .= '</div>';
        return $TableContent;
    }

    /**
     * @param $departmentId
     * @param $hotelId
     * @param $locationId
     */
    public static function getAuditorsList($departmentId, $hotelId, $locationId, $type = 2)
    {
        $users = [];
        if ($departmentId && $hotelId && $locationId) {
            $users = User::find()->joinWith(['userLocations', 'userHotels', 'userDepartments.department'])
                ->where([UserLocations::tableName() . '.location_id' => $locationId,
                    UserHotels::tableName() . '.hotel_id' => $hotelId, HotelDepartments::tableName() . '.department_id' => $departmentId])
                ->andWhere([User::tableName() . '.is_deleted' => 0, 'user_type' => $type, User::tableName() . '.is_active' => 1])
                ->asArray()->all();

        }

        return $users;
    }

    /**
     * @param $hotelId
     */
    public static function getHotels($locationId)
    {
        $hotels = [];
        if ($locationId) {
            $hotels = Hotels::find()->where([
                'location_id' => $locationId,
                'is_deleted' => 0
            ])
                ->select([
                    'id' => 'hotel_id',
                    'name' => 'hotel_name'
                ])
                ->asArray()
                ->all();
        }
        return $hotels;
    }

    /**
     * @param $hotelId
     */
    public static function getHotelDepartments($hotelId)
    {
        $deparments = [];
        if ($hotelId) {
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
                    HotelDepartments::tableName() . '.hotel_id' => $hotelId,
                    HotelDepartments::tableName() . '.is_deleted' => 0
                ])
                ->select([
                    HotelDepartments::tableName() . '.id',
                    HotelDepartments::tableName() . '.department_id',
                    HotelDepartments::tableName() . '.hotel_id'
                ])
                ->asArray()
                ->all();
        }
        return $deparments;
    }
}
