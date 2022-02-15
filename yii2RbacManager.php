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

    // custom initialization code goes here
  }
}
