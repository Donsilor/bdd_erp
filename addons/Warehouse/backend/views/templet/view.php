<?php

use common\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '样板详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title;?> - <?= $model->batch_sn?> - <?= \addons\Warehouse\common\enums\TempletStatusEnum::getValue($model->goods_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="col-xs-6">
                    <div class="box">
                        <div class="box-body table-responsive">
                            <table class="table table-hover">
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('batch_sn') ?>：</td>
                                    <td><?= $model->batch_sn ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('layout_type') ?>：</td>
                                    <td><?= \addons\Warehouse\common\enums\LayoutTypeEnum::getValue($model->layout_type)?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('style_sn') ?>：</td>
                                    <td><?= $model->style_sn ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('qiban_sn') ?>：</td>
                                    <td><?= $model->qiban_sn ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('goods_name') ?>：</td>
                                    <td><?= $model->goods_name ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('finger') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->finger)??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('finger_hk') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->finger_hk)??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('goods_size') ?>：</td>
                                    <td><?= $model->goods_size ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('goods_num') ?>：</td>
                                    <td><?= $model->goods_num ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_weight') ?>：</td>
                                    <td><?= $model->stone_weight ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_size') ?>：</td>
                                    <td><?= $model->stone_size ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('suttle_weight') ?>：</td>
                                    <td><?= $model->suttle_weight ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('cost_price') ?>：</td>
                                    <td><?= $model->cost_price ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6" style="padding: 0px;">
                    <div class="box" style="margin-bottom: 0px;">
                        <div class="box-body table-responsive" >
                            <table class="table table-hover">
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('purchase_sn') ?>：</td>
                                    <td><?= $model->purchase_sn ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('receipt_no') ?>：</td>
                                    <td><?= $model->receipt_no ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('cost_price') ?>：</td>
                                    <td><?= $model->cost_price ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('supplier_id') ?>：</td>
                                    <td><?= $model->supplier->supplier_name??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('put_in_type') ?>：</td>
                                    <td><?= \addons\Warehouse\common\enums\PutInTypeEnum::getValue($model->put_in_type) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('warehouse_id') ?>：</td>
                                    <td><?= $model->warehouse->name??""?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('channel_id') ?>：</td>
                                    <td><?= $model->channel->name??""?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('goods_status') ?>：</td>
                                    <td><?= \addons\Warehouse\common\enums\TempletStatusEnum::getValue($model->goods_status)?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('status') ?>：</td>
                                    <td><?= \common\enums\StatusEnum::getValue($model->status)?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('creator_id') ?>：</td>
                                    <td><?= $model->creator ? $model->creator->username:''  ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('created_at') ?>：</td>
                                    <td><?= \Yii::$app->formatter->asDatetime($model->created_at) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                                    <td><?= $model->remark ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12" style="padding: 0px;">
                    <div class="box">
                        <div class="box-body table-responsive" >
                            <table class="table table-hover">
                                <tr>
                                    <?php if($model->goods_image){?><td class="col-xs-4 text-center"><?= \common\helpers\ImageHelper::fancyBox($model->goods_image,90,90) ?></td><?php } ?>
                                </tr>
                                <tr>
                                    <?php if($model->goods_image){?><td class="col-xs-4 text-center"><?= $model->getAttributeLabel('goods_image') ?>：</td><?php } ?>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer text-center">
            <?php
                echo Html::button('导出', [
                    'class'=>'btn btn-success btn-sm',
                    'onclick' => 'batchExport()',
                ]);
                echo '&nbsp;';
            ?>
            <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-default btn-sm']) ?>
        </div>
    </div>
</div>
<script>
    function batchExport() {
        window.location.href = "<?= \common\helpers\Url::buildUrl('export',[],['ids'])?>?ids=<?php echo $model->id ?>";
    }
</script>


