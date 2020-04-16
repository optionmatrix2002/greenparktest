<?php

namespace app\controllers;

use app\models\AuditsSchedules;
use Yii;
use app\models\User;
use app\models\search\UserSearch;
use app\models\Locations;
use app\models\Hotels;
use app\components\AccessRule;
use app\components\EmailsComponent;
use app\components\UtilsComponent;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use app\models\Departments;
use app\models\HotelDepartments;
use app\models\UserLocations;
use app\models\UserHotels;
use app\models\UserDepartments;
use yii\helpers\ArrayHelper;
use app\models\Tickets;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller {

    public $layout = 'dashboard_layout';
    public $phoneNumbermask = "999 9999999";

    /**
     *
     * {@inheritdoc}
     * @see \yii\base\Component::behaviors()
     */
    public function behaviors() {
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'ruleConfig' => [
                'class' => AccessRule::className()
            ],
            'only' => [
                'create',
                'update',
                'delete',
                'index', 'user-view', 'view'
            ], // only be applied to
            'rules' => [
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('user/create'),
                    'actions' => [
                        'create'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('user/update'),
                    'actions' => [
                        'update'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('user'),
                    'actions' => [
                        'index', 'user-view', 'view'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('user/delete'),
                    'actions' => [
                        'delete'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ]
            ]
        ];
        return $behaviors;
    }

    /**
     * Lists all User models.
     *
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single User model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate() {
        $valid = false;
        $model = new User();
        $userLocationsModel = new UserLocations();
        $userHotelsModel = new UserHotels();
        $userDepartmentsModel = new UserDepartments();

        if ($model->load(Yii::$app->request->post())) {


            $userLocationsModel->load(Yii::$app->request->post());
            $userHotelsModel->hotel_id = Yii::$app->request->post('UserHotels')['hotel_id'];
            $userDepartmentsModel->hotel_department_id = Yii::$app->request->post('UserDepartments')['hotel_department_id'];
            $userDepartmentsModel->hodDepartmentList = Yii::$app->request->post('UserDepartments')['hodDepartmentList'];

            $postInfo = Yii::$app->request->post();
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->auth_token = Yii::$app->getSecurity()->generateRandomString(30);
                $model->password_hash = Yii::$app->getSecurity()->generateRandomString(30);
                $confirmation_token = Yii::$app->getSecurity()->generateRandomString(30);
                $model->confirmation_token = $confirmation_token;

                if ($model->save()) {

                    $locations = $postInfo['UserLocations']['location_id'];
                    $this->saveLocations($locations, $model);

                    $hotels = $postInfo['UserHotels']['hotel_id'];
                    $this->saveHotels($hotels, $model);

                    $departments = $postInfo['UserDepartments']['hotel_department_id'];
                    $hodDepartments = $postInfo['UserDepartments']['hodDepartmentList'];
                    $this->saveDepartments($departments, $model, $hodDepartments);
                    $this->assignRole($model->role_id, $model->user_id);
                    $getUserId = Yii::$app->utils->encryptData($model->user_id);

                    $recipientMail = $model->email;
                    $link = '<a href="' . \Yii::$app->urlManager->createAbsoluteUrl('/site/set-password') . '?user_id=' . $getUserId . '&token=' . $confirmation_token . '">Click Here</a>';
                    $result = true;
                    $result = EmailsComponent::sendUserVerificationLinkEmail($model->first_name, $recipientMail, $link, $action = "set");
                    if ($result) {
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', 'User created successfully');
                        return $this->redirect([
                            '/user'
                        ]);
                    } else {
                        throw new \Exception('Error occurred while sending mail');
                    }
                } else {

                    return $this->render('create', [
                        'model' => $model,
                        'userLocationsModel' => $userLocationsModel,
                        'userHotelsModel' => $userHotelsModel,
                        'userDepartmentsModel' => $userDepartmentsModel,
                        'hotelsList' => [],
                        'departmentList' => []
                    ]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect([
                    '/user'
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'userLocationsModel' => $userLocationsModel,
                'userHotelsModel' => $userHotelsModel,
                'userDepartmentsModel' => $userDepartmentsModel,
                'hotelsList' => [],
                'departmentList' => []
            ]);
        }
    }

    /**
     */
    protected function saveLocations($locations, $model) {
        UserLocations::deleteAll([
            'user_id' => $model->user_id
        ]);
        foreach ($locations as $locationId) {
            $userLocationsModel = new UserLocations();
            $userLocationsModel->location_id = $locationId;
            $userLocationsModel->user_id = $model->user_id;

            if ($userLocationsModel->save()) {
                $userLocationsModel->location_id = NULL;
                $userLocationsModel->user_id = NULL;
            }
        }
    }

    /**
     */
    protected function saveHotels($hotels, $model) {
        UserHotels::deleteAll([
            'user_id' => $model->user_id
        ]);
        foreach ($hotels as $hotelId) {
            $userHotelsModel = new UserHotels();
            $userHotelsModel->user_id = $model->user_id;
            $userHotelsModel->hotel_id = $hotelId;
            $userHotelsModel->save();
        }
    }

    /**
     */
    protected function saveDepartments($departments, $model, $hodDepartments = []) {
        UserDepartments::deleteAll([
            'user_id' => $model->user_id
        ]);

        if ($hodDepartments) {
            UserDepartments::updateAll([
                'is_hod' => 0
            ], [
                'hotel_department_id' => $hodDepartments,
                'is_hod' => 1
            ]);
        }


        foreach ($departments as $departmentId) {
            $userDepartmentsModel = new UserDepartments();
            $userDepartmentsModel->isNewRecord = true;
            $userDepartmentsModel->user_id = $model->user_id;
            $userDepartmentsModel->hotel_department_id = $departmentId;
            $userDepartmentsModel->is_hod = ($hodDepartments && in_array($departmentId, $hodDepartments)) ? 1 : 0;
            $userDepartmentsModel->save();
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $valid = false;
        $model = $this->findModel(Yii::$app->utils->decryptData($id));
        $user_type = $model->user_type;
        $userLocaltionModels = $model->userLocations;
        $userHotelModels = $model->userHotels;
        $userDepartmentModels = $model->userDepartments;
        $userHodDepartmentModels = $model->userHodDepartments;

        $userLocationsModel = $userLocaltionModels ? $userLocaltionModels[0] : new UserLocations();

        $hoddep = array_filter(ArrayHelper::getColumn($userDepartmentModels, function ($element) {
            if ($element->is_hod) {
                return $element;
            }
        }));
        $hoddep = $hoddep ? current($hoddep) : '';

        $userHotelsModel = $userHotelModels ? $userHotelModels[0] : new UserHotels();
        $userDepartmentsModel = $hoddep ? $hoddep : new UserDepartments();

        if ($userLocaltionModels) {
            $locations = ArrayHelper::getColumn(ArrayHelper::toArray($userLocaltionModels), 'location_id');
            $userLocationsModel->location_id = ($locations) ? $locations : [];
        }
        if ($userHotelModels) {
            $hotels = ArrayHelper::getColumn(ArrayHelper::toArray($userHotelModels), 'hotel_id');
            $userHotelsModel->hotel_id = ($hotels) ? $hotels : [];
        }
        if ($userDepartmentModels) {
            $departments = ArrayHelper::getColumn(ArrayHelper::toArray($userDepartmentModels), 'hotel_department_id');
            $userDepartmentsModel->hotel_department_id = ($departments) ? $departments : [];
        }
        if ($userHodDepartmentModels) {
            $hodDepartments = ArrayHelper::getColumn(ArrayHelper::toArray($userHodDepartmentModels), 'hotel_department_id');
            $userDepartmentsModel->hodDepartmentList = ($hodDepartments) ? $hodDepartments : [];
        }


        $getUserIsHod = UserDepartments::getHodUserWithHotelDepartment($model->user_id);
        $hotel_id = ArrayHelper::getColumn($getUserIsHod, 'hotel_department_id');
        $UserHotelDepartments = User::getUserHotelAndDepartment($hotel_id);
        $list = ArrayHelper::getColumn($UserHotelDepartments, 'name');
        $list = $list ? $list : [];
        $UserhotelAndDepartment = implode(', ', $list);


        $hotelsList = [];
        $departmentList = [];

        if ($model->load(Yii::$app->request->post())) {
            $postInfo = Yii::$app->request->post();
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->user_type = $user_type;
                if ($model->save()) {

                    $this->assignRole($model->role_id, $model->user_id);

                    $locations = $postInfo['UserLocations']['location_id'];
                    $this->saveLocations($locations, $model);

                    $hotels = $postInfo['UserHotels']['hotel_id'];
                    $this->saveHotels($hotels, $model);

                    $departments = $postInfo['UserDepartments']['hotel_department_id'];
                    $hodDepartments = $postInfo['UserDepartments']['hodDepartmentList'];
                    $this->saveDepartments($departments, $model, $hodDepartments);

                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'User details updated successfully');
                    return $this->redirect([
                        '/user'
                    ]);
                } else {

                    return $this->render('update', [
                        'model' => $model,
                        'userLocationsModel' => $userLocationsModel,
                        'userHotelsModel' => $userHotelsModel,
                        'userDepartmentsModel' => $userDepartmentsModel,
                        'UserhotelAndDepartment' => $UserhotelAndDepartment,
                        'hotelsList' => $hotelsList,
                        'departmentList' => $departmentList
                    ]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                \Yii::$app->session->setFlash('Error', 'Error in user details');
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'userLocationsModel' => $userLocationsModel,
                'userHotelsModel' => $userHotelsModel,
                'userDepartmentsModel' => $userDepartmentsModel,
                'UserhotelAndDepartment' => $UserhotelAndDepartment,
                'hotelsList' => $hotelsList,
                'departmentList' => $departmentList
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete() {
        $post = yii::$app->request->post();
        $decryptedUserId = yii::$app->utils->decryptData($post['deletable_user_id']);
        $userTicketCount = Tickets::find()->where([
            'assigned_user_id' => $decryptedUserId,
            'status' => [1, 4],
            'is_deleted' => 0
        ])->count();
        if ($userTicketCount == 0) {

            $auditsCount = AuditsSchedules::find()->where([
                'auditor_id' => $decryptedUserId,
            ])->orWhere(['deligation_user_id' => $decryptedUserId])->andWhere(['status' => [0, 1, 2]])->count();
            if ($auditsCount) {
                Yii::$app->session->setFlash('error', 'User cannot be deleted as audits were assigned.');
            } else {
                $modelUser = User::updateAll([
                    'is_deleted' => 1,
                    'modified_by' => \Yii::$app->user->getId()
                ], 'user_id=' . $decryptedUserId);
                if ($modelUser) {
                    Yii::$app->session->setFlash('success', 'User deleted successfully');
                }
            }
        } else {
            Yii::$app->session->setFlash('error', 'User cannot be deleted as tickets were assigned.');
        }

        return $this->redirect([
            'index'
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = User::findOne($id)) !== null) {
            if ($model->user_type == 1 && $model->id != Yii::$app->user->identity->id) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     */
    public function actionHotel() {
        $out = [];
        $postData = Yii::$app->request->post();
        if (isset($postData['depdrop_parents'])) {
            $parents = $postData['depdrop_parents'];
            $selectedData = @json_decode($postData['depdrop_all_params']['selectedHotel']);

            $parents = $postData['depdrop_parents'];
            if ($parents != null) {
                $locations_arr = $parents[0];

                header('Content-type: application/json');

                if (!empty($locations_arr)) {
                    foreach ($locations_arr as $location_id) {

                        $location = Locations::findOne($location_id);
                        $result_array = [];

                        $result_array = Hotels::find()->where([
                            'location_id' => $location_id,
                            'is_deleted' => 0
                        ])
                            ->select([
                                'id' => 'hotel_id',
                                'name' => 'hotel_name'
                            ])
                            ->asArray()
                            ->all();

                        if (!empty($result_array)) {
                            $out[$location->locationCity->name] = $result_array;
                        } else {
                            $result_array = [];
                            $result_array['id'] = '';
                            $result_array['name'] = 'No Hotels';
                            $out[$location->locationCity->name] = $result_array;
                        }
                    }
                }

                echo Json::encode([
                    'output' => $out,
                    'selected' => $selectedData
                ]);
                return;
            }
        }
        echo Json::encode([
            'output' => '',
            'selected' => ''
        ]);
    }

    /**
     */
    public function actionDepartment() {
        $department = new UtilsComponent();
        return $department->selectDepartment();
    }

    public function actionHodDepartments() {
        return (new UtilsComponent())->selectHodDepartment();
    }

    public function actionUserView($id) {
        $valid = false;
        $model = $this->findModel(Yii::$app->utils->decryptData($id));

        $userLocaltionModels = $model->userLocations;
        $userHotelModels = $model->userHotels;
        $userDepartmentModels = $model->userDepartments;

        $userLocationsModel = $userLocaltionModels ? $userLocaltionModels[0] : new UserLocations();

        $hoddep = array_filter(ArrayHelper::getColumn($userDepartmentModels, function ($element) {
            if ($element->is_hod) {
                return $element;
            }
        }));
        $hoddep = $hoddep ? current($hoddep) : '';

        $userHotelsModel = $userHotelModels ? $userHotelModels[0] : new UserHotels();
        $userDepartmentsModel = $hoddep ? $hoddep : new UserDepartments();

        if ($userLocaltionModels) {
            $locations = ArrayHelper::getColumn(ArrayHelper::toArray($userLocaltionModels), 'location_id');
            $userLocationsModel->location_id = ($locations) ? $locations : [];
        }
        if ($userHotelModels) {
            $hotels = ArrayHelper::getColumn(ArrayHelper::toArray($userHotelModels), 'hotel_id');
            $userHotelsModel->hotel_id = ($hotels) ? $hotels : [];
        }
        if ($userDepartmentModels) {
            $departments = ArrayHelper::getColumn(ArrayHelper::toArray($userDepartmentModels), 'hotel_department_id');
            $userDepartmentsModel->hotel_department_id = ($departments) ? $departments : [];
        }

        $getUserIsHod = UserDepartments::getHodUserWithHotelDepartment($model->user_id);
        $hotel_id = ArrayHelper::getColumn($getUserIsHod, 'hotel_department_id');
        $UserHotelDepartments = User::getUserHotelAndDepartment($hotel_id);
        $list = ArrayHelper::getColumn($UserHotelDepartments, 'name');
        $list = $list ? $list : [];
        $UserhotelAndDepartment = implode(', ', $list);

        $hotelsList = [];
        $departmentList = [];

        return $this->render('view_details', [
            'model' => $model,
            'userLocationsModel' => $userLocationsModel,
            'userHotelsModel' => $userHotelsModel,
            'userDepartmentsModel' => $userDepartmentsModel,
            'hotelsList' => $hotelsList,
            'departmentList' => $departmentList, 'UserhotelAndDepartment' => $UserhotelAndDepartment
        ]);
    }

    /**
     *
     * @param unknown $role_id
     * @param unknown $user_id
     * @return boolean
     */
    public function assignRole($role_id, $user_id) {
        $status = false;
        $rolesmodel = \app\models\Roles::findOne([
            'role_id' => $role_id
        ]);
        $auth = yii::$app->authManager;
        $roleToBeAssigned = $auth->getRole($rolesmodel->role_main);
        if ($rolesmodel && $roleToBeAssigned) {

            $roleAssignmentModel = \app\models\AuthAssignment::find()->where([
                'user_id' => $user_id
            ])->one();

            $roleAuthAssignModel = $roleAssignmentModel ? $roleAssignmentModel : new \app\models\AuthAssignment();

            $roleAuthAssignModel->item_name = $roleToBeAssigned->name;
            $roleAuthAssignModel->user_id = $user_id;
            $status = $roleAuthAssignModel->save();
        }

        return $status;
    }

    public function actionChangePassword() {
        $changePassword = new \app\models\ChangePasswordForm();
        $changePassword->email = Yii::$app->user->identity->email;
        if ($changePassword->load(Yii::$app->request->post()) && $changePassword->changePassword()) {
            Yii::$app->user->logout();
            Yii::$app->session->setFlash('success', 'Password Changed successfully');
            return $this->redirect(yii::$app->urlManager->createUrl('/'));
        } else {
            Yii::$app->session->setFlash('error', 'Incorrect current Password');
            return $this->redirect(Yii::$app->urlManager->createUrl('site/dashboard'));
        }
    }

    /**
     *
     * @param unknown $id
     * @param unknown $status
     * @return number
     */
    public function actionUpdatestatus($id, $status) {
        $model = User::findOne($id);
        $model->is_active = $status;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Status updated successfully');
            return 1;
        } else {
            Yii::$app->session->setFlash('success', 'Error in update status');
            return 0;
        }
    }

}
