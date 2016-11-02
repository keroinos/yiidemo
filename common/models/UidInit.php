<?php
/**
 * 获取注册用的初始化账号ID
 * TODO:目前简单版本,后续要升级为REDIS缓存版本或者其他缓存版本  redis,sqllite
 * User: zengchen01
 * Date: 2016/8/25
 * Time: 10:18
 */

namespace common\models;

use yii;

class UidInit
{
    //获取可用UID
    public function GetUidFromDB($count=1)
    {
        $sql = 'call sp_getuidlist(:count);';
        $uid = Yii::$app->userinitdb
            ->createCommand($sql)
            ->bindParam(':count', $count)
            ->queryScalar();

        if(!isset($uid)){
            return false;
        }

        return $uid;
    }

}