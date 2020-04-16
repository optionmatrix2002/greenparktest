<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_gp_errorlogs".
 *
 * @property string $id
 * @property integer $level
 * @property string $category
 * @property string $log_time
 * @property string $prefix
 * @property string $message
 * @property string $description
 */
class Errorlogs extends \yii\db\ActiveRecord
{
    
   public $start_date;
   
   public $end_date;
    
   /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_gp_errorlogs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['level'], 'integer'],
            [['prefix', 'message', 'description'], 'string'],
            [['description'], 'required'],
            [['category'], 'string', 'max' => 255],
            [['log_time'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level' => 'Level',
            'category' => 'Category',
            'log_time' => 'Log Time',
            'prefix' => 'Prefix',
            'message' => 'Message',
            'description' => 'Description',
        ];
    }
}
