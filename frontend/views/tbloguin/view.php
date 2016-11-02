<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Tbloguin */

$this->title = $model->Uin;
$this->params['breadcrumbs'][] = ['label' => 'Tbloguins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbloguin-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->Uin], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->Uin], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'Uin',
            'Passwd',
            'TowPasswd',
            'Nick',
            'username',
            'Server',
            'Owner_Id',
            'nType',
        ],
    ]) ?>

</div>
