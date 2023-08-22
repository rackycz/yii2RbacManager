<?php

namespace app\modules\yii2RbacManager\models\search;

use app\modules\yii2RbacManager\models\AuthItemChild as AuthItemChildModel;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AuthItemChild represents the model behind the search form of `app\modules\yii2RbacManager\models\AuthItemChild`.
 */
class AuthItemChild extends AuthItemChildModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent', 'child'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AuthItemChildModel::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['like', 'parent', $this->parent])
            ->andFilterWhere(['like', 'child', $this->child]);

        return $dataProvider;
    }
}
