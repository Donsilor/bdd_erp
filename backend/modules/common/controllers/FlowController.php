<?php

namespace backend\modules\common\controllers;

use addons\Purchase\common\models\Purchase;
use common\enums\AuditStatusEnum;
use common\enums\FlowStatus;
use common\enums\FlowStatusEnum;
use common\enums\TargetTypeEnum;
use common\helpers\ResultHelper;
use common\models\base\SearchModel;
use common\models\common\Flow;
use common\models\common\FlowDetails;
use Yii;
use common\traits\Curd;
use common\models\common\ConfigCate;
use backend\controllers\BaseController;

/**
 * Class ConfigCateController
 * @package backend\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class FlowController extends BaseController
{
    use Curd;
    protected $authOptional = ['audit-site'];
    /**
     * @var ConfigCate
     */
    public $modelClass = Flow::class;
    public $enableCsrfValidation = false;

    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    /**
     * ajax 审核流程
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAuditView()
    {
        $flow_type_id = Yii::$app->request->get('flow_type_id');
        $target_id = Yii::$app->request->get('target_id');
        $flow = Flow::find()->where(['flow_type'=>$flow_type_id,'target_id' => $target_id])->orderBy('id desc')->one();
        if(empty($flow)){
            exit;
        }
        $flow_detail = FlowDetails::find()->where(['flow_id'=>$flow->id])->all();
        return $this->renderAjax($this->action->id, [
            'flow_detail' => $flow_detail,
        ]);
    }



    //编辑时获取单个戒指数据
    public function actionGetFlow(){
        $post = Yii::$app->request->post();
        if(!isset($post['flow_id']) || empty($post['flow_id'])){
            return ResultHelper::json(422, '参数错误');
        }
        $id = $post['flow_id'];
//        $model = $this->findModel($id);
        $model = Flow::find()->where(['id'=>$id])->select(['id','flow_name','target_no','flow_status'])->asArray()->all();
        foreach ($model as &$val){
            $val['flow_status'] = FlowStatusEnum::getValue($val['flow_status']);
        }
        return ResultHelper::json(200, '保存成功',$model);

    }




}