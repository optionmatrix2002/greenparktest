<?php

namespace app\controllers;

use app\components\EmailsComponent;
use Yii;
use app\models\Audits;
use app\models\search\AuditsSearch;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use app\models\Locations;
use app\models\Hotels;
use app\models\UserLocations;
use app\models\UserHotels;
use app\models\UserDepartments;
use yii\helpers\Json;
use app\components\UtilsComponent;
use app\models\Answers;
use app\models\AuditAttachments;
use app\models\Departments;
use app\models\Checklists;
use app\models\HotelDepartments;
use app\models\Sections;
use app\models\SubSections;
use app\models\Tickets;
use app\models\User;
use app\models\AuditsSchedules;
use app\models\Interval;
use Codeception\PHPUnit\Constraint\Page;
use app\models\search\AuditsSchedulesSearch;
use yii\helpers\ArrayHelper;
use app\models\AuditsChecklistQuestions;
use app\models\HotelDepartmentSections;
use kartik\mpdf\Pdf;
use yii\filters\AccessControl;
use app\components\AccessRule;

/**
 * AuditsController implements the CRUD actions for Audits model.
 */
class AuditsController extends Controller
{

    public $layout = 'dashboard_layout';

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
                'create',
                'update',
                'delete',
                'index',
                'view',
                'view-audit',
                'reports'
            ], // only be applied to
            'rules' => [
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('audits/create'),
                    'actions' => [
                        'create'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('audits/update'),
                    'actions' => [
                        'update'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('audits'),
                    'actions' => [
                        'index', 'view', 'view-audit', 'reports'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('audits/delete'),
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
     * Lists all Audits models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuditsSearch();
        $auditScheduleModel = new AuditsSchedules();

        // echo "<pre>"; print_r(Yii::$app->request->queryParams); die();

        $dataProviderAudits = $searchModel->searchAudits(Yii::$app->request->queryParams);
        $dataProviderAuditsSchedules = $searchModel->searchAuditsSchedules(Yii::$app->request->queryParams,[3,4]);
        $dataProviderAuditsSchedulesChilds = $searchModel->searchAuditsSchedules(Yii::$app->request->queryParams,[0,1,2]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProviderAudits' => $dataProviderAudits,
            'dataProviderAuditsSchedules' => $dataProviderAuditsSchedules,
            'dataProviderAuditsSchedulesChilds'=>$dataProviderAuditsSchedulesChilds,
            'auditScheduleModel' => $auditScheduleModel
        ]);
    }

    /**
     * Displays a single Audits model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new Audits model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreateOld()
    {
        $valid = false;
        $model = new Audits();
        $auditLocationsModel = new Locations();
        $auditsSchedulesModel = new AuditsSchedules();

        if ($model->load(Yii::$app->request->post())) {
            $postInfo = Yii::$app->request->post('Audits');
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $checkListId = $postInfo['checklist_id'];
                $frequencyId = Checklists::findOne($checkListId)->cl_frequency_value;

                $stdt = Yii::$app->formatter->asDate($postInfo['start_date'], 'php:Y-m-d');
                $enddt = Yii::$app->formatter->asDate($postInfo['end_date'], 'php:Y-m-d');

                $totalDaysBetweenDays = $this->dateDiff($stdt, $enddt); // For get Total days count

                $intervalTotalDaysCount = $this->totalDays($frequencyId);

                if ($totalDaysBetweenDays >= $intervalTotalDaysCount) {

                    $interval = $this->getIntervelId($frequencyId); // funtion return Intervel Type
                    $model->audit_name = "AU00";
                    $model->start_date = $stdt;
                    $model->end_date = $enddt;
                    $auditsSchedulesModel->load(Yii::$app->request->post());
                    if ($model->save()) {
                        $model->audit_name = $model->audit_name . $model->audit_id;
                        $updateAuditId = Audits::updateAll([
                            'audit_name' => $model->audit_name
                        ], 'audit_id=' . $model->audit_id);

                        /* For Daya calculation */
                        $startDate = $stdt;
                        $orgEndDate = $endDate = $enddt;
                        $time = $interval;
                        $frequencyTotalDaysCount = $this->totalDays($frequencyId);
                        $insertingDays = $this->dateDiff($startDate, $endDate) / $frequencyTotalDaysCount;
                        for ($i = 1; $i <= $insertingDays; $i++) {
                            if ($startDate <= $endDate) {
                                $auditsSchedulesModel->audit_schedule_name = $model->audit_name . "-" . $i;
                                $auditsSchedulesModel->audit_id = $model->audit_id;
                                $auditsSchedulesModel->auditor_id = $model->user_id;
                                $auditsSchedulesModel->start_date = $startDate;
                                $startDates = date('Y-m-d', strtotime($startDate . ' -1 Days'));
                                $enddate = date('Y-m-d', strtotime($startDates . $time));
                                $enddate = ($enddate >= $orgEndDate) ? $orgEndDate : $enddate;
                                $auditsSchedulesModel->end_date = $enddate;
                                $startDate = date('Y-m-d', strtotime($enddate . ' +1 Days'));
                                $auditsSchedulesModel->deligation_user_id = ''; // $model->user_id;
                                $auditsSchedulesModel->deligation_status = 0;
                                $auditsSchedulesModel->status = 0;
                                $auditsSchedulesModel->is_deleted = 0;
                                $auditsSchedulesModel->created_by = \Yii::$app->user->id;
                                $auditsSchedulesModel->updated_by = \Yii::$app->user->id;
                                $auditsSchedulesModel->audit_schedule_id = null; // primary key(auto increment id) id
                                $auditsSchedulesModel->isNewRecord = true;
                                if ($auditsSchedulesModel->save()) {
                                    $valid = true;
                                }
                            }
                        }

                    } else {

                        throw new \Exception('Error saving Audit');
                    }
                } else {
                    throw new \Exception('Please select start date and end date based on checklist frequency');
                }
                if ($valid) {


                    $auditScheduled = AuditsSchedules::find()
                        ->joinWith(['audit.checklist', 'audit.hotel', 'audit.department'])
                        ->andWhere([AuditsSchedules::tableName() . '.audit_id' => $model->audit_id])
                        ->orderBy(['audit_schedule_id' => SORT_ASC])
                        ->asArray()
                        ->one();

                    $user = User::findOne($auditScheduled['auditor_id']);
                    $arrNotifications = [];
                    $arrNotifications['type'] = 'auditAssigned';
                    $arrNotifications['toEmail'] = $user->email;
                    $arrNotifications['mobileNumber'] = $user->phone;
                    $arrNotifications['deviceToken'] = $user->device_token;

                    $attributes = $auditScheduled;
                    $attributes['department'] = isset($auditScheduled['audit']['department']['department_name']) ? $auditScheduled['audit']['department']['department_name'] : '';
                    $attributes['checkList'] = isset($auditScheduled['audit']['checklist']['cl_name']) ? $auditScheduled['audit']['checklist']['cl_name'] : '';
                    $attributes['hotel'] = isset($auditScheduled['audit']['hotel']['hotel_name']) ? $auditScheduled['audit']['hotel']['hotel_name'] : '';

                    $arrNotifications['data'] = $attributes;
                    $arrNotifications['userId'] = $user->user_id;
                    Yii::$app->scheduler->triggerNotifications($arrNotifications);


                    $arrData = [];
                    $arrData['module'] = 'audit';
                    $arrData['type'] = 'create';
                    $arrData['message'] = "Audit - <b>" . $model->audit_name . '</b> is created by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
                    Yii::$app->events->createEvent($arrData);

                    $transaction->commit();

                    Yii::$app->session->setFlash('success', "Audit  $model->audit_name created successfully");
                    return $this->redirect(\Yii::$app->urlManager->createUrl([
                        'audits'
                    ]));
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getmessage());
            }
        }
        if ($model->start_date) {
            $model->start_date = date('d-m-Y', strtotime($model->start_date));
        }

        return $this->render('create', [
            'model' => $model,
            'auditLocationsModel' => $auditLocationsModel,
            'auditsSchedulesModel' => $auditsSchedulesModel

        ]);
    }


    /**
     * Creates a new Audits model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Audits();
        $auditLocationsModel = new Locations();
        $auditsSchedulesModel = new AuditsSchedules();

        if ($model->load(Yii::$app->request->post())) {
            $postInfo = Yii::$app->request->post('Audits');
            $transaction = Yii::$app->db->beginTransaction();
            try {

                $stdt = Yii::$app->formatter->asDate($postInfo['start_date'], 'php:Y-m-d');
                $enddt = Yii::$app->formatter->asDate($postInfo['end_date'], 'php:Y-m-d');

                $model->audit_name = "AU00";
                $model->start_date = $stdt;
                $model->end_date = $enddt;
                $auditsSchedulesModel->load(Yii::$app->request->post());
                if ($model->save()) {
                    $model->audit_name = $model->audit_name . $model->audit_id;
                    Audits::updateAll([
                        'audit_name' => $model->audit_name
                    ], 'audit_id=' . $model->audit_id);

                    $auditsSchedulesModel->audit_schedule_name = $model->audit_name . "-1";
                    $auditsSchedulesModel->audit_id = $model->audit_id;
                    $auditsSchedulesModel->auditor_id = $model->user_id;
                    $auditsSchedulesModel->start_date = $stdt;
                    $auditsSchedulesModel->end_date = $enddt;
                    $auditsSchedulesModel->deligation_user_id = ''; // $model->user_id;
                    $auditsSchedulesModel->deligation_status = 0;
                    $auditsSchedulesModel->status = 0;
                    $auditsSchedulesModel->is_deleted = 0;
                    if ($auditsSchedulesModel->save()) {

                        $auditScheduled = AuditsSchedules::find()
                            ->joinWith(['audit.checklist', 'audit.hotel', 'audit.department'])
                            ->andWhere([AuditsSchedules::tableName() . '.audit_id' => $model->audit_id])
                            ->orderBy(['audit_schedule_id' => SORT_ASC])
                            ->asArray()
                            ->one();

                        $user = User::findOne($auditScheduled['auditor_id']);
                        $arrNotifications = [];
                        $arrNotifications['type'] = 'auditAssigned';
                        $arrNotifications['toEmail'] = $user->email;
                        $arrNotifications['mobileNumber'] = $user->phone;
                        $arrNotifications['deviceToken'] = $user->device_token;

                        $attributes = $auditScheduled;
                        $attributes['department'] = isset($auditScheduled['audit']['department']['department_name']) ? $auditScheduled['audit']['department']['department_name'] : '';
                        $attributes['checkList'] = isset($auditScheduled['audit']['checklist']['cl_name']) ? $auditScheduled['audit']['checklist']['cl_name'] : '';
                        $attributes['hotel'] = isset($auditScheduled['audit']['hotel']['hotel_name']) ? $auditScheduled['audit']['hotel']['hotel_name'] : '';

                        $arrNotifications['data'] = $attributes;
                        $arrNotifications['userId'] = $user->user_id;
                        Yii::$app->scheduler->triggerNotifications($arrNotifications);


                        $arrData = [];
                        $arrData['module'] = 'audit';
                        $arrData['type'] = 'create';
                        $arrData['message'] = "Audit - <b>" . $model->audit_name . '</b> is created by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
                        Yii::$app->events->createEvent($arrData);

                        $transaction->commit();
                        Yii::$app->session->setFlash('success', "Audit  $model->audit_name created successfully");
                        return $this->redirect(\Yii::$app->urlManager->createUrl([
                            'audits'
                        ]));
                    }
                    throw new \Exception('Error saving Audit');

                } else {
                    $model->start_date = date('d-m-Y', strtotime($model->start_date));
                    return $this->render('create', [
                        'model' => $model,
                        'auditLocationsModel' => $auditLocationsModel,
                        'auditsSchedulesModel' => $auditsSchedulesModel

                    ]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getmessage());
            }
        }

        if ($model->start_date) {
            $model->start_date = date('d-m-Y', strtotime($model->start_date));
        }

        return $this->render('create', [
            'model' => $model,
            'auditLocationsModel' => $auditLocationsModel,
            'auditsSchedulesModel' => $auditsSchedulesModel

        ]);
    }

    /*
     * return total days count from start date and end date
     */
    public function dateDiff($startdate, $enddate)
    {
        $start_ts = strtotime($startdate);
        $end_ts = strtotime($enddate);
        $diff = $end_ts - $start_ts;
        return round($diff / 86400) + 1;
    }

    /**
     * Switch case for Intervel types
     */
    public function getIntervelId($frequencyId)
    {
        switch ($frequencyId) {
            case 1:
                $interval = "+ 7 days";
                break;
            case 2:
                $interval = "+ 14 days";
                break;
            case 3:
                $interval = "+ 30 days";
                break;
            case 4:
                $interval = "+ 90 days";
                break;
            case 5:
                $interval = "+ 180 days";
                break;
            case 6:
                $interval = "+ 365 days";
                break;
        }

        return $interval;
    }

    public function totalDays($frequencyId)
    {
        $list = [
            1 => 7,
            2 => 14,
            3 => 31,
            4 => 90,
            5 => 180,
            6 => 365
        ];
        return $getTotalDays = $list[$frequencyId];
    }

    /**
     * Updates an existing Audits model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateOld($id)
    {
        $valid = false;
        $model = $this->findModel(Yii::$app->utils->decryptData($id));
        if (!in_array($model->status, [
            0,
            1,
            2
        ])) {
            Yii::$app->session->setFlash('error', "Audit $model->audit_name cannot be updated as it is cancelled or completed.");
            return $this->redirect([
                'index'
            ]);
        }
        $auditsSchedulesModel = new AuditsSchedules();
        $auditLocationsModel = new Locations();
        $searchModel = new AuditsSearch();
        $auditScheduleSearch = new AuditsSchedulesSearch();
        $dataProvider = $auditScheduleSearch->search(Yii::$app->request->queryParams);
        $oldEndDate = $model->end_date;

        // For get audit name
        // $auditName = Audits::getAuditDetails($model->audit_id);
        $nameAudits = $model->audit_name;

        if ($model->load(Yii::$app->request->post())) {
            $postInfo = Yii::$app->request->post();
            $transaction = Yii::$app->db->beginTransaction();
            try {
                /**
                 * ***********************Delete audit schedules****************************
                 */

                $enddt = Yii::$app->formatter->asDate($postInfo['Audits']['end_date'], 'php:Y-m-d');
                $stdt = $model->start_date;
                $model->end_date = $enddt;

                $auditsSchedulesModel->load(Yii::$app->request->post());

                if ($model->save()) {
                    $valid = true;

                    if (strtotime($oldEndDate) != strtotime($model->end_date)) {

                        $frequencyId = Checklists::findOne($model->checklist_id)->cl_frequency_value;
                        $interval = $this->getIntervelId($frequencyId); // funtion return Intervel Type

                        $auditCompletedMaxDate = AuditsSchedules::find()->select([
                            'max(end_date) as maxDate'
                        ])
                            ->where([
                                'status' => [
                                    1,
                                    2,
                                    3
                                ]
                            ])
                            ->andWhere([
                                'audit_id' => $model->audit_id
                            ])
                            ->asArray()
                            ->one();

                        $originalSelectedEndDate = $auditCompletedMaxDate['maxDate'];
                        $auditCompletedMaxDate = $auditCompletedMaxDate['maxDate'];
                        $auditCompletedMaxDate = date('d-m-Y', strtotime($auditCompletedMaxDate));
                        $postEndDate = $model->end_date;

                        if ($originalSelectedEndDate && strtotime($postEndDate) < strtotime($auditCompletedMaxDate)) {

                            throw new \Exception('End date cannot be decreased, as audits are in progress or completed');
                        }
                        $iValue = 1;
                        if ($originalSelectedEndDate) {

                            AuditsSchedules::deleteAll('audit_id = :audit_id AND end_date > :endDate', [
                                ':endDate' => $originalSelectedEndDate,
                                ':audit_id' => $model->audit_id
                            ]);

                            $scheduledName = AuditsSchedules::find()->select([
                                'audit_schedule_name'
                            ])
                                ->where([
                                    'end_date' => $originalSelectedEndDate
                                ])
                                ->asArray()
                                ->one();
                            $scheduledName = $scheduledName['audit_schedule_name'];
                            $stdt = $originalSelectedEndDate;

                            $value = explode('-', $scheduledName)[1];
                            $iValue = $value + 1;
                        } else {
                            AuditsSchedules::deleteAll('audit_id = :audit_id', [
                                ':audit_id' => $model->audit_id
                            ]);
                        }
                        $totalDaysBetweenDays = $this->dateDiff($stdt, $enddt); // For get Total days count
                        $intervalTotalDaysCount = $this->totalDays($frequencyId);
                        if ($totalDaysBetweenDays >= $intervalTotalDaysCount) { // $frequencyId == $frequencyId &&

                            /* For Daya calculation */
                            $startDate = $stdt;

                            $orgEndDate = $endDate = $enddt;
                            $time = $interval;
                            $frequencyTotalDaysCount = $this->totalDays($frequencyId);
                            $insertingDays = $this->dateDiff($startDate, $endDate) / $frequencyTotalDaysCount;

                            for ($i = $iValue; $i <= $insertingDays + $iValue; $i++) {
                                $n = 1;
                                $diffDays = 0;
                                $diffDays = $this->dateDiff($startDate, $endDate); // For get Total days count
                                if ($diffDays >= $intervalTotalDaysCount && $startDate <= $endDate) {
                                    $auditsSchedulesModel->audit_schedule_name = $model->audit_name . "-" . $i;
                                    $auditsSchedulesModel->audit_id = $model->audit_id;
                                    $auditsSchedulesModel->auditor_id = $model->user_id;
                                    $auditsSchedulesModel->start_date = $startDate;
                                    $startDates = date('Y-m-d', strtotime($startDate . ' -1 Days'));
                                    $enddate = date('Y-m-d', strtotime($startDates . $time));
                                    $enddate = ($enddate >= $orgEndDate) ? $orgEndDate : $enddate;
                                    $auditsSchedulesModel->end_date = $enddate;
                                    $startDate = date('Y-m-d', strtotime($enddate . ' +1 Days'));
                                    // $auditsSchedulesModel->deligation_user_id = $model->user_id;

                                    if ($auditsSchedulesModel->deligation_user_id == "") {
                                        $auditsSchedulesModel->deligation_user_id = "";
                                    } else {
                                        // $auditsSchedulesModel->deligation_user_id = $model->user_id;
                                    }

                                    $auditsSchedulesModel->deligation_status = 0;
                                    $auditsSchedulesModel->status = 0;
                                    $auditsSchedulesModel->is_deleted = 0;
                                    $auditsSchedulesModel->audit_schedule_id = null; // primary key(auto increment id) id
                                    $auditsSchedulesModel->isNewRecord = true;
                                    if ($auditsSchedulesModel->save()) {
                                        $valid = true;
                                    } else {
                                        $valid = false;
                                        throw new \Exception('Error saving Schedule audit');
                                    }
                                    $n++;
                                }
                            }
                        } else {
                            throw new \Exception('Please select start date and end date based on checklist frequency');
                        }
                    }
                } else {

                    throw new \Exception('Error saving Audit');
                }

                if ($valid) {
                    $transaction->commit();

                    $arrData = [];
                    $arrData['module'] = 'audit';
                    $arrData['type'] = 'update';
                    $arrData['message'] = "Audit - <b>" . $model->audit_name . '</b> is updated by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
                    Yii::$app->events->createEvent($arrData);

                    Yii::$app->session->setFlash('success', "Audit $model->audit_name updated successfully");
                    return $this->redirect([
                        'update?id=' . $id
                    ]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'auditLocationsModel' => $auditLocationsModel,
            'auditsSchedulesModel' => $auditsSchedulesModel,
            'auditScheduleSearch' => $auditScheduleSearch,
            'nameAudits' => $nameAudits
        ]);
    }


    /**
     * Updates an existing Audits model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $valid = false;
        $model = $this->findModel(Yii::$app->utils->decryptData($id));
        /*if (!in_array($model->status, [
            0,
            1,
            2
        ])) {
            Yii::$app->session->setFlash('error', "Audit $model->audit_name cannot be updated as it is cancelled or completed.");
            return $this->redirect([
                'index'
            ]);
        }*/
        $auditsSchedulesModel = new AuditsSchedules();
        $auditLocationsModel = new Locations();
        $auditScheduleSearch = new AuditsSchedulesSearch();
        $dataProvider = $auditScheduleSearch->search(Yii::$app->request->queryParams);
        $nameAudits = $model->audit_name;

        if ($model->load(Yii::$app->request->post())) {
            $postInfo = Yii::$app->request->post();
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $auditsSchedulesModel->load(Yii::$app->request->post());
                if ($model->save()) {
                    $valid = true;
                } else {
                    throw new \Exception('Error saving Audit');
                }

                if ($valid) {
                    $transaction->commit();

                    $arrData = [];
                    $arrData['module'] = 'audit';
                    $arrData['type'] = 'update';
                    $arrData['message'] = "Audit - <b>" . $model->audit_name . '</b> is updated by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
                    Yii::$app->events->createEvent($arrData);

                    Yii::$app->session->setFlash('success', "Audit $model->audit_name updated successfully");
                    return $this->redirect([
                        'update?id=' . $id
                    ]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'auditLocationsModel' => $auditLocationsModel,
            'auditsSchedulesModel' => $auditsSchedulesModel,
            'auditScheduleSearch' => $auditScheduleSearch,
            'nameAudits' => $nameAudits
        ]);
    }

    /**
     * Deletes an existing Audits model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        $post = yii::$app->request->post();
        $decryptedAudit = yii::$app->utils->decryptData($post['deletable_audit_id']);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $model = $this->findModel($decryptedAudit);

            AuditsSchedules::updateAll([
                'is_deleted' => 1,
                'updated_by' => \Yii::$app->user->getId()
            ], 'audit_id=' . $decryptedAudit);

            Audits::updateAll([
                'is_deleted' => 1,
                'updated_by' => \Yii::$app->user->getId()
            ], 'audit_id=' . $decryptedAudit);


            $transaction->commit();

            $arrData = [];
            $arrData['module'] = 'audit';
            $arrData['type'] = 'delete';
            $arrData['message'] = "Audit - <b>" . $model->audit_name . '</b> is deleted by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
            Yii::$app->events->createEvent($arrData);

            Yii::$app->session->setFlash('success', 'Audit deleted successfully');

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect([
            'index'
        ]);
    }

    /**
     * Finds the Audits model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Audits the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Audits::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     */
    public function actionHotel()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $location_id = $parents[0];
                header('Content-type: application/json');
                $out = Audits::getHotels($location_id);
                echo Json::encode([
                    'output' => $out,
                    'selected' => ''
                ]);
                return;
            }
        }
        echo Json::encode([
            'output' => '',
            'selected' => ''
        ]);
    }

    public function actionDepartment()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $hotel_id = $parents[0];
                header('Content-type: application/json');
                $deparments = Audits::getHotelDepartments($hotel_id);
                $resultArray = [];
                if (!empty($deparments)) {
                    foreach ($deparments as $department) {
                        $list = [];
                        if (isset($department['department'])) {
                            $list['id'] = $department['department_id'];
                            $list['name'] = $department['hotel']['hotel_name'] . '-' . $department['department']['department_name'];
                            $resultArray[] = $list;
                        }
                    }
                }
                echo Json::encode([
                    'output' => $resultArray,
                    'selected' => ''
                ]);
                return;
            }
        }
        echo Json::encode([
            'output' => '',
            'selected' => ''
        ]);
    }

    public function actionCheckList()
    {
        $finalArray = [];
        $result = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $department_id = $parents[0];
                $hotel_id = $parents[1];
                header('Content-type: application/json');
                $sections = HotelDepartmentSections::find()->select('section_id')
                    ->where([
                        'hotel_id' => $hotel_id,
                        'department_id' => $department_id,
                        'is_deleted' => 0
                    ])
                    ->all();
                $sections = ArrayHelper::getColumn($sections, 'section_id');
                $result = [];
                if ($sections) {
                    $result = Checklists::find()->alias('cl')
                        ->select([
                            'cl.checklist_id',
                            'cl.cl_name'
                        ])
                        ->joinWith('questions as q')
                        ->where([
                            'cl_department_id' => $department_id,
                            'cl.is_deleted' => 0,
                            'cl_status' => 1,
                            'q.is_deleted' => 0,
                            'q.q_section' => $sections
                        ])
                        ->asArray()
                        ->all();
                }
                foreach ($result as $res) {
                    if ($res['questions']) {
                        $array = [];
                        $array['id'] = $res['checklist_id'];
                        $array['name'] = $res['cl_name'];
                        $finalArray[] = $array;
                    }
                }
                echo Json::encode([
                    'output' => $finalArray,
                    'selected' => ''
                ]);
                return;
            }
        }
        echo Json::encode([
            'output' => '',
            'selected' => ''
        ]);
    }

    public function actionAuditUser()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $hotel_id = $parents[0];
                $department_id = $parents[1];
                header('Content-type: application/json');

                $hotelDepartmentId = HotelDepartments::find()->where([
                    'hotel_id' => $hotel_id,
                    'department_id' => $department_id,
                    'is_deleted' => 0
                ])
                    ->select([
                        'id'
                    ])
                    ->one();
                $userArrayIds = [];
                $userId = UserDepartments::find()->where([
                    'hotel_department_id' => $hotelDepartmentId->id
                ])
                    ->select([
                        'user_id'
                    ])
                    ->asArray()
                    ->all();
                $userArrayIds = yii\helpers\ArrayHelper::getColumn($userId, 'user_id');

                $out = User::find()->where([
                    'IN',
                    'user_id',
                    $userArrayIds,
                    'is_deleted' => 0,
                    'user_type' => 2
                ])
                    ->select([
                        'id' => 'user_id',
                        'name' => 'first_name'
                    ])
                    ->asArray()
                    ->all();
                echo Json::encode([
                    'output' => $out,
                    'selected' => ''
                ]);
                return;
            }
        }
        echo Json::encode([
            'output' => '',
            'selected' => ''
        ]);
    }

    public function actionCreateAudit()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {

            $response = [];
            $response['status'] = false;
            $postInformation = Yii::$app->request->post();

            $auditModel = new AuditsSchedules();
            $auditModel->load($postInformation);
            $auditModel->start_date = date('Y-m-d', strtotime($auditModel->start_date));
            $auditModel->end_date = date('Y-m-d', strtotime($auditModel->end_date));

            $startDateConflictAudits = AuditsSchedules::find()->where(['audit_id' => $auditModel->audit_id])
                ->andWhere(['between', 'start_date', $auditModel->start_date, $auditModel->end_date])
                ->andWhere(['is_deleted' => 0])
                ->andWhere(['IN', 'status', [0, 1, 2, 3]])
                ->asArray()->all();

            if ($startDateConflictAudits) {
                $response['message'] = 'Audit already exists between given scheduled dates. Please select different dates.';
                return Json::encode($response);
            }
            $endDateConflictAudits = AuditsSchedules::find()->where(['audit_id' => $auditModel->audit_id])
                ->andWhere(['between', 'end_date', $auditModel->start_date, $auditModel->end_date])
                ->andWhere(['is_deleted' => 0])
                ->andWhere(['IN', 'status', [0, 1, 2, 3]])
                ->asArray()->all();
            if ($endDateConflictAudits) {
                $response['message'] = 'Audit already exists between given scheduled dates. Please select different dates.';
                return Json::encode($response);
            }

            $auditsCount = AuditsSchedules::find()->where(['audit_id' => $auditModel->audit_id])->count();
            $auditsCount += 1;
            $auditModel->audit_schedule_name = 'AU00' . $auditModel->audit_id . '-' . $auditsCount;
            $auditModel->status = 0;
            $auditModel->save();

            self::saveAuditEndDate($auditModel->audit_id);

            $response['status'] = false;
            $response['message'] = 'Audit scheduled successfully.';

            Yii::$app->session->setFlash('success', 'Audit ' . $auditModel->audit_schedule_name . ' is scheduled successfully');
            return $this->redirect([
                'update?id=' . Yii::$app->utils->encryptData($auditModel->audit_id)
            ]);
        }
    }

    public function actionUpdateAuditUser()
    {
        $post = yii::$app->request->post();
        if ($post && $post['update_auditschedule_id']) {
            if ($post['AuditsSchedules']['auditor_id'] != "") {


                $decryptedAuditScheduleId = yii::$app->utils->decryptData($post['update_auditschedule_id']);
                $auditSchedule = $post['AuditsSchedules'];
                $stdt = Yii::$app->formatter->asDate($auditSchedule['start_date'], 'php:Y-m-d');
                $enddt = Yii::$app->formatter->asDate($auditSchedule['end_date'], 'php:Y-m-d');

                $scheduledAudit = AuditsSchedules::find()->where(['audit_schedule_id' => $decryptedAuditScheduleId])->one();

                $oldAuditor = $scheduledAudit->auditor_id;

                $scheduledAudit->start_date = $stdt;
                $scheduledAudit->end_date = $enddt;
                $scheduledAudit->auditor_id = $auditSchedule['auditor_id'];

                $startDateConflictAudits = AuditsSchedules::find()->where(['audit_id' => $scheduledAudit->audit_id])
                    ->andWhere(['between', 'start_date', $scheduledAudit->start_date, $scheduledAudit->end_date])
                    ->andWhere(['!=', 'audit_schedule_id', $decryptedAuditScheduleId])
                    ->asArray()->all();
                if ($startDateConflictAudits) {
                    $response['error'] = 'Audit already exists between given scheduled dates. Please select different dates.';
                    return Json::encode($response);
                }
                $endDateConflictAudits = AuditsSchedules::find()->where(['audit_id' => $scheduledAudit->audit_id])
                    ->andWhere(['between', 'end_date', $scheduledAudit->start_date, $scheduledAudit->end_date])
                    ->andWhere(['!=', 'audit_schedule_id', $decryptedAuditScheduleId])
                    ->asArray()->all();
                if ($endDateConflictAudits) {
                    $response['error'] = 'Audit already exists between given scheduled dates. Please select different dates.';
                    return Json::encode($response);
                }

                if ($scheduledAudit->save()) {

                    self::saveAuditEndDate($scheduledAudit->audit_id);

                    $user = User::findOne($post['AuditsSchedules']['auditor_id']);
                    $name = $user->first_name . ' ' . $user->last_name . '.';

                    if ($oldAuditor != $scheduledAudit->auditor_id) {

                        $auditScheduled = AuditsSchedules::find()
                            ->joinWith(['audit.checklist', 'audit.hotel', 'audit.department'])
                            ->andWhere(['audit_schedule_id' => $decryptedAuditScheduleId])
                            ->asArray()
                            ->one();

                        $arrNotifications = [];
                        $arrNotifications['type'] = 'auditorUpdate';
                        $arrNotifications['toEmail'] = $user->email;
                        $arrNotifications['mobileNumber'] = $user->phone;
                        $arrNotifications['deviceToken'] = $user->device_token;
                        $arrNotifications['userId'] = $user->user_id;

                        $attributes = $auditScheduled;
                        $attributes['department'] = isset($auditScheduled['audit']['department']['department_name']) ? $auditScheduled['audit']['department']['department_name'] : '';
                        $attributes['checkList'] = isset($auditScheduled['audit']['checklist']['cl_name']) ? $auditScheduled['audit']['checklist']['cl_name'] : '';
                        $attributes['hotel'] = isset($auditScheduled['audit']['hotel']['hotel_name']) ? $auditScheduled['audit']['hotel']['hotel_name'] : '';

                        $arrNotifications['data'] = $attributes;
                        $arrNotifications['userId'] = $user->user_id;
                        Yii::$app->scheduler->triggerNotifications($arrNotifications);
                    }


                    $arrData = [];
                    $arrData['module'] = 'audit';
                    $arrData['type'] = 'update';
                    $arrData['message'] = "Audit - <b>" . $scheduledAudit->audit_schedule_name . '</b> is updated by ' . $name;
                    Yii::$app->events->createEvent($arrData);

                    $output = [
                        'success' => 'Auditor updated successfully'
                    ];
                } else {
                    $output = [
                        'error' => "Auditor not updated"
                    ];
                }
            } else {
                $output = [
                    'error' => "Please select Auditor"
                ];
            }
        } else {
            $output = [
                'error' => "Invalid request"
            ];
        }
        return json_encode($output);
    }

    public function actionGetAuditUserId()
    {
        $post = yii::$app->request->post();
        if ($post) {

            $decryptedAuditScheduleId = yii::$app->utils->decryptData($post['update_auditschedule_id']);
            if (!empty($decryptedAuditScheduleId)) {
                try {
                    $getAuditorId = AuditsSchedules::findOne($decryptedAuditScheduleId);
                    $getAuditorId->start_date = date('d-m-Y', strtotime($getAuditorId->start_date));
                    $getAuditorId->end_date = date('d-m-Y', strtotime($getAuditorId->end_date));
                    return Json::encode($getAuditorId->attributes);
                } catch (\Exception $e) {
                    throw $e;
                }
            }
        }
    }

    public function actionGetCheckListFrequency()
    {
        $response = [];
        if (Yii::$app->request->isPost) {
            $checklist_id = Yii::$app->request->post('checklist_id');
            if ($checklist_id) {
                try {
                    $getFrequencyId = Checklists::find()->select('interval_name')
                        ->join('LEFT JOIN', 'tbl_gp_interval i', 'i.interval_id=tbl_gp_checklists.cl_frequency_value')
                        ->where([
                            'checklist_id' => $checklist_id
                        ])
                        ->asArray()
                        ->one();

                    if ($getFrequencyId) {
                        $response = [
                            'interval' => $getFrequencyId['interval_name']
                        ];
                    } else {
                        $response = [
                            'error' => 'failure'
                        ];
                    }
                } catch (\Exception $e) {
                    $response = [
                        'error' => $e->getMessage()
                    ];
                }
            }
        }
        return json_encode($response);
    }

    public function actionCancel()
    {
        $output = [];
        $post = yii::$app->request->post();
        if ($post) {
            $decryptedAuditScheduleId = yii::$app->utils->decryptData($post['update_auditschedule_id']);
            $fromIndex = isset($post['fromIndex']) ? $post['fromIndex'] : '';
            if (!empty($decryptedAuditScheduleId)) {

                $getAuditId = AuditsSchedules::find()->where([
                    'audit_schedule_id' => $decryptedAuditScheduleId
                ])->one();

                if ($getAuditId) {

                    $modelAuditSchedule = AuditsSchedules::updateAll([
                        'status' => '4',
                        'updated_by' => \Yii::$app->user->getId()
                    ], 'audit_schedule_id=' . $decryptedAuditScheduleId);


                    $AuditCount = AuditsSchedules::find()->where([
                        'audit_id' => $getAuditId->audit_id
                    ])
                        ->andWhere([
                            'IN',
                            'status',
                            [
                                0,
                                1,
                                2,
                            ]
                        ])
                        ->count();

                    $arrData = [];
                    $arrData['module'] = 'audit';
                    $arrData['type'] = 'cancel';
                    $arrData['message'] = "Audit - <b>" . $getAuditId->audit_schedule_name . '</b> is cancelled by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
                    Yii::$app->events->createEvent($arrData);

                    if (!$AuditCount) {

                        Audits::updateAll([
                            'status' => '4'
                        ], 'audit_id=' . $getAuditId->audit_id);

                        Yii::$app->session->setFlash('success', "Audit  $getAuditId->audit_schedule_name canceled successfully");

                        return $this->redirect(\Yii::$app->urlManager->createUrl([
                            'audits'
                        ]));
                    } else {

                        Yii::$app->session->setFlash('success', 'Schedule Audit ' . $getAuditId->audit_schedule_name . ' canceled successfully');
                        if ($fromIndex) {
                            return $this->redirect(\Yii::$app->urlManager->createUrl([
                                'audits'
                            ]));
                        } else {
                            return $this->redirect([
                                'update?id=' . Yii::$app->utils->encryptData($getAuditId->audit_id)
                            ]);
                        }

                    }

                }
            }
        } else {
            $output = [
                'error' => "Invalid request"
            ];
        }
        return json_encode($output);
    }


    public function actionViewAudit($id)
    {
        $valid = false;
        $model = $this->findModel(Yii::$app->utils->decryptData($id));
        $auditsSchedulesModel = new AuditsSchedules();
        $auditLocationsModel = new Locations();
        $searchModel = new AuditsSearch();
        $auditScheduleSearch = new AuditsSchedulesSearch();
        $dataProvider = $auditScheduleSearch->search(Yii::$app->request->queryParams);

        return $this->render('view-audit', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'auditLocationsModel' => $auditLocationsModel,
            'auditsSchedulesModel' => $auditsSchedulesModel,
            'auditScheduleSearch' => $auditScheduleSearch

        ]);
    }

    /**
     * Displays a Reports Audits model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionReports($id)
    {
        $model = AuditsSchedules::findOne(Yii::$app->utils->decryptData($id), 'status', 'IN', [
            '3',
            '4'
        ]);


        $modelAuditAttachment = new AuditAttachments();
        $modelAudit = Audits::getAuditDetails($model->audit_id); // get Audit details
        $modelChecklists = Checklists::getCheckListAuditType($modelAudit->checklist_id);
        $answersCount = Answers::getAnswerCount($model->audit_schedule_id);
        $getAuditAttachments = AuditAttachments::getAuditAttachments($model->audit_schedule_id);

        $subSectionList = ArrayHelper::map(SubSections::getList(), 'sub_section_id', 'ss_subsection_name');
        $sectionsList = ArrayHelper::map(Sections::getList(), 'section_id', 's_section_name');

        // For get all answers with auditScheduleId and answerAttachments
        $questionsList = Audits::getAuditQuestionsAndAnswers($model->audit_schedule_id);

        $sectionSpecificQuestions = ArrayHelper::index($questionsList, null, [
            'q_section',
            'q_sub_section'
        ]);

        $arrTotalQuestions = [];


        foreach ($sectionSpecificQuestions as $sectionId => $questions) {
            if ($sectionId) {
                $modelAnswers = [];
                foreach ($questions as $subsectionId => $question) {
                    if ($subsectionId) {

                        $modelAnswers[$subsectionId]['subSectionName'] = isset($subSectionList[$subsectionId]) ? $subSectionList[$subsectionId] : $subsectionId;
                        ArrayHelper::multisort($question, 'question_id');
                        $modelAnswers[$subsectionId]['questions'] = $question;
                        $modelAnswers[$subsectionId]['sectionName'] = isset($sectionsList[$sectionId]) ? $sectionsList[$sectionId] : $sectionId;

                    }
                }
                $arrTotalQuestions[$sectionId] = $modelAnswers;
            }
        }

        /**
         * **************************End Quessionaries***********************
         */

        $totalScore = 0;
        if ($model->status == 3) {
            $this->actionMpdfDemo1();
            $this->actionGenerateAcrossSectionReport();
            $totalScore = AuditsSchedules::getAuditScore($model->audit_schedule_id);
        }


        $tickets = Tickets::find()->where([
            'audit_schedule_id' => $model->audit_schedule_id
        ])
            ->asArray()
            ->all();

        $nonCompliance = count($tickets);

        $chronicTickets = Tickets::find()->where([
            'audit_schedule_id' => $model->audit_schedule_id,
            'chronicity' => 1
        ])
            ->asArray()
            ->all();

        $chronicIssues = count($chronicTickets);

        return $this->render('reports', [
            'model' => $model,
            'modelAudit' => $modelAudit,
            'modelAnswers' => $arrTotalQuestions,
            'modelChecklists' => $modelChecklists,
            'modelAuditAttachment' => $modelAuditAttachment,
            'answersCount' => $answersCount,
            'getAuditAttachments' => $getAuditAttachments,
            'totalScore' => $totalScore,
            'nonCompliance' => $nonCompliance,
            'chronicIssues' => $chronicIssues

        ]);
    }

    public function actionUpload()
    {
        $post = Yii::$app->request->post();
        $audit_schedule_id = Yii::$app->utils->encryptData($post['audit_schedule_id']);
        $model = new AuditAttachments();
        if ($post && $post['audit_schedule_id']) {

            $model->audit_attachment_path = UploadedFile::getInstance($model, 'audit_attachment_path');
            $file = $model->audit_attachment_path;
            if ($file != "") {
                $imageName = $file->baseName . '_' . time() . '.' . $model->audit_attachment_path->extension;
                $model->audit_schedule_id = $post['audit_schedule_id'];
                $model->created_by = \Yii::$app->user->id;
                $model->updated_by = \Yii::$app->user->id;
                $model->audit_attachment_path = $imageName;
                $image_path = \Yii::getAlias('img/audit_attachments') . "/" . $imageName;
                if ($model->save()) {
                    $file->saveAs($image_path);
                    Yii::$app->session->setFlash('success', "Attachment added successfully");
                } else {
                    Yii::$app->session->setFlash('error', "Attachment not added");
                }
            } else {
                Yii::$app->session->setFlash('error', "Please select Attachment");
            }
        }
        return $this->redirect([
            'audits/reports',
            'id' => $audit_schedule_id
        ]);
    }

    public function actionDeleteAuditAttachemnt()
    {
        $post = yii::$app->request->post();
        $audit_schedule_id = Yii::$app->utils->encryptData($post['audit_schedule_id']);

        $output = [];
        if ($post && $post['deletable_auditattachment_id']) {
            $decryptedRole = yii::$app->utils->decryptData($post['deletable_auditattachment_id']);

            $modelAuditAttachmentUpdate = AuditAttachments::updateAll([
                'is_deleted' => 1,
                'updated_by' => \Yii::$app->user->getId()
            ], ' audit_attachment_id=' . $decryptedRole);
            if ($modelAuditAttachmentUpdate) {
                Yii::$app->session->setFlash('success', "Attachment deleted successfully");
            } else {
                Yii::$app->session->setFlash('success', "Attachment not deleted");
            }
        } else {
            Yii::$app->session->setFlash('error', "Invalid request");
        }
        return $this->redirect([
            'audits/reports',
            'id' => $audit_schedule_id
        ]);
    }

    public function actionMpdfDemo1()
    {
        $model = AuditsSchedules::findOne(Yii::$app->utils->decryptData($_GET['id']), 'status', 'IN', [
            '3',
            '4'
        ]);
        $modelAuditAttachment = new AuditAttachments();
        $modelAudit = Audits::getAuditDetails($model->audit_id); // get Audit details
        $modelChecklists = Checklists::getCheckListAuditType($modelAudit->checklist_id);
        $answersCount = Answers::getAnswerCount($model->audit_schedule_id);
        $getAuditAttachments = AuditAttachments::getAuditAttachments($model->audit_schedule_id);


        $subSectionList = ArrayHelper::map(SubSections::getList(), 'sub_section_id', 'ss_subsection_name');
        $sectionsList = ArrayHelper::map(Sections::getList(), 'section_id', 's_section_name');

        $questionsList = Audits::getAuditQuestionsAndAnswers($model->audit_schedule_id);

        $sectionSpecificQuestions = ArrayHelper::index($questionsList, null, [
            'q_section',
            'q_sub_section'
        ]);

        $arrTotalQuestions = [];
        foreach ($sectionSpecificQuestions as $sectionId => $questions) {
            if ($sectionId) {
                $modelAnswers = [];
                foreach ($questions as $subsectionId => $question) {
                    if ($subsectionId) {

                        $modelAnswers[$subsectionId]['subSectionName'] = isset($subSectionList[$subsectionId]) ? $subSectionList[$subsectionId] : $subsectionId;
                        ArrayHelper::multisort($question, 'question_id');
                        $modelAnswers[$subsectionId]['questions'] = $question;
                        $modelAnswers[$subsectionId]['sectionName'] = isset($sectionsList[$sectionId]) ? $sectionsList[$sectionId] : $sectionId;

                    }
                }
                $arrTotalQuestions[$sectionId] = $modelAnswers;
            }
        }

        $totalScore = AuditsSchedules::getAuditScore($model->audit_schedule_id);

        $tickets = Tickets::find()->where([
            'audit_schedule_id' => $model->audit_schedule_id
        ])
            ->asArray()
            ->all();
        $nonCompliance = count($tickets);

        $chronicTickets = Tickets::find()->where([
            'audit_schedule_id' => $model->audit_schedule_id,
            'chronicity' => 1
        ])
            ->asArray()
            ->all();
        $chronicIssues = count($chronicTickets);

        $content = $this->renderPartial('download', [
            'model' => $model,
            'modelAudit' => $modelAudit,
            'modelAnswers' => $arrTotalQuestions,
            'modelChecklists' => $modelChecklists,
            'modelAuditAttachment' => $modelAuditAttachment,
            'answersCount' => $answersCount,
            'getAuditAttachments' => $getAuditAttachments,
            'totalScore' => $totalScore,
            'nonCompliance' => $nonCompliance,
            'chronicIssues' => $chronicIssues,
            'chronicTickets'=>$chronicTickets

        ]);

        $inlineCss = '.h4color{color:orange; text-align:center} 
        table, th, tr ,td  { border: 1px solid #ddd!important;} 
        .greenbk{background: #3bb540!important;}
        .yellowbk{background: #d6d63f!important;}
        .redbk{background: red!important;}
        .slidecontainer {
            width: 100%;
         }
        .span-text-color {color: #fff; margin-bottom:10px;}
        .red {
            color: red !important;
        }.green {
            color: #3bb540 !important;
        }
        .yellow{
            color: #d6d63f !important;
        }

	        .table {
	            width: 100%;
	            max-width: 100%;
	            margin-bottom: 20px;
	        }
	        
	        dashboard.css:179
	        .table {
	            table-layout: fixed;
	        }
	        tables.less:10
	        table {
	            background-color: transparent;
	        }
	        normalize.less:416
	        table {
	            border-spacing: 0;
	            border-collapse: collapse;
	        }
	        body {
	            font-family: \'OMintranet Sans\' !important;
	        }
	        
	         @media all{
            .font_next{font-family:DoodlePen}table{border-collapse:collapse;width:100%}td{border:1px solid #000}.page-break {display: none;}
        }
        @media print{
            .page-break{display: block;page-break-before: always;}
        }
        ';

        // The name of the file that we want to create if it doesn't exist.
        $hotel = $model->audit->hotel->hotel_name;
        $department = $model->audit->department->department_name;
        $date = date('M Y', strtotime($model->start_date));
        $file = \Yii::$app->basePath . '/reports/' . $hotel . ' ' . $department . ' ' . $date . '.pdf';
        if (!is_file($file)) {
            file_put_contents($file, '');
        }

        // /print_r($content);exit;
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE, // leaner size using standard fonts
            //'format' => Pdf::FORMAT_A4,
            'filename' => $file,
            'destination' => Pdf::DEST_FILE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,

            'cssInline' => $inlineCss,
            'methods' => [
                'SetHeader' => ['Green Park Corporate Audit Team.'],
                'SetFooter' => ['{PAGENO}'],
            ],
            'content' => $content
        ]);

        return $pdf->render();
    }

    public function actionDownload()
    {
        return $this->render('download');
    }

    /**
     * @return mixed
     * @throws \yii\db\Exception
     */
    public function actionGenerateAcrossSectionReport()
    {
        $model = AuditsSchedules::findOne(Yii::$app->utils->decryptData($_GET['id']));

        if ($model->audit->checklist->cl_audit_span == 2) {
            $sch_id = $model->audit_schedule_id;

            $data = Audits::getAuditReportAcrossSection($sch_id, $model->audit_schedule_name);

            $hotel = $model->audit->hotel->hotel_name;
            $department = $model->audit->department->department_name;
            $date = date('M Y', strtotime($model->start_date));

            $file = \Yii::$app->basePath . '/reports/' . $hotel . ' ' . $department . ' ' . $date . '_ACROSS_SECTION_REPORT.pdf';
            if (!is_file($file)) {
                file_put_contents($file, '');
            }


            $inlineCss = '.h4color{color:orange; text-align:center} 
        table, th, tr ,td  { border: 1px solid #ddd!important;} 
        .greenbk{background: #3bb540!important;}
        .yellowbk{background: #d6d63f!important;}
        .redbk{background: red!important;}
        #base {
            background: green;
            display: inline-block;
            height: 11px;
            margin-left: 76px;
            margin-top: 8px;
            position: relative;
            width: 16px;
        }
        #base:before {
            border-bottom: 17px solid green!important;
            border-left: 12px solid transparent!important;
            border-right: 12px solid transparent!important;
            content: ""!important;
            height: 0!important;
            left: 0!important;
            position: absolute!important;
            top: -15px!important;
            left: -4px!important;
            width: 0!important;
        }
        .span-text-color {color: #fff; margin-bottom:10px;}
        .red {
            color: red !important;
        }.green {
            color: #3bb540 !important;
        }
        .yellow{
            color: #d6d63f !important;
        }
.circle {
           float: left !important;
             width: 15px !important;
             height: 15px !important;
             border-radius: 25px!important;
             border: 1px solid !important;
             background: #3bb540 !important;
         }
         .table {
             width: 100%;
             max-width: 100%;
             margin-bottom: 20px;
         }
         
         dashboard.css:179
         .table {
             table-layout: fixed;
         }
         tables.less:10
         table {
             background-color: transparent;
         }
         normalize.less:416
         table {
             border-spacing: 0;
             border-collapse: collapse;
         }
         body {
             font-family: \'OMintranet Sans\' !important;
         }
          @media all{
            .font_next{font-family:DoodlePen}table{border-collapse:collapse;width:100%}td{border:1px solid #000}.page-break {display: none;}
        }
        @media print{
            .page-break{display: block;page-break-before: always;}
        }
        ';


            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE, // leaner size using standard fonts

                'methods' => [
                    'SetHeader' => ['Green Park Corporate Audit Team.'],
                    'SetFooter' => ['{PAGENO}'],
                ],
                'filename' => $file,
                'destination' => Pdf::DEST_FILE,
                // 'cssFile' => '@web/css/site.css',
                'cssInline' => $inlineCss,
                'content' => $data
            ]);
            return $pdf->render();
        }
    }

    public function actionSendAttachments()
    {
        if (Yii::$app->request->isAjax) {
            try {
                $postInformation = Yii::$app->request->post();
                if ($postInformation) {
                    $auditName = $postInformation['auditName'];
                    $email = $postInformation['email'];
                    $emailList = explode(',', $email);
                    $attachments = $postInformation['attachments'];
                    $download = Yii::getAlias('@webroot') . '/img/answers_attachments/' . $auditName . '.zip';
                    $zip = new \ZipArchive();
                    if (!$zip->open($download, \ZipArchive::CREATE)) {
                        return false;
                    }
                    foreach ($attachments as $file) {
                        $file = Yii::getAlias('@webroot') . $file;
                        $file = str_replace('\\', '/', $file);

                        if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                            continue;
                        }

                        $file = realpath($file);

                        if (is_file($file) === true) {
                            $new_filename = substr($file, strrpos($file, '/') + 1);
                            $zip->addFromString($new_filename, file_get_contents($file));
                        }
                    }


                    $zip->close();


                    $audit = AuditsSchedules::find()->joinWith(['audit.hotel', 'audit.department'])->where(['audit_schedule_name' => $auditName])->asArray()->one();
                    $hotel = '';
                    $department = '';
                    if ($audit && isset($audit['audit'])) {
                        $hotel = $audit['audit']['hotel']['hotel_name'];
                        $department = $audit['audit']['department']['department_name'];
                    }
                    $date = isset($audit['start_date']) ? $audit['start_date'] : '';
                    $date = $date ? date('M Y', strtotime($date)) : '';
                    $subject = 'Audit Report - ' . $auditName . ' for ' . $hotel . ' ' . $department . ' ' . $date;
                    $auditName = \yii\helpers\Url::base(true) . "/img/answers_attachments/" . $auditName . '.zip';

                    EmailsComponent::sendAuditReportToUser($emailList, $download, $subject, $auditName);
                    //unlink($download);
                    $response['status'] = true;
                    $response['message'] = 'Email sent successfully';
                }
            } catch (Exception $e) {
                $response['status'] = false;
                $response['message'] = 'Error in sending attachment, please try again later.';
            }

            return Json::encode($response);
        }

    }

    /**
     *
     */
    public function actionGetAuditorList()
    {
        $finalArray = [];

        $post = Yii::$app->request->post();
        if (isset($post['depdrop_parents'])) {

            $parents = $post['depdrop_all_params'];
            $departmentId = $parents['department_id'];
            $hotelId = $parents['hotel_id'];
            $locationId = $parents['audits-location_id'];

            if ($departmentId && $hotelId && $locationId) {
                $users = Audits::getAuditorsList($departmentId, $hotelId, $locationId);
                foreach ($users as $user) {
                    $array = [];
                    $array['id'] = $user['user_id'];
                    $array['name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $finalArray[] = $array;
                }
                echo Json::encode([
                    'output' => $finalArray,
                    'selected' => ''
                ]);
                return;
            }
        }
        echo Json::encode([
            'output' => '',
            'selected' => ''
        ]);
    }

    public function actionGetRowDetails()
    {
        if (Yii::$app->request->isAjax) {
            $expandRowKey = Yii::$app->request->post('expandRowKey');
            $searchModel = Yii::createObject(\app\models\search\AuditsSchedulesSearch::className());
            $dataProvider = $searchModel->search([], $expandRowKey);
            return $this->renderPartial('expandActiveAudits', ['dataProvider' => $dataProvider]);
        }
    }

    public function actionDeleteAudit()
    {
        $output = [];
        $post = yii::$app->request->post();
        if ($post) {

            $decryptedAuditScheduleId = yii::$app->utils->decryptData($post['update_auditschedule_id']);
            $fromIndex = isset($post['fromIndex']) ? $post['fromIndex'] : '';
            if (!empty($decryptedAuditScheduleId)) {

                $getAuditId = AuditsSchedules::find()->where([
                    'audit_schedule_id' => $decryptedAuditScheduleId
                ])->one();

                if ($getAuditId) {

                    AuditsSchedules::updateAll([
                        'is_deleted' => 1,
                        'updated_by' => \Yii::$app->user->getId()
                    ], 'audit_schedule_id=' . $decryptedAuditScheduleId);

                    self::saveAuditEndDate($getAuditId->audit_id);

                    $arrData = [];
                    $arrData['module'] = 'audit';
                    $arrData['type'] = 'delete';
                    $arrData['message'] = "Audit - <b>" . $getAuditId->audit_schedule_name . '</b> is deleted by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
                    Yii::$app->events->createEvent($arrData);
                    Yii::$app->session->setFlash('success', 'Schedule Audit ' . $getAuditId->audit_schedule_name . ' deleted successfully');
                    if (!$fromIndex) {
                        return $this->redirect([
                            'update?id=' . Yii::$app->utils->encryptData($getAuditId->audit_id)
                        ]);
                    } else {
                        return $this->redirect([
                            'index'
                        ]);
                    }
                }
            }
        } else {
            $output = [
                'error' => "Invalid request"
            ];
        }
        return json_encode($output);
    }

    /**
     * @param $parentAuditId
     */
    public static function saveAuditEndDate($parentAuditId)
    {
        $maxDate = AuditsSchedules::find()->where(['audit_id' => $parentAuditId, 'is_deleted' => 0])->max('end_date');
        if ($maxDate) {
            Audits::updateAll([
                'end_date' => $maxDate
            ], 'audit_id=' . $parentAuditId);
        }
    }
}
