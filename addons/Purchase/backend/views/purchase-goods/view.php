<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Style\common\enums\AttrTypeEnum;
use common\helpers\AmountHelper;
use addons\Supply\common\enums\PeiliaoStatusEnum;
use addons\Style\common\enums\AttrIdEnum;
use addons\Supply\common\enums\PeishiStatusEnum;
use addons\Style\common\enums\StonePositionEnum;
use common\enums\ConfirmEnum;

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
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_sex') ?>：</td>
                                    <td><?= \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex) ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('qiban_type') ?>：</td>
                                    <td><?= \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type) ?></td>
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
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('peiliao_type') ?>：</td>
                                    <td><?= \addons\Supply\common\enums\PeiliaoTypeEnum::getValue($model->peiliao_type) ?></td>
                                </tr>
							    <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('is_inlay') ?>：</td>
                                    <td><?= \addons\Style\common\enums\InlayEnum::getValue($model->is_inlay) ?></td>
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
                                    <td class="col-xs-2 text-right">采购总额：</td>
                                    <td><?= AmountHelper::formatAmount($model->cost_price * $model->goods_num,2) ?></td>
                                </tr>                                
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                                    <td><?= $model->remark ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right">商品图片：</td>
                                    <td><?= \common\helpers\ImageHelper::fancyBox(Yii::$app->purchaseService->purchaseGoods->getStyleImage($model),90,90); ?></td>
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
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('main_stone_price') ?>：</td>
                                    <td><?= $model->main_stone_price ?></td>
                                </tr>

                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('gold_price') ?>：</td>
                                    <td><?= $model->gold_price ?></td>
                                </tr>

                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('gold_loss') ?>：</td>
                                    <td><?= $model->gold_loss ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('gold_cost_price') ?>：</td>
                                    <td><?= $model->gold_cost_price ?></td>
                                </tr>


                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('jiagong_fee') ?>：</td>
                                    <td><?= $model->jiagong_fee ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('xiangqian_fee') ?>：</td>
                                    <td><?= $model->xiangqian_fee ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('gong_fee') ?>：</td>
                                    <td><?= $model->gong_fee ?></td>
                                </tr>

                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('gaitu_fee') ?>：</td>
                                    <td><?= $model->gaitu_fee ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('penla_fee') ?>：</td>
                                    <td><?= $model->penla_fee ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('unit_cost_price') ?>：</td>
                                    <td><?= $model->unit_cost_price ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('factory_cost_price') ?>：</td>
                                    <td><?= $model->factory_cost_price ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('stone_info') ?>：</td>
                                    <td><?= $model->stone_info ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('parts_remark') ?>：</td>
                                    <td><?= $model->parts_remark ?></td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer text-center">
                <?php
                if($purchase->audit_status == \common\enums\AuditStatusEnum::SAVE) {
                    echo Html::edit(['edit','id' => $model->id],'编辑',['class' => 'btn btn-primary btn-ms openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                }
                ?>

                <?php
                if($model->produce_id && $model->produce && $model->produce->bc_status <= \addons\Supply\common\enums\BuChanEnum::IN_PRODUCTION) {
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
                <?= Html::edit(['purchase-goods-print/edit','purchase_goods_id' => $model->id],'制造单打印编辑',['class' => 'btn btn-primary btn-ms openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']); ?>
                <?= Html::a('打印',['../purchase/purchase-goods-print/print','id'=>$model->id],[
                    'target'=>'_blank',
                    'class'=>'btn btn-info btn-ms',
                ]); ?>
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
                            $attrValues[$attr->attr_id] = $attr->attr_value;
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
    <?php if(($produce = $model->produce ?? false) && !empty($attrValues)) {?>
    <div class="col-xs-6">
        <?php if($produce->produceGolds ?? false) {?>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-info"></i> 金料信息<font style="font-size:14px;color:red">【商品数量:<?= $model->goods_num?>】</font></h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <thead>
                    	<tr><th>金料材质</th><th>金重</th><th>状态</th></tr>
                    </thead>
                    <tbody>
                    	<?php foreach ($produce->produceGolds as $gold) {?>
                    	<tr>
                    		<td><?= $gold->gold_type?></td>
                        	<td><?= $gold->gold_weight/1 ?>g</td>
                        	<td><?= PeiliaoStatusEnum::getValue($gold->peiliao_status) ?></td>
                    	</tr>
                    	<?php }?>   
                    </tbody>
                </table>
            </div>
        </div>
        <?php }?>
        <?php if($produce->produceStones ?? false) {?>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-info"></i> 石料信息<font style="font-size:14px;color:red">【商品数量:<?= $model->goods_num?>，主石石重=单颗石重，副石石重=副石总重】</font></h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <thead>
                    	<tr><th>石头位置</th><th>石头类型</th><th>数量</th><th>石重</th><th>证书类型</th><th>规格(形状/色彩/颜色/净度/切工)</th><th>补石单</th><th>状态</th><th>操作</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produce->produceStones as $stone) {?>
                        <tr>
                    		<td><?= StonePositionEnum::getValue($stone->stone_position)?></td>
                        	<td><?= $stone->stone_type ? $stone->stone_type : '无'?></td>                        	
                        	<td><?= $stone->stone_num ? $stone->stone_num : '0'?></td>
                        	<td><?= ($stone->stone_position == StonePositionEnum::MAIN_STONE ? $stone->carat : $stone->stone_weight)/1;?>ct</td>
                        	<td><?= $stone->cert_type? $stone->cert_type : '无'?></td>
                        	<td><?= ($stone->shape? $stone->shape : '无').'/'.($stone->secai? $stone->secai : '无').'/'.($stone->color? $stone->color : '无').'/'.($stone->clarity? $stone->clarity : '无').'/'.($stone->cut? $stone->cut : '无')?></td>
                        	<td><?php 
                            	if($stone->is_increase == ConfirmEnum::YES) {
                            	    echo "<font color='green'>是</font>";
                            	}else{
                            	    echo "否";
                            	}
                        	    ?></td>
                        	<td><?= PeishiStatusEnum::getValue($stone->peishi_status) ?></td>
                        	<td>
                        	<?php if($stone->is_increase == ConfirmEnum::NO) {?>
                                	<?= Html::edit(['ajax-stone-increase','id'=>$stone->id ,'returnUrl'=>Url::getReturnUrl()], '补石', [
                                            'class'=>'btn btn-success btn-sm',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);?>
                             <?php }else {?>
                                 <?= Html::edit(['ajax-stone-edit','id'=>$stone->id ,'returnUrl'=>Url::getReturnUrl()], '编辑', [
                                        'class'=>'btn btn-primary btn-sm',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModal',
                                    ]);?>
                             <?php } ?>   
                                </td>
                    	</tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php }?>
    </div>
    <?php }?>
</div>






