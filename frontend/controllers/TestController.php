<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\helpers\VarDumper;
use common\models\data\user\UserAlias;
use common\models\data\user\UserSecurity;
use common\models\data\user\UserSecurityInfo;
use common\models\data\user\UserInfo;
use common\models\business\user\UserTest;
use frontend\models\test\Room;
use common\models\User;

class TestController extends Controller
{

    //注意要小写和路由请求方式一致
    public $defaultAction = 'show';

    public function actions()
    {
        return [
            'static' => [//定义URL中访问的名称
                'class' => 'yii\web\ViewAction',
                'viewPrefix' => 'static',//说明到哪个物理文件夹下寻找文件
            ],
        ];
    }

    public function actionShow()
    {
        Yii::createObject()
        //echo 'actionShow';
        //echo $this->render('is_page');
        /*$value = 'Yii provides convenient helper functions that allow you to encrypt/decrypt data using a secret key. The data is passed through the encryption function so that only the person which has the secret key will be able to decrypt it. For example, we need to store some information in our database but we need to make sure only the user who has the secret key can view it (even if the application database is compromised';
        $key = 'Yii provides convenient helper functions that allow you to encrypt/decrypt data using a secret key. The data is passed through the encryption function so that only the person which has the secret key will be able to decrypt it. For example, we need to store some information in our database but we need to make sure only the user who has the secret key can view it (even if the application database is compromised';
        $encryptValue =  Yii::$app->getSecurity()->encryptByPassword($value,$key);
        echo Yii::$app->getSecurity()->decryptByPassword($encryptValue,$key);*/
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionCa()
    {
        $aliasname = '中文账号';
        $aliastype = UserAlias::AliasType_Normal;
        $uid = 2;
       /* $alias = new UserAlias($aliasname,$aliastype);
        $alias->uid = $uid;
        $alias->UserAliasExtInfo->isphone = true;
        $alias->UserAliasExtInfo->isemail = true;

        $returnValue = $alias->save();
        var_dump($returnValue);*/
        $aliasSec = new UserSecurity($uid);
        $aliasSec->scenario = UserSecurity::SCENARIO_PASSWORD;
        //$returnValue = $aliasSec->find();
        $aliasSec->password = '';
        //$aliasSec->password = '1234';

        $returnValue = $aliasSec->validate();

        echo '<br/>';
        var_dump($returnValue);
        echo '<br/>';
        var_dump($aliasSec->getErrors());
        echo '<br/>';



        /*$returnValue = $alias->validate();
        var_dump($returnValue);
        echo '<br/>';
        var_dump($alias->getErrors());*/

        /*$aliasname = '中文账号';
        $aliastype = UserAlias::AliasType_Normal;
        $uid = 2;

        $alias = new UserAlias($aliasname,$aliastype);
        $alias->uid = $uid;
        $alias->isemail = true;

        $aliasSec = new UserSecurity($uid);
        $aliasSec->password = 'dsfsdfds';
        $aliasSec->question = 'ccc';
        $aliasSec->answer = 'dfdf';

        $aliasInfo = new UserInfo($uid);
        $aliasInfo->registerip = '127.0.0.1';
        $aliasInfo->registertime = time();
        $aliasInfo->registersource = 'test';
        
        $register = new UserTest();
        $register->alias = $alias;
        $register->secinfo = $aliasSec;
        $register->userinfo = $aliasInfo;

        $aliasSecValue = $register->SaveAll();
        var_dump($aliasSecValue);
        var_dump($register->getErrors());*/

    }

    public function actionCreate()
    {
        $model = new Room();
        $modelCanSave = false;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->fileImage = UploadedFile::getInstance($model, 'fileImage');

            if ($model->fileImage) {
                $model->fileImage->saveAs(Yii::getAlias('@uploadedfilesdir/') . $model->fileImage->baseName . '.' . $model->fileImage->extension);
            }

            $modelCanSave = true;
        }

        return $this->render('create', [
            'model' => $model,
            'modelCanSave' => $modelCanSave
        ]);
    }
}