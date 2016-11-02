<?php
/**
 * Created by PhpStorm.
 * User: zengchen01
 * Date: 2016/9/12
 * Time: 16:29
 */

namespace common\models\data\user;

use common\models\data\user\UserInfoBase;
use common\models\data\user\UserInfoExtinfo;
use Yii;


class UserInfo extends \yii\base\Model
{
    /*
     * 数据结构定义
     * 表结构和库表结构保持一致,业务数据不存储
     * 业务数据结构对象封装了需要存储在扩展字段中的业务数据
     * 特别注意:需要在__get或者__set中处理的属性,不能定义对应的属性字段,否则魔术方法中的逻辑无效
     * */
    //表结构
    private $uid;
    //private $userinfo='';
    //private $extinfo='';
    //业务数据结构对象
    private $_userinfo_base;

    /*
     * 业务属性设置
     * 1.业务数据转化为扩展字段存储
     * 2.处理需要加密的业务数据
     * */

    function __get($name)
    {
        if ('UserInfo' === $name) {
            if (!isset($this->_userinfo_base)) {
                $this->_userinfo_base = new UserInfoBase();
            }
            if (!empty($this->userinfo)) {
                $this->_userinfo_base = json_decode($this->userinfo);
            }
            return $this->_userinfo_base;
        } else if ('userinfo' === $name) {
            if (!isset($this->_userinfo_base)) {
                $this->_userinfo_base = new UserInfoBase();
            }
            return json_encode($this->_userinfo_base);
        } else {
            return parent::__get($name);
        }
    }






    /*
     * 需要验证扩展业务数据结构的有效性
     * */

    public function validate($attributeNames = null, $clearErrors = true)
    {
        $validateValue = $this->_userinfo_base->validate($attributeNames, $clearErrors);
        if (!$validateValue) {
            return $validateValue;
        }
        return parent::validate($attributeNames, $clearErrors);
    }

    public function getErrors($attribute = null)
    {
        $errors = $this->_userinfo_base->getErrors($attribute);
        if (isset($errors) && count($errors) > 0) {
            return $errors;
        }
        return parent::getErrors($attribute);
    }















    /*
    * 数据库操作
    * 没使用ActiveRecord的封装对象,直接使用command处理的方式,因为封装后不好处理分库分表的逻辑
    * */

    /*
    * 数据分表处理
    * 根据账号名计算散列规则分表,目前只分两张表
    * 规则:
    *  $hashkeyvalue = substr($pUid, strlen($pUid) - 1, 1);
       $hashkey = $hashkeyvalue % 2;
    * */


    //动态表名
    private $_tableName = '';

    public function __construct($pUid)
    {
        //初始化表名
        $this->_tableName = self::getTableName($pUid);
        //初始化帐户名,必须的数据
        $this->uid = $pUid;

        parent::__construct();
    }

    function __destruct()
    {
        //避免访问错表
        $this->_tableName = '';
    }


    /**
     * 获取当前业务表所在的数据库链接对象
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('userdb');
    }

    /**
     * 根据账号ID计算散列规则后的表名
     * @return string
     */
    public static function getTableName($pUid)
    {
        //根据别名$alias计算散列规则
        $hashkeyvalue = substr($pUid, strlen($pUid) - 1, 1);
        $hashkey = $hashkeyvalue % 2;
        //初始化表名
        return 't_user_info_' . $hashkey;
    }

    public function find()
    {
        $sql = 'select * from {{' . $this->_tableName . '}} where uid = :uid;';

        $findcmd = self::getDb()
            ->createCommand($sql)
            ->bindValue(':uid', $this->uid);

        $findvalue = $findcmd->queryOne();

        if (false === $findvalue) {
            return null;
        }

        //数组转化为对象
        foreach ($findvalue as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    public function save($db = null)
    {
        $sql = 'INSERT INTO {{' . $this->_tableName . '}} VALUES (:uid,:userinfo) ON DUPLICATE KEY UPDATE userinfo = :userinfo;';

        if (!isset($db)) {
            $db = self::getDb();
        }

        $findcmd = $db
            ->createCommand($sql)
            ->bindValue(':uid', $this->uid)
            ->bindValue(':userinfo', $this->userinfo);

        return $findcmd->execute();
    }

}