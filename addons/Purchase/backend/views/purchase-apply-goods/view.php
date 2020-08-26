<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Style\common\enums\AttrTypeEnum;
use common\helpers\AmountHelper;
use addons\Purchase\common\enums\ApplyStatusEnum;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;

$this->title =  '详情';
$this->params['breadcrumbs'][] = ['label' => '采购商品', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-bars"></i> 商品信息</h3>
            </div>
            <div class="box-body table-responsive" style="padding-left: 0px;padding-right: 0px;">
                <div class="col-xs-6">
                    <div class="box">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_sn') ?>：</td>
                                    <td><?= $model->goods_sn ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('qiban_type') ?>：</td>
                                    <td><?= \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type) ?></td>
                                </tr>  
                                <?php if($model->qiban_type != \addons\Style\common\enums\QibanTypeEnum::NO_STYLE) {?>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_sn') ?>：</td>
                                    <td><?= $model->style_sn ?></td>
                                </tr>
                                <?php }?>
                                <?php if($model->qiban_type != \addons\Style\common\enums\QibanTypeEnum::NON_VERSION) {?>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('qiban_sn') ?>：</td>
                                    <td><?= $model->qiban_sn ?></td>
                                </tr>
                                <?php }?>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_sex') ?>：</td>
                                    <td><?= \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex) ?></td>
                                </tr>
                                
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_cate_id') ?>：</td>
                                    <td><?= $model->cate->name ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('product_type_id') ?>：</td>
                                    <td><?= $model->type->name ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('jintuo_type') ?>：</td>
                                    <td><?= \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type) ?></td>
                                </tr>
							    <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('is_inlay') ?>：</td>
                                    <td><?= \addons\Style\common\enums\InlayEnum::getValue($model->is_inlay) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_name') ?>：</td>
                                    <td><?= $model->goods_name ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_num') ?>：</td>
                                    <td><?= $model->goods_num ?></td>
                                </tr>
                                <?php
                                if(\common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE)){
                                ?>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('cost_price') ?>：</td>
                                    <td><?= $model->cost_price ?></td>
                                </tr> 
                                <tr>
                                    <td class="col-xs-2 text-right">采购总额：</td>
                                    <td><?= AmountHelper::formatAmount($model->cost_price * $model->goods_num,2) ?></td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_channel_id') ?>：</td>
                                    <td><?= $model->channel->name ?? '' ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right">商品图片：</td>
                                    <td>
                                        <?php
                                        if($model->goods_type == PurchaseGoodsTypeEnum::OTHER) {
                                            $model->goods_images = $model->goods_images ? explode(',', $model->goods_images) :[];
                                            foreach ($model->goods_images as $goods_image) {
                                                echo \common\helpers\ImageHelper::fancyBox($goods_image,90,90);
                                            }
                                        }else {
                                            echo \common\helpers\ImageHelper::fancyBox(Yii::$app->purchaseService->purchaseGoods->getStyleImage($model),90,90);
                                        }
                                        ?>
                                    </td>
                                </tr>

                            </table>
                        </div>

                    </div>
                </div>

                <div class="col-xs-6">
                    <div class="box">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('creator_id') ?>：</td>
                                    <td><?=  $model->creator->username ?? ''  ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('created_at') ?>：</td>
                                    <td><?= \Yii::$app->formatter->asDatetime($model->created_at) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('confirm_status') ?>：</td>
                                    <td><?= \addons\Purchase\common\enums\ApplyConfirmEnum::getValue($model->confirm_status)?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('supplier_id') ?>：</td>
                                    <td><?= $model->supplier->name ?? ''?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('confirm_design_id') ?>：</td>
                                    <td><?= $model->designMember->username ?? ''  ?></td>
                                </tr>

                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('confirm_design_time') ?>：</td>
                                    <td><?= \Yii::$app->formatter->asDatetime($model->confirm_design_time) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('confirm_goods_id') ?>：</td>
                                    <td><?= $model->goodsMember->username ?? ''  ?></td>
                                </tr>

                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('confirm_goods_time') ?>：</td>
                                    <td><?= \Yii::$app->formatter->asDatetime($model->confirm_goods_time) ?></td>
                                </tr>

                                <?php if($model->qiban_type == \addons\Style\common\enums\QibanTypeEnum::NO_STYLE){ ?>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('format_creator_id') ?>：</td>
                                    <td><?= $model->formatCreator->username ?? ''  ?></td>
                                </tr>

                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('format_created_at') ?>：</td>
                                    <td><?= \Yii::$app->formatter->asDatetime($model->format_created_at) ?></td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                                    <td><?= $model->remark ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('stone_info') ?>：</td>
                                    <td><?= $model->stone_info ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('parts_info') ?>：</td>
                                    <td><?= $model->parts_info ?></td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer text-center">

                <?php
                if($model->apply->apply_status <= ApplyStatusEnum::CONFIRM) {
                    $action = ($model->goods_type == PurchaseGoodsTypeEnum::OTHER) ? 'edit-no-style' :'edit';
                    ?>
                    <?= Html::edit([$action,'id' => $model->id],'编辑',['class' => 'btn btn-primary btn-ms openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);?>
                <?php }?>
                <?php
                if($model->apply->apply_status <= ApplyStatusEnum::CONFIRM  && $model->confirm_status == \addons\Purchase\common\enums\ApplyConfirmEnum::DESIGN) {
                    echo Html::edit(['format-edit','id' => $model->id],'版式编辑',['class' => 'btn btn-primary btn-ms openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                }
                ?>
                <?php
                if($model->apply->apply_status == ApplyStatusEnum::AUDITED && $model->is_apply == \common\enums\ConfirmEnum::NO) {
                    echo Html::edit(['apply-edit','id' => $model->id],'申请编辑',['class' => 'btn btn-primary btn-ms openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                }
                ?>
                <?php
                if($model->is_apply == common\enums\ConfirmEnum::YES) {
                    echo Html::edit(['apply-view','id' => $model->id,'returnUrl' => Url::getReturnUrl()],'查看审批',[
                        'class' => 'btn btn-danger btn-ms',
                    ]);
                }
                ?>
                <?php
                if($model->apply->apply_status == ApplyStatusEnum::CONFIRM ){
                    if($model->confirm_status == \addons\Purchase\common\enums\ApplyConfirmEnum::DESIGN){
                        echo Html::edit(['design-confirm','id'=>$model->id], '设计部确认', [
                            'class'=>'btn btn-success btn-ms',
                            'onclick' => 'rfTwiceAffirm(this,"提交确认", "确定确认吗？");return false;',
                        ]);
                    }elseif ($model->confirm_status == \addons\Purchase\common\enums\ApplyConfirmEnum::GOODS){
                        echo Html::edit(['goods-confirm','id'=>$model->id], '商品部确认', [
                            'class'=>'btn btn-success btn-ms',
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModal',
                        ]);
                    }
                }
                ?>
            </div>

        </div>
    </div>

    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-qrcode"></i> 属性信息</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <?php
                    if($model->attrs){
                        foreach ($model->attrs as $attr){
                            ?>
                            <tr>
                                <td class="col-xs-2 text-right"><?= Yii::$app->attr->attrName($attr->attr_id)?>：</td>
                                <td><?= $attr->attr_value ?></td>
                            </tr>
                        <?php 
                        } 
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-qrcode"></i> 版式信息</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('format_sn') ?>：</td>
                        <td><?= $model->format_sn; ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('format_images') ?>：</td>
                        <td>
                            <?php
                            $format_image_list = !empty($model->format_images)?explode(',', $model->format_images):[];
                            foreach ($format_image_list as $img){
                                ?>
                                <?= \common\helpers\ImageHelper::fancyBox($img) ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('format_video') ?>：</td>
                        <td>
                            <?php
                                $format_video = !empty($model->format_video)?explode(',', $model->format_video):null;
                                if($format_video){
                                    echo common\widgets\webuploader\Files::widget([
                                        'type'=>'videos',
                                        'theme'=>'show',
                                        'value'=> $format_video,
                                        'name'=>'format_video',
                                    ]);
                                }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('format_info') ?>：</td>
                        <td>
                            <?php
                                $format_info = json_decode($model->format_info)?? [];
                                if($format_info){
                            ?>
                            <table class="table">
                                <tr>
                                    <th>特殊工艺</th>
                                    <th>工艺描述</th>
                                    <th>工艺图片</th>
                                </tr>
                                <?php

                                foreach ($format_info as $item){
                                    ?>
                                    <tr>
                                        <td><?= \addons\Purchase\common\enums\SpecialCraftEnum::getValue($item->format_craft_type)?></td>
                                        <td><?= \addons\Purchase\common\enums\SpecialCraftEnum::getValue($item->format_craft_desc)?></td>
                                        <td>
                                            <?php
                                              $format_craft_images = $item->format_craft_images ?? [];
                                                foreach ($format_craft_images as $img){
                                                    ?>
                                                    <?= \common\helpers\ImageHelper::fancyBox($img) ?>
                                                <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                            <?php } ?>
                        </td>
                    </tr>


                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('format_remark') ?>：</td>
                        <td><?= $model->format_remark ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
