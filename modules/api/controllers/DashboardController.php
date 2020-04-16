<?php

namespace app\modules\api\controllers;

use yii;
use yii\db\Query;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use app\models;
use yii\helpers\ArrayHelper;

class DashboardController extends ActiveController
{

    public $modelClass = 'app\models\Audits';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticate'] = [
            'class' => HttpBearerAuth::className()
        ];
        return $behaviors;
    }

    public function actionAuditdata()
    {
        $output = [];
        $auditor_id = Yii::$app->request->post('auditor_id');
        if (!empty($auditor_id)) {
            $finalData = array();
            $query = new Query();
            $query->select([
                'tbl_gp_audits.hotel_id',
                'tbl_gp_audits.department_id',
                'tbl_gp_hotels.hotel_name',
                'tbl_gp_departments.department_name'

            ])
                ->from('tbl_gp_audits')
                ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id = tbl_gp_audits.hotel_id')
                ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_audits.department_id')
                ->join('INNER JOIN', 'tbl_gp_audits_schedules', 'tbl_gp_audits_schedules.audit_id = tbl_gp_audits.audit_id')
                ->andFilterWhere([
                    'tbl_gp_audits_schedules.auditor_id' => $auditor_id
                ])->orWhere([
                    'or',
                    [
                        'tbl_gp_audits_schedules.deligation_user_id' => $auditor_id
                    ]
                ]);
            $command = $query->createCommand();

            $data = $command->queryAll();
            if ($data) {
                $data = ArrayHelper::index($data, null, 'hotel_id');
                foreach ($data as $key => $ldata) {
                    $departmentData = ArrayHelper::index($ldata, 'department_id');
                    $finalData[$key]['hotel_id'] = $key;
                    $finalData[$key]['hotel_name'] = current(ArrayHelper::getColumn($departmentData, 'hotel_name'));

                    $finalData[$key]['departments'] = $this->getDepartments($departmentData);
                }
                $finalData = array_values($finalData);
                foreach ($finalData as $key => $finalDatum) {
                    $finalData[$key]['departments'] = array_values($finalDatum['departments']);
                }
                $output = [
                    '200' => 'success',
                    'response' => 'success',
                    'data' => $finalData
                ];
            } else {
                $output = [
                    '200' => 'success',
                    'response' => 'fail',
                    'data' => 'No audits were available.'
                ];
            }

            return $output;
        } else {
            $output = [
                '404' => 'fail',
                'response' => 'fail',
                'message' => 'No data found'
            ];
            Yii::$app->response->statusCode = 200;
        }
        return $output;
    }

    function fix_keys($array)
    {
        foreach ($array as $k => $val) {
            if (is_array($val))
                $array[$k] = fix_keys($val); //recurse
        }
        return array_values($array);
    }

    public function getDepartments($departmentData)
    {
        $array = ArrayHelper::getColumn($departmentData, function ($element) {
            $array = [];
            $array[$element['department_id']] = array('department_id' => $element['department_id'], 'department_name' => $element['department_name']);
            ///$array[]['department_id'] = $element['department_id'];
            ///$array[$element['department_id']] = $element['department_name'];
            return $array;
        });
        $array = call_user_func_array('array_replace', $array);
        return $array;
    }

    /**
     * Api for getting dashboard audit and ticket details
     * @return array
     * @throws yii\db\Exception
     */
    public function actionDashboard()
    {
        try {
            $output = [];

            $post = Yii::$app->request->post();
            $hotel_id = isset($post['hotel_id']) ? $post['hotel_id'] : '';
            $dept_id = isset($post['department_id']) ? $post['department_id'] : '';
            $startDate = isset($post['start_date']) ? $post['start_date'] : '';
            $endDate = isset($post['end_date']) ? $post['end_date'] : '';

            $query = new Query();

            $query->select([
                'count(tbl_gp_audits_schedules.audit_schedule_id) as total_audit_count',
                'COUNT(IF(tbl_gp_audits_schedules.status=3,1, null) ) as total_completed_audit_count',
            ])
                ->from('tbl_gp_audits')
                ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id = tbl_gp_audits.hotel_id')
                ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_audits.department_id')
                ->join('INNER JOIN', 'tbl_gp_audits_schedules', 'tbl_gp_audits_schedules.audit_id = tbl_gp_audits.audit_id')
                ->andFilterWhere([
                    'tbl_gp_audits_schedules.auditor_id' => Yii::$app->user->identity->id,
                    'tbl_gp_audits_schedules.is_deleted' => 0,
                ])->orWhere([
                    'or',
                    [
                        'tbl_gp_audits_schedules.deligation_user_id' => Yii::$app->user->identity->id
                    ]
                ]);


            $query2 = new Query();
            $query2->select([
                'count(ticket_id) as ticket_count',
                'COUNT(IF(tbl_gp_tickets.chronicity=1,1, null) ) as ticket_chronicity_count',
                'COUNT(IF(tbl_gp_tickets.status=3,1, null) ) as ticket_completed_count'
            ])
                ->from('tbl_gp_tickets')
                ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id = tbl_gp_tickets.hotel_id');
            $query2->andFilterWhere(
                [
                    'tbl_gp_tickets.created_by' => Yii::$app->user->identity->id
                ]
            );

            $query3 = new Query();
            $query3->select([
                'tbl_gp_tickets.department_id',
                'COUNT(IF(tbl_gp_tickets.status IN(1,2),1, null) ) as ticket_open_count',
            ])
                ->from('tbl_gp_tickets');

            $query3->andFilterWhere(
                [
                    'tbl_gp_tickets.created_by' => Yii::$app->user->identity->id
                ]
            );

            $query4 = new Query();
            $query4->select([
                'DATE_FORMAT(tbl_gp_audits_schedules.end_date, "%b") as month',
                'round(((COUNT(IF(tbl_gp_audits_schedules.status=3,1, null) )/COUNT(tbl_gp_audits_schedules.audit_schedule_id ))*100)) as completed_audit_percentage',
            ])
                ->from('tbl_gp_audits_schedules')
                ->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id = tbl_gp_audits_schedules.audit_id');

            $query4->andFilterWhere([
                'tbl_gp_audits_schedules.auditor_id' => Yii::$app->user->identity->id,
                'tbl_gp_audits_schedules.is_deleted' => 0
            ])->orWhere([
                'or',
                [
                    'tbl_gp_audits_schedules.deligation_user_id' => Yii::$app->user->identity->id
                ]
            ]);


            $query5 = new Query();
            $query5->select([
                'tbl_gp_audits_schedules.audit_id', 'round(SUM(tbl_gp_answers.answer_score) /(COUNT(tbl_gp_answers.answer_value)* 10) * 100) as score'
            ])
                ->from('tbl_gp_audits_schedules')
                ->join('INNER JOIN', 'tbl_gp_audits_checklist_questions', 'tbl_gp_audits_checklist_questions.audit_id = tbl_gp_audits_schedules.audit_schedule_id')
                ->join('INNER JOIN', 'tbl_gp_answers', 'tbl_gp_answers.question_id = tbl_gp_audits_checklist_questions.audits_checklist_questions_id')
                ->join('INNER JOIN', 'tbl_gp_sections', 'tbl_gp_sections.section_id = tbl_gp_audits_checklist_questions.q_section')
                ->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id = tbl_gp_audits_schedules.audit_id')
                ->andFilterWhere(['tbl_gp_audits_schedules.status' => 3])
                ->andFilterWhere(['tbl_gp_audits_schedules.is_deleted' => 0])
                ->andFilterWhere(['tbl_gp_answers.not_applicable' => 0])
                ->andFilterWhere([
                    'tbl_gp_audits_schedules.auditor_id' => Yii::$app->user->identity->id
                ])->orWhere([
                    'or',
                    [
                        'tbl_gp_audits_schedules.deligation_user_id' => Yii::$app->user->identity->id
                    ]
                ]);


            if (!empty($hotel_id) && empty($dept_id) && empty($startDate) && empty($endDate)) {

                $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $hotel_id]);
                $command1 = $query->createCommand();
                $data['audits'] = $command1->queryAll();

                $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $hotel_id]);
                $command2 = $query2->createCommand();
                $data['tickets'] = $command2->queryAll();

                $query3->andFilterWhere(['tbl_gp_tickets.hotel_id' => $hotel_id])
                    ->groupBy(['tbl_gp_tickets.department_id']);
                $command3 = $query3->createCommand();
                $data['department_by'] = $command3->queryAll();

                $query4->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime('-1 year'))])
                    ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d')])
                    ->andFilterWhere(['tbl_gp_audits.hotel_id' => $hotel_id])
                    ->groupBy(['DATE_FORMAT(tbl_gp_audits_schedules.end_date, "%M-%Y")']);
                $query4->orderBy(['tbl_gp_audits_schedules.end_date' => SORT_DESC]);
                $command4 = $query4->createCommand();
                $data['completed_audits'] = $command4->queryAll();

                $query5->andFilterWhere(['tbl_gp_audits.hotel_id' => $hotel_id]);
                $query5->groupBy([
                    'tbl_gp_audits_schedules.audit_id'
                ]);
                $command = $query5->createCommand();
                $auditComp = $command->queryAll();
                $finalArray = array_column($auditComp, 'score');

                if (count($finalArray)) {
                    $data['compliance'] = round(array_sum($finalArray) / count($finalArray), 2);
                } else {
                    $data['compliance'] = round(array_sum($finalArray), 2);
                }


            } elseif (!empty($hotel_id) && !empty($dept_id) && empty($startDate) && empty($endDate)) {
                $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $hotel_id])
                    ->andFilterWhere(['tbl_gp_audits.department_id' => $dept_id]);
                $command = $query->createCommand();
                $data['audits'] = $command->queryAll();

                $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $hotel_id])
                    ->andFilterWhere(['tbl_gp_tickets.department_id' => $dept_id]);
                $command = $query2->createCommand();
                $data['tickets'] = $command->queryAll();

                $query3->andFilterWhere(['tbl_gp_tickets.hotel_id' => $hotel_id])
                    ->andFilterWhere(['tbl_gp_tickets.department_id' => $dept_id])
                    ->groupBy(['tbl_gp_tickets.department_id']);
                $command3 = $query3->createCommand();
                $data['department_by'] = $command3->queryAll();

                $query4->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d')])
                    ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime('+1 year'))])
                    ->andFilterWhere(['tbl_gp_audits.hotel_id' => $hotel_id])
                    ->andFilterWhere(['tbl_gp_audits.department_id' => $dept_id])
                    ->groupBy(['DATE_FORMAT(tbl_gp_audits_schedules.end_date, "%M-%Y")']);
                $query4->orderBy(['tbl_gp_audits_schedules.end_date' => SORT_DESC]);
                $command4 = $query4->createCommand();
                $data['completed_audits'] = $command4->queryAll();

                $query5->andFilterWhere(['tbl_gp_audits.hotel_id' => $hotel_id]);
                $query5->andFilterWhere(['tbl_gp_audits.department_id' => $dept_id]);
                $query5->groupBy([
                    'tbl_gp_audits_schedules.audit_id'
                ]);
                $command = $query5->createCommand();
                $auditComp = $command->queryAll();
                $finalArray = array_column($auditComp, 'score');

                if (count($finalArray)) {
                    $data['compliance'] = round(array_sum($finalArray) / count($finalArray), 2);
                } else {
                    $data['compliance'] = round(array_sum($finalArray), 2);
                }

            } elseif (!empty($hotel_id) && empty($dept_id) && !empty($startDate) && !empty($endDate)) {

                $datetime1 = new \DateTime($startDate);
                $datetime2 = new \DateTime($endDate);
                $interval = $datetime1->diff($datetime2)->m;

                $date1 = new \DateTime($startDate);
                $date2 = $date1->diff(new \DateTime($endDate));
                $date_diff = $date2->y;


                $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $hotel_id])
                    ->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($startDate))])
                    ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime($endDate))]);
                $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $hotel_id])
                    ->andFilterWhere(['between', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime($startDate)), date('Y-m-d', strtotime($endDate))]);
                $command = $query->createCommand();
                $data['audits'] = $command->queryAll();
                $command = $query2->createCommand();
                $data['tickets'] = $command->queryAll();

                $query3->andFilterWhere(['tbl_gp_tickets.hotel_id' => $hotel_id])
                    ->groupBy(['tbl_gp_tickets.department_id']);
                $command3 = $query3->createCommand();
                $data['department_by'] = $command3->queryAll();

                if ($date_diff < 1) {
                    $query4->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($startDate))])
                        ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime($endDate))])
                        ->andFilterWhere(['tbl_gp_audits.hotel_id' => $hotel_id])
                        ->groupBy(['DATE_FORMAT(tbl_gp_audits_schedules.end_date, "%M-%Y")']);
                } else {
                    $query4->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($startDate))])
                        ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime(date("Y-m-d", strtotime($startDate)) . " +1 year"))])
                        ->andFilterWhere(['tbl_gp_audits.hotel_id' => $hotel_id])
                        ->groupBy(['DATE_FORMAT(tbl_gp_audits_schedules.end_date, "%M-%Y")']);
                }
                $query4->orderBy(['tbl_gp_audits_schedules.end_date' => SORT_DESC]);

                $command4 = $query4->createCommand();
                $data['completed_audits'] = $command4->queryAll();

                $query5->andFilterWhere(['tbl_gp_audits.hotel_id' => $hotel_id])
                    ->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($startDate))])
                    ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime($endDate))]);
                $query5->groupBy([
                    'tbl_gp_audits_schedules.audit_id'
                ]);
                $command = $query5->createCommand();
                $auditComp = $command->queryAll();
                $finalArray = array_column($auditComp, 'score');

                if (count($finalArray)) {
                    $data['compliance'] = round(array_sum($finalArray) / count($finalArray), 2);
                } else {
                    $data['compliance'] = round(array_sum($finalArray), 2);
                }

            } elseif (!empty($hotel_id) && !empty($dept_id) && !empty($startDate) && !empty($endDate)) {

                $datetime1 = new \DateTime($startDate);
                $datetime2 = new \DateTime($endDate);
                $interval = $datetime1->diff($datetime2)->m;

                $date1 = new \DateTime($startDate);
                $date2 = $date1->diff(new \DateTime($endDate));
                $date_diff = $date2->y;


                $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $hotel_id])
                    ->andFilterWhere(['tbl_gp_audits.department_id' => $dept_id])
                    ->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($startDate))])
                    ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime($endDate))]);
                $query2->andFilterWhere(['tbl_gp_tickets.hotel_id' => $hotel_id])
                    ->andFilterWhere(['tbl_gp_tickets.department_id' => $dept_id])
                    ->andFilterWhere(['between', 'tbl_gp_tickets.due_date', date('Y-m-d', strtotime($startDate)), date('Y-m-d', strtotime($endDate))]);
                $command = $query->createCommand();
                $data['audits'] = $command->queryAll();
                $command = $query2->createCommand();
                $data['tickets'] = $command->queryAll();

                $query3->andFilterWhere(['tbl_gp_tickets.hotel_id' => $hotel_id])
                    ->andFilterWhere(['tbl_gp_tickets.department_id' => $dept_id])
                    ->groupBy(['tbl_gp_tickets.department_id']);
                $command3 = $query3->createCommand();
                $data['department_by'] = $command3->queryAll();

                if ($date_diff < 1) {
                    $query4->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($startDate))])
                        ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime($endDate))])
                        ->andFilterWhere(['tbl_gp_audits.hotel_id' => $hotel_id])
                        ->andFilterWhere(['tbl_gp_audits.department_id' => $dept_id])
                        ->groupBy(['DATE_FORMAT(tbl_gp_audits_schedules.end_date, "%M-%Y")']);
                } else {
                    $query4->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($startDate))])
                        ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime(date("Y-m-d", strtotime($startDate)) . " +1 year"))])
                        ->andFilterWhere(['tbl_gp_audits.hotel_id' => $hotel_id])
                        ->andFilterWhere(['tbl_gp_audits.department_id' => $dept_id])
                        ->groupBy(['DATE_FORMAT(tbl_gp_audits_schedules.end_date, "%M-%Y")']);
                }
                $query4->orderBy(['tbl_gp_audits_schedules.end_date' => SORT_DESC]);

                $command4 = $query4->createCommand();
                $data['completed_audits'] = $command4->queryAll();


                $query5->andFilterWhere(['tbl_gp_audits.hotel_id' => $hotel_id])
                    ->andFilterWhere(['tbl_gp_audits.hotel_id' => $hotel_id])
                    ->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($startDate))])
                    ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime($endDate))]);
                $query5->groupBy([
                    'tbl_gp_audits_schedules.audit_id'
                ]);
                $command = $query5->createCommand();
                $auditComp = $command->queryAll();
                $finalArray = array_column($auditComp, 'score');

                if (count($finalArray)) {
                    $data['compliance'] = round(array_sum($finalArray) / count($finalArray), 2);
                } else {
                    $data['compliance'] = round(array_sum($finalArray), 2);
                }

            } else {
                $output = [
                    '200' => 'success',
                    'response' => 'fail',
                    'message' => 'No data found'
                ];
                Yii::$app->response->statusCode = 200;
                return $output;
            }

            if ($data) {
                $output = [
                    '200' => 'success',
                    'response' => 'success',
                    'data' => $data
                ];
            } else {
                $output = [
                    '200' => 'success',
                    'response' => 'fail',
                    'data' => 'No audits were available.'
                ];
            }
            return $output;
        } catch (yii\db\Exception $e) {
            throw new yii\base\Exception($e->getMessage());
        }
    }

    /**
     * @return array
     * @throws yii\db\Exception
     */
    public function actionNotificationData()
    {
        try {
            $due_date = date('Y-m-d ', strtotime(' - 30 days'));
            $user_id = Yii::$app->request->post('user_id');
            if ($user_id) {
                $query = new Query();
                $query->select([
                    'notification_name as name', 'notification_message as message', 'created_at as date'
                ])
                    ->from('tbl_gp_notification_log')
                    ->andFilterWhere(['>=', 'created_at', $due_date])
                    ->andFilterWhere(['notification_type' => 3])
                    ->andFilterWhere(['user_id' => $user_id]);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $records = [];
                foreach ($data as $record) {
		    
                    //$record['date'] = Yii::$app->formatter->asDate($record['date'], 'php:Y-m-d H:i:s');
                    $records[] = $record;
                }
                if ($data) {
                    Yii::$app->response->statusCode = 200;
                    $output = [
                        '200' => 'success',
                        'response' => 'success',
                        'data' => $records
                    ];
                } else {
                    Yii::$app->response->statusCode = 200;
                    $output = [
                        '200' => 'success',
                        'response' => 'fail',
                        'data' => 'Notifications were available.'
                    ];
                }
            } else {
                Yii::$app->response->statusCode = 200;
                $output = [
                    '200' => 'success',
                    'response' => 'fail',
                    'data' => 'Post information is missing.'
                ];
            }

            return $output;
        } catch (yii\base\Exception $e) {
            throw new yii\base\Exception($e->getMessage());
        }
    }

    /**
     * @return array
     * @throws yii\db\Exception
     */
    public function actionGuideLinesDoc()
    {
        try {
            $absoluteBaseUrl = yii\helpers\Url::base(true);
            $filePath = $absoluteBaseUrl . Yii::$app->params['guideLinesPath'];

            $path = Yii::getAlias('@webroot') . Yii::$app->params['guideLinesPath'];

            if (file_exists($path)) {
                $output = [
                    '200' => 'success',
                    'response' => 'success',
                    'data' => $filePath
                ];
            } else {
                $output = [
                    '200' => 'success',
                    'response' => 'fail',
                    'data' => 'File not exists'
                ];
            }

            return $output;
        } catch (yii\base\Exception $e) {
            throw new yii\base\Exception($e->getMessage());
        }
    }
}
