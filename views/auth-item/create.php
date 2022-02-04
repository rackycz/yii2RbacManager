<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\yii2RbacManager\models\AuthItem */

$this->title = Yii::t('AuthItem', 'Create Auth Item');
$this->params['breadcrumbs'][] = ['label' => Yii::t('AuthItem', 'Auth Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
