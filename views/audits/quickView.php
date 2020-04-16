<div class="content">
    <div class="col-xs-12 margintables">
        <div class="box">
            <div class="clearfix">&nbsp;</div>
            <!--<div class="text-center">
                <img  style="width: 200px;" src="http://optionmatrix.in/greenpark/Content/Images/Greenpark_logo.png">
            </div>-->
            <?php
            /* $audit_count = \app\models\AuditsSchedules::find()->where([
              'audit_id' => $modelAudit->audit_id,'status'=>3
              ])->count(); */


            $currentAuditSchId = Yii::$app->utils->decryptData($_GET['id']);
            $auditDatesLast = $modelAudit->getAuditCompareDates($modelAudit->audit_id, $currentAuditSchId);

            $auditChildIds = \yii\helpers\ArrayHelper::getColumn($auditDatesLast, 'audit_schedule_id');

            $auditDates = array_reverse($auditDatesLast);
            $audit_count = count($auditDates);
            ?>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-bordered">
                    <tbody>
                        <tr>
                            <th colspan="<?php echo $audit_count + 4; ?>">
                                <h4 class="box-title h4color text-center"><?= strtoupper($modelAudit->hotel->hotel_name) ?></h4>
                                <h4 class="box-title text-center h4color"><?php echo strtoupper($modelAudit->checklist->cl_name); ?> </h4>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="2" rowspan="2"
                                class="text-center"><?php echo strtoupper($modelAudit->checklist->cl_name); ?></th>

                        </tr>
                        <tr>
                            <!--  <td colspan="2"></td> -->
                            <?php
                            foreach ($auditDates as $audits) {
                                echo '<td class="text-center">' . date('M Y', strtotime($audits['start_date'])) . '</td>';
                            }
                            echo '<th rowspan="2" class="text-center">VARIANCE</th><th rowspan="2" class="text-center">% of Increase / Decrease (-/ +)</th>';
                            ?>


                        <!-- <td></td>
                        <td></td> -->
                        </tr>
                        <tr style="background-color: #cfe8d0;">


                            <td class="text-center">S.No</td>
                            <td class="text-center">Sections</td>
                            <?php for ($x = 1; $x <= $audit_count; $x++) { ?>
                                <td class="text-center">SCORE OBTAINED</td>
                            <?php } ?>

                        </tr>

                        <?php
                        $colorCode = '#3bb540';
                        $scoreArray = [];
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
                            $finalScore[] = \app\models\AuditsSchedules::getAuditScore($childId);
                        }

                        $scoreCount = count($finalScore);
                        $finalScore = array_reverse($finalScore);
                        foreach ($finalScore as $score) {
                            $scores = $finalScore;
                            //$score =  array_sum($scoreValue)/count($scoreValue);
                            if ($score >= 80) {
                                $colorCode = '#3bb540';
                            } elseif ($score <= 79 && $score >= 61) {
                                $colorCode = '#d6d63f';
                            } else {
                                $colorCode = '#ff0000';
                            }
                            echo '<td class="text-center"><div class="circle" style="background: ' . $colorCode . '"></div>' . $score . '</td>';
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


                    </tbody>
                </table>
            </div>
            <div>
                <table class="table table-hover table-bordered">
                    <tbody>
                        <tr>
                            <th class="">
                                <div id="base"></div>
                            </th>
                            <th class="text-center greenbk" colspan="2" style="color: white;">
                                80-100 = Green
                            </th>
                            <th class="text-center" colspan="2">
                                Good / Excellent
                            </th>
                        </tr>
                        <tr>
                            <th class="">
                                <div id="base2"></div>
                            </th>
                            <th class="text-center yellowbk" colspan="2" style="color: white;">
                                61-79 = Yellow
                            </th>
                            <th class="text-center" colspan="2">
                                Average
                            </th>
                        </tr>
                        <tr>
                            <th class="">
                                <div id="base1"></div>
                            </th>
                            <th class="text-center redbk" colspan="2" style="color: white;">
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