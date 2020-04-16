<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\UploadedFile;
use app\components\EmailsComponent;

/**
 * This is the model class for table "{{%answers}}".
 *
 * @property integer $answer_id
 * @property integer $audit_id
 * @property integer $question_id
 * @property integer $not_applicable
 * @property integer $answer_value
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property AnswerAttachments[] $answerAttachments
 * @property AnswerComments[] $answerComments
 * @property AuditsSchedules $audit
 * @property Questions $question
 * @property User $createdBy
 * @property User $updatedBy
 */
class Answers extends \yii\db\ActiveRecord
{
    
    const DEFAULT_ASSIGNEE = '1';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%answers}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'audit_id',
                    'question_id'
                ],
                'required'
            ],
            [
                [
                    'observation_text'
                ],
                'string'
            ],
            [
                [
                    'options_values'
                ],
                'string'
                // 'max' => 100
            ],
            [
                [
                    'audit_id',
                    'question_id',
                    'not_applicable',
                    'created_by',
                    'updated_by'
                ],
                'integer'
            ],
            [
                [
                    'created_at',
                    'updated_at', 'answer_score'
                ],
                'safe'
            ],
            [
                [
                    'audit_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => AuditsSchedules::className(),
                'targetAttribute' => [
                    'audit_id' => 'audit_schedule_id'
                ]
            ],
            [
                [
                    'question_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => AuditsChecklistQuestions::className(),
                'targetAttribute' => [
                    'question_id' => 'audits_checklist_questions_id'
                ]
            ],
            [
                [
                    'created_by'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => [
                    'created_by' => 'user_id'
                ]
            ],
            [
                [
                    'updated_by'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => [
                    'updated_by' => 'user_id'
                ]
            ]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'answer_id' => Yii::t('app', 'Answer ID'),
            'audit_id' => Yii::t('app', 'Audit ID'),
            'question_id' => Yii::t('app', 'Question ID'),
            'not_applicable' => Yii::t('app', 'Not Applicable'),
            'observation_text' => Yii::t('app', 'Observation Text'),
            'options_values' => Yii::t('app', 'Options Values'),
            'answer_value' => Yii::t('app', 'Answer Value'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At')
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // TimestampBehavior also provides a method named touch() that allows you to assign the current timestamp to the specified attribute(s) and save them to the database. For example,
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => date('Y-m-d H:i:s')
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
                'value' => isset(Yii::$app->user) && isset(Yii::$app->user->id) ? Yii::$app->user->id : 1
            ]
        ];
    }
    
    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAnswerAttachments()
    {
        return $this->hasMany(AnswerAttachments::className(), [
            'answer_id' => 'answer_id'
        ]);
    }
    
    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAnswerComments()
    {
        return $this->hasMany(AnswerComments::className(), [
            'answer_id' => 'answer_id'
        ]);
    }
    
    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAudit()
    {
        return $this->hasOne(AuditsSchedules::className(), [
            'audit_schedule_id' => 'audit_id'
        ]);
    }
    
    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(AuditsChecklistQuestions::className(), [
            'audits_checklist_questions_id' => 'question_id'
        ]);
    }
    
    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), [
            'user_id' => 'created_by'
        ]);
    }
    
    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), [
            'user_id' => 'updated_by'
        ]);
    }
    
    public static function getAnswerCount($audit_schedule_id)
    {
        return self::find()->where([
            'answer_value' => 1,
            'audit_id' => $audit_schedule_id,
            'not_applicable' => 0,
        ])->count();
    }
    
    public function saveAnswers($input_answer)
    {
        // changing the status of the Audit Schedules
        if (($update_as = AuditsSchedules::findOne($input_answer['audit_id'])) !== null) {
            $updateAuditStatus = Audits::findOne([
                'audit_id' => $update_as->audit_id
            ]);
            
            $update_as->status = ($input_answer['type'] == 'submit') ? 3 : 2;
            // $updateAuditStatus->status = ($input_answer['type'] == 'submit') ? 3 : 2;
            
            $update_as->save();
            $AuditCount = AuditsSchedules::find()->where([
                'audit_id' => $update_as->audit_id
            ])
            ->andWhere([
                'IN',
                'status',
                [
                    0,
                    1,
                    2
                ]
            ])
            ->count();
            
            if (!$AuditCount) {
                $status = ($input_answer['type'] == 'submit') ? 3 : 2;
                Audits::updateAll([
                    'status' => $status
                ], 'audit_id=' . $update_as->audit_id);
            }
            
            // $updateAuditStatus->save();
        } else {
            throw new HttpException(422, 'Audit schedule not found');
        }
        
        $non_dynamic_checklist_questions = AuditsChecklistQuestions::find()->alias('acq')
        ->innerJoinWith('audit')
        ->where([
            'acq.audit_id' => $input_answer['audit_id'],
            'acq.is_deleted' => 0,
            'acq.q_sub_section_is_dynamic' => 0
        ])
        ->orderBy('acq.question_id ASC')
        ->asArray()
        ->all();
        
        $dynamic_checklist_questions = AuditsChecklistQuestions::find()->alias('acq')
        ->innerJoinWith('audit')
        ->where([
            'acq.audit_id' => $input_answer['audit_id'],
            'acq.is_deleted' => 0,
            'acq.q_sub_section_is_dynamic' => 1
        ])
        ->orderBy('acq.question_id ASC')
        ->asArray()
        ->all();
        
        if (empty($non_dynamic_checklist_questions) && empty($dynamic_checklist_questions)) {
            throw new HttpException(422, 'No questions are found for this audit id');
        } else if (empty($input_answer['non-dynamic']['sections']) && empty($input_answer['dynamic']['sections'])) {
            throw new HttpException(422, 'sections not received');
        }
        
        if (!empty($input_answer['non-dynamic']['sections'])) {
            $non_dynamic_simplified_list = $this->simplify_answer($input_answer['non-dynamic']['sections']);
            // ksort($non_dynamic_simplified_list);
        }
        
        if (!empty($input_answer['dynamic']['sections'])) {
            $dynamic_simplified_list = $this->simplify_answer($input_answer['dynamic']['sections']);
        }
        
        // validation to check if all questions are passed incase of non-dynamic quests
        if ($input_answer['type'] == 'submit') {
            
            if (!empty($non_dynamic_checklist_questions)) {
                if (empty($input_answer['non-dynamic']['sections'])) {
                    throw new HttpException(422, 'Received empty non-dynamic list');
                } else {
                    $needed_ndcQuest = ArrayHelper::getColumn($non_dynamic_checklist_questions, 'audits_checklist_questions_id');
                    $passed_ndcQuest = array_keys($non_dynamic_simplified_list);
                    
                    $needed_dcQuest = ArrayHelper::getColumn($dynamic_checklist_questions, 'audits_checklist_questions_id');
                    $passed_dcQuest = array_keys($dynamic_simplified_list);
                    
                    if (count(array_diff($needed_ndcQuest, $passed_ndcQuest)) != 0) {
                        throw new HttpException(422, 'Non dynamic questions are not matching');
                    } else if (count(array_diff($needed_dcQuest, $passed_dcQuest)) != 0) {
                        throw new HttpException(422, 'Dynamic questions are not matching');
                    }
                }
            }
        }
        
        // saving the non-dynamic list if they are passed
        if (!empty($input_answer['non-dynamic']['sections'])) {
            foreach ($non_dynamic_checklist_questions as $ndcq) {
                
                if (!empty($non_dynamic_simplified_list[$ndcq['audits_checklist_questions_id']][0])) {
                    
                    $saveStatus = $this->saveAnswerWithAttachment($ndcq, $non_dynamic_simplified_list[$ndcq['audits_checklist_questions_id']][0], $ndcq['audits_checklist_questions_id'], $input_answer['type']);
                    
                    if (!$saveStatus['status']) {
                        throw new HttpException(422, $saveStatus['message']);
                    }
                }
            }
        }
        
        // saving the dynamic list if they are passed
        if (!empty($input_answer['dynamic']['sections'])) {
            foreach ($dynamic_checklist_questions as $dcq) {
                if (!empty($dynamic_simplified_list[$dcq['audits_checklist_questions_id']])) {
                    // if first, rename the section_id in the 'tbl_gp_audits_checklist_questions' table instead of duplicating the question
                    $is_first = 1;
                    $update_acq = AuditsChecklistQuestions::findOne($dcq['audits_checklist_questions_id']);
                    $clone = new AuditsChecklistQuestions();
                    
                    foreach ($dynamic_simplified_list[$dcq['audits_checklist_questions_id']] as $sal) {
                        if ($is_first == 1) {
                            $update_acq->q_sub_section = $sal['sub_section_id'];
                            $update_acq->update();
                            
                            $audits_checklist_questions_id = $dcq['audits_checklist_questions_id'];
                        } else {
                            $clone->attributes = $update_acq->attributes;
                            $clone->audits_checklist_questions_id = null;
                            $clone->isNewRecord = true;
                            $clone->save();
                            
                            $audits_checklist_questions_id = Yii::$app->db->getLastInsertID();
                        }
                        
                        $saveStatus = $this->saveAnswerWithAttachment($dcq, $sal, $audits_checklist_questions_id, $input_answer['type']);
                        
                        if (!$saveStatus['status']) {
                            throw new HttpException(422, $saveStatus['message']);
                        }
                        $is_first++;
                    }
                }
            }
        }
        
        return true;
    }
    
    private function simplify_answer($sections)
    {
        $simplified_list = [];
        foreach ($sections as $section) {
            foreach ($section['sub-sections'] as $subSection) {
                foreach ($subSection['questions'] as $question) {
                    $q_details = $question;
                    $q_details['sub_section_id'] = $subSection['sub_section_id'];
                    $q_details['section_id'] = $section['section_id'];
                    $simplified_list[$question['question_id']][] = $q_details;
                }
            }
        }
        return $simplified_list;
    }
    
    /**
     */
    private function validate_answer($answer, $question_type, $options)
    {
        $result = 0;
        if ($question_type == 1 || $question_type == 2) {
            if ($answer[0] == "1" || $answer[0] == 1) {
                $result = 1;
            }
        } else if ($question_type == 3) {
            $benchmarkScore = Preferences::getPrefValByName('rating_siderbar_benchmark');
            if ($answer[0] > "$benchmarkScore" || $answer[0] > $benchmarkScore) {
                $result = 1;
            }
        } else if ($question_type == 4) {
            $un_options = unserialize($options);
            if (!empty($un_options[0]) && $answer[0] == $un_options[0]) {
                $result = 1;
            }
        } else if ($question_type == 5) {
            $un_options = unserialize($options);
            if (!empty($un_options) && count($answer) == count($un_options)) {
                $result = 1;
            }
        }
        return $result;
    }
    
    private function saveAnswerWithAttachment($dbChecklistQuest, $input_answer, $audits_checklist_questions_id)
    {
        if (!empty($input_answer['answer_value'])) {
            $isAnswerValid = $this->validate_answer($input_answer['answer_value'], $dbChecklistQuest['q_response_type'], $dbChecklistQuest['options']);
        } else {
            $isAnswerValid = false;
        }
        
        $answerModel = Answers::find()->where(['audit_id' => $input_answer['audit_id'], 'question_id' => $audits_checklist_questions_id])->one();
        
        if (empty($answerModel)) {
            $answerModel = new Answers();
            $answerModel->answer_id = NULL; // primary key(auto increment id) id
            $answerModel->isNewRecord = true;
        }
        
        $answerModel->audit_id = $dbChecklistQuest['audit_id'];
        $answerModel->question_id = $audits_checklist_questions_id;
        $answerModel->not_applicable = ($input_answer['not_applicable']) ? 1 : 0;
        $answerModel->answer_value = ($input_answer['not_applicable']) ? 1 : $isAnswerValid;
        $answerModel->answer_score = 0;
        if ($isAnswerValid) {
            $answerModel->answer_score = 10;
        }
        if ($dbChecklistQuest['q_response_type'] == 3) {
            $answerModel->answer_score = isset($input_answer['answer_value'][0]) ? $input_answer['answer_value'][0] : 0;
        }
        $answerModel->options_values = serialize($input_answer['answer_value']);
        $answerModel->observation_text = $input_answer['observation_text'];
        
        if ($answerModel->save()) {
            
            //            if (! $answerModel->not_applicable && ! $isAnswerValid && $auditType == 'submit') {
            //                $this->raiseTicket($dbChecklistQuest, $input_answer['sub_section_id'], Yii::$app->db->getLastInsertID(), $dbChecklistQuest['q_text']);
            //            }
            //removing the attachments if any
            $oldAnswerAttachments = AnswerAttachments::findAll(['answer_id' => $answerModel->answer_id]);
            
            //For attachment delete for perticular answer
            AnswerAttachments::deleteAll(['answer_id' => $answerModel->answer_id]);
            
            if (!empty($oldAnswerAttachments)) {
                foreach ($oldAnswerAttachments as $oldAnswerAttachment) {
                    $file_path = \Yii::$app->basePath . Yii::$app->params['attachments_save_url'] . $oldAnswerAttachment->answer_attachment_path;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }
            
            if (!empty($input_answer['attachments'])) {
                foreach ($input_answer['attachments'] as $attachment) {
                    $attachmentSaveStatus = $this->saveAttachment($attachment, $answerModel->answer_id);
                    if (!$attachmentSaveStatus['status']) {
                        return $attachmentSaveStatus;
                    }
                }
            }
            
            return [
                'status' => true,
                'message' => 'Answer saved successfully.'
            ];
        } else {
            return [
                'status' => false,
                'message' => Json::encode($answerModel->getErrors())
                // 'message' => 'Trouble saving the answers. Please try later.'
            ];
        }
    }
    
    private function saveAttachment($attachment, $answer_id)
    {
        $uploadedFile = UploadedFile::getInstanceByName($attachment);
        
        if ($uploadedFile) {
            $ext = pathinfo($uploadedFile->name, PATHINFO_EXTENSION);
            $file_name = $uploadedFile->name;
            $complete_path = \Yii::$app->basePath . Yii::$app->params['attachments_save_url'] . $file_name;
            $path = $file_name;
            if (copy($uploadedFile->tempName, $complete_path)) {
                $answerAttachmentModel = new AnswerAttachments();
                $answerAttachmentModel->answer_attachment_id = NULL; // primary key(auto increment id) id
                $answerAttachmentModel->isNewRecord = true;
                $answerAttachmentModel->answer_id = $answer_id;
                $answerAttachmentModel->answer_attachment_path = $path;
                
                if ($answerAttachmentModel->save()) {
                    return [
                        'status' => true,
                        'message' => 'Attachment saved successfully'
                    ];
                } else {
                    return [
                        'status' => false,
                        'message' => Json::encode($answerAttachmentModel->getErrors())
                        // 'message' => 'Trouble saving the attachments. Please try later.'
                    ];
                }
            } else {
                return [
                    'status' => false,
                    'message' => 'Error saving the attachment'
                ];
            }
        } else {
            return [
                'status' => false,
                'message' => 'Attachment not received'
            ];
        }
    }
    
    private function raiseTicket($auditDetails, $subSectionID, $answerID, $subject = null, $invalid_Answer = '')
    {
        
        $ids = HotelDepartments::find()->joinWith('userDepartment as u')
        ->where([
            'hotel_id' => $auditDetails['audit']['hotel_id'],
            'department_id' => $auditDetails['audit']['department_id'],
        ])
        ->andWhere(['u.is_hod' => 1])
        ->asArray()
        ->all();
        $ids = ArrayHelper::getColumn($ids, 'id');
        $user = UserDepartments::find()->where(['is_hod' => 1, 'hotel_department_id' => $ids])->asArray()->one();
        
        $user_id = '';
        if ($user) {
            $user_id = $user['user_id'];
        }
        
        $ticket = new \app\models\Tickets();
        $ticket->audit_schedule_id = $auditDetails['audit_id'];
        $ticket->location_id = $auditDetails['audit']['location_id'];
        $ticket->hotel_id = $auditDetails['audit']['hotel_id'];
        $ticket->department_id = $auditDetails['audit']['department_id'];
        $ticket->section_id = $auditDetails['q_section'];
        $ticket->sub_section_id = $subSectionID;
        $ticket->priority_type_id = $auditDetails['q_priority_type'];
        $ticket->assigned_user_id = ($user_id) ? $user_id : self::DEFAULT_ASSIGNEE;
        $ticket->answer_id = $answerID;
        //$ticket->ticket_name = \app\models\Tickets::getMaxTicketNumber();
        $ticket->ticket_name = "TKT000";
        $ticket->chronicity = $this->getQuestionChronicity($answerID);
        $ticket->due_date = $this->getPriorityBasedDueDate($auditDetails['q_priority_type']);
        $ticket->subject = $subject;
        $ticket->description = $invalid_Answer ? $invalid_Answer->observation_text : '';
        $ticket->status = 1;
        $ticket->is_deleted = 0;
        
        if (!$ticket->save()) {
            // throw new HttpException(422,  Json::encode($ticket->getErrors()));
            return [
                'status' => false,
                'message' => Json::encode($ticket->getErrors())
                
            ];
        }
        $ticket->ticket_name = $ticket->ticket_name . $ticket->ticket_id;
        
        if ($invalid_Answer) {
            $answerAttachments = $invalid_Answer->answerAttachments;
            $ticketAttachments = new TicketAttachments();
            foreach ($answerAttachments as $attachment) {
                $ticketAttachments->ticket_attachment_id = NULL; // primary key(auto increment id) id
                $ticketAttachments->isNewRecord = true;
                $ticketAttachments->ticket_id = $ticket->ticket_id;
                $ticketAttachments->ticket_attachment_path = $attachment->answer_attachment_path;
                if (!$ticketAttachments->save()) {
                    throw new HttpException(422, Json::encode($ticketAttachments->errors));
                }
            }
        }
        
        Tickets::updateAll([
            'ticket_name' => $ticket->ticket_name
        ], 'ticket_id=' . $ticket->ticket_id);
        
        
        $ticketHistory = new TicketHistory();
        $ticketHistory->ticket_id = $ticket->ticket_id;
        $userName = Tickets::getUserName($ticket->assigned_user_id);
        $userNameData = $userName ? $userName->first_name . ' ' . ($userName->last_name) : '';
        $message = "Ticket created and assigned to " . $userNameData;
        $ticketHistory->ticket_message = $message;
        if (!$ticketHistory->save()) {
            
            return [
                'status' => false,
                'message' => Json::encode($ticketHistory->getErrors())
                
            ];
        }
        
        
        /*
         * Email to department mail ids if process critical is true
         */
        /*if($auditDetails['process_critical']==1 || $ticket->chronicity==1){
            $emails= Departments::findOne(['department_id'=>$ticket->department_id]);
            if(!empty($emails->department_email)){
                $resp= EmailsComponent::sendNonComplaintToDepartment(explode(',', $emails->department_email), $ticket->ticket_name, $user_id);
            }
        }*/
    }
    
    /**
     *
     * @param unknown $answerId
     * @param unknown $ticket
     */
    public static function getQuestionChronicity($answerId)
    {
        $answerModel = self::findOne($answerId);
        $chronicity = 0;
        if ($answerModel) {
            $questionId = $answerModel->question_id;
            $questionModel = AuditsChecklistQuestions::find()->where([
                'audits_checklist_questions_id' => $questionId
            ])
            ->asArray()
            ->one();
            
            if ($questionModel) {
                
                $scheduledAuidtIds = self::getAllScheduledAudits($questionModel);
                
                $questionsList = AuditsChecklistQuestions::find()->where([
                    'q_text' => $questionModel['q_text'],
                    'q_section' => $questionModel['q_section'],
                    'q_sub_section' => $questionModel['q_sub_section'],
                    'audit_id' => $scheduledAuidtIds
                ])
                ->andWhere([
                    '!=',
                    'audits_checklist_questions_id',
                    $questionId
                ])
                ->asArray()
                ->all();
                
                $questionsList = ArrayHelper::index($questionsList, 'audits_checklist_questions_id');
                
                if ($questionsList) {
                    $questionsList = array_filter($questionsList, function ($element) use ($questionId) {
                        if ($element['audits_checklist_questions_id'] < $questionId) {
                            return true;
                        }
                    });
                        if ($questionsList) {
                            $maxQuestionId = max(array_keys($questionsList));
                            
                            $tickets = self::find()->joinWith([
                                'answerTicket'
                            ], true, 'INNER JOIN ')
                            ->where([
                                'question_id' => $maxQuestionId
                            ])
                            ->asArray()
                            ->one();
                            
                            if ($tickets) {
                                $chronicity = 1;
                            }
                        }
                }
            }
        }
        return $chronicity;
    }
    
    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAnswerTicket()
    {
        return $this->hasOne(Tickets::className(), [
            'answer_id' => 'answer_id'
        ]);
    }
    
    /**
     *
     * @param unknown $questionModel
     */
    public static function getAllScheduledAudits($questionModel)
    {
        $aduit = AuditsSchedules::findOne($questionModel['audit_id']);
        
        $scheduledAudits = AuditsSchedules::find()->select([
            'audit_schedule_id'
        ])
        ->where([
            'audit_id' => $aduit->audit_id
        ])
        ->asArray()
        ->all();
        
        $scheduledAudits = ArrayHelper::getColumn($scheduledAudits, 'audit_schedule_id');
        $scheduledAudits = $scheduledAudits ? $scheduledAudits : [];
        return $scheduledAudits;
    }
    
    
    public function saveAnswer($input_answer)
    {
        // changing the status of the Audit Schedule as draft if new input is received
        if (($update_as = AuditsSchedules::findOne($input_answer['audit_id'])) !== null) {
            
            $update_as->status = 2;
            if (!$update_as->save()) {
                throw new HttpException(422, Json::encode($update_as->errors));
            }
            
            
            //changing the status of the parent audit also
            Audits::updateAll([
                'status' => 0
            ], 'audit_id=' . $update_as->audit_id);
            
            //check if question_id is present in AuditsChecklistQuestions
            $audits_checklist_model = AuditsChecklistQuestions::findOne($input_answer['question_id']);
            
            if (empty($audits_checklist_model)) {
                throw new HttpException(422, 'Question with QID:' . $input_answer['question_id'] . ' AND AID:' . $input_answer['audit_id'] . ' not found');
            } else {
                //if a dynamic subsection is passed
                if ($input_answer['is_dynamic_subsection']) {
                    $dynamicSubSection = isset($input_answer['sub_section_id']) ? $input_answer['sub_section_id'] : '';
                    $questions_model = AuditsChecklistQuestions::find()->where(['audit_id' => $input_answer['audit_id'], 'q_section' => $input_answer['section_id'], 'q_sub_section' => $dynamicSubSection, 'question_id' => $audits_checklist_model->question_id])->one();
                    
                    //if a dynamic subsection is not found, clone it with previous one
                    if (empty($questions_model)) {
                        $questions_model = new AuditsChecklistQuestions();
                        $questions_model->attributes = $audits_checklist_model->attributes;
                        $questions_model->q_sub_section = $dynamicSubSection;
                        $questions_model->q_sub_section_is_dynamic = 1;
                        $questions_model->audits_checklist_questions_id = null;
                        $questions_model->isNewRecord = true;
                        $questions_model->save();
                        
                        $audits_checklist_questions_id = Yii::$app->db->getLastInsertID();
                    } else {
                        $audits_checklist_questions_id = $questions_model->audits_checklist_questions_id;
                    }
                    //saving the answer model
                    $saveStatus = $this->saveAnswerWithAttachment($questions_model, $input_answer, $audits_checklist_questions_id);
                } else {
                    //saving the answer model
                    $saveStatus = $this->saveAnswerWithAttachment($audits_checklist_model, $input_answer, $input_answer['question_id']);
                }
                
                if (!$saveStatus['status']) {
                    throw new HttpException(422, $saveStatus['message']);
                }
            }
            
            return true;
        } else {
            throw new HttpException(422, 'Audit schedule not found');
        }
    }
    
    /**
     * Changing the status of the audit to complete
     * @param type $input_answer
     * @return boolean
     * @throws HttpException
     */
    public function changeToComplete($input_answer)
    {
        // changing the status of the Audit Schedule as draft if new input is received
        if (($update_as = AuditsSchedules::findOne($input_answer['audit_id'])) !== null) {
            
            $questions_count = AuditsChecklistQuestions::find()->where(['audit_id' => $input_answer['audit_id'], 'is_deleted' => 0])->count();
            
            $answers_count = Answers::find()->where(['audit_id' => $input_answer['audit_id']])->count();
            
            /*if ($questions_count !== $answers_count && $questions_count < $answers_count) {
             throw new HttpException(422, "All questions haven't been answered for the AID: " . $input_answer['audit_id']);
             } else {*/
            
            $invalid_Answers = Answers::find()
            ->andWhere(['audit_id' => $input_answer['audit_id']])
            ->andWhere(['answer_value' => 0])->all();
            
            if (!empty($invalid_Answers)) {
                
                foreach ($invalid_Answers as $invalid_Answer) {
                    $quest_details = AuditsChecklistQuestions::find()->alias('acq')
                    ->innerJoinWith('audit')
                    ->where([
                        'acq.audit_id' => $input_answer['audit_id'],
                        'acq.audits_checklist_questions_id' => $invalid_Answer->question_id
                    ])
                    ->asArray()
                    ->one();
                    
                    if (!empty($quest_details)) {
                        if ($quest_details['is_deleted'] == 0) {
                            $this->raiseTicket($quest_details, $quest_details['q_sub_section'], $invalid_Answer->answer_id, $quest_details['q_text'], $invalid_Answer);
                        }
                    }
                }
            }
            // }
            $update_as->status = 3;
            $update_as->save();
            
            
            //changing the status of the parent audit also, if all the audit schedules are completed
            $AuditCount = AuditsSchedules::find()->where([
                'audit_id' => $update_as->audit_id
            ])
            ->andWhere([
                'IN',
                'status',
                [
                    0,
                    1,
                    2
                ]
            ])
            ->count();
            
            
            if (!$AuditCount) {
                Audits::updateAll([
                    'status' => 3
                ], 'audit_id=' . $update_as->audit_id);
            }
            
            return true;
        } else {
            throw new HttpException(422, 'Audit schedule not found');
        }
    }
    
    /**
     * @param $priority
     */
    protected function getPriorityBasedDueDate($priority)
    {
        
        $priorityName = ['1' => 'ticket_high_priority_due_date', '2' => 'ticket_medium_priority_due_date', '3' => 'ticket_low_priority_due_date'];
        $priority = isset($priorityName[$priority]) ? $priorityName[$priority] : 0;
        $defaultDueDate = date('Y-m-d H:i:s');
        if ($priority) {
            $dueDays = Preferences::getPrefValByName($priority);
            if ($dueDays) {
                $defaultDueDate = date('Y-m-d H:i:s', strtotime('+' . $dueDays . ' days'));
            }
        }
        return $defaultDueDate;
    }
}
