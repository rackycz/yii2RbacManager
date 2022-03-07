<?php

namespace app\components;

use yii\base\InvalidConfigException;

class ProgressColumn extends \yii\grid\Column
{
  public $percent = 0;

  public function init()
  {
    parent::init();
    // Here you can test if all public properties are setup correctly.
    // If you dont wanna test anything, feel free to remove this method.

    $this->percent = (int)$this->percent;

    if ($this->percent < 0) {
      throw new InvalidConfigException('The "abc" property must not be 0 nor null.');
    }
    if ($this->percent > 100) {
      throw new InvalidConfigException('The "abc" property must not be 0 nor null.');
    }
  }

  protected function renderDataCellContent($model, $key, $index)
  {
    if ($this->content !== null) {
      // Check what means attribute "content" in class \yii\grid\Column.
      // It is a callable which can be specified by the end-programmer.
      // If it is specified then the programmer actually does not want to use our new column type, so it can be rendered by the parent class.
      return parent::renderDataCellContent($model, $key, $index);
    }

    return '<div style="width:100%;background-color:silver;"><div style="width:'.$this->percent.'%;background-color:#28a745;">.</div></div>';
  }
}