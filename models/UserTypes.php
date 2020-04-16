<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_types}}".
 *
 * @property integer $user_type_id
 * @property string $ut_name
 * @property string $ut_description
 *
 * @property UserInfo[] $userInfos
 */
class UserTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_types}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ut_name'], 'required'],
            [['ut_name'], 'string', 'max' => 50],
            [['ut_description'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_type_id' => Yii::t('app', 'User Type ID'),
            'ut_name' => Yii::t('app', 'Ut Name'),
            'ut_description' => Yii::t('app', 'Ut Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInfos()
    {
        return $this->hasMany(UserInfo::className(), ['ui_user_type_id' => 'user_type_id']);
    }
}
