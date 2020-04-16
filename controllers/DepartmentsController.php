<?php
namespace app\controllers;

use Yii;
use app\components\AccessRule;
use app\models\Departments;
use app\models\search\DepartmentsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Audits;
use app\models\Checklists;
use app\models\Sections;
use app\models\SubSections;
use app\models\HotelDepartments;
use app\models\HotelDepartmentSections;
use app\models\HotelDepartmentSubSections;
use app\models\Tickets;
use app\models\UserDepartments;

/**
 * DepartmentsController implements the CRUD actions for Departments model.
 */
class DepartmentsController extends Controller
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
     * Lists all Departments models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepartmentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single Departments model.
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
     * Creates a new Departments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Departments();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            Yii::$app->session->setFlash('success', "Department created successfully.");
            return $this->redirect([
                '/departments'
            ]);
        } else {
            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    /**
     * Updates an existing Departments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel(Yii::$app->utils->decryptData($id));
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Department updated successfully.");
            return $this->redirect([
                '/departments'
            ]);
        } else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
    }

    /**
     * Deletes an existing Departments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    /**
     * Deletes an existing Departments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        $post = yii::$app->request->post();
        $decryptedDepartment = yii::$app->utils->decryptData($post['deletable_department_id']);
        
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $modelDepartmentWiseSectionCount = Sections::find()->where([
                's_department_id' => $decryptedDepartment,
                'is_deleted' => '0'
            ])->count();
            
            $checkList = Checklists::find()->where([
                'cl_department_id' => $decryptedDepartment,
                'is_deleted' => 0
            ])->count();
            if (! $checkList) {
                
                $audits = Audits::find()->where([
                    'department_id' => $decryptedDepartment,
                    'is_deleted' => 0
                ])
                    ->andWhere([
                    'status' => [
                        3,
                        4
                    ]
                ])
                    ->count();
                
                if (! $audits) {
                    $tickets = Tickets::find()->where([
                        'department_id' => $decryptedDepartment,
                        'is_deleted' => 0
                    ])
                        ->andWhere([
                        'status' => [
                            0,
                            1,
                            2
                        ]
                    ])
                        ->count();
                    if (! $tickets) {
                        if ($modelDepartmentWiseSectionCount == 0) {
                            $modelHotelDepartmentCount = HotelDepartments::find()->where([
                                'department_id' => $decryptedDepartment,
                                'is_deleted' => '0'
                            ])->count();
                            if ($modelHotelDepartmentCount == 0) {
                                $modelDepartmentUpdate = Departments::updateAll([
                                    'is_deleted' => 1,
                                    'modified_by' => \Yii::$app->user->getId()
                                ], 'department_id=' . $decryptedDepartment);
                                if ($modelDepartmentUpdate) {
                                    $transaction->commit();
                                    Yii::$app->session->setFlash('success', 'Department deleted successfully');
                                }
                            } else {
                                Yii::$app->session->setFlash('error', 'Department cannot be deleted as it assigned to hotel.');
                            }
                        } else {
                            Yii::$app->session->setFlash('error', 'Department cannot be deleted as it contains section.');
                        }
                    } else {
                        Yii::$app->session->setFlash('error', 'Department cannot be deleted as it contains tickets.');
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Department cannot be deleted as it contains audits.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Department cannot be deleted as it contains checklists.');
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
     * Finds the Departments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Departments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Departments::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
