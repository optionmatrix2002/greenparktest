<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%process_critical_preferences}}".
 *
 * @property int $critical_preference_id
 * @property int $module_id
 * @property string $module_option
 * @property int $stop_reminders
 * @property int $stop_escalations
 * @property string $created_at
 * @property int $created_by
 * @property string $modified_at
 * @property int $modified_by
 *
 * @property ProcessCriticalModule $module
 * @property TicketProcessCritical[] $ticketProcessCriticals
 * @property TicketProcessCritical[] $ticketProcessCriticals0
 */
class rootcause extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%root_cause}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['module_id', 'module_option', 'created_by'], 'required'],
            [['module_id', 'stop_reminders', 'stop_escalations', 'created_by', 'modified_by'], 'integer'],
            [['created_at', 'module_id','module_name','module_option','modified_at'], 'safe'],
            [['module_option'], 'string', 'max' => 100],
            [['module_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProcessCriticalModule::className(), 'targetAttribute' => ['module_id' => 'module_id']],
            [['module_option'],'unique','targetAttribute' => ['module_id','module_option'], 'message' => 'Option already taken'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'critical_preference_id' => 'Critical Preference ID',
            'module_id' => 'Module',
            'module_option' => 'Option',
            'stop_reminders' => 'Stop Reminders',
            'stop_escalations' => 'Stop Escalations',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'modified_at' => 'Modified At',
            'modified_by' => 'Modified By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(ProcessCriticalModule::className(), ['module_id' => 'module_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketProcessCriticals()
    {
        return $this->hasMany(TicketProcessCritical::className(), ['improve_plan_module_id' => 'critical_preference_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketProcessCriticals0()
    {
        return $this->hasMany(TicketProcessCritical::className(), ['prob_module_id' => 'critical_preference_id']);
    }
}
