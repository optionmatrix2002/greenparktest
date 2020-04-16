<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Query;
use yii\validators\EmailValidator;

/**
 * This is the model class for table "{{%departments}}".
 *
 * @property integer $department_id
 * @property string $department_name
 * @property string $department_description
 * * @property string $department_email
 * @property integer $created_by
 * @property integer $modified_by
 * @property string $created_date
 * @property string $modified_date
 *
 * @property Checklists[] $checklists
 * @property User $createdBy
 * @property User $modifiedBy
 * @property Hotels $departmentHotel
 */
class Departments extends \yii\db\ActiveRecord
{

    public static $monthsName = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'June', 7 => 'July', '8' => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%departments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'department_name',
                    'department_description'
                ],
                'required'
            ],
            
            [
                [
                    'created_by',
                    'modified_by'
                ],
                'integer'
            ],
            [
                [
                    'department_name'
                ],
                'unique',
                'targetClass' => self::className(),
                'filter' => [
                    '=',
                    'is_deleted',
                    0
                ]
            ],
            [
                [
                    'department_name'

                ],
                'match',
                'pattern' => '/^[0-9a-zA-Z\-,&\s]+$/'
            ],
            [
                [
                    'department_description'
                ],
                'string'
            ],
            [
                [
                    'created_date',
                    'modified_date'
                ],
                'safe'
            ],
            [
                [
                    'department_email'
                ],
                'string',
                'max' => 500
            ],
            ['department_email', 'checkEmailList'],
            
            [
                [
                    'department_name'
                ],
                'string',
                'max' => 100
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
                    'is_active'
                ],
                'default',
                'value' => 1
            ],
            [
                [
                    'modified_by'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => [
                    'modified_by' => 'user_id'
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
            'department_id' => Yii::t('app', 'Department'),
            'department_name' => Yii::t('app', 'Department Name'),
            'department_description' => Yii::t('app', 'Description'),
            'department_email' => Yii::t('app', 'Department Email'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_by' => Yii::t('app', 'Modified By'),
            'created_date' => Yii::t('app', 'Created Date'),
            'modified_date' => Yii::t('app', 'Modified Date')
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // TimestampBehavior also provides a method named touch() that allows you to assign the current timestamp to the specified attribute(s) and save them to the database. For example,
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_date',
                'updatedAtAttribute' => 'modified_date',
                'value' => date('Y-m-d H:i:s')
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'modified_by',
                'value' => (isset(Yii::$app->user) && isset(Yii::$app->user->id)) ? Yii::$app->user->id : 1
            ]
        ];
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChecklists()
    {
        return $this->hasMany(Checklists::className(), [
            'cl_department_id' => 'department_id'
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
    public function getModifiedBy()
    {
        return $this->hasOne(User::className(), [
            'user_id' => 'modified_by'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartmentHotel()
    {
        return $this->hasOne(Hotels::className(), [
            'hotel_id' => 'department_hotel_id'
        ]);
    }

    /**
     * Get audit avgerage list based departments
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getDeptAverageAuditData()
    {
        $listOfYears = $this->getAuditYears();
        $data = array();
        $params = Yii::$app->request->queryParams;
        $auditCountList = AuditsSchedules::find()->count();

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        foreach ($listOfYears as $year) {
            $yearV = $year['ayears'];


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
                ->andFilterWhere([
                    '=',
                    'YEAR(tbl_gp_audits_schedules.end_date)',
                    $year['ayears']
                ])
                ->andFilterWhere(['tbl_gp_answers.not_applicable' => 0]);

            if (isset($params['department_hotel_id'])) {
                $query5->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['department_hotel_id']]);
            }
            if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '' && isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                $query5->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                    ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
            }

            if ($userType != 1) {
                $query5->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
                $query5->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
            }
            $query5->groupBy('tbl_gp_departments.department_id');


            /* $query = new Query();
             $query->select([
                 'tbl_gp_departments.department_name as category',
                 'round(( count(tbl_gp_audits_schedules.audit_id)/' . $auditCountList . ' * 100 )) as percentage'
                 // ,'YEAR(tbl_gp_audits_schedules.end_date) as year'
             ])
                 ->from('tbl_gp_audits_schedules')
                 ->join('RIGHT JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id =tbl_gp_audits_schedules.audit_id')
                 ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id =tbl_gp_audits.department_id')
                 ->andFilterWhere([
                     '=',
                     'YEAR(tbl_gp_audits_schedules.end_date)',
                     $year['ayears']
                 ]);
             if (isset($params['department_hotel_id'])) {
                 $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['department_hotel_id']]);
             }
             if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '' && isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                 $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                     ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
             }
             $query->groupBy('tbl_gp_departments.department_id');
             */
            $command = $query5->createCommand();
            $data[$yearV] = $command->queryAll();
        }

        $newArray = array();
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
            ->andFilterWhere(['tbl_gp_answers.not_applicable' => 0]);

        if (isset($params['department_hotel_id'])) {
            $query2->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['department_hotel_id']]);
        }
        if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '' && isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
            $query2->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
        }

        if ($userType != 1) {
            $query2->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
            $query2->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
        }
        $query2->groupBy('tbl_gp_departments.department_id');

        /*$query2 = new Query();
        $query2->select([
            'tbl_gp_departments.department_name as category',
            'round(( count(tbl_gp_audits_schedules.audit_id)/' . $auditCountList . ' * 100 )) as percentage'
        ])
            ->from('tbl_gp_audits_schedules')
            ->join('RIGHT JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id =tbl_gp_audits_schedules.audit_id')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id =tbl_gp_audits.department_id');
        if (isset($params['department_hotel_id'])) {
            $query2->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['department_hotel_id']]);
        }
        if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '' && isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
            $query2->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
        }


        $query2->groupBy('tbl_gp_departments.department_id');
        */
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


    /**
     * Get audit avgerage list based departments
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getDeptAverageAuditDataMonth()
    {
        $listOfYears = $this->getTicketMonthsYears();
        $data = array();
        $params = Yii::$app->request->queryParams;


        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        foreach ($listOfYears as $year) {
            $yearV = $year['year'];
            $month = $year['month'];

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
                    'YEAR(tbl_gp_audits_schedules.end_date)',
                    $yearV
                ])
                ->andFilterWhere([
                    '=',
                    'MONTH(tbl_gp_audits_schedules.end_date)',
                    $month
                ])
                ->andFilterWhere(['tbl_gp_answers.not_applicable' => 0]);

            if (isset($params['department_hotel_id'])) {
                $query5->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['department_hotel_id']]);
            }


            if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') || (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {

                if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') && (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {
                    $query5->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                        ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
                } else if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') {
                    $toDate = explode('-', $params['departmentStartDate']);
                    $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                    $query5->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                        ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', $totDays]);
                } else if (isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                    $toDate = explode('-', $params['departmentendDate']);
                    $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                    $query5->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', $fromDays])
                        ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime('01' . $params['departmentendDate']))]);
                }
            } else {
                $query5->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime(date('Y') . '-01-01'))])
                    ->andFilterWhere(['<', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime(date('Y') . '-12-31'))]);
            }

            if ($userType != 1) {
                $query5->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
                $query5->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
            }
            $query5->groupBy('tbl_gp_departments.department_id');
            $command = $query5->createCommand();
            $month = self::$monthsName[$month] . '(' . $yearV . ')';
            $data[$month] = $command->queryAll();
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
            ->andFilterWhere(['tbl_gp_audits_schedules.is_deleted' => 0])
            ->andFilterWhere(['tbl_gp_answers.not_applicable' => 0]);

        if (isset($params['department_hotel_id'])) {
            $query2->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['department_hotel_id']]);
        }

        if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') || (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {

            if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') && (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {
                $query2->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
            } else if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') {
                $toDate = explode('-', $params['departmentStartDate']);
                $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                $query2->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', $totDays]);
            } else if (isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                $toDate = explode('-', $params['departmentendDate']);
                $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                $query2->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', $fromDays])
                    ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime('01' . $params['departmentendDate']))]);
            }
        } else {
            $query2->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime(date('Y') . '-01-01'))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime(date('Y') . '-12-31'))]);
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

    /**
     * Get audit avgerage list based hotels
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getDeptAuditData()
    {
        $listOfYears = $this->getAuditYears();
        $data = array();
        $auditCountList = AuditsSchedules::find()->count();
        $params = Yii::$app->request->queryParams;

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;
        foreach ($listOfYears as $year) {
            $yearV = $year['ayears'];
            $query = new Query();
            $query->select([
                'tbl_gp_departments.department_name as category',
                'count(tbl_gp_audits_schedules.audit_id) as percentage'
            ])
                ->from('tbl_gp_audits_schedules')
                ->join('RIGHT JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id =tbl_gp_audits_schedules.audit_id')
                ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id =tbl_gp_audits.department_id')
                ->andFilterWhere([
                    '=',
                    'YEAR(tbl_gp_audits_schedules.end_date)',
                    $year['ayears']
                ]);
            if (isset($params['department_hotel_id'])) {
                $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['department_hotel_id']]);
            }
            if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '' && isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                    ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
            }

            if ($userType != 1) {
                $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
                $query->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
            }

            $query->groupBy('tbl_gp_departments.department_id');
            $command = $query->createCommand();
            $data[$yearV] = $command->queryAll();
        }

        $ballonArray = array();
        $newArray = array();
        foreach ($data as $key => $list) {
            $newArray[$key]['year'] = $key;
            foreach ($list as $dataF) {
                $newArray[$key][$dataF['category']] = $dataF['percentage'];
            }
        }

        $query2 = new Query();
        // /$query ->select(['tbl_gp_hotels.hotel_name as category' ,'concat(round(( count(tbl_gp_audits_schedules.audit_id)/'.$auditCountList.' * 100 ),2),\'%\') as percentage','count(tbl_gp_audits_schedules.audit_id) as audit_count'])
        $query2->select([
            'tbl_gp_departments.department_name as category',
            'count(tbl_gp_audits_schedules.audit_id) as percentage'
        ])
            ->from('tbl_gp_audits_schedules')
            ->join('RIGHT JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id =tbl_gp_audits_schedules.audit_id')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id =tbl_gp_audits.department_id');
        if (isset($params['department_hotel_id'])) {
            $query2->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['department_hotel_id']]);
        }
        if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '' && isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
            $query2->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
        }
        if ($userType != 1) {
            $query2->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
            $query2->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
        }

        $query2->groupBy('tbl_gp_departments.department_id');
        $command = $query2->createCommand();
        $ballonData = $command->queryAll();
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
        $result['data'] = array_values($newArray);
        $result['ballon'] = array_values($ballonArray);

        return $result;
    }


    /**
     * Get audit avgerage list based hotels
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getDeptAuditDataMonth()
    {
        $listOfYears = $this->getTicketMonthsYears();
        $data = array();
        $params = Yii::$app->request->queryParams;

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        foreach ($listOfYears as $year) {
            $month = $year['month'];
            $yearV = $year['year'];
            $query = new Query();
            $query->select([
                'tbl_gp_departments.department_name as category',
                'count(tbl_gp_audits_schedules.audit_id) as percentage'
            ])
                ->from('tbl_gp_audits_schedules')
                ->join('RIGHT JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id =tbl_gp_audits_schedules.audit_id')
                ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id =tbl_gp_audits.department_id')
                ->andFilterWhere([
                    '=',
                    'YEAR(tbl_gp_audits_schedules.start_date)',
                    $yearV
                ])->andFilterWhere([
                    '=',
                    'MONTH(tbl_gp_audits_schedules.start_date)',
                    $month
                ]);
            if (isset($params['department_hotel_id'])) {
                $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['department_hotel_id']]);
            }


            if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') || (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {

                if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') && (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {
                    $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                        ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
                } else if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') {
                    $toDate = explode('-', $params['departmentStartDate']);
                    $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                    $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                        ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', $totDays]);
                } else if (isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                    $toDate = explode('-', $params['departmentendDate']);
                    $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                    $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', $fromDays])
                        ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime('01' . $params['departmentendDate']))]);
                }
            } else {
                $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime(date('Y') . '-01-01'))])
                    ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime(date('Y') . '-12-31'))]);
            }

            if ($userType != 1) {
                $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
                $query->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
            }
            $query->andFilterWhere(['tbl_gp_audits_schedules.status' => 3]);
            $query->andFilterWhere(['tbl_gp_audits_schedules.is_deleted' => 0]);
            $query->groupBy('tbl_gp_departments.department_id');
            $command = $query->createCommand();
            $month = self::$monthsName[$month] . '(' . $yearV . ')';
            $data[$month] = $command->queryAll();
        }

        $ballonArray = array();
        $newArray = array();
        $data = array_filter($data);

        foreach ($data as $key => $list) {
            $newArray[$key]['year'] = $key;
            foreach ($list as $dataF) {
                $newArray[$key][$dataF['category']] = $dataF['percentage'];
            }
        }

        $query2 = new Query();
        // /$query ->select(['tbl_gp_hotels.hotel_name as category' ,'concat(round(( count(tbl_gp_audits_schedules.audit_id)/'.$auditCountList.' * 100 ),2),\'%\') as percentage','count(tbl_gp_audits_schedules.audit_id) as audit_count'])
        $query2->select([
            'tbl_gp_departments.department_name as category',
            'count(tbl_gp_audits_schedules.audit_id) as percentage'
        ])
            ->from('tbl_gp_audits_schedules')
            ->join('RIGHT JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id =tbl_gp_audits_schedules.audit_id')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id =tbl_gp_audits.department_id');
        if (isset($params['department_hotel_id'])) {
            $query2->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['department_hotel_id']]);
        }


        if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') || (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {

            if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') && (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {
                $query2->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
            } else if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') {
                $toDate = explode('-', $params['departmentStartDate']);
                $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                $query2->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', $totDays]);
            } else if (isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                $toDate = explode('-', $params['departmentendDate']);
                $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                $query2->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', $fromDays])
                    ->andFilterWhere(['<=', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime('01' . $params['departmentendDate']))]);
            }
        } else {
            $query2->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime(date('Y') . '-01-01'))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.start_date', date('Y-m-t', strtotime(date('Y') . '-12-31'))]);
        }

        if ($userType != 1) {
            $query2->andFilterWhere(['tbl_gp_audits.hotel_id' => $userHotels]);
            $query2->andFilterWhere(['tbl_gp_audits.department_id' => $userDepartments]);
        }
        $query->andFilterWhere(['tbl_gp_audits_schedules.status' => 3]);
        $query->andFilterWhere(['tbl_gp_audits_schedules.is_deleted' => 3]);
        $query2->groupBy('tbl_gp_departments.department_id');
        $command = $query2->createCommand();
        $ballonData = $command->queryAll();
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
        $result['data'] = array_values($newArray);
        $result['ballon'] = array_values($ballonArray);

        return $result;
    }

    /**
     * Get audit avgerage list based hotels
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getDeptTicketAuditData()
    {
        $listOfYears = $this->getTicketYears();
        $data = array();
        $params = Yii::$app->request->queryParams;

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        foreach ($listOfYears as $year) {
            $yearV = $year['ayears'];
            $query = new Query();
            $query->select([
                'tbl_gp_departments.department_name as category',
                'count(ticket_id) as ticket_count'
            ])
                ->from('tbl_gp_tickets')
                ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_tickets.department_id')
                ->andFilterWhere([
                    '=',
                    'YEAR(tbl_gp_tickets.due_date)',
                    $year['ayears']
                ]);
            if (isset($params['department_hotel_id'])) {
                $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $params['department_hotel_id']]);
            }
            if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '' && isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                    ->andFilterWhere(['<', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
            }

            if ($userType != 1) {
                $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
                $query->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
            }

            $query->groupBy('tbl_gp_tickets.department_id');
            $command = $query->createCommand();
            $data[$yearV] = $command->queryAll();
        }

        $ballonArray = array();
        $newArray = array();
        foreach ($data as $key => $list) {
            $newArray[$key]['year'] = $key;
            foreach ($list as $dataF) {
                $newArray[$key][$dataF['category']] = $dataF['ticket_count'];
            }
        }

        $query2 = new Query();
        // /$query ->select(['tbl_gp_hotels.hotel_name as category' ,'concat(round(( count(tbl_gp_audits_schedules.audit_id)/'.$auditCountList.' * 100 ),2),\'%\') as percentage','count(tbl_gp_audits_schedules.audit_id) as audit_count'])
        $query2->select([
            'tbl_gp_departments.department_name as category',
            'count(ticket_id) as ticket_count'
        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_tickets.department_id');
        if (isset($params['department_hotel_id'])) {
            $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $params['department_hotel_id']]);
        }
        if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '' && isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
            $query2->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
        }

        if ($userType != 1) {
            $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
            $query2->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
        }

        $query2->groupBy('tbl_gp_departments.department_id');
        $command = $query2->createCommand();
        $ballonData = $command->queryAll();
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
        $result['data'] = array_values($newArray);
        $result['ballon'] = array_values($ballonArray);

        return $result;
    }

    /**
     * Get audit avgerage list based hotels
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getDeptTicketAuditDataMonth()
    {
        $listOfYears = $this->getTicketMonthsYears();

        $data = array();
        $params = Yii::$app->request->queryParams;

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        foreach ($listOfYears as $year) {
            $yearV = $year['year'];
            $month = $year['month'];
            $query = new Query();
            $query->select([
                'tbl_gp_departments.department_name as category',
                'count(ticket_id) as ticket_count'
            ])
                ->from('tbl_gp_tickets')
                ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_tickets.department_id')
                ->andFilterWhere([
                    '=',
                    'YEAR(tbl_gp_tickets.created_at)',
                    $yearV
                ])
                ->andFilterWhere([
                    '=',
                    'MONTH(tbl_gp_tickets.created_at)',
                    $month
                ]);
            if (isset($params['department_hotel_id'])) {
                $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $params['department_hotel_id']]);
            }

            if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') || (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {

                if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') && (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {
                    $query->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                        ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
                } else if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') {
                    $toDate = explode('-', $params['departmentStartDate']);
                    $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                    $query->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                        ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', $totDays]);
                } else if (isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                    $toDate = explode('-', $params['departmentendDate']);
                    $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                    $query->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', $fromDays])
                        ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime('01' . $params['departmentendDate']))]);
                }
            } else {
                $query->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime(date('Y') . '-01-01'))])
                    ->andFilterWhere(['<', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime(date('Y') . '-12-31'))]);
            }

            if ($userType != 1) {
                $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
                $query->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
            }
            $query->andFilterWhere(['tbl_gp_tickets.status' => [0, 1, 2, 4]]);
            $query->groupBy('tbl_gp_tickets.department_id');
            $command = $query->createCommand();
            $month = self::$monthsName[$month] . '(' . $yearV . ')';
            $data[$month] = $command->queryAll();
        }

        $ballonArray = array();
        $newArray = array();
        $data = array_filter($data, function ($element) {
            if ($element || $element == 0) {
                return true;
            }
        });

        foreach ($data as $key => $list) {
            $newArray[$key]['year'] = $key;
            foreach ($list as $dataF) {
                $newArray[$key][$dataF['category']] = $dataF['ticket_count'];
            }
        }

        $query2 = new Query();
        $query2->select([
            'tbl_gp_departments.department_name as category',
            'count(ticket_id) as ticket_count'
        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_tickets.department_id');

        if (isset($params['department_hotel_id'])) {
            $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $params['department_hotel_id']]);
        }

        if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') || (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {

            if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') && (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {
                $query2->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
            } else if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') {
                $toDate = explode('-', $params['departmentStartDate']);
                $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                $query2->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', $totDays]);
            } else if (isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                $toDate = explode('-', $params['departmentendDate']);
                $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                $query2->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', $fromDays])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime('01' . $params['departmentendDate']))]);
            }
        } else {
            $query2->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime(date('Y') . '-01-01'))])
                ->andFilterWhere(['<', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime(date('Y') . '-12-31'))]);
        }

        if ($userType != 1) {
            $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
            $query2->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
        }
        $query2->andFilterWhere(['tbl_gp_tickets.status' => [0, 1, 2, 4]]);
        $query2->groupBy('tbl_gp_departments.department_id');
        $command = $query2->createCommand();
        $ballonData = $command->queryAll();
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
        $result['data'] = array_values($newArray);
        $result['ballon'] = array_values($ballonArray);

        return $result;
    }

    /**
     * Get audit avgerage list based hotels
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getChronicDeptTicketAuditData()
    {
        $listOfYears = $this->getTicketYears();
        $data = array();
        $params = Yii::$app->request->queryParams;

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        foreach ($listOfYears as $year) {
            $yearV = $year['ayears'];
            $query = new Query();
            $query->select([
                'tbl_gp_departments.department_name as category',
                'count(ticket_id) as ticket_count'
            ])
                ->from('tbl_gp_tickets')
                ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_tickets.department_id')
                ->andFilterWhere([
                    '=',
                    'YEAR(tbl_gp_tickets.due_date)',
                    $year['ayears']
                ])
                ->andFilterWhere([
                    'tbl_gp_tickets.chronicity' => 1
                ]);
            if (isset($params['department_hotel_id'])) {
                $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $params['department_hotel_id']]);
            }
            if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '' && isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                    ->andFilterWhere(['<', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
            }

            if ($userType != 1) {
                $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
                $query->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
            }

            $query->groupBy('tbl_gp_tickets.department_id');
            $command = $query->createCommand();
            $data[$yearV] = $command->queryAll();
        }

        $ballonArray = array();
        $newArray = array();
        foreach ($data as $key => $list) {
            $newArray[$key]['year'] = $key;
            foreach ($list as $dataF) {
                $newArray[$key][$dataF['category']] = $dataF['ticket_count'];
            }
        }

        $query2 = new Query();
        $query2->select([
            'tbl_gp_departments.department_name as category',
            'count(ticket_id) as ticket_count'
        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_tickets.department_id')
            ->andFilterWhere([
                'tbl_gp_tickets.chronicity' => 1
            ]);
        if (isset($params['department_hotel_id'])) {
            $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $params['department_hotel_id']]);
        }
        if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '' && isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
            $query2->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
        }
        if ($userType != 1) {
            $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
            $query2->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
        }
        $query2->groupBy('tbl_gp_departments.department_id');
        $command = $query2->createCommand();
        $ballonData = $command->queryAll();
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
        $result['data'] = array_values($newArray);
        $result['ballon'] = array_values($ballonArray);

        return $result;
    }

    /**
     * Get audit avgerage list based hotels
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getChronicDeptTicketAuditDataMonth()
    {
        $listOfYears = $this->getTicketMonthsYears();
        $data = array();
        $params = Yii::$app->request->queryParams;

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        foreach ($listOfYears as $year) {
            $yearV = $year['year'];
            $month = $year['month'];
            $query = new Query();
            $query->select([
                'tbl_gp_departments.department_name as category',
                'count(ticket_id) as ticket_count'
            ])
                ->from('tbl_gp_tickets')
                ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_tickets.department_id')
                ->andFilterWhere([
                    '=',
                    'YEAR(tbl_gp_tickets.created_at)',
                    $yearV
                ])->andFilterWhere([
                    '=',
                    'MONTH(tbl_gp_tickets.created_at)',
                    $month
                ])
                ->andFilterWhere([
                    'tbl_gp_tickets.chronicity' => 1
                ]);
            if (isset($params['department_hotel_id'])) {
                $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $params['department_hotel_id']]);
            }

            if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') || (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {

                if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') && (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {
                    $query->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                        ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
                } else if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') {
                    $toDate = explode('-', $params['departmentStartDate']);
                    $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                    $query->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                        ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', $totDays]);
                } else if (isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                    $toDate = explode('-', $params['departmentendDate']);
                    $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                    $query->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', $fromDays])
                        ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime('01' . $params['departmentendDate']))]);
                }
            } else {
                $query->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime(date('Y') . '-01-01'))])
                    ->andFilterWhere(['<', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime(date('Y') . '-12-31'))]);
            }

            if ($userType != 1) {
                $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
                $query->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
            }
            $query->andFilterWhere(['tbl_gp_tickets.status' => [0, 1, 2, 4]]);
            $query->groupBy('tbl_gp_tickets.department_id');
            $command = $query->createCommand();
            $month = self::$monthsName[$month] . '(' . $yearV . ')';
            $data[$month] = $command->queryAll();
        }

        $ballonArray = array();
        $newArray = array();
        $data = array_filter($data);
        foreach ($data as $key => $list) {
            $newArray[$key]['year'] = $key;
            foreach ($list as $dataF) {
                $newArray[$key][$dataF['category']] = $dataF['ticket_count'];
            }
        }

        $query2 = new Query();
        $query2->select([
            'tbl_gp_departments.department_name as category',
            'count(ticket_id) as ticket_count'
        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_tickets.department_id')
            ->andFilterWhere([
                'tbl_gp_tickets.chronicity' => 1
            ]);
        if (isset($params['department_hotel_id'])) {
            $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $params['department_hotel_id']]);
        }
        if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') || (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {

            if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') && (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {
                $query2->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
            } else if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') {
                $toDate = explode('-', $params['departmentStartDate']);
                $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                $query2->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', $totDays]);
            } else if (isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                $toDate = explode('-', $params['departmentendDate']);
                $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                $query2->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', $fromDays])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime('01' . $params['departmentendDate']))]);
            }
        } else {
            $query2->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime(date('Y') . '-01-01'))])
                ->andFilterWhere(['<', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime(date('Y') . '-12-31'))]);
        }

        if ($userType != 1) {
            $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
            $query2->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
        }
        $query2->andFilterWhere(['tbl_gp_tickets.status' => [0, 1, 2, 4]]);
        $query2->groupBy('tbl_gp_departments.department_id');
        $command = $query2->createCommand();
        $ballonData = $command->queryAll();
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
        $result['data'] = array_values($newArray);
        $result['ballon'] = array_values($ballonArray);

        return $result;
    }

    /**
     * Get audit avgerage list based hotels
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getOverdueDeptTicketAuditData()
    {
        $time = new \DateTime('now');
        $today = $time->format('Y-m-d');
        $listOfYears = $this->getTicketYears();
        $data = array();
        $params = Yii::$app->request->queryParams;

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        foreach ($listOfYears as $year) {
            $yearV = $year['ayears'];
            $query = new Query();
            $query->select([
                'tbl_gp_departments.department_name as category',
                'count(ticket_id) as ticket_count'
            ])
                ->from('tbl_gp_tickets')
                ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_tickets.department_id')
                ->andFilterWhere([
                    '=',
                    'YEAR(tbl_gp_tickets.due_date)',
                    $year['ayears']
                ])
                ->andFilterWhere([
                    '<',
                    'tbl_gp_tickets.due_date',
                    $today
                ]);

            $query->andFilterWhere(['tbl_gp_tickets.status' => [0, 1, 4]]);

            if (isset($params['department_hotel_id'])) {
                $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $params['department_hotel_id']]);
            }
            if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '' && isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                    ->andFilterWhere(['<', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
            }
            if ($userType != 1) {
                $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
                $query->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
            }

            $query->groupBy('tbl_gp_tickets.department_id');
            $command = $query->createCommand();
            $data[$yearV] = $command->queryAll();
        }

        $ballonArray = array();
        $newArray = array();
        foreach ($data as $key => $list) {
            $newArray[$key]['year'] = $key;
            foreach ($list as $dataF) {
                $newArray[$key][$dataF['category']] = $dataF['ticket_count'];
            }
        }

        $query2 = new Query();
        // /$query ->select(['tbl_gp_hotels.hotel_name as category' ,'concat(round(( count(tbl_gp_audits_schedules.audit_id)/'.$auditCountList.' * 100 ),2),\'%\') as percentage','count(tbl_gp_audits_schedules.audit_id) as audit_count'])
        $query2->select([
            'tbl_gp_departments.department_name as category',
            'count(ticket_id) as ticket_count'
        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_tickets.department_id')
            ->andFilterWhere([
                '<',
                'tbl_gp_tickets.due_date',
                $today
            ]);
        if (isset($params['department_hotel_id'])) {
            $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $params['department_hotel_id']]);
        }
        if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '' && isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
            $query2->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
        }
        if ($userType != 1) {
            $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
            $query2->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
        }

        $query2->andFilterWhere(['tbl_gp_tickets.status' => [0, 1, 4]]);

        $query2->groupBy('tbl_gp_departments.department_id');
        $command = $query2->createCommand();
        $ballonData = $command->queryAll();
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
        $result['data'] = array_values($newArray);
        $result['ballon'] = array_values($ballonArray);

        return $result;
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function getOverdueDeptTicketAuditDataMonth()
    {
        $time = new \DateTime('now');
        $today = $time->format('Y-m-d');
        $listOfYears = $this->getTicketMonthsYears();
        $data = array();
        $params = Yii::$app->request->queryParams;

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        foreach ($listOfYears as $year) {
            $yearV = $year['year'];
            $month = $year['month'];
            $query = new Query();
            $query->select([
                'tbl_gp_departments.department_name as category',
                'count(ticket_id) as ticket_count'
            ])
                ->from('tbl_gp_tickets')
                ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_tickets.department_id')
                ->andFilterWhere([
                    '=',
                    'YEAR(tbl_gp_tickets.due_date)',
                    $yearV
                ])
                ->andFilterWhere([
                    '=',
                    'MONTH(tbl_gp_tickets.created_at)',
                    $month
                ])
                ->andFilterWhere([
                    '<',
                    'tbl_gp_tickets.created_at',
                    $today
                ]);

            $query->andFilterWhere(['tbl_gp_tickets.status' => [0, 1, 2, 4]]);
            if (isset($params['department_hotel_id'])) {
                $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $params['department_hotel_id']]);
            }


            if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') || (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {

                if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') && (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {
                    $query->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                        ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
                } else if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') {
                    $toDate = explode('-', $params['departmentStartDate']);
                    $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                    $query->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                        ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', $totDays]);
                } else if (isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                    $toDate = explode('-', $params['departmentendDate']);
                    $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                    $query->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', $fromDays])
                        ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime('01' . $params['departmentendDate']))]);
                }
            } else {
                $query->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime(date('Y') . '-01-01'))])
                    ->andFilterWhere(['<', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime(date('Y') . '-12-31'))]);
            }

            if ($userType != 1) {
                $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
                $query->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
            }

            $query->groupBy('tbl_gp_tickets.department_id');
            $command = $query->createCommand();
            $month = self::$monthsName[$month] . '(' . $yearV . ')';
            $data[$month] = $command->queryAll();
        }

        $ballonArray = array();
        $newArray = array();
        $data = array_filter($data);
        foreach ($data as $key => $list) {
            $newArray[$key]['year'] = $key;
            foreach ($list as $dataF) {
                $newArray[$key][$dataF['category']] = $dataF['ticket_count'];
            }
        }

        $query2 = new Query();
        // /$query ->select(['tbl_gp_hotels.hotel_name as category' ,'concat(round(( count(tbl_gp_audits_schedules.audit_id)/'.$auditCountList.' * 100 ),2),\'%\') as percentage','count(tbl_gp_audits_schedules.audit_id) as audit_count'])
        $query2->select([
            'tbl_gp_departments.department_name as category',
            'count(ticket_id) as ticket_count'
        ])
            ->from('tbl_gp_tickets')
            ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_tickets.department_id')
            ->andFilterWhere([
                '<',
                'tbl_gp_tickets.due_date',
                $today
            ]);
        if (isset($params['department_hotel_id'])) {
            $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $params['department_hotel_id']]);
        }


        if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') || (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {

            if ((isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') && (isset($params['departmentendDate']) && $params['departmentendDate'] != '')) {
                $query2->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
            } else if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '') {
                $toDate = explode('-', $params['departmentStartDate']);
                $totDays = date("Y-m-t", strtotime($toDate[1] . '-12-31'));
                $query2->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', $totDays]);
            } else if (isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
                $toDate = explode('-', $params['departmentendDate']);
                $fromDays = date("Y-m-d", strtotime($toDate[1] . '-01-01'));
                $query2->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', $fromDays])
                    ->andFilterWhere(['<=', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime('01' . $params['departmentendDate']))]);
            }
        } else {
            $query2->andFilterWhere(['>=', 'tbl_gp_tickets.created_at', date('Y-m-d', strtotime(date('Y') . '-01-01'))])
                ->andFilterWhere(['<', 'tbl_gp_tickets.created_at', date('Y-m-t', strtotime(date('Y') . '-12-31'))]);
        }
        if ($userType != 1) {
            $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
            $query2->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
        }

        $query2->andFilterWhere(['tbl_gp_tickets.status' => [0, 1, 2, 4]]);
        $query2->groupBy('tbl_gp_departments.department_id');
        $command = $query2->createCommand();
        $ballonData = $command->queryAll();
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
        $result['data'] = array_values($newArray);
        $result['ballon'] = array_values($ballonArray);

        return $result;
    }

    public function getAuditYears()
    {
        $params = Yii::$app->request->queryParams;
        $query = new Query();
        $query->select([
            'DISTINCT YEAR(end_date) as ayears'
        ])
            ->from('tbl_gp_audits_schedules');

        if (isset($params['departmentStartDate']) && $params['departmentStartDate'] != '' && isset($params['departmentendDate']) && $params['departmentendDate'] != '') {
            $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime('01-' . $params['departmentendDate']))]);
        }

        $query->orderBy([
            'end_date' => SORT_ASC
        ]);
        $command = $query->createCommand();

        $data = $command->queryAll();
        return $data;
    }

    public function getTicketYears()
    {
        $params = Yii::$app->request->queryParams;
        $query = new Query();
        // /$query ->select(['tbl_gp_hotels.hotel_name as category' ,'concat(round(( count(tbl_gp_audits_schedules.audit_id)/'.$auditCountList.' * 100 ),2),\'%\') as percentage','count(tbl_gp_audits_schedules.audit_id) as audit_count'])
        $query->select([
            'DISTINCT YEAR(due_date) as ayears'
        ])
            ->from('tbl_gp_tickets');
        if (isset($params['departmentStartDate']) && isset($params['departmentendDate'])) {
            $query->andFilterWhere(['>=', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime('01-' . $params['departmentStartDate']))])
                ->andFilterWhere(['<', 'tbl_gp_tickets.due_date', date('Y-m-t', strtotime('01-' . $params['departmentendDate']))]);
        }
        $query->orderBy([
            'due_date' => SORT_ASC
        ]);
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function getTicketMonthsYears()
    {
        $params = Yii::$app->request->queryParams;
        if (!isset($params['departmentStartDate']) && !isset($params['departmentendDate'])) {
            $currentYear = date('Y');
            $monthsList = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
            $data = [];
            foreach ($monthsList as $monthValue) {
                $month['month'] = $monthValue;
                $month['year'] = $currentYear;
                $data[] = $month;
            }
            return $data;
        } elseif (isset($params['departmentStartDate']) || isset($params['departmentendDate'])) {
            $from = $params['departmentStartDate'];
            $to = $params['departmentendDate'];
            if ($from && !$to) {
                $from = explode('-', $from);
                $start = (int)$from[0];

                for ($i = 1; $i <= 12; $i++) {
                    $monthValue = $start;
                    $year = $from[1];
                    if ($start > 12) {
                        $monthValue = $start - 12;
                        $year = $from[1] + 1;
                    }
                    $month['year'] = $year;
                    $month['month'] = $monthValue;
                    $data[] = $month;
                    $start++;

                }

                return $data;
            } else if ($from && $to) {
                $from = explode('-', $from);
                $to = explode('-', $to);
                $start = (int)$from[0];

                if ($from[1] == $to[1]) {
                    for ($i = $start; $i <= $to[0]; $i++) {
                        if ($i <= $to[0]) {
                            $month['month'] = $i;
                            $month['year'] = $from[1];
                            $data[] = $month;
                        }
                    }
                } else {
                    $toValue = $to[0] + 12;
                    for ($i = 1; $i <= 12; $i++) {
                        $monthValue = $start;
                        $year = $from[1];

                        if ($start > 12) {
                            $monthValue = $start - 12;
                            $year = $to[1];
                        }
                        $month['year'] = $year;
                        $month['month'] = $monthValue;
                        if ($start <= $toValue) {
                            $data[] = $month;
                        }
                        $start++;
                    }
                }

                return $data;
            } else if (!$from && !$to) {
                $currentYear = date('Y');
                $monthsList = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
                $data = [];
                foreach ($monthsList as $monthValue) {
                    $month['month'] = $monthValue;
                    $month['year'] = $currentYear;
                    $data[] = $month;
                }
                return $data;
            }
        }
    }
    
    public function checkEmailList($attribute, $params) {
       // print_r($params); die();
        $validator = new EmailValidator;
        $cleanemails = preg_replace('/\s+/', '', $this->department_email);
      //  print_r($cleanemails); die();
        $emails = is_array($cleanemails)? : explode(',', $cleanemails);
        foreach ($emails as $email) {
            $validator->validate($email)? : $this->addError($attribute, "$email is not a valid email.");
        }
    }

}
