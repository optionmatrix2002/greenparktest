<?php

namespace app\models\search;

use app\models\Departments;
use app\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use app\models\Audits;
use app\models\Preferences;

/**
 * AuditsSearch represents the model behind the search form about `app\models\Audits`.
 */
class AuditsSearch extends Audits {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [
                [
                    'audit_id',
                    'location_id',
                    'hotel_id',
                    'department_id',
                    'checklist_id',
                    'user_id',
                    'status',
                    'deligation_flag',
                    'is_deleted',
                    'created_by',
                    'updated_by'
                ],
                'integer'
            ],
            [
                [
                    'audit_name',
                    'start_date',
                    'end_date',
                    'created_at',
                    'updated_at',
                    'status', 'audit_namesearch',
                    'show_child'
                ],
                'safe'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchAudits($params) {
        $query = Audits::find()->joinWith('scheduleAuditStatus')->
                // ->joinWith('status')
                where([
            'IN',
            'tbl_gp_audits.status',
            [
                0,
                1,
                2,
                3,
                4,
            ]
        ]);

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Preferences::getPrefValByName('grid_length')
            ]
        ]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            self::tableName() . '.audit_id' => $this->audit_id,
            self::tableName() . '.location_id' => $this->location_id,
            self::tableName() . '.hotel_id' => $this->hotel_id,
            self::tableName() . '.department_id' => $this->department_id,
            self::tableName() . '.checklist_id' => $this->checklist_id,
            self::tableName() . '.user_id' => $this->user_id,
            self::tableName() . '.deligation_flag' => $this->deligation_flag,
            self::tableName() . '.status' => $this->status,
            self::tableName() . '.is_deleted' => 0,
            self::tableName() . '.created_by' => $this->created_by,
            self::tableName() . '.updated_by' => $this->updated_by,
            self::tableName() . '.created_at' => $this->created_at,
            self::tableName() . '.updated_at' => $this->updated_at,
        ]);
        $query->orderBy([
            'audit_id' => SORT_DESC
        ]);

        $query->groupBy([
            'tbl_gp_audits.audit_id'
        ]);
        if (!empty($this->start_date) && !empty($this->end_date)) {
            $query->andWhere('DATE(tbl_gp_audits.start_date) >="' . Yii::$app->formatter->asDate($this->start_date, 'php:Y-m-d') . '" AND DATE(tbl_gp_audits.end_date) <="' . Yii::$app->formatter->asDate($this->end_date, 'php:Y-m-d') . '"');
        }
        $query = User::getUserLocationRelationData($query, 'audit');
        $query->andFilterWhere([
            'like',
            'audit_name',
            $this->audit_namesearch
        ]);
        $query->andFilterWhere([
            'like',
            'audit_name',
            $this->audit_name
        ]);
        return $dataProvider;
    }

    public function searchAuditsSchedules($params, $auditsList) {
        $query = \app\models\AuditsSchedules::find()->joinWith('audit')
                // ->joinWith('status')
                ->where(['in', 'tbl_gp_audits_schedules.status', $auditsList]);
        /* echo "<pre>";
          print_r($query->asArray()->all());        exit; */

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Preferences::getPrefValByName('grid_length')
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            self::tableName() . '.hotel_id' => $this->hotel_id,
        ]);

        // grid filtering conditions
        $query->andFilterWhere([
            self::tableName() . '.audit_id' => $this->audit_id,
            self::tableName() . '.location_id' => $this->location_id,
            self::tableName() . '.hotel_id' => $this->hotel_id,
            self::tableName() . '.department_id' => $this->department_id,
            self::tableName() . '.checklist_id' => $this->checklist_id,
            self::tableName() . '.user_id' => $this->user_id,
            self::tableName() . '.deligation_flag' => $this->deligation_flag,
            self::tableName() . '.status' => $this->status,
            self::tableName() . '.created_by' => $this->created_by,
            self::tableName() . '.updated_by' => $this->updated_by,
            self::tableName() . '.created_at' => $this->created_at,
            self::tableName() . '.updated_at' => $this->updated_at,
            self::tableName() . '.is_deleted' => 0,
            'tbl_gp_audits_schedules.is_deleted' => 0
        ]);

        $query->orderBy([
            'tbl_gp_audits_schedules.audit_schedule_name' => SORT_DESC
        ]);

        /* $query->groupBy([
          'tbl_gp_audits.audit_id'
          ]); */

        if (!empty($this->start_date) && !empty($this->end_date)) {
            $query->andWhere('DATE(tbl_gp_audits_schedules.start_date) >="' . Yii::$app->formatter->asDate($this->start_date, 'php:Y-m-d') . '" AND DATE(tbl_gp_audits_schedules.end_date) <="' . Yii::$app->formatter->asDate($this->end_date, 'php:Y-m-d') . '"');
        }
        $query = User::getUserLocationRelationData($query, 'auditParent');

        $query->andFilterWhere([
            'like',
            'audit_schedule_name',
            $this->audit_namesearch
        ]);

        $query->andFilterWhere([
            'like',
            'audit_name',
            $this->audit_name
        ]);
        return $dataProvider;
    }

    public function searchRankAuditsSchedules($params) {
        $params = Yii::$app->request->queryParams;


        $query = new Query();
        // //$query = 'SET @count:=0;';
        $query->select([
                    'tbl_gp_audits_schedules.audit_id',
                    'tbl_gp_audits_schedules.end_date as end_date',
                    'tbl_gp_sections.s_section_name',
                    'tbl_gp_departments.department_name',
                    'tbl_gp_hotels.hotel_name',
                    'tbl_gp_user.first_name as auditor_id',
                    'round(SUM(tbl_gp_answers.answer_score) /(COUNT(tbl_gp_answers.answer_value) * 10 ) * 100) as score'
                ])
                ->from('tbl_gp_audits_schedules')
                ->join('INNER JOIN', 'tbl_gp_audits', 'tbl_gp_audits.audit_id = tbl_gp_audits_schedules.audit_id')
                ->join('INNER JOIN', 'tbl_gp_departments', 'tbl_gp_departments.department_id = tbl_gp_audits.department_id')
                ->join('INNER JOIN', 'tbl_gp_hotels', 'tbl_gp_hotels.hotel_id = tbl_gp_audits.hotel_id')
                ->join('INNER JOIN', 'tbl_gp_user', 'tbl_gp_user.user_id = tbl_gp_audits_schedules.auditor_id')
                ->join('INNER JOIN', 'tbl_gp_audits_checklist_questions', 'tbl_gp_audits_checklist_questions.audit_id = tbl_gp_audits_schedules.audit_schedule_id')
                ->join('INNER JOIN', 'tbl_gp_answers', 'tbl_gp_answers.question_id = tbl_gp_audits_checklist_questions.audits_checklist_questions_id')
                ->join('INNER JOIN', 'tbl_gp_sections', 'tbl_gp_sections.section_id = tbl_gp_audits_checklist_questions.q_section')
                ->andFilterWhere([
                    'IN',
                    'tbl_gp_audits_schedules.status',
                    [
                        3
                    ]
                ])
                ->andFilterWhere([
                    'tbl_gp_answers.not_applicable' => 0
        ]);
        if (isset($params['AuditsSearch']['hotel_id'])) {
            $query->andFilterWhere(['tbl_gp_audits.hotel_id' => $params['AuditsSearch']['hotel_id']]);
        }

        if (isset($params['AuditsSearch']['start_date']) && $params['AuditsSearch']['start_date'] != '' && isset($params['AuditsSearch']['end_date']) && $params['AuditsSearch']['end_date'] != '') {
            $query->andFilterWhere(['>=', 'tbl_gp_audits_schedules.start_date', date('Y-m-d', strtotime($params['AuditsSearch']['start_date']))])
                    ->andFilterWhere(['<', 'tbl_gp_audits_schedules.end_date', date('Y-m-d', strtotime($params['AuditsSearch']['end_date']))]);
        }

        $query->groupBy([
                    'tbl_gp_audits_schedules.audit_id'
                ])
                ->having([
                    '>=',
                    'score',
                    0
                ])
                ->orderBy([
                    'score' => SORT_DESC
        ]);

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Preferences::getPrefValByName('grid_length')
            ]
        ]);

        $query->andFilterWhere([
            'hotel_id' => $this->hotel_id,
            'department_id' => $this->department_id,
            'checklist_id' => $this->checklist_id,
            'user_id' => $this->user_id,
            'deligation_flag' => $this->deligation_flag,
            self::tableName() . '.status' => $this->status,
            'tbl_gp_audits_schedules.is_deleted' => 0,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);

        $this->load($params);
        $query->groupBy([Departments::tableName() . '.department_id']);
        //  echo $query->createCommand()->rawSql;
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }

}
