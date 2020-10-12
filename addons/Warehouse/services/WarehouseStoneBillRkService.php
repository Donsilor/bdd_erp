<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\StoneStatusEnum;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBill;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use common\enums\AuditStatusEnum;
use common\enums\LogTypeEnum;
use common\helpers\ExcelHelper;
use common\helpers\UploadHelper;
use Yii;
use common\components\Service;


/**
 * 领料单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseStoneBillRkService extends Service
{

    /**
     * 买石单-审核
     * @param WarehouseStoneBillMsForm $form
     * @throws
     */
    public function auditBillMs($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            $form->bill_status = BillStatusEnum::CONFIRM;

            $billGoodsList = WarehouseStoneBillGoods::find()->where(['bill_id' => $form->id])->all();
            if(empty($billGoodsList)){
                throw new \Exception("单据明细不能为空");
            }
            //石包入库
            foreach ($billGoodsList as $billGoods) {
                //$billGoods = new WarehouseStoneBillGoods();
                $stoneM = new WarehouseStone();
                $stone_sn = $billGoods->stone_sn;
                $stoneData = [
                    'stone_sn' =>(string) rand(10000000000,99999999999),//临时
                    'stone_name' => $billGoods->stone_name,
                    'stone_status' => StoneStatusEnum::IN_STOCK,
                    'style_sn' => $billGoods->style_sn,
                    'stone_type' => $billGoods->stone_type,
                    'supplier_id' => $form->supplier_id,
                    'put_in_type' => $form->put_in_type,
                    'warehouse_id' => $form->to_warehouse_id,
                    'channel_id' => $billGoods->channel_id,
                    'stone_shape' => $billGoods->shape,
                    'stone_color' => $billGoods->color,
                    'stone_clarity' => $billGoods->clarity,
                    'stone_cut' => $billGoods->cut,
                    'stone_symmetry' => $billGoods->symmetry,
                    'stone_polish' => $billGoods->polish,
                    'stone_fluorescence' => $billGoods->fluorescence,
                    'cert_id' => $billGoods->cert_id,
                    'cert_type' => (string) $billGoods->cert_type,
                    'stone_norms' => $billGoods->stone_norms,
                    'stone_size' => $billGoods->stone_size,
                    'stone_colour' => $billGoods->stone_colour,
                    'stock_cnt' => $billGoods->stone_num,
                    'first_stock_cnt' => $billGoods->stone_num,
                    'ms_cnt' => $billGoods->stone_num,
                    'stock_weight' => $billGoods->stone_weight,
                    'first_stock_weight' => $billGoods->stone_weight,
                    'ms_weight' => $billGoods->stone_weight,
                    'stone_price' => $billGoods->stone_price,
                    'cost_price' => $billGoods->cost_price,
                    'first_cost_price' => $billGoods->cost_price,
                    'sale_price' => $billGoods->sale_price,
                    'vg_weight' => $billGoods->carat,
                    'remark' => $billGoods->remark,
                    'creator_id'=>\Yii::$app->user->identity->getId(),
                ];
                $stoneM->attributes = $stoneData;
                if(false === $stoneM->save()){
                    throw new \Exception($this->getError($stoneM));
                }
                if(empty($stone_sn)){
                    \Yii::$app->warehouseService->stone->createStoneSn($stoneM);
                }else{
                    $stoneM->stone_sn = $stone_sn;
                }
                //同步更新石料编号到单据明细
                $billGoods->stone_sn = $stoneM->stone_sn;
                if(false === $billGoods->save(true,['id','stone_sn'])) {
                    throw new \Exception($this->getError($billGoods));
                }
            }

        }else{
            $form->bill_status = BillStatusEnum::SAVE;
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }



    /**
     *  导入
     * @param OrderImportKForm $form
     */
    public function importStoneRk($form)
    {
        if (!($form->file->tempName ?? true)) {
            throw new \Exception("请上传文件");
        }
        if (UploadHelper::getExt($form->file->name) != 'xlsx') {
            throw new \Exception("请上传xlsx格式文件");
        }

        $startRow = 2;
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
            if(($form->titles['remark'] ?? '') != '备注') {
                throw new \Exception("数据模板有变动，请下载最新模板");
            }
            //加载表格行数据 并且数据校验
            if(empty(array_filter($row)) || false === $form->loadRow($row,$rowIndex)){
                continue;
            }
        }

        if($form->hasError() === false) {
            foreach ($form->goods_list as $k=>$stoneBillGoods) {
                try{
                    if(false === $stoneBillGoods->save()) {
                        throw new \Exception($this->getError($stoneBillGoods));
                    }

                    $log = [
                        'bill_id' => $stoneBillGoods->bill_id,
                        'bill_status' => BillStatusEnum::SAVE,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_module' => '创建其他入库单',
                        'log_msg' => '其他入库单'.$stoneBillGoods->bill_no.'批量导入明细'
                    ];
                    \Yii::$app->warehouseService->stoneBillLog->createStoneBillLog($log);

                }catch (\Exception $e) {
                    $form->addRowError($rowIndex, 'error', "创建失败：".$e->getMessage());
                }
            }
        }
        $form->showImportMessage();
    }
}