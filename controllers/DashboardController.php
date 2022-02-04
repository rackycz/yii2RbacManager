<?php

namespace app\modules\yii2RbacManager\controllers;

use yii\web\Controller;

/**
 * Default controller for the `yii2RbacManager` module
 */
class DashboardController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
