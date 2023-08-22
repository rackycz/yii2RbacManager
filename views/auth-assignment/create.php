<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\yii2RbacManager\models\AuthAssignment $model */

$this->title = Yii::t('AuthAssignment', 'Create Auth Assignment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('AuthAssignment', 'Auth Assignments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-assignment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
