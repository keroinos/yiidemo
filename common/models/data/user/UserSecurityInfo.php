<?php
/**
 * Created by PhpStorm.
 * User: zengchen01
 * Date: 2016/9/12
 * Time: 14:20
 */

namespace common\models\data\user;


class UserSecurityInfo extends \yii\base\Model
{

    /*
     * 数据结构定义
     * 业务数据结构对象封装了需要存储在扩展字段中的业务数据
     * */

    //密保问题
    public $question;

    //密保答案
    //加密前的数据
    public $answer_info;
    //加密后的数据
    public $answer_encrypt;

    /*
     * 业务属性设置
     * 1.处理需要加密的业务数据
     * */

    function __get($name)
    {
        if ('answer' === $name) {
            return $this->answer_encrypt;
        } else {
            return parent::__get($name);
        }
    }

    public function __set($name, $value)
    {
        if ('answer' === $name) {
            $this->answer_info = $value;
            //$this->answer_encrypt = md5($value);
            //采用更安全的加密方式,仅记录hash值,验证hash值
            $this->answer_encrypt = Yii::$app->getSecurity()->generatePasswordHash($value);
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['question', 'answer_info'],
                'required'
            ],
            [
                ['question'],
                'string',
                'length' => [1, 50],
                'encoding' => 'utf-8',
                'message' => 'invalid question.',
            ],
            [
                ['answer_info'],
                'string',
                'length' => [1, 50],
                'encoding' => 'utf-8',
                'message' => 'invalid answer.',
            ],
        ];
    }
}