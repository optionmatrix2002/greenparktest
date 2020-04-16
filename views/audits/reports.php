<?php
/* @var $this View */

/* @var $content string */

use app\assets\AppAsset;
use app\models\Tickets;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

$this->title = 'Audit Report for Audit ID - ' . $model->audit_schedule_name;
$this->params['breadcrumbs'][] = ['label' => 'Audits', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->audit_schedule_id, 'url' => ['Reports', 'id' => $model->audit_schedule_id]];


AppAsset::register($this);
View::registerCssFile(yii::$app->urlManager->createUrl('css/custom-checkbox.css'));
View::registerJsFile(yii::$app->urlManager->createUrl('js/loading-bar/loading-bar.min.js' ), ['depends' => JqueryAsset::className()]);
View::registerCssFile(yii::$app->urlManager->createUrl('js/loading-bar/loading-bar.css'));
View::registerJsFile(yii::$app->urlManager->createUrl('js/audits.js?version='.time()), ['depends' => JqueryAsset::className()]);
View::registerCssFile(yii::$app->urlManager->createUrl('css/dashboard.css'), ['depends' => JqueryAsset::className()]);
View::registerCssFile(yii::$app->urlManager->createUrl('css/auditReports.css'), ['depends' => JqueryAsset::className()]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/modelpopup.js'), ['depends' => JqueryAsset::className()]);


$this->registerJsFile(
    'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.js', ['depends' => [JqueryAsset::className()]]
);
$this->registerJsFile(
    'https://cdnjs.cloudflare.com/ajax/libs/jszip-utils/0.0.2/jszip-utils.js', ['depends' => [JqueryAsset::className()]]
);
$this->registerJsFile(
    'https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js', ['depends' => [JqueryAsset::className()]]
);

$this->registerJs('
$(".nav-bids").removeClass("active");
$("#MenuAudits").addClass("active");
', View::POS_END);
?>

    <div class="container-fluid">
        <h2><?= $this->title ?></h2>
    </div>
    <div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="fa fa-file-text-o header-icon-fontcolor"></span>
    </span>
        <p id="details-text" class="details">
            Audit report can be viewed and downloaded from here.
        </p>
    </div>
    <div class="col-md-12">
    <div class="row">
        <ul class="nav nav-tabs" id="myTab">

            <?php
            $compareActiveClassName = '';
            $detailsActiveClass = '';
            ?>

            <?php
            if ($model->status == 3 || $model->status == 2) {
                $compareActiveClassName = 'in active';
                $detailsActiveClass = '';
                ?>

                <li class="active"><a class="tabs" data-toggle="tab" href="#quickview"><i class="fa fa-eye"
                                                                                          aria-hidden="true"></i>&nbsp;
                        Comparison</a></li>
                <li><a class="tabs" data-toggle="tab" href="#details"><i class="fa fa-th-list"
                                                                         aria-hidden="true"
                                                                         onclick="return getheadtext();"></i>&nbsp;Details</a>
                </li>
                <?php
            } else {
                $detailsActiveClass = 'in active';
                $compareActiveClassName = ''
                ?>
                <li class="active"><a class="tabs" data-toggle="tab" href="#details"><i class="fa fa-th-list"
                                                                                        aria-hidden="true"
                                                                                        onclick="return getheadtext();"></i>&nbsp;Details</a>
                </li>
            <?php } ?>

            <?php if ($model->status == 3 || $model->status == 2) { ?>
                <li><a class="tabs" data-toggle="tab" href="#questionnare"><i class="fa fa-question-circle"
                                                                              aria-hidden="true"></i>&nbsp;Questionnaire</a>
                </li>

                <li><a class="tabs" data-toggle="tab" href="#auditnotes"><i class="fa fa-sticky-note-o"
                                                                            aria-hidden="true"></i>&nbsp;Audit Notes</a>
                </li>
                <li><a class="tabs" data-toggle="tab" href="#chronic"><i class="fa fa-exclamation-circle"
                                                                         aria-hidden="true"></i>&nbsp;Chronic</a>
                </li>
            <?php } ?>

            <li class="pull-right" style="margin-bottom: 2px; border:1px solid #ccc">
                <a href="<?= yii::$app->urlManager->createUrl('audits/view-audit?id=' . Yii::$app->utils->encryptData($model->audit_id)); ?>"
                   class="btn btn-default"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
            </li>
            <?php if ($model->status == 3) { ?>
                <li style="float:right; margin-right: 20px">
                    <a href="javascript:void(0);" class="btn btn-success donwload-reports"><i
                                class="glyphicon glyphicon-download" aria-hidden="true"></i>&nbsp;Download</a>
                </li>
            <?php } ?>
            <?php if ($model->status == 3) { ?>
                <li style="float:right; margin-right: 20px">
                    <a href="javascript:void(0);" class="btn btn-success email-reports"><i
                                class="glyphicon " aria-hidden="true"></i>&nbsp;Email Report</a>
                </li>
            <?php } ?>

        </ul>
        <div class="tab-content">
            <!-- First Section Start here -->
            <div id="details" class="tab-pane fade <?= $detailsActiveClass ?>">
                <div class="content">
                    <div class="col-md-12">
                        <div class="col-md-4 nopadding" style="margin-bottom: 10px; text-align: center;">
                            <h2><u><?= $modelAudit->checklist->cl_name ?> Audit</u></h2>
                        </div>
                    </div>
                    <div class="col-md-4 nopadding" style="margin-bottom: 10px; text-align: center;">
                        <h3 style="text-indent: 13px;">Audit Score </h3>
                        <?php if ($model->status == 4) { ?>
                            Audit is Cancelled
                        <?php } elseif ($model->status == 3) { ?>
                            <h3 style="text-indent: 13px;"><?= round($totalScore, 2) ?>/100 </h3>
                        <?php } elseif ($model->status == 1 || $model->status == 2) { ?>
                            Audit is currently in progress
                        <?php } elseif ($model->status == 0) { ?>
                            Audit is scheduled
                        <?php } ?>

                    </div>
                    <div class="col-md-12">

                        <div class="col-sm-2 nopadding">
                            <label>Audit ID</label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <label><?= $modelAudit->audit_name ?></label>
                        </div>
                    </div>
                    <div class="col-md-12">

                        <div class="col-sm-2 nopadding">
                            <label>Scheduled Audit ID </label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <label><?= $model->audit_schedule_name ?></label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-sm-2 nopadding">
                            <label>Location </label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <label><?= $modelAudit->location->locationCity->name ?> </label>
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
                            <label><?= $modelAudit->hotel->hotel_name ?> </label>
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
                            <label><?= $modelAudit->department->department_name ?> </label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-sm-2 nopadding">
                            <label>Checklist </label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <label><?= $modelAudit->checklist->cl_name ?> </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-sm-2 nopadding">
                            <label>Audit Method </label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">

                            <label><?= $modelAudit->checklist->clAuditMethod->audit_method_name ?> </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-sm-2 nopadding">
                            <label>Audit Type </label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <label><?= $modelChecklists->cl_audit_type == 1 ? "External" : "Internal" ?> </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-sm-2 nopadding">
                            <label>Auditor</label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">

                            <label>
                                <?= $model->auditor->first_name . " " . $model->auditor->last_name ?>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-sm-2 nopadding">
                            <label>Start Date</label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <label><?= Yii::$app->formatter->asDate($model->start_date, 'php:d-m-Y') ?> </label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-sm-2 nopadding">
                            <label>End Date</label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <label><?= Yii::$app->formatter->asDate($model->end_date, 'php:d-m-Y') ?> </label>
                        </div>
                    </div>
                    <?php if ($model->status == 3 || $model->status == 4) { ?>
                        <div class="col-md-12">
                            <div class="col-sm-2 nopadding">
                                <label>Submitted Date</label>
                            </div>
                            <div class="col-sm-1 nopadding">
                                <label>: </label>
                            </div>
                            <div class="col-sm-9 nopadding">
                                <label><?= Yii::$app->formatter->asDate($model->updated_at, 'php:d-m-Y') ?> </label>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-md-12">
                        <div class="col-sm-2 nopadding">
                            <label>Status</label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <label><?php ?>
                                <?php
                                $statusType = [0 => 'Scheduled', 1 => 'In-progress', 2 => 'Draft', 3 => 'Completed', 4 => 'Cancelled'];
                                echo $statusType[$model->status];
                                ?>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-sm-2 nopadding">
                            <label>Assigned By</label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <label><?= $model->createdBy->first_name . " " . $model->createdBy->last_name ?></label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-sm-2 nopadding">
                            <label>Non-Compliance</label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <label> <?= $nonCompliance > 0 ? '<a title = "Click to view tickets"  target="_blank" href=' . yii::$app->urlManager->createUrl(['tickets/index', 'TicketsSearch[audit_schedule_id]' => $model->audit_schedule_id]) . '>' . $nonCompliance . '</a>' : $nonCompliance ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-sm-2 nopadding">
                            <label>Chronic Issues Found </label>
                        </div>
                        <div class="col-sm-1 nopadding">
                            <label>: </label>
                        </div>
                        <div class="col-sm-9 nopadding">
                            <label> <?= $chronicIssues > 0 ? '<a title = "Click to view tickets"  target="_blank" href=' . yii::$app->urlManager->createUrl(['tickets/index', 'TicketsSearch[audit_schedule_id]' => $model->audit_schedule_id, 'TicketsSearch[chronicity]' => 1]) . '>' . $chronicIssues . '</a>' : $chronicIssues ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <!---------------------------------------Second Section Start here------------------------------------- -->
            <div id="questionnare" class="tab-pane fade ">
                <div class="display-noncomplaints padding-left-25 pull-right">
                    <label>Show Only Non-Compliances</label>
                    <?= Html::checkbox('questionComplaints', true, ['id' => 'enableNonComplaints']) ?>
                </div>
                <?php
                $fileAttach = [];
                $auditTickets = Tickets::find()->select(['ticket_id', 'answer_id', 'ticket_name','chronicity'])->where(['audit_schedule_id' => $model->audit_schedule_id])->asArray()->indexBy('answer_id')->all();

                if ($modelAnswers) {
                    foreach ($modelAnswers as $modelAnswer) {
                        foreach ($modelAnswer as $subSections) {
                            $questions = array_filter(ArrayHelper::getColumn($subSections['questions'], function ($element) {
                                if ($element['checkListAnswers']) {
                                    return $element;
                                }
                            }));
                            if (!$questions) {
                                continue;
                            }
                            ?>
                            <!-- -----------------------------Upper div-------------------------- -->
                            <div class="col-sm-12 nopadding" style="margin-bottom: 10px;">
                                <div class="col-sm-12">
                                    <strong>Section: </strong><?= $subSections['sectionName'] ?>
                                </div>

                                <div class="col-sm-12">
                                    <strong>Subsection: </strong><?= $subSections['subSectionName'] ? $subSections['subSectionName'] : 'Dynamic questions' ?>
                                </div>

                            </div>


                            <!-- -----------------------------Questiona start here-------------------------- -->
                            <div class="col-sm-12">
                                <div class="col-md-12 nopadding">
                                    <div class="panel-group ui-sortable" aria-expanded="true">
                                        <?php
                                        // echo '<pre>'; print_r($subSections['questions']);die;
                                        $n = 1;
                                        if ($subSections['questions']) {
                                            foreach ($subSections['questions'] as $question) {
                                                if ($question['checkListAnswers']) {
                                                    $panelClass = 'complaint-class';
                                                    $display = 'none';
                                                    $eachAnswers = $question['checkListAnswers'];
                                                    if ($eachAnswers['not_applicable'] == "0" && $eachAnswers['answer_value'] == 0) {
                                                        $panelClass = 'nonComplaintClass';
                                                        $display = 'block';
                                                    }
                                                    ?>
                                                    <div style="display:<?= $display ?> "
                                                         class="panel panel-default <?= $panelClass ?>">
                                                        <div class="panel-body">
                                                            <div class="col-sm-12 marginTB10">
                                                                <div class="col-sm-10">
                                                                    <div class="col-sm-12">
                                                                        <?= $n ?>: &nbsp; <?= $question['q_text'] ?>
                                                                        <?php
                                                                        $eachAnswers = $question['checkListAnswers'];

                                                                        if ($eachAnswers['not_applicable'] == "0" && $eachAnswers['answer_value'] == 1) {
                                                                            ?>
                                                                            <span style="color:green;margin-right: 46px; "
                                                                                  class="pull-right"><b><?= "Compliant"; ?></b></span>
                                                                            <?php
                                                                        } elseif ($eachAnswers['not_applicable'] == "0" && $eachAnswers['answer_value'] == 0) {
                                                                            ?>
                                                                            <span style="color:red;margin-right: 15px;"
                                                                                  class="pull-right">
                                                                                <b><?php
                                                                                    $ticket = isset($auditTickets[$eachAnswers['answer_id']]) ? $auditTickets[$eachAnswers['answer_id']] : null;

                                                                                    if ($ticket) {
                                                                                        $ticketId = $ticket['ticket_id'];
                                                                                        $ticketName = $ticket['ticket_name'];
                                                                                        $anchorTag = Html::a($ticketName, yii::$app->urlManager->createUrl('tickets/reports?id=' . Yii::$app->utils->encryptData($ticketId)), ['target' => '_blank', 'title' => "Click to view ticket"]);
                                                                                        echo "Non-Compliant<span style=color:black;> ($anchorTag)</span>";
                                                                                    } else {
                                                                                        echo "Non-Compliant";
                                                                                    }
                                                                                    ?></b></span>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                    <div class="col-sm-12 col-md-12">

                                                                        <?php
                                                                        switch ($question['q_response_type']) {
                                                                            case '1':
                                                                                ?>
                                                                                <div class="col-sm-3">
                                                                                    <input disabled type="radio"
                                                                                           value="1" <?= ($eachAnswers['not_applicable'] == '1') ? '' : (($eachAnswers['answer_value'] == '1') ? 'checked' : '') ?> >
                                                                                    <label for="APRYes">True</label>
                                                                                </div>
                                                                                <div class="col-sm-3">
                                                                                    <input disabled type="radio"
                                                                                           value="0" <?= ($eachAnswers['not_applicable'] == '1') ? '' : (($eachAnswers['answer_value'] == '0') ? 'checked' : '') ?>>
                                                                                    <label for="APRNo">False</label>
                                                                                </div>
                                                                                <?php
                                                                                break;
                                                                            case '2':
                                                                                ?>
                                                                                <div class="col-sm-3">
                                                                                    <input disabled type="radio"
                                                                                           value="1" <?= ($eachAnswers['not_applicable'] == '1') ? '' : (($eachAnswers['answer_value'] == '1') ? 'checked' : '') ?>>
                                                                                    <label for="APRYes">Yes</label>
                                                                                </div>
                                                                                <div class="col-sm-3">
                                                                                    <input disabled type="radio"
                                                                                           value="0" <?= ($eachAnswers['not_applicable'] == '1') ? '' : (($eachAnswers['answer_value'] == '0') ? 'checked' : '') ?>>
                                                                                    <label for="APRNo">No</label>
                                                                                </div>
                                                                                <?php
                                                                                break;
                                                                            case '3':
                                                                                ?>
                                                                                <div id="slidecontainer"
                                                                                     class="col-md-6">
                                                                                    <?php
                                                                                    $ratingValue = @unserialize($eachAnswers['options_values']);
                                                                                    if (is_array($ratingValue) && isset($ratingValue[0])) {
                                                                                        $ratingValue = $ratingValue[0];
                                                                                    } else {
                                                                                        $ratingValue = 0;
                                                                                    }
                                                                                    ?>
                                                                                    <input disabled type="range" min="1"
                                                                                           max="10"
                                                                                           value="<?= ($eachAnswers['not_applicable'] == '1') ? 0 : $ratingValue ?>"
                                                                                           class="slider rating-top"
                                                                                           id="myRange">
                                                                                    <b style="margin-left: 153px;"><?= ($eachAnswers['not_applicable'] == '1') ? 0 : $ratingValue ?></b>
                                                                                </div>
                                                                            <?php
                                                                        }

                                                                        if (in_array($question['q_response_type'], [
                                                                            5,
                                                                            4
                                                                        ])) {
                                                                            $questionnaireOptions = @unserialize($question['options']);
                                                                            $questionnaireOptionsValue = @unserialize($eachAnswers['options_values']);


                                                                            $responseType = $question['q_response_type'];
                                                                            $inputType = $responseType == 5 ? 'checkbox' : 'radio';

                                                                            foreach ($questionnaireOptions as $questionnairekey => $questionnairevalue) {
                                                                                ?>
                                                                                <div class="col-sm-6 col-md-6">

                                                                                    <input disabled
                                                                                           type="<?= $inputType ?>"
                                                                                           value="<?= $questionnairekey ?>" <?= ($eachAnswers['not_applicable'] == '1') ? '' : (is_array($questionnaireOptionsValue) ? (in_array($questionnairekey, $questionnaireOptionsValue) ? 'checked' : '') : '') ?>>
                                                                                    <label for=""><?= $questionnairevalue ?></label>
                                                                                </div>

                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>

                                                                        <?php if ($eachAnswers['not_applicable'] == "1") { ?>
                                                                            <div class="col-md-3">
                                                                                <input disabled
                                                                                       type="checkbox" <?= $eachAnswers['not_applicable'] == "1" ? 'checked' : '' ?>>
                                                                                <label for="APRYes"> Not
                                                                                    applicable </label>
                                                                            </div>
                                                                            <?php ?>

                                                                        <?php } ?>
                                                                    </div>

                                                                    <?php
                                                                    $anwerAttachments = $eachAnswers['answerAttachments'];
                                                                    $anwerAttachments = $anwerAttachments ? $anwerAttachments : [];
                                                                    ?>

                                                                    <div class="col-sm-12">
                                                                        <?php ?>
                                                                        <?php
                                                                        foreach ($anwerAttachments as $anwerAttachmentsValues
                                                                        ) {
                                                                            if (file_exists(Yii::getAlias('@webroot') . '/img/answers_attachments/' . $anwerAttachmentsValues['answer_attachment_path'])) {
                                                                                ?>

                                                                                <div style="word-wrap: break-word"
                                                                                     class="col-sm-9">
                                                                                    <a title="Download <?= $anwerAttachmentsValues['answer_attachment_path'] ?>"
                                                                                       href="<?= Yii::getAlias('@web') . '/img/answers_attachments/' . $anwerAttachmentsValues['answer_attachment_path'] ?>"
                                                                                       download>
                                                                                        <?= $anwerAttachmentsValues['answer_attachment_path'] ?>
                                                                                        <i class="fa fa-arrow-circle-o-down"
                                                                                           title="Download answer attachment"></i>
                                                                                    </a>
                                                                                    <?php
                                                                                    $fileAttach[] = Url::base() . '/img/answers_attachments/' . $anwerAttachmentsValues['answer_attachment_path'];
                                                                                    ?>

                                                                                    &nbsp;&nbsp; &nbsp;
                                                                                </div>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?> <br>
                                                                    </div>


                                                                    <div class="col-sm-12">
                                                                        <p><?= $eachAnswers['observation_text'] ?> </p>
                                                                    </div>


                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $n++;
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>


                            <?php
                        }
                    }
                } else {
                    echo "No Data found";
                }
                ?>

            </div>
            <!---------------------------------------Fourth Section End here------------------------------------- -->
            <div id="quickview" class="tab-pane fade <?= $compareActiveClassName ?>">

                <?php if ($model->status == 3 || $model->status == 4) { ?>

                    <?= $this->render("quickView", ['modelAudit' => $modelAudit]); ?>

                    <?php
                } else {
                    echo "Comparison is shown after audit is submitted.";
                }
                ?>

            </div>
            <div id="auditnotes" class="tab-pane fade">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'action' => yii::$app->urlManager->createUrl('audits/upload')]) ?>
                <div class="col-md-12 showfilter">
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="form-group">
                            Upload Audit Attachment:
                        </div>
                    </div>


                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="form-group">
                            <input type="hidden" name="audit_schedule_id" value="<?= $model->audit_schedule_id ?>">
                            <?= $form->field($modelAuditAttachment, 'audit_attachment_path')->fileInput()->label(false) ?>
                        </div>
                    </div>


                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="form-group">
                            <?= Html::submitButton('Save', ['class' => 'btn btn-success attachment_btn', 'disabled' => 'disabled']) ?>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <!-------------------------------File attachment Start Here----------------------->
                <div class="col-sm-12 nopadding">
                    <?php if ($getAuditAttachments) { ?>
                        <table id="" class="table table-bordered table-hover table-responsive margintop20">
                            <thead class="theadcolor">
                            <tr role="row">
                                <th>S.No</th>
                                <th style="width: 60%">File Name</th>
                                <th>Uploaded By</th>
                                <th class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $n = 1;
                            foreach ($getAuditAttachments as $getAuditAttachmentsDetails) {
                                ?>
                                <tr>
                                    <td><?= $n ?></td>
                                    <td>
                                        <p style="word-wrap:break-word"><?= $getAuditAttachmentsDetails->audit_attachment_path ?> </p>
                                    </td>
                                    <td>
                                        <p><?= $getAuditAttachmentsDetails->updatedBy->first_name ?></p>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        if (file_exists(Yii::getAlias('@webroot') . '/img/audit_attachments/' . $getAuditAttachmentsDetails->audit_attachment_path)) {
                                            $fileAttach[] = Url::base() . '/img/audit_attachments/' . $getAuditAttachmentsDetails->audit_attachment_path;
                                        }
                                        ?>
                                        <a title="Download"
                                           href="<?= Yii::getAlias('@web') ?>/img/audit_attachments/<?= $getAuditAttachmentsDetails->audit_attachment_path ?>"
                                           download>
                                            <i class="fa fa-arrow-circle-o-down" title="Download"></i>
                                        </a>
                                        &nbsp;&nbsp; &nbsp;
                                        <a href="javascript:void(0)" title="Delete" class="delete_auditattachment_btn"
                                           data-token=<?= yii::$app->utils->encryptData($getAuditAttachmentsDetails->audit_attachment_id); ?>><i
                                                    class="fa fa-trash-o" title="Delete"></i></a>
                                    </td>
                                </tr>
                                <?php
                                $n++;
                            }
                            ?>
                            </tbody>
                        </table>
                        <?php
                    } else {
                        echo "No audit notes attached.";
                    }
                    ?>
                </div>
            </div>
            <div id="chronic" class="tab-pane fade">

                <?php
                $fileAttachChronic = [];
                $auditTicketsChronic = array_filter(ArrayHelper::getColumn($auditTickets,function($element){
                    if($element['chronicity'] == 1){
                        return $element;
                    }
                }));

                if ($modelAnswers && $auditTicketsChronic) {
                    $answerIds = ArrayHelper::getColumn($auditTicketsChronic, 'answer_id');

                    foreach ($modelAnswers as $modelAnswer) {
                        foreach ($modelAnswer as $subSections) {
                            $questions = array_filter(ArrayHelper::getColumn($subSections['questions'], function ($element) {
                                if ($element['checkListAnswers']) {
                                    return $element;
                                }
                            }));
                            if (!$questions) {
                                continue;
                            }
                            $subsectionsQuestionsIds = ArrayHelper::getColumn($subSections['questions'], 'checkListAnswers.answer_id');

                            $bFound = (count(array_intersect($subsectionsQuestionsIds, $answerIds))) ? true : false;

                            if (!$bFound) {
                                continue;
                            }
                            ?>
                            <!-- -----------------------------Upper div-------------------------- -->
                            <div class="col-sm-12 nopadding" style="margin-bottom: 10px;">
                                <div class="col-sm-12">
                                    <strong>Section: </strong><?= $subSections['sectionName'] ?>
                                </div>

                                <div class="col-sm-12">
                                    <strong>Subsection: </strong><?= $subSections['subSectionName'] ? $subSections['subSectionName'] : 'Dynamic questions' ?>
                                </div>

                            </div>


                            <!-- -----------------------------Questiona start here-------------------------- -->
                            <div class="col-sm-12">
                                <div class="col-md-12 nopadding">
                                    <div class="panel-group ui-sortable" aria-expanded="true">
                                        <?php
                                        $n = 1;
                                        if ($subSections['questions']) {

                                            foreach ($subSections['questions'] as $question) {
                                                if ($question['checkListAnswers']) {
                                                    $panelClass = '';
                                                    $display = '';
                                                    $eachAnswers = $question['checkListAnswers'];

                                                    ?>
                                                    <?php if ($eachAnswers['not_applicable'] == "0" && $eachAnswers['answer_value'] == 0 && in_array($eachAnswers['answer_id'], $answerIds)) { ?>
                                                        <div style="display:<?= $display ?> "
                                                             class="panel panel-default <?= $panelClass ?>">
                                                            <div class="panel-body">
                                                                <div class="col-sm-12 marginTB10">
                                                                    <div class="col-sm-10">
                                                                        <div class="col-sm-12">

                                                                            <?= $n ?>: &nbsp; <?= $question['q_text'] ?>
                                                                            <?php
                                                                            $eachAnswers = $question['checkListAnswers'];
                                                                            ?>
                                                                            <span style="color:red;margin-right: 15px;"
                                                                                  class="pull-right">
                                                                                <b><?php
                                                                                    $ticket = isset($auditTicketsChronic[$eachAnswers['answer_id']]) ? $auditTicketsChronic[$eachAnswers['answer_id']] : null;
                                                                                    $ticketId = $ticket['ticket_id'];
                                                                                    $ticketName = $ticket['ticket_name'];
                                                                                    $anchorTag = Html::a($ticketName, yii::$app->urlManager->createUrl('tickets/reports?id=' . Yii::$app->utils->encryptData($ticketId)), ['target' => '_blank', 'title' => "Click to view ticket"]);
                                                                                    echo "Non-Compliant<span style=color:black;> ($anchorTag)</span>";

                                                                                    ?></b></span>
                                                                            <?php
                                                                            ?>
                                                                        </div>
                                                                        <div class="col-sm-12 col-md-12">

                                                                            <?php
                                                                            switch ($question['q_response_type']) {
                                                                                case '1':
                                                                                    ?>
                                                                                    <div class="col-sm-3">
                                                                                        <input disabled type="radio"
                                                                                               value="1" <?= ($eachAnswers['not_applicable'] == '1') ? '' : (($eachAnswers['answer_value'] == '1') ? 'checked' : '') ?> >
                                                                                        <label for="APRYes">True</label>
                                                                                    </div>
                                                                                    <div class="col-sm-3">
                                                                                        <input disabled type="radio"
                                                                                               value="0" <?= ($eachAnswers['not_applicable'] == '1') ? '' : (($eachAnswers['answer_value'] == '0') ? 'checked' : '') ?>>
                                                                                        <label for="APRNo">False</label>
                                                                                    </div>
                                                                                    <?php
                                                                                    break;
                                                                                case '2':
                                                                                    ?>
                                                                                    <div class="col-sm-3">
                                                                                        <input disabled type="radio"
                                                                                               value="1" <?= ($eachAnswers['not_applicable'] == '1') ? '' : (($eachAnswers['answer_value'] == '1') ? 'checked' : '') ?>>
                                                                                        <label for="APRYes">Yes</label>
                                                                                    </div>
                                                                                    <div class="col-sm-3">
                                                                                        <input disabled type="radio"
                                                                                               value="0" <?= ($eachAnswers['not_applicable'] == '1') ? '' : (($eachAnswers['answer_value'] == '0') ? 'checked' : '') ?>>
                                                                                        <label for="APRNo">No</label>
                                                                                    </div>
                                                                                    <?php
                                                                                    break;
                                                                                case '3':
                                                                                    ?>
                                                                                    <div id="slidecontainer"
                                                                                         class="col-md-6">
                                                                                        <?php
                                                                                        $ratingValue = @unserialize($eachAnswers['options_values']);
                                                                                        if (is_array($ratingValue) && isset($ratingValue[0])) {
                                                                                            $ratingValue = $ratingValue[0];
                                                                                        } else {
                                                                                            $ratingValue = 0;
                                                                                        }
                                                                                        ?>
                                                                                        <input disabled type="range"
                                                                                               min="1"
                                                                                               max="10"
                                                                                               value="<?= ($eachAnswers['not_applicable'] == '1') ? 0 : $ratingValue ?>"
                                                                                               class="slider rating-top"
                                                                                               id="myRange">
                                                                                        <b style="margin-left: 153px;"><?= ($eachAnswers['not_applicable'] == '1') ? 0 : $ratingValue ?></b>
                                                                                    </div>
                                                                                <?php
                                                                            }

                                                                            if (in_array($question['q_response_type'], [
                                                                                5,
                                                                                4
                                                                            ])) {
                                                                                $questionnaireOptions = @unserialize($question['options']);
                                                                                $questionnaireOptionsValue = @unserialize($eachAnswers['options_values']);


                                                                                $responseType = $question['q_response_type'];
                                                                                $inputType = $responseType == 5 ? 'checkbox' : 'radio';

                                                                                foreach ($questionnaireOptions as $questionnairekey => $questionnairevalue) {
                                                                                    ?>
                                                                                    <div class="col-sm-6 col-md-6">

                                                                                        <input disabled
                                                                                               type="<?= $inputType ?>"
                                                                                               value="<?= $questionnairekey ?>" <?= ($eachAnswers['not_applicable'] == '1') ? '' : (is_array($questionnaireOptionsValue) ? (in_array($questionnairekey, $questionnaireOptionsValue) ? 'checked' : '') : '') ?>>
                                                                                        <label for=""><?= $questionnairevalue ?></label>
                                                                                    </div>

                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>


                                                                        </div>

                                                                        <?php
                                                                        $anwerAttachments = $eachAnswers['answerAttachments'];
                                                                        $anwerAttachments = $anwerAttachments ? $anwerAttachments : [];
                                                                        ?>

                                                                        <div class="col-sm-12">
                                                                            <?php ?>
                                                                            <?php
                                                                            foreach ($anwerAttachments as $anwerAttachmentsValues
                                                                            ) {
                                                                                if (file_exists(Yii::getAlias('@webroot') . '/img/answers_attachments/' . $anwerAttachmentsValues['answer_attachment_path'])) {
                                                                                    ?>

                                                                                    <div style="word-wrap: break-word"
                                                                                         class="col-sm-9">
                                                                                        <a title="Download <?= $anwerAttachmentsValues['answer_attachment_path'] ?>"
                                                                                           href="<?= Yii::getAlias('@web') . '/img/answers_attachments/' . $anwerAttachmentsValues['answer_attachment_path'] ?>"
                                                                                           download>
                                                                                            <?= $anwerAttachmentsValues['answer_attachment_path'] ?>
                                                                                            <i class="fa fa-arrow-circle-o-down"
                                                                                               title="Download answer attachment"></i>
                                                                                        </a>
                                                                                        <?php
                                                                                        $fileAttach[] = Url::base() . '/img/answers_attachments/' . $anwerAttachmentsValues['answer_attachment_path'];
                                                                                        ?>

                                                                                        &nbsp;&nbsp; &nbsp;
                                                                                    </div>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?> <br>
                                                                        </div>

                                                                        <div class="col-sm-12">
                                                                            <p><?= $eachAnswers['observation_text'] ?> </p>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php

                                                    }
                                                    ?>
                                                    <?php

                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <?php
                        }
                    }
                } else {
                    echo "No chronic issues found.";
                }
                ?>

            </div>
        </div>
    </div>

    <div id="deletepopup" class="modal fade" role="dialog">
        <?php ActiveForm::begin(['id' => 'delete_checklist_form', 'action' => yii::$app->urlManager->createUrl('audits/delete-audit-attachemnt'), 'method' => 'post',]) ?>
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" style="color: #fff!important; opacity: 1;"
                            aria-hidden="true">
                        �
                    </button>
                    <h4 class="modal-title">Confirmation</h4>
                </div>
                <div class="modal-body clearfix">
                    <input type="hidden" name="audit_schedule_id" value="<?= $model->audit_schedule_id ?>">
                    <input type="hidden" name="deletable_auditattachment_id" id="deletable_auditattachment_id"
                           value=""/>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <label>
                            Are you sure you want to delete this audit attachment? You can't undo this action.
                        </label>
                    </div>
                </div>
                <div class="modal-footer clearfix" style="border-top: none; margin-top: 5px;">
                    <div class="col-sm-12">
                        <input class="btn btn-danger" type="submit" value="Delete">
                        <button type="button" class="btn btn-Clear" data-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end() ?>
    </div>

    <!-- delete popup modal start -->
    <div id="emailAuditReport" class="modal fade" role="dialog">

        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" style="color: #fff!important; opacity: 1;"
                            aria-hidden="true">
                        �
                    </button>
                    <h4 class="modal-title">Email Details</h4>
                </div>
                <div class="modal-body clearfix">
                    <input type="hidden" name="audit_schedule_id" value="<?= $model->audit_schedule_id ?>">
                    <input type="hidden" name="deletable_auditattachment_id" id="deletable_auditattachment_id"
                           value=""/>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-3">
                            <label class="required-label">
                                Email Id :
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <?= Html::hiddenInput("hidden_audit_schedule_id", $model->audit_schedule_id, ['class' => "hidden_audit_schedule_id"]); ?>
                            <?= Html::textInput("email_reports", '', ['class' => "form-control email_report_input"]); ?>
                            <span class="email-error-text red">

                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer clearfix" style="border-top: none; margin-top: 5px;">
                    <div class="col-sm-12">
                        <button id="sendEmailRport" disabled="true" type="button" class="btn btn-Clear btn-success">
                            <i class=""> </i> Send
                        </button>
                        <button type="button" class="btn btn-Clear" data-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    
    <div id="downloadReportsModal" class="modal fade" role="dialog">

        <div class="modal-dialog popup-radius modal-sm">
            <!-- Modal content-->
            <div class="modal-content popup-content">
                <?php ActiveForm::begin(['id' => 'download_files_form']) ?>
                <div class="modal-header popup-content-header-color">
                    <h4 class="modal-title">
                        <span class="model-delete-headding">Download Files</span>
                    </h4>
                </div>
                <div class="modal-body clearfix">
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <p class="text-muted">
                            Make selection and click download
                        </p>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <p>
                            <input id="impackSummary" class="custom-check" type="radio" name="downloadReportsRadio" checked />
                            <label for="impackSummary">Impact Summary <i class="fa fa-question-circle-o text-muted" title="Consolidated audit report in PDF format"></i></label>
                        </p>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <p>
                            <input class="custom-check" type="radio" id="entireAudit" name="downloadReportsRadio" />
                            <label for="entireAudit">Entire Audit Report <i class="fa fa-question-circle-o text-muted" title="Entire Audit Report in ZIP format"></i></label>
                        </p>
                    </div>
                    <div class="col-sm-12" style="margin-top:5px;">
                        <div class="downloadAuditFilesStatus text-muted text-center" id="downloadAuditFilesStatus"></div>
                        <div  class="downloadAuditFilesPercentage ldBar hidden label-center auto" id="downloadAuditFilesPercentage"  data-preset="line" style="width:100%;height:50px;"></div>
                    </div>
                </div>
                <div class="modal-footer clearfix text-center" style="border-top: none; margin-top: 5px;">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-success submitButton" >
                            <i class="fa fa-download" style="color:#fff;"></i> Download
                        </button>
                        <button type="button" class="btn btn-Clear close-btn" data-dismiss="modal">
                              <i class="fa fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
                <?php ActiveForm::end() ?>
            </div>
        </div>

    </div>


<?php
$hotel = $model->audit->hotel->hotel_name;
$department = $model->audit->department->department_name;
$date = date('M Y', strtotime($model->start_date));

$fileAttach[] = Url::base() . '/reports/' . $hotel . ' ' . $department . ' ' . $date . '.pdf';

if ($model->audit->checklist->cl_audit_span == 2) {
    $fileAttach[] = Url::base() . '/reports/' . $hotel . ' ' . $department . ' ' . $date . '_ACROSS_SECTION_REPORT.pdf';
}

$this->registerJs("var attachmentPath ='" . yii::$app->params['attachments_save_url'] . "';", View::POS_HEAD);
$this->registerJs("var fileList = " . json_encode($fileAttach) . ";", View::POS_HEAD);
$this->registerJs("var auditId = " . json_encode($model->audit_schedule_name) . ";", View::POS_HEAD);
$this->registerJs("var impactSummaryPDF = '" . Url::base() . "/reports/" . $hotel . " " . $department . " " . $date . ".pdf';", View::POS_HEAD);

$this->registerJs("
    
$( '.donwload-reports').click(function() {
    $('#downloadReportsModal').modal('show');
});

");
?>