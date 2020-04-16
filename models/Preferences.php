<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%preferences}}".
 *
 * @property integer $preferences_id
 * @property string $preferences_name
 * @property string $preferences_lable
 * @property string $preferences_value
 * @property string $preferences_description
 * @property string $preferences_type
 * @property string $preferences_options
 * @property integer $created_by
 * @property integer $update_by
 * @property string $created_at
 * @property string $update_at
 *
 * @property User $createdBy
 * @property User $updateBy
 */
class Preferences extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%preferences}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'preferences_name',
                    'preferences_value',
                    'preferences_description',
                    'preferences_type',
                    'created_by',
                    'update_by',
                    'created_at',
                    'update_at'
                ],
                'required'
            ],
            [
                [
                    'preferences_description',
                    'preferences_options'
                ],
                'string'
            ],
            [
                [
                    'created_by',
                    'update_by'
                ],
                'integer'
            ],
            [
                [
                    'created_at',
                    'update_at'
                ],
                'safe'
            ],
            [
                [
                    'preferences_name',
                    'preferences_value',
                    'preferences_type'
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
                    'update_by'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => [
                    'update_by' => 'user_id'
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
            'preferences_id' => Yii::t('app', 'Preferences ID'),
            'preferences_name' => Yii::t('app', 'Preferences Name'),
            'preferences_lable' => Yii::t('app', 'Preferences Lable'),
            'preferences_value' => Yii::t('app', 'Preferences Value'),
            'preferences_description' => Yii::t('app', 'Preferences Description'),
            'preferences_type' => Yii::t('app', 'Preferences Type'),
            'preferences_options' => Yii::t('app', 'Preferences Options'),
            'created_by' => Yii::t('app', 'Created By'),
            'update_by' => Yii::t('app', 'Update By'),
            'created_at' => Yii::t('app', 'Created At'),
            'update_at' => Yii::t('app', 'Update At')
        ];
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
    public function getUpdateBy()
    {
        return $this->hasOne(User::className(), [
            'user_id' => 'update_by'
        ]);
    }


    public static function getSelectAuditorReminder()
    {
        return $newValueArry = [
            "1" => "1",
            "2" => "2"
        ];
    }

    public static function getSelectEventReminder()
    {
        return $newValueArry = [
            "1" => "1",
            "2" => "2"
        ];
    }

    /**
     * Get preference value by name of the preference
     *
     * @param
     *            $name
     * @return mixed|string
     */
    public static function getPrefValByName($name)
    {
        $model = Preferences::find()->select([
            'preferences_value',
            'preferences_type'
        ])
            ->where([
                'preferences_name' => $name
            ])
            ->one();
        if ($model->preferences_type == 'multipleselect') {
            $result = json_decode($model->preferences_value);
        } else {
            $result = $model->preferences_value;
        }
        return $result;
    }

    public static function getSelectNewValueArry()
    {
        return $newValueArry = [
            "10" => "10",
            "20" => "20",
            "30" => "30",
            "40" => "40",
            "50" => "50"
        ];
    }

    public static function getSelectRatingSliderArry()
    {
        return $newValueArry = [
            "0" => "0",
            "1" => "1",
            "2" => "2",
            "3" => "3",
            "4" => "4",
            "5" => "5",
            "6" => "6",
            "7" => "7",
            "8" => "8",
            "9" => "9",
            "10" => "10"
        ];
    }
}
