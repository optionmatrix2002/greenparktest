<?php
namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Departments;
use app\models\Preferences;

/**
 * DepartmentsSearch represents the model behind the search form about `app\models\Departments`.
 */
class DepartmentsSearch extends Departments
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'department_id',
                    'created_by',
                    'modified_by',
                    'is_deleted'
                ],
                'integer'
            ],
            [
                [
                    'department_name',
                    'department_description',
                    'created_date',
                    'modified_date'
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
        $query = Departments::find()->where([
            'is_deleted' => 0
        ]);
        
        // add conditions that should always apply here
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Preferences::getPrefValByName('grid_length'),
            ]
        ]);
        
        $this->load($params);
        
        if (! $this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        // grid filtering conditions
        $query->andFilterWhere([
            'department_id' => $this->department_id,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'created_date' => $this->created_date,
            'modified_date' => $this->modified_date,
            'is_deleted' => $this->is_deleted
        ])->orderBy([
            'department_id' => SORT_DESC
        ]);
        
        $query->andFilterWhere([
            'like',
            'department_name',
            $this->department_name
        ])->andFilterWhere([
            'like',
            'department_description',
            $this->department_description
        ]);
        
        return $dataProvider;
    }
}
