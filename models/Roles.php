<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%roles}}".
 *
 * @property integer $role_id
 * @property string $role_name
 * @property string $role_main
 * @property string $role_feature_access_list
 * @property string $role_properties_access_list
 *
 * @property UserInfo[] $userInfos
 */
class Roles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%roles}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_name'], 'required'],
            [['role_feature_access_list', 'role_properties_access_list'], 'string'],
            [['role_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role_id' => Yii::t('app', 'Role ID'),
            'role_name' => Yii::t('app', 'Role Name'),
            'role_feature_access_list' => Yii::t('app', 'Role Feature Access List'),
            'role_properties_access_list' => Yii::t('app', 'Role Properties Access List'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasMany(User::className(), ['role_id' => 'role_id']);
    }
    public static function getRoleWithName($decryptedRole)
    {
        return  self::find()->where(['role_main' => $decryptedRole])->one();
    }
}
