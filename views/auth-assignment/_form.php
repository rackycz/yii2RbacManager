<?php

use app\models\User;
use app\modules\yii2RbacManager\models\AuthItem;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\yii2RbacManager\models\AuthAssignment $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="auth-assignment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'item_name')->dropDownList(AuthItem::getFilterData('name', AuthItem::class, 'name', function ($relatedModel, $defaultValue) {
        return $relatedModel->name;
    })) ?>

    <?= $form->field($model, 'user_id')->dropDownList(User::getFilterData('id', User::class, 'id', function ($relatedModel, $defaultValue) {
        return $relatedModel->email;
    })) ?>
    <?php // echo $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('AuthAssignment', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
