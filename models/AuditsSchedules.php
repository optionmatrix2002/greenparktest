<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_gp_audits_schedules".
 *
 * @property integer $audit_schedule_id
 * @property string $audit_schedule_name
 * @property integer $audit_id
 * @property integer $auditor_id
 * @property string $start_date
 * @property string $end_date
 * @property integer $deligation_user_id
 * @property integer $deligation_status
 * @property integer $status
 * @property integer $is_deleted
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Answers[] $answers
 * @property Audits $audit
 * @property User $deligationUser
 * @property User $auditor
 */
class AuditsSchedules extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_gp_audits_schedules';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'audit_schedule_name',
                    'audit_id',
                    'auditor_id',
                    'start_date',
                    'end_date'
                    //'deligation_user_id',
                    //  'is_deleted'
                ],
                'required'
            ],
            [
                [
                    'audit_id',
                    'auditor_id',
                    // 'deligation_user_id',
                    'deligation_status',
                    'status',
                    'is_deleted',
                    'created_by',
                    'updated_by'
                ],
                'integer'
            ],
            [
                [
                    'start_date',
                    'end_date',
                    'created_at',
                    'updated_at', 'notification_status'
                ],
                'safe'
            ],
            ['notification_status', 'default', 'value' => 0],
            [
                [
                    'audit_schedule_name'
                ],
                'string',
                'max' => 100
            ],
            [
                [
                    'audit_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => Audits::className(),
                'targetAttribute' => [
                    'audit_id' => 'audit_id'
                ]
            ],
            /*  [
                  [
                      'deligation_user_id'
                  ],
                  'exist',
                  'skipOnError' => true,
                  'targetClass' => User::className(),
                  'targetAttribute' => [
                      'deligation_user_id' => 'user_id'
                  ]
              ],*/
            [
                [
                    'auditor_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => [
                    'auditor_id' => 'user_id'
                ]
            ]
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
                'value' => (isset(Yii::$app->user) && isset(Yii::$app->user->id)) ? Yii::$app->user->id : 1
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'audit_schedule_id' => Yii::t('app', 'Audit Schedule ID'),
            'audit_schedule_name' => Yii::t('app', 'Audit Schedule Name'),
            'audit_id' => Yii::t('app', 'Audit ID'),
            'auditor_id' => Yii::t('app', 'Auditor'),
            'start_date' => Yii::t('app', 'Start Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'deligation_user_id' => Yii::t('app', 'Deligation User ID'),
            'deligation_status' => Yii::t('app', 'Deligation Status'),
            'status' => Yii::t('app', 'Status'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At')
        ];
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditsChecklistQuestions()
    {
        return $this->hasMany(AuditsChecklistQuestions::className(), [
            'audit_id' => 'audit_schedule_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(Answers::className(), [
            'audit_id' => 'audit_schedule_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAudit()
    {
        return $this->hasOne(Audits::className(), [
            'audit_id' => 'audit_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeligationUser()
    {
        return $this->hasOne(User::className(), [
            'user_id' => 'deligation_user_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditor()
    {
        return $this->hasOne(User::className(), [
            'user_id' => 'auditor_id'
        ]);
    }

    public function getStatus()
    {
        return $this->hasMany(Audits::className(), [
            'status' => 'status'
        ]);
    }

    public function getScheduleAuditStatus()
    {
        return $this->hasMany(AuditsSchedules::className(), [
            'audit_id' => 'audit_id'
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
     * @param $auditId
     * @return float|int
     */
    public static function getAuditScore($auditId)
    {
		 $result = Audits::getAuditList($auditId, '', '', 'audit_schedule_id');
        $sum = ArrayHelper::getColumn($result, 'score');
        $sum = round(array_sum($sum) / count($sum));
        return $sum;
		
        $answersCount = Answers::find()->where([
            'audit_id' => $auditId,
            'not_applicable' => 0,
        ])->sum('answer_score');

        $totalQuestions = AuditsChecklistQuestions::find()->joinWith(['answers'])->where([
            AuditsChecklistQuestions::tableName() . '.audit_id' => $auditId, Answers::tableName() . '.not_applicable' => 0
        ])->count();


		$totalQuestions = Answers::find()->where([
            'audit_id' => $auditId,
            'not_applicable' => 0,
        ])->count();
		
        $totalQuestions = $totalQuestions * 10;

        $totalScore = 0;
        if ($totalQuestions != 0) {
            $totalScore = round(number_format(($answersCount / $totalQuestions) * 100, 2));
        }
        return $totalScore;
    }

    /**
     * @param $auditId
     */
    public static function getLastAuditEndDate($auditId)
    {
        $audit = self::find()->where(['audit_id' => $auditId])->orderBy('audit_schedule_id DESC')->asArray()->one();
        if ($audit) {
            return $audit['end_date'];
        }
        return '';

    }
}
