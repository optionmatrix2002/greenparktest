<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "tbl_gp_answer_attachments".
 *
 * @property integer $answer_attachment_id
 * @property integer $answer_id
 * @property string $answer_attachment_path
 * @property integer $is_deleted
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Answers $answer
 * @property User $createdBy
 * @property User $updatedBy
 */
class AnswerAttachments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_gp_answer_attachments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['answer_id', 'answer_attachment_path'], 'required'],
            [['answer_id', 'is_deleted', 'created_by', 'updated_by'], 'integer'],
            [['answer_attachment_path'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['answer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Answers::className(), 'targetAttribute' => ['answer_id' => 'answer_id']],
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
            'answer_attachment_id' => Yii::t('app', 'Answer Attachment ID'),
            'answer_id' => Yii::t('app', 'Answer ID'),
            'answer_attachment_path' => Yii::t('app', 'Answer Attachment Path'),
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
    public function getAnswer()
    {
        return $this->hasOne(Answers::className(), ['answer_id' => 'answer_id']);
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
