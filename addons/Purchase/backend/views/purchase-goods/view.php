<?php
use common\helpers\Html;
use common\helpers\Url;
use common\helpers\AmountHelper;
use addons\Supply\common\enums\PeiliaoStatusEnum;
use addons\Supply\common\enums\PeishiStatusEnum;
use addons\Style\common\enums\StonePositionEnum;
use common\enums\ConfirmEnum;
use addons\Supply\common\enums\BuChanEnum;

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

                            </table>
                        </div>

                    </div>
                </div>

                <div class="col-xs-6">
                    <div class="box">
                        <div class="table-responsive">
                            <table class="table table-hover">
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


    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-qrcode"></i> 属性信息</h3>
            </div>
            <div class="box-body table-responsive">
                <div class="col-xs-6">
                    <table class="table table-hover">
                       <?php
                       if($model->attrs){
                           $num = ceil(count($model->attrs)/2)-1;
                            foreach ($model->attrs as $k => $attr){
                               $attrValues[$attr->attr_id] = $attr->attr_value;
                               ?>
                                <tr>
                                    <td class="col-xs-2 text-right"><?= Yii::$app->attr->attrName($attr->attr_id)?>：</td>
                                    <td><?= $attr->attr_value ?></td>
                                </tr>
                            <?php
                            if($k == $num){
                         ?>
                            </table>
                        </div>
                        <div class="col-xs-6">
                            <table class="table table-hover">
                        <?php
                            }
                            }
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-qrcode"></i> 其他信息</h3>
            </div>
            <div class="box-body table-responsive">
                <div class="col-xs-6">
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('main_stone_sn') ?>：</td>
                            <td><?= $model->main_stone_sn ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_sn1') ?>：</td>
                            <td><?= $model->second_stone_sn1 ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_stone_sn2') ?>：</td>
                            <td><?= $model->second_stone_sn2 ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('main_peishi_way') ?>：</td>
                            <td><?= \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->main_peishi_way) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_peishi_way1') ?>：</td>
                            <td><?= \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->second_peishi_way1) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('second_peishi_way2') ?>：</td>
                            <td><?= \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->second_peishi_way2) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('peiliao_way') ?>：</td>
                            <td><?= \addons\Warehouse\common\enums\PeiLiaoWayEnum::getValue($model->peiliao_way) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('peijian_way') ?>：</td>
                            <td><?= \addons\Warehouse\common\enums\PeiJianWayEnum::getValue($model->peijian_way) ?></td>
                        </tr>


                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('gold_price') ?>：</td>
                            <td><?= $model->gold_price ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('gold_cost_price') ?>：</td>
                            <td><?= $model->gold_cost_price ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('gold_amount') ?>：</td>
                            <td><?= $model->gold_amount ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('gross_weight') ?>：</td>
                            <td><?= $model->gross_weight ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('gold_loss') ?>：</td>
                            <td><?= $model->gold_loss ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('single_stone_weight') ?>：</td>
                            <td><?= $model->single_stone_weight ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('parts_material') ?>：</td>
                            <td><?= $model->parts_material ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('parts_num') ?>：</td>
                            <td><?= $model->parts_num ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('parts_weight') ?>：</td>
                            <td><?= $model->parts_weight ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('parts_price') ?>：</td>
                            <td><?= $model->parts_price ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('parts_amount') ?>：</td>
                            <td><?= $model->parts_amount ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('peishi_fee') ?>：</td>
                            <td><?= $model->peishi_fee ?></td>
                        </tr>

                    </table>
                </div>
                <div class="col-xs-6">
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('peishi_amount') ?>：</td>
                            <td><?= $model->peishi_amount ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('xianqian_price') ?>：</td>
                            <td><?= $model->xianqian_price ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('factory_cost_price') ?>：</td>
                            <td><?= $model->factory_cost_price ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('factory_mo') ?>：</td>
                            <td><?= $model->factory_mo ?></td>
                        </tr>

                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('parts_price') ?>：</td>
                            <td><?= $model->parts_price ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('factory_cost_price') ?>：</td>
                            <td><?= $model->factory_cost_price ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('factory_mo') ?>：</td>
                            <td><?= $model->factory_mo ?></td>
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
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('ke_gong_fee') ?>：</td>
                            <td><?= $model->gong_fee ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('biaomiangongyi_fee') ?>：</td>
                            <td><?= $model->biaomiangongyi_fee ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('fense_fee') ?>：</td>
                            <td><?= $model->fense_fee ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('bukou_fee') ?>：</td>
                            <td><?= $model->bukou_fee ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('penrasa_fee') ?>：</td>
                            <td><?= $model->penrasa_fee ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('edition_fee') ?>：</td>
                            <td><?= $model->edition_fee ?></td>
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
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('parts_fee') ?>：</td>
                            <td><?= $model->parts_fee ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('cert_fee') ?>：</td>
                            <td><?= $model->cert_fee ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('unit_cost_price') ?>：</td>
                            <td><?= $model->unit_cost_price ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('factory_total_price') ?>：</td>
                            <td><?= $model->factory_total_price ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('company_total_price') ?>：</td>
                            <td><?= $model->company_total_price ?></td>
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
    <?php if(($produce = $model->produce ?? false) && !empty($attrValues)) {?>
    <div class="col-xs-12">
        <?php if($produce->produceGolds ?? false) {?>
        <div class="box" id="box-gold" name="box-gold">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-info"></i> 配料信息<font style="font-size:14px;color:red">【商品数量:<?= $model->goods_num?>】</font></h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <thead>
                    	<tr><th>金料材质</th><th>金重</th><th>补料单</th><th>状态</th><th>操作</th></tr>
                    </thead>
                    <tbody>
                    	<?php foreach ($produce->produceGolds as $gold) {?>
                    	<tr>
                    		<td><?= $gold->gold_type?></td>
                        	<td><?= $gold->gold_weight/1 ?>g</td>
                        	<td><?php 
                            	if($gold->is_increase == ConfirmEnum::YES) {
                            	    echo "<font color='red'>是</font>";
                            	}else{
                            	    echo "否";
                            	}
                        	    ?>
                        	</td>
                        	<td><?= PeiliaoStatusEnum::getValue($gold->peiliao_status) ?></td>
                        	<td>
                        	<?php if($gold->is_increase == ConfirmEnum::NO && $produce->bc_status >= BuChanEnum::TO_PRODUCTION) {?>
                                	<?= Html::edit(['ajax-gold-increase','id'=>$gold->id ,'returnUrl'=>Url::getReturnUrl()], '补料', [
                                            'class'=>'btn btn-success btn-sm',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);?>
                             <?php }else if($gold->peiliao_status == PeiliaoStatusEnum::PENDING) {?>
                             
                                 <?= Html::edit(['ajax-gold-edit','id'=>$gold->id ,'returnUrl'=>Url::getReturnUrl()], '编辑', [
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
        <?php if($produce->produceStones ?? false) {?>
        <div class="box" id="box-stone" name="box-stone">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-info"></i> 配石信息<font style="font-size:14px;color:red">【商品数量:<?= $model->goods_num?>，主石石重=单颗石重，副石石重=副石总重】</font></h3>
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
                            	    echo "<font color='red'>是</font>";
                            	}else{
                            	    echo "否";
                            	}
                        	    ?></td>
                        	<td><?= PeishiStatusEnum::getValue($stone->peishi_status) ?></td>
                        	<td>
                        	<?php if($stone->is_increase == ConfirmEnum::NO && $produce->bc_status >= BuChanEnum::TO_PRODUCTION) {?>
                                	<?= Html::edit(['ajax-stone-increase','id'=>$stone->id ,'returnUrl'=>Url::getReturnUrl()], '补石', [
                                            'class'=>'btn btn-success btn-sm',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);?>
                             <?php }else if($stone->peishi_status == PeishiStatusEnum::PENDING) {?>
                             
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






