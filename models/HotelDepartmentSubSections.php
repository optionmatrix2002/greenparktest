<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "tbl_gp_hotel_department_sub_sections".
 *
 * @property integer $id
 * @property integer $hotel_id
 * @property integer $department_id
 * @property integer $section_id
 * @property integer $sub_section_id
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property Hotels $hotel
 * @property Departments $department
 * @property Sections $section
 * @property SubSections $subSection
 */
class HotelDepartmentSubSections extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_gp_hotel_department_sub_sections';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hotel_id', 'department_id', 'section_id', 'sub_section_id'], 'required'],
            [['hotel_id', 'department_id', 'section_id', 'sub_section_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'user_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'user_id']],
            [['hotel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Hotels::className(), 'targetAttribute' => ['hotel_id' => 'hotel_id']],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Departments::className(), 'targetAttribute' => ['department_id' => 'department_id']],
            [['section_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sections::className(), 'targetAttribute' => ['section_id' => 'section_id']],
            [['sub_section_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubSections::className(), 'targetAttribute' => ['sub_section_id' => 'sub_section_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'hotel_id' => Yii::t('app', 'Hotel ID'),
            'department_id' => Yii::t('app', 'Department ID'),
            'section_id' => Yii::t('app', 'Section ID'),
            'sub_section_id' => Yii::t('app', 'Sub Section ID'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['user_id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['user_id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHotel()
    {
        return $this->hasOne(Hotels::className(), ['hotel_id' => 'hotel_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Departments::className(), ['department_id' => 'department_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSection()
    {
        return $this->hasOne(Sections::className(), ['section_id' => 'section_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubSection()
    {
        return $this->hasOne(SubSections::className(), ['sub_section_id' => 'sub_section_id']);
    }

    public static function getHotelAndDepartment($subsection_id, $hotelId)
    {
        return self::find()->where(['sub_section_id' => $subsection_id, 'hotel_id' => $hotelId])->one();
    }
}
