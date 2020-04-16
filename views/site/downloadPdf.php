<?php
use app\models\Audits;
?>
<div id="quickview" class="tab-pane fade">

            <div class="content">
                <div class="col-xs-12 margintables">
                    <div class="box">
                        <div class="clearfix">&nbsp;</div>
                        <!--<div class="text-center">
                            <img  style="width: 200px;" src="http://optionmatrix.in/greenpark/Content/Images/Greenpark_logo.png">
                        </div>-->
                        <?php
                        $audit_count = \app\models\AuditsSchedules::find()->where([
                            'audit_id' => $_GET['id']
                        ])->count();
                       //$audit_count = 1;
					   $modelAudit = Audits::getAuditDetails($_GET['id']);
                        ?>
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-hover table-bordered">
                                <tbody>
                                <tr>
                                    <th colspan="<?php echo $audit_count+2; ?>">
                                        <h4 class="box-title h4color text-center" style="text-align: center">MARIGOLD BY GREENPARK</h4>
                                        <h4 class="box-title text-center h4color" style="text-align: center"><?php echo strtoupper($modelAudit->checklist->cl_name); ?> AUDIT</h4>
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="2" rowspan="2" class="text-center"><?php echo strtoupper($modelAudit->checklist->cl_name); ?> AUDIT</th>

                                </tr>
                                <tr>
                                    <!--  <td colspan="2"></td> -->
                                    <?php $auditData = $modelAudit->getAuditDates($modelAudit->audit_id,0);
                                    foreach ($auditData as $audits)    {
                                        echo '<td class="text-center">'.date('dS M Y',strtotime($audits['end_date'])).'</td>';
                                    }
                                    ?>


                                    <!-- <td></td>
                                    <td></td> -->
                                </tr>
                                <tr style="background-color: #cfe8d0;">


                                    <td class="text-center">S.No</td>
                                    <td class="text-center">AREAS</td>
                                    <?php for ($x = 1; $x <= $audit_count; $x++) { ?>
                                        <td class="text-center">SCORE OBTAINED</td>
                                    <?php }?>

                                </tr>
                                <tr>
                                    <?php
                                    $loopC = 1;
                                    $colorCode = '#3bb540';
                                    $auditData = $modelAudit->getAuditList($modelAudit->audit_id);
                                    foreach ($auditData as $audits)    {
                                        echo '<td class="text-center">'.$loopC.'</td>';
                                        echo '<td class="text-center">'.$audits['s_section_name'].'</td>';
                                        if($audits['score'] > 70){
                                            $colorCode = '#3bb540';
                                        }elseif($audits['score'] < 70 && $audits['score'] > 60){
                                            $colorCode = '#d6d63f';
                                        }else{
                                            $colorCode = '#ff0000';
                                        }
                                        echo '<td class="text-center"><div class="circle" style="background: '.$colorCode.'"></div>'.$audits['score'].'</td>';
                                        $loopC++;
                                    }
                                    ?>

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