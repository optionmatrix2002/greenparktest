<?php

namespace app\modules\api\controllers;

use yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use app\models;
use app\models\User;
use app\models\LoginForm;
use app\models\Locations;
use app\models\UserHotels;
use app\models\Audits;
use app\models\Questions;
use yii\web\HttpException;
use app\models\AuditsSchedules;
use yii\helpers\ArrayHelper;
use app\models\Sections;
use app\models\SubSections;
use app\models\AuditsChecklistQuestions;
use app\models\HotelDepartmentSubSections;
use yii\helpers\Json;

class QuestionsController extends ActiveController
{

    public $modelClass = 'app\models\Questions';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticate'] = [
            'class' => HttpBearerAuth::className()
        ];
        return $behaviors;
    }

    public function actionAuditQuestions()
    {

        $transaction = Yii::$app->db->beginTransaction();
        try {
            ini_set('memory_limit','-1');
            ini_set('max_execution_time','180');
            $output = [];
            $user_id = Yii::$app->request->post('user_id');
            // $user_id = \Yii::$app->user->id;
            $audit_schedule_id = Yii::$app->request->post('audit_id');
            if ($user_id && $audit_schedule_id) {

                $auditScheduled = AuditsSchedules::find()->where([
                    'auditor_id' => $user_id
                ])
                    ->orWhere([
                        'or',
                        [
                            'deligation_user_id' => $user_id
                        ]
                    ])->andWhere(['audit_schedule_id' => $audit_schedule_id])->one();
                if (!$auditScheduled) {
                    $output = [
                        '400' => 'fail',
                        'response' => 'fail',
                        'message' => 'Audit is not assigned to user.'
                    ];
                    return $output;
                }
                if ($auditScheduled) {
                    /*  $startDate = $auditScheduled->start_date;
                     $endDate = $auditScheduled->end_date;
                     $currentDate = date('Y-m-d');

                     if (!(strtotime($startDate) <= strtotime($currentDate) && strtotime($endDate) >= strtotime($currentDate))) {
                         $output = [
                             '200' => 'success',
                             'response' => 'fail',
                             'message' => 'Audit can only be started during scheduled period.'
                         ];
                         return $output;
                     } else { */
                    $auditCheckList = $auditScheduled->audit->checklist;

                    if ($auditCheckList->cl_status != 1) {
                        ini_set('memory_limit','128M');
                        ini_set('max_execution_time','30');
                        $output = [
                            '200' => 'success',
                            'response' => 'fail',
                            'message' => 'Audit checklist is currently inactive. Please contact administrator.'
                        ];
                        return $output;
                    }
                    /* } */
                }

                $audits = (new yii\db\Query())->select('sa.audit_schedule_id as audit_id,q.*')
                    ->from('{{%audits_schedules}} sa')
                    ->join('LEFT JOIN', "{{%audits}} a", 'a.audit_id = sa.audit_id')
                    ->join("LEFT JOIN", '{{%checklists}} ck', 'ck.checklist_id = a.checklist_id')
                    ->join("LEFT JOIN", '{{%questions}} q', 'q.q_checklist_id = ck.checklist_id')
                    ->where([
                        'sa.audit_schedule_id' => $audit_schedule_id,
                        'sa.is_deleted' => 0,
                        'q.is_deleted' => 0,
                        'ck.is_deleted' => 0
                    ])
                    ->all();

                $mainAudit = $auditScheduled->audit;

                $hotelSections = models\HotelDepartmentSections::find()->where([
                    'hotel_id' => $mainAudit->hotel_id,
                    'department_id' => $mainAudit->department_id,
                    'is_deleted' => 0
                ])
                    ->asArray()
                    ->all();
                $hotelSubSections = HotelDepartmentSubSections::find()->where([
                    'hotel_id' => $mainAudit->hotel_id,
                    'department_id' => $mainAudit->department_id,
                    'is_deleted' => 0
                ])
                    ->asArray()
                    ->all();

                $hotelSections = ArrayHelper::getColumn($hotelSections, 'section_id');
                $hotelSubSections = ArrayHelper::getColumn($hotelSubSections, 'sub_section_id');

                $model = new AuditsChecklistQuestions();
                foreach ($audits as $audit) {
                    if (in_array($audit['q_section'], $hotelSections) && ($audit['q_sub_section'] && in_array($audit['q_sub_section'], $hotelSubSections) || !$audit['q_sub_section'])) {
                        $model->audit_id = $audit['audit_id'];
                        $model->checklist_id = $audit['q_checklist_id'];
                        $model->question_id = $audit['question_id'];
                        $model->q_text = $audit['q_text'];
                        $model->q_section = $audit['q_section'];
                        $model->q_sub_section = $audit['q_sub_section'] ? $audit['q_sub_section'] : '';
                        $model->q_sub_section_is_dynamic = $audit['q_sub_section_is_dynamic'];
                        $model->q_access_type = $audit['q_access_type'];
                        $model->q_priority_type = $audit['q_priority_type'];
                        $model->q_response_type = $audit['q_response_type'];
                        $model->process_critical = $audit['process_critical'];
                        $model->options = $audit['options'];
                        $model->is_deleted = $audit['is_deleted'];
                        $model->isNewRecord = true;
                        $model->audits_checklist_questions_id = '';

                        if (!$model->save()) {
                            $transaction->rollBack();
                            // throw new HttpException(422, Json::encode($model->getErrors()));
                            throw new HttpException(422, 'Error in saving the question');
                        }
                        /* $question_id = $model->audits_checklist_questions_id;
                        $answerModel->audit_id = $audit['audit_id'];
                        $answerModel->question_id = $question_id;
                        $answerModel->isNewRecord = true;
                        $answerModel->answer_id = '';
                        
                        if (!$answerModel->save()) {
                                $transaction->rollBack();
                               // throw new HttpException(422, Json::encode($answerModel->attributes));
                                 throw new HttpException(422, 'Error in saving the answers');
                            } */

                    }
                }

                $audits = AuditsSchedules::find()->joinWith([
                    'auditor as user' => function ($query) {
                        $query->select([
                            'concat(user.`first_name`," ",user.`last_name`) as auditor'
                        ]);
                    },
                    'createdBy as assignedUser' => function ($query) {
                        $query->select([
                            'concat(assignedUser.`first_name`," ",assignedUser.`last_name`) as assigned_by'
                        ]);
                    },
                    'audit as au' => function ($query) {
                        $query->select([
                            'au.location_id',
                            'au.hotel_id',
                            'au.department_id',
                            'au.checklist_id',
                            'au.audit_id'
                        ]);
                    },
                    'audit.location as l' => function ($query) {
                        $query->select([
                            'l.location_city_id'
                        ]);
                    },
                    'audit.location.locationCity as lc' => function ($query) {
                        $query->select([
                            'lc.name'
                        ]);
                    },
                    'audit.hotel h' => function ($query) {
                        $query->select([
                            'h.hotel_name'
                        ]);
                    },
                    'audit.department d' => function ($query) {
                        $query->select([
                            'd.department_name'
                        ]);
                    },
                    'audit.checklist ck' => function ($query) {
                        $query->select([
                            'ck.cl_name',
                            'ck.cl_audit_type'
                        ]);
                    },
                    'auditsChecklistQuestions acq' => function ($query) {
                        $query->select([
                            'acq.*'
                        ]);
                    }
                ])
                    ->where([
                        'audit_schedule_id' => $audit_schedule_id,
                        AuditsSchedules::tableName() . '.is_deleted' => 0,
                        AuditsSchedules::tableName() . '.status' => 0
                    ])
                    ->asArray()
                    ->one();

                $questions_set = [];
                $all_sections = [];
                $all_sub_sections = [];

                if ($audits) {
                    $questions_set = $audits['auditsChecklistQuestions'];
                    $questions_sections = ArrayHelper::index($questions_set, null, 'q_section');
                    $department_id = $audits['audit']['department_id'];
                    $all_sections = ArrayHelper::index(Sections::find()->where([
                        's_department_id' => $department_id,
                        'is_deleted' => 0
                    ])
                        ->asArray()
                        ->all(), 'section_id');

                    $sections = array();

                    foreach ($questions_sections as $key => $each_section) {
                        $all_sub_sections = ArrayHelper::index(SubSections::find()->where([
                            'ss_section_id' => $key,
                            'is_deleted' => 0
                        ])
                            ->asArray()
                            ->all(), 'sub_section_id');

                        $each_section_arr = array();
                        $each_section_arr['section_id'] = $all_sections[$key]['section_id'];
                        $each_section_arr['section_name'] = $all_sections[$key]['s_section_name'];
                        $each_section_arr['sub_sections'] = $this->actionPrepareSubSection($each_section, $all_sub_sections);
                        if ($each_section_arr) {
                            $sections[] = $each_section_arr;
                        }
                    }

                    $result = array();
                    $result['audit_id'] = $audits['audit_schedule_id'];
                    $result['audit_name'] = $audits['audit']['checklist']['cl_name'];
                    $result['department_name'] = $audits['audit']['department']['department_name'];
                    $result['audit_type'] = $audits['audit']['checklist']['cl_audit_type'];
                    $result['audit_month'] = $audits['start_date'];
                    $result['hotel_name'] = $audits['audit']['hotel']['hotel_name'];
                    $result['assigned_by'] = $audits['createdBy']['assigned_by'];
                    $result['auditor'] = $audits['auditor']['auditor'];
                    $result['sections'] = $sections;
                    if ($result) {
                        $status = AuditsSchedules::updateAll([
                            'status' => 1
                        ], [
                            'audit_schedule_id' => $audit_schedule_id,
                            'is_deleted' => 0
                        ]);
                        if ($status) {

                            $data = [];
                            $data['module'] = 'audit';
                            $data['type'] = 'start';
                            $data['message'] = "Audit - <b>" . $auditScheduled->audit_schedule_name . '</b> is started by ' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name;
                            Yii::$app->events->createEvent($data);

                            $transaction->commit();

                            $output = [
                                '200' => 'Success',
                                'response' => 'Success',
                                'message' => 'Questions',
                                'Questions' => $result
                            ];
                        }
                    }
                } else {
                    $output = [
                        '400' => 'fail',
                        'response' => 'fail',
                        'message' => 'Audit already Started'
                    ];
                }
            } else {
                $output = [
                    '404' => 'fail',
                    'response' => 'fail',
                    'message' => 'No Post data'
                ];
            }

            ini_set('memory_limit','128M');
            ini_set('max_execution_time','30');
            return $output;
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw new HttpException(422, $ex->getMessage());
        }
    }

    private function actionPrepareSubSection($section_subsections, $all_sub_sections)
    {
        $sub_section_arr = array();
        $exixtingSubsectionId = '';
        $subsectionsArray = [];

        foreach ($section_subsections as $value) {

            if (!in_array($value['q_sub_section'], $subsectionsArray)) {
                $each_sub_section_arr = [];
                $subsectionId = $value['q_sub_section'] ? $value['q_sub_section'] : 0;
                $each_sub_section_arr['sub_section_id'] = $subsectionId;
                $each_sub_section_arr['q_sub_section_is_dynamic'] = $value['q_sub_section_is_dynamic'] ? $value['q_sub_section_is_dynamic'] : 0;
                $each_sub_section_arr['sub_section_name'] = $subsectionId ? $all_sub_sections[$value['q_sub_section']]['ss_subsection_name'] : 'Dynamic';
                $each_sub_section_arr['questions'] = $this->actionPrepareQuestionArray($section_subsections, $subsectionId);
                $sub_section_arr[] = $each_sub_section_arr;
                $subsectionsArray[] = $value['q_sub_section'];
            }
        }
        return $sub_section_arr;
    }

    private function actionPrepareQuestionArray($section_subsections, $q_sub_section = '')
    {
        $question_arr = [];
        foreach ($section_subsections as $value) {
            if ($q_sub_section == $value['q_sub_section']) {
                $access = [];
                $accessJson = json_decode($value['q_access_type']);
                $accessJson = $accessJson ? $accessJson : [];
                foreach ($accessJson as $accessId) {
                    $access[] = [
                        'access_id' => $accessId
                    ];
                }
                $options = [];
                $optionsUns = unserialize($value['options']);
                $optionsUns = $optionsUns ? $optionsUns : [];
                foreach ($optionsUns as $key => $optionValue) {
                    $options[] = [
                        'option' => $optionValue,
                        'optionId' => $key
                    ];
                }
                $each_question_arr = [];

                if (in_array($value['q_response_type'], [
                    1,
                    2
                ])) {
                    $option1 = $value['q_response_type'] == 1 ? 'True' : 'Yes';
                    $option2 = $value['q_response_type'] == 1 ? 'False' : 'No';

                    $options[] = [
                        'option' => $option1,
                        'optionId' => 1
                    ];
                    $options[] = [
                        'option' => $option2,
                        'optionId' => 0
                    ];
                }

                $each_question_arr = [
                    'question_id' => $value['audits_checklist_questions_id'],
                    'question_name' => $value['q_text'],
                    'response_type' => $value['q_response_type'],
                    'access' => $access,
                    'options' => $options,
                ];
                $question_arr[] = $each_question_arr;
            }
        }
        return $question_arr;
    }

}
