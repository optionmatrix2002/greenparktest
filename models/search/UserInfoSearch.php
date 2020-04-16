<?php
namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;
use app\models\UserInfo;

/**
 * UserInfoSearch represents the model behind the search form about `app\models\UserInfo`.
 */
class UserInfoSearch extends UserInfo
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'user_info_id',
                    'ui_phone',
                    'ui_is_user_active',
                    'ui_user_id',
                    'ui_user_type_id',
                    'ui_hotel_id',
                    'ui_location_id',
                    'ui_department_id',
                    'ui_role_id'
                ],
                'integer'
            ],
            [
                [
                    'ui_first_name',
                    'ui_last_name'
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
        $query = User::find();
        
        // add conditions that should always apply here
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        
        $this->load($params);
        
        if (! $this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        // grid filtering conditions
        $query->andFilterWhere([
            'phone' => $this->phone,
            'is_active' => $this->is_active,
            'user_id' => $this->user_id,
            'user_type' => $this->user_type,
            // 'ui_hotel_id' => $this->ui_hotel_id,
            // 'ui_location_id' => $this->ui_location_id,
            // 'ui_department_id' => $this->ui_department_id,
            'ui_role_id' => $this->role_id
        ]);
        
        $query->andFilterWhere([
            'like',
            'first_name',
            $this->first_name
        ])
            ->andFilterWhere([
            'like',
            'last_name',
            $this->last_name
        ])
            ->orderBy([
            'user_id' => SORT_DESC
        ]);
        
        return $dataProvider;
    }
}
