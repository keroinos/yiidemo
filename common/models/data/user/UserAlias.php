<?php
/**
 * 账号别名数据  维护登录账户信息索引数据
 * User: zengchen01
 * Date: 2016/8/25
 * Time: 10:59
 */

namespace common\models\data\user;

use Yii;

class UserAlias extends \yii\base\Model
{
    /*
     * 账户类型定义
     * 添加账号类型需要注意修改rules验证规则
     * 当前支持普通帐号(支持中文,当作昵称使用),邮箱,手机
     * todo:后续支持第三方账号类型登录,微信,新浪微博等
     * */
    //普通帐号
    const AliasType_Normal = 1;
    //邮箱
    const AliasType_Email = 2;
    //手机
    const AliasType_Phone = 3;


    /*
     * 数据结构定义
     * 表结构和库表结构保持一致,业务数据不存储
     * 业务数据结构对象封装了需要存储在扩展字段中的业务数据
     * 特别注意:需要在__get或者__set中处理的属性,不能定义对应的属性字段,否则魔术方法中的逻辑无效
     * */
    //表结构
    public $aliasname;
    public $aliastype;
    public $uid;
    //public $extinfo='';
    //业务数据结构对象
    private $_useralias_extinfo;


    /*
     * 业务属性设置
     * 业务数据转化为扩展字段存储
     * */

    function __get($name)
    {
        if ('UserAliasExtInfo' === $name) {
            if (!isset($this->_useralias_extinfo)) {
                $this->_useralias_extinfo = new UserAliasExtinfo();
            }
            if (!empty($this->extinfo)) {
                $this->_useralias_extinfo = json_decode($this->extinfo);
            }
            return $this->_useralias_extinfo;
        } else if ('extinfo' === $name) {
            if (!isset($this->_useralias_extinfo)) {
                $this->_useralias_extinfo = new UserAliasExtinfo();
            }
            return json_encode($this->_useralias_extinfo);
        } else {
            return parent::__get($name);
        }
    }










    /*
     * 需要验证扩展业务数据结构的有效性
     * */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        $validateValue = $this->_useralias_extinfo->validate($attributeNames, $clearErrors);
        if (!$validateValue) {
            return $validateValue;
        }
        return parent::validate($attributeNames, $clearErrors);
    }

    public function getErrors($attribute = null)
    {
        $errors = $this->_useralias_extinfo->getErrors($attribute);
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
            [
                ['aliasname', 'aliastype', 'uid'],
                'required'
            ],
            //这里自定义message无效
            [
                ['aliasname'],
                'string',
                'length' => [1, 64],
                'encoding' => 'utf-8',
                'message' => 'invalid aliasname.'
            ],
            //账户类型为普通账户的时候，验证不允许是手机号或者邮箱
            [
                ['aliasname'],
                function ($attribute, $params) {

                    $error = '';
                    $validator_email = new \yii\validators\EmailValidator();
                    $validator_phone = new \yii\validators\RegularExpressionValidator(['pattern' => '/(13|15|18)[0-9]{9}$/']);
                    if ($validator_email->validate($this->$attribute, $error)) {
                        $this->addError($attribute, '普通账户不能为邮箱');
                    } elseif ($validator_phone->validate($this->$attribute, $error)) {
                        $this->addError($attribute, '普通账户不能为手机号');
                    }
                },
                'when' => function ($model) {
                    return $model->aliastype == UserAlias::AliasType_Normal;
                },
                'message' => '请填写普通账户(非邮箱)(非手机).'
            ],
            //账户类型为邮箱的时候，验证邮箱格式是否合法
            [
                ['aliasname'],
                'email',
                'when' => function ($model) {
                    return $model->aliastype == UserAlias::AliasType_Email;
                },
                'message' => '请填写邮箱.'
            ],
            //账户类型为手机号的时候，验证手机号是否合法
            [
                ['aliasname'],
                function ($attribute, $params) {
                    $error = '';
                    $validator_phone = new \yii\validators\RegularExpressionValidator(['pattern' => '/^(13|15|18)[0-9]{9}$/']);
                    if (!$validator_phone->validate($this->$attribute, $error)) {
                        $this->addError($attribute, '手机号格式错误');
                    }
                },
                'when' => function ($model) {
                    return $model->aliastype == UserAlias::AliasType_Phone;
                },
                'message' => '请填写手机.'
            ],
            //验证账户类型为规定类型
            [
                ['aliastype'],
                'in',
                'strict' => true,
                'range' => [
                    UserAlias::AliasType_Normal,
                    UserAlias::AliasType_Email,
                    UserAlias::AliasType_Phone],
                'message' => 'Please choose a valid aliastype.'
            ],
            [
                ['uid'],
                'integer',
                'min' => 1
            ],
            [
                ['extinfo'],
                'string',
                'max' => 1000
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
     *  $hashvalue = md5($pAliasName);
        $hashkeyvalue = substr($hashvalue, 0, 1);
        $hashkey = $hashkeyvalue % 2;
     * */

    //动态表名
    private $_tableName = '';

    public function __construct($pAliasName)
    {
        //初始化表名
        $this->_tableName = self::getTableName($pAliasName);
        //初始化帐户名,必须的数据
        $this->aliasname = $pAliasName;
        //识别账户类型
        $this->aliastype = self::FindAliasType($pAliasName);

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
     * 根据账号名计算散列规则后的表名
     * @return string
     */
    public static function getTableName($pAliasName)
    {
        //根据别名$alias计算散列规则
        $hashvalue = md5($pAliasName);
        $hashkeyvalue = substr($hashvalue, 0, 1);
        $hashkey = $hashkeyvalue % 2;
        //初始化表名
        return 'tb_user_alias_' . $hashkey;
    }

    /**
     * 从数据库加载数据
     * @return common\models\data\user\UserAlias
     */
    public function find()
    {

        $sql = 'select * from {{' . $this->_tableName . '}} where aliasname = :aliasname and aliastype = :aliastype;';

        $findcmd = self::getDb()
            ->createCommand($sql)
            ->bindValue(':aliasname', $this->aliasname)
            ->bindValue(':aliastype', $this->aliastype);

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

    /**
     * 将业务数据存储到数据库
     * @param $db 默认使用当前的数据库链接,需要事务处理的时候，需要传入事务的数据库链接
     * @return int 数据库处理状态
     */
    public function save($db = null)
    {
        //使用ON DUPLICATE KEY语法,实现插入和更新
        $sql = 'INSERT INTO {{' . $this->_tableName . '}} VALUES (:aliasname,:aliastype,:uid,:extinfo) ON DUPLICATE KEY UPDATE uid = :uid , extinfo = :extinfo;';

        //默认使用当前的数据库链接,需要事务处理的时候，需要传入事务的数据库链接
        if (!isset($db)) {
            $db = self::getDb();
        }

        $findcmd = $db
            ->createCommand($sql)
            ->bindValue(':aliasname', $this->aliasname)
            ->bindValue(':aliastype', $this->aliastype)
            ->bindValue(':uid', $this->uid)
            ->bindValue(':extinfo', $this->extinfo);

        return $findcmd->execute();
    }

    /*
     * 业务方法
     * 1.获取账户类型 public static function FindAliasType($aliasname)
     * */

    public static function FindAliasType($aliasname)
    {
        $error = '';
        $validator_email = new \yii\validators\EmailValidator();
        $validator_phone = new \yii\validators\RegularExpressionValidator(['pattern' => '/(13|15|18)[0-9]{9}$/']);
        if ($validator_email->validate($aliasname, $error)) {
            return self::AliasType_Email;
        } elseif ($validator_phone->validate($aliasname, $error)) {
            return self::AliasType_Phone;
        } else {
            return self::AliasType_Normal;
        }
    }
}