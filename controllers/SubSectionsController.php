<?php
namespace app\controllers;

use Yii;
use app\components\AccessRule;
use app\models\Checklists;
use app\models\SubSections;
use app\models\Tickets;
use app\models\search\SubSectionsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Sections;
use yii\helpers\Json;
use app\models\HotelDepartmentSubSections;

use app\models\Questions;

/**
 * SubSectionsController implements the CRUD actions for SubSections model.
 */
class SubSectionsController extends Controller
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
     * Lists all SubSections models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SubSectionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single SubSections model.
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

    /**
     * Creates a new SubSections model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SubSections([
            'scenario' => 'mastredatasubsection'
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Subsection created successfully.");
            return $this->redirect([
                '/sub-sections'
            ]);
        } else {
            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    /**
     * Updates an existing SubSections model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel(Yii::$app->utils->decryptData($id));
        $model->department_id = $model->ssSection->s_department_id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Subsection updated successfully.");
            return $this->redirect([
                '/sub-sections'
            ]);
        } else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
    }

    /**
     * Deletes an existing SubSections model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        $post = yii::$app->request->post();
        $decryptedSubSection = yii::$app->utils->decryptData($post['deletable_subsection_id']);
        
        //echo '<pre>'; print_r($decryptedSubSection);die;
        
        $questionCount = Questions::find()->joinWith([
            'qChecklist'
        ])
        ->where([
            'q_sub_section' => $decryptedSubSection,
            Questions::tableName() . '.is_deleted' => 0,
            Checklists::tableName() . '.is_deleted' => 0
        ])
        ->count();
        if (!$questionCount) {
            $tickets = Tickets::find()->where([
                'sub_section_id' => $decryptedSubSection,
                'is_deleted' => 0
            ])->andWhere(['status' => [0,1,2]])->count();
            if(!$tickets)
            {
                
                $modelHotelDepartmentSubSection = HotelDepartmentSubSections::find()->where([
                    'sub_section_id' => $decryptedSubSection,
                    'is_deleted' => 0
                ])->count();
                if ($modelHotelDepartmentSubSection != 0) {
                    
                    Yii::$app->session->setFlash('error', "Subsection cannot be deleted as it assigned to hotel");
                } else {
                    $modelSubSection = SubSections::updateAll([
                        'is_deleted' => 1,
                        'modified_by' => \Yii::$app->user->getId()
                    ], 'sub_section_id=' . $decryptedSubSection);
                    if ($modelSubSection) {
                        Yii::$app->session->setFlash('success', "Subsection deleted successfully.");
                    } else {
                        Yii::$app->session->setFlash('error', "Subsection not deleted successfully.");
                    }
                }
                
                
                
            }
            else
            {
                Yii::$app->session->setFlash('error', 'Subsection cannot be deleted as it contains tickets.');
            }
            
        } else {
            Yii::$app->session->setFlash('error', "Subsection cannot be deleted as it contains questions.");
        }
        
        return $this->redirect([
            'index'
        ]);
    }

    /**
     * Finds the SubSections model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return SubSections the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SubSections::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    // action to get the city name from the dropdown
    public function actionSelectSection()
    { // die("IN");
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $department_id = $parents[0];
                header('Content-type: application/json');
                $out = Sections::find()->where([
                    's_department_id' => $department_id,
                    'is_deleted' => '0'
                ])
                    ->select([
                    'id' => 'section_id',
                    'name' => 's_section_name'
                ])
                    ->asArray()
                    ->all();
                echo Json::encode([
                    'output' => $out,
                    'selected' => ''
                ]);
                return;
            }
        }
        echo Json::encode([
            'output' => '',
            'selected' => ''
        ]);
    }
}
