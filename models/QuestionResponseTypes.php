<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%question_response_types}}".
 *
 * @property integer $response_type_id
 * @property string $response_name
 *
 * @property Questions[] $questions
 */
class QuestionResponseTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%question_response_types}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['response_name'], 'required'],
            [['response_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'response_type_id' => Yii::t('app', 'Response Type ID'),
            'response_name' => Yii::t('app', 'Response Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Questions::className(), ['q_response_type' => 'response_type_id']);
    }
}
