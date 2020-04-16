<?php
/* @var $this View */
/* @var $content string */
use app\assets\AppAsset;
use yii\web\View;
use yii\helpers\Json;

$this->title = 'Questionnaire';

AppAsset::register($this);
View::registerCssFile(yii::$app->urlManager->createUrl('css/questionnaire.css'));
?>
    <div class="container-fluid">
        <h2>Questions for checklist <?= $checkListName?> </h2>
    </div>

    <div class="col-sm-12">
        <a
                href="<?= yii::$app->urlManager->createUrl('/check-lists/index'); ?>"
                class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
    </div>

    <div class="col-sm-12 nopadding" style="margin-bottom: 10px;">
        <div class="col-sm-12">
            <h4> <strong>Department : <?= $department_name; ?> </strong></h4>
        </div>
    </div>
    <!-------------------------------------------------------Quations Start here -------------------------------------->
<?php
if ($modelQuestionnaire) {
    foreach ($modelQuestionnaire as $subSections) {
        ?>
        <div class="col-sm-12 nopadding" style="margin-bottom: 10px;">
            <div class="col-sm-12">
                <!--  <strong>Department: <?= $department_name; ?> </strong>-->
            </div>
            <div class="col-sm-12">
                <strong>Section: <?= $subSections['sectionName'] ?> </strong>
            </div>

            <div class="col-sm-12">
                <strong>Subsection: <?= $subSections['subSectionName'] ? $subSections['subSectionName'] : 'Dynamic' ?></strong>
            </div>

        </div>
        <?php
        $i = 1;
        if ($subSections['questions']) {
            foreach ($subSections['questions'] as $eachQuestion) {
                ?>
                <div class="col-sm-12" id="">
                    <div class="col-md-12 nopadding">
                        <div class="panel-group"
                             aria-expanded="true">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="col-sm-12 marginTB10">
                                        <div class="col-sm-10 nopadding">
                                            <div class="col-sm-12">
                                                <?=$i;?>: &nbsp; <?= $eachQuestion['q_text']; ?>
                                            </div>
                                            <div class="col-sm-12">
                                                <?php
                                                switch ($eachQuestion['q_response_type']) {
                                                    case '1':
                                                        ?>
                                                        <div class="col-sm-6">
                                                            <input disabled type="radio" value="1"> <label for="APRYes">True</label>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <input disabled type="radio" value="2"> <label for="APRNo">False</label>
                                                        </div><?php
                                                        break;
                                                    case '2':
                                                        ?>
                                                        <div class="col-sm-6">
                                                            <input disabled type="radio" value="1"> <label for="APRYes">Yes</label>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <input disabled type="radio" value="2"> <label for="APRNo">No</label>
                                                        </div><?php
                                                        break;
                                                    case '3':
                                                        ?>
                                                        <div id="slidecontainer" class="col-md-6 rating-top">
                                                            <input disabled type="range" min="1" max="10" value="5"
                                                                   class="slider" id="myRange">

                                                        </div>
                                                    <?php
                                                }
                                                if (in_array($eachQuestion['q_response_type'], [
                                                    5,
                                                    4
                                                ])) {
                                                    $questionnaireOptions = @unserialize($eachQuestion['options']);
                                                    $responseType = $eachQuestion['q_response_type'];
                                                    $inputType = $responseType == 5 ? 'checkbox' : 'radio';

                                                    foreach ($questionnaireOptions as $questionnairekey => $questionnairevalue) {

                                                        ?>
                                                        <div class="col-sm-6">
                                                            <input disabled type="<?= $inputType ?>"
                                                                   value="<?=$questionnairekey?>"> <label for=""><?=$questionnairevalue?></label>
                                                        </div>
                                                    <?php  } ?>

                                                <?php } ?>
                                            </div>
                                        </div>

                                    </div>
                                    <?php
                                    $accessType = Json::decode($eachQuestion['q_access_type']);
                                    ?>
                                    <div class="col-sm-12">
                                        <div class="col-sm-2">Access:</div>
                                        <div class="col-sm-10">
                                            <div class="col-sm-3">
                                                <input type="checkbox"
                                                    <?php if(is_array($accessType)){ if(in_array("1",$accessType)) { echo 'checked'; } }?>
                                                       disabled> <label for="Camara Access">Camera Access</label>
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="checkbox"
                                                    <?php if(is_array($accessType)){ if(in_array("2",$accessType)) { echo 'checked'; } }?>
                                                       disabled> <label for="Chk'@q.QuestionID'Option2">Gallery Access</label>
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="checkbox"
                                                    <?php if(is_array($accessType)){ if(in_array("3",$accessType)) { echo 'checked'; } }?>
                                                       disabled> <label for="File Browser Access">File Access</label>
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="checkbox"
                                                    <?php if(is_array($accessType)){ if(in_array("4",$accessType)) { echo 'checked'; } }?>
                                                       disabled> <label for="All Access">All Access</label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php  ?>
                                    <div class="col-sm-12">
                                        <div class="col-sm-2">Priority:</div>
                                        <div class="col-sm-10">
                                            <div class="col-sm-3">
                                                <input type="radio"
                                                    <?php if($eachQuestion['q_priority_type']=="1"){ echo "checked";} ?>
                                                       disabled> <label for="High">High</label>
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="radio"
                                                    <?php if($eachQuestion['q_priority_type']=="2"){ echo "checked";} ?>
                                                       disabled> <label for="Medium">Medium</label>
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="radio"
                                                    <?php if($eachQuestion['q_priority_type']=="3"){ echo "checked";} ?>
                                                       disabled> <label for="Low">Low</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <div class="col-sm-2">Process Critical:</div>
                                        <div class="col-sm-10">
                                            <div class="col-sm-3">
                                                <input type="checkbox"
                                                    <?php if($eachQuestion['process_critical']=="1"){ echo "checked";} ?>
                                                       disabled> 
                                            </div>
                                            
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php  $i++; } }?>
    <?php } ?>
<?php }else{  echo 'NO Questions';}  ?>