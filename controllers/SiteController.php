<?php

namespace app\controllers;

use app\components\SchedulerComponent;
use app\models\Alertmaster;
use app\models\HotelDepartments;
use app\models\UserDepartments;
use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\components\EmailsComponent;
use app\models\LoginForm;
use app\models\User;
use app\models\ChangePasswordForm;
use app\models\Audits;
use app\models\search\TicketsSearch;
use app\models\Tickets;
use app\components\UtilsComponent;
use app\models\Departments;
use app\models\search\AuditsSearch;
use kartik\mpdf\Pdf;

class SiteController extends Controller
{

    public $layout = 'login_layout';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'logout'
                ],
                'rules' => [
                    [
                        'actions' => [
                            'logout'
                        ],
                        'allow' => true,
                        'roles' => [
                            '@'
                        ]
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => [
                        'post'
                    ]
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null
            ]
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (yii::$app->user->isGuest) {
            $this->layout = 'login_layout';
            $loginModel = new LoginForm();
            return $this->render('login', [
                'loginModel' => $loginModel
            ]);
        }
        return $this->redirect(yii::$app->urlManager->createAbsoluteUrl('site/dashboard'));
    }

    public function actionVerifyLogin()
    {
        $post = yii::$app->request->post();
        $loginModel = new LoginForm();
        if (yii::$app->request->isAjax && $post) {
            if ($loginModel->load($post) && $loginModel->login()) {

                $userAction = 'site/welcome';
                if (Yii::$app->authManager->checkPermissionAccess('site/dashboard')) {
                    $userAction = 'site/dashboard';
                }

                $output = [
                    'success' => 'Login Successful!',
                    'redirect_url' => yii::$app->urlManager->createAbsoluteUrl($userAction)
                ];
            } else {
                $output = [
                    'error' => $loginModel->getFirstError('password')
                ];
            }
        } else {
            $output = [
                'error' => 'Invalid request'
            ];
        }
        return json_encode($output);
    }

    public function actionDashboard()
    {
        if (!yii::$app->user->isGuest) {

            $this->layout = 'dashboard_layout';

            $auditModel = new Audits();
            $ticketModel = new Tickets();

            /**
             * *Statics tab data **
             */
            $countAudits = $auditModel->getAuditCount(Yii::$app->request->queryParams);

            $overdueAudits = $auditModel->getOverdueAudits(Yii::$app->request->queryParams);

            $upcomingAudits = $auditModel->getUpcomingAudits(Yii::$app->request->queryParams);
            /**
             * *Statics tab data **
             */

            /**
             * *ticket distribution tab data **
             */
            $ticketLocationData = $ticketModel->getTicketLocationData(Yii::$app->request->queryParams);
            $ticketHotelData = $ticketModel->getTicketHotelData(Yii::$app->request->queryParams);
            $ticketDepartmentData = $ticketModel->getTicketDeptData(Yii::$app->request->queryParams);

            $searchModel = new TicketsSearch();
            $dataProvider = $searchModel->searchRecentTickets(Yii::$app->request->queryParams);
            /**
             * *ticket distribution tab data **
             */

            /**
             * *hotels tab data **
             */

            $hotelAuditData = $auditModel->getHotelAuditData(Yii::$app->request->queryParams);
            $hotelTicketData = $ticketModel->getHotelTicketData(Yii::$app->request->queryParams);
            $hotelChronicData = $ticketModel->getHotelChronicData(Yii::$app->request->queryParams);
            $hotelOverdueTicketData = $ticketModel->getHotelOverdueTicketData(Yii::$app->request->queryParams);
            $hotelAvgData = $auditModel->getHotelAverageAuditData(Yii::$app->request->queryParams);
            $hotelAvgData1 = $auditModel->getHotelAverageAuditDataDepartment(Yii::$app->request->queryParams);

            /**
             * *hotels distribution tab data **
             */

            /**
             * *department trends tab data **
             */
            $deptModel = new Departments();
            $deptAuditData = $deptModel->getDeptAuditDataMonth(Yii::$app->request->queryParams);

            $deptAuditTicketData = $deptModel->getDeptTicketAuditDataMonth(Yii::$app->request->queryParams);
            $deptAuditChronicData = $deptModel->getChronicDeptTicketAuditDataMonth(Yii::$app->request->queryParams);
            $deptAuditOverdueData = $deptModel->getOverdueDeptTicketAuditDataMonth(Yii::$app->request->queryParams);
            $deptAuditAvgData = $deptModel->getDeptAverageAuditDataMonth(Yii::$app->request->queryParams);

            /**
             * *department trends tab data **
             */

            $searchAuditModel = new AuditsSearch();
            $dataProviderRankAuditsSchedules = $searchAuditModel->searchRankAuditsSchedules(Yii::$app->request->queryParams);


            return $this->render('index', [
                'countAudits' => $countAudits,
                'overdueAudits' => $overdueAudits,
                'upcomingAudits' => $upcomingAudits,
                'ticketLocationData' => $ticketLocationData,
                'ticketHotelData' => $ticketHotelData,
                'ticketDepartmentData' => $ticketDepartmentData,
                'hotelAuditData' => $hotelAuditData,
                'hotelTicketData' => $hotelTicketData,
                'hotelChronicData' => $hotelChronicData,
                'hotelOverdueTicketData' => $hotelOverdueTicketData,
                'hotelAvgData' => $hotelAvgData,
                'hotelAvgData1' => $hotelAvgData1,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'searchAuditModel' => $searchAuditModel,
                'dataProviderRankAuditsSchedules' => $dataProviderRankAuditsSchedules,
                'deptAuditAvgData' => $deptAuditAvgData,
                'deptAuditData' => $deptAuditData,
                'deptAuditOverdueData' => $deptAuditOverdueData,
                'deptAuditChronicData' => $deptAuditChronicData,
                'deptAuditTicketData' => $deptAuditTicketData,
                'auditModel' => $auditModel
            ]);

        }
        return $this->redirect(yii::$app->urlManager->createUrl('/'));
    }

    public function actionTest()
    {

        $hoteldepartmentModel = HotelDepartments::find()->joinWith('userDepartmentHod as u')
            ->where([
                'hotel_id' => 86,
                'department_id' => 68,
                'u.is_hod' => 1
            ])
            ->asArray()
            ->all();
        echo '<pre>';
        print_r($hoteldepartmentModel);exit;
        if ($hoteldepartmentModel['userDepartment']) {
            $user_id = $hoteldepartmentModel['userDepartment']['user_id'];
        }
        echo $user_id;exit;
        /*echo date('Y-m-d H:i:s');
        set_time_limit(3600);
        ini_set('memory_limit', '-1');
        Yii::$app->scheduler->escalationOneTickets();
        set_time_limit(30);
        ini_set('memory_limit', '128M');
        echo date('Y-m-d H:i:s');*/
        //Yii::$app->scheduler->triggerAuditsMail();
    }

    public function actionTime()
    {
        return $this->render('time-date', [
            'response' => date('H:i:s')
        ]);
    }

    public function actionDate()
    {
        return $this->render('time-date', [
            'response' => date('Y-M-d')
        ]);
    }

    public function actionDepartments($id = null)
    {
        $postsQuery = Departments::find()->select([
            'tbl_gp_departments.department_id',
            'tbl_gp_departments.department_name'
        ])
            ->where([
                'tbl_gp_audits.hotel_id' => $id,
            ])
            ->join('LEFT JOIN', 'tbl_gp_audits', 'tbl_gp_audits.department_id = tbl_gp_departments.department_id')
            ->orderBy('tbl_gp_departments.department_id DESC');

        if (Yii::$app->user && Yii::$app->user->identity->user_type != 1) {
            $return = User::getUserAssingemnts();
            $userDepartments = $return['userdepartments'];
            $postsQuery->andWhere(['tbl_gp_departments.department_id' => $userDepartments]);
        }

        $posts = $postsQuery->all();

        if (!empty($posts)) {
            echo "<option value=''>Department</option>";
            foreach ($posts as $post) {
                echo "<option value='" . $post->department_id . "'>" . $post->department_name . "</option>";
            }
        } else {
            echo "<option value=''>Department</option>";
        }
    }

    public function actionAudits($id = null, $hotel_id = null)
    {
        $posts = Audits::find()->select([
            'tbl_gp_audits.audit_id',
            'tbl_gp_audits.audit_name'
        ])
            ->where(['tbl_gp_audits.hotel_id' => $hotel_id])
            ->andFilterWhere([
                'tbl_gp_audits.department_id' => $id
            ])
            ->all();

        if (!empty($posts)) {
            foreach ($posts as $post) {
                echo "<option value='" . $post->audit_id . "'>" . $post->audit_name . "</option>";
            }
        } else {
            echo "<option>-</option>";
        }
    }

    public function actionCompareaudits()
    {
        if (Yii::$app->request->isAjax) {
            $audit_count = 0;
            $data = Yii::$app->request->post();

            if ($data['AuditsSearch']['hotel_id'] && $data['AuditsSearch']['department_id'] && $data['AuditsSearch']['audit_id']) {

                $audit_count = \app\models\AuditsSchedules::find()->where([
                    'audit_id' => $data['AuditsSearch']['audit_id'],
                    'status' => 3,
                    'is_deleted' => 0
                ])->count();

                if ($audit_count) {
                    $content = $this->reportAuditContent($data['AuditsSearch']['audit_id']);
                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => true,
                        'content' => $content,
                        'count' => $audit_count
                    ];
                } else {
                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => false,
                        'content' => 'No audit is completed',
                        'count' => $audit_count
                    ];
                }
            } else {
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'content' => 'Please select Hotel, Department and Audit ID to view report.',
                    'count' => $audit_count
                ];
            }
        }
    }

    public function actionPdfDownload()
    {
        // /$content = $this->reportAuditContent($_GET['id']);
        $content = $this->reportAuditContent($_GET['id']);
        $audits = \app\models\Audits::find()->where([
            'audit_id' => $_GET['id']
        ])->one();

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
        ';
        $pdf = new Pdf([
            'filename' => 'Report_' . $audits->audit_name . '.pdf',
            'destination' => Pdf::DEST_DOWNLOAD, // leaner size using standard fonts
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'cssFile' => '@web/css/default.css',
            'cssInline' => $inlineCss,
            'methods' => [
                'SetHeader' => ['Green Park Corporate Audit Team.'],
                'SetFooter' => ['{PAGENO}'],
            ],
            'content' => $content
        ]);
        return $pdf->render();
    }

    public function reportAuditContent($audit_id)
    {
        $audit_count = \app\models\AuditsSchedules::find()->where([
            'audit_id' => $audit_id,
            'status' => 3
        ])->count();

        $modelAudit = Audits::getAuditDetails($audit_id);
        $content = '';
        //$content .= '<div class="col-xs-12 margintables" style="margin-left:180px;">Audit Quick View Report for Audit ID - '. $auditName->audit_name . '</div>';
        $content .= '<div class="col-xs-12 margintables"><div class="box"><div class="clearfix">&nbsp;</div>
                                       <div class="box-body table-responsive no-padding">
                                       <table class="table table-hover table-bordered">
                                       <tbody>
                                            <tr>
                                                <th colspan="' . ($audit_count + 2) . '">
                                                    <h4 class="box-title h4color text-center">' . strtoupper($modelAudit->hotel->hotel_name) . '</h4>
                                                    <h4 class="box-title text-center h4color">' . strtoupper($modelAudit->checklist->cl_name) . '</h4>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="2" rowspan="2" class="text-center">' . strtoupper($modelAudit->checklist->cl_name) . '</th>
                                            </tr>
                                            <tr>';
        $auditDates = $modelAudit->getAuditDates($modelAudit->audit_id);

        foreach ($auditDates as $audits) {
            $content .= '<th  class="text-center">' . $audits['audit_schedule_name'] . '  ' . date('M Y', strtotime($audits['start_date'])) . '</th>';
        }
        // $content.= '<th rowspan="2" class="text-center">VARIANCE</th><th rowspan="2" class="text-center">% of Increase / Decrease (-/ +)</th>';
        $content .= '</tr>
<tr style="background-color: #cfe8d0;">


                        <td class="text-center">S.No</td>
                        <td class="text-center">SECTIONS</td>';
        for ($x = 1; $x <= $audit_count; $x++) {
            $content .= '<td class="text-center">SCORE OBTAINED</td>';
        }

        $content .= '</tr><tr>';

        $colorCode = '#3bb540';
        $scoreArray = array();
        foreach ($auditDates as $audits) {
            $auditData = $modelAudit->getAuditList($modelAudit->audit_id, $audits['end_date']);
            foreach ($auditData as $auditlist) {
                $scoreArray[$auditlist['s_section_name']][] = $auditlist['score'];
            }
        }

        // / print_r($scoreArray);
        $loopC = 1;
        foreach ($scoreArray as $key => $scores) {
            $content .= '<tr><td class="text-center">' . $loopC . '</td><td class="text-center">' . $key . '</td>';
            // $innerLoopC = 1;
            $scoreCount = count($scores);
            foreach ($scores as $score) {
                // echo $score;
                $colorClass = '';
                if ($score >= 80) {
                    $colorCode = '#3bb540';
                    $colorClass = 'greenbk';
                } elseif ($score < 79 && $score > 61) {
                    $colorCode = '#d6d63f';
                    $colorClass = 'yellowbk';
                } else {
                    $colorCode = '#ff0000';
                    $colorClass = 'redbk';
                }
                $content .= '<td class="text-center"><div class="circle ' . $colorClass . '" style="background: ' . $colorCode . '"></div>' . $score . '</td>';

            }
            $content .= '</tr>';
            $loopC++;
        }
        $content .= '<tr><td colspan="2" class="text-center"><b>Audit Score</b></td>';
        $innerLoopC = 1;

        $finalScore = [];

        $auditChildIds = \yii\helpers\ArrayHelper::getColumn($auditDates, 'audit_schedule_id');

        foreach ($auditChildIds as $childId) {
            $finalScore[] = \app\models\AuditsSchedules::getAuditScore($childId);

        }
        $scoreCount = count($finalScore);
        //$finalScore = array_reverse($finalScore);

        foreach ($finalScore as $score) {
            if ($score >= 80) {
                $colorCode = '#3bb540';
            } elseif ($score <= 79 && $score >= 61) {
                $colorCode = '#d6d63f';
            } else {
                $colorCode = '#ff0000';
            }
            $content .= '<td class="text-center"><div class="circle" style="background: ' . $colorCode . '"></div>' . $score . '</td>';
            $innerLoopC++;
        }
        $content .= '</tr>';
        $content .= '</tr>
                                    </tbody>
                                   </table>
                                   
                                   </div>';

        $content .= '</div></div>';

        return $content;
    }

    public function actionWelcome()
    {
        if (!yii::$app->user->isGuest) {
            $this->layout = 'dashboard_layout';
            $this->view->title = "Green Park Corporate Audit Application";
            return $this->render('welcome');
        }
        return $this->redirect(yii::$app->urlManager->createUrl('/'));
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        if (Yii::$app->user->logout()) {
            return $this->redirect(yii::$app->urlManager->createUrl('/'));
        }
    }


    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSetPassword()
    {
        $this->layout = 'login_layout';
        $model = new LoginForm();

        $model->setscenario('savePassword');
        $userid = Yii::$app->request->get('user_id');
        $token = Yii::$app->request->get('token');
        $getUserId = \Yii::$app->utils->decryptData($userid);
        $modelUsers = User::find()->where([
            'user_id' => $getUserId,
            'confirmation_token' => $token
        ])->One();

        if (!$modelUsers) {
            Yii::$app->session->setFlash("error", "Link has been expired, Password has been already set.");
            return $this->redirect(yii::$app->urlManager->createAbsoluteUrl('site/login'));
        }

        $now = date_create(date('Y-m-d')); // or your date as well
        $your_date = $modelUsers->password_requested_date;
        $your_date = date_create($your_date);
        $diff = date_diff($now, $your_date);

        if ($diff->d < 3) {
            if (Yii::$app->request->post()) {

                if ($model && $model->load(Yii::$app->request->post())) {
                    $modelUsers->setPassword($model->password);
                    $modelUsers->password_requested_date = null;
                    $modelUsers->confirmation_token = null;
                    $modelUsers->is_email_verified = 1;
                    if ($modelUsers->save()) {
                        Yii::$app->session->setFlash("success", "Password has been reset successfully.");
                        return $this->redirect(yii::$app->urlManager->createAbsoluteUrl('site/login'));
                    } else {
                        throw new \yii\web\NotFoundHttpException("Unable to save user");
                    }
                } else {
                }
            } else {

                return $this->render('setPassword', [
                    'model' => $model
                ]);
            }
        } else {

            Yii::$app->session->setFlash('error', 'Token Expired');
            return $this->redirect(yii::$app->urlManager->createAbsoluteUrl('site/login'));
        }
    }

    public function actionForgotPassword()
    {


        $this->layout = 'login_layout';
        $model = new User();
        $now = date('Y-m-d H:i:s');
        $post = Yii::$app->request->post();


        if (Yii::$app->request->post()) {
            if ($post['User']['email'] != "") {

                $userEmailIdCheck = User::getValidUserCount($post['User']['email']);
                if ($userEmailIdCheck) {
                    if ($model->load(Yii::$app->request->post())) {
                        $model = $model->findByUsername($model->email);

                        if ($model) {
                            $recipientMail = $model->email;
                            $model->confirmation_token = $model->generateKey();
                            $model->password_requested_date = $now;

                            if ($model && $model->load(Yii::$app->request->post())) {
                                if ($model->save()) {
                                    $getUserId = Yii::$app->utils->encryptData($model->user_id);
                                    $link = '<a href="' . \Yii::$app->urlManager->createAbsoluteUrl('/site/set-password') . '?user_id=' . $getUserId . '&token=' . $model->confirmation_token . '">Click Here</a>';
                                    // $result = true;

                                    $result = EmailsComponent::sendUserVerificationLinkEmail($model->first_name, $recipientMail, $link, $action = "forgot");
                                    if ($result) {

                                        Yii::$app->session->setFlash('success', 'Email sent');
                                    } else {
                                        Yii::$app->session->setFlash('success', 'Email sent');
                                    }
                                } else {
                                    Yii::$app->session->setFlash('success', 'Email sent');
                                }
                            } else {
                                Yii::$app->session->setFlash('success', 'Email sent');
                            }
                        } else {
                            Yii::$app->session->setFlash('success', 'Email sent');
                        }
                    } else {
                        Yii::$app->session->setFlash('success', 'Email sent');
                    }

                } else {
                    Yii::$app->session->setFlash('error', 'Please enter valid Email');
                    return $this->redirect(yii::$app->urlManager->createAbsoluteUrl('site/forgot-password'));
                }
                return $this->redirect([
                    '/'
                ]);
            } else {
                Yii::$app->session->setFlash('error', 'Please enter Email');
                return $this->render('forgotPassword', [
                    'model' => $model
                ]);
            }

        } else {
            return $this->render('forgotPassword', [
                'model' => $model
            ]);
        }
    }

    public function actionHelpdocupload()
    {

        if (Yii::$app->request->isAjax) {
            $target_dir = "help/";
            $target_file = $target_dir . basename($_FILES["file_upload_help"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $supportedTypes = array('pdf');
            if (!in_array($imageFileType, $supportedTypes)) {
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return ['code' => '500', 'content' => 'Please upload pdf file.'];
            } elseif (!$_FILES["file_upload_help"]["size"]) {
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return ['code' => '500', 'content' => 'File size should be less that 5MB.'];
            } else {
                $filename = "guidelines.pdf";
                $loc = $orgF = "help/$filename";
                if (file_exists($loc)) {
                    list($name, $ext) = explode('.', $loc);
                    while (file_exists($loc)) {
                        $loc = $name . '_' . time() . '.' . $ext;
                        $filename = $name . '_' . time() . '.' . $ext;
                    }
                    rename("help/guidelines.pdf", $filename);
                }

                if (move_uploaded_file($_FILES["file_upload_help"]["tmp_name"], "help/guidelines.pdf")) {
                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    return ['code' => '100', 'content' => 'Uploaded successfully. '];
                    //return "The file ". basename( $_FILES["file_upload"]["name"]). " has been uploaded.";
                } else {
                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    return ['code' => '500', 'content' => 'Sorry, there was an error uploading your file. '];
                }

            }
        }
    }

    /**
     * Triggers notifications for Audits.
     */
    public function actionTriggerAuditSubmittedNotifications()
    {
        try {
            set_time_limit(3600);
            \Yii::$app->scheduler->triggerTicketsMail();
            set_time_limit(30);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
