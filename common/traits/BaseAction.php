<?php

namespace common\traits;

use Yii;
use yii\base\Model;
use common\components\Init;
use yii\web\UnprocessableEntityHttpException;

/**
 * trait BaseAction
 * @package common\traits
 * @author jianyan74 <751393839@qq.com>
 */
trait BaseAction
{
    protected $merchant_id;

    /**
     * 默认分页
     *
     * @var int
     */
    protected $pageSize = 10;
    
    protected $noAuthOptional = [];

    /**
     * 商户id
     *
     * @return int
     */
    public function getMerchantId()
    {
        if (!$this->merchant_id) {
            $this->merchant_id = Yii::$app->services->merchant->getId();
        }

        return $this->merchant_id;
    }

    /**
     * @param Model $model
     * @return string
     */
    public function getError(Model $model)
    {
        return $this->analyErr($model->getFirstErrors());
    }

    /**
     * 重载配置
     *
     * @param $merchant_id
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function afreshLoad($merchant_id)
    {
        // 微信配置 具体可参考EasyWechat
        Yii::$app->params['wechatConfig'] = [];
        // 微信支付配置 具体可参考EasyWechat
        Yii::$app->params['wechatPaymentConfig'] = [];
        // 微信小程序配置 具体可参考EasyWechat
        Yii::$app->params['wechatMiniProgramConfig'] = [];
        // 微信开放平台第三方平台配置 具体可参考EasyWechat
        Yii::$app->params['wechatOpenPlatformConfig'] = [];
        // 微信企业微信配置 具体可参考EasyWechat
        Yii::$app->params['wechatWorkConfig'] = [];
        // 微信企业微信开放平台 具体可参考EasyWechat
        Yii::$app->params['wechatOpenWorkConfig'] = [];

        (new Init())->afreshLoad($merchant_id);
    }

    /**
     * 解析错误
     *
     * @param $fistErrors
     * @return string
     */
    protected function analyErr($firstErrors)
    {
        return Yii::$app->debris->analyErr($firstErrors);
    }

    /**
     * @param $model \yii\db\ActiveRecord|Model
     * @throws \yii\base\ExitException
     */
    protected function activeFormValidate($model)
    {
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                Yii::$app->response->data = \yii\widgets\ActiveForm::validate($model);
                Yii::$app->end();
            }
        }
    }
    protected function formValidate($model)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->data = \yii\widgets\ActiveForm::validate($model);
        Yii::$app->end();
    }
    /**
     * 错误提示信息
     *
     * @param string $msgText 错误内容
     * @param string $skipUrl 跳转链接
     * @param string $msgType 提示类型 [success/error/info/warning]
     * @return mixed
     */
    protected function message($msgText, $skipUrl, $msgType = null)
    {
        if (!$msgType || !in_array($msgType, ['success', 'error', 'info', 'warning'])) {
            $msgType = 'success';
        }
        $msgText = \yii\helpers\StringHelper::truncate($msgText, 900);
        Yii::$app->getSession()->setFlash($msgType, $msgText);
        return $skipUrl;
    }
    
    /**
     * 新增/编辑多语言
     * @param unknown $model
     * @param string $is_ajax
     */
    protected function editLang(& $model,$is_ajax = false){
        
        $langModel = $model->langModel();
        $langClassName = substr(strrchr($langModel->className(), '\\'), 1);
        $langPosts = Yii::$app->request->post($langClassName);
        if(empty($langPosts)){
            return false;
        }
        foreach ($langPosts as $lang_key=>$lang_post){
            $is_new = true;
            foreach ($model->langs as $langModel){
                if($lang_key == $langModel->language){
                    $langModel->load([$langClassName =>$langPosts[$langModel->language]]);
                    $model->link('langs', $langModel);
                    $is_new = false;
                    break;
                }
            }
            if($is_new == true){
                $langModel = $model->langModel();
                $langModel->load([$langClassName =>$lang_post]);
                $langModel->master_id = $model->id;
                $langModel->language = $lang_key;
                if(false === $langModel->save()){
                    throw new UnprocessableEntityHttpException($this->getError($langModel));
                }
            }
        }
        
        return true;
    }
    
    /**
     * 获取分页大小
     * @param number $default
     * @return array|mixed
     */
    public function getPageSize($default = 10)
    {
        return Yii::$app->request->get('per-page', $default);
    }
}