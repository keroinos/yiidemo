<?php
/**
 * Created by PhpStorm.
 * User: zengchen01
 * Date: 2016/8/17
 * Time: 19:21
 */

namespace frontend\controllers;
use Yii;
use yii\web\Controller;

class PostController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionCA()
    {
        echo 'test';
        return 'fff';
        //return $this->render('CA',['data'=>'testinfo']);
    }
}