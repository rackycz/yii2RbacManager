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
  public $listData = [];

  /**
   * @var string Default text for <select> without a selected value. See \yii\helpers\Html::activeDropDownList()
   */
  public $listDataPrompt = 'Select a value ...';

  /**
   * @var array Standars options for the input. See for example \yii\helpers\Html::activeDropDownList()
   */
  public $inputOptions = [];

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

  /**
   * @var string[] These methods in \yii\helpers\Html require listData
   */
  private $allowedHtmlMethodNames_listDataRequired = [
    'activeCheckboxList',
    'activeDropDownList',
    'activeListBox',
    'activeRadioList',

    'checkboxList',
    'dropDownList',
    'listBox',
    'radioList',
  ];


  public function init()
  {
    parent::init();

    if (!in_array($this->htmlMethodName, $this->allowedHtmlMethodNames)) {
      throw new InvalidConfigException('The "activeMethodName" property has invalid value. Check class ' . get_class() . ' for details.');
    }

    if (!is_array($this->listData)) {
      throw new InvalidConfigException('The "listData" property must be an array.');
    }

  }

  protected function renderDataCellContent($model, $key, $index)
  {
    $pkNamePrefix = '';
    foreach ($model->getPrimaryKey(true) as $pkCol => $pkVal) {
      $pkNamePrefix .= '[' . $pkVal . ']';
    }

    $errorText = '';
    $errorClass = '';
    if (!empty($model->getErrors($this->attribute))) {
      // array of errors is always returned
      $errorText = $model->getErrors($this->attribute)[0];
      $errorClass = 'is-invalid';
    }

    $this->inputOptions['class'] = trim(($this->inputOptions['class'] ?? '') . ' form-control ' . $errorClass);
    $this->inputOptions['title'] = $errorText;
    $this->inputOptions['prompt'] = $this->listDataPrompt;

    $htmlMethodName = $this->htmlMethodName;
    switch ($htmlMethodName) {
      case 'activeCheckbox':
      case 'activeHiddenInput':
      case 'activePasswordInput':
      case 'activeTextarea':
      case 'activeTextInput':
        // activeTextInput($model, $attribute, $options = [])
        return Html::$htmlMethodName($model, $this->inputNamePrefix . $pkNamePrefix . $this->attribute, $this->inputOptions);

      case 'checkbox':
      case 'hiddenInput':
      case 'passwordInput':
      case 'textarea':
      case 'textInput':
        // textInput($name, $value = null, $options = [])
        // return Html::$htmlMethodName($model, $this->inputNamePrefix . $pkNamePrefix . $this->attribute, $this->inputOptions);

      case 'activeCheckboxList':
      case 'activeDropDownList':
      case 'activeListBox':
      case 'activeRadioList':
        // activeCheckboxList($model, $attribute, $items, $options = [])
        return Html::$htmlMethodName($model, $this->inputNamePrefix . $pkNamePrefix . $this->attribute, $this->listData, $this->inputOptions);

      case 'checkboxList':
      case 'dropDownList':
      case 'listBox':
      case 'radioList':
        // checkboxList($name, $selection = null, $items = [], $options = [])
        // return Html::$htmlMethodName($model, $this->inputNamePrefix . $pkNamePrefix . $this->attribute, $this->listData, $this->inputOptions);
    }

  }
}