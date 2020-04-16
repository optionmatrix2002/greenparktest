<?php

use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use dosamigos\tinymce\TinyMce;
?>

<div class="email-form">
<?php Pjax::begin(); ?>
<?php $form = ActiveForm::begin(['id'=>'email_template_form','action'=>'/preferences/save-email-template?id='.yii::$app->utils->encryptData($model->template_id)]); ?>

<?= $form->field($model, 'email_content')->widget(TinyMce::className(), [
    'options' => ['rows' => 20],
   // 'language' => 'eng',
    'clientOptions' => [
        'plugins' => [
            "advlist autolink lists link charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste"
        ],
        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
    ]
]);?>

 <div class="form-group">
 <input type="submit" class="btn btn-success" value="Update" >
    </div>

    <?php ActiveForm::end(); Pjax::end(); ?>
</div>


