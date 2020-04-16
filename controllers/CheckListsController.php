<?php

namespace app\controllers;

use app\models\Events;
use app\models\Preferences;
use Yii;
use app\components\AccessRule;
use app\models\Checklists;
use app\models\AuditMethods;
use app\models\search\ChecklistsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Questions;
use app\models\Sections;
use app\models\SubSections;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use app\models\AuditsSchedules;
use app\models\Audits;

/**
 * CheckListsController implements the CRUD actions for Checklists model.
 */
class CheckListsController extends Controller
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
                'add-questionnaire',
                'delete',
                'index', 'update-questionnaire', 'view-questionnaire', 'view'
            ], // only be applied to
            'rules' => [
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('check-lists/create'),
                    'actions' => [
                        'create', 'add-questionnaire',
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('check-lists/update'),
                    'actions' => [
                        'update', 'update-questionnaire', 'add-questionnaire',
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('check-lists'),
                    'actions' => [
                        'index', 'view-questionnaire', 'view'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],
                [
                    'allow' => Yii::$app->authManager->checkPermissionAccess('check-lists/delete'),
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
     * Lists all Checklists models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ChecklistsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single Checklists model.
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
     * Creates a new Checklists model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $valid = false;
        $model = new Checklists();
        $modelAuditMethods = AuditMethods::find()->asArray()->all();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $data = array();
                $data['module'] = 'checklist';
                $data['type'] = 'create';
                $data['message'] = "Checklist - <b>" . $model->cl_name . '</b> created by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
                Yii::$app->events->createEvent($data);
                $valid = true;
                $checklist_id = Yii::$app->utils->encryptData($model->checklist_id);
            }
            if ($valid) {
                Yii::$app->session->setflash('success', 'Checklist created successfully');
                return $this->redirect([
                    '/check-lists/add-questionnaire/',
                    'id' => $checklist_id

                ]);
            }
        }
        return $this->render('create', [
            'model' => $model,
            'modelAuditMethods' => $modelAuditMethods
        ]);
    }

    /**
     * Updates an existing Checklists model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel(\Yii::$app->utils->decryptData($id));
        if ($model->cl_status) {
            Yii::$app->session->setFlash('info', 'Checklist should be Inactive to make any changes');
            return $this->redirect([
                'index'
            ]);
        }
        $audit_span = $model->cl_audit_span;

        if ($model->load(Yii::$app->request->post())) {
            $model->cl_audit_span = $audit_span;
            if ($model->save()) {
                $data = array();
                $data['module'] = 'checklist';
                $data['type'] = 'update';
                $data['message'] = "Checklist - <b>" . $model->cl_name . "</b> updated by " . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
                Yii::$app->events->createEvent($data);
                Yii::$app->session->setflash('success', 'Checklist updated successfully');
                return $this->redirect([
                    '/check-lists/add-questionnaire/',
                    'id' => $id
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
    }

    /**
     * Deletes an existing Checklists model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteQuestionnaire()
    {
        $post = Yii::$app->request->post();
        $output = [];
        $id = Yii::$app->utils->encryptData($post['checklist_id']);
        $sectionid = $post['section_id'];
        $checklistid = Yii::$app->utils->decryptData($id);
        $auditspan_id = $post['auditspan_id'];
        $decryptedQuestionnaire = Yii::$app->utils->decryptData($post['deletable_questionnaire_id']);
        $decryptedQuestionnaireText = Yii::$app->utils->decryptData($post['questiontext']);

        $modelQuestionnaireCount = Questions::find()->where([
            'question_id' => $decryptedQuestionnaire
        ])->count();
        if ($modelQuestionnaireCount != 0) {
            if ($auditspan_id == "1") {
                $modelQuestionsUpdate = Questions::updateAll([
                    'is_deleted' => 1
                ], 'question_id=' . $decryptedQuestionnaire);
            } else if ($auditspan_id == "2") {
                $modelQuestionsUpdate = Questions::updateAll([
                    'is_deleted' => 1
                ], [
                    'q_checklist_id' => $checklistid,
                    'q_section' => $sectionid,
                    'q_text' => $decryptedQuestionnaireText
                ]);
            }

            if ($modelQuestionsUpdate) {
                Yii::$app->session->setFlash('success', 'Question deleted successfully');
            } else {
                Yii::$app->session->setFlash('error', 'Question cannot be deleted');
            }
        } else {

            Yii::$app->session->setFlash('error', 'Invalid request');
        }
        return $this->redirect([
            '/check-lists/add-questionnaire/',
            'id' => $id
        ]);
    }

    /**
     * Finds the Checklists model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Checklists the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Checklists::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAddQuestionnaire($id)
    {
        $checklist_id = Yii::$app->utils->decryptData($id);
        $checklistModel = $this->findModel($checklist_id);

        if ($checklistModel->cl_status) {
            Yii::$app->session->setFlash('info', 'Check list should be in-active to add/edit questions ');

            return $this->redirect([
                'update',
                'id' => $id

            ]);
        }

        $modelQuestionnaire = $this->getCheckListBasedQuestions($checklist_id, $checklistModel);
        $model = new Questions();
        $sectionsModel = new Sections();
        $subSectionsModel = new SubSections();

        if ($model && $model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($checklistModel->cl_audit_span == 2) {
                    $this->saveAcrossSectionQuestions($model, Yii::$app->request->post(), $checklist_id);
                } else {

                    $this->saveSpecificSectionQuestions($model, Yii::$app->request->post(), $checklist_id);
                }

                $transaction->commit();

                Yii::$app->session->setflash('success', "Question added successfully");
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
            return $this->redirect([
                '/check-lists/add-questionnaire?id=' . $id
            ]);
        } else {
            return $this->render('addQuestionnaire', [
                'model' => $model,
                'sectionsModel' => $sectionsModel,
                'subSectionsModel' => $subSectionsModel,
                'department_id' => $checklistModel->cl_department_id,
                'cl_audit_span' => $checklistModel->cl_audit_span,
                'checkListName' => $checklistModel->cl_name,
                'checklist_id' => $checklist_id,
                'modelQuestionnaire' => $modelQuestionnaire

            ]);
        }
    }

    /*
     * View checklist related Questionnaries
     * /
     */
    public function actionViewQuestionnaire($id)
    {
        $checklist_id = Yii::$app->utils->decryptData($id);
        $checklistModel = $this->findModel($checklist_id);

        /**
         * ************************Get Questions*****************************
         */
        $modelQuestionnaire = $this->getCheckListBasedQuestions($checklist_id, $checklistModel);

        return $this->render('viewQuestionnaire', [
            'department_name' => $checklistModel->clDepartment->department_name,
            'checkListName' => $checklistModel->cl_name,
            'modelQuestionnaire' => $modelQuestionnaire

        ]);
    }

    protected function getCheckListBasedQuestions($checklist_id, $checklistModel)
    {
        $subSectionList = ArrayHelper::map(SubSections::getList(), 'sub_section_id', 'ss_subsection_name');
        $sectionsList = ArrayHelper::map(Sections::getList(), 'section_id', 's_section_name');

        $questionsList = Questions::find()->where([
            'q_checklist_id' => $checklist_id,
            'is_deleted' => 0
        ])
            ->asArray()
            ->all();

        $sectionSpecificQuestions = ArrayHelper::index($questionsList, null, 'q_section');

        $modelQuestionnaire = [];
        if ($checklistModel->cl_audit_span == 2) {
            foreach ($sectionSpecificQuestions as $sectionId => $question) {

                $subsectionIds = array_unique(ArrayHelper::getColumn($question, 'q_sub_section'));
                $subsectionNames = array_filter($subSectionList, function ($key) use ($subsectionIds) {
                    if (in_array($key, $subsectionIds)) {
                        return true;
                    }
                }, ARRAY_FILTER_USE_KEY);

                $modelQuestionnaire[$sectionId]['sectionName'] = $sectionsList[$sectionId];
                $modelQuestionnaire[$sectionId]['subSectionName'] = implode(',', $subsectionNames);
                $questions = $checklistModel->cl_audit_span == 2 ? ArrayHelper::index($question, 'q_text') : ArrayHelper::index($question, 'question_id');

                $modelQuestionnaire[$sectionId]['questions'] = $questions;
            }
        } else {

            $sectionSpecificQuestions = ArrayHelper::index($questionsList, null, [
                'q_sub_section',
                'q_section'
            ]);

            foreach ($sectionSpecificQuestions as $subsectionId => $question) {
                if ($subsectionId) {

                    $question = call_user_func_array('array_merge', $question);
                    $subsectionIds = array_unique(ArrayHelper::getColumn($question, 'q_section'));

                    $sectionNames = array_filter($sectionsList, function ($key) use ($subsectionIds) {
                        if (in_array($key, $subsectionIds)) {
                            return true;
                        }
                    }, ARRAY_FILTER_USE_KEY);

                    $questions = ArrayHelper::index($question, null, 'q_sub_section');

                    $modelQuestionnaire[$subsectionId]['subSectionName'] = isset($subSectionList[$subsectionId]) ? $subSectionList[$subsectionId] : '';

                    $modelQuestionnaire[$subsectionId]['sectionName'] = implode(',', $sectionNames);

                    $modelQuestionnaire[$subsectionId]['questions'] = $question;
                } else {

                    foreach ($question as $newSectionId => $newQuestion) {
                        // $questions = ArrayHelper::index($question, null, 'q_sub_section');

                        $modelQuestionnaire[$newSectionId]['subSectionName'] = '';

                        $modelQuestionnaire[$newSectionId]['sectionName'] = $sectionsList[$newSectionId];

                        $modelQuestionnaire[$newSectionId]['questions'] = $newQuestion;
                    }
                }
            }
        }

        return $modelQuestionnaire;
    }

    public function actionSubSection($checklistId = '', $cl_audit_span = '')
    {
        $out = [];
        $postData = Yii::$app->request->post();

        if (isset($postData['depdrop_parents'])) {
            $parents = $postData['depdrop_parents'];
            $selectedData = @json_decode($postData['depdrop_all_params']['selectedSubsections']);

            $parents = $postData['depdrop_parents'];
            if ($parents != null) {
                $section_id = $parents[0];
                // header('Content-type: application/json');
                if (!empty($section_id)) {
                    $questionSubsections = Questions::findOne([
                        'q_checklist_id' => $checklistId,
                        'q_section' => $section_id,
                        'is_deleted' => 0
                    ]);

                    $out = SubSections::find()->where([
                        'ss_section_id' => $section_id,
                        'is_deleted' => 0
                    ])
                        ->select([
                            'id' => 'sub_section_id',
                            'name' => 'ss_subsection_name'
                        ])
                        ->asArray()
                        ->all();
                    $subSectionIds = [];
                    if ($cl_audit_span == 2) {
                        $questionSubsections = Questions::find()->where([
                            'q_checklist_id' => $checklistId,
                            'q_section' => $section_id,
                            'is_deleted' => 0
                        ])
                            ->select([
                                'q_sub_section'
                            ])
                            ->asArray()
                            ->all();
                        $subSectionIds = yii\helpers\ArrayHelper::getColumn($questionSubsections, 'q_sub_section');
                        $subSectionIds = $subSectionIds ? $subSectionIds : [];
                    }
                    echo Json::encode([
                        'output' => $out,
                        'selected' => array_filter($subSectionIds)
                    ]);
                }
            }
        } else {
            echo Json::encode([
                'output' => '',
                'selected' => ''
            ]);
        }
    }

    /**
     *
     * @param unknown $model
     * @param unknown $post
     * @param unknown $checklist_id
     * @throws \Exception
     */
    protected function saveSpecificSectionQuestions($model, $post, $checklist_id)
    {
        $checkedValue = $post['Questions']['checkedvalue'];

        try {

            if ($checkedValue == "0") {
                $dynamincQuestions = Questions::find()->where([
                    'q_checklist_id' => $checklist_id,
                    'q_section' => $model->q_section,
                    'q_sub_section_is_dynamic' => 1,
                    'is_deleted' => 0
                ])
                    ->asArray()
                    ->all();

                $records = [];

                foreach ($dynamincQuestions as $question) {
                    $record = [];
                    $record = $question;
                    $record['q_sub_section'] = $model->q_sub_section;
                    $record['q_sub_section_is_dynamic'] = 1;
                    unset($record['question_id']);
                    $records[] = $record;
                }

                $rows = [
                    'q_text',
                    'q_checklist_id',
                    'q_section',
                    'q_sub_section',
                    'q_sub_section_is_dynamic',
                    'q_access_type',
                    'q_priority_type',
                    'q_response_type',
                    'options',
                    'is_deleted'
                ];
                if ($records) {
                    Yii::$app->db->createCommand()
                        ->batchInsert('{{%questions}}', $rows, $records)
                        ->execute();
                }

                Questions::updateAll([
                    'is_deleted' => 1
                ], [
                    'q_checklist_id' => $checklist_id,
                    'q_section' => $model->q_section,
                    'q_sub_section_is_dynamic' => 1
                ]);
            }

            $options = array_filter($post['options']);
            $model->options = serialize($options);
            $model->q_access_type = json_encode($model->q_access_type);
            $model->q_sub_section = $model->q_sub_section_is_dynamic ? null : $model->q_sub_section;
            $model->q_sub_section_is_dynamic = $checkedValue;

            $model->save();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     *
     * @param unknown $model
     * @param unknown $postInformation
     */
    protected function saveAcrossSectionQuestions($model, $postInformation, $checklist_id)
    {
        $checkedValue = $postInformation['Questions']['checkedvalue'];

        $inputSubsectionData = $model->q_sub_section;

        $questionSubsectionData = Questions::find()->where([
            'q_checklist_id' => $checklist_id,
            'q_section' => $model->q_section,
            'is_deleted' => 0
        ])
            ->asArray()
            ->all();

        if ($checkedValue == "0") {

            $inputSubsectionData = $inputSubsectionData ? $inputSubsectionData : [];
            $deletedSubSections = [];

            if ($questionSubsectionData) {

                $existingSubsections = array_filter(array_unique(ArrayHelper::getColumn($questionSubsectionData, 'q_sub_section')));

                $existingSubsections = $existingSubsections ? $existingSubsections : [];

                $newlyAddedSubSections = array_diff($inputSubsectionData, $existingSubsections);

                $deletedSubSections = array_diff($existingSubsections, $inputSubsectionData);

                $newlyAddedSubSections = $newlyAddedSubSections ? $newlyAddedSubSections : [];

                $subsection = '';

                if ($existingSubsections) {

                    $this->createNewQuestions($existingSubsections, $postInformation);
                }
                if (!empty($newlyAddedSubSections)) {
                    $this->addSubSectionsToCheckList($model, $checklist_id, $subsection, $newlyAddedSubSections, $questionSubsectionData, $postInformation);
                }
                if ($deletedSubSections) {
                    $this->deleteRemovedSubSection($checklist_id, $model, $deletedSubSections);
                }

            } else {
                $this->createNewQuestions($inputSubsectionData, $postInformation);
            }
        } else {

            $questions = ArrayHelper::index($questionSubsectionData, 'q_text');

            Questions::updateAll([
                'is_deleted' => 1
            ], [
                'q_checklist_id' => $checklist_id,
                'q_section' => $model->q_section
            ]);

            $records = [];

            foreach ($questions as $question) {
                $record = [];
                $record = $question;
                $record['q_sub_section'] = null;
                $record['q_sub_section_is_dynamic'] = 1;
                unset($record['question_id']);
                $records[] = $record;
            }

            $rows = [
                'q_text',
                'q_checklist_id',
                'q_section',
                'q_sub_section',
                'q_sub_section_is_dynamic',
                'q_access_type',
                'q_priority_type',
                'q_response_type',
                'options',
                'is_deleted'
            ];
            if ($records) {
                Yii::$app->db->createCommand()
                    ->batchInsert('{{%questions}}', $rows, $records)
                    ->execute();
            }

            $model = new Questions();

            if ($model && $model->load($postInformation)) {
                $options = array_filter($postInformation['options']);
                $model->options = serialize($options);
                $model->isNewRecord = true;
                $model->question_id = '';
                $model->q_access_type = json_encode($model->q_access_type);
                $model->q_sub_section = '';
                $model->q_sub_section_is_dynamic = $checkedValue;

                $model->save();
            }
        }
    }

    /**
     *
     * @param unknown $existingSubsections
     * @param unknown $postInformation
     */
    protected function createNewQuestions($existingSubsections, $postInformation)
    {
        $model = new Questions();

        // adding new question to existing sub sections

        foreach ($existingSubsections as $subSectionsData) {

            if ($model->load($postInformation)) {
                $options = array_filter($postInformation['options']);
                $model->isNewRecord = true;
                $model->question_id = '';
                $model->options = serialize($options);
                $model->q_access_type = Json::encode($model->q_access_type);
                $model->q_sub_section = $subSectionsData;
                $subsection = $subSectionsData;
                $model->save();
            }
        }
    }

    protected function deleteRemovedSubSection($checklist_id, $model, $deletedSubSections)
    {
        if ($deletedSubSections) {
            Questions::updateAll([
                'is_deleted' => 1
            ], [
                'q_checklist_id' => $checklist_id,
                'q_section' => $model->q_section,
                'q_sub_section' => $deletedSubSections
            ]);
        }
    }

    /**
     *
     * @param unknown $model
     * @param unknown $subsection
     * @param unknown $newlyAddedSubSections
     */
    protected function addSubSectionsToCheckList($model, $checklist_id, $subsection, $newlyAddedSubSections, $questionSubsectionData, $postInformation)
    {
        $arrCheckListQuestions = [];

        if ($subsection) {
            $arrCheckListQuestions = Questions::find()->where([
                'q_checklist_id' => $checklist_id,
                'q_section' => $model->q_section,
                'q_sub_section' => $subsection,
                'q_sub_section_is_dynamic' => 0,
                'is_deleted' => 0
            ])
                ->asArray()
                ->all();
        } else {

            // creating post question to sub sections
            $this->createNewQuestions($newlyAddedSubSections, $postInformation);
        }

        if ($arrCheckListQuestions) {
            //foreach ($newlyAddedSubSections as $newSubSectionId) {
            $this->saveNewQuestions($newlyAddedSubSections, $arrCheckListQuestions);
            //}
        }

        $dynamicList = [];
        $questionSubsectionData = ArrayHelper::index($questionSubsectionData, 'q_text');

        foreach ($questionSubsectionData as $questionSubsection) {
            if ($questionSubsection['q_sub_section_is_dynamic']) {
                $dynamicList[] = $questionSubsection;
            }
        }

        if ($dynamicList) {

            Questions::updateAll([
                'is_deleted' => 1
            ], [
                'q_checklist_id' => $checklist_id,
                'q_section' => $model->q_section,
                'q_sub_section_is_dynamic' => 1
            ]);

            $this->saveNewQuestions($newlyAddedSubSections, $dynamicList, true);
        }
    }

    /**
     *
     * @param unknown $newlyAddedSubSections
     * @param unknown $questionsList
     */
    protected function saveNewQuestions($newlyAddedSubSections, $questionsList, $dynamic = false)
    {
        foreach ($newlyAddedSubSections as $newSubSectionId) {

            $records = [];

            foreach ($questionsList as $question) {
                $record = [];
                $record = $question;
                $record['q_sub_section'] = $newSubSectionId;
                $record['q_sub_section_is_dynamic'] = '0';
                unset($record['question_id']);
                $records[] = $record;
            }
            $rows = [
                'q_text',
                'q_checklist_id',
                'q_section',
                'q_sub_section',
                'q_sub_section_is_dynamic',
                'q_access_type',
                'q_priority_type',
                'q_response_type',
                'options',
                'is_deleted'
            ];
            Yii::$app->db->createCommand()
                ->batchInsert('{{%questions}}', $rows, $records)
                ->execute();
        }
    }

    /**
     */
    public function actionGetSubSection()
    {
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();

            $dynamicSubSection = Questions::find()->where([
                'q_section' => $post['sectionId'],
                'q_checklist_id' => $post['checkListId'],
                'is_deleted' => 0
            ])
                ->asArray()
                ->all();

            $dynamicQuestionCount = array_filter(ArrayHelper::getColumn($dynamicSubSection, function ($element) {
                if ($element['q_sub_section_is_dynamic']) {
                    return $element;
                }
            }));

            $subQuestionCount = array_filter(ArrayHelper::getColumn($dynamicSubSection, function ($element) {
                if (!$element['q_sub_section_is_dynamic']) {
                    return $element;
                }
            }));

            $result['dynamicStatus'] = $dynamicQuestionCount ? true : false;
            $result['subSectionStatus'] = $subQuestionCount ? true : false;

            echo Json::encode($result);
        }
    }


    public function actionGetSubSectionCount()
    {

        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $dynamicSubSection = Questions::find()->where([
                'q_section' => $post['sectionId'],
                'q_checklist_id' => $post['checkListId'],
                'is_deleted' => 0
            ])
                ->count();
            return $dynamicSubSection;

        }
    }

    public function actionDelete()
    {
        $post = yii::$app->request->post();
        $id = yii::$app->utils->decryptData($post['deletable_checklist_id']);
        $model = $this->findModel($id);
        if ($model) {

            $AuditCount = Audits::find()->where([
                'checklist_id' => $id,
                'is_deleted' => 0
            ])
                ->andWhere([
                    'NOT IN',
                    'status',
                    [
                        3,
                        4
                    ]
                ])
                ->count();

            if ($AuditCount <= 0) {
                Questions::updateAll([
                    'is_deleted' => 1
                ], [
                    'q_checklist_id' => $id
                ]);
                $model->is_deleted = 1;
                if ($model->save()) {
                    $data = array();
                    $data['module'] = 'checklist';
                    $data['type'] = 'delete';
                    $data['message'] = 'Checklist - <b>' . $model->cl_name . '</b> deleted by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
                    Yii::$app->events->createEvent($data);
                    Yii::$app->session->setFlash('success', 'Checklist deleted successfully');
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to delete Checklist');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Checklist cannot be deleted as it assigned to Audits.');
            }
        }
        return $this->redirect([
            'index'
        ]);
    }

    public function actionUpdateStatus()
    {
        if (!empty(Yii::$app->request->post('status'))) {
            $model = $this->findModel(Yii::$app->request->post('status'));
            $data = array();
            $data['module'] = 'checklist';
            $data['type'] = 'update';
            $data['message'] = "Checklist - <b>" . $model->cl_name . '</b> status has been changed  by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . '.';
            Yii::$app->events->createEvent($data);
            if ($model->cl_status == 1) {
                $model->cl_status = 0;
                $model->save();
            } else if ($model->cl_status == 0) {
                $model->cl_status = 1;
                $model->save();
            }
            Yii::$app->session->setFlash('success', 'Status updated successfully');
            return true;
            //return $this->redirect(['index']);
        }
    }

    public function actionUpdateQuestionnaire($id, $question_id)
    {
        $model = new Questions();
        $sectionsModel = new Sections();
        $subSectionsModel = new SubSections();
        $selectedOptions = array();

        $checklist_id = Yii::$app->utils->decryptData($id);
        $question_id = Yii::$app->utils->decryptData($question_id);

        $checklistModel = $this->findModel($checklist_id);

        if ($checklistModel->cl_status) {
            Yii::$app->session->setFlash('info', 'Check list should be in-active to add/edit questions ');

            return $this->redirect([
                'update',
                'id' => $id

            ]);
        }

        // get individual question details
        $model = Questions::find()->where([
            'q_checklist_id' => $checklist_id,
            'question_id' => $question_id,
            'is_deleted' => 0
        ])->One();

        $is_dynamic = $model->q_sub_section_is_dynamic;
        // $q_sub_section = $model->q_sub_section;
        $q_section = $model->q_section;

        $modelQuestionnaire = [];

        if (!empty($model->options)) {
            $selectedOptions = @unserialize($model->options);
        }

        if (!empty($model->q_access_type)) {
            $model->q_access_type = json_decode($model->q_access_type);
            $model->q_access_type = $model->q_access_type ? $model->q_access_type : [];
            $q_access_type = array();

            foreach ($model->q_access_type as $accessType) {
                $q_access_type[] = (int)$accessType;
            }

            $model->q_access_type = array_values($q_access_type);
        }
        $questionText = $model->q_text;
        if ($model && $model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {

                $model->q_sub_section_is_dynamic = $is_dynamic;
                // $model->q_sub_section = $q_sub_section;
                $model->q_section = $q_section;

                if ($checklistModel->cl_audit_span == 2) {

                    $this->updateAcrossSectionQuestions($model, Yii::$app->request->post(), $checklist_id, $questionText);
                } else {

                    $this->updateSpecificSectionQuestions($model, Yii::$app->request->post(), $checklist_id);
                }

                $transaction->commit();

                Yii::$app->session->setflash('success', "Question updated successfully");
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
            return $this->redirect([
                '/check-lists/add-questionnaire?id=' . $id
            ]);
        } else {

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('updateQuestionaire', [
                    'model' => $model,
                    'sectionsModel' => $sectionsModel,
                    'subSectionsModel' => $subSectionsModel,
                    'department_id' => $checklistModel->cl_department_id,
                    'cl_audit_span' => $checklistModel->cl_audit_span,
                    'checkListName' => $checklistModel->cl_name,
                    'checklist_id' => $checklist_id,
                    'modelQuestionnaire' => $modelQuestionnaire,
                    'selectedOptions' => $selectedOptions
                ]);
            } else {
                return $this->render('updateQuestionaire', [
                    'model' => $model,
                    'sectionsModel' => $sectionsModel,
                    'subSectionsModel' => $subSectionsModel,
                    'department_id' => $checklistModel->cl_department_id,
                    'cl_audit_span' => $checklistModel->cl_audit_span,
                    'checkListName' => $checklistModel->cl_name,
                    'checklist_id' => $checklist_id,
                    'modelQuestionnaire' => $modelQuestionnaire,
                    'selectedOptions' => $selectedOptions
                ]);
            }
        }
    }

    /**
     *
     * @param unknown $model
     * @param unknown $postInformation
     */
    protected function updateAcrossSectionQuestions($model, $postInformation, $checklist_id, $questionText)
    {
        $inputSubsectionData = $model->q_sub_section;

        $questionSubsectionData = Questions::find()->where([
            'q_checklist_id' => $checklist_id,
            'q_section' => $model->q_section,
            'is_deleted' => 0
        ])
            ->asArray()
            ->all();

        if (!$model->q_sub_section_is_dynamic) {

            $inputSubsectionData = $inputSubsectionData ? $inputSubsectionData : [];
            $deletedSubSections = [];
            if ($questionSubsectionData) {

                $newPostedSubsection = $model->q_sub_section;
                $existingSubsections = array_filter(array_unique(ArrayHelper::getColumn($questionSubsectionData, 'q_sub_section')));

                $subsection = '';
                if ($existingSubsections) {
                    $this->updateQuestions($model, $existingSubsections, $postInformation, $questionText);
                }
                // For Update adding new subsections
                $newSubsections = array_diff($newPostedSubsection, $existingSubsections);
                // For already added subsections listout removed subsections
                $RemoveUpdatedSubsections = array_diff($existingSubsections, $newPostedSubsection);
                $access_type = json_encode($model->q_access_type);
                // For update new subsections
                if ($newSubsections && $model->load($postInformation)) {

                    $this->updateQuestionsToNewSubsection($newSubsections, $model);

                    /*foreach ($newSubsections as $newSubsectionData) {

                        $options = array_filter($postInformation['options']);
                        $model->q_access_type = $access_type;
                        $model->q_sub_section = $newSubsectionData;
                        $model->question_id = null;
                        $model->isNewRecord = true;
                        if ($model->q_response_type == 1 || $model->q_response_type == 2 || $model->q_response_type == 3) {
                            $model->options = 'a:0:{}';
                        } else {
                            $model->options = serialize($options);
                        }

                        if ($model) {
                            $model->save();
                        }


                    }*/


                } else {
                    foreach ($RemoveUpdatedSubsections as $RemoveUpdatedSubsectionsData) {
                        Questions::updateAll([
                            'is_deleted' => 1
                        ], [
                            'q_sub_section' => $RemoveUpdatedSubsectionsData,
                            'q_checklist_id' => $checklist_id
                        ]);
                    }
                }

                if ($RemoveUpdatedSubsections) {
                    $this->deleteRemovedSubSection($checklist_id, $model, $RemoveUpdatedSubsections);
                }
            } else {

                $this->updateQuestions($model, $inputSubsectionData, $postInformation, $questionText);
            }
        } else {
            $options = array_filter($postInformation['options']);
            $model->q_access_type = Json::encode($model->q_access_type);
            if ($model->q_response_type == 1 || $model->q_response_type == 2 || $model->q_response_type == 3) {

                $model->options = 'a:0:{}';
            } else {
                $model->options = serialize($options);
            }
            $model->save();
        }
    }

    /**
     *
     * @param unknown $existingSubsections
     * @param unknown $postInformation
     */
    protected function updateQuestions($modelQuestion, $existingSubsections, $postInformation, $questionText)
    {

        // updating question to existing sub sections
        foreach ($existingSubsections as $subSectionsData) {

            $model = Questions::find()->where([
                'q_checklist_id' => $modelQuestion->q_checklist_id,
                'q_section' => $modelQuestion->q_section,
                'q_sub_section' => $subSectionsData,
                'q_text' => $questionText,
                'is_deleted' => 0
            ])->One();

            if ($model && $model->load($postInformation)) {

                $options = array_filter($postInformation['options']);
                $model->q_access_type = Json::encode($model->q_access_type);
                $model->q_sub_section = $subSectionsData;
                $subsection = $subSectionsData;
                if ($model->q_response_type == 1 || $model->q_response_type == 2 || $model->q_response_type == 3) {

                    $model->options = 'a:0:{}';
                } else {
                    $model->options = serialize($options);
                }

                $model->save();
            }
        }
    }

    /**
     *
     * @param unknown $model
     * @param unknown $post
     * @param unknown $checklist_id
     * @throws \Exception
     */
    protected function updateSpecificSectionQuestions($model, $post, $checklist_id)
    {
        try {

            $options = array_filter($post['options']);
            if ($model->q_response_type == 1 || $model->q_response_type == 2 || $model->q_response_type == 3) {

                $model->options = 'a:0:{}';
            } else {
                $model->options = serialize($options);
            }

            $model->q_access_type = json_encode($model->q_access_type);
            $model->q_sub_section = $model->q_sub_section_is_dynamic ? null : $model->q_sub_section;
            $model->save();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $newSubsections
     * @param $model
     * @throws \yii\db\Exception
     */
    protected function updateQuestionsToNewSubsection($newSubsections, $model)
    {
        $questionSubsectionData = Questions::find()->where([
            'q_checklist_id' => $model->q_checklist_id,
            'q_section' => $model->q_section,
            'is_deleted' => 0
        ])
            ->asArray()
            ->all();
        $questionSubsectionData = ArrayHelper::index($questionSubsectionData, 'q_text');

        foreach ($newSubsections as $newSubSectionId) {

            $records = [];

            foreach ($questionSubsectionData as $question) {
                $record = [];
                $record = $question;
                $record['q_sub_section'] = $newSubSectionId;
                unset($record['question_id']);
                $records[] = $record;
            }
            $rows = [
                'q_text',
                'q_checklist_id',
                'q_section',
                'q_sub_section',
                'q_sub_section_is_dynamic',
                'q_access_type',
                'q_priority_type',
                'q_response_type',
                'options',
                'is_deleted'
            ];
            Yii::$app->db->createCommand()
                ->batchInsert('{{%questions}}', $rows, $records)
                ->execute();
        }
    }
}
