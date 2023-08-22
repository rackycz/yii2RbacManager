<?php

use app\modules\yii2RbacManager\models\AuthItem;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\yii2RbacManager\models\AuthItemChild $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="auth-item-child-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parent')->dropDownList(AuthItem::getFilterData('name', AuthItem::class, 'name', function ($relatedModel, $defaultValue) {
        return $relatedModel->name;
    })) ?>

    <?= $form->field($model, 'child')->dropDownList(AuthItem::getFilterData('name', AuthItem::class, 'name', function ($relatedModel, $defaultValue) {
        return $relatedModel->name;
    })) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('AuthItemChild', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
