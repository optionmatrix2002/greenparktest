<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller {

    public $permissionList = [];

    public function actionInit() {
        /*      if (!$this->confirm("Are you sure? It will re-create permissions tree.")) {
          return self::EXIT_CODE_NORMAL;
          }
         */
        $auth = Yii::$app->authManager;
        $transaction = yii::$app->db->beginTransaction();
        $auth->removeAll();
        $permissionsList = require(__DIR__ . '/permissionsList.php');
        $this->createPermissions($permissionsList);

        if ($this->confirm("Are you sure you want to save changes?")) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }
        return self::EXIT_CODE_NORMAL;
    }

    private function createPermissions($permissionsArray) {
        if (is_array($permissionsArray)) {
            foreach ($permissionsArray as $permission => $subPermission) {
                $this->doMapRelationShips($permission, $subPermission);
            }
        }
    }

    private function doMapRelationShips($parent, $child = []) {
        $auth = Yii::$app->authManager;
        $createdParent = $auth->getPermission($parent);
        if (!$createdParent) {
            $createdParent = $auth->createPermission($parent);
            $createdParent->description = yii::$app->utils->convertToSpacedString($parent);
            $auth->add($createdParent);
        }
        foreach ($child as $subPermission => $subPermissionValue) {
            $createdChild = $auth->getPermission($subPermission);
            if (!$createdChild) {
                $createdChild = $auth->createPermission($subPermission);
                $createdChild->description = $subPermissionValue;
                $auth->add($createdChild);
            }
            $auth->addChild($createdParent, $createdChild);
            echo "Parent: " . $parent . " ----> Child: " . $subPermission . "\n";
        }
    }

}
