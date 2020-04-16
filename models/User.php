<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;
use phpDocumentor\Reflection\Types\This;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $user_id
 * @property string $email
 * @property string $password_hash
 * @property string $auth_token
 * @property integer $is_email_verified
 * @property string $password_requested_date
 * @property string $last_login_time
 * @property string $created_date
 * @property string $modified_date
 *
 * @property UserInfo[] $userInfos
 */
class User extends ActiveRecord implements IdentityInterface
{

    public $departmentId;

    public $hoteld;

    public $hotelsList = [];

    public $deparmentList = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'email',
                    'password_hash',
                    'first_name',
                    'last_name',
                    'user_type',
                    'is_active',
                    'role_id',
                    'phone'
                ],
                'required'
            ],

            [
                [
                    'is_email_verified',
                    'user_type',
                    'is_active',
                    'role_id'
                    // 'phone'
                ],
                'integer'
            ],
            [
                [
                    'password_requested_date',
                    'last_login_time',
                    'created_date',
                    'modified_date',
                    'created_by',
                    'modified_by'
                ],
                'safe'
            ],
            [
                [
                    'email'
                ],
                'string',
                'max' => 100
            ],
            [
                [
                    'email'
                ],
                'email'
            ],

            [
                [
                    'email'
                ],
                'unique',
                'message' => 'A user with this email already exists, please enter a different email id',
                'targetClass' => self::className(),
                'filter' => [
                    '=',
                    'is_deleted',
                    0
                ]
            ],
            [
                [
                    'first_name',
                    'last_name'
                ],
                'match',
                'pattern' => '/^[a-zA-Z\s]+$/'
            ],
            [
                [
                    'password_hash',
                    'auth_token'
                ],
                'string',
                'max' => 200
            ],
            [
                [
                    'confirmation_token'
                ],
                'string',
                'max' => 250
            ],
            [['phone'], 'string', 'min' => 10, 'tooShort' => '{attribute} should be at least 10 digits'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'User ID'),
            'email' => Yii::t('app', 'Email'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'auth_token' => Yii::t('app', 'Auth Token'),
            'is_email_verified' => Yii::t('app', 'Is Email Verified'),
            'password_requested_date' => Yii::t('app', 'Password Requested Date'),
            'last_login_time' => Yii::t('app', 'Last Login Time'),
            'created_date' => Yii::t('app', 'Created Date'),
            'modified_date' => Yii::t('app', 'Modified Date'),
            'is_active' => Yii::t('app', 'Status'),
            'role_id' => Yii::t('app', 'Role')
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
                'createdAtAttribute' => 'created_date',
                'updatedAtAttribute' => 'modified_date',
                'value' => date('Y-m-d H:i:s')
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'modified_by',
                'value' => isset(Yii::$app->user->id) ? Yii::$app->user->id : 1
            ]
        ];
    }

    /**
     *
     * @return ActiveQuery
     */
    public function getUserInfos()
    {
        return $this->hasMany(UserInfo::className(), [
            'ui_user_id' => 'user_id'
        ]);
    }

    /**
     *
     * @return ActiveQuery
     */
    public function getIndividualUserInfos()
    {
        return $this->hasOne(UserInfo::className(), [
            'ui_user_id' => 'user_id'
        ]);
    }

    /**
     * Validates password
     *
     * @param string $password
     *            password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    public static function findByUsername($email)
    {
        return self::findOne([
            'email' => strtolower($email),
            'is_deleted' => 0,
            'is_active' => 1
        ]);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne([
            'auth_token' => $token, 'is_active' => 1, 'is_deleted' => 0
        ]);
    }

    public function getId()
    {
        return $this->user_id;
    }

    public function getAuthKey()
    {
        return $this->auth_token;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function generateKey()
    {
        return Yii::$app->getSecurity()->generateRandomString(30);
    }

    /*
     * Generate password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        return $this->password_hash = \Yii::$app->security->generatePasswordHash($password);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUiRole()
    {
        return $this->hasOne(Roles::className(), [
            'role_id' => 'role_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserLocations()
    {
        return $this->hasMany(UserLocations::className(), [
            'user_id' => 'user_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserHotels()
    {
        return $this->hasMany(UserHotels::className(), [
            'user_id' => 'user_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserDepartments()
    {
        return $this->hasMany(UserDepartments::className(), [
            'user_id' => 'user_id'
        ]);
    }
    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserHodDepartments() {
        return $this->hasMany(UserDepartments::className(), [
            'user_id' => 'user_id'
        ])->where(['is_hod'=>1]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUiUserType()
    {
        return $this->hasOne(UserTypes::className(), [
            'user_type_id' => 'user_type'
        ]);
    }

    /**
     *
     * @return string
     */
    public function getUserHotelsData()
    {
        if (!$this->hotelsList) {
            $list = Hotels::find()->asArray()->all();
            $this->hotelsList = ArrayHelper::map($list, 'hotel_id', 'hotel_name');
        }

        $hotels = ArrayHelper::getColumn($this->userHotels, 'hotel_id');
        $hotelsArray = [];
        foreach ($hotels as $hotel) {
            if (isset($this->hotelsList[$hotel])) {
                $hotelsArray[] = $this->hotelsList[$hotel];
            }
        }
        return implode(',', $hotelsArray);
    }

    /**
     *
     * @return string
     */
    public function getUserDepartmentsData()
    {
        if (!$this->deparmentList) {
            $list = Departments::find()->asArray()->all();
            $this->deparmentList = ArrayHelper::map($list, 'department_id', 'department_name');
        }

        if (!$this->hotelsList) {
            $list = Hotels::find()->asArray()->all();
            $this->hotelsList = ArrayHelper::map($list, 'hotel_id', 'hotel_name');
        }

        $deps = ArrayHelper::getColumn($this->userDepartments, 'hotel_department_id');
        $depart_id_list = HotelDepartments::find()->select(['department_id', 'hotel_id'])
            ->where([
                'id' => $deps
            ])
            ->asArray()
            ->all();
        $depString = [];
        foreach ($depart_id_list as $dep) {
            if (isset($this->deparmentList[$dep['department_id']])) {
                $depString[] = $this->hotelsList[$dep['hotel_id']] . '-' . $this->deparmentList[$dep['department_id']];
            }
        }
        return implode(',', $depString);
    }

    /**
     * @param $query
     * @return mixed
     */
    public static function getUserLocationRelationData($query, $type = '')
    {
        $userId = Yii::$app->user->identity->id;


        $userType = Yii::$app->user->identity->user_type;
        if ($userType == 1) {
            return $query;
        }

        $return = self::getUserAssingemnts();

        $userHotels = $return['userHotels'];
        $userdepartments = $return['userdepartments'];

        $userHotels = $userHotels ? $userHotels : 0;
        $userdepartments = $userdepartments ? $userdepartments : 0;

        if ($type == 'audit') {
            $query->andFilterWhere([Audits::tableName() . '.hotel_id' => $userHotels]);
            $query->joinWith(['hotelDepartment.userDepartment']);
            $query->andFilterWhere([UserDepartments::tableName() . '.user_id' => $userId]);
        } else if ($type == 'auditParent') {
            $query->andFilterWhere([Audits::tableName() . '.hotel_id' => $userHotels]);
            $query->joinWith(['audit.hotelDepartment.userDepartment']);
            $query->andFilterWhere([UserDepartments::tableName() . '.user_id' => $userId]);
        }elseif ($type == 'checklist') {
            $query->andFilterWhere(['cl_department_id' => $userdepartments]);
        } else {
            $query->andFilterWhere([Tickets::tableName() . '.hotel_id' => $userHotels]);
            $query->joinWith(['hotelDepartment.userDepartment']);
            $query->andFilterWhere([UserDepartments::tableName() . '.user_id' => $userId]);
        }

        return $query;

    }

    /**
     *
     */
    public static function getUserAssingemnts($id = null)
    {
        $userId = $id ? $id : Yii::$app->user->identity->id;
        $userHotels = UserHotels::find()->select(['hotel_id'])->where(['user_id' => $userId])->asArray()->all();
        $userHotels = ArrayHelper::getColumn($userHotels, 'hotel_id');

        $userdepartments = UserDepartments::find()->joinWith(['department'])->where(['user_id' => $userId])->asArray()->all();
        $userdepartments = ArrayHelper::getColumn($userdepartments, 'department.department_id');

        $return['userHotels'] = $userHotels;
        $return['userdepartments'] = $userdepartments;
        return $return;
    }

    public static function getUserHotelAndDepartment($hotel_id)
    {
        $deparments = HotelDepartments::find()->joinWith([
            'department' => function ($query) {
                $query->select([
                    'department_name',
                    'department_id'
                ]);
            },
            'hotel'
        ])
            ->where([
                HotelDepartments::tableName() . '.is_deleted' => 0
            ])
            ->andWhere(['IN', HotelDepartments::tableName() . '.id', $hotel_id])
            ->select([
                HotelDepartments::tableName() . '.id',
                HotelDepartments::tableName() . '.department_id',
                HotelDepartments::tableName() . '.hotel_id'
            ])
            ->asArray()
            ->all();
        $resultArray = [];

        if (!empty($deparments)) {
            foreach ($deparments as $department) {
                $list = [];
                if (isset($department['department'])) {
                    $list['id'] = $department['id'];
                    $list['name'] = $department['hotel']['hotel_name'] . '-' . $department['department']['department_name'];
                    $resultArray[] = $list;
                }
            }
        } else {
            $list['id'] = '';
            $list['name'] = 'No Departments';
            $resultArray[] = $list;
        }
        return $resultArray;
    }

    public static function getValidUserCount($user_emailid)
    {
        return self::find()->select('email')->where(['email' => $user_emailid, 'is_deleted' => 0])->count();
    }
}
