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
use app\models\AuditsSchedules;
use yii\web\HttpException;
use app\models\AnswerAttachments;
use yii\helpers\ArrayHelper;
use app\models\Sections;
use app\models\SubSections;
use yii\helpers\Url;

class AuditsController extends ActiveController
{

	public $modelClass = 'app\models\User';

	public function behaviors()
	{
		$behaviors = parent::behaviors();
		$behaviors['authenticate'] = [
			'class' => HttpBearerAuth::className()
		];
		return $behaviors;
	}

	public function actionAllAudits()
	{
		try {
			$output = [];
			$user_id = Yii::$app->request->post('user_id');
			if ($user_id) {
				$status = [
					0,
					1,
					2
				];
				$result = $this->getAuditsByUser($user_id, $status);
				$result_start = [];
				foreach ($result as $auditresesults) {
					$startDate = $auditresesults['start_date'];
					$endDate = $auditresesults['end_date'];
					$currentDate = date('Y-m-d');
					$auditresesults['is_audit_start'] = 1;
					if (!(strtotime($startDate) <= strtotime($currentDate))) {
						$auditresesults['is_audit_start'] = 0;
					}

					if ($auditresesults['deligation_flag'] && $auditresesults['deligation_user_id'] && $auditresesults['deligation_user_id'] == Yii::$app->user->identity->id) {
						$auditresesults['deligation_flag'] = 0;
						$auditresesults['deligation_status'] = 0;
					}

					$result_start[] = $auditresesults;
				}

				$audits = ($result_start) ? $result_start : [];
				$output = [
					'200' => 'Success',
					'response' => 'success',
					'message' => 'Successfull',
					'audits' => $audits
				];
			} else {
				$output = [
					'400' => 'fail',
					'response' => 'fail',
					'message' => 'No Post Data'
				];
			}
			return $output;
		} catch (yii\base\Exception $ex) {
			throw new HttpException(422, $ex->getMessage());
		}
	}

    /**
     * @return array
     * @throws HttpException
     */
    public function actionDeligateUsers()
    {
    	$output = [];
    	try {

    		$audit_schedule_id = Yii::$app->request->post('audit_id');
    		$userId = Yii::$app->request->post('user_id');

    		if ($audit_schedule_id) {

    			$audit = AuditsSchedules::find()->joinWith(['audit'])->where(['audit_schedule_id' => $audit_schedule_id])->asArray()->one();
    			$audit = $audit['audit'];

    			$auditors = Audits::getAuditorsList($audit['department_id'], $audit['hotel_id'], $audit['location_id']);
    			$absoluteBaseUrl = Url::base(true);
    			$path = $absoluteBaseUrl . "/imageuploads/";
    			$auditors = ($auditors) ? $auditors : [];
    			$users = [];

    			$hotels = models\Hotels::findOne($audit['hotel_id']);
    			$hotelName = $hotels ? $hotels->hotel_name : '';

    			$departments = models\Departments::findOne($audit['department_id']);
    			$departmentName = $departments ? $departments->department_name : '';

    			$location = Locations::find()->joinWith(['locationCity'])->where(['location_id' => $audit['location_id']])->asArray()->one();

    			$locationName = ($location && $location['locationCity'] && isset($location['locationCity']['name'])) ? $location['locationCity']['name'] : '';

    			foreach ($auditors as $auditor) {
    				if ($userId != $auditor['user_id']) {


    					$alias = Yii::getAlias('@webroot');
    					$user = [];
    					if ($auditor['image'] && file_exists($alias . '/imageuploads/' . $auditor['image'])) {
    						$user['image'] = $path . $auditor['image'];
    					} else {
    						$user['image'] = '';
    					}
    					$user['name'] = $auditor['first_name'] . ' ' . $auditor['last_name'];
    					$user['user_id'] = $auditor['user_id'];
    					$user['hotel_name'] = $hotelName;
    					$user['department_name'] = $departmentName;
    					$user['location_name'] = $locationName;
    					$user['email'] = $auditor['email'];
    					$users[] = $user;
    				}
    			}

    			$output = [
    				'200' => 'Success',
    				'response' => 'success',
    				'message' => 'Successfull',
    				'users' => $users
    			];
    		} else {
    			$output = [
    				'400' => 'fail',
    				'response' => 'fail',
    				'message' => 'No Post Data'
    			];
    		}
    		return $output;
    	} catch (\Exception $ex) {
    		throw new HttpException(422, $ex->getMessage());
    	}
    }

    /**
     * @return array
     * @throws HttpException
     */
    public function actionUpdateDeligate()
    {
    	$output = [];
    	try {
    		$deletaged_user_id = Yii::$app->request->post('user_id');
    		$audit_schedule_id = Yii::$app->request->post('audit_id');

    		if ($deletaged_user_id && $audit_schedule_id) {
    			$result = AuditsSchedules::updateAll([
    				'deligation_user_id' => $deletaged_user_id,
    				'deligation_status' => 1
    			], [
    				'audit_schedule_id' => $audit_schedule_id,
    				'is_deleted' => 0,
    				'deligation_status' => 0
    			]);

    			if ($result) {

    				$auditScheduled = AuditsSchedules::find()
    				->joinWith(['audit.checklist', 'audit.hotel', 'audit.department'])
    				->andWhere(['audit_schedule_id' => $audit_schedule_id])
    				->asArray()
    				->one();
    				$user = User::findOne($deletaged_user_id);
    				$name = $user->first_name;

    				$notifications = [];
    				$notifications['type'] = 'delegation';
    				$notifications['toEmail'] = $user->email;
    				$notifications['mobileNumber'] = $user->phone;
    				$notifications['deviceToken'] = $user->device_token;

    				$attributes = $auditScheduled;
    				$attributes['department'] = isset($auditScheduled['audit']['department']['department_name']) ? $auditScheduled['audit']['department']['department_name'] : '';
    				$attributes['checkList'] = isset($auditScheduled['audit']['checklist']['cl_name']) ? $auditScheduled['audit']['checklist']['cl_name'] : '';
    				$attributes['hotel'] = isset($auditScheduled['audit']['hotel']['hotel_name']) ? $auditScheduled['audit']['hotel']['hotel_name'] : '';

    				$notifications['data'] = $attributes;
    				$notifications['userId'] = $user->user_id;
    				Yii::$app->scheduler->triggerNotifications($notifications);

    				$data = [];
    				$data['module'] = 'audit';
    				$data['type'] = 'delegated';
    				$data['message'] = "Audit - <b>" . $auditScheduled['audit_schedule_name'] . '</b> is delegated by' . Yii::$app->user->identity->first_name . ' to ' . $name;


    				Yii::$app->events->createEvent($data);

    				$output = [
    					'200' => 'Success',
    					'response' => 'success',
    					'message' => 'Audit Delegated Successfully'
    				];
    			} else {
    				$output = [
    					'400' => 'fail',
    					'response' => 'fail',
    					'message' => 'Audit already Assigned'
    				];
    			}
    		} else {
    			$output = [
    				'404' => 'fail',
    				'response' => 'fail',
    				'message' => 'No Post Data'
    			];
    		}
    		return $output;
    	} catch (\Exception $ex) {
    		throw new HttpException(422, $ex->getMessage());
    	}
    }

    public function actionCancelDelegation()
    {
    	$output = [];
    	try {
    		$deletaged_user_id = Yii::$app->request->post('user_id');
    		$audit_schedule_id = Yii::$app->request->post('audit_id');

    		if ($deletaged_user_id && $audit_schedule_id) {
    			$auditSchedules = AuditsSchedules::find()->where(['audit_schedule_id' => $audit_schedule_id])->asArray()->one();

    			if ($auditSchedules['status'] == 0) {
    				$result = AuditsSchedules::updateAll([
    					'deligation_user_id' => 0,
    					'deligation_status' => 0
    				], [
    					'audit_schedule_id' => $audit_schedule_id,
    					'is_deleted' => 0,
    					'status' => [0],
    					'deligation_status' => 1
    				]);

    				if ($result) {

    					$auditScheduled = AuditsSchedules::findOne($audit_schedule_id);
    					$data = [];
    					$data['module'] = 'audit';
    					$data['type'] = 'delegated';
    					$data['message'] = "Audit - <b>" . $auditScheduled->audit_schedule_name . '</b> delegation is canceled by' . Yii::$app->user->identity->first_name;
    					Yii::$app->events->createEvent($data);

    					$output = [
    						'200' => 'Success',
    						'response' => 'success',
    						'message' => 'Delegation Cancelled'
    					];
    				} else {
    					$output = [
    						'400' => 'fail',
    						'response' => 'fail',
    						'message' => 'Audit already Assigned'
    					];
    				}
    			} else {
    				$output = [
    					'400' => 'fail',
    					'response' => 'fail',
    					'message' => 'Audit already started. Undelegation can\'t happen.'
    				];
    			}
    		} else {
    			$output = [
    				'404' => 'fail',
    				'response' => 'fail',
    				'message' => 'No Post Data'
    			];
    		}
    		return $output;
    	} catch (\Exception $ex) {
    		throw new HttpException(422, $ex->getMessage());
    	}
    }

    public function actionAuditsHistory()
    {
    	try {
    		$output = [];
    		$user_id = Yii::$app->request->post('user_id');
    		if ($user_id) {
    			$status = [3];
    			$result = $this->getAuditsByUser($user_id, $status);
    			$audits = ($result) ? $result : [];
    			$output = [
    				'200' => 'Success',
    				'response' => 'success',
    				'message' => 'Successfull',
    				'audits' => $audits
    			];
    		} else {
    			$output = [
    				'400' => 'fail',
    				'response' => 'fail',
    				'message' => 'No Post Data'
    			];
    		}
    		return $output;
    	} catch (\Exception $ex) {
    		throw new HttpException(422, $ex->getMessage());
    	}
    }

    private function getAuditsByUser($user_id, $status)
    {
    	$result = (new yii\db\Query())->select('CONCAT_WS(" ", u.`first_name`, u.`last_name`) as name,sa.deligation_user_id,c.name as location_name,h.hotel_name,d.department_name,sa.updated_at as audit_submitted_date,ck.cl_audit_type as audit_type,ck.cl_name as audit_name,CONCAT_WS(" ", au.`first_name`, au.`last_name`) as assignedby,ck.cl_audit_type as audit_type,a.deligation_flag,sa.audit_schedule_id as audit_id,sa.status,a.audit_name as parent,sa.audit_schedule_name as child,sa.deligation_status,sa.start_date,sa.end_date')
    	->from('{{%audits}} a')
    	->join('LEFT JOIN', "{{%audits_schedules}} sa", 'sa.audit_id = a.audit_id ')
    	->join("LEFT JOIN", '{{%user}} u', 'u.user_id = sa.auditor_id')
    	->join("LEFT JOIN", '{{%locations}} l', 'l.location_id = a.location_id')
    	->join("LEFT JOIN", '{{%cities}} c', 'c.id = l.location_city_id')
    	->join("LEFT JOIN", '{{%hotels}} h', 'h.hotel_id = a.hotel_id')
    	->join("LEFT JOIN", '{{%departments}} d', 'd.department_id = a.department_id')
    	->join("LEFT JOIN", '{{%checklists}} ck', 'ck.checklist_id = a.checklist_id')
    	->join("LEFT JOIN", '{{%user}} au', 'au.user_id = sa.created_by')
    	->where([
    		'sa.auditor_id' => $user_id
    	])
    	->orWhere([
    		'or',
    		[
    			'sa.deligation_user_id' => $user_id
    		]
    	])
    	->andWhere([
    		'IN',
    		'sa.status',
    		$status
    	])
    	->andWhere([
    		 'a.is_deleted' => 0,
    		 'sa.is_deleted' => 0
    	])
    	->all();
    	return $result;
    }

    public function actionAuditSummery()
    {
    	try {
    		ini_set('memory_limit','-1');
    		ini_set('max_execution_time','180');
    		$audit_schedule_id = Yii::$app->request->post('audit_id');
    		$output = $this->getAuditQuestions($audit_schedule_id, 3);
    		ini_set('memory_limit','128M');
    		ini_set('max_execution_time','30');
    		return $output;
    	} catch (\Exception $ex) {
    		throw new HttpException(422, $ex->getTraceAsString());
    	}
    }

    public function actionResumeAudits()
    {
    	try {
    		ini_set('memory_limit','-1');
    		ini_set('max_execution_time','180');
    		$audit_schedule_id = Yii::$app->request->post('audit_id');
    		$output = $this->getAuditQuestions($audit_schedule_id, [
    			1,
    			2
    		]);
    		ini_set('memory_limit','128M');
    		ini_set('max_execution_time','30');
    		return $output;
    	} catch (\Exception $ex) {
    		throw new HttpException(422, $ex->getTraceAsString());
    	}
    }

    Private function getAuditQuestions($audit_schedule_id, $status)
    {
    	$audits = AuditsSchedules::find()->joinWith([
    		'auditor as user' => function ($query) {
    			$query->select([
    				'CONCAT_WS(" ", user.`first_name`, user.`last_name`) as auditor'

    			]);
    		},
    		'createdBy as assignedUser' => function ($query) {
    			$query->select([
    				'CONCAT_WS(" ", assignedUser.`first_name`, assignedUser.`last_name`)  as assigned_by'
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
    		},
    		'answers ans' => function ($query) {
    			$query->select([
    				'ans.*'
    			]);
    		}
    	])
    	->where([
    		'audit_schedule_id' => $audit_schedule_id,
    		AuditsSchedules::tableName() . '.is_deleted' => 0,
    		AuditsSchedules::tableName() . '.status' => $status
    	])
    	->asArray()
    	->one();

    	$auditScore = 0;
    	if ($audits) {
    		if ($status == 3) {
    			$auditScore = AuditsSchedules::getAuditScore($audit_schedule_id);
    		}

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
    		$all_sub_sections = ArrayHelper::index(SubSections::find()->where([
    			'is_deleted' => 0
    		])
    		->asArray()
    		->all(), 'sub_section_id');
    		foreach ($questions_sections as $key => $each_section) {

    			$each_section_arr = array();
    			$each_section_arr['section_id'] = $all_sections[$key]['section_id'];
    			$each_section_arr['section_name'] = $all_sections[$key]['s_section_name'];
    			$each_section_arr['sub_sections'] = $this->actionPrepareSubSection($each_section, $all_sub_sections, $status);
    			if ($each_section_arr) {
    				$sections[] = $each_section_arr;
    			}
    		}

    		$result = array();
    		$result['audit_id'] = $audits['audit_schedule_id'];
    		$result['audit_name'] = $audits['audit_schedule_name'];
    		$result['checklist_name'] = $audits['audit']['checklist']['cl_name'];
    		$result['location_name'] = $audits['audit']['location']['locationCity']['name'];
    		$result['hotel_name'] = $audits['audit']['hotel']['hotel_name'];
    		$result['department_name'] = $audits['audit']['department']['department_name'];
    		$result['audit_type'] = $audits['audit']['checklist']['cl_audit_type'];
    		$result['audit_month'] = $audits['start_date'];
    		$result['assigned_by'] = $audits['createdBy']['assigned_by'];
    		$result['aduit_submitted_date'] = $audits['updated_at'];
    		$result['auditor'] = $audits['auditor']['auditor'];
    		$result['audit_score'] = $auditScore;
    		$result['sections'] = $sections;

    		$tickets = models\Tickets::find()->where([
    			'audit_schedule_id' => $audits['audit_schedule_id']
    		])
    		->asArray()
    		->all();

    		$chronicTickets = array_filter(ArrayHelper::getColumn($tickets, function ($element) {
    			if ($element['chronicity'] == 1) {
    				return true;
    			}
    		}));

    		$result['non_compliance_count'] = count($tickets);
    		$result['chronic_issues_count'] = count($chronicTickets);

    		if ($result) {

    			$output = [
    				'200' => 'Success',
    				'response' => 'Success',
    				'message' => 'Questions',
    				'Questions' => $result
    			];
    		}
    	} else {
    		$output = [
    			'400' => 'fail',
    			'response' => 'fail',
    			'message' => 'No Audits found'
    		];
    	}

    	return $output;
    }

    private function actionPrepareSubSection($section_subsections, $all_sub_sections, $status = false)
    {
    	$sub_section_arr = array();

    	$subsectionsArray = [];

    	foreach ($section_subsections as $value) {

    		if (!in_array($value['q_sub_section'], $subsectionsArray)) {

    			$each_sub_section_arr = [];
    			$subsectionId = $value['q_sub_section'] ? $value['q_sub_section'] : 0;
    			$each_sub_section_arr['sub_section_id'] = $subsectionId;
    			$each_sub_section_arr['q_sub_section_is_dynamic'] = $value['q_sub_section_is_dynamic'] ? $value['q_sub_section_is_dynamic'] : 0;

    			$each_sub_section_arr['sub_section_name'] = 'Dynamic';

    			if ($subsectionId) {

    				if (isset($all_sub_sections[$value['q_sub_section']])) {

    					$each_sub_section_arr['sub_section_name'] = $all_sub_sections[$value['q_sub_section']]['ss_subsection_name'];
    				} else {
    					$each_sub_section_arr['sub_section_name'] = $value['q_sub_section'];
    				}
    			}

    			$each_sub_section_arr['questions'] = $this->actionPrepareQuestionArray($section_subsections, $subsectionId);
    			if ($status == 3) {
    				if ($each_sub_section_arr['sub_section_name'] != 'Dynamic') {
    					$sub_section_arr[] = $each_sub_section_arr;
    				}
    			} else {
    				$sub_section_arr[] = $each_sub_section_arr;
    			}


    			$subsectionsArray[] = $value['q_sub_section'];
    		}
    	}
    	return $sub_section_arr;
    }

    private function actionPrepareQuestionArray($section_subsections, $q_sub_section = '')
    {
    	$question_arr = [];
    	$questionIds = [];
    	$auditIds = [];
    	$questionIds = ArrayHelper::getColumn($section_subsections, 'audits_checklist_questions_id');
    	$auditIds = ArrayHelper::getColumn($section_subsections, 'audit_id');
        /*
         * foreach ($section_subsections as $questionsIds){
         * $questionIds[] = $questionsIds['audits_checklist_questions_id'];
         *
         * $auditIds[] = $questionsIds['audit_id'];
         * }
         */
        $answers = models\Answers::find()->select([
        	'answer_id',
        	'answer_value',
        	'question_id',
        	'options_values',
        	'not_applicable',
        	'observation_text'
        ])
        ->where([
        	'question_id' => $questionIds,
        	'audit_id' => $auditIds
        ])
        ->asArray()
        ->all();

        $answerValues = ArrayHelper::index($answers, 'question_id');

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

        		$answerOptions = [];
        		$answerAttachments = [];
        		$answers = [];

        		$each_question_arr = [
        			'question_id' => $value['audits_checklist_questions_id'],
        			'question_name' => $value['q_text'],
        			'q_sub_section_is_dynamic' => $value['q_sub_section_is_dynamic'],
        			'response_type' => $value['q_response_type'],
        			'access' => $access,
        			'options' => $options
        		];

        		if (isset($answerValues[$value['audits_checklist_questions_id']]['options_values'])) {
        			$answerOptionUn = unserialize($answerValues[$value['audits_checklist_questions_id']]['options_values']);
        			$answerOptionUn = $answerOptionUn ? $answerOptionUn : [];
        			foreach ($answerOptionUn as $key => $answerValue) {
        				$answerOptions[] = [
        					'option' => $answerValue,
        					'optionId' => $key
        				];
        			}
        			$answerAttachments = AnswerAttachments::find()->select('answer_attachment_path')
        			->where([
        				'answer_id' => $answerValues[$value['audits_checklist_questions_id']]['answer_id']
        			])
        			->asArray()
        			->all();
        			$answerAttachmentsPath = [];
        			$absoluteBaseUrl = Url::base(true);
        			$path = $absoluteBaseUrl . "/img/answers_attachments/";
        			foreach ($answerAttachments as $attachments) {
        				$alias = Yii::getAlias('@webroot');
        				if (file_exists($alias . '/img/answers_attachments/' . $attachments['answer_attachment_path'])) {
        					$answerAttachmentsPath[] = [
        						'attachment_path' => $path . $attachments['answer_attachment_path']
        					];
        				}
        			}
        			$each_question_arr['answer'] = [
        				'answer_id' => $answerValues[$value['audits_checklist_questions_id']]['answer_id'],
        				'answer_value' => $answerValues[$value['audits_checklist_questions_id']]['answer_value'],
        				'options_values' => $answerOptions,
        				'not_applicable' => $answerValues[$value['audits_checklist_questions_id']]['not_applicable'],
        				'answer_attachments' => $answerAttachmentsPath,
        				'observation_text' => $answerValues[$value['audits_checklist_questions_id']]['observation_text']
        			];
        		}

        		$question_arr[] = $each_question_arr;
        	}
        }

        return $question_arr;
    }
}
