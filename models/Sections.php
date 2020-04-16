<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%sections}}".
 *
 * @property integer $section_id
 * @property integer $s_department_id
 * @property string $s_section_name
 * @property string $s_section_remarks
 * @property integer $created_by
 * @property integer $modified_by
 * @property string $created_date
 * @property string $modified_date
 *
 * @property Questions[] $questions
 * @property User $createdBy
 * @property User $modifiedBy
 * @property SubSections[] $subSections
 */
class Sections extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sections}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    's_department_id',
                    's_section_name'
                ],
                'required'
            ],
            [
                [
                    's_department_id',
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
                    's_section_name'
                ],
                'string',
                'max' => 100
            ],
            [
                [
                    's_section_name'
                ],
                'unique',
                'targetAttribute' => [
                    's_section_name',
                    's_department_id'
                ],
                'message' => 'The combination Section and Department has already been taken.',
                'filter' => [
                    '=',
                    'is_deleted',
                    0
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
                    's_section_remarks'
                ],
                'string',
                'max' => 200
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
            'section_id' => Yii::t('app', 'Section '),
            's_department_id' => Yii::t('app', ' Department '),
            's_section_name' => Yii::t('app', 'Section '),
            's_section_remarks' => Yii::t('app', 'Description'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_by' => Yii::t('app', 'Modified By'),
            'created_date' => Yii::t('app', 'Created Date'),
            'modified_date' => Yii::t('app', 'Modified Date')
        
        ];
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Questions::className(), [
            'q_section' => 'section_id'
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
    public function getSubSections()
    {
        return $this->hasMany(SubSections::className(), [
            'ss_section_id' => 'section_id'
        ]);
    }

    public function getDepartment()
    {
        return $this->hasOne(Departments::className(), [
            'department_id' => 's_department_id'
        ]);
    }

    /**
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getList()
    {
        return self::find()->where([
            'is_deleted' => 0
        ])
            ->asArray()
            ->all();
    }
}
