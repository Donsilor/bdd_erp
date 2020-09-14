<?php

use common\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '石料详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title;?> - <?= $model->stone_sn?> - <?= \addons\Warehouse\common\enums\StoneStatusEnum::getValue($model->stone_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="col-xs-6">
                    <div class="box">
                        <div class="box-body table-responsive">
                            <table class="table table-hover">
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_sn') ?>：</td>
                                    <td><?= $model->stone_sn ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_name') ?>：</td>
                                    <td><?= $model->stone_name ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('style_sn') ?>：</td>
                                    <td><?= $model->style_sn ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_type') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->stone_type)??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stock_cnt') ?>：</td>
                                    <td><?= $model->stock_cnt ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stock_weight') ?>：</td>
                                    <td><?= $model->stock_weight ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_type') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->stone_type)??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_shape') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->stone_shape)??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_color') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->stone_color)??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_clarity') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->stone_clarity)??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_cut') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->stone_cut)??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_symmetry') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->stone_symmetry)??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_polish') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->stone_polish)??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_fluorescence') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->stone_fluorescence)??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_colour') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->stone_colour)??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('cert_id') ?>：</td>
                                    <td><?= $model->cert_id ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('cert_type') ?>：</td>
                                    <td><?= Yii::$app->attr->valueName($model->cert_type)??"" ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_size') ?>：</td>
                                    <td><?= $model->stone_size ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('stone_price') ?>：</td>
                                    <td><?= $model->stone_price ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('cost_price') ?>：</td>
                                    <td><?= $model->cost_price ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('fenbaoru_cnt') ?>：</td>
                                    <td><?= $model->fenbaoru_cnt ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('fenbaoru_weight') ?>：</td>
                                    <td><?= $model->fenbaoru_weight ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('fenbaochu_cnt') ?>：</td>
                                    <td><?= $model->fenbaochu_cnt ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('fenbaochu_weight') ?>：</td>
                                    <td><?= $model->fenbaochu_weight ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('ms_cnt') ?>：</td>
                                    <td><?= $model->ms_cnt ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('ms_weight') ?>：</td>
                                    <td><?= $model->ms_weight ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('ss_cnt') ?>：</td>
                                    <td><?= $model->ss_cnt ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('ss_weight') ?>：</td>
                                    <td><?= $model->ss_weight ?></td>
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
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('ss_cnt') ?>：</td>
                                    <td><?= $model->ss_cnt ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('ss_weight') ?>：</td>
                                    <td><?= $model->ss_weight ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('hs_cnt') ?>：</td>
                                    <td><?= $model->hs_cnt ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('hs_weight') ?>：</td>
                                    <td><?= $model->hs_weight ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('ts_cnt') ?>：</td>
                                    <td><?= $model->ts_cnt ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('ts_weight') ?>：</td>
                                    <td><?= $model->ts_weight ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('ys_cnt') ?>：</td>
                                    <td><?= $model->ys_cnt ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('ys_weight') ?>：</td>
                                    <td><?= $model->ys_weight ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('sy_cnt') ?>：</td>
                                    <td><?= $model->sy_cnt ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('sy_weight') ?>：</td>
                                    <td><?= $model->sy_weight ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('th_cnt') ?>：</td>
                                    <td><?= $model->th_cnt ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('th_weight') ?>：</td>
                                    <td><?= $model->th_weight ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('sy_cnt') ?>：</td>
                                    <td><?= $model->sy_cnt ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('rk_cnt') ?>：</td>
                                    <td><?= $model->rk_cnt ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('rk_weight') ?>：</td>
                                    <td><?= $model->rk_weight ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('ck_cnt') ?>：</td>
                                    <td><?= $model->ck_cnt ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('ck_weight') ?>：</td>
                                    <td><?= $model->ck_weight ?></td>
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
                                    <td class="col-xs-3 text-right">入库单号：</td>
                                    <td><?= $bill['bill_no']??""?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right">采购收货单号：</td>
                                    <td><?= $bill['receipt_no']??""?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-3 text-right">采购单号：</td>
                                    <td><?= $bill['purchase_sn']??""?></td>
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


