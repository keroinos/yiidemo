<?php
/**
 * Created by PhpStorm.
 * User: zengchen01
 * Date: 2016/9/12
 * Time: 17:44
 */

namespace common\models\business\user;

use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use yii\base\Model;

use common\models\data\user\UserAlias;
use common\models\data\user\UserSecurity;

class UserLogin extends Model implements IdentityInterface
{
    private $_user_alias;
    private $_user_security;

    public static function findIdentity($id)
    {
        $user = new UserLogin();
        $user->_user_alias = new UserAlias($id);
        $return_user_alias = $user->_user_alias->find();
        if (!isset($return_user_alias)) {
            return null;
        }

        $user->_user_alias = $return_user_alias;

        $user->_user_security = new UserSecurity($user->_user_alias->uid);
        $return_user_secutiry = $user->_user_security->find();

        if (!isset($return_user_secutiry)) {
            return null;
        }

        return $user;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        //todo:通过REDIS缓存读取用户数据
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function getAuthKey()
    {
        return $this->_user_security->password;
    }

    public function getId()
    {
        return $this->_user_alias->aliasname;
    }

    public function validateAuthKey($authKey)
    {
        return Yii::$app->getSecurity()->validatePassword($authKey, $this->getAuthKey());
    }


}