<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\Json;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%ticket_attachments}}".
 *
 * @property integer $ticket_attachment_id
 * @property integer $ticket_id
 * @property string $ticket_attachment_path
 * @property integer $is_deleted
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Tickets $ticket
 * @property User $createdBy
 * @property User $updatedBy
 */
class TicketAttachments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ticket_attachments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id', 'ticket_attachment_path'], 'required',"on"=>'create_attachment'],
            [['ticket_id', 'is_deleted', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['ticket_attachment_path'], 'string', 'max' => 1000],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tickets::className(), 'targetAttribute' => ['ticket_id' => 'ticket_id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'user_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'user_id']],
            //For upload  validation
            ['ticket_attachment_path', 'file', 'extensions' => ['png', 'jpg', 'jpeg', 'docx', 'doc', 'xls', 'xlsx', 'pdf'], 'maxSize' => 1024 * 1024 * 5, 'tooBig' => 'Limit is 5 MB'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ticket_attachment_id' => Yii::t('app', 'Ticket Attachment ID'),
            'ticket_id' => Yii::t('app', 'Ticket ID'),
            'ticket_attachment_path' => Yii::t('app', 'Attachment'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
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

    public function saveTicketAttachments($attachment, $ticket_id)
    {
        $uploadedFile = UploadedFile::getInstanceByName($attachment);
        if ($uploadedFile) {
            $ext = pathinfo($uploadedFile->name, PATHINFO_EXTENSION);
            $file_name =  $uploadedFile->name;
            $complete_path = \Yii::$app->basePath . Yii::$app->params['attachments_save_url'] . $file_name;
            $path = $file_name;
            if ($uploadedFile->saveAs($complete_path)) {
                $ticketAttachments = new TicketAttachments();
                $ticketAttachments->ticket_id = $ticket_id;
                $ticketAttachments->ticket_attachment_path = $path;
                if ($ticketAttachments->save()) {
                    return [
                        'status' => true,
                        'message' => 'Attachment saved successfully'
                    ];
                } else {
                    return [
                        'status' => false,
                        'message' => Json::encode($ticketAttachments->getErrors())
                    ];
                }
            } else {
                return [
                    'status' => false,
                    'message' => 'Error saving the attachment'
                ];
            }
        } else {
            return [
                'status' => false,
                'message' => 'Attachment not received'
            ];
        }
    }

}
