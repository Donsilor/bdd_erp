<?php

use common\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '赠品详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title;?> - <?= $model->gift_sn?> - <?= \addons\Warehouse\common\enums\GiftStatusEnum::getValue($model->gift_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="col-xs-6">
                    <div class="box">
                        <div class="box-body table-responsive">
                            <table class="table table-hover">
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('gift_sn') ?>：</td>
                                    <td><?= $model->gift_sn ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('style_sn') ?>：</td>
                                    <td><?= $model->style_sn ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('gift_name') ?>：</td>
                                    <td><?= $model->gift_name ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('product_type_id') ?>：</td>
                                    <td><?= $model->type->name ?? '' ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_cate_id') ?>：</td>
                                    <td><?= $model->cate->name ?? '' ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('style_sex') ?>：</td>
                                    <td><?= \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('material_type') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->material_type)??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('material_color') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->material_color)??"" ?></td>
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
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('chain_length') ?>：</td>
                                    <td><?= $model->chain_length ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('main_stone_type') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->main_stone_type)??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('main_stone_num') ?>：</td>
                                    <td><?= $model->main_stone_num ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('gift_size') ?>：</td>
                                    <td><?= $model->gift_size ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('gift_num') ?>：</td>
                                    <td><?= $model->gift_num ?></td>
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
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('gift_weight') ?>：</td>
                                    <td><?= $model->gift_weight ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('gold_price') ?>：</td>
                                    <td><?= $model->gold_price ?></td>
                                </tr>
                                <?php
                                if(\common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE)){
                                ?>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('cost_price') ?>：</td>
                                    <td><?= $model->cost_price ?></td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('purchase_sn') ?>：</td>
                                    <td><?= $model->purchase_sn ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('receipt_no') ?>：</td>
                                    <td><?= $model->receipt_no ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('gold_price') ?>：</td>
                                    <td><?= $model->gold_price ?></td>
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
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('gift_status') ?>：</td>
                                    <td><?= \addons\Warehouse\common\enums\GiftStatusEnum::getValue($model->gift_status)?></td>
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


