<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Tbloguin */

$this->title = 'Update Tbloguin: ' . $model->Uin;
$this->params['breadcrumbs'][] = ['label' => 'Tbloguins', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Uin, 'url' => ['view', 'id' => $model->Uin]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbloguin-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
