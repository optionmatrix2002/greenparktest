<?php

namespace app\controllers;

use app\components\AccessRule;
use app\models\AuthItem;
use app\models\Roles;
use app\models\User;
use app\models\search\RolesSearch;
use yii;
use yii\web\Controller;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use app\models\Alertmaster;
use app\models\RolealertAssignment;


class RolesController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'ruleConfig' => [
                'class' => AccessRule::className()
            ],
            'only' => [
                'add-role',
                'delete-role',
                'index',
                'load-permissions-by-role'
            ], // only be applied to
            'rules' => [
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('roles/add-role'),
                    'actions' => [
                        'add-new-role'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('roles/delete-role'),
                    'actions' => [
                        'delete-role'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],

                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('roles'),
                    'actions' => [
                        'index'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],

                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('roles/load-permissions-by-role'),
                    'actions' => [
                        'load-permissions-by-role'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ]

            ]
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        $searchModel = new RolesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->layout = 'dashboard_layout';
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionAdd()
    {
        $this->layout = 'dashboard_layout';
        return $this->render('add');
    }

    public function actionManageRole($id = '')
    {
        if ($id) {
            $decryptedRole = yii::$app->utils->decryptData($id);
            // $role = yii::$app->authManager->getRole($decryptedRole);
            $rolesModel = Roles::findOne([
                'role_main' => $decryptedRole
            ]);
        } else {
            $rolesModel = new Roles();
        }
        return $this->renderAjax("manage_role_form", [
            'rolesModel' => $rolesModel
        ]);
    }

    public function actionAddNewRole()
    {
        $output = [];
        $valid = false;
        $post = yii::$app->request->post();
        $modelRoleAlertAssignment = new RolealertAssignment();
        if ($post && isset($post['Roles'])) {
            $auth = yii::$app->authManager;
            $roleModel = Roles::findOne([
                'LOWER(role_name)' => strtolower($post['Roles']['role_name'])
            ]);
            $existingRole = '';//$auth->getRole(trim($post['Roles']['role_name']));
            if (!$existingRole && !$roleModel) {
                $transaction = yii::$app->db->beginTransaction();
                $roleModel = new Roles();
                $roleModel->attributes = $post['Roles'];
                $roleModel->role_main = $post['Roles']['role_name'];
                if ($roleModel->save()) {
                    $role = $auth->createRole($roleModel->role_main);
                    $role->description = yii::$app->utils->convertToSpacedString($roleModel->role_name);
                    if ($auth->add($role)) {

                        $getAlertMasterId = ArrayHelper::getColumn(Alertmaster::find()->all(), 'alert_id');

                        foreach ($getAlertMasterId as $childPermissionKey => $childPermissionValue) {
                            $modelRoleAlertAssignment->role_id = $roleModel->role_id;
                            $modelRoleAlertAssignment->alert_id = $childPermissionValue;
                            $modelRoleAlertAssignment->id = null; // primary key(auto increment id) id
                            $modelRoleAlertAssignment->isNewRecord = true;
                            $modelRoleAlertAssignment->email_id = 0;
                            $modelRoleAlertAssignment->sms_id = 0;
                            $modelRoleAlertAssignment->notification_id = 0;

                            if ($modelRoleAlertAssignment->save()) {

                                $valid = true;

                            }

                        }
                        if ($valid) {
                            $transaction->commit();
                            $output = [
                                'success' => 'Successfully Created'
                            ];
                        }

                    } else {
                        $transaction->rollBack();
                        $output = [
                            'error' => 'Failed to create role'
                        ];
                    }
                }
            } else {
                $output = [
                    'error' => 'This role name already exists'
                ];
            }
        }
        return json_encode($output);
    }

    public function actionUpdateRole($id = '')
    {
        $output = [];
        $post = yii::$app->request->post();
        if ($id && isset($post['Roles'])) {
            $decryptedRole = yii::$app->utils->decryptData($id);
            $role = yii::$app->authManager->getRole($decryptedRole);
            if ($role) {
                $rolesModel = Roles::findOne([
                    'LOWER(role_main)' => strtolower($role->name)
                ]);
                $existingRoleModel = Roles::find()->where([
                    'LOWER(role_name)' => strtolower($post['Roles']['role_name'])
                ])
                    ->andWhere('LOWER(role_main) != :posted_role_name', [
                        ':posted_role_name' => strtolower($rolesModel->role_main)
                    ])
                    ->one();
                if (!$existingRoleModel) {
                    $transaction = yii::$app->db->beginTransaction();
                    if ($rolesModel->role_name != $post['Roles']['role_name']) {
                        $rolesModel->role_name = $post['Roles']['role_name'];
                        if ($rolesModel->save()) {
                            $auth = AuthItem::findOne($rolesModel->role_main);
                            $auth->description = yii::$app->utils->convertToSpacedString($rolesModel->role_name);
                            if ($auth->save()) {
                                $transaction->commit();
                                $output = [
                                    'success' => 'Role Updated Successfully'
                                ];
                            }
                        }
                    } else {
                        $transaction->rollBack();
                        $output = [
                            'success' => 'Saved with same Role Name'
                        ];
                    }
                } else {
                    $output = [
                        'error' => 'Role aleady exists'
                    ];
                }
            } else {
                $output = [
                    'error' => 'Role Name not found'
                ];
            }
        } else {
            $output = [
                'error' => 'Error with Role Name'
            ];
        }
        return json_encode($output);
    }

    /**
     * @return string
     */
    public function actionLoadPermissionsByRole()
    {
        $post = yii::$app->request->post();
        if ($post && $post['role_token']) {
            $modelRoleAlertAssignment = new RolealertAssignment();
            $decryptedRole = yii::$app->utils->decryptData($post['role_token']);
            $rolePermissions = ArrayHelper::map(yii::$app->authManager->getPermissionsByRole($decryptedRole), 'name', 'name');
            //For get Role id
            $getRoleIdWithname = Roles::getRoleWithName($decryptedRole);
            $getRole_id = $getRoleIdWithname ? $getRoleIdWithname->role_id : '';

            $roleAssignments = RolealertAssignment::find()->where(['role_id' => $getRole_id])->asArray()->all();
            $roleAssignments = ArrayHelper::index($roleAssignments, 'alert_id');

            $roles = [];
            $alertMasterModelData = Alertmaster::getAlertMaster();
            foreach ($alertMasterModelData as $alert) {
                $alertMasterModel = [];
                $alertMasterModel['alert_id'] = $alert['alert_id'];
                $alertMasterModel['alert_type'] = $alert['alert_type'];

                $alertMasterModel['email_id'] = 0;
                $alertMasterModel['sms_id'] = 0;
                $alertMasterModel['notification_id'] = 0;
                if (isset($roleAssignments[$alert['alert_id']])) {
                    $roleAssign = $roleAssignments[$alert['alert_id']];
                    $alertMasterModel['email_id'] = $roleAssign['email_id'];
                    $alertMasterModel['sms_id'] = $roleAssign['sms_id'];
                    $alertMasterModel['notification_id'] = $roleAssign['notification_id'];
                }
                $roles[] = $alertMasterModel;
            }

            return $this->renderAjax('permissions_by_role', [
                'rolePermissions' => $rolePermissions,
                'permissions' => yii::$app->authManager->getPermissions(),
                'alertMasterModel' => $roles,
                'modelRoleAlertAssignment' => $modelRoleAlertAssignment,
                'encryptedRole' => $post['role_token']
            ]);
        }
    }

    public function actionSaveRoleAssignment($id = '')
    {

        $output = [];
        $valid = false;
        $modelRoleAlertAssignment = new RolealertAssignment();
        $post = yii::$app->request->post();

        $roleId = Yii::$app->utils->decryptData($post['role_id']);
        //For get Role id
        $getRoleIdWithname = Roles::getRoleWithName($roleId);
        $getRole_id = $getRoleIdWithname ? $getRoleIdWithname->role_id : '';


        if ($id) {
            $decryptedRole = yii::$app->utils->decryptData($id);
            $role = yii::$app->authManager->getRole($decryptedRole);
            $postedPermissions = [];
            if (isset($post['Permissions'])) {
                $postedPermissions = $post['Permissions'];
            }
            if ($role) {
                $transaction = yii::$app->db->beginTransaction();
                $permissionsList = yii::$app->authManager->getPermissionsByRole($decryptedRole);
                if ($permissionsList) {
                    foreach ($permissionsList as $permission) {
                        yii::$app->authManager->removeChild($role, yii::$app->authManager->getPermission($permission->name));
                    }
                }
                if (isset($post['Permissions'])) {
                    foreach ($post['Permissions'] as $childPermissionKey => $childPermissionValue) {
                        yii::$app->authManager->addChild($role, yii::$app->authManager->getPermission($childPermissionKey));
                    }
                }
                $newPermissionsList = yii::$app->authManager->getPermissionsByRole($decryptedRole);
                if (count($newPermissionsList) == count($postedPermissions)) {

                    //For delete previous AlertAssignment
                    Yii::$app
                        ->db
                        ->createCommand()
                        ->delete('tbl_gp_rolealert_assignment', ['role_id' => $getRole_id])
                        ->execute();

                    if ($post) {
                        foreach ($post['alert_id'] as $childPermissionKey => $childPermissionValue) {
                            $modelRoleAlertAssignment->role_id = $getRoleIdWithname->role_id;
                            $modelRoleAlertAssignment->alert_id = $childPermissionValue;
                            $modelRoleAlertAssignment->id = null; // primary key(auto increment id) id
                            $modelRoleAlertAssignment->isNewRecord = true;
                            $modelRoleAlertAssignment->email_id = isset($post['email_id']) ? (in_array($childPermissionValue, $post['email_id']) ? 1 : 0) : 0;
                            $modelRoleAlertAssignment->sms_id = isset($post['sms_id']) ? (in_array($childPermissionValue, $post['sms_id']) ? 1 : 0) : 0;
                            $modelRoleAlertAssignment->notification_id = isset($post['notification_id']) ? (in_array($childPermissionValue, $post['notification_id']) ? 1 : 0) : 0;

                            if ($modelRoleAlertAssignment->save()) {
                                $valid = true;
                            }
                        }
                    }
                    if ($valid) {
                        $transaction->commit();
                        $output = [
                            'success' => 'Changes saved successfully for Role'
                        ];
                    }
                } else {
                    $output = [
                        'success' => 'Failed to save changes for Role: ' . $role->description
                    ];
                    $transaction->rollBack();
                }
            } else {
                $output = [
                    'error' => 'Role details not found'
                ];
            }
        } else {
            $output = [
                'error' => "Invalid request"
            ];
        }
        return json_encode($output);


    }

    public function actionDeleteRole()
    {
        $post = yii::$app->request->post();
        $output = [];
        if ($post && $post['deletable_role_id']) {
            $decryptedRole = yii::$app->utils->decryptData($post['deletable_role_id']);
            $role = yii::$app->authManager->getRole($decryptedRole);
            $roleModel = Roles::findOne([
                'role_main' => $decryptedRole
            ]);

            $modelRoleWiseUserCount = User::find()->where([
                'role_id' => $roleModel->role_id,
                'is_deleted' => '0'
            ])->count();
            if ($modelRoleWiseUserCount == 0) {
                $modelRoleUpdate = Roles::updateAll([
                    'is_deleted' => 1,
                    'modified_by' => \Yii::$app->user->getId()
                ], ' role_id=' . $roleModel->role_id);
                if ($modelRoleUpdate) {
                    $output = [
                        'success' => 'Role deleted successfully for Role: ' . $roleModel->role_name
                    ];
                }
            } else {
                $output = [
                    'error' => 'Role cannot be deleted as it is assigned to user'
                ];
            }
        } else {
            $output = [
                'error' => "Invalid request"
            ];
        }
        return json_encode($output);
    }

}
