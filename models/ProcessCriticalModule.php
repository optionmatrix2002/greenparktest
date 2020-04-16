<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%process_critical_module}}".
 *
 * @property int $module_id
 * @property string $module_name
 *
 * @property ProcessCriticalPreferences[] $processCriticalPreferences
 */
class ProcessCriticalModule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%process_critical_module}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['module_name'], 'required'],
            [['module_name'], 'safe'],
            [['module_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'module_id' => 'Module ID',
            'module_name' => 'Module Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcessCriticalPreferences()
    {
        return $this->hasMany(ProcessCriticalPreferences::className(), ['module_id' => 'module_id']);
    }
}
