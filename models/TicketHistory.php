<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%ticket_history}}".
 *
 * @property integer $ticket_history_id
 * @property integer $ticket_id
 * @property string $ticket_message
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Tickets $ticket
 */
class TicketHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ticket_history}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id', 'ticket_message'], 'required'],
            [['ticket_id', 'created_by', 'updated_by'], 'integer'],
            [['ticket_message'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tickets::className(), 'targetAttribute' => ['ticket_id' => 'ticket_id']],
        ];
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        parent::init();
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ticket_history_id' => Yii::t('app', 'Ticket History ID'),
            'ticket_id' => Yii::t('app', 'Ticket ID'),
            'ticket_message' => Yii::t('app', 'Ticket Message'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Tickets::className(), ['ticket_id' => 'ticket_id']);
    }
}
