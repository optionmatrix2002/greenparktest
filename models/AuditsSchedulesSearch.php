<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AuditsSchedules;

/**
 * AuditsSchedulesSearch represents the model behind the search form about `app\models\AuditsSchedules`.
 */
class AuditsSchedulesSearch extends AuditsSchedules
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['audit_schedule_id', 'audit_id', 'auditor_id', 'deligation_user_id', 'deligation_status', 'status', 'is_deleted', 'created_by', 'updated_by'], 'integer'],
            [['audit_schedule_name', 'start_date', 'end_date', 'created_at', 'updated_at'], 'safe'],
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
    public function search($params = [], $id = null)
    {

        $auditId = Yii::$app->request->get('id');
        $auditId = $id ? $id : Yii::$app->utils->decryptData($auditId);

        $query = AuditsSchedules::find()->where(['audit_id' => $auditId]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'audit_schedule_id' => $this->audit_schedule_id,
            'audit_id' => $this->audit_id,
            'auditor_id' => $this->auditor_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'deligation_user_id' => $this->deligation_user_id,
            'deligation_status' => $this->deligation_status,
            'status' => $this->status,
            'is_deleted' => 0,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'audit_schedule_name', $this->audit_schedule_name]);

        return $dataProvider;
    }
}
