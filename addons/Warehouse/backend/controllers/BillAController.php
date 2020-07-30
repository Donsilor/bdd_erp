<?php

namespace addons\Warehouse\backend\controllers;

use addons\Style\common\enums\JintuoTypeEnum;
use common\enums\LogTypeEnum;
use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\PutInTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillAForm;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseBillGoodsA;
use addons\Warehouse\common\models\WarehouseGoods;
use common\enums\AuditStatusEnum;
use common\helpers\ExcelHelper;
use common\helpers\PageHelper;
use common\helpers\SnHelper;
use common\helpers\StringHelper;
use common\helpers\Url;
use common\models\base\SearchModel;
use common\traits\Curd;
use yii\db\Exception;


/**
 * Attribute
 *
 * Class AttributeController
 * @package backend\modules\goods\controllers
 */
class BillAController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillAForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_A;

    /**
     * 调拨单
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
            'pageSize' => $this->getPageSize(),
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],

            ]
        ]);

        $dataProvider = $searchModel
            ->search(\Yii::$app->request->queryParams,['updated_at']);

        $dataProvider->key = 'id';

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',Warehousebill::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Warehousebill::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        $audit_time = $searchModel->audit_time;
        if (!empty($audit_time)) {
            $dataProvider->query->andFilterWhere(['>=',Warehousebill::tableName().'.audit_time', strtotime(explode('/', $audit_time)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',Warehousebill::tableName().'.audit_time', (strtotime(explode('/', $audit_time)[1]) + 86400)] );//结束时间
        }

        $dataProvider->query->andWhere(['>',Warehousebill::tableName().'.status',-1]);
        $dataProvider->query->andWhere(['=',Warehousebill::tableName().'.bill_type','A']);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(Warehousebill::tableName().'.id');
            $this->actionExport($queryIds);
        }
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    /**
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillAForm();

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                $isNewRecord = $model->isNewRecord;
                if($isNewRecord){
                    $model->bill_no = SnHelper::createBillSn($this->billType);
                    $model->bill_type = $this->billType;
                    $log_msg = "创建调整单{$model->bill_no}，出库仓库为{$model->fromWarehouse->name}";
                }else{
                    $log_msg = "修改调整单{$model->bill_no}，出库仓库为{$model->fromWarehouse->name}";
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }

                $log = [
                    'bill_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '调整单',
                    'log_msg' => $log_msg
                ];
                \Yii::$app->warehouseService->billLog->createBillLog($log);
                $trans->commit();
                if($isNewRecord) {
                    return $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
                }else{
                    \Yii::$app->getSession()->setFlash('success','保存成功');
                    return $this->redirect(\Yii::$app->request->referrer);
                }
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
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = \Yii::$app->request->get('id');
        $tab = \Yii::$app->request->get('tab',1);
        $returnUrl = \Yii::$app->request->get('returnUrl',Url::to(['bill-m/index']));
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->bill->menuTabList($id,$this->billType, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * @return mixed
     * 申请审核
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if($model->bill_status != BillStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        if($model->goods_num<=0){
            return $this->message('单据明细不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
        }

        $trans = \Yii::$app->db->beginTransaction();
        try{
            $model->bill_status = BillStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;
            if(false === $model->save()){
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            //日志
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '调整单',
                'log_msg' => '单据提审'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }

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
                $trans = \Yii::$app->db->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->bill_status = BillStatusEnum::CONFIRM; //单据状态改成审核
                    //更新库存状态和仓库
                    $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $id])->select(['goods_id'])->all();
                    foreach ($billGoods as $goods){
                        $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK, 'warehouse_id' => $model->to_warehouse_id],['goods_id' => $goods->goods_id, 'goods_status' => GoodsStatusEnum::IN_ADJUS]);
                        if(!$res){
                            throw new Exception("商品{$goods->goods_id}不是调整状态或者不存在，请查看原因");
                        }
                    }
                }else{
                    $model->bill_status = BillStatusEnum::SAVE;
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }

                //日志
                $log = [
                    'bill_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '调整单',
                    'log_msg' => '单据审核'
                ];
                \Yii::$app->warehouseService->billLog->createBillLog($log);
                $trans->commit();
                $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
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
     * 删除/关闭
     *
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }

        try{
            $trans = \Yii::$app->db->beginTransaction();
            $model->bill_status = BillStatusEnum::CANCEL;
            //更新库存状态
            $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $id])->select(['goods_id'])->all();
            foreach ($billGoods as $goods){
                $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK],['goods_id' => $goods->goods_id, 'goods_status' => GoodsStatusEnum::IN_ADJUS]);
                if(!$res){
                    throw new Exception("商品{$goods->goods_id}不是调整中或者不存在，请查看原因");
                }
            }
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }

            //日志
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '调整单',
                'log_msg' => '单据取消'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            \Yii::$app->getSession()->setFlash('success','关闭成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }


        return $this->message("关闭失败", $this->redirect(['index']), 'error');
    }



    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids=null){
        $name = '调整单明细';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }

        list($list,) = $this->getData($ids);
        $header = [
            ['条码号', 'goods_id' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['商品名称', 'goods_name' , 'text'],
            ['产品分类', 'style_cate_name' , 'text'],
            ['产品线', 'product_type_name' , 'text'],
            ['金托类型', 'jintuo_type' , 'text'],
            ['款式性别', 'style_sex' , 'text'],
            ['材质', 'material' , 'text'],
            ['材质颜色', 'goods_color' ,  'text'],
            ['镶口', 'xiangkou' ,  'text'],
            ['手寸类型', 'finger' ,  'text'],
            ['手寸号', 'finger' , 'text'],
            ['尺寸', 'product_size' , 'text'],
            ['货重', 'gold_weight' , 'text'],
            ['净重', 'suttle_weight' , 'text'],
            ['损耗', 'gold_loss' , 'text'],
            ['含耗重', 'gold_weight_sum' , 'text'],
            ['金价', 'gold_price' , 'text'],
            ['金料额', 'gold_amount' , 'text'],
            ['石号', 'main_stone_sn' , 'text'],
            ['粒数', 'main_stone_num' , 'text'],
            ['主石类型', 'main_stone_type' , 'text'],
            ['主石形状', 'diamond_shape' , 'text'],
            ['石重', 'diamond_carat' , 'text'],
            ['颜色', 'diamond_color' ,'text'],
            ['净度', 'diamond_clarity' , 'text'],
            ['切工', 'diamond_cut' , 'text'],
            ['抛光', 'diamond_polish' , 'text'],
            ['对称', 'diamond_symmetry' , 'text'],
            ['荧光', 'diamond_fluorescence' , 'text'],
            ['单价', 'main_stone_price' , 'text'],
            ['金额', 'main_stone_price_sum','text'],
            ['钻石证书类型', 'diamond_cert_type','text'],
            ['钻石证书号', 'diamond_cert_id','text'],
            ['副石1类型	', 'second_stone_type1' , 'text'],
            ['副石1形状', 'second_stone_shape1' , 'text'],
            ['副石1粒数', 'second_stone_num1' , 'text'],
            ['副石1石重', 'second_stone_weight1' , 'text'],
            ['副石1颜色', 'second_stone_color1' , 'text'],
            ['副石1净度', 'second_stone_clarity1' , 'text'],
            ['副石1总计价', 'second_stone_price1' , 'text'],
            ['副石2类型', 'second_stone_type2' , 'text'],
            ['副石2粒数', 'second_stone_num2' , 'text'],
            ['副石2重', 'second_stone_weight2' , 'text'],
            ['工费', 'gong_fee' , 'text'],
            ['补口费', 'bukou_fee' , 'text'],
            ['镶石费', 'xianqian_fee' , 'text'],
            ['证书费', 'cert_fee' , 'text'],
            ['工艺费', 'biaomiangongyi_fee' , 'text'],
            ['总单价', 'price_sum' , 'text'],
            ['备注', 'goods_remark' , 'text']

        ];

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }

    /**
     * 单据打印
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPrint()
    {
        $this->layout = '@backend/views/layouts/print';
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        list($lists,$total) = $this->getData($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'lists' => $lists,
            'total' =>$total
        ]);
    }


    private function getData($ids){
        $select = ['g.*','w.bill_no','w.bill_type','w.bill_status','wg.warehouse_id','wg.style_sn','wg.goods_name','wg.goods_num','wg.put_in_type'
            ,'wg.material','wg.gold_weight','wg.gold_loss','wg.diamond_carat','wg.diamond_color','wg.diamond_clarity',
            'wg.cost_price','wg.diamond_cert_id','wg.goods_remark','type.name as product_type_name','cate.name as style_cate_name'];
        $query = WarehouseBill::find()->alias('w')
            ->leftJoin(WarehouseBillGoods::tableName()." wg",'w.id=wg.bill_id')
            ->leftJoin(WarehouseGoods::tableName().' g','g.goods_id=wg.goods_id')
            ->leftJoin(ProductType::tableName().' type','type.id=g.product_type_id')
            ->leftJoin(StyleCate::tableName().' cate','cate.id=g.style_cate_id')
            ->where(['w.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [
            'goods_num_count' => 0,
            'gold_weight_count' => 0,
            'suttle_weight_count' => 0,
            'gold_amount_count' => 0,
            'main_stone_weight_count' => 0,
            'main_stone_price_sum_count' => 0,
            'second_stone_weight1_count' => 0,
            'second_stone_price1_sum_count' => 0,
            'price_count' => 0,
            'price_sum_count' => 0,
            'cert_fee_count' => 0,

        ];
        foreach ($lists as &$list){
            //成色
            $material = empty($list['material']) ? 0 : $list['material'];
            $list['material'] = \Yii::$app->attr->valueName($material);
            //单据状态
            $list['bill_status'] = BillStatusEnum::getValue($list['bill_status']);
            //入库方式
            $list['put_in_type'] = PutInTypeEnum::getValue($list['put_in_type']);
            //金托类型
            $list['jintuo_type'] = JintuoTypeEnum::getValue($list['jintuo_type']);
            //款式性别
            $list['style_sex'] = StyleSexEnum::getValue($list['style_sex']);
            //钻石颜色
            $material_color = empty($list['material_color']) ? 0 : $list['material_color'];
            $list['material_color'] = \Yii::$app->attr->valueName($material_color);
            //钻石净度
            $diamond_clarity = empty($list['diamond_clarity']) ? 0 : $list['diamond_clarity'];
            $list['diamond_clarity'] = \Yii::$app->attr->valueName($diamond_clarity);
            //钻石形状
            $diamond_shape = empty($list['diamond_shape']) ? 0 : $list['diamond_shape'];
            $list['diamond_shape'] = \Yii::$app->attr->valueName($diamond_shape);
            //钻石切工
            $diamond_cut = empty($list['diamond_cut']) ? 0 : $list['diamond_cut'];
            $list['diamond_cut'] = \Yii::$app->attr->valueName($diamond_cut);
            //钻石抛光
            $diamond_polish = empty($list['diamond_polish']) ? 0 : $list['diamond_polish'];
            $list['diamond_polish'] = \Yii::$app->attr->valueName($diamond_polish);
            //钻石对称
            $diamond_symmetry = empty($list['diamond_symmetry']) ? 0 : $list['diamond_symmetry'];
            $list['diamond_symmetry'] = \Yii::$app->attr->valueName($diamond_symmetry);
            //钻石荧光
            $diamond_fluorescence = empty($list['diamond_fluorescence']) ? 0 : $list['diamond_fluorescence'];
            $list['diamond_fluorescence'] = \Yii::$app->attr->valueName($diamond_fluorescence);
            //钻石证书类型
            $diamond_cert_type = empty($list['diamond_cert_type']) ? 0 : $list['diamond_cert_type'];
            $list['diamond_cert_type'] = \Yii::$app->attr->valueName($diamond_cert_type);
            //主石类型
            $main_stone_type = empty($list['main_stone_type']) ? 0 : $list['main_stone_type'];
            $list['main_stone_type'] = \Yii::$app->attr->valueName($main_stone_type);
            //主石金额
            $main_stone_price = empty($list['main_stone_price']) ? 0 : $list['main_stone_price'];
            $list['main_stone_price_sum'] = $main_stone_price * $list['main_stone_num'];
            //副石1类型
            $second_stone_type1 = empty($list['second_stone_type1']) ? 0 : $list['second_stone_type1'];
            $list['second_stone_type1'] = \Yii::$app->attr->valueName($second_stone_type1);
            //副石1颜色
            $second_stone_color1 = empty($list['second_stone_color1']) ? 0 : $list['second_stone_color1'];
            $list['second_stone_color1'] = \Yii::$app->attr->valueName($second_stone_color1);
            //副石1净度
            $second_stone_clarity1 = empty($list['second_stone_clarity1']) ? 0 : $list['second_stone_clarity1'];
            $list['second_stone_clarity1'] = \Yii::$app->attr->valueName($second_stone_clarity1);
            //副石1形状
            $second_stone_shape1 = empty($list['second_stone_shape1']) ? 0 : $list['second_stone_shape1'];
            $list['second_stone_shape1'] = \Yii::$app->attr->valueName($second_stone_shape1);
            //副石1金额
            $second_stone_price1 = empty($list['second_stone_price1']) ? 0 : $list['second_stone_price1'];
            $list['second_stone_price1_sum'] = $second_stone_price1 * $list['second_stone_num1'];
            //副石2类型
            $second_stone_type2 = empty($list['second_stone_type2']) ? 0 : $list['second_stone_type2'];
            $list['second_stone_type2'] = \Yii::$app->attr->valueName($second_stone_type2);
            //副石2重
            $second_stone_weight2 = empty($list['second_stone_weight2']) ? 0 : $list['second_stone_weight2'];
            $list['second_stone_weight2'] = \Yii::$app->attr->valueName($second_stone_weight2);
            //副石1形状
            $second_stone_weight2 = empty($list['second_stone_weight2']) ? 0 : $list['second_stone_weight2'];
            $list['second_stone_weight2'] = \Yii::$app->attr->valueName($second_stone_weight2);
            //单价
            $list['price'] = $list['cost_price'] + $list['main_stone_price_sum'] + $list['gong_fee']
                + $list['bukou_fee'] + $list['biaomiangongyi_fee'];
            //总额
            $list['price_sum'] = $list['price'] * $list['goods_num'];
            //含耗重
            $gold_loss = empty($list['gold_loss']) ? 0 : $list['gold_loss'];
            $suttle_weight = empty($list['suttle_weight']) ? 0 : $list['suttle_weight'];
            $list['gold_weight_sum'] = $suttle_weight + $gold_loss;

            //统计
            $total['goods_num_count'] += $list['goods_num'];  //件数
            $total['gold_weight_count'] += $list['gold_weight']; //货重
            $total['suttle_weight_count'] += $list['suttle_weight']; //净重
            $total['gold_amount_count'] += $list['gold_amount']; //金料额
            $total['main_stone_weight_count'] += $list['diamond_carat']; //石重
            $total['main_stone_price_sum_count'] += $list['main_stone_price_sum']; //主石金额
            $total['second_stone_weight1_count'] += $list['second_stone_weight1']; //副石石重
            $total['second_stone_price1_sum_count'] += $list['second_stone_price1_sum']; //副石金额
            $total['price_count'] += $list['price']; //单价
            $total['price_sum_count'] += $list['price_sum']; //总额
            $total['cert_fee_count'] += $list['price_sum']; //证书费

        }
        return [$lists,$total];
    }

    //获取已经调整的明细
    public function getDjustNum($bill_id){
        return WarehouseBillGoodsA::find()->where(['bill_id'=>$bill_id,'audit_status'=>AuditStatusEnum::PASS])->count();
    }






}
