<?php

namespace addons\Style\backend\controllers;

use addons\Style\common\enums\LogTypeEnum;
use common\enums\AttrIdEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\Style\common\models\Style;
use Yii;
use addons\Style\common\models\Diamond;
use common\traits\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;
use common\helpers\ResultHelper;
use yii\base\Exception;
use common\helpers\ExcelHelper;
use addons\Style\common\models\DiamondLang;

/**
* Diamond
*
* Class DiamondController
* @package backend\modules\goods\controllers
*/
class DiamondController extends BaseController
{
    use Curd;

    /**
    * @var Diamond
    */
    public $modelClass = Diamond::class;


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
            ->search(Yii::$app->request->queryParams, ['goods_name','language']);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    /**
     * 编辑/创建 多语言
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);
        
        $model = $this->findModel($id);
        $status = $model ? $model->status:0;
        // ajax 验证
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                if($model->isNewRecord == true){
                    $model->onsale_time = time();
                    $model->status = StatusEnum::DISABLED;
                }
                $model->audit_status = AuditStatusEnum::SAVE;
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }
                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                $error = $e->getMessage();
                \Yii::error($error);
                return $this->message("保存失败:".$error, $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }
            
            return $this->message("保存成功", $this->redirect($returnUrl), 'success');
        }
        
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }


    /**
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }


    /**
     * @return mixed
     * 申请审核
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model->audit_status = AuditStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

    }


    /**
     * ajax 审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);

        if($model->audit_status == AuditStatusEnum::PENDING) {
            $model->audit_status = AuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->status = StatusEnum::ENABLED;
                }

                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                //日志
                $log = [
                    'diamond_id' => $model->id,
                    'cert_id' => $model->cert_id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_msg' => '裸钻审核：'.AuditStatusEnum::getValue($model->audit_status),
                    'creator_id' => Yii::$app->user->identity->getId(),
                    'creator' => \Yii::$app->user->identity->username,
                ];
                \Yii::$app->styleService->diamond->createLog($log);
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(\Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }
    
    /**
     * 生成商品名称
     * @return string[]|array[]
     */
    public function actionGetGoodsName(){
        $carat = Yii::$app->request->post('carat',null);
        $cert_type_id = Yii::$app->request->post('cert_type',null);
        $shape_id = Yii::$app->request->post('shape',null);
        $color_id = Yii::$app->request->post('color',null);
        $clarity_id = Yii::$app->request->post('clarity',null);

        $carat_str = '';
        if(!empty($carat)){
            $carat_str .= $carat.'ct';
        }
        $goods_name = $carat_str;
        if($shape_id){
            $goods_name .= ' '.Yii::$app->attr->valueName($shape_id);
        }
        if($color_id){
            $goods_name .= ' '.Yii::$app->attr->valueName($color_id).'色';
        }
        if($clarity_id){
            $goods_name .= ' '.Yii::$app->attr->valueName($clarity_id).'净度';
        }
        if($cert_type_id){
            $goods_name .= ' '.Yii::$app->attr->valueName($cert_type_id);
        }
        $data = $goods_name;

        return ResultHelper::json(200, '保存成功',$data);

    }

    /**
     * ajax更新排序/状态
     *
     * @param $id
     * @return array
     */
    public function actionAjaxUpdate($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return ResultHelper::json(404, '找不到数据');
        }

        $model->attributes = ArrayHelper::filter(Yii::$app->request->get(), ['sort', 'status']);
        if (!$model->save(false)) {
            return ResultHelper::json(422, $this->getError($model));
        }else{
            Style::updateAll(['status'=>$model->status],['id'=>$model->style_id]);
        }
        return ResultHelper::json(200, '修改成功');
    }

    /**
     * 导出Excel
     *
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'relations'=>[
                'lang' => ['goods_name'],
            ],
            'pageSize' => $this->pageSize
        ]);
        //print_r(Yii::$app->request->get());exit;
        $param['SearchModel'] = Yii::$app->request->get();
        $dataProvider = $searchModel->search($param);
        $dataProvider->setPagination(false);
        $list = $dataProvider->models;
//        print_r($list);exit;

        // [名称, 字段名, 类型, 类型规则]
        $header = [
            ['ID', 'id'],
            ['名称', 'goods_name', 'text'],
            ['编码', 'goods_sn', 'text'],
            ['证书类型', 'cert_type', 'selectd',\common\enums\DiamondEnum::getCertTypeList()],
            ['证书号', 'cert_id', 'text'],
            ['库存', 'goods_num', 'text'],
            ['成本价', 'cost_price', 'text'],
            ['市场价', 'market_price', 'text'],
            ['销售价', 'sale_price', 'text'],
            ['石重', 'carat', 'text'],
            ['净度', 'clarity', 'selectd',\common\enums\DiamondEnum::getClarityList()],
            ['切工', 'cut', 'selectd',\common\enums\DiamondEnum::getCutList()],
            ['颜色', 'color', 'selectd',\common\enums\DiamondEnum::getColorList()],
            ['形状', 'shape', 'selectd',\common\enums\DiamondEnum::getShapeList()],
            ['荧光', 'fluorescence', 'selectd',\common\enums\DiamondEnum::getFluorescenceList()],
            ['切割深度(%)', 'depth_lv', 'text'],
            ['台宽(%)', 'table_lv', 'text'],
            ['对称', 'symmetry', 'selectd',\common\enums\DiamondEnum::getSymmetryList()],
            ['抛光', 'polish', 'selectd',\common\enums\DiamondEnum::getPolishList()],
            ['石底层', 'stone_floor', 'text'],
            ['长度', 'length', 'text'],
            ['宽度', 'width', 'text'],
            ['长宽比(%)', 'aspect_ratio', 'text'],
            ['售后服务', 'sale_services', 'text'],
            ['货品来源', 'source_id', 'text'],
            ['来源折扣', 'source_discount', 'text'],
//            ['库存类型', 'is_stock', 'text'],
            ['上架时间', 'onsale_time', 'date', 'Y-m-d'],
            ['上架状态', 'status', 'selectd',\common\enums\StatusEnum::getMap()],
            ['创建时间', 'created_at', 'date', 'Y-m-d'],
            ['更新时间', 'updated_at', 'date', 'Y-m-d'],

        ];


        return ExcelHelper::exportData($list, $header, '裸钻数据导出_' . time());
    }
}
