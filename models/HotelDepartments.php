<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\validators\EmailValidator;

/**
 * This is the model class for table "tbl_gp_hotel_departments".
 *
 * @property integer $id
 * @property integer $hotel_id
 * @property integer $department_id
 * @property string $configured_emails
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property Hotels $hotel
 * @property Departments $department
 */
class HotelDepartments extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_gp_hotel_departments';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [
                [
                    'hotel_id',
                    'department_id'
                ],
                'required'
            ],
            [
                [
                    'hotel_id',
                    'department_id',
                    'created_by',
                    'updated_by'
                ],
                'integer'
            ],
            [
                [
                    'created_at',
                    'updated_at',
                    'configured_emails'
                ],
                'safe'
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
            ],
            [
                [
                    'hotel_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => Hotels::className(),
                'targetAttribute' => [
                    'hotel_id' => 'hotel_id'
                ]
            ],
            [
                [
                    'department_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => Departments::className(),
                'targetAttribute' => [
                    'department_id' => 'department_id'
                ]
            ],
            ['configured_emails', 'checkEmailList']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'hotel_id' => Yii::t('app', 'Hotel ID'),
            'department_id' => Yii::t('app', 'Department ID'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At')
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
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

    public function checkEmailList($attribute, $params) {
        // print_r($params); die();
        $validator = new EmailValidator;
        $cleanemails = preg_replace('/\s+/', '', $this->configured_emails);
        //  print_r($cleanemails); die();
        $emails = is_array($cleanemails) ?: explode(',', $cleanemails);
        $emailCount = array_count_values($emails);

        foreach ($emails as $email) {
            if ($emailCount[$email] > 1) {
                $this->addError($attribute, "$email is duplicated for $emailCount[$email] times");
                return false;
            }
            $validator->validate($email) ?: $this->addError($attribute, "$email is not a valid email" );
        }
    }

    /**
     *
     * @return ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), [
                    'user_id' => 'created_by'
        ]);
    }

    /**
     *
     * @return ActiveQuery
     */
    public function getUpdatedBy() {
        return $this->hasOne(User::className(), [
                    'user_id' => 'updated_by'
        ]);
    }

    /**
     *
     * @return ActiveQuery
     */
    public function getHotel() {
        return $this->hasOne(Hotels::className(), [
                    'hotel_id' => 'hotel_id'
        ]);
    }

    /**
     *
     * @return ActiveQuery
     */
    public function getDepartment() {
        return $this->hasOne(Departments::className(), [
                    'department_id' => 'department_id'
        ]);
    }

    /**
     *
     * @return ActiveQuery
     */
    public function getUserDepartment() {
        return $this->hasOne(UserDepartments::className(), [
                    'hotel_department_id' => 'id'
        ]);
    }

    /**
     *
     * @return ActiveQuery
     */
    public function getUserDepartmentHod() {
        return $this->hasOne(UserDepartments::className(), [
                    'hotel_department_id' => 'id'
                ])->andOnCondition(['is_hod' => '1']);
    }

}
