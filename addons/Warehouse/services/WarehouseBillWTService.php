<?php

namespace addons\Warehouse\services;


use Yii;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\enums\PandianStatusEnum;
use addons\Warehouse\common\forms\WarehouseBillWForm;
use addons\Warehouse\common\models\WarehouseBillW;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\enums\AuditStatusEnum;
use addons\Warehouse\common\enums\BillWStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\PandianAdjustEnum;
use addons\Warehouse\common\models\WarehouseBillGoodsW;
use common\enums\ConfirmEnum;
use common\enums\LogTypeEnum;
use addons\Warehouse\common\forms\ImportBillWForm;
use common\helpers\UploadHelper;
use common\helpers\ExcelHelper;

/**
 * 临时盘点单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillWTService extends WarehouseBillService
{
    
    /**
     * 创建盘点单
     * @param WarehouseBillWForm $form
     * @throws \Exception
     */
    public function createBillW($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        //锁定仓库
        \Yii::$app->warehouseService->warehouse->lockWarehouse($form->to_warehouse_id);
        
        $bill = new WarehouseBill();
        $bill->attributes = $form->toArray();
        $bill->bill_status = BillStatusEnum::SAVE;
        if(false === $bill->save() ) {
            throw new \Exception($this->getError($bill));
        }
        
        //批量创建单据明细
        $page_size = 100;
        for($page = 1; $page <= 200 ; $page ++) {
            $goods_list = WarehouseGoods::find()->where(['warehouse_id'=>$bill->to_warehouse_id,'goods_status'=>GoodsStatusEnum::IN_STOCK])->limit($page_size)->asArray()->all();
            if(!empty($goods_list)) {
                $bill_goods_values = [];
                foreach ($goods_list as $goods) {
                    $goods_ids[] = $goods['goods_id'];
                    $bill_goods = [
                            'bill_id'=>$bill->id,
                            'bill_type'=>$bill->bill_type,
                            'bill_no'=>$bill->bill_no,
                            'goods_id'=>$goods['goods_id'],
                            'style_sn'=>$goods['style_sn'],
                            'goods_name'=>$goods['goods_name'],
                            'goods_num'=>$goods['stock_num'],
                            'cost_price'=>$goods['cost_price'],
                            'market_price'=>$goods['market_price'],
                            'to_warehouse_id'=>$goods['warehouse_id'],
                            'status'=> PandianStatusEnum::SAVE,
                    ];
                    $bill_goods_values[] = array_values($bill_goods);
                }
                if(empty($bill_goods_keys)) {
                    $bill_goods_keys = array_keys($bill_goods);
                }
                //更新仓库所有货品 盘点中
                WarehouseGoods::updateAll(['goods_status'=>GoodsStatusEnum::IN_PANDIAN],['goods_id'=>$goods_ids,'goods_status'=>GoodsStatusEnum::IN_STOCK]);
                //导入明细
                $result = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $bill_goods_keys, $bill_goods_values)->execute();
                if(!$result) {
                    throw new \Exception('导入单据明细失败');
                }
                
            }
            
            if(count($goods_list) < $page_size) {
                break;
            }
        }
        
        //同步盘点明细关系表
        $sql = "insert into ".WarehouseBillGoodsW::tableName().'(id,should_num) select id,goods_num from '.WarehouseBillGoods::tableName()." where bill_id=".$bill->id;
        $should_num = Yii::$app->db->createCommand($sql)->execute();
        if(false === $should_num) {
            throw new \Exception('导入单据明细失败2');
        }
        //盘点单附属表
        $billW = new WarehouseBillW();
        $billW->id = $bill->id;
        $billW->should_num = $should_num;
        
        if(false === $billW->save()){
            throw new \Exception($this->getError($billW));
        }
        
        //更新应盘数量和总金额
        $this->billWSummary($bill->id);
        return $bill;
    }
    /**
     * 盘点数量
     */
    public function pandianNum($id, $actual_num)
    {
        if($actual_num < 0) {
            throw new \Exception("实盘数量不能小于0，且必须为数字");
        }
        $billGoods = WarehouseBillGoods::find()->where(['id'=>$id])->one();
        if($billGoods->goods_num > $actual_num) {
            $billGoods->status = PandianStatusEnum::LOSS;//盘亏
        }else if($billGoods->goods_num < $actual_num) {
            $billGoods->status = PandianStatusEnum::PROFIT;//盘盈
        }else if($billGoods->goods_num = $actual_num) {
            $billGoods->status = PandianStatusEnum::NORMAL;//正常
        }
        //如果仓库不对，为 盘盈
        if($billGoods->to_warehouse_id != $billGoods->from_warehouse_id) {
            $billGoods->status = PandianStatusEnum::PROFIT;//盘盈
        }
        
        if(false === $billGoods->save(true,['status'])) {
            throw new \Exception($this->getError($billGoods));
        }
        
        //更改实盘数量
        WarehouseBillGoodsW::updateAll(['actual_num'=>$actual_num],['id'=>$id]);
        //汇总统计
        $this->billWSummary($billGoods->bill_id);
        
        return true;
    }
    /**
     * 批量导入盘点货品
     * @param ImportBillWForm $form
     */
    public function importGoods($form)
    {
        if (!($form->file->tempName ?? true)) {
            throw new \Exception("请上传文件");
        }
        if (UploadHelper::getExt($form->file->name) != 'xlsx') {
            throw new \Exception("请上传xlsx格式文件");
        }
        if ($form->bill_id == '') {
            throw new \Exception("bill_id不能为空");
        }
        
        $startRow = 1;
        $endColumn = count($form->columns);
        $rows = ExcelHelper::import($form->file->tempName, $startRow, $endColumn, $form->columns);//从第1行开始,第4列结束取值
        if(!isset($rows[$startRow+1])) {
            throw new \Exception("导入数据不能为空");
        }
        //1.数据校验及格式化
        foreach ($rows as $rowIndex=> & $row) {
            if($rowIndex == $startRow) {
                $form->titles = $row;
                continue;
            }
            if(($form->titles['goods_num'] ?? '') != '盘点数量') {
                throw new \Exception("数据模板有变动，请下载最新模板");
            }
            //加载表格行数据 并且数据校验
            if(empty(array_filter($row)) || false === $form->loadRow($row,$rowIndex)){
                continue;
            }
            try {
                $this->pandianGoods($form->bill_id, [$form->goods_id=>$form->goods_num]);
            }catch (\Exception $e) {
                $form->addRowError($rowIndex, 'error', $e->getMessage());
            }
        }
        $form->showImportMessage();
    }
    /**
     * 盘点商品操作
     * @param int $bill_id
     * @param array $goods_ids_nums  [货号=>12]
     * @throws \Exception
     */
    public function pandianGoods($bill_id, $goods_ids_nums)
    {
        //校验单据状态
        $bill = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        if ($bill->bill_status != BillStatusEnum::SAVE) {
            throw new \Exception("盘点单已结束");
        }
        // $bill_detail_ids = [];
        foreach ($goods_ids_nums as $goods_id=>$num) {
            $isNewRecord = false;
            $billGoods = WarehouseBillGoods::find()->where(['goods_id'=>$goods_id,'bill_id'=>$bill->id])->one();
            if($billGoods && $billGoods->status != PandianStatusEnum::SAVE) {
                throw new \Exception("[{$goods_id}]货号已盘点,请勿重复盘点");
                //已盘点且正常=>忽略
                //continue;
            }
            $goods = WarehouseGoods::find()->where(['goods_id'=>$goods_id])->one();
            if(empty($goods)) {
                throw new \Exception("[{$goods_id}]货号不存在");
            }
            if(!$billGoods) {
                $isNewRecord = true;
                $billGoods = new WarehouseBillGoods();
                $billGoods->bill_id = $bill->id;
                $billGoods->bill_type = $bill->bill_type;
                $billGoods->style_sn = $goods->style_sn;
                $billGoods->bill_no = $bill->bill_no;
                $billGoods->to_warehouse_id = $bill->to_warehouse_id;//盘点仓库
                $billGoods->status = PandianStatusEnum::PROFIT;//盘盈
                //商品属性
                $billGoods->goods_id = $goods->goods_id;
                $billGoods->goods_num = 0;//应盘数量
            }else {
                if($billGoods->goods_num >1) {
                    $billGoods->status = PandianStatusEnum::DOING;//盘点中
                }
                
                if($billGoods->goods_num < $num){
                    $billGoods->status = PandianStatusEnum::PROFIT;//盘盈
                }else if($billGoods->goods_num > $num) {
                    $billGoods->status = PandianStatusEnum::LOSS;//盘亏
                }else if($billGoods->goods_num == $num) {
                    $billGoods->status = PandianStatusEnum::NORMAL;//正常
                }
            }
            $billGoods->goods_name = $goods->goods_name;
            $billGoods->from_warehouse_id = $goods->warehouse_id;//归属仓库
            //更多商品属性
            //............
            if(false === $billGoods->save()) {
                throw new \Exception($this->getError($billGoods));
            }
            
            //盘点明细关系表数据更新
            if($isNewRecord) {
                $sql = "insert into ".WarehouseBillGoodsW::tableName().'(id,should_num) select id,goods_num from '.WarehouseBillGoods::tableName()." where id=".$billGoods->id;
                Yii::$app->db->createCommand($sql)->execute();
            }
            WarehouseBillGoodsW::updateAll(['actual_num'=>$num,'status'=>ConfirmEnum::YES],['id'=>$billGoods->id]);
            //$bill_detail_ids[] = $billGoods->id;
        }
        $this->billWSummary($bill->id);
        
    }
    /**
     * 盘点结束
     * @param WarehouseBillW $bill
     */
    public function finishBillW($bill_id)
    {
        $bill = WarehouseBill::find()->where(['id'=>$bill_id])->one();
        if(!$bill || $bill->bill_status == BillWStatusEnum::FINISHED) {
            throw new \Exception("盘点已结束");
        }
        $bill->status = BillWStatusEnum::FINISHED;
        $bill->bill_status = BillStatusEnum::PENDING; //待审核
        if(false === $bill->save(false,['id','status', 'bill_status'])) {
            throw new \Exception($this->getError($bill));
        }
        //1.未盘点设为盘亏
        WarehouseBillGoods::updateAll(['status'=>PandianStatusEnum::LOSS],['bill_id'=>$bill_id,'status'=>PandianStatusEnum::SAVE]);
        
        //2.解锁商品
        $subQuery = WarehouseBillGoods::find()->select(['goods_id'])->where(['bill_id'=>$bill->id]);
        WarehouseGoods::updateAll(['goods_status'=>GoodsStatusEnum::IN_STOCK],['goods_id'=>$subQuery,'goods_status'=>GoodsStatusEnum::IN_PANDIAN]);
        
        //3.解锁仓库
        \Yii::$app->warehouseService->warehouse->unlockWarehouse($bill->to_warehouse_id);
        
        //4.自动调整盘亏盘盈数据
        //$this->adjustGoods($bill_id);
        //5.盘点单汇总
        $this->billWSummary($bill_id);
    }
    
    /**
     * 盘点商品矫正
     * @param unknown $bill_id
     */
    public function adjustBillW($bill_id){
        
        $pandianStatusArray = [PandianStatusEnum::LOSS,PandianStatusEnum::PROFIT];
        $goodsStatusArray1  = [GoodsStatusEnum::HAS_SOLD];
        $goodsStatusArray2  = [GoodsStatusEnum::IN_TRANSFER,GoodsStatusEnum::IN_RETURN_FACTORY,GoodsStatusEnum::IN_SALE,GoodsStatusEnum::IN_REFUND];
        
        $bill_goods_list = WarehouseBillGoods::find()->select(['id','goods_id','status'])->where(['bill_id'=>$bill_id,'bill_type'=>BillTypeEnum::BILL_TYPE_W,'status'=>$pandianStatusArray])->limit(99999)->all();
        if(empty($bill_goods_list)) {
            return true;
        }
        foreach ($bill_goods_list as $billGoods){
            
            $goods = WarehouseGoods::find()->select(['id','goods_id','goods_status','warehouse_id'])->where(['goods_id'=>$billGoods->goods_id])->one();
            if(empty($goods)){
                continue;
            }
            $billGoods->from_warehouse_id = $goods->warehouse_id;
            if($billGoods->status == PandianStatusEnum::LOSS && in_array($goods->goods_status,$goodsStatusArray1)){
                //如果盘亏-货品状态【已销售】 调整状态：【已销售】
                $billGoods->goodsW->ajust_status = PandianAdjustEnum::HAS_SOLD;
            }else if($billGoods->status == PandianStatusEnum::PROFIT && in_array($goods->goods_status,$goodsStatusArray2)){
                //如果盘盈-货品状态【收货中、调拨中、报损中,返厂中、销售中、退货中】  调整状态：【在途】
                $billGoods->status = PandianStatusEnum::NORMAL;
                $billGoods->goodsW->ajust_status = PandianAdjustEnum::ON_WAY;
            } else {
                continue;
            }
            if(false === $billGoods->save(true,['id','status','from_warehouse_id'])) {
                throw new \Exception($this->getError($billGoods));
            }
            if($billGoods->goodsW->save(true,['id','adjust_status'])) {
                throw new \Exception($this->getError($billGoods->billW));
            }
            
        }
        $this->billWSummary($bill_id);
        
        //日志
        $log = [
                'bill_id' => $bill_id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '盘点单',
                'log_msg' => '盘点自动校正'
        ];
        \Yii::$app->warehouseService->billLog->createBillLog($log);
        return true;
        
    }
    /**
     * 盘点审核
     * @param WarehouseBillWForm $form
     */
    public function auditBillW($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $subQuery = WarehouseBillGoods::find()->select(['goods_id'])->where(['bill_id'=>$form->id]);
        if($form->audit_status == AuditStatusEnum::PASS) {
            $form->bill_status = BillStatusEnum::CONFIRM;
            WarehouseGoods::updateAll(['goods_status'=>GoodsStatusEnum::IN_STOCK],['goods_id'=>$subQuery,'goods_status'=>GoodsStatusEnum::IN_PANDIAN]);
            //解锁仓库
            \Yii::$app->warehouseService->warehouse->unlockWarehouse($form->to_warehouse_id);
        }else {
            $form->bill_status = BillStatusEnum::CANCEL;
            WarehouseGoods::updateAll(['goods_status'=>GoodsStatusEnum::IN_STOCK],['goods_id'=>$subQuery,'goods_status'=>GoodsStatusEnum::IN_PANDIAN]);
        }
        if(false === $form->save() ){
            throw new \Exception($this->getError($form));
        }
    }
    
    /**
     * 盘点单汇总
     * @param unknown $bill_id
     */
    public function billWSummary($bill_id)
    {
        $sum = WarehouseBillGoods::find()->alias("g")->innerJoin(WarehouseBillGoodsW::tableName().' gw','g.id=gw.id')
        ->select(['sum(if(gw.status='.ConfirmEnum::YES.',gw.actual_num,0)) as actual_num',
                'sum(if(g.status='.PandianStatusEnum::PROFIT.',abs(gw.actual_num-gw.should_num),0)) as profit_num',
                'sum(if(g.status='.PandianStatusEnum::LOSS.',abs(gw.actual_num-gw.should_num),0)) as loss_num',
                'sum(if(g.status='.PandianStatusEnum::SAVE.',gw.should_num,0)) as save_num',
                'sum(if(g.status='.PandianStatusEnum::NORMAL.',gw.actual_num,0)) as normal_num',
                'sum(if(gw.adjust_status>'.PandianAdjustEnum::SAVE.',abs(gw.actual_num-gw.should_num),0)) as adjust_num',
                'sum(1) as goods_num',//明细总数量
                'sum(IFNULL(g.cost_price,0)*gw.should_num) as total_cost',
                'sum(IFNULL(g.sale_price,0)*gw.should_num) as total_sale',
                'sum(IFNULL(g.market_price,0)*gw.should_num) as total_market'
        ])->where(['g.bill_id'=>$bill_id])->asArray()->one();
        
        if($sum) {
            $billUpdate  = ['goods_num'=>$sum['goods_num'], 'total_cost'=>$sum['total_cost'], 'total_sale'=>$sum['total_sale'], 'total_market'=>$sum['total_market']];
            $billWUpdate = ['save_num'=>$sum['save_num'],'profit_num'=>$sum['profit_num'],'actual_num'=>$sum['actual_num'], 'loss_num'=>$sum['loss_num'], 'normal_num'=>$sum['normal_num'], 'adjust_num'=>$sum['adjust_num']];
            
            $res1 = WarehouseBill::updateAll($billUpdate,['id'=>$bill_id]);
            $res2 = WarehouseBillW::updateAll($billWUpdate,['id'=>$bill_id]);
            return $res1 && $res2;
        }
        return false;
    }
}