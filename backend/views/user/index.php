<?php

use yii\helpers\Html;
use yii\grid\GridView;

// echo "<pre>";
// print_r($dataProviderAuthItem);
// exit();

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<script type="text/javascript">
    function changeTitle()
    {
        if (document.title=='Users')
            document.title=='AuthItem';
        else
            document.title=='Users';
    }
</script>


<div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#user" onclick="changeTitle()" aria-controls="user" role="tab" data-toggle="tab">User</a></li>
    <li role="presentation"><a href="#role" onclick="changeTitle()" aria-controls="role" role="tab" data-toggle="tab">Role</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="user">

        <div class="user-index">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <?= Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProviderUser,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'id',
                    'username',
                    // 'auth_key',
                    // 'password_hash',
                    // 'password_reset_token',
                    'email:email',
                    'status',
                    //'created_at',
                    //'updated_at',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
        </div>

    </div>
    <div role="tabpanel" class="tab-pane" id="role">

        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a(Yii::t('app', 'Create Auth Item'), ['authitem/create'], ['class' => 'btn btn-success']) ?>
        </p>

            <?= GridView::widget([
                'dataProvider' => $dataProviderAuthItem,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'name',
                    'description',
                    // 'rule_name',
                    // 'type',
                    // 'id',
                    // 'username',
                    // 'auth_key',
                    // 'password_hash',
                    // 'password_reset_token',
                    //'email:email',
                    //'status',
                    //'created_at',
                    //'updated_at',

                    ['class' => 'yii\grid\ActionColumn',
                     'controller'=>'authitem'
                    ],
                ],
            ]); ?>

    </div>
  </div>

</div>
