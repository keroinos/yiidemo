<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Tbloguin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tbloguin-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'Passwd')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'TowPasswd')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Nick')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Server')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Owner_Id')->textInput() ?>

    <?= $form->field($model, 'nType')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
