<?php

use addons\Style\common\enums\AttrIdEnum;
use addons\Supply\common\enums\PeiliaoStatusEnum;
use addons\Supply\common\enums\PeishiStatusEnum;
use common\helpers\Html;
use addons\Supply\common\enums\BuChanEnum;
use addons\Purchase\common\enums\ApplyStatusEnum;

$this->title = '查看质检详情';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];
?>
<div class="row">
<div class="col-xs-12">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-cog"></i> 采购收货单详情</h3>
        </div>
        <div class="box-body table-responsive">
             <table class="table table-hover">
                    <tr>
                        <td class="col-xs-1 text-right">采购收货单号：</td>
                        <td><?php echo $bill->receipt_no ?? '';?></td>
                        <td class="col-xs-1 text-right">单据状态：</td>
                        <td><?php echo \addons\Purchase\common\enums\ReceiptStatusEnum::getValue($bill->receipt_status??'');?></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-center">
                        <?php if($model->goods_status == \addons\Purchase\common\enums\ReceiptGoodsStatusEnum::IQC_ING) {?>
                            <?= Html::edit(['iqc','ids'=>$model->id], '质检', [
                                'class'=>'btn btn-success btn-sm openIframe',
                                'data-width'=>'60%',
                                'data-height'=>'60%',
                                'data-offset'=>'10px',
                             ]);?>
                         <?php }?>
                         <span class="btn btn-white" onclick="window.location.href='<?php echo $returnUrl;?>'">返回</span>
                        </td>                       
                    </tr>
                </table>
        </div>
    </div>
</div>
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-info"></i> 采购单货品详情</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('factory_mo') ?>：</td>
                        <td><?= $model->factory_mo ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('finger') ?>：</td>
                        <td><?= Yii::$app->attr->valueName($model->finger)??"" ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('xiangkou') ?>：</td>
                        <td><?= $model->xiangkou ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('material') ?>：</td>
                        <td><?= Yii::$app->attr->valueName($model->material)??"" ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('gold_weight') ?>：</td>
                        <td><?= $model->gold_weight ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('gold_loss') ?>：</td>
                        <td><?= $model->gold_loss ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('jintuo_type') ?>：</td>
                        <td><?= \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type)??"" ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('gross_weight') ?>：</td>
                        <td><?= $model->gross_weight ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('suttle_weight') ?>：</td>
                        <td><?= $model->suttle_weight ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('cert_id') ?>：</td>
                        <td><?= $model->cert_id ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('product_size') ?>：</td>
                        <td><?= $model->product_size ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('main_stone') ?>：</td>
                        <td><?= Yii::$app->attr->valueName($model->main_stone)??"" ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('main_cert_id') ?>：</td>
                        <td><?= $model->main_cert_id ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('main_stone_sn') ?>：</td>
                        <td><?= $model->main_stone_sn ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('main_stone_num') ?>：</td>
                        <td><?= $model->main_stone_num ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('main_stone_weight') ?>：</td>
                        <td><?= $model->main_stone_weight ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('main_stone_color') ?>：</td>
                        <td><?= Yii::$app->attr->valueName($model->main_stone_color)??"" ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('main_stone_clarity') ?>：</td>
                        <td><?= Yii::$app->attr->valueName($model->main_stone_clarity)??"" ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_cert_id1') ?>：</td>
                        <td><?= $model->second_cert_id1 ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_sn1') ?>：</td>
                        <td><?= $model->second_stone_sn1 ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone1') ?>：</td>
                        <td><?= $model->second_stone1 ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_num1') ?>：</td>
                        <td><?= $model->second_stone_num1 ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_weight1') ?>：</td>
                        <td><?= $model->second_stone_weight1 ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone2') ?>：</td>
                        <td><?= Yii::$app->attr->valueName($model->second_stone2)??"" ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_num2') ?>：</td>
                        <td><?= $model->second_stone_num2 ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_weight2') ?>：</td>
                        <td><?= $model->second_stone_weight2 ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone3') ?>：</td>
                        <td><?= Yii::$app->attr->valueName($model->second_stone3)??"" ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_num3') ?>：</td>
                        <td><?= $model->second_stone_num3 ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_weight3') ?>：</td>
                        <td><?= $model->second_stone_weight3 ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('parts_weight') ?>：</td>
                        <td><?= $model->parts_weight ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-info"></i> 布产单货品详情</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <?php
                    foreach ($produce->attrs as $k=>$attr){
                        $attrValues[$attr->attr_id] = $attr->attr_value;
                        ?>
                        <tr>
                            <td class="col-xs-2 text-right"><?= Yii::$app->attr->attrName($attr->attr_id)?>：</td>
                            <td><?= $attr->attr_value ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <?php if($produce->peiliao_status != PeiliaoStatusEnum::NONE) {?>
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-info"></i> 金料信息</h3>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr><th>金料材质</th><th>金重</th><th>状态</th></tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($attrValues[AttrIdEnum::MATERIAL])) {?>
                                <tr>
                                    <td><?= $attrValues[AttrIdEnum::MATERIAL]?></td>
                                    <td><?= $attrValues[AttrIdEnum::JINZHONG] ?? 0 ?>g</td>
                                    <td><?= PeiliaoStatusEnum::getValue($produce->peiliao_status) ?></td>
                                </tr>
                            <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php }?>
            <?php if($produce->peishi_status != PeishiStatusEnum::NONE) {?>
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-info"></i> 石料信息</h3>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr><th>石头位置</th><th>石头类型</th><th>数量</th><th>石重</th><th>证书类型</th><th>规格(颜色/净度/切工/对称/荧光)</th><th>状态</th></tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($attrValues[AttrIdEnum::MAIN_STONE_TYPE])) {?>
                                <tr>
                                    <td>主石</td>
                                    <td><?= $attrValues[AttrIdEnum::MAIN_STONE_TYPE]?></td>
                                    <td><?= $attrValues[AttrIdEnum::MAIN_STONE_NUM]??'0'?></td>
                                    <td><?= $attrValues[AttrIdEnum::MAIN_STONE_WEIGHT]??'0'?>ct</td>
                                    <td><?= $attrValues[AttrIdEnum::DIA_CERT_TYPE]??'无'?></td>
                                    <td><?= ($attrValues[AttrIdEnum::DIA_COLOR] ?? '无').'/'.($attrValues[AttrIdEnum::DIA_CLARITY] ?? '无').'/'.($attrValues[AttrIdEnum::DIA_CUT] ?? '无').'/'.($attrValues[AttrIdEnum::DIA_SYMMETRY] ?? '无').'/'.($attrValues[AttrIdEnum::DIA_FLUORESCENCE] ?? '无')?></td>
                                    <td><?= PeishiStatusEnum::getValue($produce->peishi_status) ?></td>
                                </tr>
                            <?php }?>
                            <?php if(!empty($attrValues[AttrIdEnum::SIDE_STONE1_TYPE])) {?>
                                <tr>
                                    <td>副石1</td>
                                    <td><?= $attrValues[AttrIdEnum::SIDE_STONE1_TYPE]?></td>
                                    <td><?= $attrValues[AttrIdEnum::SIDE_STONE1_NUM]??'0'?></td>
                                    <td><?= $attrValues[AttrIdEnum::SIDE_STONE1_WEIGHT]??'0'?>ct</td>
                                    <td>无</td>
                                    <td><?= ($attrValues[AttrIdEnum::SIDE_STONE1_COLOR] ?? '无').'/'.($attrValues[AttrIdEnum::SIDE_STONE1_CLARITY] ?? '无').'/无/无/无'?></td>
                                    <td><?= PeishiStatusEnum::getValue($produce->peishi_status) ?></td>
                                </tr>
                            <?php }?>
                            <?php if(!empty($attrValues[AttrIdEnum::SIDE_STONE2_TYPE])) {?>
                                <tr>
                                    <td>副石2</td>
                                    <td><?= $attrValues[AttrIdEnum::SIDE_STONE2_TYPE]?></td>
                                    <td><?= $attrValues[AttrIdEnum::SIDE_STONE2_NUM]??'0'?></td>
                                    <td><?= $attrValues[AttrIdEnum::SIDE_STONE2_WEIGHT]??'0'?>ct</td>
                                    <td>无</td>
                                    <td></td>
                                    <td><?= PeishiStatusEnum::getValue($produce->peishi_status) ?></td>
                                </tr>
                            <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php }?>
        </div>
        </div>
    </div>
