<?php
/**
 * 1) Include this module in your config file like this:
 * 'modules' => [
 * 'yii2RbacManager' => [
 * 'class' => 'app\modules\yii2RbacManager\yii2RbacManager',
 * ],
 * ],
 *
 * 2) Create Auth tables as described here:
 * https://www.yiiframework.com/doc/guide/2.0/en/security-authorization
 */

namespace app\modules\yii2RbacManager;

use Yii;

/**
 * yii2RbacManager module definition class
 * Configuration in web.php:
'modules' => [
  'yii2RbacManager' => [
    'class' => 'app\modules\yii2RbacManager\yii2RbacManager',
    'defaultRoute' => 'dashboard'
  ],
],
 */
class yii2RbacManager extends \yii\base\Module
{
  /**
   * {@inheritdoc}
   */
  public $controllerNamespace = 'app\modules\yii2RbacManager\controllers';

  /**
   * Default controller and action
   * @var string
   */
  public $defaultRoute = 'dashboard/index';

  /**
   * Each value is expected to start with "app\".
   * It will be prefixed with "@" so later "@app\" can be processed by \yii\helpers\Url::to($path)
   * See method namespaceToAbsolutePath()
   * @var string[]
   */
  public $scannedControllerFolders = [
    '@app/controllers' => [
      'namespace' => 'app\controllers',
    ],
  ];

  public $authAssignment = [
    'tableName' => 'auth_assignment',
  ];

  public $authItem = [
    'tableName' => 'auth_item',
  ];

  public $authItemChild = [
    'tableName' => 'auth_item_child',
  ];

  public $authRule = [
    'tableName' => 'auth_rule',
  ];

  public $controllerFilenameEnd = 'Controller';
  public $actionNameStart = 'action';

  /**
   * {@inheritdoc}
   */
  public function init()
  {
    parent::init();

        $components = Yii::$app->components;

        // Where to search for translations?
        $components['i18n']['translations']['rbacm'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/modules/yii2RbacManager/messages',
            'sourceLanguage' => 'en-US',
        ];

        // If you are using themes, you might need to set the "theme" also for this module:
        $components['view']['theme'] = [
            'basePath' => '@app/modules/admin/themes/yii',
            'baseUrl' => '@web/modules/admin/themes/yii',
            'pathMap' => [
                // Standard Yii theme with some enhancements:
                '@app/views' => '@app/modules/admin/themes/yii',
                '@app/modules/yii2RbacManager/views' => '@app/modules/admin/themes/yii',

                // Original theme by Accelerate with some enhancements:
                //'@app/views' => '@app/modules/admin/themes/accelerate',
                //'@app/modules/yii2RbacManager/views' => '@app/modules/admin/themes/accelerate',
            ],
        ];

        Yii::$app->components = $components;
  }
}
