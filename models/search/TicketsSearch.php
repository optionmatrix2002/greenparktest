<?php

namespace app\models\search;

use app\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Preferences;
use app\models\Tickets;

/**
 * TicketsSearch represents the model behind the search form about `app\models\Tickets`.
 */
class TicketsSearch extends Tickets
{
    /**
     * @inheritdoc
     */
    public $process_critical;
    public $prob_module_id;
    public $root_cause;
    public $improvement_plan;
    public $improve_plan_module_id;
    
    public function rules()
    {
        return [
            [['ticket_id', 'audit_schedule_id', 'hotel_id', 'department_id', 'section_id', 'sub_section_id', 'priority_type_id', 'assigned_user_id', 'answer_id', 'chronicity', 'status', 'is_deleted', 'created_by', 'updated_by','process_critical','prob_module_id','improve_plan_module_id'], 'integer'],
            [['improvement_plan','root_cause'],'string'],
            [['ticket_name', 'ticket_name', 'due_date', 'subject', 'description', 'created_at', 'updated_at', 'dateAssignedType', 'startDate', 'endDate', 'overDueTicket','process_critical','prob_module_id','improve_plan_module_id','improvement_plan','root_cause'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
    public function searchActiveTickets($params)
    {
        $query = Tickets::find()
        ->leftJoin('tbl_gp_answers',"tbl_gp_tickets.answer_id=tbl_gp_answers.answer_id")
        ->leftJoin('tbl_gp_audits_checklist_questions',"tbl_gp_answers.question_id=tbl_gp_audits_checklist_questions.audits_checklist_questions_id")
        ->where(['IN', 'status', [0, 1, 2, 4]]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Preferences::getPrefValByName('grid_length'),
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
            'ticket_id' => $this->ticket_id,
            self::tableName() . '.hotel_id' => $this->hotel_id,
            self::tableName() . '.department_id' => $this->department_id,
            self::tableName() . '.section_id' => $this->section_id,
            'sub_section_id' => $this->sub_section_id,
            'priority_type_id' => $this->priority_type_id,
            'assigned_user_id' => $this->assigned_user_id,
            'answer_id' => $this->answer_id,
            'chronicity' => $this->chronicity,
            'status' => $this->status,
            'is_deleted' => $this->is_deleted,
            self::tableName().'.created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
           
        ]);

        if ($this->audit_schedule_id == -1) {
            $query->andWhere([self::tableName() . '.audit_schedule_id' => NULL]);
        } else {
            $query->andFilterWhere([self::tableName() . '.audit_schedule_id' => $this->audit_schedule_id]);
        }

        if ($this->overDueTicket == 2) {
            $query->andWhere('DATE(tbl_gp_tickets.due_date) <"' . Yii::$app->formatter->asDate(date('Y-m-d'), 'php:Y-m-d') . '"');
        } else if ($this->overDueTicket == 1) {
            $query->andWhere('DATE(tbl_gp_tickets.due_date) >"' . Yii::$app->formatter->asDate(date('Y-m-d'), 'php:Y-m-d') . '"');
        }

        if (!empty($this->due_date)) {
            $query->andFilterWhere(['DATE(due_date)' => Yii::$app->formatter->asDate($this->due_date, 'php:Y-m-d')]);
        }
        if (!empty($this->updated_at)) {
            $query->andFilterWhere(['DATE(updated_at)' => Yii::$app->formatter->asDate($this->updated_at, 'php:Y-m-d')]);
        }


        if (!empty($this->dateAssignedType)) {

            if ($this->dateAssignedType == 1) {
                $query->andWhere('DATE(tbl_gp_tickets.created_at) >="' . Yii::$app->formatter->asDate($this->startDate, 'php:Y-m-d') . '" AND DATE(tbl_gp_tickets.created_at) <="' . Yii::$app->formatter->asDate($this->endDate, 'php:Y-m-d') . '"');
            } else if ($this->dateAssignedType == 2) {
                $query->andWhere('DATE(tbl_gp_tickets.due_date) >="' . Yii::$app->formatter->asDate($this->startDate, 'php:Y-m-d') . '" AND DATE(tbl_gp_tickets.due_date) <="' . Yii::$app->formatter->asDate($this->endDate, 'php:Y-m-d') . '"');
            } else if ($this->dateAssignedType == 3) {
                $query->andWhere('DATE(tbl_gp_tickets.updated_at) >="' . Yii::$app->formatter->asDate($this->startDate, 'php:Y-m-d') . '" AND DATE(tbl_gp_tickets.updated_at) <="' . Yii::$app->formatter->asDate($this->endDate, 'php:Y-m-d') . '"');
            }

        }

        /*$query->andFilterWhere(['like', 'ticket_name', $this->ticket_name])
            ->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'description', $this->description]); */
        $query = User::getUserLocationRelationData($query);
        $query->andFilterWhere([
            'like',
            'ticket_name',
            $this->ticket_name
        ]);
        
        if($this->process_critical!='' && $this->process_critical==0){
            $query->andWhere('tbl_gp_audits_checklist_questions.process_critical = 0 OR tbl_gp_audits_checklist_questions.process_critical is NULL');
        }elseif ($this->process_critical==1){
            $query->andFilterWhere(['tbl_gp_audits_checklist_questions.process_critical' => $this->process_critical]);
        }
        
        $query->andFilterWhere([
            '!=',
            self::tableName() . '.is_deleted',
            1
        ])
            ->orderBy([
                'ticket_id' => SORT_DESC
            ]);

        return $dataProvider;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function addUserCondition($query)
    {

    }

    public function searchArchivedTickets($params)
    {
        $query = Tickets::find()->leftJoin('tbl_gp_answers',"tbl_gp_tickets.answer_id=tbl_gp_answers.answer_id")->leftJoin('tbl_gp_audits_checklist_questions',"tbl_gp_answers.question_id=tbl_gp_audits_checklist_questions.audits_checklist_questions_id")->where(['IN', 'status', [3, 5]]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'ticket_id' => $this->ticket_id,
            self::tableName() . '.hotel_id' => $this->hotel_id,
            self::tableName() . '.department_id' => $this->department_id,
            'section_id' => $this->section_id,
            'sub_section_id' => $this->sub_section_id,
            'priority_type_id' => $this->priority_type_id,
            'assigned_user_id' => $this->assigned_user_id,
            'answer_id' => $this->answer_id,
            'chronicity' => $this->chronicity,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'is_deleted' => $this->is_deleted,
            self::tableName().'.created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
        ]);

        /* $query->andFilterWhere(['like', 'ticket_name', $this->ticket_name])
             ->andFilterWhere(['like', 'subject', $this->subject])
             ->andFilterWhere(['like', 'description', $this->description]); */

        if ($this->audit_schedule_id == -1) {
            $query->andWhere([self::tableName() . '.audit_schedule_id' => NULL]);
        } else {
            $query->andFilterWhere([self::tableName() . '.audit_schedule_id' => $this->audit_schedule_id]);
        }

        if ($this->overDueTicket == 2) {
            $query->andWhere('DATE(tbl_gp_tickets.due_date) <"' . Yii::$app->formatter->asDate(date('Y-m-d'), 'php:Y-m-d') . '"');
        } else if ($this->overDueTicket == 1) {
            $query->andWhere('DATE(tbl_gp_tickets.due_date) >"' . Yii::$app->formatter->asDate(date('Y-m-d'), 'php:Y-m-d') . '"');
        }

        if (!empty($this->due_date)) {
            $query->andFilterWhere(['DATE(due_date)' => Yii::$app->formatter->asDate($this->due_date, 'php:Y-m-d')]);
        }
        if (!empty($this->updated_at)) {
            $query->andFilterWhere(['DATE(updated_at)' => Yii::$app->formatter->asDate($this->updated_at, 'php:Y-m-d')]);
        }

        if (!empty($this->dateAssignedType)) {

            if ($this->dateAssignedType == 1) {
                $query->andWhere('DATE(tbl_gp_tickets.created_at) >="' . Yii::$app->formatter->asDate($this->startDate, 'php:Y-m-d') . '" AND DATE(tbl_gp_tickets.created_at) <="' . Yii::$app->formatter->asDate($this->endDate, 'php:Y-m-d') . '"');
            } else if ($this->dateAssignedType == 2) {
                $query->andWhere('DATE(tbl_gp_tickets.due_date) >="' . Yii::$app->formatter->asDate($this->startDate, 'php:Y-m-d') . '" AND DATE(tbl_gp_tickets.due_date) <="' . Yii::$app->formatter->asDate($this->endDate, 'php:Y-m-d') . '"');
            } else if ($this->dateAssignedType == 3) {
                $query->andWhere('DATE(tbl_gp_tickets.updated_at) >="' . Yii::$app->formatter->asDate($this->startDate, 'php:Y-m-d') . '" AND DATE(tbl_gp_tickets.updated_at) <="' . Yii::$app->formatter->asDate($this->endDate, 'php:Y-m-d') . '"');
            }
        }
        
        if($this->process_critical!='' && $this->process_critical==0){
            $query->andWhere('tbl_gp_audits_checklist_questions.process_critical = 0 OR tbl_gp_audits_checklist_questions.process_critical is NULL');
        }elseif ($this->process_critical==1){
            $query->andFilterWhere(['tbl_gp_audits_checklist_questions.process_critical' => $this->process_critical]);
        }
        
        $query = User::getUserLocationRelationData($query);
        $query->andFilterWhere([
            '!=',
            self::tableName() . '.is_deleted',
            1
        ])
            ->orderBy([
                'ticket_id' => SORT_DESC
            ]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchRecentTickets($params)
    {
        $query = Tickets::find()->where(['IN', 'status', [0, 1, 2, 3, 4]]);
        $this->load($params);
        $query->limit(10);
        $query->andFilterWhere([
            '!=',
            self::tableName() . '.is_deleted',
            1
        ])
            ->orderBy([
                'ticket_id' => SORT_DESC
            ]);

        $return = User::getUserAssingemnts();
        $userHotels = $return['userHotels'];
        $userDepartments = $return['userdepartments'];
        $userType = Yii::$app->user->identity->user_type;

        if ($userType != 1) {
            $query->andFilterWhere(['tbl_gp_tickets.hotel_id' => $userHotels]);
            $query->andFilterWhere(['tbl_gp_tickets.department_id' => $userDepartments]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ]
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }
        return $dataProvider;
    }
}
