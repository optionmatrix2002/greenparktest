<?php
/* @var $this View */

/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use SebastianBergmann\CodeCoverage\Report\PHP;
use app\models\Audits;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

$this->title = $model->ticket_name . ' Ticket Report';

AppAsset::register($this);
View::registerJsFile(yii::$app->urlManager->createUrl('js/ticketReports.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/modelpopup.js'), [
    'depends' => JqueryAsset::className()
]);
View::registerCssFile(yii::$app->urlManager->createUrl('css/questionnaire.css'));

AppAsset::register($this);
View::registerCssFile('//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
View::registerJsFile(yii::$app->urlManager->createUrl('js/audits.js'), [
    'depends' => JqueryAsset::className()
]);

$this->registerJs('
$(".nav-bids").removeClass("active");
$("#tickets").addClass("active");
', \yii\web\View::POS_END);
?>

<div class="container-fluid">
    <h2> Ticket Report : <?= $model->ticket_name ?></h2>
</div>
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed"> <span
            class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
    <?php if ($model->status == 3 || $model->status == 4) { ?>
        Complete details and attachments of a ticket can be view here.
    <?php } else { ?>
        <p>Complete details and attachments of a ticket can be
            managed from here.</p>
    <?php } ?>
</div>
<div class="col-sm-12 col-lg-12 col-md-12">
    <div class="row">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a class="tabs" data-toggle="tab"
                                  href="#description"><i class="fa fa-server" aria-hidden="true"></i>&nbsp;Description</a></li>
            <li><a class="tabs" data-toggle="tab" href="#details"><i
                        class="fa fa-th-list" aria-hidden="true"></i>&nbsp;Details</a></li>

            <li><a class="tabs" data-toggle="tab" href="#attachments"><i
                        class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;Attachments
                    (<?= $modelTicketsAttachmentsCount ?>)</a></li>
            <li><a class="tabs" data-toggle="tab" href="#history"><i
                        class="fa fa-history" aria-hidden="true"></i>&nbsp;History</a></li>
                <?php if ($model->chronicity == 1 OR $model->audit_schedule_id == null) { ?>
                <li><a class="tabs" data-toggle="tab" href="#processCritical"><i
                            class="fa fa-history" aria-hidden="true"></i>&nbsp;Root Cause
                        Analysis</a></li>
            <?php } ?>		
            <li class="pull-right backbutton"
                style="margin-bottom: 2px; border: 1px solid #ccc"><a
                    href="<?= yii::$app->urlManager->createUrl('tickets'); ?>"
                    class="btn btn-default"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
            </li>
        </ul>
        <div class="tab-content">
            <div id="details" class="tab-pane fade ">
                <div class="content">
                    <div class="col-sm-12 col-lg-12 col-md-12">
                        <div class="col-sm-3 col-md-3 col-lg-3 nopadding">
                            <label>Ticket Number </label>
                        </div>
                        <div class="col-sm-1 col-lg-1 col-md-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-4 col-lg-3 col-md-3 nopadding">
                            <label><?= $model->ticket_name ?></label>
                        </div>
                        <?php if ($model->status == 3 || $model->status == 5) { ?>

                        <?php } else { ?>
                            <?php if (Yii::$app->authManager->checkPermissionAccess('tickets/update')) { ?>
                                <div class="col-sm-4 nopadding">
                                    <a href="javascript:void(0)" title="Edit"
                                       class="edit_tickets_info_btn pull-left btn btn-success"
                                       data-token=<?= yii::$app->utils->encryptData($model->ticket_id) ?>>Edit
                                        <i class="fa fa-edit" title="Edit"></i>
                                    </a>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <?php if (!empty($modelAuditScheduleName->audit_schedule_name)) { ?>
                        <div class="col-sm-12 col-lg-12 col-md-12">
                            <div class="col-sm-3 col-md-3 col-lg-3 nopadding">
                                <label>Scheduled Audit ID </label>
                            </div>
                            <div class="col-sm-1 col-lg-1 col-md-1 nopadding">
                                <label>: </label>
                            </div>
                            <div class="col-sm-4 col-lg-3 col-md-3 nopadding">
                                <label><?= $modelAuditScheduleName->audit_schedule_name ?></label>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-sm-12 col-lg-12 col-md-12">
                        <div class="col-sm-3 col-md-3 col-lg-3 nopadding">
                            <label>Location </label>
                        </div>
                        <div class="col-sm-1 col-lg-1 col-md-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-8 col-lg-8 col-md-8 nopadding">
                            <label><?= $model->locations->locationCity->name ?> </label>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-12 col-md-12">
                        <div class="col-sm-3 col-md-3 col-lg-3 nopadding">
                            <label>Hotel Name </label>
                        </div>
                        <div class="col-sm-1 col-lg-1 col-md-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-8 col-lg-8 col-md-8 nopadding">
                            <label><?= $model->hotel->hotel_name ?> </label>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-12 col-md-12">
                        <div class="col-sm-3 col-md-3 col-lg-3 nopadding">
                            <label>Department </label>
                        </div>
                        <div class="col-sm-1 col-lg-1 col-md-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-8 col-lg-8 col-md-8 nopadding">
                            <label><?= $model->department->department_name ?> </label>
                        </div>
                    </div>
                    <?php if (!empty($modelAuditScheduleName->audit_schedule_name)) { ?>
                        <div class="col-sm-12 col-lg-12 col-md-12">
                            <div class="col-sm-3 col-md-3 col-lg-3 nopadding">
                                <label>Checklist</label>
                            </div>
                            <div class="col-sm-1 col-lg-1 col-md-1 nopadding">
                                <label>: </label>
                            </div>
                            <div class="col-sm-8 col-lg-8 col-md-8 nopadding">
                                <label><?= $modelAuditSchedule->audit->checklist->cl_name ?> </label>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-sm-12 col-lg-12 col-md-12">
                        <div class="col-sm-3 col-md-3 col-lg-3 nopadding">
                            <label>Section </label>
                        </div>
                        <div class="col-sm-1 col-lg-1 col-md-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-8 col-lg-8 col-md-8 nopadding">
                            <label><?= $model->section->s_section_name ?> </label>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-12 col-md-12">
                        <div class="col-sm-3 col-md-3 col-lg-3 nopadding">
                            <label>Subsection </label>
                        </div>
                        <div class="col-sm-1 col-lg-1 col-md-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-8 col-lg-8 col-md-8 nopadding">
                            <?php
                            $subSectionList = ArrayHelper::map(\app\models\SubSections::getList(), 'sub_section_id', 'ss_subsection_name');
                            $subsectionId = $model->sub_section_id;
                            ?>
                            <label><?= isset($subSectionList[$subsectionId]) ? $subSectionList[$subsectionId] : $subsectionId ?> </label>

                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-12 col-md-12">
                        <div class="col-sm-3 col-md-3 col-lg-3 nopadding">
                            <label>Priority </label>
                        </div>
                        <div class="col-sm-1 col-lg-1 col-md-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-8 col-lg-8 col-md-8 nopadding">
                            <?php
                            $priority_type = $model->priority_type_id;
                            $priority_type_arr = [
                                1 => 'High',
                                2 => 'Medium',
                                3 => 'Low'
                            ];
                            ?>
                            <label style="color: <?php
                            if ($priority_type == "1") {
                                echo "red";
                            } else if ($priority_type == "2") {
                                echo "orange";
                            } else if ($priority_type == "3") {
                                echo "green";
                            }
                            ?> ">
                                       <?= $priority_type_arr[$priority_type]; ?>
                            </label>
                        </div>
                    </div>


                    <?php
                    $ticket_type = "Dynamic";
                    if (!empty($model->audit_schedule_id)) {
                        $ticket_type = "Audit";
                    }
                    ?>

                    <div class="col-sm-12 col-lg-12 col-md-12">
                        <div class="col-sm-3 col-md-3 col-lg-3 nopadding">
                            <label>Process Critical (<?php echo $ticket_type; ?>) </label>
                        </div>
                        <div class="col-sm-1 col-lg-1 col-md-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-8 col-lg-8 col-md-8 nopadding">
                            <label>
                                <?php
                                if ($ticket_type == "Dynamic") {
                                    echo!empty($model->process_critical_dynamic) ? "Yes" : "No";
                                } else {
                                    echo!empty($modelAnswers['question']['process_critical']) ? "Yes" : "No";
                                }
                                ?>
                            </label>
                        </div>
                    </div>





                    <div class="col-sm-12 col-lg-12 col-md-12">
                        <div class="col-sm-3 col-md-3 col-lg-3 nopadding">
                            <label>Status </label>
                        </div>
                        <div class="col-sm-1 col-lg-1 col-md-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-8 col-lg-8 col-md-8 nopadding">
                            <label><?php
                                $status = $model->status;
                                $status_arr = [
                                    1 => 'Assigned',
                                    2 => 'Resolved',
                                    3 => 'Closed',
                                    4 => 'Rejected',
                                    5 => 'Cancelled'
                                ];

                                echo $status_arr[$status];
                                ?> </label>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-12 col-md-12">
                        <div class="col-sm-3 col-md-3 col-lg-3 nopadding">
                            <label>Assigned To </label>
                        </div>
                        <div class="col-sm-1 col-lg-1 col-md-1 nopadding">
                            <label>: </label>
                        </div>
                        <?php if (Yii::$app->authManager->checkPermissionAccess('tickets/update') || in_array($status, [3, 5])) { ?>
                            <div
                                class="col-sm-8 col-lg-8 col-md-8 nopadding">
                                <label><?= $model->assignedUser->first_name . ' ' . $model->assignedUser->last_name ?></label>
                            </div>
                            <?php
                        } else {
                            ?>

                            <div
                                class="col-sm-3 col-lg-3 col-md-3 nopadding">
                                    <?php
                                    $users = \app\models\Audits::getAuditorsList($model->department_id, $model->hotel_id, $model->location_id, 3);
                                    $users = ArrayHelper::map($users, 'user_id', function ($element) {
                                                return $element['first_name'] . ' ' . $element['last_name'];
                                            });
                                    echo Html::hiddenInput('assignedUserHidden', $model->assigned_user_id, [
                                        'class' => 'form-control',
                                        'id' => 'changeAssignedUserHidden'
                                    ]);
                                    echo Html::hiddenInput('selectedTicket', $model->ticket_id, [
                                        'class' => 'form-control',
                                        'id' => 'selectedTicketId'
                                    ]);
                                    echo Html::dropDownList('assignedUser', $model->assigned_user_id, $users, [
                                        'class' => 'form-control',
                                        'id' => 'changeAssignedUser'
                                    ])
                                    ?>
                            </div>
                            <div class="col-sm-3 col-lg-3 col-md-3 nopadding">
                                <button disabled class="btn btn-success" id="saveAssingedUser">
                                    <i class=""></i>Save
                                </button>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-sm-12 col-lg-12 col-md-12">
                        <div class="col-sm-3 col-md-3 col-lg-3 nopadding">
                            <label>Due Date </label>
                        </div>
                        <div class="col-sm-1 col-lg-1 col-md-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-8 col-lg-8 col-md-8 nopadding">
                            <label style="color: red"><?= Yii::$app->formatter->asDate($model->due_date, 'php:d-m-Y') ?> </label>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-12 col-md-12">
                        <div class="col-sm-3 col-md-3 col-lg-3 nopadding">
                            <?php if ($model->audit_schedule_id) { ?>
                                <label>Auditor </label>
                            <?php } else { ?>
                                <label>Created By </label>

                            <?php } ?>

                        </div>
                        <div class="col-sm-1 col-lg-1 col-md-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-8 col-lg-8 col-md-8 nopadding">
                            <?php $userId = $model->created_by ?>
                            <label><?= Audits::getUserName($userId)->first_name . ' ' . Audits::getUserName($userId)->last_name ?> </label>
                        </div>
                    </div>
                </div>
            </div>
            <div id="description" class="tab-pane fade in active">
                <div class="content">
                    <div class="col-md-12">
                        <div class="col-sm-2 nopadding">
                            <label>Section </label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <label><?= $model->section->s_section_name ?> </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-sm-2 nopadding">
                            <label>Subsection </label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <label><?= ($model->subSection) ? $model->subSection->ss_subsection_name : $model->sub_section_id ?> </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-sm-2 nopadding">
                            <label>Subject</label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <label><?= $model->subject ?> </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-sm-2 nopadding">
                            <label>Answer </label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>

                        <?php
                        if ($modelAnswers['question']) {
                            switch ($modelAnswers['question']['q_response_type']) {
                                case 1:
                                    ?>
                                    <div class="col-sm-9 nopadding">
                                        <div class="col-sm-3">
                                            <input disabled type="radio" value="1"
                                                   <?= ($modelAnswers['answer_value'] == '1') ? 'checked' : '' ?>>
                                            <label for="APRYes">True</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <input disabled type="radio" value="0"
                                                   <?= ($modelAnswers['answer_value'] == '0') ? 'checked' : '' ?>>
                                            <label for="APRNo">False</label>
                                        </div>

                                    </div>

                                    <?php
                                    break;
                                case 2:
                                    ?>
                                    <div class="col-sm-9 nopadding">
                                        <div class="col-sm-3">
                                            <input disabled type="radio" value="1"
                                                   <?= ($modelAnswers['answer_value'] == '1') ? 'checked' : '' ?>>
                                            <label for="APRYes">Yes</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <input disabled type="radio" value="0"
                                                   <?= ($modelAnswers['answer_value'] == '0') ? 'checked' : '' ?>>
                                            <label for="APRNo">No</label>
                                        </div>
                                    </div>
                                    <?php
                                    break;
                                case 3:
                                    ?>
                                    <div class="col-sm-9 nopadding">
                                        <div class="col-sm-3 ">
                                            <?php
                                            $ratingValue = '';
                                            $ratingValue = @unserialize($modelAnswers['options_values']);
                                            if (isset($ratingValue[0])) {
                                                $ratingValue = $ratingValue[0];
                                            } else {
                                                $ratingValue = 0;
                                            }
                                            ?>
                                <!--   <input disabled type="range"
                                       title="<?= $ratingValue ?>%"
                                       min="10" max="100"
                                       value="<?= $ratingValue ?>"
                                       class="slider"><?= $ratingValue ?>
                                            -->
                                            <b style="margin-left: 53px;"><?= $ratingValue ?></b>
                                        </div>

                                        <div id="slidecontainer" class="col-md-4"></div>
                                    </div>


                                    <?php
                                    break;
                                    ?>

                                <?php
                            }
                            ?>
                            <?php
                            if (in_array($modelAnswers['question']['q_response_type'], [
                                        5,
                                        4
                                    ])) {
                                $questionnaireOptions = @unserialize($modelAnswers['question']['options']);
                                $questionnaireOptionsValue = @unserialize($modelAnswers['options_values']);
                                $responseType = $modelAnswers['question']['q_response_type'];
                                $inputType = $responseType == 5 ? 'checkbox' : 'radio';
                                $qns = 1;
                                foreach ($questionnaireOptions as $questionnairekey => $questionnairevalue) {

                                    if ($qns == 1) {
                                        ?>

                                        <div
                                            class="col-sm-9 nopadding">
                                            <div class="col-sm-3 ">
                                                <label class="mul-options" for="APRNo">Multiple Options</label>
                                            </div>
                                            <div class="col-sm-4 "></div>

                                        </div>
                                    <?php } ?>

                                    <div class="col-sm-12 nopadding">

                                        <div class="col-sm-3 nopadding"></div>
                                        <div class="col-sm-2">
                                            <input disabled type="<?= $inputType ?>"
                                                   value="<?= $questionnairekey ?>"
                                                   <?= is_array($questionnaireOptionsValue) ? (in_array($questionnairekey, $questionnaireOptionsValue) ? 'checked' : '') : '' ?>>
                                            <label for=""><?= $questionnairevalue ?></label>
                                        </div>


                                    </div>
                                    <?php
                                    $qns ++;
                                }
                            }
                            ?>


                        <?php } ?>


                    </div>


                    <div class="col-md-12 well">
                        <div class="col-sm-2 nopadding">
                            <label>Observations </label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <label><?= $model->description ?></label>
                        </div>
                    </div>


                    <div class="col-md-12 well">

                        <div class="col-sm-2 nopadding">
                            <label>Comments </label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <?php
                        $c_count = 1;
                        if (!empty($modelTicketsComments)) {
                            foreach ($modelTicketsComments as $getTicketsComments) {
                                ?>

                                <div class="col-sm-2 nopadding">
                                    <label> </label>
                                </div>
                                <div class="col-sm-1 nopadding">
                                    <label> </label>
                                </div>

                                <div class="col-sm-9 nopadding">
                                    <p>
                                        <b> <?= $c_count ?>.
                                            Comment
                                            by <?= $getTicketsComments->createdBy->first_name . " " . $getTicketsComments->createdBy->last_name; ?>
                                            &nbsp;&nbsp;&nbsp; <?= date('d-m-Y h:i A', strtotime($getTicketsComments->created_at)); ?></b>
                                    </p>
                                    <p><?= $getTicketsComments->ticket_comment ?></p>
                                </div>

                                <?php
                                $c_count ++;
                            }
                        } else {
                            ?>
                            <span style="margin-left: 22px;">--</span>
                            <?php
                        }
                        ?>
                    </div>


                    <?php if ($model->status == 3 || $model->status == 5) { ?>


                        <?php
                    } else {
                        if (Yii::$app->authManager->checkPermissionAccess('tickets/create')) {
                            ?>

                            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'action' => yii::$app->urlManager->createUrl('tickets/comments')]) ?>
                            <div class="col-md-12 well">
                                <div class="col-sm-2 nopadding">
                                    <label>Add Comments </label>
                                </div>
                                <div class="col-sm-1 nopadding">
                                    <label>: </label>
                                </div>

                                <div class="col-sm-6 nopadding">
                                    <input type="hidden" name="ticket_id"
                                           value="<?= $model->ticket_id ?>">
                                           <?= $form->field($modelComments, 'ticket_comment')->textarea(['rows' => '3', 'placeholder' => 'Enter Comments'])->label(false); ?>
                                </div>
                                <div class="col-sm-9 nopadding pull-right"
                                     style="margin-top: 10px;">
                                         <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                                </div>
                            </div>
                            <?php ActiveForm::end(); ?>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <div id="attachments" class="tab-pane fade">

                <!---------------------------------------------Third section start here ---------------------->
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'action' => yii::$app->urlManager->createUrl('tickets/upload')]) ?>

                <?php if ($model->status == 3 || $model->status == 5) { ?>


                    <?php
                } else {
                    if (Yii::$app->authManager->checkPermissionAccess('tickets/create')) {
                        ?>
                        <div class="col-md-9 showfilter">

                            <div class="col-lg-3 col-md-6 col-sm-12 rating-top ">
                                <div class="form-group">Add New Attachment:</div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 rating-top">
                                <div class="form-group">
                                    <input type="hidden" name="ticket_id"
                                           value="<?= $model->ticket_id ?>">
                                           <?= $form->field($modelTicketAttachment, 'ticket_attachment_path')->fileInput()->label(false) ?>
                                </div>
                            </div>


                            <div class="col-lg-3 col-md-6 col-sm-12 rating-top">
                                <div class="form-group">
                                    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                                </div>
                            </div>


                        </div> <?php
                    }
                }
                ?>
                <?php ActiveForm::end(); ?>
                <div class="content">
                    <?php
                    if (!empty($modelTicketsAttachments)) {
                        foreach ($modelTicketsAttachments as $eachTicketsAttachments) {
                            ?>
                            <div class="col-md-9 well">
                                <p>Uploaded
                                    by <?= $eachTicketsAttachments->createdBy->first_name . ' ' . $eachTicketsAttachments->createdBy->last_name ?>
                                    on <?= date('d-m-Y h:i A', strtotime($eachTicketsAttachments['created_at'])); ?> </p>
                                <p>

                                    <a style="word-wrap: break-word" download
                                       title="Download <?= $eachTicketsAttachments['ticket_attachment_path'] ?>"
                                       href="<?= Yii::getAlias('@web') . '/img/answers_attachments/' . $eachTicketsAttachments['ticket_attachment_path'] ?>"><?= $eachTicketsAttachments->ticket_attachment_path ?>


                                    </a> <a
                                        title="Download <?= $eachTicketsAttachments['ticket_attachment_path'] ?>"
                                        href="<?= Yii::getAlias('@web') . '/img/answers_attachments/' . $eachTicketsAttachments['ticket_attachment_path'] ?>"
                                        download>&nbsp; <i class="fa fa-arrow-circle-o-down"
                                                       title="Download <?= $eachTicketsAttachments->ticket_attachment_path ?>"></i>
                                    </a> &nbsp;


                                </p>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="col-md-9 well">';
                        echo "No attachments for this ticket.";
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

            <div id="history" class="tab-pane fade">
                <div class="content">
                    <?php
                    if (!empty($modelTicketHistory)) {
                        foreach ($modelTicketHistory as $eachTicketHistory) {
                            ?>
                            <div class="col-md-9 well">
                                <p> <?= date('d-m-Y h:i A', strtotime($eachTicketHistory->created_at)); ?>  </p>

                                <p><?= $eachTicketHistory->ticket_message ?>.</p>
                            </div>
                            <?php
                        }
                    } else {
                        echo 'No Data found.';
                    }
                    ?>

                </div>
            </div>


            <?php if ($model->chronicity == 1 OR $model->audit_schedule_id == null) { ?>
                <div id="processCritical" class="tab-pane fade">
                    <div class="content">
                        <?php $form_root_cause = ActiveForm::begin(['id' => 'root_cause_analysis_form', 'action' => [yii::$app->urlManager->createUrl('tickets/save-ticket-preference'), 'id' => Yii::$app->utils->encryptData($model->ticket_id)]]); ?>
                        <div class="col-md-12">
                            <div class="col-sm-2 nopadding">
                                <label>Location </label>
                            </div>
                            <div class="col-sm-1 nopadding">
                                <label>: </label>
                            </div>
                            <div class="col-sm-9 nopadding">
                                <label><?php echo $model->locations->location_description; ?> </label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="col-sm-2 nopadding">
                                <label>Hotel </label>
                            </div>
                            <div class="col-sm-1 nopadding">
                                <label>: </label>
                            </div>
                            <div class="col-sm-9 nopadding">
                                <label><?php echo $model->hotel->hotel_name; ?> </label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="col-sm-2 nopadding">
                                <label>Department </label>
                            </div>
                            <div class="col-sm-1 nopadding">
                                <label>: </label>
                            </div>
                            <div class="col-sm-9 nopadding">
                                <label><?php echo $model->department->department_name; ?> </label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="col-sm-2 nopadding">
                                <label>Problem </label>
                            </div>
							<div class="col-sm-1 nopadding">
                                <label>: </label>
                            </div>
                            <div class="col-sm-9 nopadding">
                                <label><?= $model->subject ?> </label>
                            </div>
							
                            <div class="col-sm-9 nopadding">
                                <label><?php echo $model->description; ?> </label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="col-sm-2 nopadding">
                                <label>Problem Classification </label>
                            </div>
                            <div class="col-sm-1 nopadding">
                                <label>: </label>
                            </div>
                            <div class="col-sm-4 nopadding">
                                <?= $form_root_cause->field($root_cause_analysis, 'prob_module_id')->dropDownList(ArrayHelper::map(\app\models\ProcessCriticalPreferences::find()->where(['module_id' => 1])->asArray()->all(), 'critical_preference_id', 'module_option'), ['prompt' => 'Select'], ['class' => 'form-control'])->label(false); ?> 
                            </div>
                        </div>



                        <div class="col-md-12">
                            <div class="col-sm-2 nopadding">
                                <label>Root Cause Analysis</label>
                            </div>
                            <div class="col-sm-1 nopadding">
                                <label>: </label>
                            </div>
                           
							<div class="col-sm-4 nopadding">
                                <?= $form_root_cause->field($root_cause_analysis, 'root_cause')->textarea(['class' => 'form-control', 'rows' => 3])->label(false); ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-sm-2 nopadding">
                                <label>Improvement Plan for Zero Deviation </label>
                            </div>
                            <div class="col-sm-1 nopadding">
                                <label>: </label>
                            </div>
                            <div class="col-sm-4  nopadding">
                                <?= $form_root_cause->field($root_cause_analysis, 'improvement_plan')->textarea(['class' => 'form-control', 'rows' => 3])->label(false); ?> 
                            </div>
                        </div>
                        <?php
                        $dateEnabledModules = \app\models\ProcessCriticalPreferences::find()
                                ->select("critical_preference_id")
                                ->where('module_id=:module_id AND (stop_reminders = 1 OR stop_escalations = 1)', [':module_id' => 2])
                                ->asArray()
                                ->column();
                        ?>
                        <input type="hidden" id="dateEnabledModules" data-date-enabled-modules='<?= json_encode($dateEnabledModules); ?>'>
                        <div class="col-md-12">
                            <div class="col-sm-2 nopadding">
                                <label>Improvement Plan Classification </label>
                            </div>
                            <div class="col-sm-1 nopadding">
                                <label>: </label>
                            </div>
                            <div class="col-sm-4 nopadding">
                                <?= $form_root_cause->field($root_cause_analysis, 'improve_plan_module_id')->dropDownList(ArrayHelper::map(\app\models\ProcessCriticalPreferences::find()->where(['module_id' => 2])->asArray()->all(), 'critical_preference_id', 'module_option'), ['prompt' => 'Select'], ['class' => 'form-control'])->label(false); ?> 
                            </div>
                        </div>

                        <div class="col-md-12">
                            <?php if ($root_cause_analysis->isNewRecord || !in_array($root_cause_analysis->improve_plan_module_id, $dateEnabledModules)) { ?>
                                <div id="stop_notification_date_block" class="hidden">
                                    <div class="col-sm-2 nopadding">
                                        <label>Stop Notification Until</label>
                                    </div>
                                    <div class="col-sm-1 nopadding">
                                        <label>: </label>
                                    </div>
                                    <div class="col-sm-4 nopadding">
                                        <?= $form_root_cause->field($root_cause_analysis, 'stop_notifications_until_date')->textInput(['id' => 'ticketprocesscritical-stop_notifications_until_date', 'class' => 'datetimepicker form-control', 'placeholder' => 'Select date'])->label(false); ?>
                                    </div>
                                </div>
                            <?php } else if (in_array($root_cause_analysis->improve_plan_module_id, $dateEnabledModules)) {
                                ?>
                                <div id="stop_notification_date_block" class="">
                                    <div class="col-sm-2 nopadding">
                                        <label>Stop Notification Until</label>
                                    </div>
                                    <div class="col-sm-1 nopadding">
                                        <label>: </label>
                                    </div>
                                    <div class="col-sm-4 nopadding">
                                        <?= $form_root_cause->field($root_cause_analysis, 'stop_notifications_until_date')->textInput(['value' => date("d-m-Y", strtotime($root_cause_analysis->stop_notifications_until_date)), 'id' => 'ticketprocesscritical-stop_notifications_until_date', 'class' => 'datetimepicker form-control', 'placeholder' => 'Select date'])->label(false); ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <div class='col-sm-12' style="margin-top: 20px;">
                            <div class="col-sm-2 nopadding">
                            </div>
                            <div class="col-sm-9 nopadding text-center">
                                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>

                            </div>
                        </div>

                        <?php ActiveForm::end(); ?>

                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
</div>


<!----------------------------------------------Update auditor POPUP start here---------------------------- -->
<?php
$newRoleform = ActiveForm::begin([
            'id' => 'ticket_update_user_form',
            'action' => yii::$app->urlManager->createUrl('tickets/update-audit-user'),
            'method' => 'post'
        ])
?>
<div id="edituserspop" class="modal fade" role="dialog"
     style="height: auto">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit</h4>
            </div>
            <div class="modal-body" style="min-height: 360px">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-3 nopadding">
                            <label>Assigned To :</label>
                        </div>
                        <div class="col-sm-8 nopadding">
                            <input type="hidden" name="user_id" id="user_id"
                                   value="<?= Yii::$app->user->getId() ?>" /> <input type="hidden"
                                   name="updat_ticket_id" id="updat_ticket_id" value="" /> <input
                                   type="hidden" id="edit_user_id"
                                   value="<?= yii::$app->urlManager->createUrl('tickets/get-audit-user-id'); ?>" />
                            <div class="col-sm-10 nopadding">

                                <?php
                                $users = \app\models\Audits::getAuditorsList($model->department_id, $model->hotel_id, $model->location_id, 3);

                                $users = ArrayHelper::map($users, 'user_id', function ($element) {
                                            return $element['first_name'] . ' ' . $element['last_name'];
                                        });

                                echo $newRoleform->field($model, 'assigned_user_id')
                                        ->widget(Select2::classname(), [
                                            'data' => $users,
                                            'showToggleAll' => false,
                                            'language' => 'en',
                                            'options' => [
                                                'placeholder' => 'Staff User'
                                            ],
                                            'pluginOptions' => [
                                                'allowClear' => true
                                            ]
                                        ])
                                        ->label(false);
                                ?>

                            </div>


                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-3 nopadding">
                            <label>Due Date :</label>
                        </div>
                        <div class="col-sm-8 nopadding">
                            <div class="col-sm-10 nopadding ">

                                <?= $form->field($model, 'due_date')->textInput(['value' => $model->due_date ? Yii::$app->formatter->asDate($model->due_date, "php:d-m-Y") : '', 'class' => 'datetimepicker form-control clsDatePicker', 'id' => 'dateDue'])->label(false); ?>
                            </div>

                            <div class='col-sm-12'
                                 style="margin-top: 20px; margin-left: -29px;">
                                <div class="col-sm-2 nopadding"></div>
                                <div class="col-sm-9 nopadding text-center">
                                    <button type="submit" id="ticket_audit_update_user"
                                            class="btn btn-success">
                                        <i class=""></i>Save
                                    </button>
                                    <button type="button" class="btn btn-default"
                                            data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>