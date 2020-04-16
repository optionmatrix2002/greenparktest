<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%interval}}".
 *
 * @property integer $interval_id
 * @property string $interval_name
 *
 * @property Checklists[] $checklists
 */
class Interval extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%interval}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['interval_name'], 'required'],
            [['interval_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'interval_id' => Yii::t('app', 'Interval ID'),
            'interval_name' => Yii::t('app', 'Interval Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChecklists()
    {
        return $this->hasMany(Checklists::className(), ['cl_frequency_duration' => 'interval_id']);
    }
}
