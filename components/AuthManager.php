<?php
namespace app\components;

use yii;
use yii\rbac\DbManager;
use yii\rbac\Item;

class AuthManager extends DbManager
{

    public function checkPermissionAccess($rule)
    {
        if (Yii::$app->user->identity && Yii::$app->user->identity->user_type == 1) {
            return true;
        }
        
        $roles = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id));
        if ($roles) {
            $permissionsList = array_keys(\Yii::$app->authManager->getPermissionsByRole($roles[0]));
            
            if (in_array($rule, $permissionsList)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function removeAll()
    {
        $this->removeAllAssignments();
        $this->db->createCommand()
            ->delete($this->itemChildTable)
            ->execute();
        $this->db->createCommand()
            ->delete($this->itemTable, 'type = ' . Item::TYPE_PERMISSION)
            ->execute();
        $this->db->createCommand()
            ->delete($this->ruleTable)
            ->execute();
        $this->invalidateCache();
    }
}