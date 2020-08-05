<?php

use yii\widgets\ActiveForm;
$form = ActiveForm::begin([]);
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body table-responsive">
                <div class="col-lg-12">
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('xuhao') ?>：</td>
                            <td><?= $model->xuhao ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('purchase_sn') ?>：</td>
                            <td><?= $model->purchase_sn ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('produce_sn') ?>：</td>
                            <td><?= $model->produce_sn ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('put_in_type') ?>：</td>
                            <td><?= \addons\Warehouse\common\enums\PutInTypeEnum::getValue($model->put_in_type) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('goods_name') ?>：</td>
                            <td><?= $model->goods_name ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('style_sn') ?>：</td>
                            <td><?= $model->style_sn ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('factory_mo') ?>：</td>
                            <td><?= $model->factory_mo ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('goods_num') ?>：</td>
                            <td><?= $model->goods_num ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('goods_status') ?>：</td>
                            <td><?= \addons\Purchase\common\enums\ReceiptGoodsStatusEnum::getValue($model->goods_status)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('finger') ?>：</td>
                            <td><?= $model->finger ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('xiangkou') ?>：</td>
                            <td><?= $model->xiangkou ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('material') ?>：</td>
                            <td><?= Yii::$app->attr->valueName($model->material) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('gold_weight') ?>：</td>
                            <td><?= $model->gold_weight ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('gold_loss') ?>：</td>
                            <td><?= $model->gold_loss ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('gross_weight') ?>：</td>
                            <td><?= $model->gross_weight ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('suttle_weight') ?>：</td>
                            <td><?= $model->suttle_weight ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('jintuo_type') ?>：</td>
                            <td><?= \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type)?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('created_at') ?>：</td>
                            <td><?= \Yii::$app->formatter->asDatetime($model->created_at) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('goods_remark') ?>：</td>
                            <td><?= $model->goods_remark ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
<!-- tab-content end -->
</div>
