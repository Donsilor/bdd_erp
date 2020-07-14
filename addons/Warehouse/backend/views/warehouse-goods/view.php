<?php

use common\helpers\Html;
use addons\Supply\common\enums\BuChanEnum;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '商品详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">商品详情 - <?php echo $model->goods_id?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header" style="padding-top: 0px;">
                <h3 class="box-title"><i class="fa fa-bars"></i> 基本信息</h3>
            </div>
            <div class="box-body table-responsive" style="margin-top:0px; ">
                <div class="col-xs-6">
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_id') ?>：</td>
                            <td><?= $model->goods_id ?></td>
                        </tr>

                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_sn') ?>：</td>
                            <td><?= $model->style_sn ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_name') ?>：</td>
                            <td><?= $model->goods_name ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_status') ?>：</td>
                            <td><?= \addons\Warehouse\common\enums\GoodsStatusEnum::getValue($model->goods_status) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('warehouse_id') ?>：</td>
                            <td><?= $model->warehouse->name ?? '' ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_channel_id') ?>：</td>
                            <td><?= $model->channel->name ?? '' ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('product_type_id') ?>：</td>
                            <td><?= $model->productType->name ?? '' ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_cate_id') ?>：</td>
                            <td><?= $model->styleCate->name ?? '' ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('is_inlay') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->is_inlay) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('jintuo_type') ?>：</td>
                            <td><?= \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type) ?></td>
                        </tr>

                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_num') ?>：</td>
                            <td><?= $model->goods_num ?></td>
                        </tr>

                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('cost_price') ?>：</td>
                            <td><?= $model->cost_price ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('market_price') ?>：</td>
                            <td><?= $model->market_price ?></td>
                        </tr>


                    </table>
                </div>

                <div class="col-xs-6">
                    <table class="table table-hover">

                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('put_in_type') ?>：</td>
                            <td><?= \addons\Warehouse\common\enums\PutInTypeEnum::getValue($model->put_in_type) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('supplier_id') ?>：</td>
                            <td><?= $model->supplier->supplier_name ?? '' ?></td>
                        </tr>

                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('order_sn') ?>：</td>
                            <td><?= $model->order_sn ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('produce_sn') ?>：</td>
                            <td><?= $model->produce_sn ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('created_at') ?>：</td>
                            <td><?= Yii::$app->formatter->asDate($model->created_at); ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right">库龄：</td>
                            <td><?= Yii::$app->formatter->asDuration(bcsub (time(),$model->created_at)); ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('creator_id') ?>：</td>
                            <td><?= $model->creator->username ?? '' ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('created_at') ?>：</td>
                            <td><?= Yii::$app->formatter->asDatetime($model->created_at) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('updated_at') ?>：</td>
                            <td><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('weixiu_status') ?>：</td>
                            <td><?= \addons\Warehouse\common\enums\WeixiuStatusEnum::getValue($model->weixiu_status) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('weixiu_warehouse_id') ?>：</td>
                            <td><?= $model->weixiuWarehouse->name ?? '' ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_image') ?>：</td>
                            <td><?= \common\helpers\ImageHelper::fancyBox($model->goods_image,60,60) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="box-footer text-center">
                <?php
//                    if(Yii::$app->warehouseService->warehouseGoods->editStatus($model)) {
//                        echo Html::edit(['edit', 'id' => $model->id], '编辑', [
//                            'class' => 'btn btn-primary btn-sm openIframe',
//                            'data-width' => '90%',
//                            'data-height' => '90%',
//                            'data-offset' => '20px'
//                        ]);
//                    }
//
//                if(Yii::$app->warehouseService->warehouseGoods->applyStatus($model)) {
//                    echo '&nbsp;';
//                    echo Html::edit(['ajax-apply', 'id' => $model->id], '提审', [
//                        'class' => 'btn btn-success btn-sm',
//                        'onclick' => 'rfTwiceAffirm(this,"提交审核","确定提交吗？");return false;',
//                    ]);
//                }
//                if($model->audit_status == \common\enums\AuditStatusEnum::PENDING) {
//                    echo '&nbsp;';
//                    echo Html::edit(['apply-view', 'id' => $model->id, 'returnUrl' => \common\helpers\Url::getReturnUrl()], '查看审批', [
//                        'class' => 'btn btn-danger btn-sm',
//                    ]);
//                }
                ?>
            </div>
        </div>
    </div>

    <div class="col-xs-12">
        <div class="box">
            <div class="box-header" >
                <h3 class="box-title"><i class="fa fa-bars"></i> 属性信息</h3>
            </div>
            <div class="box-body table-responsive" style="margin-top:0px; ">
                <div class="col-xs-6">
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('finger') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->finger) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('product_size') ?>：</td>
                            <td><?= $model->product_size ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('xiangkou') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->xiangkou) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('material_type') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->material_type) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_color') ?>：</td>
                            <td><?= $model->goods_color ?></td>
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
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('suttle_weight') ?>：</td>
                            <td><?= $model->suttle_weight ?></td>
                        </tr>

                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('parts_gold_weight') ?>：</td>
                            <td><?= $model->parts_gold_weight ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('parts_num') ?>：</td>
                            <td><?= $model->parts_num ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('parts_fee') ?>：</td>
                            <td><?= $model->parts_fee ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('gong_fee') ?>：</td>
                            <td><?= $model->gong_fee ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('bukou_fee') ?>：</td>
                            <td><?= $model->bukou_fee ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('xianqian_fee') ?>：</td>
                            <td><?= $model->xianqian_fee ?></td>
                        </tr>

                    </table>
                </div>
                <div class="col-xs-6">
                    <table class="table table-hover">


                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('cert_fee') ?>：</td>
                            <td><?= $model->cert_fee ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('fense_fee') ?>：</td>
                            <td><?= $model->fense_fee ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('biaomiangongyi_fee') ?>：</td>
                            <td><?= $model->biaomiangongyi_fee ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('total_gong_fee') ?>：</td>
                            <td><?= $model->total_gong_fee ?></td>
                        </tr>

                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('cert_type') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->cert_type) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('cert_id') ?>：</td>
                            <td><?= $model->cert_id ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('chain_long') ?>：</td>
                            <td><?= $model->chain_long ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('chain_type') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->chain_type) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('cramp_ring') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->cramp_ring) ?></td>
                        </tr>

                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('talon_head_type') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->talon_head_type) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('xiangqian_craft') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->xiangqian_craft) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('biaomiangongyi') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->biaomiangongyi) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('kezi') ?>：</td>
                            <td><?= $model->kezi ?></td>
                        </tr>






                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12">
        <div class="box">
            <div class="box-header" >
                <h3 class="box-title"><i class="fa fa-bars"></i> 石头信息</h3>
            </div>
            <div class="box-body table-responsive" style="margin-top:0px; ">
                <div class="col-xs-6">
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('main_stone_type') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->main_stone_type) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('main_stone_num') ?>：</td>
                            <td><?= $model->main_stone_num ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('main_stone_colour') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->main_stone_colour) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('diamond_carat') ?>：</td>
                            <td><?= $model->diamond_carat ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('diamond_shape') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->diamond_shape) ?></td>
                        </tr>

                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('diamond_color') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->diamond_color) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('diamond_clarity') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->diamond_clarity) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('diamond_cut') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->diamond_cut) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('diamond_polish') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->diamond_polish) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('diamond_symmetry') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->diamond_symmetry) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('diamond_fluorescence') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->diamond_fluorescence) ?></td>
                        </tr>

                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('main_stone_price') ?>：</td>
                            <td><?= $model->main_stone_price ?></td>
                        </tr>

                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('diamond_cert_id') ?>：</td>
                            <td><?= $model->diamond_cert_id ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('diamond_cert_type') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->diamond_cert_type) ?></td>
                        </tr>

                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('main_stone_size') ?>：</td>
                            <td><?= $model->main_stone_size ?></td>
                        </tr>



                    </table>
                </div>
                <div class="col-xs-6">
                    <table class="table table-hover">


                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_type1') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->second_stone_type1) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_shape1') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->second_stone_shape1) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_color1') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->second_stone_color1) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_clarity1') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->second_stone_clarity1) ?></td>
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
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_price1') ?>：</td>
                            <td><?= $model->second_stone_price1 ?></td>
                        </tr>



                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_type2') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->second_stone_type2) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_shape2') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->second_stone_shape2) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_color2') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->second_stone_color2) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_clarity2') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->second_stone_clarity2) ?></td>
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
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_price2') ?>：</td>
                            <td><?= $model->second_stone_price2 ?></td>
                        </tr>

                    </table>
                </div>
            </div>
        </div>
    </div>


</div>


