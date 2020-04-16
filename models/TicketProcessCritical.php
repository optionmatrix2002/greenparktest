<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%ticket_process_critical}}".
 *
 * @property int $ticket_process_critical_id
 * @property int $ticket_id
 * @property int $prob_module_id
 * @property string $root_cause
 * @property string $improvement_plan
 * @property int $improve_plan_module_id
 * @property int $stop_notifications_until_date
 * @property int $created_by
 
 * @property string $created_at
 * @property int $modified_by
 * @property string $modified_at
 *
 * @property Tickets $ticket
 * @property ProcessCriticalPreferences $improvePlanModule
 * @property ProcessCriticalPreferences $probModule
 */
class TicketProcessCritical extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ticket_process_critical}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'prob_module_id', 'root_cause', 'improvement_plan', 'improve_plan_module_id', 'created_by'], 'required'],
            [['ticket_id', 'prob_module_id', 'improve_plan_module_id', 'created_by', 'modified_by'], 'integer'],
            [['created_at', 'modified_at','stop_notifications_until_date'], 'safe'],
            [['root_cause', 'improvement_plan'], 'string', 'max' => 1000],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tickets::className(), 'targetAttribute' => ['ticket_id' => 'ticket_id']],
            [['improve_plan_module_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProcessCriticalPreferences::className(), 'targetAttribute' => ['improve_plan_module_id' => 'critical_preference_id']],
            [['prob_module_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProcessCriticalPreferences::className(), 'targetAttribute' => ['prob_module_id' => 'critical_preference_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ticket_process_critical_id' => 'Ticket Process Critical ID',
            'ticket_id' => 'Ticket ID',
            'prob_module_id' => 'Problem Classification',
            'root_cause' => 'Root Cause',
            'improvement_plan' => 'Improvement Plan',
            'improve_plan_module_id' => 'Improvement Plan Classification',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'modified_by' => 'Modified By',
            'modified_at' => 'Modified At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Tickets::className(), ['ticket_id' => 'ticket_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImprovePlanModule()
    {
        return $this->hasOne(ProcessCriticalPreferences::className(), ['critical_preference_id' => 'improve_plan_module_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProbModule()
    {
        return $this->hasOne(ProcessCriticalPreferences::className(), ['critical_preference_id' => 'prob_module_id']);
    }
}
