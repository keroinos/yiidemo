<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\Tbloguin */

$this->title = 'Create Tbloguin';
$this->params['breadcrumbs'][] = ['label' => 'Tbloguins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbloguin-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
