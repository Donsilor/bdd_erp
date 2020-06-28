<?php

use common\helpers\Html;
use addons\Supply\common\enums\BuChanEnum;

$this->title = '商品调整审批';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];
?>
<div class="row">
<div class="col-xs-12">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-cog"></i> 商品调整-审批</h3>
        </div>
        <div class="box-body table-responsive">
             <table class="table table-hover">
                    <tr>
                        <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('goods_id') ?>：</td>
                        <td><?= $model->goods_id ?? '' ?></td>
                        <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('style_sn') ?>：</td>
                        <td><?= $model->style_sn ?? '' ?></td>
                        <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('goods.goods_status') ?>：</td>
                        <td><?= \addons\Warehouse\common\enums\GoodsStatusEnum::getValue($model->goods->goods_status) ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('goods.jintuo_type') ?>：</td>
                        <td><?= \addons\Style\common\enums\JintuoTypeEnum::getValue($model->goods->jintuo_type) ?></td>
                        <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('goods.product_type_id') ?>：</td>
                        <td><?= $model->goods->productType->name ?? '' ?></td>
                        <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('goods.style_cate_id') ?>：</td>
                        <td><?= $model->goods->styleCate->name ?? '' ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-center">
                        <?php if($model->audit_status == \common\enums\AuditStatusEnum::PENDING) {?>
                            <?= Html::edit(['apply-audit','id'=>$model->id], '审  批', [
                                 'class'=>'btn btn-success btn-sm',
                                 'data-toggle' => 'modal',
                                 'data-target' => '#ajaxModal',
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
                <h3 class="box-title"><i class="fa fa-info"></i> 修改前</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <?php if($model->apply_info) { ?>
                        <?php foreach ($model->apply_info as $info) {?>
                            <tr>
                                <td class="col-xs-2 nowrap text-right"><?php echo $info['label']?>：</td>
                                <td><?php echo $info['org_value']?></td>
                            </tr>
                        <?php }?>
                    <?php }?>


                </table>
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-info"></i> 修改后</h3>
            </div>
            <div class="box-body table-responsive">
                 <table class="table table-hover">
                    <?php if($model->apply_info) {?>
                       <?php foreach ($model->apply_info as $info) {?>
                        <tr>
                            <td class="col-xs-2 nowrap text-right<?php echo $info['changed'] ?' red':'';?>" style="white-space:nowrap;"><?php echo $info['label']?>：</td>
                            <td<?php echo $info['changed'] ?' class="red"':'';?>><?php echo $info['value']?></td>
                        </tr>
                       <?php }?>
                  <?php }?>
                </table>
            </div>
        </div>
    </div>    
</div>