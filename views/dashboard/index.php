<?php

use app\modules\yii2RbacManager\models\AuthItem;
use app\modules\yii2RbacManager\models\AuthItemChild;

?>

<div class="jumbotron">
    <h1 class="display-4">RBAC dashboard</h1>
    <p class="lead">Welcome. This page displays basic statistics about permissions and roles in your Yii2 system.</p>
    <hr class="my-4">
    <p>It uses utility classes for typography and spacing to space content out within the larger container.</p>
    <p class="lead">
        <a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a>
    </p>
</div>

<?php

$authTablesSql = <<<SQL
create table `auth_rule`
(
   `name`                 varchar(64) not null,
   `data`                 blob,
   `created_at`           integer,
   `updated_at`           integer,
    primary key (`name`)
) engine InnoDB;

create table `auth_item`
(
   `name`                 varchar(64) not null,
   `type`                 smallint not null,
   `description`          text,
   `rule_name`            varchar(64),
   `data`                 blob,
   `created_at`           integer,
   `updated_at`           integer,
   primary key (`name`),
   foreign key (`rule_name`) references `auth_rule` (`name`) on delete set null on update cascade,
   key `type` (`type`)
) engine InnoDB;

create table `auth_item_child`
(
   `parent`               varchar(64) not null,
   `child`                varchar(64) not null,
   primary key (`parent`, `child`),
   foreign key (`parent`) references `auth_item` (`name`) on delete cascade on update cascade,
   foreign key (`child`) references `auth_item` (`name`) on delete cascade on update cascade
) engine InnoDB;

create table `auth_assignment`
(
   `item_name`            varchar(64) not null,
   `user_id`              varchar(64) not null,
   `created_at`           integer,
   primary key (`item_name`, `user_id`),
   foreign key (`item_name`) references `auth_item` (`name`) on delete cascade on update cascade,
   key `auth_assignment_user_id_idx` (`user_id`)
) engine InnoDB;
SQL;

$imgUrl = \yii\helpers\Url::to(['dashboard/get-image', 'image' => 'rbac.svg'], true);
echo $this->render('card', [
    'title' => Yii::t('rbacm', 'These tables should be in your DB'),
    'text' => Yii::t('rbacm', 'This RBAC manager will tell you what the tables contain and will try to put it into context. If you have relations among your tables there should not be any PK-FK conflicts and some statistics might be useless as conflicts cannot exist ... (Names of tables and columns are configurable in this RBAC module)'),
    'grid' => '<div style="text-align: center;">' . \yii\helpers\Html::img($imgUrl) . '</div><br/>',
    'code' => $authTablesSql,
    'codeId' => 'auth-tables-creation',
    'showButtonLabel' => 'Show code',
]);
?>

<br>

<div class="card">
    <div class="card-header">
        <?php echo Yii::t('rbacm', 'Here you can directly edit Auth tables'); ?>
    </div>
    <div class="card-body">
        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups"
             style="justify-content: center;">
            <div class="btn-group mr-2" role="group" aria-label="First group">
                <?= \yii\helpers\Html::a('AuthItem', ['auth-item/index'], ['class' => 'btn btn-primary']) ?>
            </div>
            <div class="btn-group mr-2" role="group" aria-label="First group">
                <?= \yii\helpers\Html::a('AuthItemChild', ['auth-item-child/index'], ['class' => 'btn btn-primary']) ?>
            </div>
            <div class="btn-group mr-2" role="group" aria-label="First group">
                <?= \yii\helpers\Html::a('AuthRule', ['auth-rule/index'], ['class' => 'btn btn-primary']) ?>
            </div>
            <div class="btn-group mr-2" role="group" aria-label="First group">
                <?= \yii\helpers\Html::a('AuthAssignment', ['auth-assignment/index'], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
</div>

<br>
<h1 style="text-align: center;">Auth tables statistics:</h1>
<br>

<div class="card-deck">
    <?php
    $getParentsWithoutAuthItem = AuthItemChild::getParentsWithoutAuthItem();
    $dataProvider = new yii\data\ArrayDataProvider([
        'allModels' => $getParentsWithoutAuthItem['data'],
        'pagination' => [
            'pageSize' => 10
        ],
    ]);

    $grid = yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}\n{summary}",
        'columns' => [
            'parent',
            'assignedToNrOfUsers',
        ],
    ]);
    echo $this->render('card', [
        'title' => Yii::t('rbacm', 'Parents in <code>AuthItemChild</code> without <code>AuthItem</code>'),
        'text' => Yii::t('rbacm', 'Here you can see which items are used as <code>parents</code> in table <code>AuthItemChild</code> but are not defined in <code>AuthItem</code>. If such an item is assigned to a user, number of these users is displayed as well.'),
        'grid' => $grid,
        'code' => $getParentsWithoutAuthItem['code'],
        'codeId' => 'parents-without-authitem',
        'showButtonLabel' => 'Show code',
    ]);
    ?>

    <br>

    <?php
    $getParentsWithoutAuthItem = AuthItemChild::getChildrenWithoutAuthItem();
    $dataProvider = new yii\data\ArrayDataProvider([
        'allModels' => $getParentsWithoutAuthItem['data'],
        'pagination' => [
            'pageSize' => 10
        ],
    ]);

    $grid = yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}\n{summary}",
        'columns' => [
            'child',
            'assignedToNrOfUsers',
        ],
    ]);
    echo $this->render('card', [
        'title' => Yii::t('rbacm', 'Children in <code>AuthItemChild</code> without <code>AuthItem</code>'),
        'text' => Yii::t('rbacm', 'Here you can see which items are used as <code>children</code> in table <code>AuthItemChild</code> but are not defined in <code>AuthItem</code>. If such an item is assigned to a user, number of these users is displayed as well.'),
        'grid' => $grid,
        'code' => $getParentsWithoutAuthItem['code'],
        'codeId' => 'children-without-authitem',
        'showButtonLabel' => 'Show code',
    ]);
    ?>
</div>

<br>

<div class="card-deck">
    <?php
    $topParents = AuthItemChild::getTopParents();
    $dataProvider = new yii\data\ArrayDataProvider([
        'allModels' => $topParents['data'],
        'pagination' => [
            'pageSize' => 10
        ],
    ]);

    $grid = yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}\n{summary}",
        'columns' => [
            'item',
            'assignedToNrOfUsers',
        ],
    ]);
    echo $this->render('card', [
        'title' => Yii::t('rbacm', 'Top parents in <code>AuthItemChild</code>'),
        'text' => Yii::t('rbacm', 'Here you can see which items are only used as <code>parents</code> and never as <code>children</code> in table <code>AuthItemChild</code>.'),
        'grid' => $grid,
        'code' => $topParents['code'],
        'codeId' => 'top-parents-without-authitem',
        'showButtonLabel' => 'Show code',
    ]);
    ?>

    <br>

    <?php

    ini_set('xdebug.var_display_max_depth', 10);
    ini_set('xdebug.var_display_max_children', 256);
    ini_set('xdebug.var_display_max_data', 1024);
    $treeData = AuthItemChild::getParentChildTreeData(false);
    $tree = AuthItemChild::renderTreeData($treeData);

    echo $this->render('card', [
        'title' => Yii::t('rbacm', 'Parent-child tree defined in table <code>AuthItemChild</code>'),
        'text' => Yii::t('rbacm', 'Here you can see the actual tree of all Roles and Permissions'),
        'grid' => $tree,
        'code' => 'None',
        'codeId' => 'parent-children-tree',
        'showButtonLabel' => 'Show code',
    ]);

    ?>
</div>

<br>

<div class="card-deck">
    <?php
    $authItemsNotUsedInAuthTree = AuthItem::getAuthItemsNotUsedInAuthTree();
    $dataProvider = new yii\data\ArrayDataProvider([
        'allModels' => $authItemsNotUsedInAuthTree['data'],
        'pagination' => [
            'pageSize' => 10
        ],
    ]);

    $grid = yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}\n{summary}",
        'columns' => [
            'itemName',
        ],
    ]);

    yii\widgets\Pjax::begin([
        'id' => 'abc123',
        'enablePushState' => false,
        'enableReplaceState' => false
    ]);
    echo $this->render('card', [
        'title' => Yii::t('rbacm', '<code>AuthItems</code> not used in <code>AuthItemChild</code>'),
        'text' => Yii::t('rbacm', 'Here you can see which <code>AuthItems</code> are not part of the tree defined in <code>AuthItemChild</code>.'),
        'grid' => $grid,
        'code' => $authItemsNotUsedInAuthTree['code'],
        'codeId' => 'auth-items-not-used-in-auth-item-child',
        'showButtonLabel' => 'Show code',
    ]);
    yii\widgets\Pjax::end();
    ?>

    <br>

    <?php
    $authItemsAssignedToAUser = AuthItem::getAuthItemsAssignedToAUser();
    $dataProvider = new yii\data\ArrayDataProvider([
        'allModels' => $authItemsAssignedToAUser['data'],
        'pagination' => [
            'pageSize' => 10
        ],
    ]);

    $grid = yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}\n{summary}",
        'columns' => [
            'itemName',
        ],
    ]);
    echo $this->render('card', [
        'title' => Yii::t('rbacm', '<code>AuthItems</code> assigned to a user'),
        'text' => Yii::t('rbacm', '<code>AuthItems</code> assigned to a user in table <code>AuthAssignment</code>'),
        'grid' => $grid,
        'code' => $authItemsAssignedToAUser['code'],
        'codeId' => 'auth-items-assigned-to-a-user',
        'showButtonLabel' => 'Show code',
    ]);
    ?>
</div>
<br>

<div class="card-deck">
    <?php
    $authItemsAssignedToAUser = AuthItem::getAuthItemsAssignedToAUserButWithoutAuthItem();
    $dataProvider = new yii\data\ArrayDataProvider([
        'allModels' => $authItemsAssignedToAUser['data'],
        'pagination' => [
            'pageSize' => 10
        ],
    ]);

    $grid = yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}\n{summary}",
        'columns' => [
            'itemName',
        ],
    ]);
    echo $this->render('card', [
        'title' => Yii::t('rbacm', '<code>AuthItems</code> assigned to a user but without record in <code>AuthItem</code>'),
        'text' => Yii::t('rbacm', '<code>AuthItems</code> assigned to a user in table <code>AuthAssignment</code> but without a record in <code>AuthItem</code>'),
        'grid' => $grid,
        'code' => $authItemsAssignedToAUser['code'],
        'codeId' => 'auth-items-assigned-to-a-user',
        'showButtonLabel' => 'Show code',
    ]);
    ?>
    <?php
    ini_set('xdebug.var_display_max_depth', 10);
    ini_set('xdebug.var_display_max_children', 256);
    ini_set('xdebug.var_display_max_data', 1024);
    $treeData = AuthItemChild::getParentChildTreeData(true);
    $tree = AuthItemChild::renderTreeData($treeData);

    echo $this->render('card', [
        'title' => Yii::t('rbacm', 'Child-parent (inverse) tree defined in table <code>AuthItemChild</code>'),
        'text' => Yii::t('rbacm', 'Here you can see what parents does a certain permission/role have'),
        'grid' => $tree,
        'code' => 'None',
        'codeId' => 'parent-children-tree',
        'showButtonLabel' => 'Show code',
    ]);

    ?>
</div>

<br>

<div class="card-deck">
    <?php
    $authItemsNrOfRolesAndPermissions = AuthItem::getNrOrRolesAndPermissions();
    $dataProvider = new yii\data\ArrayDataProvider([
        'allModels' => $authItemsNrOfRolesAndPermissions['data'],
        'pagination' => [
            'pageSize' => 10
        ],
    ]);

    $grid = yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}\n{summary}",
        'columns' => [
            [
                'attribute' => 'type',
                'value' => function ($model, $key, $index, $column) {
                    if ($model['type'] == yii\rbac\Item::TYPE_ROLE) {
                        return Yii::t('rbacm', 'Role');
                    }
                    return Yii::t('rbacm', 'Permission');
                }
            ],
            'count',
        ],
    ]);
    echo $this->render('card', [
        'title' => Yii::t('rbacm', 'Nr of roles and permissions'),
        'text' => Yii::t('rbacm', 'Nr of roles and permissions in table <code>AuthItem</code>.'),
        'grid' => $grid,
        'code' => $authItemsNrOfRolesAndPermissions['code'],
        'codeId' => 'nr-of-roles-and-permissions',
        'showButtonLabel' => 'Show code',
    ]);
    ?>

    <br>

    <?php
    $authItemsAssignedToAUser = AuthItem::getAuthItemsAssignedToAUser();
    $dataProvider = new yii\data\ArrayDataProvider([
        'allModels' => $authItemsAssignedToAUser['data'],
        'pagination' => [
            'pageSize' => 10
        ],
    ]);

    $grid = yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}\n{summary}",
        'columns' => [
            'itemName',
        ],
    ]);

    $code = implode('<br>', array_keys(\Yii::$app->controller->module->scannedControllerFolders));

    echo $this->render('card', [
        'title' => Yii::t('rbacm', 'Tree of controllers and actions'),
        'text' => Yii::t('rbacm', 'These controllers and actions wese found in folders you specified in modul-config in value <strong>scannedControllerFolders</strong>.<br><br>If this list is empty, uncomment renderTreeData() in dashboard/index.php'),
        'grid' => '', //AuthItemChild::renderTreeData(AuthItem::getAllControllersAndActions(), true),
        'code' => $code,
        'codeId' => 'controller-action-tree',
        'showButtonLabel' => 'Show folders',
    ]);
    ?>
</div>

<?php
//echo '<h1>Current module</h1>';
//var_dump(\Yii::$app->controller->module->myParam);
//var_dump(AuthItem::getAllControllersAndActions());

?>

<style>
    pre.code {
        background-color: rgb(240, 240, 240);
        padding: 0.5rem;
        font-size: 0.8rem;
    }

    div.summary {
        text-align: right;
    }

    li.collapsibleLi {
        list-style: none;
        cursor: pointer;
        margin-left: 0;
        padding-left: 1em;
    }

    li.collapsibleLi:before {
        content: "⊞";
        padding-right: 5px;
    }

    ul.collapsedUl {
        display: none;
    }

    ul.tree {
        padding-left: 1rem;
    }
</style>

<script>
    document.addEventListener('click', function (event) {
        if (event.target.matches('li.collapsibleLi')) {
            event.preventDefault();
            event.stopPropagation();
            liClicked(event.target)
            event.target.closest('tr').remove();
        }
    });

    function liClicked(li) {
        let ul = li.querySelector('ul');
        if (ul === null) {
            return true;
        }
        ul.classList.toggle('collapsedUl');
        // if (window.getComputedStyle(ul).display === 'block') {
        //     ul.style.display = 'none';
        // } else {
        //     ul.style.display = 'block';
        // }
    }
</script>