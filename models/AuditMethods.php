<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%audit_methods}}".
 *
 * @property integer $audit_method_id
 * @property string $audit_method_name
 *
 * @property Checklists[] $checklists
 */
class AuditMethods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%audit_methods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['audit_method_name'], 'required'],
            [['audit_method_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'audit_method_id' => Yii::t('app', 'Audit Method ID'),
            'audit_method_name' => Yii::t('app', 'Audit Method Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChecklists()
    {
        return $this->hasMany(Checklists::className(), ['cl_audit_method' => 'audit_method_id']);
    }
}
