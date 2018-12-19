<?php
namespace common\component;
use yii\rbac\Rule;
use yii\rbac\Role;
use Yii;
/**
* 
*/
class AuthorRule extends Rule
{
	public $name='isAuthor';

	public function execute($user,$item,$params)
	{
		// $query="SELECT [[item_name]] FROM {{auth_assignment}} WHERE [[user_id]]=$user;";
		// $res=Yii::$app->db->createCommand($query)->queryOne();
		// if($res['item_name']=='admin')
		// 	return true;
		return isset($params['post']) ? $params['post']->author_id==$user:false;
	}
}

?>