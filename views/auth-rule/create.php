<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\yii2RbacManager\models\AuthRule $model */

$this->title = Yii::t('AuthRule', 'Create Auth Rule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('AuthRule', 'Auth Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
