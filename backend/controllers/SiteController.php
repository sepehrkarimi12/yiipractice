<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login','init', 'error','index'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index','logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionInit()
    {
    	$auth=Yii::$app->authManager;

    	// add to auth_item table with type 2
    	$create_post=$auth->createPermission('create_post');
    	$create_post->description='user can creates post';
    	$auth->add($create_post);

    	$update_post=$auth->createPermission('update_post');
    	$update_post->description='user can updates post';
    	$auth->add($update_post);

    	$delete_post=$auth->createPermission('delete_post');
    	$delete_post->description='user can deletes post';
    	$auth->add($delete_post);

    	// also add to auth_item table with type 1
    	$author=$auth->createRole('author');
    	$admin=$auth->createRole('admin');

    	$auth->add($author);
    	$auth->add($admin);

    	// add to auth_item_child table
    	$auth->addChild($author,$create_post);
    	$auth->addChild($author,$update_post);

    	$auth->addChild($admin,$create_post);
    	$auth->addChild($admin,$update_post);
    	$auth->addChild($admin,$delete_post);

    	// which user is in which group(user is admin OR user is ordinary)
    	$auth->assign($admin,1);
    	$auth->assign($author,2);

    	// add rule
    	$rule=new \common\component\AuthorRule;
    	$auth->add($rule);

    	// create new permision for this reason that add rule for it
    	$updateOwnPost=$auth->createPermission('updateOwnPost');
    	$updateOwnPost->description='everybody can update own post';
    	$updateOwnPost->ruleName=$rule->name;
    	$auth->add($updateOwnPost);

    	$auth->addChild($updateOwnPost,$update_post);
    	$auth->addChild($author,$updateOwnPost);


    }

}
