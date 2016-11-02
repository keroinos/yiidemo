<?php
namespace common\models\business\user;
/**
 * Created by PhpStorm.
 * User: zengchen01
 * Date: 2016/9/12
 * Time: 15:40
 */

use common\models\data\user\UserAlias;
use common\models\data\user\UserSecurity;
use Yii;

class UserTest extends \yii\base\Model
{
    public $alias;
    public $secinfo;
    public $userinfo;

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('userdb');
    }

    public function SaveAll()
    {
        $db = self::getDb();
        $transaction = $db->beginTransaction();

        try {

            $this->alias->save($db);
            $this->secinfo->save($db);
            $this->userinfo->save($db);

            $transaction->commit();

        } catch (\Exception $e) {

            $transaction->rollBack();

            throw $e;
        }
    }


}