<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthItem[] $itemNames
 * @property Post[] $posts
 */
class User extends \yii\db\ActiveRecord
{
    public $auth_item;
    public $password;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'email', 'auth_item'], 'required', 'on'=>'new_user'],
            [['username','email'],'unique'],
            [['username', 'email', 'auth_item'], 'required', 'on'=>'edit_user'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['password'],'safe','on'=>'edit_user']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'email' => Yii::t('app', 'Email'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemNames()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])->viaTable('auth_assignment', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::className(), ['author_id' => 'id']);
    }

    public function save($runValidation = true, $attributeNames = NULL)
    {
        $user = new \common\models\User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        if($user->save())
        {
            $this->id=$user->id;
            if($this->addAssignment()==true)
                return true;
        }

    }

    private function addAssignment()
    {
        $modelAssign=new \backend\models\AuthAssignment;
        $modelAssign->user_id=$this->id;
        $modelAssign->item_name=$this->auth_item;
        // echo "$this->id";
        // exit();
        if($modelAssign->save())
            return true;
    }



    public function update($runValidation = true, $attributeNames = NULL)
    {
        if (!empty($this->password)) {
            $this->password_hash=Yii::$app->security->generatePasswordHash($this->password);
        }

        $userrole= \backend\models\AuthAssignment::findOne(['user_id'=>$this->id]);
        if ($userrole!=null) {
            // $userrole->user_id=$this->id;
            $userrole->item_name=$this->auth_item;
            $userrole->update();
        }
        else{
            $this->id=$userrole->user_id;
            $this->addAssignment();
        }

        $this->status=$this->status==1 ? 10 : 0;
        return parent::update();

    }

}
