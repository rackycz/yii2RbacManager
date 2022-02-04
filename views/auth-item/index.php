<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\yii2RbacManager\models\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('AuthItem', 'Auth Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('AuthItem', 'Create Auth Item'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'type',
            'description:ntext',
            'rule_name',
            'data',
            //'created_at',
            //'updated_at',

            [
              'class' => 'yii\grid\ActionColumn',
              'urlCreator' => function ($action, $model, $key, $index) {
                // ActionColumn.createUrl()
                $params = [];
                foreach ($model->primaryKey() as $pkColName) {
                  $params[$pkColName] = (string)$model->$pkColName;
                }
                $params[0] = $action;
                return \yii\helpers\Url::to($params);
               }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
