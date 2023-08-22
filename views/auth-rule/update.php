<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\yii2RbacManager\models\AuthRule $model */

$this->title = Yii::t('AuthRule', 'Update Auth Rule: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('AuthRule', 'Auth Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'name' => $model->name]];
$this->params['breadcrumbs'][] = Yii::t('AuthRule', 'Update');
?>
<div class="auth-rule-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
