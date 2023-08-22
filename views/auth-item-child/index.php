<?php

use app\modules\yii2RbacManager\models\AuthItemChild;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\modules\yii2RbacManager\models\search\AuthItemChild $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('AuthItemChild', 'Auth Item Children');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-child-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('AuthItemChild', 'Create Auth Item Child'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'parent',
            'child',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, AuthItemChild $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'parent' => $model->parent, 'child' => $model->child]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
