<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%question_access_types}}".
 *
 * @property integer $access_type_id
 * @property string $access_name
 *
 * @property Questions[] $questions
 */
class QuestionAccessTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%question_access_types}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['access_name'], 'required'],
            [['access_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'access_type_id' => Yii::t('app', 'Access Type ID'),
            'access_name' => Yii::t('app', 'Access Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Questions::className(), ['q_access_type' => 'access_type_id']);
    }
}
