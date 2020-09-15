<?php

namespace addons\Warehouse\backend\controllers;

use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\enums\LogTypeEnum;
use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Supply\common\enums\PeishiTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\PeiLiaoWayEnum;
use addons\Warehouse\common\enums\PutInTypeEnum;
use addons\Warehouse\common\forms\WarehouseGoodsForm;
use addons\Warehouse\common\forms\WarehousGoodsSearchForm;
use addons\Warehouse\common\models\WarehouseGoods;
use common\enums\AuditStatusEnum;
use common\enums\ConfirmEnum;
use common\helpers\ExcelHelper;
use common\helpers\PageHelper;
use common\helpers\ResultHelper;
use common\helpers\StringHelper;
use common\helpers\Url;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;


/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class WarehouseGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseGoods::class;
    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['goods_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->getPageSize(),
            'relations' => [
                'productType' => ['name','is_inlay'],
                'styleCate' => ['name'],
                'supplier' => ['supplier_name'],
                'warehouse' => ['name'],
                'weixiuWarehouse' => ['name'],
                'creator' => ['username'],

            ]
        ]);

        $search = new WarehousGoodsSearchForm();
        $search->attributes = Yii::$app->request->get();
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['updated_at']);
        if(!Yii::$app->request->get()){
            $search->goods_status = GoodsStatusEnum::IN_STOCK;
            $searchModel->goods_status = GoodsStatusEnum::IN_STOCK;
        }

        $dataProvider->query
            ->andFilterWhere(['goods_status'=>$search->goods_status])
            ->andFilterWhere(['material_type'=>$search->material_type])
            ->andFilterWhere(['jintuo_type'=>$search->jintuo_type])
            ->andFilterWhere(['main_stone_type'=>$search->main_stone_type])
            ->andFilterWhere(['in', 'goods_id', $search->goods_ids()])
            ->andFilterWhere(['in', 'style_cate_id', $search->styleCateIds()])
            ->andFilterWhere(['in', 'product_type_id', $search->proTypeIds()])
            ->andFilterWhere(['like', 'goods_name', $search->goods_name()])
//            ->andFilterWhere($search->betweenGoldWeight())
            ->andFilterWhere($search->betweenSuttleWeight())
            ->andFilterWhere($search->betweenDiamondCarat())
            ->andFilterWhere(['in', 'warehouse_id', $search->warehouse_id])
            ->andFilterWhere(['in', 'supplier_id', $search->supplier_id])
            ->andFilterWhere(['in', 'style_channel_id', $search->style_channel_id])
            ->andFilterWhere(['in', 'goods_source', $search->goods_source])
            ->andFilterWhere($search->goods_sn());


        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseGoods::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseGoods::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }


        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(WarehouseGoods::tableName().'.id');
            $this->actionExport($queryIds);
        }


        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'search' => $search,
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
        $goods_id = Yii::$app->request->get('goods_id');
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['warehouser-goods/index']));
        if(empty($id) && !empty($goods_id)){
            $goodsInfo = WarehouseGoods::find()->where(['goods_id'=>$goods_id])->asArray()->one();
            $id = $goodsInfo['id']??0;
        }
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->warehouseGoods->menuTabList($id,$returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * @return array|mixed|string
     * WarehouseGoodsForm $model
     */
    public function actionEdit(){
        $this->layout = '@backend/views/layouts/iframe';
        $id = Yii::$app->request->get('id', null);
        $this->modelClass = WarehouseGoodsForm::class;
        $model = $this->findModel($id);
        $old_model = clone $model;
        $model = $model ?? new WarehouseGoodsForm();
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{
                $trans = Yii::$app->trans->beginTransaction();

                $data = \Yii::$app->request->post('WarehouseGoodsForm');
                $model->apply_info = json_encode($data);
                $model->is_apply = ConfirmEnum::YES;
                $model->audit_status = AuditStatusEnum::SAVE;
                $model->apply_id = \Yii::$app->user->identity->getId();
                if(false === $model->save(true,['apply_id', 'is_apply', 'apply_info','audit_status'])) {
                    throw new \Exception("保存失败",500);
                }

                //日志
                $log_msg = '申请修改；';
                foreach ($data as $k=>$val) {
                    $old = $old_model->$k;
                    if($old != $val){
                        $log_msg .= "{$model->getAttributeLabel($k)} 由 ({$old}) 改成 ({$val})";
                    }
                }
                $log = [
                    'goods_id' => $model->id,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_msg' => $log_msg
                ];
                \Yii::$app->warehouseService->warehouseGoods->createWarehouseGoodsLog($log);


                $trans->commit();
                //前端提示
                return ResultHelper::json(200, '操作成功');
            }catch (\Exception $e){
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $model->initApplyEdit();
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
        if($model->audit_status != AuditStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->audit_status = AuditStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

    }

    /**
     * 查看审批
     * @property PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionApplyView()
    {

        $id = Yii::$app->request->get('id');
        $this->modelClass = WarehouseGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseGoodsForm();
        $model->initApplyView();

        return $this->render($this->action->id, [
            'model' => $model,
            'returnUrl'=>$this->returnUrl
        ]);
    }
    /**
     * 申请编辑-审核(ajax)
     * @property PurchaseGoodsForm $model
     * @return mixed
     */
    public function actionApplyAudit()
    {

        $returnUrl = Yii::$app->request->get('returnUrl',Yii::$app->request->referrer);

        $id = Yii::$app->request->get('id');

        $this->modelClass = WarehouseGoodsForm::class;
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseGoodsForm();
        $old_model = clone $model;

        $model->audit_status = AuditStatusEnum::PASS;
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->trans->beginTransaction();
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->initApplyEdit();
                    //日志
                    $log_msg = '审核通过；';
                    $data = json_decode($model->apply_info,true) ?? [];
                    foreach ($data as $k=>$val) {
                        $old = $old_model->$k;
                        if($old != $val){
                            $log_msg .= "{$model->getAttributeLabel($k)} 由 ({$old}) 改成 ({$val})";
                        }
                    }
                    $log = [
                        'goods_id' => $model->id,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_msg' => $log_msg
                    ];
                    \Yii::$app->warehouseService->warehouseGoods->createWarehouseGoodsLog($log);
                }
                $model->apply_id = '';
                $model->apply_info = '';
                $model->is_apply = ConfirmEnum::NO;
                $model->save(false);
                $trans->commit();
                return $this->message("操作成功", $this->redirect(['warehouse-goods/view','id'=>$id]), 'success');
            }catch (\Exception $e){
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect($returnUrl), 'error');
            }

        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }



    public function actionEditss()
    {
        $this->layout = '@backend/views/layouts/iframe';
        $id = Yii::$app->request->get('id', null);
        $model = $this->findModel($id);
        $old_model = clone $model;
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                $param = Yii::$app->request->post('WarehouseGoods');
                foreach ($param as $key=>$new){
                    $old = $old_model->$key;
                    if($old != $new){
                        $log_msg = "{$model->getAttributeLabel($key)} 由 ({$old}) 改成 ({$new})";
                        $log = [
                            'goods_id' => $model->id,
                            'log_type' => LogTypeEnum::ARTIFICIAL,
                            'log_msg' => $log_msg
                        ];
                        Yii::$app->warehouseService->warehouseGoods->createWarehouseGoodsLog($log);
                    }
                }
                $model->save();
                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(Yii::$app->request->referrer);
            }catch(\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }


    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids=null){
        $name = '库存';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('库存ID不为空', $this->redirect(['index']), 'warning');
        }

        list($list,) = $this->getData($ids);
        $header = [
            ['条码号', 'goods_id' , 'text'],
            ['款号', 'style_sn' , 'text'],
            ['起版号', 'qiban_sn' , 'text'],
            ['产品分类', 'style_cate_name' , 'text'],
            ['产品线', 'product_type_name' , 'text'],
            ['商品名称', 'goods_name' , 'text'],
            ['商品状态', 'goods_status' , 'text'],
            ['金托类型', 'jintuo_type' , 'text'],
            ['材质', 'material_type' , 'text'],
            ['商品数量', 'goods_num' , 'text'],
            ['手寸（美）', 'finger' , 'text'],
            ['手寸（港）', 'finger_hk' , 'text'],
            ['尺寸', 'length' , 'text'],
            ['成品尺寸', 'product_size' , 'text'],
            ['镶口', 'xiangkou' ,  'text'],
            ['刻字', 'kezi' ,  'text'],
            ['链类型', 'chain_type' ,  'text'],
            ['扣环', 'cramp_ring' ,  'text'],
            ['爪头形状	', 'talon_head_type' ,  'text'],
            ['配料方式	', 'peiliao_way' ,  'text'],
            ['连石重(g)', 'suttle_weight' , 'text'],
            ['金重(g)', 'gold_weight' , 'text'],
            ['损耗(%)', 'gold_loss' , 'text'],
            ['含耗重(g)	', 'gross_weight' , 'text'],
            ['折足(g)', 'pure_gold' , 'text'],
            ['金价', 'gold_price' , 'text'],
            ['金料额', 'gold_amount' , 'text'],
            ['主石配石类型', 'main_peishi_type' , 'text'],
            ['主石编号', 'main_stone_sn' , 'text'],
            ['主石配石方式', 'main_peishi_way' , 'text'],
            ['主石类型	', 'main_stone_type' , 'text'],
            ['主石数量	', 'main_stone_num' , 'text'],
            ['主石形状	', 'diamond_shape' , 'text'],
            ['主石重', 'diamond_carat' , 'text'],
            ['主石单价', 'main_stone_price' , 'text'],
            ['主石成本', 'main_stone_cost' , 'text'],
            ['主石颜色', 'diamond_color' ,'text'],
            ['主石净度', 'diamond_clarity' , 'text'],
            ['主石切工', 'diamond_cut' , 'text'],
            ['主石色彩', 'main_stone_colour' , 'text'],
            ['副石1编号', 'second_stone_sn1' , 'text'],
            ['副石1配石方式', 'second_peishi_way1' , 'text'],
            ['副石1类型	', 'second_stone_type1' , 'text'],
            ['副石1数量', 'second_stone_num1' , 'text'],
            ['副石1形状', 'second_stone_shape1' , 'text'],
            ['副石1重量', 'second_stone_weight1' , 'text'],


            ['款式性别', 'style_sex' , 'text'],
            ['材质颜色', 'goods_color' ,  'text'],
            ['货重', 'gold_weight' , 'text'],

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
    private function getData($ids){
        $select = ['g.*','type.name as product_type_name','cate.name as style_cate_name'];
        $query = WarehouseGoods::find()->alias('g')
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
            //商品状态
            $list['goods_status'] = GoodsStatusEnum::getValue($list['goods_status']);
            //材质
            $material_type = empty($list['material_type']) ? '' : $list['material_type'];
            $list['material_type'] = \Yii::$app->attr->valueName($material_type);
            //材质颜色
            $material_color = empty($list['material_color']) ? '' : $list['material_color'];
            $list['material_color'] = \Yii::$app->attr->valueName($material_color);

            //手寸（美）
            $finger = empty($list['finger']) ? '' : $list['finger'];
            $list['finger'] = \Yii::$app->attr->valueName($finger);
            //手寸（港）
            $finger_hk = empty($list['finger_hk']) ? '' : $list['finger_hk'];
            $list['finger_hk'] = \Yii::$app->attr->valueName($finger_hk);
            //镶口
            $xiangkou = empty($list['xiangkou']) ? '' : $list['xiangkou'];
            $list['xiangkou'] = \Yii::$app->attr->valueName($xiangkou);
            //链类型
            $chain_type = empty($list['chain_type']) ? '' : $list['chain_type'];
            $list['chain_type'] = \Yii::$app->attr->valueName($chain_type);
            //扣环
            $cramp_ring = empty($list['cramp_ring']) ? '' : $list['cramp_ring'];
            $list['cramp_ring'] = \Yii::$app->attr->valueName($cramp_ring);
            //扣环
            $talon_head_type = empty($list['talon_head_type']) ? '' : $list['talon_head_type'];
            $list['talon_head_type'] = \Yii::$app->attr->valueName($talon_head_type);
            //入库方式
            $list['put_in_type'] = PutInTypeEnum::getValue($list['put_in_type']);
            //金托类型
            $list['jintuo_type'] = JintuoTypeEnum::getValue($list['jintuo_type']);
            //款式性别
            $list['style_sex'] = StyleSexEnum::getValue($list['style_sex']);

            //入库方式
            $list['peiliao_way'] = PeiLiaoWayEnum::getValue($list['peiliao_way']);
            //主石配石类型
            $list['main_peishi_type'] = PeishiTypeEnum::getValue($list['main_peishi_type']);
            //主石配石方式
            $list['main_peishi_way'] = PeishiTypeEnum::getValue($list['main_peishi_type']);
            //主石类型
            $main_stone_type = empty($list['main_stone_type']) ? '' : $list['main_stone_type'];
            $list['main_stone_type'] = \Yii::$app->attr->valueName($main_stone_type);

            //钻石形状
            $diamond_shape = empty($list['diamond_shape']) ? 0 : $list['diamond_shape'];
            $list['diamond_shape'] = \Yii::$app->attr->valueName($diamond_shape);

            //主石成本
            $main_stone_price = empty($list['main_stone_price']) ? 0 : $list['main_stone_price'];
            $diamond_carat = empty($list['diamond_carat']) ? 0 : $list['diamond_carat'];
            $list['main_stone_cost'] = $main_stone_price * $diamond_carat;

            //钻石颜色
            $diamond_color = empty($list['diamond_color']) ? 0 : $list['diamond_color'];
            $list['diamond_color'] = \Yii::$app->attr->valueName($diamond_color);

            //钻石净度
            $diamond_clarity = empty($list['diamond_clarity']) ? 0 : $list['diamond_clarity'];
            $list['diamond_clarity'] = \Yii::$app->attr->valueName($diamond_clarity);
            //钻石切工
            $diamond_cut = empty($list['diamond_cut']) ? 0 : $list['diamond_cut'];
            $list['diamond_cut'] = \Yii::$app->attr->valueName($diamond_cut);
            //钻石色彩
            $main_stone_colour = empty($list['main_stone_colour']) ? 0 : $list['main_stone_colour'];
            $list['main_stone_colour'] = \Yii::$app->attr->valueName($main_stone_colour);






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




}
