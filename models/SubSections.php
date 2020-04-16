<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%sub_sections}}".
 *
 * @property integer $sub_section_id
 * @property integer $ss_section_id
 * @property string $ss_subsection_name
 * @property string $ss_subsection_remarks
 * @property integer $created_by
 * @property integer $modified_by
 * @property string $created_date
 * @property string $modified_date
 *
 * @property Questions[] $questions
 * @property Sections $ssSection
 * @property User $createdBy
 * @property User $modifiedBy
 */
class SubSections extends \yii\db\ActiveRecord
{

    public $department_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sub_sections}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'ss_section_id',
                    'ss_subsection_name',
                    //'ss_subsection_remarks'
                ],
                'required'
            ],
            [
                [
                    'department_id'
                ],
                'required',
                'on' => 'mastredatasubsection'
            ],
            [
                [
                    'ss_section_id',
                    'created_by',
                    'modified_by'
                ],
                'integer'
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
                    'ss_subsection_name'
                ],
                'string',
                'max' => 100
            ],
            [
                [
                    'ss_subsection_name'
                ],
                'unique',
                'targetAttribute' => [
                    'ss_subsection_name',
                    'ss_section_id'
                ],
                'message' => 'The combination Sub Section and Section has already been taken.',
                'filter' => [
                    '=',
                    'is_deleted',
                    0
                ]
            ],
            [
                [
                    'ss_subsection_remarks'
                ],
                'string',
                'max' => 200
            ],
            [
                [
                    'ss_section_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => Sections::className(),
                'targetAttribute' => [
                    'ss_section_id' => 'section_id'
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
            'sub_section_id' => Yii::t('app', 'Subsection'),
            'ss_section_id' => Yii::t('app', 'Section'),
            'ss_subsection_name' => Yii::t('app', 'Subsection'),
            'ss_subsection_remarks' => Yii::t('app', 'Description'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_by' => Yii::t('app', 'Modified By'),
            'created_date' => Yii::t('app', 'Created Date'),
            'modified_date' => Yii::t('app', 'Modified Date'),
            'is_active' => Yii::t('app', 'Status'),
            'department_id' => Yii::t('app', 'Department')
        ];
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Questions::className(), [
            'q_sub_section' => 'sub_section_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSsSection()
    {
        return $this->hasOne(Sections::className(), [
            'section_id' => 'ss_section_id'
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

    public static function getList()
    {
        return self::find()->where([
            'is_deleted' => 0
        ])
            ->asArray()
            ->all();
    }
}
