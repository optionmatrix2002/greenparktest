<?php
namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Preferences;
use app\models\SubSections;

/**
 * SubSectionsSearch represents the model behind the search form about `app\models\SubSections`.
 */
class SubSectionsSearch extends SubSections
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'sub_section_id',
                    'ss_section_id',
                    'created_by',
                    'modified_by',
                    'is_deleted'
                ],
                'integer'
            ],
            [
                [
                    'ss_subsection_name',
                    'ss_subsection_remarks',
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
        $query = SubSections::find()->where([
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
            'sub_section_id' => $this->sub_section_id,
            'ss_section_id' => $this->ss_section_id,
            'created_by' => $this->created_by,
            'modified_by' => $this->modified_by,
            'created_date' => $this->created_date,
            'modified_date' => $this->modified_date,
            'is_deleted' => $this->is_deleted
        ])->orderBy([
            'sub_section_id' => SORT_DESC
        ]);
        
        $query->andFilterWhere([
            'like',
            'ss_subsection_name',
            $this->ss_subsection_name
        ])->andFilterWhere([
            'like',
            'ss_subsection_remarks',
            $this->ss_subsection_remarks
        ]);
        
        return $dataProvider;
    }
}
