<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Preferences;
use app\models\User;
use app\models\UserDepartments;

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class UserSearch extends User
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'user_id',
                    'is_email_verified'
                ],
                'integer'
            ],
            [
                [
                    'email',
                    'departmentId',
                    'password_hash',
                    'role_id',
                    'is_active',
                    'auth_token',
                    'password_requested_date',
                    'last_login_time',
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
        $query = User::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Preferences::getPrefValByName('grid_length')
            ]
        ]);

        $this->load($params);
        if ($this->departmentId) {
            $query->joinWith('userDepartments.department');
            $query->where([
                \app\models\HotelDepartments::tableName() . '.department_id' => $this->departmentId
            ]);
        }
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'role_id' => $this->role_id,
            'is_email_verified' => $this->is_email_verified,
            'is_active' => $this->is_active,
            'password_requested_date' => $this->password_requested_date,
            'last_login_time' => $this->last_login_time,
            'created_date' => $this->created_date,
            'modified_date' => $this->modified_date
        ]);
        if (Yii::$app->user->identity->user_type != 1) {
            $query->andFilterWhere([
                '!=',
                'user_type',
                1
            ]);
        }


        $query->andFilterWhere([
            '!=',
            self::tableName() . '.is_deleted',
            1
        ]);

        $query->andFilterWhere([
            'like',
            'email',
            $this->email
        ])
            ->andFilterWhere([
                'like',
                'password_hash',
                $this->password_hash
            ])
            ->andFilterWhere([
                'like',
                'auth_token',
                $this->auth_token
            ])
            ->orderBy([
                'user_id' => SORT_DESC
            ]);

        return $dataProvider;
    }
}
