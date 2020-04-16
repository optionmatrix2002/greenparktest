<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%user_department}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $hotel_department_id
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * @property User $user
 * @property Departments $department
 * @property User $createdBy
 * @property User $updatedBy
 */
class UserDepartments extends \yii\db\ActiveRecord
{
    public  $hodDepartmentList = [];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_department}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'user_id',
                    'hotel_department_id'
                ],
                'required'
            ],
            [
                [
                    'user_id',
                    'hotel_department_id',
                    'created_by',
                    'updated_by'
                ],
                'integer'
            ],
            [
                [
                    'created_at',
                    'updated_at',
                    'is_hod',
                    'hodDepartmentList'
                ],
                'safe'
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
                    'hotel_department_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => HotelDepartments::className(),
                'targetAttribute' => [
                    'hotel_department_id' => 'id'
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'hotel_department_id' => 'Department',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At'
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
    public function getDepartment()
    {
        return $this->hasOne(HotelDepartments::className(), [
            'id' => 'hotel_department_id'
        ]);
    }

    public function getDepartmentData()
    {
        return $this->hasOne(Departments::className(), [
            'department_id' => 'hotel_department_id'
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

    public static function getHodUserWithHotelDepartment($user_id)
    {
        return self::find()->select('hotel_department_id')->where(['user_id' => $user_id, 'is_hod' => 1])->all();
    }
    
    public static function getDepartmentHead($hotelId,$departmentId){
        
        $ids = HotelDepartments::find()->joinWith('userDepartment as u')
        ->where([
            'hotel_id' => $hotelId,
            'department_id' => $departmentId,
        ])
        ->andWhere(['u.is_hod' => 1])
        ->asArray()
        ->all();
        $ids = ArrayHelper::getColumn($ids, 'id');
        $user = UserDepartments::find()->where(['is_hod' => 1, 'hotel_department_id' => $ids])->asArray()->one();
        
        $user_id = 1;
        if ($user) {
            $user_id = $user['user_id'];
        }
        return $user_id;
    }
}
