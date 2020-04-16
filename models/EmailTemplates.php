<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%email_templates}}".
 *
 * @property int $template_id
 * @property int $email_type
 * @property string $email_content
 * @property int $created_by
 * @property string $created_at
 * @property int $modified_by
 * @property string $modified_at
 */
class EmailTemplates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%email_templates}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email_type', 'email_content', 'created_by'], 'required'],
            [['email_type', 'created_by', 'modified_by'], 'integer'],
            [['email_content'], 'string'],
            [['created_at', 'modified_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'template_id' => 'Template ID',
            'email_type' => 'Email Type',
            'email_content' => 'Email Content',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'modified_by' => 'Modified By',
            'modified_at' => 'Modified At',
        ];
    }
}
