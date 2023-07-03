# yii2RbacManager.
RBAC manager for PHP framework Yii2

# Setup

You can modify application's configuration in order to use Themes and Translations like this:

```
// This code belongs to the main Module-class

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
```