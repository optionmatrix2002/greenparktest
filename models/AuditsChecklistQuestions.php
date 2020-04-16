<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%audits_checklist_questions}}".
 *
 * @property integer $audits_checklist_questions_id
 * @property integer $audit_id
 * @property integer $checklist_id
 * @property integer $question_id
 * @property string $q_text
 * @property integer $q_section
 * @property integer $q_sub_section
 * @property integer $q_sub_section_is_dynamic
 * @property string $q_access_type
 * @property integer $q_priority_type
 * @property integer $q_response_type
 * @property string $options
 * @property integer $is_deleted
 *
 * @property Audits $audit
 * @property Checklists $checklist
 * @property Questions $question
 * @property QuestionPriorityTypes $qPriorityType
 * @property QuestionResponseTypes $qResponseType
 * @property Sections $qSection
 * @property SubSections $qSubSection
 */
class AuditsChecklistQuestions extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%audits_checklist_questions}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'audit_id',
                    'checklist_id',
                    'question_id',
                    'q_text',
                    'q_section',
                    'q_sub_section_is_dynamic',
                    'q_access_type',
                    'q_priority_type',
                    'q_response_type',
                    'options'
                ],
                'required'
            ],
            [
                [
                    'audit_id',
                    'checklist_id',
                    'question_id',
                    'q_section',
                    'q_sub_section_is_dynamic',
                    'q_priority_type',
                    'q_response_type',
                    'is_deleted',
                    'process_critical'
                ],
                'integer'
            ],
            [
                [
                    'q_text',
                    'q_access_type',
                    'options'
                ],
                'string'
            ],
            [
                [
                    'audit_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => AuditsSchedules::className(),
                'targetAttribute' => [
                    'audit_id' => 'audit_schedule_id'
                ]
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
                    'question_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => Questions::className(),
                'targetAttribute' => [
                    'question_id' => 'question_id'
                ]
            ],
            [
                [
                    'q_priority_type'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => QuestionPriorityTypes::className(),
                'targetAttribute' => [
                    'q_priority_type' => 'priority_type_id'
                ]
            ],
            [
                [
                    'q_response_type'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => QuestionResponseTypes::className(),
                'targetAttribute' => [
                    'q_response_type' => 'response_type_id'
                ]
            ],
            [
                [
                    'q_section'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => Sections::className(),
                'targetAttribute' => [
                    'q_section' => 'section_id'
                ]
            ]
            // [['q_sub_section'], 'exist', 'skipOnError' => true, 'targetClass' => SubSections::className(), 'targetAttribute' => ['q_sub_section' => 'sub_section_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'audits_checklist_questions_id' => Yii::t('app', 'Audits Checklist Questions ID'),
            'audit_id' => Yii::t('app', 'Audit ID'),
            'checklist_id' => Yii::t('app', 'Checklist ID'),
            'question_id' => Yii::t('app', 'Question ID'),
            'q_text' => Yii::t('app', 'Q Text'),
            'q_section' => Yii::t('app', 'Q Section'),
            'q_sub_section' => Yii::t('app', 'Q Sub Section'),
            'q_sub_section_is_dynamic' => Yii::t('app', 'Q Sub Section Is Dynamic'),
            'q_access_type' => Yii::t('app', 'Q Access Type'),
            'q_priority_type' => Yii::t('app', 'Q Priority Type'),
            'q_response_type' => Yii::t('app', 'Q Response Type'),
            'process_critical'=> Yii::t('app', 'Process Critical'),
            'options' => Yii::t('app', 'Options'),
            'is_deleted' => Yii::t('app', 'Is Deleted')
        ];
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScheduledAudit()
    {
        return $this->hasOne(AuditsSchedules::className(), [
            'audit_schedule_id' => 'audit_id'
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
        ])->viaTable('tbl_gp_audits_schedules', [
            'audit_schedule_id' => 'audit_id'
        ]);
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
    public function getQuestion()
    {
        return $this->hasOne(Questions::className(), [
            'question_id' => 'question_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQPriorityType()
    {
        return $this->hasOne(QuestionPriorityTypes::className(), [
            'priority_type_id' => 'q_priority_type'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQResponseType()
    {
        return $this->hasOne(QuestionResponseTypes::className(), [
            'response_type_id' => 'q_response_type'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQSection()
    {
        return $this->hasOne(Sections::className(), [
            'section_id' => 'q_section'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQSubSection()
    {
        return $this->hasOne(SubSections::className(), [
            'sub_section_id' => 'q_sub_section'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(Answers::className(), [
            'question_id' => 'audits_checklist_questions_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCheckListAnswers()
    {
        return $this->hasOne(Answers::className(), [
            'question_id' => 'audits_checklist_questions_id'
        ]);
    }

}
