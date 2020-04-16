<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%question_priority_types}}".
 *
 * @property integer $priority_type_id
 * @property string $priority_name
 *
 * @property Questions[] $questions
 */
class QuestionPriorityTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%question_priority_types}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['priority_name'], 'required'],
            [['priority_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'priority_type_id' => Yii::t('app', 'Priority Type ID'),
            'priority_name' => Yii::t('app', 'Priority Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Questions::className(), ['q_priority_type' => 'priority_type_id']);
    }
}
