<?php
namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Preferences;
use app\models\Roles;

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class RolesSearch extends Roles
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'role_id',
                ],
                'integer'
            ],
            [
                [
                    'role_name',                    
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
        $query = Roles::find();
        
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
            'role_id' => $this->role_id,
            'role_name' => $this->role_name,
            'is_active' => $this->is_active,           
            'created_date' => $this->created_date,
            'modified_date' => $this->modified_date
        ]);
        $query->andFilterWhere([
            '!=',
            self::tableName().'.is_deleted',
            1
        ]);
        
        $query->andFilterWhere([
            'like',
            'role_name',
            $this->role_name
        ])
        
            ->orderBy([
            'role_id' => SORT_DESC
        ]);
        
        return $dataProvider;
    }
}
