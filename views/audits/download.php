<?php
/* @var $this View */

/* @var $content string */

use app\assets\AppAsset;
use app\models\AuditsSchedules;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\View;

$this->title = 'Audit Report for Audit ID - ' . $model->audit_schedule_name;
$this->params['breadcrumbs'][] = ['label' => 'Audits', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->audit_schedule_id, 'url' => ['Reports', 'id' => $model->audit_schedule_id]];


AppAsset::register($this);
View::registerCssFile(yii::$app->urlManager->createUrl('css/dashboard.css'), ['depends' => JqueryAsset::className()]);;
View::registerCssFile(yii::$app->urlManager->createUrl('css/auditReports.css'), ['depends' => JqueryAsset::className()]);;

?>

<div style="border:1px solid black" class="container-fluid">
    <?php /*?>
    <div class="text-center">
        <img src="<?php echo Yii::$app->urlManager->createAbsoluteUrl('') . 'img' . DIRECTORY_SEPARATOR . 'greenpark_textlogo.png'; ?>"
        />

        <?php

        echo Html::img(Yii::$app->urlManager->createAbsoluteUrl('') . 'img' . DIRECTORY_SEPARATOR . 'greenpark_textlogo.png', ['style' => 'width:35%']); ?>
    </div>
    <?php */ ?>
    <h2 class="text-center"><?= $this->title ?> </h2>


    <div class="col-md-12">
        <div class="row">
            <div>
                <div class="col-sm-12 nopadding h4color " style="margin-bottom: 10px;  ">
                    <div class="col-md-6 "><h2><u><?= $modelAudit->checklist->cl_name ?></u></h2></div>
                </div>
            </div>

            <div>

            </div>
            <div class="tab-content">
                <!-- First Section Start here -->
                <div id="details" class="tab-pane fade in active">
                    <div class="content">

                        <div class="col-md-4 nopadding" style="margin-bottom: 10px; text-align: center;">
                            <h3 style="text-indent: 13px;">Audit Score </h3>
                            <h3 style="text-indent: 13px;"><?= round($totalScore) ?>/100 </h3>
                        </div>

                        <div class="col-sm-12 nopadding h4color " style="margin-bottom: 10px;  ">
                            <div class="col-md-6 "><h3><b>Details</b></h3></div>
                        </div>

                        <div class="col-md-12">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td>
                                        Audit
                                    </td>
                                    <td>
                                        <?= $modelAudit->audit_name ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Scheduled Audit
                                    </td>
                                    <td>
                                        <?= $model->audit_schedule_name ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Location
                                    </td>

                                    <td>
                                        <?= $modelAudit->location->locationCity->name ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Hotel
                                    </td>

                                    <td>
                                        <?= $modelAudit->hotel->hotel_name ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Department
                                    </td>
                                    <td>
                                        <?= $modelAudit->department->department_name ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Checklist
                                    </td>
                                    <td>
                                        <?= $modelAudit->checklist->cl_name ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Audit Type
                                    </td>
                                    <td>
                                        <?= $modelChecklists->cl_audit_type == 1 ? "External" : "Internal" ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Auditor
                                    </td>
                                    <td>
                                        <?= ucfirst($model->auditor->first_name) . " " . $model->auditor->last_name ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Start Date
                                    </td>

                                    <td>
                                        <?= Yii::$app->formatter->asDate($model->start_date, 'php:d-m-Y') ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        End Date
                                    </td>
                                    <td>
                                        <?= Yii::$app->formatter->asDate($model->end_date, 'php:d-m-Y') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Submitted Date
                                    </td>
                                    <td>
                                        <?= Yii::$app->formatter->asDate($model->updated_at, 'php:d-m-Y') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Status
                                    </td>

                                    <td>
                                        <?php
                                        $statusType = [0 => 'Scheduled', 1 => 'In-progress', 2 => 'Draft', 3 => 'Completed', 4 => 'Cancelled'];
                                        echo $statusType[$model->status];
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Assigned By
                                    </td>

                                    <td>
                                        <?= ucfirst($model->createdBy->first_name) . " " . ucfirst($model->createdBy->last_name) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Non-Compliance
                                    </td>

                                    <td>
                                        <?= $nonCompliance ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Chronic Issues
                                    </td>
                                    <td>
                                        <?= $chronicIssues ?>
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix">&nbsp;</div>
            <div class="clearfix">&nbsp;</div>
            <div class="clearfix">&nbsp;</div>


            <div>
                <div class="col-sm-12 nopadding h4color " style="margin-bottom: 10px;  ">
                    <div class="col-md-6 "><h3><span class="text-center"><b>Comparison</b></span>
                        </h3></div>
                </div>
            </div>
            <!---------------------------------------Fourth Section End here------------------------------------- -->
            <div id="quickview" class="tab-pane fade">

                <div class="content">
                    <div class="col-xs-12 margintables">
                        <div class="box">
                            <div class="clearfix">&nbsp;</div>

                            <?php
                            $currentAuditSchId = Yii::$app->utils->decryptData($_GET['id']);
                            $auditDatesLast = $modelAudit->getAuditCompareDates($modelAudit->audit_id, $currentAuditSchId);
                            $auditChildIds = ArrayHelper::getColumn($auditDatesLast, 'audit_schedule_id');
                            $auditDates = array_reverse($auditDatesLast);
                            $audit_count = count($auditDates);

                            ?>
                            <div class="box-body table-responsive no-padding">
                                <table class="table table-hover table-bordered">
                                    <tbody>
                                    <tr>
                                        <th style="text-align:center" colspan="<?php echo $audit_count + 4; ?>">
                                            <h4 class="box-title h4color text-center"
                                                style="text-align: center"><?= strtoupper($modelAudit->hotel->hotel_name) ?></h4>
                                            <h4 class="box-title text-center h4color"
                                                style="text-align: center"><?php echo strtoupper($modelAudit->checklist->cl_name); ?></h4>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center" colspan="2" rowspan="2"
                                            class="text-center"><?php echo strtoupper($modelAudit->checklist->cl_name); ?>
                                        </th>

                                    </tr>
                                    <tr>

                                        <?php
                                        foreach ($auditDates as $audits) {
                                            echo '<td class="text-center">' . date('M Y', strtotime($audits['start_date'])) . '</td>';
                                        }
                                        echo '<th rowspan="2" class="text-center">VARIANCE</th><th rowspan="2" class="text-center">% of Increase / Decrease (-/ +)</th>';
                                        ?>
                                    </tr>
                                    <tr style="background-color: #cfe8d0;">
                                        <td class="text-center">S.No</td>
                                        <td class="text-center">Sections</td>
                                        <?php for ($x = 1; $x <= $audit_count; $x++) { ?>
                                            <td class="text-center">SCORE OBTAINED</td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <?php
                                        $colorCode = '#3bb540';
                                        $scoreArray =[];
                                       foreach ($auditDates as $index => $audits) {
                            $auditData = $modelAudit->getAuditList($modelAudit->audit_id, $audits['end_date'], 2);
                            $datesArray = array();
                            foreach ($auditData as $auditlist) {
                                $scoreArray[$auditlist['s_section_name']][($index == 0 ? "prev" : "curr")] = $auditlist['score'];
                            }
                        }

                        if ($audit_count > 1) {
                            //Filling empty year values with zero
                            foreach ($scoreArray as $key => $tempArray) {
                                if (!isset($scoreArray[$key]['prev'])) {
                                    $scoreArray[$key]['prev'] = -1;
                                } elseif (!isset($scoreArray[$key]['curr'])) {
                                    $scoreArray[$key]['curr'] = -1;
                                }
                            }
                        }

                        if ($audit_count > 1) {
                            $loopC = 1;
                            foreach ($scoreArray as $subsectionNameKey => $scores) {
                                echo '<tr><td class="text-center">' . $loopC . '</td><td class="text-center">' . $subsectionNameKey . '</td>';
                                $prevScore = $scores['prev'];
                                $currScore = $scores['curr'];
                                $prevColorCode = '#ff0000';
                                $currColorCode = '#ff0000';
                                //Previouus year value
                                if ($prevScore != -1) { // NO value case i.e. NA case
                                    if ($prevScore >= 80) {
                                        $prevColorCode = '#3bb540';
                                    } elseif ($prevScore <= 79 && $prevScore >= 61) {
                                        $prevColorCode = '#d6d63f';
                                    }
                                    echo '<td class="text-center"><div class="circle" style="background: ' . $prevColorCode . '"></div>' . $prevScore . '</td>';
                                } else {
                                    echo '<td class="text-center" > -- </td>';
                                }
                                //Current year value            
                                if ($currScore != -1) { // NO value case i.e. NA case
                                    if ($currScore >= 80) {
                                        $currColorCode = '#3bb540';
                                    } elseif ($currScore <= 79 && $currScore >= 61) {
                                        $currColorCode = '#d6d63f';
                                    }
                                    echo '<td class="text-center"><div class="circle" style="background: ' . $currColorCode . '"></div>' . $currScore . '</td>';
                                } else {
                                    echo '<td class="text-center" > -- </td>';
                                }

                                if ($prevScore == -1 || $currScore == -1) {
                                    echo '<td class="text-center" > -- </td>';
                                    echo '<td class="text-center" > -- </td>';
                                } else {
                                    //Calculating variance if both values available
                                    $varience = $currScore - $prevScore;
                                    $varClass = '';
                                    if ($varience < 0) {
                                        $varClass = 'red';
                                    }
                                    echo '<td class="text-center ' . $varClass . '">' . $varience . '</td>';
                                    //Calculating Percentage if both values available
                                    $perVar = '';
                                    if ($prevScore != 0) {
                                        $perVar = ($varience / $prevScore) * 100;
                                    } else {
                                        $perVar = $currScore;
                                    }
                                    $perTextClass = '';
                                    if ($perVar < 0) {
                                        $perTextClass = 'red';
                                    }
                                    echo '<td class="text-center ' . $perTextClass . '">' . round($perVar, 2) . '%</td>';
                                }
                                echo '</tr>';
                                $loopC++;
                            }
                        } else {
                            $loopC = 1;
                            foreach ($scoreArray as $subsectionNameKey => $scores) {
                                echo '<tr><td class="text-center">' . $loopC . '</td><td class="text-center">' . $subsectionNameKey . '</td>';
                                $scoreCount = count($scores);
                                foreach ($scores as $score) {
                                    if ($score >= 80) {
                                        $colorCode = '#3bb540';
                                    } elseif ($score <= 79 && $score >= 61) {
                                        $colorCode = '#d6d63f';
                                    } else {
                                        $colorCode = '#ff0000';
                                    }
                                    echo '<td class="text-center"><div class="circle" style="background: ' . $colorCode . '"></div>' . $score . '</td>';
                                    echo '<td class="text-center">-</td>';
                                    echo '<td class="text-center">-</td>';
                                }
                                echo '</tr>';
                                $loopC++;
                            }
                        }

                                        echo '<tr><td colspan="2" class="text-center"><b>Audit Score</b></td>';
                                        $innerLoopC = 1;

                                        $finalScore = [];

                                        foreach ($auditChildIds as $childId) {
                                            $finalScore[] = AuditsSchedules::getAuditScore($childId);

                                        }
                                        $scoreCount = count($finalScore);
                                        $finalScore = array_reverse($finalScore);
                                        foreach ($finalScore as $score) {
                                            $scores = $finalScore;
                                            $imageName = '';
                                            if ($score >= 80) {
                                                $colorCode = '#3bb540';
                                                $imageName = 'scoreGreen.png';
                                            } elseif ($score <= 79 && $score >= 61) {
                                                $colorCode = '#d6d63f';
                                                $imageName = 'scoreAvg.png';
                                            } else {
                                                $colorCode = '#ff0000';
                                                $imageName = 'scoreLow.png';
                                            }

                                            echo '<td class="text-center">
<span style="float: left !important;">
' . Html::img(Yii::getAlias("@webroot") . "/img/" . $imageName, ["class" => "checkedimage", "style" => "float:left !important;width:3%;height:3%"]) . '
</span><span style="float: right">' . $score . '</span></td>';

                                            //echo '<td class="text-center"><div class="circle" style="background: ' . $colorCode . '"></div>' . $score . '</td>';
                                            if ($innerLoopC > 1) {
                                                $varience = $scores[1] - $scores[0];
                                                $textClass = '';
                                                if ($varience && $varience < 0) {
                                                    $textClass = 'red';
                                                }
                                                echo '<td class="text-center ' . $textClass . '">' . $varience . '</td>';
                                                $perVar = '';
                                                if ($scores[0] != 0) {
                                                    $perVar = ($varience / $scores[0]) * 100;
                                                } else {
                                                    $perVar = $scores[1];
                                                }
                                                $textClass = '';
                                                if ($perVar && $perVar < 0) {
                                                    $textClass = 'red';
                                                }

                                                echo '<td class="text-center ' . $textClass . '">' . round($perVar, 2) . '%</td>';
                                            }

                                            if ($scoreCount == 1) {
                                                echo '<td class="text-center">-</td>';
                                                echo '<td class="text-center">-</td>';
                                            }
                                            $innerLoopC++;
                                        }
                                        echo '</tr>';

                                        ?>

                                    </tr>


                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <table class="table table-hover table-bordered">
                                    <tbody>
                                    <tr>
                                        <th class="">
                                            <div id="base">
                                                <?php echo Html::img(Yii::getAlias('@webroot') . '/img/upArrow.png', ['class' => "checkedimage", "style" => "width:8%;height:5%"]); ?>

                                            </div>
                                        </th>
                                        <th class="text-center greenbk" colspan="2"
                                            style="background:#3bb540; color: white;">
                                            80-100 = Green
                                        </th>
                                        <th class="text-center" colspan="2">
                                            Good / Excellent
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="">
                                            <div id="base2">
                                                <?php echo Html::img(Yii::getAlias('@webroot') . '/img/upArrowYellow.jpg', ['class' => "checkedimage", "style" => "width:8%;height:5%"]); ?>

                                            </div>
                                        </th>
                                        <th class="text-center yellowbk" colspan="2"
                                            style="background: #d6d63f; color: white;">
                                            61-79 = Yellow
                                        </th>
                                        <th class="text-center" colspan="2">
                                            Average
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="">
                                            <div id="base1">
                                                <?php echo Html::img(Yii::getAlias('@webroot') . '/img/downArrow.png', ['class' => "checkedimage", "style" => "width:8%;height:5%"]); ?>

                                            </div>
                                        </th>
                                        <th class="text-center redbk" colspan="2"
                                            style="background: red; color: white;">
                                            60 below is Red
                                        </th>
                                        <th class="text-center" colspan="2">
                                            Poor / Fair
                                        </th>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
            </div>
            <?php
            $nonComplianceQuestions = [];
            $nonComplianceAnswers = [];

            if ($modelAnswers) {
                foreach ($modelAnswers as $modelAnswer) {
                    foreach ($modelAnswer as $subSections) {
                        if ($subSections['questions']) {
                            foreach ($subSections['questions'] as $question) {
                                $eachAnswers = $question['checkListAnswers'];
                                if ($eachAnswers['not_applicable'] == "0" && $eachAnswers['answer_value'] == 0) {
                                    $nonComplianceQuestions[] = $question['question_id'];
                                    $nonComplianceAnswers[] = $eachAnswers['answer_id'];
                                }
                            }
                        }
                    }
                }
            }
            ?>
            
            <div class="page-break"></div>
            <!-- Non Compliance starts here -->
            <div>
                <div class="col-sm-12 nopadding h4color " style="margin-bottom: 10px;  ">
                    <div class="col-md-6"><h3><span class="text-center"><b>Non-Compliance</b></span>
                        </h3></div>
                </div>
            </div>
            <!---------------------------------------Second Section Start here------------------------------------- -->
            <div id="non-compliance" class="tab-pane fade">

                <div class="col-sm-12">
                    <div class="col-md-12 nopadding">
                        <div class="" id="nc_accordion" aria-expanded="true">
                            <?php
                            if ($nonComplianceQuestions && $modelAnswers) {
                                foreach ($modelAnswers as $modelAnswer) {
                                    foreach ($modelAnswer as $subSections) {
                                        $questions = array_filter(ArrayHelper::getColumn($subSections['questions'], function ($element) {
                                                    if ($element['checkListAnswers']) {
                                                        return $element['question_id'];
                                                    }
                                                }));
                                                $validation = false;
                                        foreach($nonComplianceQuestions as $nonCompQuestion){
                                            if(in_array($nonCompQuestion,$questions )){
                                               $validation = true;
                                               break;
                                            }
                                        }
                                        if(!$validation){
                                            continue;
                                        }
                                        ?>
                                        <!-- -----------------------------Upper div-------------------------- -->
                                        <div class="col-sm-12 nopadding" style="margin-bottom: 10px;">
                                            <div class="ol-sm-12">
                                                <strong>Section: </strong><?= $subSections['sectionName'] ?>
                                            </div>
                                            <div class="ol-sm-12">
                                                <strong>Subsection: </strong><?= $subSections['subSectionName'] ? $subSections['subSectionName'] : 'Dynamic questions' ?>
                                            </div>
                                        </div>


                                        <!-- -----------------------------Questiona start here-------------------------- -->
                                        <div class="ol-sm-12">
                                            <div class="col-md-12 nopadding">
                                                <div class="" id="accordion"
                                                     aria-expanded="true">
                                                    <?php

                                                    $n = 1;
                                                    if ($subSections['questions']) {
                                                        foreach ($subSections['questions'] as $question) {
                                                            $eachAnswers = $question['checkListAnswers'];
                                                            if($eachAnswers['answer_value']){
                                                                continue;
                                                            }
                                                            ?>
                                                            <?= $n ?>:
                                                            &nbsp; <?= $question['q_text'] ?>
                                                            &nbsp;&nbsp;
                                                            <?php if ($eachAnswers['not_applicable'] == "1") { ?>

                                                                <?php

                                                                echo Html::img(Yii::getAlias('@webroot') . '/img/checkBoxCheckedImage.png', ['class' => "checkedimage", "style" => "width:3%;height:%"]);
                                                                ?>
                                                                <label for="APRYes"> Not
                                                                    Applicable </label>
                                                            <?php } ?>
                                                            <?php

                                                            if ($eachAnswers['not_applicable'] == "0" && $eachAnswers['answer_value'] == 1) {
                                                                /*  ?>
                                                                  <span style="color:green;margin-right: 5%;"
                                                                        class="pull-right"><b><?= "Compliant"; ?></b></span>
                                                                  <?php*/

                                                            } elseif ($eachAnswers['not_applicable'] == "0" && $eachAnswers['answer_value'] == 0) {
                                                                ?>
                                                                <span style="color:red;margin-right: 10%;"
                                                                      class="pull-right">
                                                                                <b><?= "NC"; ?></b></span>
                                                                <?php
                                                            }
                                                            ?>


                                                            <div class="col-sm-12 col-md-12">

                                                                <?php


                                                                switch ($question['q_response_type']) {
                                                                    case '1':
                                                                        ?>
                                                                        <div class="col-sm-3">
                                                                            <?php
                                                                            if (($eachAnswers['not_applicable'] == '0') && $eachAnswers['answer_value'] == '1') {
                                                                                echo Html::img(Yii::getAlias('@webroot') . '/img/radioButtonChecked.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);

                                                                                ?>
                                                                                <label for="APRYes">True</label>
                                                                            <?php } ?>

                                                                        </div>
                                                                        <div class="col-sm-3">

                                                                            <?php
                                                                            if (($eachAnswers['not_applicable'] == '0') && $eachAnswers['answer_value'] == '0') {
                                                                                echo Html::img(Yii::getAlias('@webroot') . '/img/radioButtonChecked.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);
                                                                                ?>
                                                                                <label for="APRYes">False</label>

                                                                            <?php } ?>
                                                                        </div>
                                                                        <?php
                                                                        break;
                                                                    case '2':
                                                                        ?>
                                                                        <div class="col-sm-3">

                                                                            <?php
                                                                            if (($eachAnswers['not_applicable'] == '0') && $eachAnswers['answer_value'] == '1') {
                                                                                echo Html::img(Yii::getAlias('@webroot') . '/img/radioButtonChecked.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);

                                                                                ?>
                                                                                <label for="APRYes">Yes</label>
                                                                            <?php } ?>

                                                                        </div>
                                                                        <div class="col-sm-3">

                                                                            <?php
                                                                            if (($eachAnswers['not_applicable'] == '0') && $eachAnswers['answer_value'] == '0') {
                                                                                echo Html::img(Yii::getAlias('@webroot') . '/img/radioButtonChecked.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);

                                                                                ?>
                                                                                <label for="APRYes">No</label>
                                                                            <?php } ?>
                                                                        </div>
                                                                        <?php
                                                                        break;
                                                                    case '3':
                                                                        ?>

                                                                        <div class="slidecontainer col-md-6">
                                                                            <?php $ratingValue = @unserialize($eachAnswers['options_values']);
                                                                            if (is_array($ratingValue) && isset($ratingValue[0])) {
                                                                                $ratingValue = $ratingValue[0];
                                                                            } else {
                                                                                $ratingValue = 0;
                                                                            }
                                                                            echo ($eachAnswers['not_applicable'] == '1') ? '' : 'Rating: ' . $ratingValue;
                                                                            //echo "Rating: " . $ratingValue;
                                                                            ?>
                                                                        </div>
                                                                        <?php
                                                                        break;
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


                                                                            <?php if ($inputType == 'radio') { ?>

                                                                                <?php
                                                                                if (($eachAnswers['not_applicable'] == '0') && in_array($questionnairekey, $questionnaireOptionsValue)) {
                                                                                    echo Html::img(Yii::getAlias('@webroot') . '/img/radioButtonChecked.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);

                                                                                    ?>
                                                                                    <label for=""><?= $questionnairevalue ?></label>

                                                                                <?php } ?>
                                                                            <?php } else if ($inputType == 'checkbox') { ?>
                                                                                <?php
                                                                                if (($eachAnswers['not_applicable'] == '0') && in_array($questionnairekey, $questionnaireOptionsValue)) {
                                                                                    echo Html::img(Yii::getAlias('@webroot') . '/img/checkBoxCheckedImage.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);

                                                                                    ?>
                                                                                    <label for=""><?= $questionnairevalue ?></label>
                                                                                <?php } ?>
                                                                            <?php } ?>


                                                                        </div>
                                                                    <?php }
                                                                } ?>


                                                            </div>

                                                            <?php $anwerAttachments = $eachAnswers['answerAttachments'];
                                                            $anwerAttachments = $anwerAttachments ? $anwerAttachments : [];
                                                            ?>

                                                            <div class="col-sm-12">
                                                                <?php $fileAttach = array(); ?>
                                                                <?php if ($anwerAttachments) { ?>
                                                                    <label>Attachments :</label>
                                                                <?php } ?>
                                                                <?php foreach ($anwerAttachments as $anwerAttachmentsValues) { ?>

                                                                    <div class="col-sm-6">
                                                                        <?php echo $anwerAttachmentsValues['answer_attachment_path']; ?>

                                                                    </div>
                                                                <?php } ?> <br>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <?php if (isset($eachAnswers['observation_text']) && $eachAnswers['observation_text']) { ?>
                                                                    <label>Observation Text : </label>
                                                                    <p><?= $eachAnswers['observation_text'] ?> </p>
                                                                <?php } ?>
                                                            </div>


                                                            <?php $n++;
                                                        }
                                                        ?>
                                                        <div class="clearfix"></div>
                                                        <div class="clearfix"></div>
                                                        <div class="clearfix"></div>
                                                        <div class="clearfix"></div>
                                                        <?php
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        // echo '<div class="page-break"></div>';
                                    }
                                }
                            } else {
                                echo "No non-compliances found";
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-break"></div>
            <!-- Non Compliance starts here -->
            <div>
                <div class="col-sm-12 nopadding h4color " style="margin-bottom: 10px;  ">
                    <div class="col-md-6"><h3><span class="text-center"><b>Chronic</b></span>
                        </h3></div>
                </div>
            </div>
            <!---------------------------------------Second Section Start here------------------------------------- -->
            <div id="chronic_issues" class="tab-pane fade">
                <div class="col-sm-12">
                    <div class="col-md-12 nopadding">
                        <div class="" id="nc_accordion" aria-expanded="true">
                            <?php
                            $chronicTicketsAnswerList = array_column($chronicTickets,'answer_id');
                            if ($chronicTicketsAnswerList && $modelAnswers) {
                                foreach ($modelAnswers as $modelAnswer) {
                                    foreach ($modelAnswer as $subSections) {
                                        $answersList = array_filter(ArrayHelper::getColumn($subSections['questions'], function ($element) {
                                                    if ($element['checkListAnswers']) {
                                                        return $element['checkListAnswers']['answer_id'];
                                                    }
                                                }));
                                                $validation = false;
                                        foreach($chronicTicketsAnswerList as $chronicTicketsAnswer){
                                            if(in_array($chronicTicketsAnswer,$answersList )){
                                               $validation = true;
                                               break;
                                            }
                                        }
                                        if(!$validation){
                                            continue;
                                        }
                                        ?>
                                        <!-- -----------------------------Upper div-------------------------- -->
                                        <div class="col-sm-12 nopadding" style="margin-bottom: 10px;">
                                            <div class="ol-sm-12">
                                                <strong>Section: </strong><?= $subSections['sectionName'] ?>
                                            </div>
                                            <div class="ol-sm-12">
                                                <strong>Subsection: </strong><?= $subSections['subSectionName'] ? $subSections['subSectionName'] : 'Dynamic questions' ?>
                                            </div>
                                        </div>


                                        <!-- -----------------------------Questiona start here-------------------------- -->
                                        <div class="col-sm-12">
                                            <div class="col-md-12 nopadding">
                                                <div class="" id="accordion"
                                                     aria-expanded="true">
                                                    <?php

                                                    $n = 1;
                                                    if ($subSections['questions']) {
                                                        foreach ($subSections['questions'] as $question) {
                                                            $eachAnswers = $question['checkListAnswers'];
                                                            if(!in_array( $eachAnswers['answer_id'],$chronicTicketsAnswerList)){
                                                                continue;
                                                            }
                                                            ?>
                                                            <?= $n ?>:
                                                            &nbsp; <?= $question['q_text'] ?>
                                                            &nbsp;&nbsp;
                                                            <?php if ($eachAnswers['not_applicable'] == "1") { ?>

                                                                <?php

                                                                echo Html::img(Yii::getAlias('@webroot') . '/img/checkBoxCheckedImage.png', ['class' => "checkedimage", "style" => "width:3%;height:%"]);
                                                                ?>
                                                                <label for="APRYes"> Not
                                                                    Applicable </label>
                                                            <?php } ?>
                                                            <?php

                                                            if ($eachAnswers['not_applicable'] == "0" && $eachAnswers['answer_value'] == 1) {
                                                                /*  ?>
                                                                  <span style="color:green;margin-right: 5%;"
                                                                        class="pull-right"><b><?= "Compliant"; ?></b></span>
                                                                  <?php*/

                                                            } elseif ($eachAnswers['not_applicable'] == "0" && $eachAnswers['answer_value'] == 0) {
                                                                ?>
                                                                <span style="color:red;margin-right: 10%;"
                                                                      class="pull-right">
                                                                                <b><?= "NC"; ?></b></span>
                                                                <?php
                                                            }
                                                            ?>


                                                            <div class="col-sm-12 col-md-12">

                                                                <?php


                                                                switch ($question['q_response_type']) {
                                                                    case '1':
                                                                        ?>
                                                                        <div class="col-sm-3">
                                                                            <?php
                                                                            if (($eachAnswers['not_applicable'] == '0') && $eachAnswers['answer_value'] == '1') {
                                                                                echo Html::img(Yii::getAlias('@webroot') . '/img/radioButtonChecked.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);

                                                                                ?>
                                                                                <label for="APRYes">True</label>
                                                                            <?php } ?>

                                                                        </div>
                                                                        <div class="col-sm-3">

                                                                            <?php
                                                                            if (($eachAnswers['not_applicable'] == '0') && $eachAnswers['answer_value'] == '0') {
                                                                                echo Html::img(Yii::getAlias('@webroot') . '/img/radioButtonChecked.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);
                                                                                ?>
                                                                                <label for="APRYes">False</label>

                                                                            <?php } ?>
                                                                        </div>
                                                                        <?php
                                                                        break;
                                                                    case '2':
                                                                        ?>
                                                                        <div class="col-sm-3">

                                                                            <?php
                                                                            if (($eachAnswers['not_applicable'] == '0') && $eachAnswers['answer_value'] == '1') {
                                                                                echo Html::img(Yii::getAlias('@webroot') . '/img/radioButtonChecked.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);

                                                                                ?>
                                                                                <label for="APRYes">Yes</label>
                                                                            <?php } ?>

                                                                        </div>
                                                                        <div class="col-sm-3">

                                                                            <?php
                                                                            if (($eachAnswers['not_applicable'] == '0') && $eachAnswers['answer_value'] == '0') {
                                                                                echo Html::img(Yii::getAlias('@webroot') . '/img/radioButtonChecked.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);

                                                                                ?>
                                                                                <label for="APRYes">No</label>
                                                                            <?php } ?>
                                                                        </div>
                                                                        <?php
                                                                        break;
                                                                    case '3':
                                                                        ?>

                                                                        <div class="slidecontainer col-md-6">
                                                                            <?php $ratingValue = @unserialize($eachAnswers['options_values']);
                                                                            if (is_array($ratingValue) && isset($ratingValue[0])) {
                                                                                $ratingValue = $ratingValue[0];
                                                                            } else {
                                                                                $ratingValue = 0;
                                                                            }
                                                                            echo ($eachAnswers['not_applicable'] == '1') ? '' : 'Rating: ' . $ratingValue;
                                                                            //echo "Rating: " . $ratingValue;
                                                                            ?>
                                                                        </div>
                                                                        <?php
                                                                        break;
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


                                                                            <?php if ($inputType == 'radio') { ?>

                                                                                <?php
                                                                                if (($eachAnswers['not_applicable'] == '0') && in_array($questionnairekey, $questionnaireOptionsValue)) {
                                                                                    echo Html::img(Yii::getAlias('@webroot') . '/img/radioButtonChecked.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);

                                                                                    ?>
                                                                                    <label for=""><?= $questionnairevalue ?></label>

                                                                                <?php } ?>
                                                                            <?php } else if ($inputType == 'checkbox') { ?>
                                                                                <?php
                                                                                if (($eachAnswers['not_applicable'] == '0') && in_array($questionnairekey, $questionnaireOptionsValue)) {
                                                                                    echo Html::img(Yii::getAlias('@webroot') . '/img/checkBoxCheckedImage.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);

                                                                                    ?>
                                                                                    <label for=""><?= $questionnairevalue ?></label>
                                                                                <?php } ?>
                                                                            <?php } ?>


                                                                        </div>
                                                                    <?php }
                                                                } ?>


                                                            </div>

                                                            <?php $anwerAttachments = $eachAnswers['answerAttachments'];
                                                            $anwerAttachments = $anwerAttachments ? $anwerAttachments : [];
                                                            ?>

                                                            <div class="col-sm-12">
                                                                <?php $fileAttach = array(); ?>
                                                                <?php if ($anwerAttachments) { ?>
                                                                    <label>Attachments :</label>
                                                                <?php } ?>
                                                                <?php foreach ($anwerAttachments as $anwerAttachmentsValues) { ?>

                                                                    <div class="col-sm-6">
                                                                        <?php echo $anwerAttachmentsValues['answer_attachment_path']; ?>

                                                                    </div>
                                                                <?php } ?> <br>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <?php if (isset($eachAnswers['observation_text']) && $eachAnswers['observation_text']) { ?>
                                                                    <label>Observation Text : </label>
                                                                    <p><?= $eachAnswers['observation_text'] ?> </p>
                                                                <?php } ?>
                                                            </div>


                                                            <?php $n++;
                                                        }
                                                        ?>
                                                        <div class="clearfix"></div>
                                                        <div class="clearfix"></div>
                                                        <div class="clearfix"></div>
                                                        <div class="clearfix"></div>
                                                        <?php
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        // echo '<div class="page-break"></div>';
                                    }
                                }
                            } else {
                                echo "No chronic issues found";
                            } ?>
                        </div>
                    </div>
                </div>

            </div>
            <div class="page-break"></div>

            <div>
                <div class="col-sm-12 nopadding h4color " style="margin-bottom: 10px;  ">
                    <div class="col-md-6"><h3><span class="text-center"><b>Audit Questionnaire</b></span>
                        </h3></div>
                </div>
            </div>
            
            <!---------------------------------------Second Section Start here------------------------------------- -->
            <div id="questionnare" class="tab-pane fade">

                <div class="col-sm-12">
                    <div class="col-md-12 nopadding">
                        <div class="" id="accordion" aria-expanded="true">
                            <?php
                            if ($modelAnswers) {
                                foreach ($modelAnswers as $modelAnswer) {
                                    foreach ($modelAnswer as $subSections) {
                                        ?>
                                        <!-- -----------------------------Upper div-------------------------- -->
                                        <div class="col-sm-12 nopadding" style="margin-bottom: 10px;">
                                            <div class="ol-sm-12">
                                                <strong>Section: </strong><?= $subSections['sectionName'] ?>
                                            </div>
                                            <div class="ol-sm-12">
                                                <strong>Subsection: </strong><?= $subSections['subSectionName'] ? $subSections['subSectionName'] : 'Dynamic questions' ?>
                                            </div>
                                        </div>


                                        <!-- -----------------------------Questiona start here-------------------------- -->
                                        <div class="ol-sm-12">
                                            <div class="col-md-12 nopadding">
                                                <div class="" id="accordion"
                                                     aria-expanded="true">
                                                    <?php

                                                    $n = 1;
                                                    if ($subSections['questions']) {
                                                        foreach ($subSections['questions'] as $question) {
                                                            $eachAnswers = $question['checkListAnswers'];
                                                            ?>
                                                            <?= $n ?>:
                                                            &nbsp; <?= $question['q_text'] ?>
                                                            &nbsp;&nbsp;
                                                            <?php if ($eachAnswers['not_applicable'] == "1") { ?>

                                                                <?php

                                                                echo Html::img(Yii::getAlias('@webroot') . '/img/checkBoxCheckedImage.png', ['class' => "checkedimage", "style" => "width:3%;height:%"]);
                                                                ?>
                                                                <label for="APRYes"> Not
                                                                    Applicable </label>
                                                            <?php } ?>
                                                            <?php

                                                            if ($eachAnswers['not_applicable'] == "0" && $eachAnswers['answer_value'] == 1) {
                                                                /*  ?>
                                                                  <span style="color:green;margin-right: 5%;"
                                                                        class="pull-right"><b><?= "Compliant"; ?></b></span>
                                                                  <?php*/

                                                            } elseif ($eachAnswers['not_applicable'] == "0" && $eachAnswers['answer_value'] == 0) {
                                                        
                                                                ?>
                                                                <span style="color:red;margin-right: 10%;"
                                                                      class="pull-right">
                                                                                <b><?= "NC"; ?></b></span>
                                                                <?php
                                                            }
                                                            ?>


                                                            <div class="col-sm-12 col-md-12">

                                                                <?php


                                                                switch ($question['q_response_type']) {
                                                                    case '1':
                                                                        ?>
                                                                        <div class="col-sm-3">
                                                                            <?php
                                                                            if (($eachAnswers['not_applicable'] == '0') && $eachAnswers['answer_value'] == '1') {
                                                                                echo Html::img(Yii::getAlias('@webroot') . '/img/radioButtonChecked.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);

                                                                                ?>
                                                                                <label for="APRYes">True</label>
                                                                            <?php } ?>

                                                                        </div>
                                                                        <div class="col-sm-3">

                                                                            <?php
                                                                            if (($eachAnswers['not_applicable'] == '0') && $eachAnswers['answer_value'] == '0') {
                                                                                echo Html::img(Yii::getAlias('@webroot') . '/img/radioButtonChecked.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);
                                                                                ?>
                                                                                <label for="APRYes">False</label>

                                                                            <?php } ?>
                                                                        </div>
                                                                        <?php
                                                                        break;
                                                                    case '2':
                                                                        ?>
                                                                        <div class="col-sm-3">

                                                                            <?php
                                                                            if (($eachAnswers['not_applicable'] == '0') && $eachAnswers['answer_value'] == '1') {
                                                                                echo Html::img(Yii::getAlias('@webroot') . '/img/radioButtonChecked.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);

                                                                                ?>
                                                                                <label for="APRYes">Yes</label>
                                                                            <?php } ?>

                                                                        </div>
                                                                        <div class="col-sm-3">

                                                                            <?php
                                                                            if (($eachAnswers['not_applicable'] == '0') && $eachAnswers['answer_value'] == '0') {
                                                                                echo Html::img(Yii::getAlias('@webroot') . '/img/radioButtonChecked.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);

                                                                                ?>
                                                                                <label for="APRYes">No</label>
                                                                            <?php } ?>
                                                                        </div>
                                                                        <?php
                                                                        break;
                                                                    case '3':
                                                                        ?>

                                                                        <div class="slidecontainer col-md-6">
                                                                            <?php $ratingValue = @unserialize($eachAnswers['options_values']);
                                                                            if (is_array($ratingValue) && isset($ratingValue[0])) {
                                                                                $ratingValue = $ratingValue[0];
                                                                            } else {
                                                                                $ratingValue = 0;
                                                                            }
                                                                            echo ($eachAnswers['not_applicable'] == '1') ? '' : 'Rating: ' . $ratingValue;
                                                                            //echo "Rating: " . $ratingValue;
                                                                            ?>
                                                                        </div>
                                                                        <?php
                                                                        break;
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


                                                                            <?php if ($inputType == 'radio') { ?>

                                                                                <?php
                                                                                if (($eachAnswers['not_applicable'] == '0') && in_array($questionnairekey, $questionnaireOptionsValue)) {
                                                                                    echo Html::img(Yii::getAlias('@webroot') . '/img/radioButtonChecked.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);

                                                                                    ?>
                                                                                    <label for=""><?= $questionnairevalue ?></label>

                                                                                <?php } ?>
                                                                            <?php } else if ($inputType == 'checkbox') { ?>
                                                                                <?php
                                                                                if (($eachAnswers['not_applicable'] == '0') && in_array($questionnairekey, $questionnaireOptionsValue)) {
                                                                                    echo Html::img(Yii::getAlias('@webroot') . '/img/checkBoxCheckedImage.png', ['class' => "checkedimage", "style" => "width:4%;height:4%"]);

                                                                                    ?>
                                                                                    <label for=""><?= $questionnairevalue ?></label>
                                                                                <?php } ?>
                                                                            <?php } ?>


                                                                        </div>
                                                                    <?php }
                                                                } ?>


                                                            </div>

                                                            <?php $anwerAttachments = $eachAnswers['answerAttachments'];
                                                            $anwerAttachments = $anwerAttachments ? $anwerAttachments : [];
                                                            ?>

                                                            <div class="col-sm-12">
                                                                <?php $fileAttach = array(); ?>
                                                                <?php if ($anwerAttachments) { ?>
                                                                    <label>Attachments :</label>
                                                                <?php } ?>
                                                                <?php foreach ($anwerAttachments as $anwerAttachmentsValues) { ?>

                                                                    <div class="col-sm-6">
                                                                        <?php echo $anwerAttachmentsValues['answer_attachment_path']; ?>

                                                                    </div>
                                                                <?php } ?> <br>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <?php if (isset($eachAnswers['observation_text']) && $eachAnswers['observation_text']) { ?>
                                                                    <label>Observation Text : </label>
                                                                    <p><?= $eachAnswers['observation_text'] ?> </p>
                                                                <?php } ?>
                                                            </div>


                                                            <?php $n++;
                                                        }
                                                        ?>
                                                        <div class="clearfix"></div>
                                                        <div class="clearfix"></div>
                                                        <div class="clearfix"></div>
                                                        <div class="clearfix"></div>
                                                        <?php
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        // echo '<div class="page-break"></div>';

                                    }
                                }
                            } else {
                                echo "No Data found";
                            } ?>
                        </div>
                    </div>
                </div>

            </div>
            
        </div>




