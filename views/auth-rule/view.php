<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\modules\yii2RbacManager\models\AuthRule $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('AuthRule', 'Auth Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="auth-rule-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('AuthRule', 'Update'), ['update', 'name' => $model->name], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('AuthRule', 'Delete'), ['delete', 'name' => $model->name], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('AuthRule', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'data',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
