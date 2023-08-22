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

    public function actionGetImage($image)
    {
        if ($image !== 'rbac.svg') {
            exit;
        }

        $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $image;

        //header('Content-Type: application/octet-stream'); // PNG, JPG
        header('Content-Type: image/svg+xml'); // SVG

        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . basename($path));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        if (ob_get_contents()) {
            ob_clean();
        }
        flush();
        readfile($path);

        // Or do we want to use fpassthru() instead of readfile().
        // @link https://www.php.net/manual/en/function.fpassthru.php
        // ...  But:
        // If you just want to dump the contents of a file to the output buffer,
        // without first modifying it or seeking to a particular offset,
        // you may want to use the readfile(), which saves you the fopen() call.

        exit;
    }
}
