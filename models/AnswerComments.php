<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%answer_comments}}".
 *
 * @property integer $answer_comments_id
 * @property integer $answer_id
 * @property string $answer_comment
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Answers $answer
 * @property User $createdBy
 * @property User $updatedBy
 */
class AnswerComments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%answer_comments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['answer_id', 'answer_comment', 'created_by', 'updated_by'], 'required'],
            [['answer_id', 'created_by', 'updated_by'], 'integer'],
            [['answer_comment'], 'string'],
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
            'answer_comments_id' => Yii::t('app', 'Answer Comments ID'),
            'answer_id' => Yii::t('app', 'Answer ID'),
            'answer_comment' => Yii::t('app', 'Answer Comment'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
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
