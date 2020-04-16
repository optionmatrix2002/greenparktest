<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%auth_item_child}}".
 *
 * @property string $parent
 * @property string $child
 *
 * @property AuthItem $parent
 * @property AuthItem $child
 */
class AuthItemChild extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_item_child}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent', 'child'], 'required'],
            [['parent', 'child'], 'string', 'max' => 64],
            [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['parent' => 'name']],
            [['child'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['child' => 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parent' => Yii::t('app', 'Parent'),
            'child' => Yii::t('app', 'Child'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentNode()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'parent']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildNode()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'child']);
    }
}
