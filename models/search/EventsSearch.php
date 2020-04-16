<?php

namespace app\models\search;

use app\models\Preferences;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Events;

/**
 * EventsSearch represents the model behind the search form about `app\models\Events`.
 */
class EventsSearch extends Events
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by'], 'integer'],
            [['module', 'event_type', 'message', 'created_at', 'updated_at','start_date','end_date'], 'safe'],
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
        $query = Events::find();

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
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'module', $this->module])
             //->andFilterWhere(['like', 'created_at', $this->start_date])
            ->andFilterWhere(['like', 'event_type', $this->event_type])
            ->andFilterWhere(['like', 'message', $this->message])->orderBy([
                'id' => SORT_DESC
            ]);
            
            if(!empty($this->start_date) && !empty($this->end_date))
            {
                
                 $query->andWhere('DATE(tbl_gp_events.created_at) BETWEEN  "'. Yii::$app->formatter->asDate($this->start_date, 'php:Y-m-d') .'" AND "'. Yii::$app->formatter->asDate($this->end_date, 'php:Y-m-d') .'"');
            
            }  
         

        return $dataProvider;
    }
}
