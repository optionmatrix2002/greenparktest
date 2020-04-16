<?php
/**
 * 
 * @author Vinod Kumar Ravuri
 *
 */
namespace app\components;

use Yii;
use yii\web\HttpException;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

class AccessRule extends \yii\filters\AccessRule
{

    /**
     * @inheritdoc
     */
    protected function matchRole($user)
    {
        
        if (empty($this->roles)) {
            return true;
        }
        foreach ($this->roles as $role) {
            if ($role === '?') {
                if ($user->getIsGuest()) {
                    return true;
                    // return $this->redirect(\Yii::$app->urlManager->createUrl(['site/login']));
                }
            } elseif ($role === '@') {
                
                if (! $user->getIsGuest()) {
                    return true;
                }
                // Check if the user is logged in, and the roles match
            } elseif ($role == 'rbac') {
                
                if (Yii::$app->user->identity && Yii::$app->user->identity->user_type == 1) {
                    return true;
                }
                $roles = array_keys(\Yii::$app->authManager->getRolesByUser(\Yii::$app->user->id));
                if (! $roles) {
                    return false;
                }
                $permissionsList = array_keys(\Yii::$app->authManager->getPermissionsByRole($roles[0]));
                
                $requestedUrl = \Yii::$app->request->url;
                foreach ($permissionsList as $permission) {
                    if (strpos($requestedUrl, $permission) !== false) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}