<?php
namespace app\controllers;

use Yii;
use app\components\AccessRule;
use app\models\Sections;
use app\models\search\SectionsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Checklists;
use app\models\Questions;
use app\models\SubSections;
use app\models\HotelDepartmentSections;
use app\models\HotelDepartmentSubSections;
use app\models\Tickets;

/**
 * SectionsController implements the CRUD actions for Sections model.
 */
class SectionsController extends Controller
{

    public $layout = 'dashboard_layout';

    /**
     * @inheritdoc
     */
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
     * Lists all Sections models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SectionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single Sections model.
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
     * Creates a new Sections model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Sections();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Section created successfully.");
            return $this->redirect([
                '/sections'
            ]);
        } else {
            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    /**
     * Updates an existing Sections model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel(Yii::$app->utils->decryptData($id));
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Section updated successfully.");
            return $this->redirect([
                '/sections'
            ]);
        } else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
    }

    /**
     * Deletes an existing Sections model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */   
    public function actionDelete()
    {
        $post = yii::$app->request->post();
        $decryptedSection = yii::$app->utils->decryptData($post['deletable_section_id']);
        
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $modelSectionWiseSubsectionCount = SubSections::find()->where([
                'ss_section_id' => $decryptedSection,
                'is_deleted' => '0'
            ])->count();
            
            $questionCount = Questions::find()->joinWith([
                'qChecklist'
            ])
            ->where([
                'q_section' => $decryptedSection,
                Questions::tableName() . '.is_deleted' => 0,
                Checklists::tableName() . '.is_deleted' => 0
            ])
            ->count();
            if (! $questionCount) {
                
                
                $tickets = Tickets::find()->where([
                    'section_id' => $decryptedSection,
                    'is_deleted' => 0
                ])->andWhere(['status' => [0,1,2]])->count();
                if(!$tickets)
                {
                    
                    if ($modelSectionWiseSubsectionCount == 0) {
                        $modelHotelDepartmentSection = HotelDepartmentSections::find()->where([
                            'section_id' => $decryptedSection,
                            'is_deleted' => '0'
                        ])->count();
                        if ($modelHotelDepartmentSection == 0) {
                            $modelHotelDepartmentSubSection = HotelDepartmentSubSections::find()->where([
                                'section_id' => $decryptedSection,
                                'is_deleted' => '0'
                            ])->count();
                            if ($modelHotelDepartmentSubSection == 0) {
                                $modelSectionUpdate = Sections::updateAll([
                                    'is_deleted' => 1,
                                    'modified_by' => \Yii::$app->user->getId()
                                ], 'section_id=' . $decryptedSection);
                                if ($modelSectionUpdate) {
                                    $transaction->commit();
                                    Yii::$app->session->setFlash('success', 'Section deleted successfully');
                                }
                            } else {
                                Yii::$app->session->setFlash('error', 'Section cannot be deleted as it assigned to hotel.');
                            }
                        } else {
                            Yii::$app->session->setFlash('error', 'Section cannot be deleted as it assigned to hotel.');
                        }
                    } else {
                        Yii::$app->session->setFlash('error', 'Section cannot be deleted as sub section is assigned.');
                    }
                    
                }
                else
                {
                    Yii::$app->session->setFlash('error', 'Section cannot be deleted as it contains tickets.');
                }
                
                
                
            } else {
                Yii::$app->session->setFlash('error', 'Section cannot be deleted as it contains questions.');
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect([
            'index'
        ]);
    }

    /**
     * Finds the Sections model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Sections the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sections::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
