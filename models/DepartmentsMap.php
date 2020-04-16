<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%departments_map}}".
 *
 * @property integer $department_map_id
 * @property integer $dm_location_id
 * @property integer $dm_hotel_id
 * @property integer $dm_department_id
 *
 * @property Locations $dmLocation
 * @property Hotels $dmHotel
 * @property Departments $dmDepartment
 * @property Sections[] $sections
 */
class DepartmentsMap extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%departments_map}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dm_location_id', 'dm_hotel_id', 'dm_department_id'], 'required'],
            [['dm_location_id', 'dm_hotel_id', 'dm_department_id'], 'integer'],
            [['dm_location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Locations::className(), 'targetAttribute' => ['dm_location_id' => 'location_id']],
            [['dm_hotel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Hotels::className(), 'targetAttribute' => ['dm_hotel_id' => 'hotel_id']],
            [['dm_department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Departments::className(), 'targetAttribute' => ['dm_department_id' => 'department_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'department_map_id' => Yii::t('app', 'Department Map ID'),
            'dm_location_id' => Yii::t('app', 'Dm Location ID'),
            'dm_hotel_id' => Yii::t('app', 'Dm Hotel ID'),
            'dm_department_id' => Yii::t('app', 'Dm Department ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDmLocation()
    {
        return $this->hasOne(Locations::className(), ['location_id' => 'dm_location_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDmHotel()
    {
        return $this->hasOne(Hotels::className(), ['hotel_id' => 'dm_hotel_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDmDepartment()
    {
        return $this->hasOne(Departments::className(), ['department_id' => 'dm_department_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSections()
    {
        return $this->hasMany(Sections::className(), ['s_department_id' => 'department_map_id']);
    }
}
