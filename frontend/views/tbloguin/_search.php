<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\TbloguinSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tbloguin-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Uin') ?>

    <?= $form->field($model, 'Passwd') ?>

    <?= $form->field($model, 'TowPasswd') ?>

    <?= $form->field($model, 'Nick') ?>

    <?= $form->field($model, 'username') ?>

    <?php // echo $form->field($model, 'Server') ?>

    <?php // echo $form->field($model, 'Owner_Id') ?>

    <?php // echo $form->field($model, 'nType') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
