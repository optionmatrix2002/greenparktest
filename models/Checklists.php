<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gp_checklists".
 *
 * @property integer $checklist_id
 * @property string $cl_name
 * @property integer $cl_audit_type
 * @property integer $cl_audit_method
 * @property integer $cl_department_id
 * @property integer $cl_frequency_value
 * @property integer $cl_frequency_duration
 * @property integer $cl_audit_span
 * @property integer $cl_status
 *
 * @property AuditMethods $clAuditMethod
 * @property Departments $clDepartment
 * @property Interval $clFrequencyDuration
 * @property Questions[] $questions
 */
class Checklists extends \yii\db\ActiveRecord
{


    public $checklistname_search;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_gp_checklists';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'cl_name',
                    'cl_audit_type',
                    'cl_audit_method',
                    'cl_department_id',
                    'cl_frequency_value',
                    'cl_audit_span',
                    'cl_status'
                ],
                'required'
            ],
            [
                [
                    'cl_audit_type',
                    'cl_audit_method',
                    'cl_department_id',
                    'cl_frequency_value',
                    'cl_audit_span',
                    'cl_status'
                ],
                'integer'
            ],
            [
                [
                    'cl_name'
                ],
                'string',
                'max' => 100
            ],
            [
                [
                    'cl_audit_method'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => AuditMethods::className(),
                'targetAttribute' => [
                    'cl_audit_method' => 'audit_method_id'
                ]
            ],
            [
                [
                    'cl_department_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => Departments::className(),
                'targetAttribute' => [
                    'cl_department_id' => 'department_id'
                ]
            ]
            // [['cl_frequency_duration'], 'exist', 'skipOnError' => true, 'targetClass' => Interval::className(), 'targetAttribute' => ['cl_frequency_duration' => 'interval_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'checklist_id' => Yii::t('app', 'Checklist'),
            'cl_name' => Yii::t('app', 'Checklist Name'),
            'cl_audit_type' => Yii::t('app', 'Audit Type'),
            'cl_audit_method' => Yii::t('app', 'Audit Method'),
            'cl_department_id' => Yii::t('app', 'Department '),
            'cl_frequency_value' => Yii::t('app', 'Frequency'),
            'cl_audit_span' => Yii::t('app', 'Audit Span'),
            'cl_status' => Yii::t('app', 'Status')
        ];
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClAuditMethod()
    {
        return $this->hasOne(AuditMethods::className(), [
            'audit_method_id' => 'cl_audit_method'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClDepartment()
    {
        return $this->hasOne(Departments::className(), [
            'department_id' => 'cl_department_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClFrequencyDuration()
    {
        return $this->hasOne(Interval::className(), [
            'interval_id' => 'cl_frequency_duration'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Questions::className(), [
            'q_checklist_id' => 'checklist_id'
        ]);
    }

    public static function getCheckListQuestionsCount($checklist_id)
    {
        return Questions::find()->where([
            'q_checklist_id' => $checklist_id,
            'is_deleted' => 0
        ])->count();
    }

    public static function getCheckListAuditType($checklist_id)
    {
        return self::find()->select('cl_audit_type')
            ->where([
                'checklist_id' => $checklist_id
            ])
            ->one();
    }
    public static function getCheckListAcrossSectionQuestionsCount($checklist_id)
    {
        return Questions::find()->where([
            'q_checklist_id' => $checklist_id,
            'is_deleted' => 0
        ])->groupBy('q_text')->count();
    }
}
