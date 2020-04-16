<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "{{%ticket_comments}}".
 *
 * @property integer $ticket_comments_id
 * @property integer $ticket_id
 * @property string $ticket_comment
 * @property integer $ticket_status
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Tickets $ticket
 * @property User $createdBy
 * @property User $updatedBy
 */
class TicketComments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ticket_comments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id',  'ticket_comment_status',  'ticket_comment'], 'required'],
            [['ticket_id', 'ticket_comment_status', 'created_by', 'updated_by'], 'integer'],
            [['ticket_comment'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tickets::className(), 'targetAttribute' => ['ticket_id' => 'ticket_id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'user_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'user_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ticket_comments_id' => Yii::t('app', 'Ticket Comments ID'),
            'ticket_id' => Yii::t('app', 'Ticket ID'),
            'ticket_comment' => Yii::t('app', 'Ticket Comment'),
            'ticket_comment_status' => Yii::t('app', 'Ticket Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        //parent::init();
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
    public function getTicket()
    {
        return $this->hasOne(Tickets::className(), ['ticket_id' => 'ticket_id']);
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
}
