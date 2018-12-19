<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property int $type
 * @property string $description
 * @property string $rule_name
 * @property resource $data
 * @property int $created_at
 * @property int $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property User[] $users
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 * @property AuthItemChild[] $authItemChildren0
 * @property Authitem[] $children
 * @property Authitem[] $parents
 */
class Authitem extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['name'], 'unique'],
            [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::className(), 'targetAttribute' => ['rule_name' => 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'type' => Yii::t('app', 'Type'),
            'description' => Yii::t('app', 'Description'),
            'rule_name' => Yii::t('app', 'Rule Name'),
            'data' => Yii::t('app', 'Data'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('auth_assignment', ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuleName()
    {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren0()
    {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Authitem::className(), ['name' => 'child'])->viaTable('auth_item_child', ['parent' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParents()
    {
        return $this->hasMany(Authitem::className(), ['name' => 'parent'])->viaTable('auth_item_child', ['child' => 'name']);
    }

    private function all_roles()
    {
        return[
            'post'=>[
                ['name'=>'admin_post','label'=>'Admin Post','checked'=>0],
                ['name'=>'add_post','label'=>'Add Post','checked'=>0],
                ['name'=>'edit_post','label'=>'Edit Post','checked'=>0],
                ['name'=>'delete_post','label'=>'Delete Post','checked'=>0],
            ],
            'comment'=>[
                ['name'=>'admin_comment','label'=>'Admin Comment','checked'=>0],
                ['name'=>'add_comment','label'=>'Add Comment','checked'=>0],
                ['name'=>'edit_comment','label'=>'Edit Comment','checked'=>0],
                ['name'=>'delete_comment','label'=>'Delete Comment','checked'=>0],
            ],
            'category'=>[
                ['name'=>'admin_category','label'=>'Admin Category','checked'=>0],
                ['name'=>'add_category','label'=>'Add Category','checked'=>0],
                ['name'=>'edit_category','label'=>'Edit Category','checked'=>0],
                ['name'=>'delete_category','label'=>'Delete Category','checked'=>0],
            ],
            'user'=>[
                ['name'=>'admin_user','label'=>'Admin User','checked'=>0],
                ['name'=>'add_user','label'=>'Add User','checked'=>0],
                ['name'=>'edit_user','label'=>'Edit User','checked'=>0],
                ['name'=>'delete_user','label'=>'Delete User','checked'=>0],
            ]
        ];
    }

    public function getAllRoles()
    {
        $roles=$this->all_roles();
        if(!$this->isNewRecord)
        {
            $db_all_roles=(new yii\db\Query())
                ->select(['child'])
                ->from('auth_item_child')
                ->where(['parent'=>$this->name])
                ->all();
            // echo $db_all_roles[0]['child'];
            // echo "<pre>";
            // print_r($db_all_roles);
            // exit();
            $db_roles=[];
            foreach ($db_all_roles as $k => $v) {
                array_push($db_roles,$v['child']);
            }

            // print_r($db_roles);
            // exit();

            foreach ($roles as $kr => $vr) {
                foreach ($vr as $ki => $item) {
                    if (in_array($item['name'], $db_roles)) {
                        $roles[$kr][$ki]['checked']=1;
                    }
                }
            }
        }
        // echo $roles['post'][0]['name'];            
        // echo "<pre>";
        
        // echo "<hr>";
        // print_r($roles);
        //     exit();
        return $roles;
        // echo "<pre>";
        // print_r( $this->all_roles());
        // exit();
        // return $this->all_roles();
    }

    public function save($runValidation = true, $attributeNames = NULL)
    {
        $auth=Yii::$app->authManager;
        $time=time();
        $items=Yii::$app->request->post('Items');
        $newName=Yii::$app->request->post('Authitem')['name'];
        $newDescription=Yii::$app->request->post('Authitem')['description'];
        // echo "<pre>";
        // print_r( Yii::$app->request->post('Authitem'));
        // exit();
        $deleteName=Yii::$app->request->get('id');
        $sql="DELETE FROM `auth_item` WHERE `auth_item`.`name` = '{$deleteName}';";
        // echo $sql;
        // exit();
        Yii::$app->db->createCommand($sql)->query();
        // exit();


        $sql="INSERT IGNORE INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES ('{$newName}', 1, '{$newDescription}', NULL, NULL, $time, $time)";
        Yii::$app->db->createCommand($sql)->query();

        foreach ($items as $k=>$v) {
            $sql="INSERT IGNORE INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES ('{$k}', 2, '{$k}', NULL, NULL, $time, $time)";
            Yii::$app->db->createCommand($sql)->query();

            $sql="INSERT IGNORE INTO `auth_item_child` (`parent`, `child`) VALUES ('{$newName}', '$k')";
            Yii::$app->db->createCommand($sql)->query();
        }

        return true;
        // echo "<pre>";
        // print_r($_POST);
        // exit();
        
        // $auth=Yii::$app->authManager;
        // $items=Yii::$app->request->post('Items');
        // foreach ($items as $k=>$v) {
        //     $permission=$auth->createPermission($k);
        //     $permission->description=$k;
        //     $auth->add($permission);
        // }

        // $role=$auth->createRole($this->name);
        // $auth->add($role);
        // $auth->addChild($role,$permission);
        
    }
    
}
