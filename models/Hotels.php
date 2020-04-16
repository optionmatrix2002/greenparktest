<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%hotels}}".
 *
 * @property integer $hotel_id
 * @property integer $location_id
 * @property string $hotel_name
 * @property string $hotel_phone_number
 * @property string $hotel_address
 * @property integer $hotel_status
 * @property integer $created_by
 * @property integer $modified_by
 * @property string $created_date
 * @property string $modified_date
 *
 * @property Departments[] $departments
 * @property User $createdBy
 * @property User $modifiedBy
 * @property Locations $location
 * @property UserInfo[] $userInfos
 */
class Hotels extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hotels}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['location_id', 'hotel_name', 'hotel_phone_number', 'hotel_address'], 'required'],
            [['location_id', 'hotel_status', 'created_by', 'modified_by'], 'integer'],
            [['hotel_address'], 'string'],
            [['created_date', 'modified_date'], 'safe'],
            [['hotel_name'], 'string', 'max' => 100],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'user_id']],
            [['modified_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['modified_by' => 'user_id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Locations::className(), 'targetAttribute' => ['location_id' => 'location_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hotel_id' => Yii::t('app', 'Hotel ID'),
            'location_id' => Yii::t('app', 'Location'),
            'hotel_name' => Yii::t('app', 'Hotel Name'),
            'hotel_phone_number' => Yii::t('app', 'Phone Number'),
            'hotel_address' => Yii::t('app', 'Address'),
            'hotel_status' => Yii::t('app', 'Hotel Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_by' => Yii::t('app', 'Modified By'),
            'created_date' => Yii::t('app', 'Created Date'),
            'modified_date' => Yii::t('app', 'Modified Date'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartments()
    {
        return $this->hasMany(Departments::className(), ['department_hotel_id' => 'hotel_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['user_id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedBy()
    {
        return $this->hasOne(User::className(), ['user_id' => 'modified_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Locations::className(), ['location_id' => 'location_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInfos()
    {
        return $this->hasMany(UserInfo::className(), ['ui_hotel_id' => 'hotel_id']);
    }
}
