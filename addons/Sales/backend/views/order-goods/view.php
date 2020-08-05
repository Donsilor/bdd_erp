<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Sales\common\enums\OrderStatusEnum;
$this->title =  '详情';
$this->params['breadcrumbs'][] = ['label' => '订单商品', 'url' => ['index']];
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
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_name') ?>：</td>
                                    <td><?= $model->goods_name ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_sn') ?>：</td>
                                    <td><?= $model->style_sn ?></td>
                                </tr>
                                <?php if($model->qiban_sn) {?>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('qiban_sn') ?>：</td>
                                    <td><?= $model->qiban_sn ?></td>
                                </tr>
                                <?php }?>   

                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_num') ?>：</td>
                                    <td><?= $model->goods_num ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_price') ?>：</td>
                                    <td><?= $model->goods_price ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_pay_price') ?>：</td>
                                    <td><?= $model->goods_pay_price ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_discount') ?>：</td>
                                    <td><?= $model->goods_discount ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                                    <td><?= $model->remark ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right">商品图片：</td>
                                    <td><?= \common\helpers\ImageHelper::fancyBox($model->goods_image,90,90); ?></td>
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
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_sex') ?>：</td>
                                    <td><?= \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('qiban_type') ?>：</td>
                                    <td><?= \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_cate_id') ?>：</td>
                                    <td><?= $model->cate->name ?? '' ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('product_type_id') ?>：</td>
                                    <td><?= $model->type->name ?? '' ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_channel_id') ?>：</td>
                                    <td><?= $model->channel->name ?? '' ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('jintuo_type') ?>：</td>
                                    <td><?= \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type) ?></td>
                                </tr>

                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('is_inlay') ?>：</td>
                                    <td><?= \addons\Style\common\enums\InlayEnum::getValue($model->is_inlay) ?></td>
                                </tr>


                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer text-center">
                <?php
                if($model->order->order_status == \addons\Sales\common\enums\OrderStatusEnum::SAVE) {
                    echo Html::edit(['edit','id' => $model->id],'编辑',['class' => 'btn btn-primary btn-ms openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                }
                ?>

<!--                --><?php
//                if($model->order->order_status == OrderStatusEnum::CONFORMED) {
//                    echo Html::edit(['apply-edit','id' => $model->id],'申请编辑',['class' => 'btn btn-primary btn-ms openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
//                }
//                ?>
                <?php
                if($model->is_apply == common\enums\ConfirmEnum::YES) {
                    echo Html::edit(['apply-view','id' => $model->id,'returnUrl' => Url::getReturnUrl()],'查看审批',[
                        'class' => 'btn btn-danger btn-ms',
                    ]);
                }
                ?>
            </div>
        </div>
    </div>


    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-qrcode"></i> 属性信息</h3>
            </div>
            <div class="box-body table-responsive">
                <div class="col-xs-6">
                    <div class="box">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <?php
                                    $order_goods_attrs = $model->attrs ?? [];
                                    $order_goods_attrs_list = \common\helpers\ArrayHelper::map($order_goods_attrs,'attr_id','attr_value');
                                    $attrs = Yii::$app->salesService->orderGoods->Attrs();
                                    $count = count($attrs);
                                    $i = 0;
                                    foreach ($attrs as $attr){
                                        $i++;
                                        if($i == $count/2 + 1){
                                        ?>

                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="box">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <?php } ?>

                                        <tr>
                                            <td class="col-xs-2 text-right"><?= Yii::$app->attr->attrName($attr['attr_id'])?>：</td>
                                            <td><?= $order_goods_attrs_list[$attr['attr_id']] ?? '' ?></td>
                                        </tr>
                                    <?php } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





