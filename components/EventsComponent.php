<?php

namespace app\components;


use app\models\Events;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class EventsComponent extends Component
{
    public function createEvent($data)
    {
        $model = new Events();
        $model->module = $data['module'];
        $model->event_type = $data['type'];
        $model->message = $data['message'];
        $model->save();
        /*if($model->save()){
            echo 'success';
        }else{
            echo '<pre>';
            print_r($model);
           echo 'failded';
        }*/
    }

}

?>