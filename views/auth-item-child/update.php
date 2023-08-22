<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\yii2RbacManager\models\AuthItemChild $model */

$this->title = Yii::t('AuthItemChild', 'Update Auth Item Child: {name}', [
    'name' => $model->parent,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('AuthItemChild', 'Auth Item Children'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->parent, 'url' => ['view', 'parent' => $model->parent, 'child' => $model->child]];
$this->params['breadcrumbs'][] = Yii::t('AuthItemChild', 'Update');
?>
<div class="auth-item-child-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
