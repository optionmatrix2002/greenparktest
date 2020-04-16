<?php
namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Preferences;
use app\models\Sections;

/**
 * SectionsSearch represents the model behind the search form about `app\models\Sections`.
 */
class SectionsSearch extends Sections
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'section_id',
                    's_department_id',
                    'created_by',
                    'modified_by',
                    'is_deleted'
                ],
                'integer'
            ],
            [
                [
                    's_section_name',
                    's_section_remarks',
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
        $query = Sections::find()->where([
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
            'section_id' => $this->section_id,
            's_department_id' => $this->s_department_id,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'created_date' => $this->created_date,
            'modified_date' => $this->modified_date,
            'is_deleted' => $this->is_deleted
        ])->orderBy([
            'section_id' => SORT_DESC
        ]);
        
        $query->andFilterWhere([
            'like',
            's_section_name',
            $this->s_section_name
        ])->andFilterWhere([
            'like',
            's_section_remarks',
            $this->s_section_remarks
        ]);
        
        return $dataProvider;
    }
}
