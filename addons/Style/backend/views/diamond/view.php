<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Style\common\enums\AttrTypeEnum;
use common\helpers\AmountHelper;

$this->title =  '详情';
$this->params['breadcrumbs'][] = ['label' => '裸钻商品', 'url' => ['index']];
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
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_sn') ?>：</td>
                                    <td><?= $model->goods_sn ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_num') ?>：</td>
                                    <td><?= $model->goods_num ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('status') ?>：</td>
                                    <td><?= \common\enums\FrameEnum::getValue($model->status) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('is_stock') ?>：</td>
                                    <td><?= \addons\Sales\common\enums\IsStockEnum::getValue($model->is_stock) ?></td>
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
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('cost_price') ?>：</td>
                                    <td><?= $model->cost_price ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('market_price') ?>：</td>
                                    <td><?= $model->market_price ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('sale_price') ?>：</td>
                                    <td><?= $model->sale_price ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                                    <td><?= $model->remark ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_image') ?>：</td>
                                    <td><?= \common\helpers\ImageHelper::fancyBox($model->goods_image,90,90); ?></td>
                                </tr>

                                <tr>
                                    <?php $parame_images = !empty($model->parame_images)?explode(',', $model->parame_images):[];?>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('parame_images') ?>：</td>
                                    <td>
                                        <?php foreach ($parame_images as $img){
                                            echo \common\helpers\ImageHelper::fancyBox($img,90,90);
                                            echo  '&nbsp;';
                                        }?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_gia_image') ?>：</td>
                                    <td><?= \common\helpers\ImageHelper::fancyBox($model->goods_gia_image,90,90); ?></td>
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
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('carat') ?>：</td>
                                    <td><?= $model->carat ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('shape') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->shape) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('color') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->color) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('clarity') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->clarity) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('cut') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->cut) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('polish') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->polish) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('symmetry') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->symmetry) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('fluorescence') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->fluorescence) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('stone_floor') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->stone_floor) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('depth_lv') ?>：</td>
                                    <td><?= $model->depth_lv ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('table_lv') ?>：</td>
                                    <td><?= $model->table_lv ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('aspect_ratio') ?>：</td>
                                    <td><?= $model->aspect_ratio ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('length') ?>：</td>
                                    <td><?= $model->length ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('width') ?>：</td>
                                    <td><?= $model->width ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer text-center">
                <?php
                if($model->audit_status == \common\enums\AuditStatusEnum::SAVE) {
                    echo Html::edit(['edit','id' => $model->id],'编辑',['class' => 'btn btn-primary btn-ms openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                }
                ?>

            </div>
        </div>
    </div>



</div>





