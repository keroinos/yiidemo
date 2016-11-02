<?php
/**
 * Created by PhpStorm.
 * User: zengchen01
 * Date: 2016/9/12
 * Time: 14:16
 */

namespace common\models\data\user;

use common\models\data\user\UserSecurityInfo;

class UserSecurity extends \yii\base\Model
{

    /*
     * 数据结构定义
     * 表结构和库表结构保持一致,业务数据不存储
     * 业务数据结构对象封装了需要存储在扩展字段中的业务数据
     * 特别注意:需要在__get或者__set中处理的属性,不能定义对应的属性字段,否则魔术方法中的逻辑无效
     * */
    //表结构
    public $uid;
    //public $password;
    //public $extinfo='';
    //加密前的数据
    public $password_info;
    //加密后的数据
    private $password_encrypt;
    //业务数据结构对象
    private $_usersecurity_info;

    /*
     * 业务属性设置
     * 1.业务数据转化为扩展字段存储
     * 2.处理需要加密的业务数据
     * */

    function __get($name)
    {
        if ('UserSecurityInfo' === $name) {
            if (!isset($this->_usersecurity_info)) {
                $this->_usersecurity_info = new UserSecurityInfo();
            }
            if (!empty($this->extinfo)) {
                $this->_usersecurity_info = json_decode($this->extinfo);
            }
            return $this->_usersecurity_info;
        } else if ('extinfo' === $name) {
            if (!isset($this->_usersecurity_info)) {
                $this->_usersecurity_info = new UserSecurityInfo();
            }
            return json_encode($this->_usersecurity_info);
        } else if ('password' === $name) {
            return $this->password_encrypt;
        } else {
            return parent::__get($name);
        }
    }

    public function __set($name, $value)
    {
        if ('password' === $name) {
            $this->password_info = $value;
            //采用更安全的加密方式,仅记录hash值,验证hash值
            $this->password_encrypt = Yii::$app->getSecurity()->generatePasswordHash($value);
        } else {
            parent::__set($name, $value);
        }
    }


    /*
     * 业务场景
     * 这里采用限定场景的方式，不允许使用默认场景，强制要求使用的时候必须设置场景
     * */

    //密码场景
    const SCENARIO_PASSWORD = 'password';
    //密保资料场景
    const SCENARIO_QUESTION2ANSWER = 'question2answer';

    public function scenarios()
    {
        /*$scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_PASSWORD] = ['uid', 'password', 'password_info'];
        $scenarios[self::SCENARIO_QUESTION2ANSWER] = ['uid', 'question', 'answer', 'answer_info'];
        return $scenarios;*/

        return [
            self::SCENARIO_PASSWORD => ['uid', 'password', 'password_info'],
            self::SCENARIO_QUESTION2ANSWER => ['uid', '_usersecurity_info'],
        ];
    }

    /*
     * 需要验证扩展业务数据结构的有效性
     * */

    public function validate($attributeNames = null, $clearErrors = true)
    {
        if (self::SCENARIO_QUESTION2ANSWER === $this->scenario) {
            $validateValue = $this->_usersecurity_info->validate($attributeNames, $clearErrors);
            if (!$validateValue) {
                return $validateValue;
            }
        } else {
            return parent::validate($attributeNames, $clearErrors);
        }
    }

    public function getErrors($attribute = null)
    {
        $errors = $this->_usersecurity_info->getErrors($attribute);
        if (isset($errors) && count($errors) > 0) {
            return $errors;
        }
        return parent::getErrors($attribute);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //特别注意，使用trim这类函数 如果处理的是需要加密的数据会造成重复处理
            //遇到这个问题，说明filter是在对象赋值以后才对属性做处理
            /*[
                ['password', 'question', 'answer'],
                'trim'
            ],*/
            [
                ['uid'],
                'required',
            ],
            [
                ['password_info'],
                'required',
                'on' => self::SCENARIO_PASSWORD,
            ],
            [
                ['password_info'],
                'string',
                'length' => [1, 10],
                'encoding' => 'utf-8',
                'message' => 'invalid password.',
                'on' => self::SCENARIO_PASSWORD,
            ],
        ];
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
        return 't_user_security_' . $hashkey;
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
            if ('password' === $key) {
                $this->passwordinfo = $value;
            } else {
                $this->$key = $value;
            }

        }
        return $this;
    }

    public function save($db = null)
    {
        if (!isset($db)) {
            $db = self::getDb();
        }

        if (self::SCENARIO_QUESTION2ANSWER === $this->scenario) {

            $sql = 'INSERT INTO {{' . $this->_tableName . '}} VALUES (:uid,:securityinfo) ON DUPLICATE KEY UPDATE securityinfo = :securityinfo;';

            $findcmd = $db
                ->createCommand($sql)
                ->bindValue(':uid', $this->uid)
                ->bindValue(':securityinfo', $this->securityinfo);

        } else {

            $sql = 'INSERT INTO {{' . $this->_tableName . '}} VALUES (:uid,:password) ON DUPLICATE KEY UPDATE password = :password ;';

            $findcmd = $db
                ->createCommand($sql)
                ->bindValue(':uid', $this->uid)
                ->bindValue(':password', $this->password);
        }

        return $findcmd->execute();
    }


}