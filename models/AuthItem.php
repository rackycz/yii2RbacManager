<?php

namespace app\modules\yii2RbacManager\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property int $type
 * @property string|null $description
 * @property string|null $rule_name
 * @property resource|null $data
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthItemChild[] $authItemChildren
 * @property AuthItemChild[] $authItemChildren0
 * @property AuthItem[] $children
 * @property AuthItem[] $parents
 * @property AuthRule $ruleName
 */
class AuthItem extends \yii\db\ActiveRecord
{

  const TYPE_ROLE = 1;
  const TYPE_PERMISSION = 2;

  public static function getTypesListData()
  {
    return [
      self::TYPE_ROLE => Yii::t('AuthItem', 'User role'),
      self::TYPE_PERMISSION => Yii::t('AuthItem', 'Permission'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    //return '{{%auth_item}}';
    return \Yii::$app->controller->module->authItem['tableName'];
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['name', 'type'], 'required'],
      [['type', 'created_at', 'updated_at'], 'integer'],
      [['description', 'data'], 'string'],
      [['name', 'rule_name'], 'string', 'max' => 64],
      [['rule_name'], 'default', 'value' => null],
      [['name'], 'unique'],
      [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::className(), 'targetAttribute' => ['rule_name' => 'name']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'name' => Yii::t('AuthItem', 'Name'),
      'type' => Yii::t('AuthItem', 'Type'),
      'description' => Yii::t('AuthItem', 'Description'),
      'rule_name' => Yii::t('AuthItem', 'Rule Name'),
      'data' => Yii::t('AuthItem', 'Data'),
      'created_at' => Yii::t('AuthItem', 'Created At'),
      'updated_at' => Yii::t('AuthItem', 'Updated At'),
    ];
  }

  /**
   * Gets query for [[AuthAssignments]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getAuthAssignments()
  {
    return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
  }

  /**
   * Gets query for [[AuthItemChildren]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getAuthItemChildren()
  {
    return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
  }

  /**
   * Gets query for [[AuthItemChildren0]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getAuthItemChildren0()
  {
    return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
  }

  /**
   * Gets query for [[Children]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getChildren()
  {
    return $this->hasMany(AuthItem::className(), ['name' => 'child'])->viaTable('auth_item_child', ['parent' => 'name']);
  }

  /**
   * Gets query for [[Parents]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getParents()
  {
    return $this->hasMany(AuthItem::className(), ['name' => 'parent'])->viaTable('auth_item_child', ['child' => 'name']);
  }

  /**
   * Gets query for [[RuleName]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getRuleName()
  {
    return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
  }

  public static function getAuthItemsNotUsedInAuthTree()
  {

    $authItem = '`' . AuthItem::tableName() . '`';
    $authItemChild = '`' . AuthItemChild::tableName() . '`';
    $parentColumn = '`parent`';
    $childColumn = '`child`';
    $authItemPkCol = '`name`';

    $sql = "SELECT $authItemPkCol as itemName FROM $authItem 
LEFT OUTER JOIN $authItemChild t1 ON $authItem.$authItemPkCol = t1.$parentColumn
LEFT OUTER JOIN $authItemChild t2 ON $authItem.$authItemPkCol = t2.$childColumn
WHERE t1.$parentColumn IS NULL AND t2.$childColumn IS NULL";

    return [
      'code' => $sql,
      'data' => Yii::$app->getDb()->createCommand($sql)->queryAll(),
    ];
  }

  public static function getAuthItemsAssignedToAUser()
  {

    $authAssignment = '`' . AuthAssignment::tableName() . '`';
    $authAssignmentItemName = '`item_name`';

    $sql = "SELECT $authAssignmentItemName as itemName, count(*) AS `count` 
FROM $authAssignment 
GROUP BY $authAssignmentItemName";

    return [
      'code' => $sql,
      'data' => Yii::$app->getDb()->createCommand($sql)->queryAll(),
    ];
  }

  public static function getAuthItemsAssignedToAUserButWithoutAuthItem()
  {

    $authItem = '`' . AuthItem::tableName() . '`';
    $authItemPkCol = '`name`';
    $authAssignment = '`' . AuthAssignment::tableName() . '`';
    $authAssignmentItemName = '`item_name`';

    $sql = "SELECT $authAssignmentItemName as itemName, count(*) AS `count` 
FROM $authAssignment
LEFT OUTER JOIN $authItem ON $authAssignment.$authAssignmentItemName = $authItem.$authItemPkCol
WHERE $authItem.$authItemPkCol IS NULL
GROUP BY `itemName`
ORDER BY `itemName`";

    return [
      'code' => $sql,
      'data' => Yii::$app->getDb()->createCommand($sql)->queryAll(),
    ];
  }

  public static function getNrOrRolesAndPermissions()
  {

    $authItem = '`' . AuthItem::tableName() . '`';
    $authItemType = '`type`';

    $sql = "SELECT $authItemType as `type`, count(*) as `count` 
FROM $authItem 
GROUP BY $authItemType";

    return [
      'code' => $sql,
      'data' => Yii::$app->getDb()->createCommand($sql)->queryAll(),
    ];
  }

  public static function getAllControllersAndActions($lowerCase = false)
  {
    $scannedControllerFolders = \Yii::$app->controller->module->scannedControllerFolders;
    $controllerFilenameEnd = \Yii::$app->controller->module->controllerFilenameEnd;

    $result = [];
    foreach ($scannedControllerFolders as $relativeFolderPath => $folderConfig) {
      $absoluteFolderPath = \yii\helpers\Url::to($relativeFolderPath);
      foreach (scandir($absoluteFolderPath) as $fileName) {
        if (strpos($fileName, $controllerFilenameEnd) === false) {
          continue;
        }
        $controllerClassName = explode('.', $fileName)[0];
        $result[$controllerClassName] = self::getListOfActionsForController($folderConfig['namespace'] . '\\' . $controllerClassName);
      }
    }
    return $result;
  }

  public static function getListOfActionsForController($controllerClassName)
  {
    $actionNameStart = \Yii::$app->controller->module->actionNameStart;
    $lowerCase = false;

    $result = [];
    $methods = get_class_methods($controllerClassName);
    foreach ($methods as $method) {
      if (substr($method, 0, strlen($actionNameStart)) !== $actionNameStart) {
        // does not start with "action"
        continue;
      }
      $actionFirstLetter = substr($method, strlen($actionNameStart), 1);
      if (strtoupper($actionFirstLetter) != $actionFirstLetter) {
        // not upper case
        continue;
      }
      $actionName = substr($method, strlen($actionNameStart));
      if ($lowerCase) {
        $actionName = strtolower($actionName);
      }
      $result[] = $actionName;

    }
    sort($result);
    return array_fill_keys($result, ''); // values are used as keys. This is needed in renderTreeData()
  }


    /**
     * Returns distinct-list of values in $column.
     *
     * If $relatedModel and other parameters are provided, list-data is returned in format ['id' => 'Text'].
     * This can be used in dropDownLists as source of data.
     *
     * Shouldn't be used in case of large tables.
     *
     * See how $from and $to values are treated in ArrayHelper::map() to understand the 2 last arguments of getFilterData()
     *
     * @param $column
     * @param null $relatedModel
     * @param string $relatedPkCol
     * @param string $relatedDisplayValue if NULL, getFilterText() will be used
     * @return array
     */
    public static function getFilterData($column, $relatedModel = null, $relatedPkCol = 'id', $relatedDisplayValue = null)
    {
        $usedValues = ArrayHelper::getColumn(self::find()->select($column)->distinct()->asArray()->all(), $column);
        if (!$relatedModel) {
            return $usedValues;
        }

        $relatedDisplayValue = $relatedDisplayValue ?? function ($relatedModel, $defaultValue) {
                // See how $from and $to values are treated in ArrayHelper::map() to understand the 2 last arguments of Blog::getFilterData()
                return $relatedModel->getFilterText();
            };

        return ArrayHelper::map($relatedModel::find()->andWhere([$relatedPkCol => $usedValues])->all(), $relatedPkCol, $relatedDisplayValue);
    }
}
