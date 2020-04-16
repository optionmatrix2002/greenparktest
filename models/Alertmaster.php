<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_gp_alertmaster".
 *
 * @property integer $alert_id
 * @property string $alert_type
 *
 * @property RolealertAssignment[] $rolealertAssignments
 */
class Alertmaster extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_gp_alertmaster';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alert_type'], 'required'],
            [['alert_type'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'alert_id' => 'Alert ID',
            'alert_type' => 'Alert Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRolealertAssignments()
    {
        return $this->hasMany(RolealertAssignment::className(), ['alert_id' => 'alert_id']);
    }

    /**
     * For get AlertMasterWithAlertAssignment
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAlertMasterWithAlertAssignment($getRole_id)
    {
        return self::find()->alias('am')
            ->select('ramt.email_id,ramt.sms_id,ramt.notification_id,am.alert_id,am.alert_type,ramt.role_id')
            ->leftJoin('tbl_gp_rolealert_assignment as ramt', 'ramt.alert_id = am.alert_id')
            ->where(['ramt.role_id' => $getRole_id])
            ->asArray()
            ->all();
    }

    public static function getAlertMaster()
    {
        return self::find()->alias('am')
            ->select('am.alert_id,am.alert_type')
            ->asArray()
            ->all();
    }

    /**
     * @param $type
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getListOfAlertTypeUsers($alertId)
    {
        $users = self::find()
            ->select([User::tableName() . '.*', self::tableName() . '.alert_id'])
            ->joinWith(['rolealertAssignments.role.user'])
            ->where([self::tableName() . '.alert_id' => $alertId])
            ->andWhere([User::tableName() . '.is_deleted' => 0])
            ->andWhere([User::tableName() . '.is_active' => 1])
            ->asArray()
            ->all();
        $usersList = [];

        foreach ($users as $user) {
            $roles = $user['rolealertAssignments'];
            foreach ($roles as $role) {
                $usersData = ArrayHelper::getValue($role, 'role.user');
                foreach ($usersData as $user) {
                    if (!$user['is_deleted']) {
                        $user['emailTrigger'] = $role['email_id'];
                        $user['smsTrigger'] = $role['sms_id'];
                        $user['pushNotificationTrigger'] = $role['notification_id'];
                        $usersList[$user['user_id']] = $user;
                    }
                }
            }
        }
        return $usersList;
    }
}
