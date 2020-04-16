<?php
/* @var $this View */

/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\View;
use kartik\growl\Growl;


?>


<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">


<body class="login">
<?php $this->beginBody() ?>




<?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

