<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\search\SectionsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sections-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{input}",
            'options' => [
                'tag' => 'false'
            ]
        ],
    ]); ?>

<div class="col-lg-12 col-md-12 col-sm-12  showfilter">
	<div class="col-lg-4 col-md-6 col-sm-6">
            <?php
            echo $form->field($model, 'role_id')
                ->dropDownList(ArrayHelper::map(app\models\Roles::find()->where(['is_deleted' => 0])->all(), 'role_id', 'role_name'), [
                'prompt' => 'Select Role'
            ], [
                'class',
                'form-control'
            ])
                ->label(false);
            ?>
   </div>
    <div class="col-lg-4 col-md-6 col-sm-6 pull-right text-right">
       <?= Html::submitButton('Go', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Clear', Yii::$app->urlManager->createUrl(['/roles']),['class' => 'btn btn-default']) ?>
   </div>
    <?php ActiveForm::end(); ?>

</div>
</div>