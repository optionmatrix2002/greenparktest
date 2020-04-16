<?php

namespace app\models\search;

use app\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Checklists;
use app\models\Preferences;

/**
 * ChecklistsSearch represents the model behind the search form about `app\models\Checklists`.
 */
class ChecklistsSearch extends Checklists
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'checklist_id',
                    'cl_audit_type',
                    'cl_audit_method',
                    'cl_department_id',
                    'cl_frequency_value',
                    'cl_frequency_duration',
                    'cl_audit_span',
                    'cl_status'
                ],
                'integer'
            ],
            [
                [
                    'cl_name',
                    'checklistname_search'
                ],
                'safe'
            ]
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
    public function search($params)
    {
        $query = Checklists::find();

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
        $query->andFilterWhere([
            'is_deleted' => 0
        ]);
        // grid filtering conditions
        $query->andFilterWhere([
            'checklist_id' => $this->checklist_id,
            'cl_audit_type' => $this->cl_audit_type,
            'cl_audit_method' => $this->cl_audit_method,
            'cl_department_id' => $this->cl_department_id,
            'cl_frequency_value' => $this->cl_frequency_value,
            'cl_frequency_duration' => $this->cl_frequency_duration,
            'cl_audit_span' => $this->cl_audit_span,
            'cl_status' => $this->cl_status
        ])->orderBy([
            'checklist_id' => SORT_DESC
        ]);

        $query = User::getUserLocationRelationData($query,'checklist');
        $query->andFilterWhere([
            'like',
            'cl_name',
            $this->checklistname_search
        ]);
        $query->andFilterWhere([
            'like',
            'cl_name',
            $this->cl_name
        ]);

        return $dataProvider;
    }
}
