<?php
/**
 * Created by PhpStorm.
 * User: zengchen01
 * Date: 2016/9/12
 * Time: 16:30
 */

namespace common\models\data\user;


class UserInfoBase extends \yii\base\Model
{
    /*
     * 数据结构定义
     * 业务数据结构对象封装了需要存储在扩展字段中的业务数据
     * */

    //注册时间
    public $register_time;

    //注册IP
    public $register_ip;

    //注册来源标记
    public $register_source = 'default';


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['register_time', 'register_ip','register_source'],
                'required'
            ],
            [
                ['register_time'],
                'date',
                'format' => 'datetime',
                'message' => 'invalid register_time.',
            ],
            [
                ['register_ip'],
                'ip',
                'message' => 'invalid register_ip.',
            ],
            [
                ['register_source'],
                'string',
                'length' => [1, 50],
                'encoding' => 'utf-8',
                'message' => 'invalid register_source.',
            ],

        ];
    }

}