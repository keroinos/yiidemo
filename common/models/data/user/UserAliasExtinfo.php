<?php
/**
 * 账号别名数据 扩展信息数据
 * User: zengchen01
 * Date: 2016/9/8
 * Time: 18:18
 */

namespace common\models\data\user;


class UserAliasExtinfo extends \yii\base\Model
{
    /*
     * 数据结构定义
     * 业务数据结构对象封装了需要存储在扩展字段中的业务数据
     * */

    //是否绑定手机
    public $isphone = false;
    //是否绑定邮件
    public $isemail = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['isphone', 'isemail'],
                'required'
            ],
            [
                ['isphone', 'isemail'],
                'boolean'
            ]
        ];
    }

}