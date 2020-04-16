<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Errorlogs;
use app\models\Preferences;

/**
 * ErrorlogsSearch represents the model behind the search form about `app\models\Errorlogs`.
 */
class ErrorlogsSearch extends Errorlogs
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'level'], 'integer'],
            [['category', 'log_time', 'prefix', 'message', 'description','start_date','end_date'], 'safe'],
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
        $query = Errorlogs::find();

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
            'id' => $this->id,
            'level' => $this->level,
        ]);

        $query->andFilterWhere(['like', 'category', $this->category])
           ->andFilterWhere(['like', 'prefix', $this->prefix])
            ->andFilterWhere(['like', 'message', $this->message])
            ->andFilterWhere(['like', 'description', $this->description]);
        
              $query->orderBy([
                'id' => SORT_DESC
            ]);
              
              
        if(!empty($this->start_date) && !empty($this->end_date))
        {
             $query->andWhere('tbl_gp_errorlogs.log_time BETWEEN "'. $this->start_date .'" AND "'. $this->end_date .'"');
        }  
      
        return $dataProvider;
    }
}
