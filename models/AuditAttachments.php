<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gp_audit_attachments".
 *
 * @property integer $audit_attachment_id
 * @property integer $audit_schedule_id
 * @property string $audit_attachment_path
 * @property integer $is_deleted
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property AuditsSchedules $auditSchedule
 * @property User $createdBy
 * @property User $updatedBy
 */
class AuditAttachments extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_gp_audit_attachments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'audit_schedule_id',
                    'created_by',
                    'updated_by'
                ],
                'required'
            ],
            [
                [
                    'audit_schedule_id',
                    'is_deleted',
                    'created_by',
                    'updated_by'
                ],
                'integer'
            ],
            [
                [
                    'audit_attachment_path'
                ],
                'string'
            ],
            [
                [
                    'created_at',
                    'updated_at'
                ],
                'safe'
            ],
            [
                [
                    'audit_schedule_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => AuditsSchedules::className(),
                'targetAttribute' => [
                    'audit_schedule_id' => 'audit_schedule_id'
                ]
            ],
            [
                [
                    'created_by'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => [
                    'created_by' => 'user_id'
                ]
            ],
            [
                [
                    'updated_by'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => [
                    'updated_by' => 'user_id'
                ]
            ],
            
            // For upload validation


            ['audit_attachment_path', 'file', 'extensions' => ['png', 'jpg', 'jpeg', 'docx', 'doc', 'xls', 'xlsx', 'pdf'], 'maxSize' => 1024 * 1024 * 5, 'tooBig' => 'Limit is 5 MB'],
        
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'audit_attachment_id' => Yii::t('app', 'Audit Attachment'),
            'audit_schedule_id' => Yii::t('app', 'Audit Schedule ID'),
            'audit_attachment_path' => Yii::t('app', 'Audit Attachment'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At')
        ];
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditSchedule()
    {
        return $this->hasOne(AuditsSchedules::className(), [
            'audit_schedule_id' => 'audit_schedule_id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), [
            'user_id' => 'created_by'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), [
            'user_id' => 'updated_by'
        ]);
    }

    public static function getAuditAttachments($audit_schedule_id)
    {
        return self::find()->where([
            'audit_schedule_id' => $audit_schedule_id,
            'is_deleted' => 0
        ])->all();
    }
}
