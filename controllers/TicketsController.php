<?php

namespace app\controllers;

use app\models\Audits;
use Yii;
use app\components\AccessRule;
use app\models\Tickets;
use app\models\search\TicketsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\TicketComments;
use app\models\Hotels;
use yii\helpers\Json;
use app\models\HotelDepartments;
use app\models\HotelDepartmentSections;
use app\models\HotelDepartmentSubSections;
use app\models\User;
use app\models\Checklists;
use app\models\AuditsSchedules;
use app\models\Answers;
use app\models\TicketAttachments;
use app\models\AuditAttachments;
use yii\web\UploadedFile;
use app\models\TicketHistory;
use app\models\TicketProcessCritical;
use app\models\Departments;
use app\components\EmailsComponent;
use app\models\UserDepartments;

/**
 * TicketsController implements the CRUD actions for Tickets model.
 */
class TicketsController extends Controller {

    public $layout = 'dashboard_layout';

    /**
     * @inheritdoc
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
                'index',
                'reports',
            ], // only be applied to
            'rules' => [
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('tickets/create'),
                    'actions' => [
                        'create'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('tickets/update'),
                    'actions' => [
                        'update'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('tickets'),
                    'actions' => [
                        'index', 'reports'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('tickets/delete'),
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
     * Lists all Tickets models.
     *
     * @return mixed
     */
    public function actionIndex() {
        ini_set('memory_limit', '1024M');
        $searchModel = new TicketsSearch();
        $dataProvider = $searchModel->searchActiveTickets(Yii::$app->request->queryParams);
        $dataArchivedProvider = $searchModel->searchArchivedTickets(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'dataArchivedProvider' => $dataArchivedProvider
        ]);
    }

    /**
     * Displays a single Tickets model.
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
     * Creates a new Tickets model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate() {
        $model = new Tickets();
        $modelTicketHistory = new TicketHistory();
        $modelTicketAttachment = new TicketAttachments();
        if ($model->load(Yii::$app->request->post())) {
            $postInfo = Yii::$app->request->post('Tickets');
            $model->ticket_name = "TKT000";
            $model->is_deleted = 0;
            $model->created_by = \Yii::$app->user->id;
            $model->updated_by = \Yii::$app->user->id;
            $model->due_date = Yii::$app->formatter->asDate($postInfo['due_date'], 'php:Y-m-d');
            $model->status = 1;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    $uploadedFile = UploadedFile::getInstanceByName("TicketAttachments[ticket_attachment_path]");
                    if ($uploadedFile && $uploadedFile->error == 0) {
                        $saveattachment = $modelTicketAttachment->saveTicketAttachments("TicketAttachments[ticket_attachment_path]", $model->ticket_id);
                        if (!$saveattachment['status']) {
                            throw new \Exception($saveattachment['message']);
                        }
                    }
                    $model->ticket_name = $model->ticket_name . $model->ticket_id;
                    Tickets::updateAll([
                        'ticket_name' => $model->ticket_name
                            ], 'ticket_id=' . $model->ticket_id);

                    $modelTicketHistory->ticket_id = $model->ticket_id;
                    $getUserName = Tickets::getUserName($model->assigned_user_id);
                    $modelTicketHistory->ticket_message = "Ticket created and assigned to " . ucfirst($getUserName->first_name . ' ' . $getUserName->last_name);
                    if ($modelTicketHistory->save()) {
                        $valid = true;
                    } else {
                        throw new \Exception('Error saving Tickets history');
                    }
                } else {
                    throw new \Exception('Error saving Tickets');
                }
                if ($valid) {

                    Tickets::sendNotifications($model->ticket_id, 'ticketAssigned');

                    /*
                     * Email to department mail ids if process critical is true
                     */
                    if($model->process_critical_dynamic == 1 || $model->chronicity == 1){
                        $user_id = UserDepartments::getDepartmentHead($model->hotel_id, $model->department_id);
                        $deptHotelModel = \app\models\HotelDepartments::findOne(['department_id' => $model->department_id, 'hotel_id' =>$model->hotel_id,'is_deleted' => 0]);
                        if ($deptHotelModel && $deptHotelModel->configured_emails) {
                            EmailsComponent::sendNonComplaintToDepartment(explode(',', $deptHotelModel->configured_emails), $model->ticket_name, $user_id);
                        }
                    }

                    $data = [];
                    $data['module'] = 'ticket';
                    $data['type'] = 'create';
                    $data['message'] = "Ticket - <b>" . $model->ticket_name . '</b> is created by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
                    Yii::$app->events->createEvent($data);

                    $transaction->commit();
                    Yii::$app->session->setFlash('success', "Ticket  $model->ticket_name created successfully");
                    return $this->redirect(\Yii::$app->urlManager->createUrl([
                                        'tickets'
                    ]));
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getTraceAsString());
            }
        }
        return $this->render('create', [
                    'model' => $model,
                    'modelTicketAttachment' => $modelTicketAttachment
        ]);
    }

    /**
     * Updates an existing Tickets model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel(Yii::$app->utils->decryptData($id));
        $oldStaff = $model->assigned_user_id;
        $status = $model->status;
        if ($model->load(Yii::$app->request->post())) {
            $model->due_date = Yii::$app->formatter->asDate($model->due_date, 'php:Y-m-d');

            if ($model->save()) {

                if ($oldStaff != $model->assigned_user_id) {
                    Tickets::sendNotifications($model->ticket_id, 'ticketAssigned');

                    $modelTicketHistory = new TicketHistory();
                    $modelTicketHistory->ticket_id = $model->ticket_id;
                    $getUserName = Tickets::getUserName($model->assigned_user_id);
                    $modelTicketHistory->ticket_message = "Ticket is assigned to " . ucfirst($getUserName->first_name . ' ' . $getUserName->last_name);
                    $modelTicketHistory->save();
                }

                if ($status != $model->status) {
                    Tickets::sendStatusChangeNotification($model->ticket_id, 2);
                }

                $data = [];
                $data['module'] = 'ticket';
                $data['type'] = 'update';
                $data['message'] = "Ticket - <b>" . $model->ticket_name . '</b> is updated by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';


                Yii::$app->events->createEvent($data);

                Yii::$app->session->setFlash('success', "Tickets $model->ticket_name updated successfully");
                return $this->redirect(\Yii::$app->urlManager->createUrl([
                                    'tickets'
                ]));
            } else {
                throw new \Exception('Error saving Tickets');
            }
        }

        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Tickets model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete() {
        $post = yii::$app->request->post();
        $decryptedTickets = yii::$app->utils->decryptData($post['deletable_ticket_id']);

        $ticket = Tickets::findOne($decryptedTickets);
        if ($ticket->status != 5) {
            Yii::$app->session->setFlash('error', "Ticket cannot be deleted");
        } else {
            $ticket->is_deleted = 1;
            $ticket->save();
            $data = [];
            $data['module'] = 'ticket';
            $data['type'] = 'delete';
            $data['message'] = "Ticket - <b>" . $ticket->ticket_name . '</b> is deleted by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
            Yii::$app->events->createEvent($data);
            Yii::$app->session->setFlash('success', "Ticket Deleted successfully");
        }

        return $this->redirect([
                    'index'
        ]);
    }

    /**
     * Finds the Tickets model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Tickets the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Tickets::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     */
    public function actionHotel() {
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

    public function actionDepartment() {
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

    public function actionSection() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_all_params'];
            $departmentId = $parents['department_id'];
            $hotelId = $parents['hotel_id'];
            if ($hotelId && $departmentId) {


                header('Content-type: application/json');

                $sections = Tickets::getHotelSections($hotelId, $departmentId);

                $resultArray = [];
                if (!empty($sections)) {
                    foreach ($sections as $section) {
                        $list = [];
                        if (isset($section['section'])) {
                            $list['id'] = $section['section_id'];
                            $list['name'] = $section['department']['department_name'] . '-' . $section['section']['s_section_name'];
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

    public function actionSubSection() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_all_params'];
            $sectionId = $parents['section_id'];
            $departmentId = $parents['department_id'];
            $hotelId = $parents['hotel_id'];
            if ($departmentId && $hotelId && $sectionId) {

                header('Content-type: application/json');
                $subsections = Tickets::getHotelSubSections($sectionId, $departmentId, $hotelId);
                $resultArray = [];
                if (!empty($subsections)) {
                    foreach ($subsections as $subSection) {
                        $list = [];
                        if (isset($subSection['subSection'])) {
                            $list['id'] = $subSection['sub_section_id'];
                            $list['name'] = $subSection['section']['s_section_name'] . '-' . $subSection['subSection']['ss_subsection_name'];
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

    public function actionReports($id) {
        $model = Tickets::findOne(Yii::$app->utils->decryptData($id));
        $ticketId = Yii::$app->utils->decryptData($id);
        $modelTicketAttachment = new TicketAttachments();
        $modelTicketAttachment->scenario = "create_attachment";
        $modelComments = new TicketComments();

        // For get AuditSchedule Name
        $modelAuditScheduleName = Tickets::getgetAuditsSchedulesName($model->audit_schedule_id);
        // For get Audit Id and Checklist Id
        $modelAuditSchedule = Tickets::getAuditsSchedules($model->audit_schedule_id);
        // For get Answer details
        $modelAnswers = Tickets::getAnswers($model->answer_id);
        // For Tickets Comments
        $modelTicketsComments = Tickets::getTicketsComments($ticketId);


        //echo '<pre>'; print_r($model);die();
        // For Tickets Attachments
        $modelTicketsAttachments = Tickets::getTicketsAttachments($ticketId);
        // For Ticket Attachment Count
        $modelTicketsAttachmentsCount = Tickets::getTicketsAttachmentsCount($ticketId);

        // For Tickets History
        $modelTicketHistory = Tickets::getTicketHistory($ticketId);

        $root_cause_analysis = TicketProcessCritical::find()->where(['ticket_id' => Yii::$app->utils->decryptData($id)])->one();

        if (empty($root_cause_analysis)) {
            $root_cause_analysis = new TicketProcessCritical();
        }

        //print_r($root_cause_analysis); die();

        return $this->render('reports', [
                    'model' => $model,
                    'modelAuditSchedule' => $modelAuditSchedule,
                    'modelComments' => $modelComments,
                    'modelAuditScheduleName' => $modelAuditScheduleName,
                    'modelAnswers' => $modelAnswers,
                    'modelTicketsComments' => $modelTicketsComments,
                    'modelTicketsAttachments' => $modelTicketsAttachments,
                    'modelTicketAttachment' => $modelTicketAttachment,
                    'modelTicketsAttachmentsCount' => $modelTicketsAttachmentsCount,
                    'modelTicketHistory' => $modelTicketHistory,
                    'root_cause_analysis' => $root_cause_analysis
        ]);
    }

    public function actionSaveTicketPreference($id) {

        if (\Yii::$app->request->isPost) {

            $model = TicketProcessCritical::find()->where(['ticket_id' => Yii::$app->utils->decryptData($id)])->one();

            if (empty($model)) {
                $model = new TicketProcessCritical();

                $model->ticket_id = Yii::$app->utils->decryptData($id);
                $model->created_by = \Yii::$app->user->id;
            }
            $post = Yii::$app->request->post();
            if ($model->load($post)) {
                if ($post['TicketProcessCritical']['stop_notifications_until_date']) {
                    $model->stop_notifications_until_date = date("Y-m-d", strtotime($model->stop_notifications_until_date));
                } else {
                    $model->stop_notifications_until_date = NULL;
                }
                if ($model->save()) {
                    $output = [
                        'success' => 'Saved Successfully'
                    ];
                }
            } else {
                $output = [
                    'error' => $model->getErrorSummary(true)
                ];
            }
            return json_encode($output);
        }
    }

    public function actionUpload() {
        $valid = false;
        $post = Yii::$app->request->post();
        $ticket_id = $post['ticket_id'];
        $model = new TicketAttachments();
        $modelTicketHistory = new TicketHistory();
        if ($post) {
            $model->ticket_attachment_path = UploadedFile::getInstance($model, 'ticket_attachment_path');
            $file = $model->ticket_attachment_path;
            $imageName = $file->baseName . '_' . time() . '.' . $model->ticket_attachment_path->extension;

            $model->ticket_id = $ticket_id;
            $model->created_by = \Yii::$app->user->id;
            $model->updated_by = \Yii::$app->user->id;
            $model->ticket_attachment_path = $imageName;
            $image_path = \Yii::getAlias('img/answers_attachments') . "/" . $imageName;
            $transaction = Yii::$app->db->beginTransaction();
            try {

                $modelTicketHistory->load(Yii::$app->request->post());
                if ($model->save()) {
                    $file->saveAs($image_path);

                    $modelTicketHistory->ticket_id = $ticket_id;

                    $getUserName = Tickets::getUserName(\Yii::$app->user->id);
                    $insertText = ucfirst($getUserName->first_name . ' ' . $getUserName->last_name) . " added attachment";
                    $modelTicketHistory->ticket_message = $insertText;

                    if ($modelTicketHistory->save()) {
                        $valid = true;
                    } else {

                        Yii::$app->session->setFlash('error', "Ticket attachment history not added");
                    }
                } else {

                    Yii::$app->session->setFlash('error', "Attachment not added");
                }
                if ($valid) {
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', "Attachment added successfully");
                    return $this->redirect([
                                'tickets/reports',
                                'id' => Yii::$app->utils->encryptData($ticket_id)
                    ]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->redirect([
                    'tickets/reports',
                    'id' => Yii::$app->utils->encryptData($ticket_id)
        ]);
    }

    public function actionCancel() {
        $modelTicketHistory = new TicketHistory();
        $post = yii::$app->request->post();
        $audit_ticket_id = Yii::$app->utils->encryptData($post['cancel_ticekts_id']);

        $output = [];
        if ($post && $post['cancel_ticekts_id']) {
            $decryptedTickets = yii::$app->utils->decryptData($post['cancel_ticekts_id']);
            $modelAuditAttachmentUpdate = Tickets::updateAll([
                        'status' => 5,
                        'updated_by' => \Yii::$app->user->getId()
                            ], ' ticket_id=' . $decryptedTickets);


            $modelTicketHistory->ticket_id = yii::$app->utils->decryptData($post['cancel_ticekts_id']);
            $getUserName = Tickets::getUserName(Yii::$app->user->getId());
            $modelTicketHistory->ticket_message = "Ticket is cancelled by " . ucfirst($getUserName->first_name . '' . $getUserName->last_name);
            $modelTicketHistory->save();


            $ticket = Tickets::findOne($decryptedTickets);
            $data = [];
            $data['module'] = 'ticket';
            $data['type'] = 'cancel';
            $data['message'] = "Ticket - <b>" . $ticket->ticket_name . '</b> is cancelled by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
            Yii::$app->events->createEvent($data);

            Yii::$app->session->setFlash('success', "Tickets cancelled successfully");
        } else {
            Yii::$app->session->setFlash('error', "Invalid request");
        }
        return $this->redirect(['index']);
    }

    public
            function actionComments() {
        $valid = false;
        $modelTicketHistory = new TicketHistory();
        $post = yii::$app->request->post();
        $ticket_id = Yii::$app->utils->encryptData($post['ticket_id']);
        $output = [];
        if ($post && $post['ticket_id']) {
            $modelComments = new TicketComments();
            $modelComments->ticket_id = $post['ticket_id'];
            $modelComments->ticket_comment = $post['TicketComments']['ticket_comment'];
            $modelComments->ticket_comment_status = 0;

            $transaction = Yii::$app->db->beginTransaction();
            try {

                $modelTicketHistory->load(Yii::$app->request->post());
                if ($modelComments->save()) {
                    $modelTicketHistory->ticket_id = $post['ticket_id'];
                    $getUserName = Tickets::getUserName(\Yii::$app->user->id);
                    $insertText = ucfirst($getUserName->first_name . ' ' . $getUserName->last_name) . " added comment";
                    $modelTicketHistory->ticket_message = $insertText;

                    if ($modelTicketHistory->save()) {
                        $valid = true;
                    } else {
                        Yii::$app->session->setFlash('error', "Ticket comment history not added");
                    }
                } else {
                    Yii::$app->session->setFlash('error', "Comments not added");
                }
                if ($valid) {
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', "Comments added successfully");
                    return $this->redirect([
                                'tickets/reports',
                                'id' => $ticket_id
                    ]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        } else {
            Yii::$app->session->setFlash('error', "Invalid request");
        }

        return $this->redirect([
                    'tickets/reports',
                    'id' => $ticket_id
        ]);
    }

    public
            function actionGetAuditUserId() {
        $post = yii::$app->request->post();
        if ($post) {
            $decryptedTicketId = yii::$app->utils->decryptData($post['updat_ticket_id']);
            if (!empty($decryptedTicketId)) {
                try {
                    $getAssignedUserId = Tickets::findOne($decryptedTicketId);
                    return $getAssignedUserId->assigned_user_id;
                } catch (\Exception $e) {
                    throw $e;
                }
            }
        }
    }

    /**
     *
     * @return string
     */
    public function actionUpdateAuditUser() {
        $output = [];
        $post = yii::$app->request->post();

        $assigned_user_id = $post['Tickets']['assigned_user_id'];
        $update_tickets_id = yii::$app->utils->decryptData($post['updat_ticket_id']);

        if ($post && $post['updat_ticket_id']) {
            $modelTicketHistory = new TicketHistory();

            $modelTicketHistory->load(Yii::$app->request->post());
            if ($post['Tickets']['assigned_user_id'] != "") {

                $modelUpdateTicketsUser = Tickets::updateAll([
                            'assigned_user_id' => $post['Tickets']['assigned_user_id'],
                            'updated_by' => \Yii::$app->user->getId()
                                ], 'ticket_id=' . $update_tickets_id);

                if ($post['Tickets']['due_date'] != "") {

                    $modelUpdateTicketsDuedate = Tickets::updateAll([
                                'due_date' => Yii::$app->formatter->asDate($post['Tickets']['due_date'], 'php:Y-m-d'),
                                'updated_by' => \Yii::$app->user->getId()
                                    ], 'ticket_id=' . $update_tickets_id);


                    $modelTicketHistory->ticket_id = $update_tickets_id;
                    if ($modelUpdateTicketsUser) {
                        $getUserName = Tickets::getUserName($assigned_user_id);
                        $insertText = "Ticket assigned to  " . ucfirst($getUserName->first_name) . ' ' . $getUserName->last_name;
                    } else if ($modelUpdateTicketsDuedate) {
                        $getUserName = Tickets::getUserName($post['user_id']);
                        $insertText = ucfirst($getUserName->first_name) . ' ' . $getUserName->last_name . " updated ticket due date";
                    }

                    $modelTicketHistory->ticket_message = $insertText;

                    if ($modelTicketHistory->save()) {


                        $model = Tickets::findOne($update_tickets_id);

                        if ($modelUpdateTicketsUser) {
                            Tickets::sendNotifications($update_tickets_id, 'ticketAssigned');
                        }

                        $data = [];
                        $data['module'] = 'ticket';
                        $data['type'] = 'update';
                        $data['message'] = "Ticket - <b>" . $model->ticket_name . '</b> is updated by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
                        Yii::$app->events->createEvent($data);

                        $output = [
                            'success' => 'Details updated successfully'
                        ];
                    } else {

                        $output = [
                            'error' => 'Ticket auto assigned history not added'
                        ];
                    }
                } else {
                    $output = [
                        'error' => "Please select Due Date"
                    ];
                }
            } else {
                $output = [
                    'error' => "Please select Assigned to user"
                ];
            }
        } else {
            $output = [
                'error' => "Invalid request"
            ];
        }
        return json_encode($output);
    }

    /**
     *
     */
    public function actionGetStaffList() {
        $finalArray = [];

        $post = Yii::$app->request->post();
        if (isset($post['depdrop_parents'])) {

            $parents = $post['depdrop_all_params'];
            $departmentId = $parents['department_id'];
            $hotelId = $parents['hotel_id'];
            $locationId = $parents['tickets-location_id'];

            if ($departmentId && $hotelId && $locationId) {
                $users = Audits::getAuditorsList($departmentId, $hotelId, $locationId, 3);
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

    public function actionUpdateTicketAssigned() {
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $userId = $post['userId'];
            $ticketId = $post['ticketId'];
            $modelTicketHistory = new TicketHistory();
            $modelTicketHistory->load(Yii::$app->request->post());
            Tickets::updateAll([
                'assigned_user_id' => $userId,
                'updated_by' => \Yii::$app->user->getId()
                    ], 'ticket_id=' . $ticketId);
            $modelTicketHistory->ticket_id = $ticketId;
            $getUserName = Tickets::getUserName($userId);
            $insertText = "Ticket assigned to  " . ucfirst($getUserName->first_name) . ' ' . $getUserName->last_name;
            $modelTicketHistory->ticket_message = $insertText;
            $model = Tickets::findOne($ticketId);
            Tickets::sendNotifications($ticketId, 'ticketAssigned');
            $data = [];
            $data['module'] = 'ticket';
            $data['type'] = 'update';
            $data['message'] = "Ticket - <b>" . $model->ticket_name . '</b> is updated by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
            Yii::$app->events->createEvent($data);
            return true;
        }
    }

}
