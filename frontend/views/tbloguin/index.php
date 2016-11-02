<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\TbloguinSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tbloguins';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbloguin-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Tbloguin', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'Uin',
            'Passwd',
            'TowPasswd',
            'Nick',
            'username',
            // 'Server',
            // 'Owner_Id',
            // 'nType',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
