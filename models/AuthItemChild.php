<?php

namespace app\modules\yii2RbacManager\models;

use Yii;

/**
 * This is the model class for table "auth_item_child".
 *
 * @property string $parent
 * @property string $child
 *
 * @property AuthItem $child0
 * @property AuthItem $parent0
 */
class AuthItemChild extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return \Yii::$app->controller->module->authItemChild['tableName'];
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['parent', 'child'], 'required'],
      [['parent', 'child'], 'string', 'max' => 64],
      [['parent', 'child'], 'unique', 'targetAttribute' => ['parent', 'child']],
      [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['parent' => 'name']],
      [['child'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['child' => 'name']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'parent' => Yii::t('AuthItemChild', 'Parent'),
      'child' => Yii::t('AuthItemChild', 'Child'),
    ];
  }

  /**
   * Gets query for [[Child0]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getChild0()
  {
    return $this->hasOne(AuthItem::className(), ['name' => 'child']);
  }

  /**
   * Gets query for [[Parent0]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getParent0()
  {
    return $this->hasOne(AuthItem::className(), ['name' => 'parent']);
  }

  public static function getParentsWithoutAuthItem()
  {
    return self::getChildParentWithoutAuthItem(true);
  }

  public static function getChildrenWithoutAuthItem()
  {
    return self::getChildParentWithoutAuthItem(false);
  }

  private static function getChildParentWithoutAuthItem($parent = false)
  {
    $authItem = '`' . AuthItem::tableName() . '`';
    $authItemChild = '`' . AuthItemChild::tableName() . '`';
    $authAssignment = '`' . AuthAssignment::tableName() . '`';
    $authAssignment_itemNameCol = '`item_name`';
    $authItemPkCol = '`name`';

    $column = '`child`';
    if ($parent) {
      $column = '`parent`';
    }

    $sql = "SELECT tmp.$column, SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as assignedToNrOfUsers 
FROM (
    SELECT distinct($column) as $column FROM $authItemChild
    LEFT OUTER JOIN $authItem ON $authItemChild.$column = $authItem.$authItemPkCol
    WHERE $authItem.$authItemPkCol IS NULL
    ORDER BY $column
) AS `tmp`
LEFT OUTER JOIN $authAssignment ON $authAssignment.$authAssignment_itemNameCol = `tmp`.$column
GROUP BY tmp.$column";

    return [
      'code' => $sql,
      'data' => Yii::$app->getDb()->createCommand($sql)->queryAll(),
    ];
  }

  public static function getTopParents($inverse = false)
  {

    $authItemChild = '`' . AuthItemChild::tableName() . '`';
    $authAssignment = '`' . AuthAssignment::tableName() . '`';
    $authAssignment_itemNameCol = '`item_name`';
    $parentColumn = '`parent`';
    $childColumn = '`child`';

    if ($inverse) {
      $parentColumn = '`child`';
      $childColumn = '`parent`';
    }

    $sql = "SELECT tmp.$parentColumn as `item`, SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as assignedToNrOfUsers 
FROM (
    SELECT distinct(t1.$parentColumn) as $parentColumn
    FROM $authItemChild t1 
    LEFT OUTER JOIN $authItemChild t2 ON (t1.$parentColumn = t2.$childColumn)
    WHERE t2.$parentColumn IS NULL AND t2.$childColumn IS NULL
) AS `tmp`
LEFT OUTER JOIN $authAssignment ON $authAssignment.$authAssignment_itemNameCol = `tmp`.$parentColumn
GROUP BY $parentColumn
ORDER BY $parentColumn";

    return [
      'code' => $sql,
      'data' => Yii::$app->getDb()->createCommand($sql)->queryAll(),
    ];
  }
//
//  public static function getDeepestChildren()
//  {
//
//    $authItemChild = '`' . AuthItemChild::tableName() . '`';
//    $authAssignment = '`' . AuthAssignment::tableName() . '`';
//    $authAssignment_itemNameCol = '`item_name`';
//    $parentColumn = '`parent`';
//    $childColumn = '`child`';
//
//    $sql = "SELECT tmp.$childColumn as $childColumn, SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as assignedToNrOfUsers
//FROM (
//    SELECT distinct(t1.$childColumn) as $childColumn
//    FROM $authItemChild t1
//    LEFT OUTER JOIN $authItemChild t2 ON (t1.$childColumn = t2.$parentColumn)
//    WHERE t2.$childColumn IS NULL AND t2.$parentColumn IS NULL
//) AS `tmp`
//LEFT OUTER JOIN $authAssignment ON $authAssignment.$authAssignment_itemNameCol = `tmp`.$childColumn
//GROUP BY $childColumn
//ORDER BY $childColumn";
//
//    return [
//      'code' => $sql,
//      'data' => Yii::$app->getDb()->createCommand($sql)->queryAll(),
//    ];
//  }

  public static function getParentsToChildrenArray($inverse = false)
  {
    $groupConcatSeparator = '|';
    $groupConcatSqlLimit = 'SET SESSION group_concat_max_len = 1000000;'; // number of chars. Run it before GROUP_CONCAT
    $authItemChild = '`' . AuthItemChild::tableName() . '`';

    $parentColumn = 'parent';
    $parentColumnQ = '`' . $parentColumn . '`';
    $childColumn = 'child';
    $childColumnQ = '`' . 'child' . '`';
    $concatColumn = 'child';
    $concatColumnQ = '`' . $concatColumn . '`';

    if ($inverse) {
      $parentColumn = 'child';
      $parentColumnQ = '`' . $parentColumn . '`';
      $childColumn = 'parent';
      $childColumnQ = '`' . 'parent' . '`';
      $concatColumn = 'parent';
      $concatColumnQ = '`' . $concatColumn . '`';
    }

    $sql = "SELECT $parentColumnQ, GROUP_CONCAT($childColumnQ SEPARATOR '$groupConcatSeparator') $concatColumnQ FROM $authItemChild GROUP BY $parentColumnQ ORDER BY $parentColumnQ";
    Yii::$app->getDb()->createCommand($groupConcatSqlLimit)->execute();
    $parents = Yii::$app->getDb()->createCommand($sql)->queryAll();

    $result = [];
    foreach ($parents as $row) {
      $parent = $row[$parentColumn];
      $children = explode($groupConcatSeparator, $row[$concatColumn]);
      $result[$parent] = $children;
    }
    return $result;
  }

  public static function getParentChildTreeData($inverse = false, $parents = [], $parentsToChildren = [])
  {
    if (empty($parents)) {
      // only executed 1x at the beginning.
      $parents = self::getTopParents($inverse);
      $parents = \yii\helpers\ArrayHelper::getColumn($parents['data'], 'item');
    }
    if (empty($parentsToChildren)) {
      // only executed 1x at the beginning.
      $parentsToChildren = self::getParentsToChildrenArray($inverse);
    }
    $tree = [];
    foreach ($parents as $parent) {
      if (isset($parentsToChildren[$parent])) {
        $tree[$parent] = self::getParentChildTreeData($inverse, $parentsToChildren[$parent], $parentsToChildren);
      } else {
        // if current item has no children it does not create a sub array
        $tree[] = $parent;
      }
    }
    return $tree;
  }

//  public static function getChildParentTreeData($inverse = false, $children = [], $parentsToChildren = [])
//  {
//    if (empty($children)) {
//      // only executed 1x at the beginning.
//      $children = self::getDeepestChildren();
//      $children = \yii\helpers\ArrayHelper::getColumn($children['data'], 'child');
//    }
//    if (empty($parentsToChildren)) {
//      // only executed 1x at the beginning.
//      $parentsToChildren = self::getParentsToChildrenArray($inverse);
//    }
//    $tree = [];
//    foreach ($children as $child) {
//      if (isset($parentsToChildren[$child])) {
//        $tree[$child] = self::getChildParentTreeData($parentsToChildren[$child], $parentsToChildren);
//      } else {
//        // if current item has no children it does not create a sub array
//        $tree[] = $child;
//      }
//    }
//    return $tree;
//  }


//  public function getAuthItemInverseTree($searchedChild, $parents = [], $childrenToParents = [], $level = 0) {
//
//    if ($level===0) {
//      // run only once
//      $childrenToParents = self::getChildrenToParrentsArray();
//      $sql = "SELECT distinct(parent) FROM AuthItemChild WHERE child LIKE :searchedChild";
//      $command = Yii::app()->db->createCommand($sql);
//      $command->bindValue(':searchedChild',$searchedChild);
//      $parents = $command->queryColumn();
//    }
//
//    $tree = [];
//
//    foreach ($parents as $parent) {
//      if (isset($childrenToParents[$parent])) {
//        $tree[$parent] = $this->getAuthItemInverseTree($parent, $childrenToParents[$parent]??[], $childrenToParents, $level+1);
//      } else {
//        // if current item has no children it does not create a sub array
//        $tree[$parent] = [];
//      }
//    }
//
////    if ($level === 0) {
////      return [$searchedChild => $tree];
////    }
//
//    return $tree;
//
//
//
//  }

  public static function renderTreeData($tree, $defaultCollapsed = false, $level = 0)
  {
    $ulClass = '';
    if ($defaultCollapsed && $level > 0) {
      $ulClass = 'collapsedUl';
    }
    $result = "<ul class='$ulClass'>";
    foreach ($tree as $parent => $children) {
      $result .= '<li class="collapsibleLi">';
      if (is_array($children) && !empty($children)) {
        $result .= $parent;
        $result .= self::renderTreeData($children, $defaultCollapsed, $level+1);
      } else {
        // if current item has no children it does not create a sub array
        $result .= $children;
      }
      $result .= '</li>';
    }
    $result .= '</ul>';
    return $result;
  }

}
