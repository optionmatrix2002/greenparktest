<?php
namespace app\controllers;

use Yii;
use app\components\AccessRule;
use app\models\Preferences;
use app\models\ProcessCriticalPreferences;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\EmailTemplates;

/**
 * PreferencesController implements the CRUD actions for Preferences model.
 */
class PreferencesController extends Controller
{

    public $layout = 'dashboard_layout';

    /**
     * @inheritdoc
     */

    public function behaviors()
    {
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'ruleConfig' => [
                'class' => AccessRule::className()
            ],
            'only' => [
                'create',
                'update',
                'delete',
                'index'
            ], // only be applied to
            'rules' => [
                [
                    'allow' => true,
                    'actions' => [
                        'create',
                        'delete',
                        'update',
                        'index'
                    ],
                    'roles' => [
                        '@'
                    ]
                ]
            ]
        ];
        return $behaviors;
    }

    /**
     * Lists all Preferences models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        
        $dataProvider = new ActiveDataProvider([
            'query' => Preferences::find()
        ]);

        $query=ProcessCriticalPreferences::find();
        
        $rcadataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
                
        $rcadataProvider->setSort([
            'attributes'=>[
                'module_option','module_id'
            ]
        ]);   
        
       $emailtemplate= EmailTemplates::findOne(['template_id'=>1]);
       
      
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'rcadataProvider'=>$rcadataProvider,
            'emailtemplate'=>$emailtemplate
        ]);
    }
    

    /**
     * Displays a single Preferences model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }
    
    public function actionSaveEmailTemplate($id){
       
            $model =EmailTemplates::findOne(['template_id'=>Yii::$app->utils->decryptData($id)]);
            
            if(\Yii::$app->request->isPost){
                $output = [];
                //print_r(Yii::$app->utils->decryptData($id)); die();
                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    
                    $output = [
                        'success' => 'Updated Successfully'
                    ];
                    
                    //return $this->redirect(['view', 'id' => $model->critical_preference_id]);
                }else{
                    $output = [
                        'error' => $model->getErrorSummary(true)
                    ];
                }
                return json_encode($output);
            }
        
    }

    /**
     * Creates a new Preferences model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Preferences();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([
                'view',
                'id' => $model->preferences_id
            ]);
        } else {
            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    /*
     * For type of preferences
     */
    /*
     * public function getPreferences($prferences_id)
     * {
     * switch($prferences_id)
     * {
     * case 1:
     * $type = "Grid Length";
     * $model->preferences_value = $preferencenewvalue_onedata;
     * break;
     *
     * case 2:
     * $type = "Email Id";
     * $model->preferences_value = $preferencenewvalue_twodata;
     * break;
     *
     * case 3:
     * $type = "Mobile Number";
     * $model->preferences_value = $preferencenewvalue_threedata;
     * break;
     * case 4:
     * $type = "Audit Reminder";
     * $model->preferences_value = json_encode($explode_forthdata);
     * break;
     * case 5:
     * $type = "Event Reminder";
     * $model->preferences_value = json_encode($explode_fifthdata);
     * break;
     * case 6:
     * $type = "Rating Slider";
     * $model->preferences_value = $preferencenewvalue_sixthdata;
     * break;
     * case 7:
     * $type = "Audits Score";
     * $model->preferences_value = $preferencenewvalue_seventhdata;
     * break;
     * case 8:
     * $type = "High priority";
     * $model->preferences_value = $preferencenewvalue_eightdata;
     * break;
     * case 9:
     * $type = "Medium priority";
     * $model->preferences_value = $preferencenewvalue_ninedata;
     * break;
     * case 10:
     * $type = "Low priority";
     * $model->preferences_value = $preferencenewvalue_tendata;
     * break;
     *
     * }
     * return $type; $model->preferences_value;
     * }
     */

    /**
     * Updates an existing Preferences model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $post = yii::$app->request->post();
        $prferences_id = Yii::$app->utils->decryptData($post['update_prferences_id']);
        $preferencenewvalue_onedata = $post['preferencenewvalue_one'];
        $preferencenewvalue_twodata = $post['preferencenewvalue_two'];
        $preferencenewvalue_threedata = $post['preferencenewvalue_three'];
        $preferencenewvalue_fourthdata = $post['preferencenewvalue_fourth'];
        $preferencenewvalue_fifthdata = $post['preferencenewvalue_fifth'];
        $preferencenewvalue_sixthdata = $post['preferencenewvalue_sixth'];
        $preferencenewvalue_seventhdata = $post['preferencenewvalue_seventh'];



        $preferencenewvalue_eightdata = $post['preferencenewvalue_eigth'];
        $preferencenewvalue_ninedata = $post['preferencenewvalue_nine'];
        $preferencenewvalue_tendata = $post['preferencenewvalue_ten'];


        $model = $this->findModel($prferences_id);
        if ($post) {
            switch ($prferences_id) {
                case 1:
                    $type = "Grid Length";
                    $model->preferences_value = $preferencenewvalue_onedata;
                    break;

                case 2:
                    $type = "Email Id";
                    $model->preferences_value = $preferencenewvalue_twodata;
                    break;

                case 3:
                    $type = "Mobile Number";
                    $model->preferences_value = $preferencenewvalue_threedata;
                    break;
                case 4:
                    $type = "Audit Reminder";
                    $model->preferences_value = $preferencenewvalue_fourthdata;


                    break;
                case 5:
                    $type = "Event Reminder";

                    $model->preferences_value = $preferencenewvalue_fifthdata;

                    break;
                case 6:
                    $type = "Rating Slider";
                    $model->preferences_value = $preferencenewvalue_sixthdata;


                    break;
                case 7:
                    $type = "Audits Score";
                    $model->preferences_value = $preferencenewvalue_seventhdata;
                    break;
                case 8:
                    $type = "High priority";
                    $model->preferences_value = $preferencenewvalue_eightdata;
                    break;
                case 9:
                    $type = "Medium priority";
                    $model->preferences_value = $preferencenewvalue_ninedata;
                    break;
                case 10:
                    $type = "Low priority";
                    $model->preferences_value = $preferencenewvalue_tendata;
                    break;
            }

            //echo '<pre>';print_r($model);die;

            if ($model->save()) {
                Yii::$app->session->setFlash('success', $type . " updated successfully");
            } else {


                Yii::$app->session->setFlash('error', "Please enter " . $type);
            }
        }
        return $this->redirect([
            'index'
        ]);
    }

    /**
     * Deletes an existing Preferences model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect([
            'index'
        ]);
    }

    /**
     * Finds the Preferences model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Preferences the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Preferences::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdatepreferences()
    {
        $post = yii::$app->request->post();
    }
    
    
}
