<?php

namespace app\controllers;

use app\components\AccessRule;
use app\models;
use app\models\Cities;
use app\models\Departments;
use app\models\Hotels;
use app\models\Locations;
use app\models\Sections;
use app\models\SubSections;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\db\Exception;
use yii\db\Query;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use app\models\Audits;
use app\models\Checklists;
use app\models\HotelDepartments;
use app\models\HotelDepartmentSections;
use app\models\HotelDepartmentSubSections;
use app\models\Questions;
use app\models\Tickets;
use app\models\UserHotels;
use app\models\UserLocations;
use app\models\UserDepartments;
use app\models\User;

class OrganisationController extends Controller {

    public function behaviors() {
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
                    'allow' => Yii::$app->authManager->checkPermissionAccess('organisation'),
                    'actions' => [
                        'index'
                    ],
                    'roles' => [
                        'rbac'
                    ]
                ],
            ]
        ];
        return $behaviors;
    }

    public function actionIndex() {
        $this->layout = 'dashboard_layout';
        return $this->render('index');
    }

    public function actionLoadNewLocation() {
        $locationsModel = new Locations();
        return $this->renderAjax('new_location', [
                    'locationsModel' => $locationsModel
        ]);
    }

    public function actionLoadEditLocation($location_id) {
        $locationsModel = Locations::findOne(yii::$app->utils->decryptSetUp($location_id));
        return $this->renderAjax('new_location', [
                    'locationsModel' => $locationsModel
        ]);
    }

    public function actionLoadDeleteLocation($location_id) {
        $locationsModel = Locations::findOne(yii::$app->utils->decryptSetUp($location_id));
        if ($locationsModel) {
            return $this->renderAjax('delete_node', [
                        'node_id' => $location_id,
                        'node_type' => 'location'
            ]);
        }
    }

    public function actionLoadDeleteHotel($hotel_id) {
        $hotelModel = Hotels::findOne(yii::$app->utils->decryptSetUp($hotel_id));
        if ($hotelModel) {
            return $this->renderAjax('delete_node', [
                        'node_id' => $hotel_id,
                        'node_type' => 'hotel'
            ]);
        }
    }

    public function actionLoadDeleteDepartment($department_id, $hotelId = "") {
        $departmentsModel = Departments::findOne(yii::$app->utils->decryptSetUp($department_id));
        if ($departmentsModel) {
            return $this->renderAjax('delete_node', [
                        'node_id' => $department_id,
                        'hotelId' => $hotelId,
                        'node_type' => 'department'
            ]);
        }
    }

    public function actionLoadDeleteSection($section_id, $hotelId, $departmentId) {
        $sectionModel = HotelDepartmentSections::findOne([
                    'section_id' => yii::$app->utils->decryptSetUp($section_id),
                    'hotel_id' => yii::$app->utils->decryptSetUp($hotelId),
                    'is_deleted' => 0
                        // 'department_id' => yii::$app->utils->decryptSetUp($departmentId)
        ]);
        if ($sectionModel) {
            return $this->renderAjax('delete_node', [
                        'node_id' => $section_id,
                        'node_type' => 'section',
                        'hotelId' => $hotelId,
                        'departmentId' => $departmentId
            ]);
        }
    }

    public function actionLoadDeleteSubsection($subsection_id, $hotel_id = "") {
        $subsectionModel = SubSections::findOne(yii::$app->utils->decryptSetUp($subsection_id));
        if ($subsectionModel) {
            return $this->renderAjax('delete_node', [
                        'node_id' => $subsection_id,
                        'hotelId' => $hotel_id,
                        'node_type' => 'subsection'
            ]);
        }
    }

    public function actionDeleteNode() {
        $output = [];
        if (yii::$app->user->id) {
            $post = yii::$app->request->post();
            if ($post && isset($post['node_type'], $post['node_id']) && $post['node_id']) {
                switch ($post['node_type']) {
                    case "location":
                        $output = $this->deleteLocation($post['node_id']);
                        break;
                    case "hotel":
                        $output = $this->deleteHotel($post['node_id']);
                        break;
                    case "department":
                        $output = $this->deleteDepartment($post['node_id'], $post['hotelId']);
                        break;
                    case "section":
                        $output = $this->deleteSection($post['node_id'], $post['hotelId'], $post['departmentId']);
                        break;
                    case "subsection":
                        $output = $this->deleteSubsection($post['node_id'], $post['hotelId']);
                        break;
                }
            } else {
                $output = [
                    'error' => 'There is a problem on server. Please refresh and try again.'
                ];
            }
        } else {
            $output = [
                'error' => 'Unauthorized or session timed-out.'
            ];
        }
        return json_encode($output);
    }

    /**
     *
     * @param unknown $subsectionId
     * @param unknown $hotelid
     * @return array|string[]|NULL[]
     */
    private function deleteSubsection($subsectionId, $hotelid) {
        $output = [];

        $subsection_id = yii::$app->utils->decryptSetUp($subsectionId);
        $hotelId = yii::$app->utils->decryptSetUp($hotelid);

        $tickets = Tickets::find()->where([
                    'sub_section_id' => $subsection_id,
                    'hotel_id' => $hotelId,
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

        $getHotelDepartment = HotelDepartmentSubSections::getHotelAndDepartment($subsection_id, $hotelId);
        // For getAuditCount depends department and hotel Id
        $audits = Audits::getAuditsDepartmentHotelCount($getHotelDepartment->hotel_id, $getHotelDepartment->department_id, $subsection_id);

        if (!$audits) {

            if (!$tickets) {

                $subsectionHotelDepartmentModel = HotelDepartmentSubSections::find()->where([
                            'sub_section_id' => Yii::$app->utils->decryptSetUp($subsectionId),
                            'hotel_id' => $hotelId,
                            'is_deleted' => 0
                        ])
                        ->one();

                $modelDepartmentSubSections = HotelDepartmentSubSections::updateAll([
                            'is_deleted' => 1,
                            'updated_by' => \Yii::$app->user->getId()
                                ], [
                            'sub_section_id' => $subsection_id,
                            'hotel_id' => $hotelId
                ]);

                if ($modelDepartmentSubSections) {
                    $output = [
                        'success' => 'Sub section deleted successfully',
                        'node' => Yii::$app->utils->encryptSetUp($subsectionHotelDepartmentModel->id, 'subsection')
                    ];
                }
            } else {
                $output = [
                    'error' => 'Subsection cannot be deleted as it contains tickets'
                ];
            }
        } else {
            $output = [
                'error' => 'Subsection cannot be deleted as it contains audit for hotel.'
            ];
        }

        return $output;
    }

    private function deleteSection($section_id, $hotelId, $departmentId) {
        $output = [];
        $sectionId = yii::$app->utils->decryptSetUp($section_id);
        $hotelDepartmentSections = HotelDepartmentSections::findOne([
                    'section_id' => yii::$app->utils->decryptSetUp($section_id),
                    'hotel_id' => yii::$app->utils->decryptSetUp($hotelId),
                    'is_deleted' => 0
        ]);

        if ($hotelDepartmentSections) {
            $transaction = yii::$app->db->beginTransaction();
            try {

                $getHotelDepartment = HotelDepartmentSections::getHotelAndDepartmentDependSection($sectionId, yii::$app->utils->decryptSetUp($hotelId));
                // For getAuditCount depends department and hotel Id
                $audits = '';
                if ($getHotelDepartment) {
                    $audits = Audits::getAuditsDepartmentHotelCount(yii::$app->utils->decryptSetUp($hotelId), $getHotelDepartment->department_id);
                }


                if (!$audits) {

                    $tickets = Tickets::find()->where([
                                'section_id' => $sectionId,
                                'hotel_id' => yii::$app->utils->decryptSetUp($hotelId),
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

                    if (!$tickets) {

                        $modelHotelDepartmentSubSection = HotelDepartmentSubSections::find()->where([
                                    'section_id' => $sectionId,
                                    'hotel_id' => yii::$app->utils->decryptSetUp($hotelId),
                                    'is_deleted' => '0'
                                ])
                                ->count();
                        if ($modelHotelDepartmentSubSection == 0) {
                            $modelSectionUpdate = HotelDepartmentSections::updateAll([
                                        'is_deleted' => 1,
                                        'updated_by' => \Yii::$app->user->getId()
                                            ], 'section_id=' . $sectionId . ' AND hotel_id = ' . yii::$app->utils->decryptSetUp($hotelId));
                            if ($modelSectionUpdate) {
                                $transaction->commit();
                                $output = [
                                    'success' => 'Section Deleted Successfully',
                                    'node' => Yii::$app->utils->encryptSetUp($hotelDepartmentSections->id, 'section')
                                ];
                            }
                        } else {
                            $transaction->rollBack();
                            $output = [
                                'error' => 'Section cannot be deleted as sub sections are assigned to it.'
                            ];
                        }
                    } else {
                        $transaction->rollBack();
                        $output = [
                            'error' => 'Section cannot be deleted as it contains tickets.'
                        ];
                    }
                } else {
                    $transaction->rollBack();
                    $output = [
                        'error' => 'Section cannot be deleted as it contains audits for hotel. '
                    ];
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                $output = [
                    'error' => $e->getMessage()
                ];
            } catch (StaleObjectException $e) {
                $transaction->rollBack();
                $output = [
                    'error' => $e->getMessage()
                ];
            }
        } else {

            $output = [
                'error' => 'Section not found'
            ];
        }

        return $output;
    }

    private function deleteDepartment($departmentId, $hotel_id) {
        $hotelId = yii::$app->utils->decryptSetUp($hotel_id);
        $department_id = yii::$app->utils->decryptSetUp($departmentId);
        $output = [];
        $hotelDepartment = HotelDepartments::findOne([
                    'department_id' => Yii::$app->utils->decryptSetUp($departmentId),
                    'hotel_id' => $hotelId,
                    'is_deleted' => 0
        ]);
        if ($hotelDepartment) {
            $transaction = Yii::$app->db->beginTransaction();
            try {

                $audits = Audits::getAuditsDepartmentHotelCount($hotelId, $department_id);
                if (!$audits) {

                    $modelHotelDepartmentSectionCount = HotelDepartmentSections::find()->where([
                                'department_id' => $department_id,
                                'hotel_id' => $hotelId,
                                'is_deleted' => '0'
                            ])->count();

                    if ($modelHotelDepartmentSectionCount == 0) {

                        $userDepartments = HotelDepartments::find()->joinWith([
                                    'userDepartment',
                                    'userDepartment.user'
                                ])
                                ->where([
                                    'department_id' => $department_id,
                                    'hotel_id' => $hotelId,
                                    User::tableName() . '.is_deleted' => 0
                                ])
                                ->count();
                        if (!$userDepartments) {
                            $modelHotelDepartmentUpdate = HotelDepartments::updateAll([
                                        'is_deleted' => 1,
                                        'updated_by' => \Yii::$app->user->getId()
                                            ], 'department_id=' . $department_id . ' AND hotel_id=' . $hotelId);
                            if ($modelHotelDepartmentUpdate) {
                                $transaction->commit();
                                $output = [
                                    'success' => 'Department Deleted Successfully',
                                    'node' => Yii::$app->utils->encryptSetUp($hotelDepartment->id, 'department')
                                ];
                            }
                        } else {
                            $transaction->rollBack();
                            $output = [
                                'error' => 'Department cannot be deleted as it assigned to user.'
                            ];
                        }
                    } else {
                        $transaction->rollBack();
                        $output = [
                            'error' => 'Department cannot be deleted as section are assigned to it.'
                        ];
                    }
                } else {
                    $transaction->rollBack();
                    $output = [
                        'error' => 'Department cannot be deleted as it contains  audits for hotel.'
                    ];
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                $output = [
                    'error' => $e->getMessage()
                ];
            }
        } else {
            $output = [
                'error' => 'Department not found'
            ];
        }
        return $output;
    }

    private function deleteHotel($hotelId) {
        $output = [];
        $getHotelId = yii::$app->utils->decryptSetUp($hotelId);
        $hotelModel = Hotels::findOne(yii::$app->utils->decryptSetUp($hotelId));
        $transaction = yii::$app->db->beginTransaction();
        if ($hotelModel) {
            try {
                $modelHotelWiseHotelDepartmentCount = HotelDepartments::find()->where([
                            'hotel_id' => $getHotelId,
                            'is_deleted' => '0'
                        ])->count();
                if ($modelHotelWiseHotelDepartmentCount == 0) {
                    $modelHotelWiseUserCount = UserHotels::find()->joinWith([
                                'user'
                            ])
                            ->where([
                                'hotel_id' => $getHotelId,
                                User::tableName() . '.is_deleted' => 0
                            ])
                            ->count();
                    if ($modelHotelWiseUserCount == 0) {

                        $modelHotelUpdate = Hotels::updateAll([
                                    'is_deleted' => 1,
                                    'modified_by' => \Yii::$app->user->getId()
                                        ], 'hotel_id=' . $getHotelId);
                        if ($modelHotelUpdate) {
                            $transaction->commit();
                            $output = [
                                'success' => 'Hotel Deleted Successfully',
                                'node' => $hotelId
                            ];
                        }
                    } else {
                        $transaction->rollBack();
                        $output = [
                            'error' => "Hotel cannot be deleted as it assigned to User"
                        ];
                    }
                } else {
                    $transaction->rollBack();
                    $output = [
                        'error' => "Hotel cannot be deleted as departments are assigned to it."
                    ];
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                $output = [
                    'error' => $e->getMessage()
                ];
            } catch (StaleObjectException $e) {
                $transaction->rollBack();
                $output = [
                    'error' => $e->getMessage()
                ];
            }
        } else {
            $transaction->rollBack();
            $output = [
                'error' => 'Hotel not found'
            ];
        }
        return $output;
    }

    private function deleteLocation($locationId) {
        $output = [];
        $locationId = yii::$app->utils->decryptSetUp($locationId);
        $transaction = yii::$app->db->beginTransaction();
        if ($locationId) {

            try {
                $modelLocationWiseHotelCount = Hotels::find()->where([
                            'location_id' => $locationId,
                            'is_deleted' => 0
                        ])->count();
                if ($modelLocationWiseHotelCount == 0) {

                    $modelLocationWiseUserCount = UserLocations::find()->joinWith([
                                'user'
                            ])
                            ->where([
                                'location_id' => $locationId,
                                User::tableName() . '.is_deleted' => 0
                            ])
                            ->count();

                    if ($modelLocationWiseUserCount == 0) {
                        Locations::updateAll([
                            'is_deleted' => 1,
                            'modified_by' => \Yii::$app->user->getId()
                                ], 'location_id=' . $locationId);
                        $transaction->commit();
                        $output = [
                            'success' => 'Location deleted successfully',
                            'node' => yii::$app->utils->encryptSetUp($locationId, 'location')
                        ];
                    } else {
                        $output = [
                            'error' => 'Location cannot be deleted as it assigned to user',
                            'node' => $locationId
                        ];
                    }
                } else {
                    $output = [
                        'error' => 'Location cannot be deleted as hotels are assigned to it',
                        'node' => $locationId
                    ];
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                $output = [
                    'error' => $e->getMessage()
                ];
            } catch (StaleObjectException $e) {
                $transaction->rollBack();
                $output = [
                    'error' => $e->getMessage()
                ];
            }
        } else {
            $output = [
                'error' => 'Location not found'
            ];
        }
        return $output;
    }

    public function actionLoadNewHotel($location_id = "") {
        if ($location_id) {
            $locationsModel = Locations::findOne(yii::$app->utils->decryptSetUp($location_id));
            if ($locationsModel) {
                $hotelModel = new Hotels();
                return $this->renderAjax('new_hotel', [
                            'locationsModel' => $locationsModel,
                            'hotelModel' => $hotelModel
                ]);
            }
        }
        return $this->renderAjax('//site/error', [
                    'message' => "Invalid request or no location",
                    "name" => 'Server error'
        ]);
    }

    public function actionLoadEditHotel($hotel_id = "") {
        if ($hotel_id) {
            $hotelsModel = Hotels::findOne(yii::$app->utils->decryptSetUp($hotel_id));
            if ($hotelsModel) {
                $locationsModel = Locations::findOne([
                            'location_id' => $hotelsModel->location_id
                ]);
                if ($locationsModel) {
                    return $this->renderAjax('new_hotel', [
                                'locationsModel' => $locationsModel,
                                'hotelModel' => $hotelsModel
                    ]);
                }
            }
        }
        return $this->renderAjax('//site/error', [
                    'message' => "Invalid request or no location",
                    "name" => 'Server error'
        ]);
    }

    public function actionLoadNewDepartment($hotel_id = "") {
        if ($hotel_id) {
            $hotelModel = Hotels::findOne(yii::$app->utils->decryptSetUp($hotel_id));
            if ($hotelModel) {
                $departmentModel = new Departments();
                $hotelDepartmentModel = new HotelDepartments();
                return $this->renderAjax('new_department', [
                            'departmentModel' => $departmentModel,
                            'hotelModel' => $hotelModel,
                            'hotelDepartmentModel' => $hotelDepartmentModel
                ]);
            }
        }
        return $this->renderAjax('//site/error', [
                    'message' => "Invalid request or no Hotel",
                    "name" => 'Server error'
        ]);
    }

    public function actionLoadEditDepartment($department_id = "", $hotelId = "") {
        if ($department_id) {
            $departmentModel = Departments::findOne(yii::$app->utils->decryptSetUp($department_id));
            $hotelDepartmentModel = HotelDepartments::findOne([
                        'department_id' => yii::$app->utils->decryptSetUp($department_id),
                        'hotel_id' => yii::$app->utils->decryptSetUp($hotelId),
                        'is_deleted' => 0,
            ]);
            if ($departmentModel) {
                $hotelModel = Hotels::findOne($hotelDepartmentModel->hotel_id);
                if ($hotelModel) {
                    return $this->renderAjax('new_department', [
                                'hotelDepartmentModel' => $hotelDepartmentModel,
                                'departmentModel' => $departmentModel,
                                'hotelModel' => $hotelModel
                    ]);
                }
            }
        }
        return $this->renderAjax('//site/error', [
                    'message' => "Invalid request or no location",
                    "name" => 'Server error'
        ]);
    }

    public function actionLoadNewSection($department_id = "", $hotelId = "") {
        if ($department_id && $hotelId) {

            $hotelDepartmentModel = HotelDepartments::find()->where([
                        'id' => Yii::$app->utils->decryptSetUp($department_id),
                        'hotel_id' => Yii::$app->utils->decryptSetUp($hotelId),
                        'is_deleted' => 0
                    ])
                    ->one();
            $departmentSectionModel = new HotelDepartmentSections();
            if ($hotelDepartmentModel) {
                $sectionsModel = new Sections();
                return $this->renderAjax('new_section', [
                            'hotelDepartmentModel' => $hotelDepartmentModel,
                            'sectionsModel' => $sectionsModel,
                            'departmentSectionModel' => $departmentSectionModel
                ]);
            }
        }
        return $this->renderAjax('//site/error', [
                    'message' => "Invalid request or no Department",
                    "name" => 'Server error'
        ]);
    }

    public function actionLoadEditSection($section_id = "", $hotelId = '') {
        if ($section_id) {
            $sectionsModel = Sections::findOne(yii::$app->utils->decryptSetUp($section_id));
            $departmentSectionModel = HotelDepartmentSections::findOne([
                        'section_id' => yii::$app->utils->decryptSetUp($section_id),
                        'hotel_id' => yii::$app->utils->decryptSetUp($hotelId),
                        'is_deleted' => 0
            ]);
            if ($departmentSectionModel) {
                $departmentsModel = Departments::findOne($departmentSectionModel->department_id);
                $hotelDepartmentModel = HotelDepartments::findOne([
                            'department_id' => $departmentSectionModel->department_id,
                            'hotel_id' => yii::$app->utils->decryptSetUp($hotelId),
                            'is_deleted' => 0
                ]);

                if ($departmentsModel) {
                    return $this->renderAjax('new_section', [
                                'hotelDepartmentModel' => $hotelDepartmentModel,
                                'departmentSectionModel' => $departmentSectionModel,
                                'departmentsModel' => $departmentsModel,
                                'sectionsModel' => $sectionsModel
                    ]);
                }
            }
        }
        return $this->renderAjax('//site/error', [
                    'message' => "Invalid request or no Department",
                    "name" => 'Server error'
        ]);
    }

    public function actionLoadNewSubsection($section_id = "", $hotelId = "") {
        if ($section_id) {
            $hotelId = (yii::$app->utils->decryptSetUp($hotelId));
            $sectionsModel = HotelDepartmentSections::find()->where([
                        'id' => (yii::$app->utils->decryptSetUp($section_id)),
                        'hotel_id' => $hotelId,
                        'is_deleted' => 0
                    ])
                    ->one();
            if ($sectionsModel) {
                $subSectionsModel = new SubSections();
                $hotelSubSectionsModel = new HotelDepartmentSubSections();
                return $this->renderAjax('new_subsection', [
                            'subSectionsModel' => $subSectionsModel,
                            'sectionsModel' => $sectionsModel,
                            'hotelSubSectionsModel' => $hotelSubSectionsModel
                ]);
            }
        }
        return $this->renderAjax('//site/error', [
                    'message' => "Invalid request or no Section",
                    "name" => 'Server error'
        ]);
    }

    public function actionLoadEditSubsection($subsection_id = "", $hotel_id = "") {
        if ($subsection_id) {
            $subSectionsModel = SubSections::findOne(yii::$app->utils->decryptSetUp($subsection_id));
            if ($subSectionsModel) {

                $sectionsModel = HotelDepartmentSubSections::find()->where([
                            'sub_section_id' => (yii::$app->utils->decryptSetUp($subsection_id)),
                            'hotel_id' => (yii::$app->utils->decryptSetUp($hotel_id)),
                            'is_deleted' => 0
                        ])
                        ->one();
                if ($sectionsModel) {
                    $hotelSubSectionsModel = HotelDepartmentSubSections::find()->where([
                                'sub_section_id' => $subSectionsModel->sub_section_id,
                                'hotel_id' => (yii::$app->utils->decryptSetUp($hotel_id)),
                                'is_deleted' => 0
                            ])->one();
                    return $this->renderAjax('new_subsection_update', [
                                'sectionsModel' => $sectionsModel,
                                'subSectionsModel' => $subSectionsModel,
                                'hotelSubSectionsModel' => $hotelSubSectionsModel
                    ]);
                }
            }
        }
        return $this->renderAjax('//site/error', [
                    'message' => "Invalid request or no Section",
                    "name" => 'Server error'
        ]);
    }

    public function actionManageLocation($location_id = "") {
        $output = [];
        $post = yii::$app->request->post();
        $locationsModel = null;
        if ($location_id) {
            $locationsModel = Locations::findOne(yii::$app->utils->decryptSetUp($location_id));
        }
        if (!$locationsModel) {
            $locationsModel = new Locations();
        }

        if (isset($post['Locations']) && $locationsModel->load($post)) {
            $transaction = yii::$app->db->beginTransaction();
            $locationsModel->created_by = yii::$app->user->id;
            $locationsModel->created_date = date("Y-m-d H:i:s");
            $isNewRecord = $locationsModel->isNewRecord;
            if ($locationsModel->save()) {
                $encryptedLocationId = yii::$app->utils->encryptSetUp($locationsModel->location_id, 'location');
                if ($isNewRecord) {
                    $output = [
                        'success' => 'Location created successfully',
                        'parent_node' => 'root',
                        'node' => [
                            'id' => $encryptedLocationId,
                            "text" => $locationsModel->locationCity->name,
                            "type" => "location",
                            'action_url' => yii::$app->urlManager->createUrl([
                                'organisation/load-new-hotel',
                                'location_id' => $encryptedLocationId
                            ]),
                            'delete_url' => yii::$app->urlManager->createUrl([
                                'organisation/load-delete-location',
                                'location_id' => $encryptedLocationId
                            ]),
                            'edit_url' => yii::$app->urlManager->createUrl([
                                'organisation/load-edit-location',
                                'location_id' => $encryptedLocationId
                            ]),
                            "state" => [
                                "opened" => false, // is the node open
                                "disabled" => false, // is the node disabled
                                "selected" => false // is the node selected
                            ],
                            'children' => false
                        ]
                    ];
                } else {
                    $output = [
                        'success' => 'Location Updated successfully',
                        'node' => [
                            'id' => $encryptedLocationId,
                            "text" => $locationsModel->locationCity->name
                        ]
                    ];
                }

                $transaction->commit();
            } else {
                $transaction->rollBack();
                $output = [
                    'error' => 'Failed to add location'
                ];
            }
        } else {
            $output = [
                'error' => 'Invalid request'
            ];
        }
        return json_encode($output);
    }

    public function actionAjaxValidateLocation() {
        $post = yii::$app->request->post();
        $locationId = (isset($post['locationId']) && $post['locationId']) ? $post['locationId'] : '';
        $locationModel = $locationId ? Locations::find()->where([
                    'location_id' => $locationId
                ])->one() : new Locations();

        if (yii::$app->request->isAjax && $locationModel->load(yii::$app->request->post())) {
            yii::$app->response->format = 'json';
            return ActiveForm::validate($locationModel);
        }
    }

    public function actionManageHotel($hotel_id = '') {
        $post = yii::$app->request->post();
        $output = [];
        $hotelModel = null;
        if (isset($post['encrypted_location_id'])) {
            $locationModel = Locations::findOne(yii::$app->utils->decryptSetUp($post['encrypted_location_id']));
            if ($locationModel) {
                if ($hotel_id) {
                    $hotelModel = Hotels::findOne(yii::$app->utils->decryptSetUp($hotel_id));
                }
                if (!$hotelModel) {
                    $hotelModel = new Hotels();
                }
                if ($hotelModel->load($post)) {
                    $transaction = yii::$app->db->beginTransaction();
                    try {
                        if ($hotelModel->isNewRecord) {
                            $hotelModel->location_id = $locationModel->location_id;
                            $hotelModel->created_by = yii::$app->user->id;
                            $hotelModel->created_date = date("Y-m-d H:i:s");
                            $hotelModel->hotel_status = 1;
                        } else {
                            $hotelModel->modified_by = yii::$app->user->id;
                            $hotelModel->modified_date = date("Y-m-d H:i:s");
                        }
                        $isNewRecord = $hotelModel->isNewRecord;
                        if ($hotelModel->save()) {
                            $transaction->commit();
                            $encryptedHotel = yii::$app->utils->encryptSetUp($hotelModel->hotel_id, 'hotel');
                            if ($isNewRecord) {
                                $output = [
                                    'success' => 'Hotel Added successfully',
                                    'parent_node' => $post['encrypted_location_id'],
                                    'node' => [
                                        'id' => $encryptedHotel,
                                        "text" => $hotelModel->hotel_name,
                                        "type" => "hotel",
                                        'action_url' => yii::$app->urlManager->createUrl([
                                            'organisation/load-new-department',
                                            'hotel_id' => $encryptedHotel
                                        ]),
                                        'delete_url' => yii::$app->urlManager->createUrl([
                                            'organisation/load-delete-hotel',
                                            'hotel_id' => $encryptedHotel
                                        ]),
                                        'edit_url' => yii::$app->urlManager->createUrl([
                                            'organisation/load-edit-hotel',
                                            'hotel_id' => $encryptedHotel
                                        ]),
                                        'clone_url' => yii::$app->urlManager->createUrl([
                                            'organisation/load-clone-hotel',
                                            'hotel_id' => $encryptedHotel
                                        ]),
                                        "state" => [
                                            "opened" => false, // is the node open
                                            "disabled" => false, // is the node disabled
                                            "selected" => false // is the node selected
                                        ],
                                        'children' => false
                                    ]
                                ];
                            } else {
                                $output = [
                                    'success' => 'Hotel Updated Successfully',
                                    'node' => [
                                        'id' => $encryptedHotel,
                                        "text" => $hotelModel->hotel_name
                                    ]
                                ];
                            }
                        } else {
                            $transaction->rollBack();
                            $output = [
                                'error' => $hotelModel->errors
                            ];
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        $output = [
                            'error' => "Failed to save Hotel Details"
                        ];
                    }
                }
            } else {
                $output = [
                    'error' => "Invalid Location"
                ];
            }
        } else {
            $output = [
                'error' => "Location must not be empty"
            ];
        }

        return json_encode($output);
    }

    /**
     *
     * @return string
     */
    public function actionManageNewDepartment() {
        $post = yii::$app->request->post();
        $result = [];
        $result['status'] = false;
        $result['data'] = '';
        $result['error'] = '';
        if ($post) {

            $departmentModel = new Departments();
            if ($departmentModel->load($post)) {
                if ($departmentModel->save()) {
                    $result['status'] = true;
                    $result['data'] = [
                        $departmentModel->department_id => $departmentModel->department_name
                    ];
                } else {
                    $error = [];
                    foreach ($departmentModel->errors as $field => $value) {
                        $error[$field] = implode(',', $value);
                    }
                    $result['error'] = $error;
                }
            } else {
                $result['error'] = "Error in saving";
            }
        }
        return Json::encode($result);
    }

    /**
     *
     * @return string
     */
    public function actionManageNewSubSection() {
        $post = yii::$app->request->post();
        $result = [];
        $result['status'] = false;
        $result['data'] = '';
        $result['error'] = '';
        $sectionId = Yii::$app->utils->decryptSetUp($post['encrypted_section_id']);
        if ($sectionId) {

            $subsection = new SubSections();
            $subsection->ss_section_id = $sectionId;
            if ($subsection->load($post)) {
                if ($subsection->save()) {
                    $result['status'] = true;
                    $result['data'] = [
                        $subsection->sub_section_id => $subsection->ss_subsection_name
                    ];
                } else {
                    $error = [];
                    foreach ($subsection->errors as $field => $value) {
                        $error[$field] = implode(',', $value);
                    }
                    $result['error'] = $error;
                }
            } else {
                $result['error'] = "Error in saving";
            }
        }
        return Json::encode($result);
    }

    public function actionAddSubSection() {
        $post = yii::$app->request->post();
        $output = [];
        $subSectionModel = null;
        if (isset($post['encrypted_section_id'])) {
            $sectionModel = Sections::findOne(Yii::$app->utils->decryptSetUp($post['encrypted_section_id']));
            $hotelId = Yii::$app->utils->decryptSetUp($post['encrypted_hotel_id']);
            $sectionId = Yii::$app->utils->decryptSetUp($post['encrypted_section_id']);
            $subSectionId = $post['HotelDepartmentSubSections']['sub_section_id'];
            if ($hotelId && $sectionId) {
                $subSectionModel = HotelDepartmentSubSections::find()->where([
                            'section_id' => $sectionId,
                            'hotel_id' => $hotelId,
                            'sub_section_id' => $subSectionId,
                            'is_deleted' => 0
                        ])->one();
                $subSectionModel = ($subSectionModel) ? $subSectionModel : new HotelDepartmentSubSections();
                if ($subSectionModel->load($post)) {
                    $subSectionModel->section_id = $sectionId;
                    $subSectionModel->hotel_id = $hotelId;
                    $subSectionModel->department_id = $sectionModel->s_department_id;
                    try {
                        $isNewRecord = $subSectionModel->isNewRecord;
                        if ($subSectionModel->save()) {
                            $encryptedSubsection = Yii::$app->utils->encryptSetUp($subSectionModel->sub_section_id, 'subsection');
                            $encryptedSubsectionRel = Yii::$app->utils->encryptSetUp($subSectionModel->id, 'subsection');
                            $encpHotel = $post['encrypted_hotel_id'];
                            if ($isNewRecord) {
                                $output = [
                                    'success' => "Subsection Added Successfully",
                                    "parent_node" => yii::$app->utils->encryptSetUp($subSectionModel->section_id, 'section'),
                                    'node' => [
                                        'id' => $encryptedSubsectionRel,
                                        "text" => $subSectionModel->subSection->ss_subsection_name,
                                        'type' => "subsection",
                                        'hotelId' => $encpHotel,
                                        'delete_url' => yii::$app->urlManager->createUrl([
                                            'organisation/load-delete-subsection',
                                            'subsection_id' => $encryptedSubsection,
                                            'hotel_id' => $encpHotel
                                        ]),
                                        'edit_url' => yii::$app->urlManager->createUrl([
                                            'organisation/load-edit-subsection',
                                            'subsection_id' => $encryptedSubsection,
                                            'hotel_id' => $encpHotel
                                        ]),
                                        'clone_url' => yii::$app->urlManager->createUrl([
                                            'organisation/load-clone-subsection',
                                            'subsection_id' => $encryptedSubsection
                                        ]),
                                        "state" => [
                                            "opened" => false, // is the node open
                                            "disabled" => false, // is the node disabled
                                            "selected" => false // is the node selected
                                        ],
                                        'children' => false
                                    ]
                                ];
                            } else {
                                $output = [
                                    'success' => "Subsection Updated Successfully",
                                    'node' => [
                                        'id' => $encryptedSubsection,
                                        "text" => $subSectionModel->subSection->ss_subsection_name
                                    ]
                                ];
                            }
                        } else {
                            $output = [
                                'error' => $subSectionModel->errors
                            ];
                        }
                    } catch (Exception $e) {
                        $output = [
                            'error' => "Failed to save Subsection Details"
                        ];
                    }
                }
            } else {
                $output = [
                    'error' => "Invalid Section"
                ];
            }
        } else {
            $output = [
                'error' => "Section must not be empty"
            ];
        }

        return json_encode($output);
    }

    public function actionAddDepartment() {
        $post = Yii::$app->request->post();
        $output = [];

        $hotelId = Yii::$app->utils->decryptSetUp($post['encrypted_hotel_id']);
        $departmentId = $post['HotelDepartments']['department_id'];

        if ($hotelId && $departmentId) {
            if ($departmentId) {
                $hotelDepartmentModel = HotelDepartments::find()->where([
                            'hotel_id' => $hotelId,
                            'department_id' => $departmentId,
                            'is_deleted' => 0
                        ])->one();
            }
            if (!$hotelDepartmentModel) {
                $hotelDepartmentModel = new HotelDepartments();
            }
            if ($hotelDepartmentModel->load($post)) {
                $hotelDepartmentModel->hotel_id = $hotelId;
                try {
                    $isNewRecord = $hotelDepartmentModel->isNewRecord;
                    if ($hotelDepartmentModel->save()) {
                        $encryptedDepartmentId = Yii::$app->utils->encryptSetUp($hotelDepartmentModel->department_id, 'department');
                        $encryptedDepartmentRelId = Yii::$app->utils->encryptSetUp($hotelDepartmentModel->id, 'department');
                        $encryptedHotelId = Yii::$app->utils->encryptSetUp($hotelId, 'hotel');
                        if ($isNewRecord) {
                            $output = [
                                'success' => "Department added successfully",
                                "parent_node" => Yii::$app->utils->encryptSetUp($hotelDepartmentModel->hotel_id, 'hotel'),
                                'node' => [
                                    'id' => $encryptedDepartmentRelId,
                                    'hotelId' => $encryptedHotelId,
                                    "text" => $hotelDepartmentModel->department->department_name,
                                    "type" => "department",
                                    'action_url' => Yii::$app->urlManager->createUrl([
                                        'organisation/load-new-section',
                                        'department_id' => $encryptedDepartmentRelId,
                                        'hotelId' => $encryptedHotelId
                                    ]),
                                    'delete_url' => Yii::$app->urlManager->createUrl([
                                        'organisation/load-delete-department',
                                        'department_id' => $encryptedDepartmentId,
                                        'hotelId' => $encryptedHotelId
                                    ]),
                                    'edit_url' => Yii::$app->urlManager->createUrl([
                                        'organisation/load-edit-department',
                                        'department_id' => $encryptedDepartmentId,
                                        'hotelId' => $encryptedHotelId
                                    ]),
                                    'clone_url' => Yii::$app->urlManager->createUrl([
                                        'organisation/load-clone-department',
                                        'department_id' => $encryptedDepartmentId
                                    ]),
                                    "state" => [
                                        "opened" => false, // is the node open
                                        "disabled" => false, // is the node disabled
                                        "selected" => false // is the node selected
                                    ],
                                    'children' => false
                                ]
                            ];
                        } else {
                            $output = [
                                'success' => "Department Updated Successfully",
                                'node' => [
                                    'id' => $encryptedDepartmentId,
                                    "text" => $hotelDepartmentModel->department->department_name
                                ]
                            ];
                        }
                    } else {
                        $output = [
                            'error' => $hotelDepartmentModel->errors
                        ];
                    }
                } catch (Exception $e) {
                    $output = [
                        'error' => "Failed to save Department Details"
                    ];
                }
            }
        } else {
            $output = [
                'error' => "Invalid Hotel"
            ];
        }

        return json_encode($output);
    }

    public function actionAssignSection() {
        $post = yii::$app->request->post();
        $output = [];
        $sectionModel = null;
        if (isset($post['encrypted_department_id'])) {
            $departmentId = Yii::$app->utils->decryptSetUp($post['encrypted_department_id']);
            $sectionId = $post['HotelDepartmentSections']['section_id'];
            $hotelId = Yii::$app->utils->decryptSetUp($post['encrypted_hotel_id']);
            if ($departmentId && $hotelId) {
                if ($sectionId) {
                    $sectionModel = HotelDepartmentSections::find()->where([
                                'hotel_id' => $hotelId,
                                'department_id' => $departmentId,
                                'section_id' => $sectionId,
                                'is_deleted' => 0
                            ])->one();
                }
                if (!$sectionModel) {
                    $sectionModel = new HotelDepartmentSections();
                }
                if ($sectionModel->load($post)) {
                    try {
                        $sectionModel->department_id = $departmentId;
                        $sectionModel->hotel_id = $hotelId;
                        $isNewRecord = $sectionModel->isNewRecord;
                        if ($sectionModel->save()) {
                            $encryptedSectionid = Yii::$app->utils->encryptSetUp($sectionModel->section_id, 'section');
                            $encryptedSectionRelId = Yii::$app->utils->encryptSetUp($sectionModel->id, 'section');
                            $encryptedHotelId = Yii::$app->utils->encryptSetUp($hotelId, 'hotel');

                            if ($isNewRecord) {
                                $output = [
                                    'success' => "Section Added Successfully",
                                    "parent_node" => yii::$app->utils->encryptSetUp($sectionModel->department_id, 'department'),
                                    'node' => [
                                        'id' => $encryptedSectionRelId,
                                        'hotelId' => $encryptedHotelId,
                                        "text" => $sectionModel->section->s_section_name,
                                        "type" => "section",
                                        'action_url' => Yii::$app->urlManager->createUrl([
                                            'organisation/load-new-subsection',
                                            'section_id' => $encryptedSectionRelId,
                                            'hotelId' => $encryptedHotelId
                                        ]),
                                        'delete_url' => Yii::$app->urlManager->createUrl([
                                            'organisation/load-delete-section',
                                            'section_id' => $encryptedSectionid,
                                            'hotelId' => $encryptedHotelId,
                                            'departmentId' => $post['encrypted_department_id']
                                        ]),
                                        'edit_url' => Yii::$app->urlManager->createUrl([
                                            'organisation/load-edit-section',
                                            'section_id' => $encryptedSectionid,
                                            'hotelId' => $encryptedHotelId,
                                        ]),
                                        'clone_url' => Yii::$app->urlManager->createUrl([
                                            'organisation/load-clone-section',
                                            'section_id' => $encryptedSectionid
                                        ]),
                                        "state" => [
                                            "opened" => false, // is the node open
                                            "disabled" => false, // is the node disabled
                                            "selected" => false // is the node selected
                                        ],
                                        'children' => false
                                    ]
                                ];
                            } else {
                                $output = [
                                    'success' => "Section Updated Successfully",
                                    'node' => [
                                        'id' => $encryptedSectionid,
                                        "text" => $sectionModel->section->s_section_name
                                    ]
                                ];
                            }
                        } else {
                            $output = [
                                'error' => $sectionModel->errors
                            ];
                        }
                    } catch (Exception $e) {
                        $output = [
                            'error' => "Failed to save Section Details"
                        ];
                    }
                }
            } else {
                $output = [
                    'error' => "Invalid Department"
                ];
            }
        } else {
            $output = [
                'error' => "Department must not be empty"
            ];
        }

        return json_encode($output);
    }

    /**
     *
     * @return string
     */
    public function actionCreateNewSection() {
        $post = Yii::$app->request->post();
        $result = [];
        $departmentId = Yii::$app->utils->decryptSetUp($post['encrypted_department_id']);
        if (isset($post['encrypted_department_id'])) {

            $sectionModel = new Sections();
            $sectionModel->s_department_id = $departmentId;
            if ($sectionModel->load($post)) {
                if ($sectionModel->save()) {
                    $result['status'] = true;
                    $result['data'] = [
                        $sectionModel->section_id => $sectionModel->s_section_name
                    ];
                } else {
                    $error = [];
                    foreach ($sectionModel->errors as $field => $value) {
                        $error[$field] = implode(',', $value);
                    }
                    $result['error'] = $error;
                }
            }
        }
        return Json::encode($result);
    }

    public function actionManageDepartment($department_id = '') {
        $post = yii::$app->request->post();
        $output = [];
        if (isset($post['encrypted_hotel_id'])) {
            $hotelId = Yii::$app->utils->decryptSetUp($post['encrypted_hotel_id']);
            $departmentId = Yii::$app->utils->decryptSetUp($department_id);
            $hotelDepartmentModel = Departments::findOne($departmentId);
            if ($hotelDepartmentModel->load($post)) {

                $transaction = yii::$app->db->beginTransaction();
                try {
                    $hotelDeData = HotelDepartments::find()->select([
                                'id',
                                'department_id'
                            ])
                            ->where([
                                'department_id' => $departmentId,
                                'is_deleted' => 0
                            ])
                            ->asArray()
                            ->all();
                    $idLists = ArrayHelper::getColumn($hotelDeData, 'id');
                    $list = [];
                    foreach ($idLists as $id) {
                        $encrptIds = [];
                        $encrptIds['node']['id'] = Yii::$app->utils->encryptSetUp($id, 'department');
                        $encrptIds['node']['text'] = $hotelDepartmentModel->department_name;
                        $list[] = $encrptIds;
                    }
                    $isNewRecord = $hotelDepartmentModel->isNewRecord;
                    if ($hotelDepartmentModel->save()) {
                        // $encryptedDepartmentId = Yii::$app->utils->encryptSetUp($hotelDeData->id, 'department');
                        $transaction->commit();
                        $output = [
                            'success' => "Department Updated Successfully",
                            'nodes' => $list
                        ];
                    } else {
                        $transaction->rollBack();
                        $output = [
                            'error' => $hotelDepartmentModel->errors
                        ];
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                    $output = [
                        'error' => "Failed to save Department Details"
                    ];
                }
            }
        } else {
            $output = [
                'error' => "Hotel must not be empty"
            ];
        }

        return json_encode($output);
    }

    public function actionManageSection($section_id = '') {
        $post = yii::$app->request->post();
        $output = [];
        $sectionModel = null;
        if (isset($post['encrypted_department_id'])) {
            $departmentModel = Departments::findOne(yii::$app->utils->decryptSetUp($post['encrypted_department_id']));
            if ($departmentModel) {
                if ($section_id) {
                    $sectionModel = Sections::findOne(Yii::$app->utils->decryptSetUp($section_id));
                }
                if ($sectionModel->load($post)) {
                    $transaction = yii::$app->db->beginTransaction();
                    try {
                        $isNewRecord = $sectionModel->isNewRecord;
                        if ($sectionModel->save()) {
                            $idsList = HotelDepartmentSections::find()->where([
                                        'section_id' => Yii::$app->utils->decryptSetUp($section_id),
                                        'is_deleted' => 0
                                    ])
                                    ->asArray()
                                    ->all();
                            $idsList = ArrayHelper::getColumn($idsList, 'id');
                            $array = [];
                            foreach ($idsList as $id) {
                                $node = [];
                                $node['node']['id'] = yii::$app->utils->encryptSetUp($id, 'section');
                                $node['node']['text'] = $sectionModel->s_section_name;
                                $array[] = $node;
                            }
                            $output = [
                                'success' => "Section Updated Successfully",
                                'nodes' => $array
                            ];

                            $transaction->commit();
                        } else {
                            $transaction->rollBack();
                            $output = [
                                'error' => $sectionModel->errors
                            ];
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        $output = [
                            'error' => "Failed to save Section Details"
                        ];
                    }
                }
            } else {
                $output = [
                    'error' => "Invalid Department"
                ];
            }
        } else {
            $output = [
                'error' => "Department must not be empty"
            ];
        }

        return json_encode($output);
    }

    public function actionManageSubsection($subsection_id = '') {
        $post = yii::$app->request->post();
        $output = [];
        $subSectionModel = null;
        if (isset($post['encrypted_section_id'])) {
            $sectionModel = Sections::findOne(yii::$app->utils->decryptSetUp($post['encrypted_section_id']));
            if ($sectionModel) {
                if ($subsection_id) {
                    $subSectionModel = SubSections::findOne(yii::$app->utils->decryptSetUp($subsection_id));
                }
                if (!$subSectionModel) {
                    $subSectionModel = new SubSections();
                }
                if ($subSectionModel->load($post)) {
                    $transaction = yii::$app->db->beginTransaction();
                    $subSectionModel->ss_section_id = $sectionModel->section_id;

                    try {
                        if ($subSectionModel->save()) {
                            $encryptedSubsection = yii::$app->utils->encryptSetUp($subSectionModel->sub_section_id, 'subsection');
                            $idsList = HotelDepartmentSubSections::find()->where([
                                        'sub_section_id' => $subSectionModel->sub_section_id,
                                        'is_deleted' => 0
                                    ])
                                    ->asArray()
                                    ->all();
                            $idsList = ArrayHelper::getColumn($idsList, 'id');
                            $array = [];
                            foreach ($idsList as $id) {
                                $node = [];
                                $node['node']['id'] = yii::$app->utils->encryptSetUp($id, 'subsection');
                                $node['node']['text'] = $subSectionModel->ss_subsection_name;
                                $array[] = $node;
                            }

                            $output = [
                                'success' => "Subsection Updated Successfully",
                                'nodes' => $array
                            ];

                            $transaction->commit();
                        } else {
                            $transaction->rollBack();
                            $output = [
                                'error' => $subSectionModel->errors
                            ];
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        $output = [
                            'error' => "Failed to save Subsection Details"
                        ];
                    }
                }
            } else {
                $output = [
                    'error' => "Invalid Section"
                ];
            }
        } else {
            $output = [
                'error' => "Section must not be empty"
            ];
        }

        return json_encode($output);
    }

    public function actionLoadHierarchy() {
        yii::$app->response->format = 'json';
        $output = [];
        $get = yii::$app->request->get();
        if ($get && isset($get['id'])) {
            if ($get['id'] == "#" || $get['id'] == "") { // initial load parent module and getting locations
                $output[] = [
                    "id" => "root",
                    "text" => "Root",
                    'type' => "root",
                    "state" => [
                        "opened" => true, // is the node open
                        "disabled" => false, // is the node disabled
                        "selected" => false // is the node selected
                    ],
                    // "li_attr"=>["data-action"=>yii::$app->urlManager->createUrl('organisation/load-new-location')],
                    'action_url' => yii::$app->urlManager->createUrl('organisation/load-new-location'),
                    'children' => $this->fetchLocationHierarchy()
                ];
            } else if (isset($get['type'])) {
                switch ($get['type']) {
                    case "location":
                        $output = $this->fetchHotelsHierarchy($get['id']); // Getting Hotels for the Location
                        break;
                    case "hotel":
                        $output = $this->fetchDepartmentsHierarchy($get['id']); // Getting deparment for the Hotel
                        break;
                    case "department":
                        $output = $this->fetchSectionsHierarchy($get['id'], $get['hotelId']); // Getting deparment for the Hotel
                        break;
                    case "section":
                        $output = $this->fetchSubsectionsHierarchy($get['id'], $get['hotelId']); // Getting deparment for the Hotel
                        break;
                    default:
                        break;
                }
            }
        }
        return $output;
    }

    private function fetchSubsectionsHierarchy($encryptedSectionId, $hotelId) {
        $subSectionsHierarchy = [];
        $sectionRel = HotelDepartmentSections::findOne(Yii::$app->utils->decryptSetUp($encryptedSectionId));
        $subSections = HotelDepartmentSubSections::find()->where([
                    'section_id' => $sectionRel->section_id,
                    'is_deleted' => 0,
                    'hotel_id' => Yii::$app->utils->decryptSetUp($hotelId)
                ])
                ->all();

        if ($subSections) {
            foreach ($subSections as $subSection) {
                $encryptedSubsectionId = Yii::$app->utils->encryptSetUp($subSection->sub_section_id, "subsection");
                $encryptedSubsectionRelId = Yii::$app->utils->encryptSetUp($subSection->id, "subsection");
                $subSectionsHierarchy[] = [
                    "id" => $encryptedSubsectionRelId,
                    'hotelId' => $hotelId,
                    "type" => "subsection",
                    "text" => $subSection->subSection->ss_subsection_name,
                    'delete_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-delete-subsection',
                        'subsection_id' => $encryptedSubsectionId,
                        'hotel_id' => $hotelId
                    ]),
                    'edit_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-edit-subsection',
                        'subsection_id' => $encryptedSubsectionId,
                        'hotel_id' => $hotelId
                    ]),
                    'clone_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-clone-subsection',
                        'subsection_id' => $encryptedSubsectionId
                    ]),
                    "state" => [
                        "opened" => false, // is the node open
                        "disabled" => false, // is the node disabled
                        "selected" => false // is the node selected
                    ],
                    'children' => false
                ];
            }
        }
        return $subSectionsHierarchy;
    }

    private function fetchSectionsHierarchy($encryptedDepartmentId, $hotelId) {
        $sectionsHierarchy = [];
        $hotelId = Yii::$app->utils->decryptSetUp($hotelId);
        $departmentId = Yii::$app->utils->decryptSetUp($encryptedDepartmentId);
        $hotelDeparts = HotelDepartments::findOne($departmentId);
        $sections = HotelDepartmentSections::find()->where([
                    'hotel_id' => $hotelId,
                    'is_deleted' => 0,
                    'department_id' => $hotelDeparts->department_id
                ])->all();
        // $deps = yii::$app->utils->encryptSetUp($hotelDeparts->department_id);
        if ($sections) {
            foreach ($sections as $section) {
                $encryptedSectionId = yii::$app->utils->encryptSetUp($section->section_id, "section");
                $encryptedSectionRelId = yii::$app->utils->encryptSetUp($section->id, "section");
                $encryptedHotelId = yii::$app->utils->encryptSetUp($hotelId, "hotel");
                $sectionsHierarchy[] = [
                    "id" => $encryptedSectionRelId,
                    "type" => "section",
                    'hotelId' => $encryptedHotelId,
                    "text" => $section->section->s_section_name,
                    'action_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-new-subsection',
                        'section_id' => $encryptedSectionRelId,
                        'hotelId' => $encryptedHotelId
                    ]),
                    'delete_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-delete-section',
                        'section_id' => $encryptedSectionId,
                        'hotelId' => $encryptedHotelId,
                        'departmentId' => $encryptedDepartmentId
                    ]),
                    'edit_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-edit-section',
                        'section_id' => $encryptedSectionId,
                        'hotelId' => $encryptedHotelId,
                    ]),
                    'clone_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-clone-section',
                        'section_id' => $encryptedSectionId
                    ]),
                    "state" => [
                        "opened" => false, // is the node open
                        "disabled" => false, // is the node disabled
                        "selected" => false // is the node selected
                    ],
                    // 'children' => SubSections::find()->where(['ss_section_id' => $section->section_id])->exists()
                    'children' => HotelDepartmentSubSections::find()->where([
                        'section_id' => $section->section_id,
                        'department_id' => $hotelDeparts->department_id,
                        'hotel_id' => $hotelId
                    ])->exists()
                ];
            }
        }
        return $sectionsHierarchy;
    }

    private function fetchDepartmentsHierarchy($encryptedHotelId) {
        $departmentsHierarchy = [];
        $hotelId = Yii::$app->utils->decryptSetUp($encryptedHotelId);
        $departments = HotelDepartments::find()->where([
                    'hotel_id' => $hotelId,
                    'is_deleted' => 0
                ])->all();

        if ($departments) {
            foreach ($departments as $department) {
                $encryptedDepartmentRelId = Yii::$app->utils->encryptSetUp($department->id, "department");
                $encryptedDepartmentId = Yii::$app->utils->encryptSetUp($department->department_id, "department");
                $departmentsHierarchy[] = [
                    "id" => $encryptedDepartmentRelId,
                    "type" => "department",
                    'hotelId' => $encryptedHotelId,
                    'departmentId' => $encryptedDepartmentId,
                    "text" => $department->department->department_name,
                    'configure_email_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-configure-emails',
                        'department_id' => $encryptedDepartmentId,
                        'hotel_id' => $encryptedHotelId
                    ]),
                    'action_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-new-section',
                        'department_id' => $encryptedDepartmentRelId,
                        'hotelId' => $encryptedHotelId
                    ]),
                    'delete_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-delete-department',
                        'department_id' => $encryptedDepartmentId,
                        'hotelId' => $encryptedHotelId
                    ]),
                    'edit_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-edit-department',
                        'department_id' => $encryptedDepartmentId,
                        'hotelId' => $encryptedHotelId
                    ]),
                    'clone_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-clone-department',
                        'department_id' => $encryptedDepartmentId
                    ]),
                    "state" => [
                        "opened" => false, // is the node open
                        "disabled" => false, // is the node disabled
                        "selected" => false // is the node selected
                    ],
                    // 'children' => Sections::find()->where(['s_department_id' => $department->department_id])->exists()
                    'children' => HotelDepartmentSections::find()->where([
                        'hotel_id' => $hotelId,
                        'department_id' => $department->department_id
                    ])->exists()
                ];
            }
        }
        return $departmentsHierarchy;
    }

    private function fetchHotelsHierarchy($encryptedLocationCityId) {
        $hotelsHierarchy = [];
        $hotels = Hotels::findAll([
                    'location_id' => Yii::$app->utils->decryptSetUp($encryptedLocationCityId),
                    'is_deleted' => 0
        ]);
        if ($hotels) {
            foreach ($hotels as $hotel) {
                $encryptedHotelId = yii::$app->utils->encryptSetUp($hotel->hotel_id, 'hotel');
                $hotelsHierarchy[] = [
                    "id" => $encryptedHotelId,
                    "type" => "hotel",
                    "text" => $hotel->hotel_name,
                    'action_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-new-department',
                        'hotel_id' => $encryptedHotelId
                    ]),
                    'delete_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-delete-hotel',
                        'hotel_id' => $encryptedHotelId
                    ]),
                    'edit_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-edit-hotel',
                        'hotel_id' => $encryptedHotelId
                    ]),
                    'clone_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-clone-hotel',
                        'hotel_id' => $encryptedHotelId
                    ]),
                    "state" => [
                        "opened" => false, // is the node open
                        "disabled" => false, // is the node disabled
                        "selected" => false // is the node selected
                    ],
                    // 'children' => Departments::find()->where(['department_hotel_id' => $hotel->hotel_id])->exists()
                    'children' => HotelDepartments::find()->where([
                        'hotel_id' => $hotel->hotel_id
                    ])->exists()
                ];
            }
        }
        return $hotelsHierarchy;
    }

    private function fetchLocationHierarchy() {
        $locations = Locations::find()->where([
                    'is_deleted' => 0
                ])
                ->with("locationCity")
                ->all();
        $locationsHierarchy = [];
        if ($locations) {
            foreach ($locations as $location) {
                $encryptedLocationId = yii::$app->utils->encryptSetUp($location->location_id, 'location');
                $locationsHierarchy[] = [
                    "id" => $encryptedLocationId,
                    "type" => "location",
                    "text" => $location->locationCity->name,
                    'action_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-new-hotel',
                        'location_id' => $encryptedLocationId
                    ]),
                    'edit_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-edit-location',
                        'location_id' => $encryptedLocationId
                    ]),
                    'delete_url' => yii::$app->urlManager->createUrl([
                        'organisation/load-delete-location',
                        'location_id' => $encryptedLocationId
                    ]),
                    "state" => [
                        "opened" => false, // is the node open
                        "disabled" => false, // is the node disabled
                        "selected" => false // is the node selected
                    ],
                    'children' => Hotels::find()->where([
                        'location_id' => $location->location_id
                    ])->exists()
                ];
            }
        }
        return $locationsHierarchy;
    }

    public function actionGetCitiesByState() {
        $output = [];
        $post = yii::$app->request->post();
        if ($post && $post['depdrop_parents']) {
            $output = Cities::find()->select('id,name')
                    ->where([
                        'state_id' => $post['depdrop_parents'][0]
                    ])
                    ->asArray()
                    ->all();
        }
        return json_encode([
            'output' => $output
        ]);
    }

    public function actionLoadCloneHotel($hotel_id = '') {
        $hotelModel = Hotels::findOne(yii::$app->utils->decryptSetUp($hotel_id));
        if ($hotelModel) {
            $locations = Locations::find()->select("location_id,c.name")
                    ->where([
                        'is_deleted' => 0
                    ])
                    ->asArray()
                    ->alias('l')
                    ->join("INNER JOIN", 'tbl_gp_cities c', 'l.location_city_id=c.id')
                    ->all();
            if ($locations) {
                return $this->renderAjax('clone_hotel', [
                            'locations' => $locations,
                            'hotelModel' => $hotelModel
                ]);
            }
        }
    }

    /**
     *
     * @param string $hotel_id
     * @return string
     */
    public function actionSaveClonedHotelInfo($hotel_id = '') {
        $output = [];
        $post = yii::$app->request->post();
        if (Yii::$app->user->id) {
            if ($hotel_id) {
                $hotelModel = Hotels::findOne(yii::$app->utils->decryptSetUp($hotel_id));
                if ($hotelModel) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if ($post && isset($post['selected_locations_list'])) {
                            $departments = HotelDepartments::findAll([
                                        'hotel_id' => $hotelModel->hotel_id,
                                        'is_deleted' => 0
                            ]);
                            foreach ($post['selected_locations_list'] as $selectedLocation) {
                                $newHotelModal = new Hotels();
                                $newHotelModal->attributes = $hotelModel->attributes;
                                $newHotelModal->location_id = $selectedLocation;
                                if ($newHotelModal->save()) {
                                    if ($departments) {
                                        foreach ($departments as $department) {
                                            $newDepartmenModal = new HotelDepartments();
                                            $newDepartmenModal->attributes = $department->attributes;
                                            $newDepartmenModal->hotel_id = $newHotelModal->hotel_id;
                                            if ($newDepartmenModal->save()) {
                                                $sections = HotelDepartmentSections::findAll([
                                                            'department_id' => $department->department_id,
                                                            'hotel_id' => $hotelModel->hotel_id,
                                                            'is_deleted' => 0
                                                ]);
                                                if ($sections) {
                                                    foreach ($sections as $section) {
                                                        $newSectionModel = new HotelDepartmentSections();
                                                        $newSectionModel->attributes = $section->attributes;
                                                        $newSectionModel->hotel_id = $newHotelModal->hotel_id;
                                                        if ($newSectionModel->save()) {
                                                            $subsections = HotelDepartmentSubSections::findAll([
                                                                        'section_id' => $section->section_id,
                                                                        'hotel_id' => $hotelModel->hotel_id,
                                                                        'is_deleted' => 0
                                                            ]);
                                                            if ($subsections) {
                                                                foreach ($subsections as $subsection) {
                                                                    $newSubsectionModel = new HotelDepartmentSubSections();
                                                                    $newSubsectionModel->attributes = $subsection->attributes;
                                                                    $newSubsectionModel->hotel_id = $newHotelModal->hotel_id;
                                                                    if (!$newSubsectionModel->save()) {
                                                                        $transaction->rollBack();
                                                                        $output = [
                                                                            'error' => "Failed to clone Subsection: " . $newSubsectionModel->ss_subsection_name
                                                                        ];
                                                                    }
                                                                }
                                                            }
                                                        } else {
                                                            $transaction->rollBack();
                                                            $output = [
                                                                'error' => "Failed to clone Section: " . $newSectionModel->s_section_name
                                                            ];
                                                        }
                                                    }
                                                }
                                            } else {
                                                $transaction->rollBack();
                                                $output = [
                                                    'error' => "Failed to clone Department: " . $newDepartmenModal->department_name
                                                ];
                                            }
                                        }
                                    }
                                } else {
                                    $transaction->rollBack();
                                    $output = [
                                        'error' => "Failed to clone Hotel: " . $newHotelModal->hotel_name
                                    ];
                                }
                            }
                            if (!isset($output['error'])) {
                                $transaction->commit();
                                $output = [
                                    'success' => "Hotel successfully cloned to selected locations"
                                ];
                            }
                        } else {
                            $output = [
                                'error' => 'Please select locations.'
                            ];
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        $output = [
                            'error' => 'There is a problem on server. Please try again' . $e->getMessage()
                        ];
                    }
                } else {
                    $output = [
                        'error' => 'Hotel not found'
                    ];
                }
            } else {
                $output = [
                    'error' => 'Empty hotel token received'
                ];
            }
        } else {
            $output = [
                'error' => 'Unauthorized. Please login'
            ];
        }

        return json_encode($output);
    }

    public function actionLoadConfigureEmails($department_id = "", $hotel_id = "") {
        if ($department_id && $hotel_id) {
            $hotelDepartmentModel = HotelDepartments::find()->where([
                        'department_id' => Yii::$app->utils->decryptSetUp($department_id),
                        'hotel_id' => Yii::$app->utils->decryptSetUp($hotel_id),
                        'is_deleted' => 0
                    ])
                    ->one();
            if ($hotelDepartmentModel) {
                return $this->renderAjax('configure_dept_email', [
                            'hotelDepartmentModel' => $hotelDepartmentModel
                ]);
            }
        }
        return $this->renderAjax('//site/error', [
                    'message' => "Invalid request or no Department",
                    "name" => 'Server error'
        ]);
    }

    public function actionSaveConfigureEmails($department_id = "", $hotel_id = "") {
        $output = [];
        if ($department_id && $hotel_id) {
            $hotelDepartmentModel = HotelDepartments::find()->where([
                        'department_id' => Yii::$app->utils->decryptSetUp($department_id),
                        'hotel_id' => Yii::$app->utils->decryptSetUp($hotel_id),
                        'is_deleted' => 0
                    ])
                    ->one();
            if ($hotelDepartmentModel && $hotelDepartmentModel->load(yii::$app->request->post())) {
                if ($hotelDepartmentModel->save()) {
                    $output = ['success' => 'Saved successfully'];
                } else {
                    $output = ['error' =>$hotelDepartmentModel->getFirstError('configured_emails')];
                }
            } else {
                $output = ['error' => 'Invalid hotel or department'];
            }
        }
        return json_encode($output);
    }

}
