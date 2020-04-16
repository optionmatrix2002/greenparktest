<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%locations}}".
 *
 * @property integer $location_id
 * @property integer $location_city_id
 * @property integer $location_state_id
 * @property string $location_description
 * @property integer $created_by
 * @property integer $modified_by
 * @property string $created_date
 * @property string $modified_date
 *
 * @property Hotels[] $hotels
 * @property Cities $locationCity
 * @property User $createdBy
 * @property User $modifiedBy
 * @property States $locationState
 * @property UserInfo[] $userInfos
 */
class Locations extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%locations}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'location_city_id',
                    'location_state_id'
                ],
                'required'
            ],
            [
                [
                    'location_city_id',
                    'location_state_id',
                    'created_by',
                    'modified_by'
                ],
                'integer'
            ],
            [
                [
                    'location_city_id'
                ],
                'unique',
                'message' => "Location already exists",
                'targetClass' => self::className(),
                'filter' => [
                    '=',
                    'is_deleted',
                    0
                ]
            ],
            [
                [
                    'location_description'
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
                    'location_city_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => Cities::className(),
                'targetAttribute' => [
                    'location_city_id' => 'id'
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
            ],
            [
                [
                    'location_state_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => States::className(),
                'targetAttribute' => [
                    'location_state_id' => 'id'
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
            'location_id' => Yii::t('app', 'Location ID'),
            'location_city_id' => Yii::t('app', 'City'),
            'location_state_id' => Yii::t('app', 'State'),
            'location_description' => Yii::t('app', 'Description'),
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
    public function getHotels()
    {
        return $this->hasMany(Hotels::className(), [
            'location_id' => 'location_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocationCity()
    {
        return $this->hasOne(Cities::className(), [
            'id' => 'location_city_id'
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
    public function getLocationState()
    {
        return $this->hasOne(States::className(), [
            'id' => 'location_state_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserInfos()
    {
        return $this->hasMany(UserInfo::className(), [
            'ui_location_id' => 'location_id'
        ]);
    }

    /**
     */
    public static function getLocations()
    {
        return self::find()->asArray->all();
    }
}
