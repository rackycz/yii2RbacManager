<?php

namespace app\modules\yii2RbacManager\models;

use Yii;

/**
 * This is the model class for table "auth_rule".
 *
 * @property string $name
 * @property resource|null $data
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property AuthItem[] $authItems
 */
class AuthRule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
      return '{{%auth_rule}}';
      //return \Yii::$app->controller->module->authRule['tableName'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['data'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('AuthRule', 'Name'),
            'data' => Yii::t('AuthRule', 'Data'),
            'created_at' => Yii::t('AuthRule', 'Created At'),
            'updated_at' => Yii::t('AuthRule', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[AuthItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItems()
    {
        return $this->hasMany(AuthItem::className(), ['rule_name' => 'name']);
    }

    public static function getRulesAsListData() {
      $names = AuthRule::find()->asArray()->select(['name'])->all();
      return \yii\helpers\ArrayHelper::map($names, 'name', 'name');
    }
}
