<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%questions}}".
 *
 * @property integer $question_id
 * @property string $q_text
 * @property integer $q_checklist_id
 * @property integer $q_section
 * @property integer $q_sub_section
 * @property integer $q_sub_section_is_dynamic
 * @property string $q_access_type
 * @property integer $q_priority_type
 * @property int $process_critical 1=>true,0=>false
 * @property integer $q_response_type
 *
 * @property Checklists $qChecklist
 * @property Sections $qSection
 * @property SubSections $qSubSection
 * @property QuestionPriorityTypes $qPriorityType
 * @property QuestionResponseTypes $qResponseType
 */
class Questions extends \yii\db\ActiveRecord
{
    
    // public $accesstype;
    
    public $checkedvalue;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%questions}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'q_text',
                    'q_checklist_id',
                    'q_section',
                    'q_priority_type',
                    'q_response_type'
                ],
                'required'
            ],
            [
                [
                    'q_text',
                    'q_access_type'
                ],
                'string'
            ],
            
            [
                'q_sub_section',
                'required',
                'when' => function ($model) {
                return $model->q_sub_section_is_dynamic ? false : true;
                },
                'whenClient' => "function(attribute, value) {
                      if($('#questions-q_sub_section_is_dynamic').is(':checked')){
                        return false;
                       }
                        return true;
                  }"
                    ],
                    [
                        [
                            'q_checklist_id',
                            'q_section',
                            'q_sub_section',
                            'q_sub_section_is_dynamic',
                            'q_priority_type',
                            'process_critical',
                            'q_response_type'
                        ],
                        'integer'
                    ],
                    [
                        [
                            'q_checklist_id'
                        ],
                        'exist',
                        'skipOnError' => true,
                        'targetClass' => Checklists::className(),
                        'targetAttribute' => [
                            'q_checklist_id' => 'checklist_id'
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
                    ],
                    [
                        [
                            'q_sub_section'
                        ],
                        'exist',
                        'skipOnError' => true,
                        'targetClass' => SubSections::className(),
                        'targetAttribute' => [
                            'q_sub_section' => 'sub_section_id'
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
                    ]
                    ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'question_id' => Yii::t('app', 'Question ID'),
            'q_text' => Yii::t('app', 'Question'),
            'q_checklist_id' => Yii::t('app', 'Q Checklist ID'),
            'q_section' => Yii::t('app', 'Section'),
            'q_sub_section' => Yii::t('app', 'Sub Section'),
            'q_sub_section_is_dynamic' => Yii::t('app', 'Sub Section Is Dynamic'),
            'q_access_type' => Yii::t('app', 'Access Type'),
            'q_priority_type' => Yii::t('app', 'Priority Type'),
            'process_critical' => 'Process Critical',
            'q_response_type' => Yii::t('app', 'Response Type')
            // 'accepttype' => Yii::t('app', 'Access Type'),
        ];
    }
    
    public function validateSubSection($attributes)
    {
        echo 'testrteweetet';
        if (! $this->q_sub_section_is_dynamic && ! $this->$attribute) {
            $this->addError($attribute, "Sub Section cannot be blank.");
        }
    }
    
    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQChecklist()
    {
        return $this->hasOne(Checklists::className(), [
            'checklist_id' => 'q_checklist_id'
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
    
    public static function getQuesionersFromSubsection($sectionId, $subSectionId)
    {
        return self::find()->where([
            'q_section' => $sectionId,
            'q_sub_section' => $subSectionId,
            'is_deleted' => 0
        ])->all();
    }
}
