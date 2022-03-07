<?php

namespace app\components;

use yii\base\InvalidConfigException;
use \yii\helpers\Html;

class TabularInputColumn extends \yii\grid\DataColumn
{

  /**
   * @var string Name of a method in \yii\helpers\Html class
   */
  public $htmlMethodName = 'activeTextInput';

  /**
   * @var string For example "[G1]" will result in input name="[G1][pkValue]attributeName"
   */
  public $inputNamePrefix = '';

  /**
   * @var array Some input types require a list of data to be displayed. See for example \yii\helpers\Html::activeCheckboxList()
   */
  public $items = [];

  /**
   * @var array Standars options for the input. See for example \yii\helpers\Html::activeDropDownList()
   */
  public $inputOptions = [];

  /**
   * @var string Input will always have at least this class
   */
  public $defaultInputClass = 'form-control';

  /**
   * @var string Error class
   */
  public $errorInputClass = 'is-invalid';

  /**
   * @var string[] Only these methods from \yii\helpers\Html can be used as $this->activeMethodName
   */
  private $allowedHtmlMethodNames = [
    'activeCheckbox',
    'activeCheckboxList',
    'activeDropDownList',
    'activeHiddenInput',
    'activeListBox',
    'activePasswordInput',
    'activeRadioList',
    'activeTextarea',
    'activeTextInput',

    'checkbox',
    'checkboxList',
    'dropDownList',
    'hiddenInput',
    'listBox',
    'passwordInput',
    'radioList',
    'textarea',
    'textInput',
  ];

//  /**
//   * @var string[] These methods in \yii\helpers\Html require items
//   */
//  private $allowedHtmlMethodNames_itemsRequired = [
//    'activeCheckboxList',
//    'activeDropDownList',
//    'activeListBox',
//    'activeRadioList',
//
//    'checkboxList',
//    'dropDownList',
//    'listBox',
//    'radioList',
//  ];


  public function init()
  {
    parent::init();

    if (!in_array($this->htmlMethodName, $this->allowedHtmlMethodNames)) {
      throw new InvalidConfigException('The "activeMethodName" property has invalid value. Check class ' . get_class() . ' for details.');
    }

    if (!is_array($this->items)) {
      throw new InvalidConfigException('The "items" property must be an array.');
    }

  }

  protected function renderDataCellContent($model, $key, $index)
  {
    if ($this->inputOptions['name'] ?? '' == '') {
      $this->inputOptions['name'] = '';
      foreach ($model->getPrimaryKey(true) as $pkCol => $pkVal) {
        $this->inputOptions['name'] .= '[' . $pkVal . ']';
      }
    }

    $this->defaultInputClass = trim($this->defaultInputClass);

    $errorText = '';
    $errorClass = '';
    if (!empty($model->getErrors($this->attribute))) {
      // array of errors is always returned
      $errorText = $model->getErrors($this->attribute)[0];
      $errorClass = trim($this->errorInputClass);
    }

    $this->inputOptions['class'] = trim($this->inputOptions['class'] ?? '');
    $this->inputOptions['class'] = implode(' ', array_filter([$this->inputOptions['class'], $this->defaultInputClass, $errorClass]));
    $this->inputOptions['title'] = $errorText;

    $htmlMethodName = $this->htmlMethodName;
    $attribute = $this->attribute;
    $value = $model->$attribute;

    switch ($htmlMethodName) {
      case 'activeCheckbox':
      case 'activeHiddenInput':
      case 'activePasswordInput':
      case 'activeTextarea':
      case 'activeTextInput':
        // activeTextInput($model, $attribute, $options = [])
        return Html::$htmlMethodName($model, $this->inputNamePrefix . $this->inputOptions['name'] . $this->attribute, $this->inputOptions);

      case 'checkbox':
      case 'hiddenInput':
      case 'passwordInput':
      case 'textarea':
      case 'textInput':
        // textInput($name, $value = null, $options = [])
        return Html::$htmlMethodName($this->inputNamePrefix . $this->inputOptions['name'] . $this->attribute, $value, $this->inputOptions);

      case 'activeCheckboxList':
      case 'activeDropDownList':
      case 'activeListBox':
      case 'activeRadioList':
        // activeCheckboxList($model, $attribute, $items, $options = [])
        return Html::$htmlMethodName($model, $this->inputNamePrefix . $this->inputOptions['name'] . $this->attribute, $this->items, $this->inputOptions);

      case 'checkboxList':
      case 'dropDownList':
      case 'listBox':
      case 'radioList':
        // checkboxList($name, $selection = null, $items = [], $options = [])
        return Html::$htmlMethodName($this->inputNamePrefix . $this->inputOptions['name'] . $this->attribute, $value, $this->items, $this->inputOptions);
    }

  }
}