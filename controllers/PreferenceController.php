<?php

namespace app\controllers;

use Yii;
use app\models\ProcessCriticalPreferences;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PreferenceController implements the CRUD actions for ProcessCriticalPreferences model.
 */
class PreferenceController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    public $layout = 'dashboard_layout';

    /**
     * Lists all ProcessCriticalPreferences models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ProcessCriticalPreferences::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates a new ProcessCriticalPreferences model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProcessCriticalPreferences();

        if (\Yii::$app->request->isPost){
            
            $output = [];
            
            $model->created_by=Yii::$app->user->id;
            $model->created_at=date('Y-m-d h:i:s');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $output = [
                'success' => 'Saved Successfully'
            ];
        }else{
            $output = [
                'error' => $model->getFirstError('module_option')
            ];
        }
        return json_encode($output);
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProcessCriticalPreferences model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    { //    $this->layout=false;
        $model = $this->findModel(Yii::$app->utils->decryptData($id));

        if(\Yii::$app->request->isPost){
            $output = [];
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
           
            $output = [
                'success' => 'Updated Successfully'
            ];
            
            //return $this->redirect(['view', 'id' => $model->critical_preference_id]);
        }else{
            $output = [
                'error' => $model->getFirstError('module_option')
            ];
        }
        return json_encode($output);
        }

        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }
    
    
    public function actionManageReminderEscalation($id)
    { //    $this->layout=false;
        $model = $this->findModel(Yii::$app->utils->decryptData($id));
        
        if(\Yii::$app->request->isPost){
            $output = [];
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                
                $output = [
                    'success' => 'Preference Updated Successfully'
                ];
                
                //return $this->redirect(['view', 'id' => $model->critical_preference_id]);
            }else{
                $output = [
                    'error' => $model->getFirstError('module_option')
                ];
            }
            return json_encode($output);
        }
        
    }

    /**
     * Deletes an existing ProcessCriticalPreferences model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete()
    {
        if(\Yii::$app->request->isPost && \Yii::$app->request->post('id')!=''){
        
            $output = [];
        $id=\Yii::$app->request->post('id');
        try {
            
        
        if ($this->findModel(Yii::$app->utils->decryptData($id))->delete()){
            
            $output = [
                'success' => 'Preference Deleted Successfully'
            ];
        }else{
            $output = [
                'error' => 'Unable to delete as the option is associated with a ticket'
            ];
        }
        
        } catch (\Exception $e) {
            
            $output = [
                'error' => 'Unable to delete as the option is associated with a ticket'
            ];
        }

        return json_encode($output);
        }
    }

    /**
     * Finds the ProcessCriticalPreferences model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProcessCriticalPreferences the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProcessCriticalPreferences::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
