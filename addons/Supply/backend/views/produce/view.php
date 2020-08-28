<?php

use common\helpers\Html;
use addons\Supply\common\enums\BuChanEnum;
use common\helpers\ArrayHelper;
use addons\Style\common\enums\AttrIdEnum;
use addons\Supply\common\enums\PeiliaoStatusEnum;
use addons\Supply\common\enums\PeishiStatusEnum;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '布产单详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">布产详情 - <?php echo $model->produce_sn?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
  <div class="tab-content" >
         <div class="box">
             <div class="col-xs-6">
                 <div class="box">
                     <div class="box-body table-responsive" style="padding-top: 0px;padding-bottom: 0px;">
                         <table class="table table-hover">
                             <tr>
                                 <td class="col-xs-2 text-right no-border-top"><?= $model->getAttributeLabel('produce_sn') ?>：</td>
                                 <td class="no-border-top"><?= $model->produce_sn ?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('supplier_id') ?>：</td>
                                 <td><?= $model->supplier->supplier_name ?? '' ?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('from_type') ?>：</td>
                                 <td><?= \addons\Supply\common\enums\FromTypeEnum::getValue($model->from_type) ?></td>
                             </tr>                             
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('purchase_sn') ?>：</td>
                                 <td><?= Html::a($model->purchase_sn,['../purchase/purchase/view','id'=>$model->purchaseGoods->purchase_id ?? ''],['target'=>'_blank','style'=>"text-decoration:underline;color:#3c8dbc"])?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('bc_status') ?>：</td>
                                 <td><?= \addons\Supply\common\enums\BuChanEnum::getValue($model->bc_status) ?></td>
                             </tr>                             
 							<tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('peiliao_type') ?>：</td>
                                 <td><?= \addons\Supply\common\enums\PeiliaoTypeEnum::getValue($model->peiliao_type) ?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('peiliao_status') ?>：</td>
                                 <td><?= PeiliaoStatusEnum::getValue($model->peiliao_status) ?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('peishi_type') ?>：</td>
                                 <td><?= \addons\Supply\common\enums\PeishiTypeEnum::getValue($model->peishi_type) ?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('peishi_status') ?>：</td>
                                 <td><?= PeishiStatusEnum::getValue($model->peishi_status) ?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('goods_num') ?>：</td>
                                 <td><?= $model->goods_num ?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('shippent_num') ?>：</td>
                                 <td><?= Yii::$app->supplyService->produce->getShippentNum($model->id) ?></td>
                             </tr>

                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('follower_name') ?>：</td>
                                 <td><?=  $model->follower_name ?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('created_at') ?>：</td>
                                 <td><?= \Yii::$app->formatter->asDatetime($model->created_at) ?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('factory_distribute_time') ?>：</td>
                                 <td><?= \Yii::$app->formatter->asDatetime($model->factory_distribute_time) ?></td>
                             </tr>  
                                                      
                         </table>
                     </div>
                 </div>
             </div>

             <div class="col-xs-6">
                 <div class="box">
                     <div class="box-body table-responsive" style="padding-top: 0px;padding-bottom: 0px;">
                         <table class="table table-hover">
                             <tr>
                                 <td class="col-xs-2 text-right no-border-top"><?= $model->getAttributeLabel('goods_name') ?>：</td>
                                 <td class="no-border-top"><?= $model->goods_name ?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_sn') ?>：</td>
                                 <td><?= $model->style_sn ?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('factory_mo') ?>：</td>
                                 <td><?= $model->factory_mo ?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_sex') ?>：</td>
                                 <td><?= \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex) ?></td>
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
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('qiban_type') ?>：</td>
                                 <td><?= \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type) ?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('qiban_sn') ?>：</td>
                                 <td><?= $model->qiban_sn ?></td>
                             </tr>
                             
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('product_type_id') ?>：</td>
                                 <td><?= $model->type->name ?? '' ?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_cate_id') ?>：</td>
                                 <td><?= $model->cate->name ??''?></td>
                             </tr>
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('factory_order_time') ?>：</td>
                                 <td><?= \Yii::$app->formatter->asDatetime($model->factory_order_time) ?></td>
                             </tr> 
                             <tr>
                                 <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('factory_delivery_time') ?>：</td>
                                 <td><?= \Yii::$app->formatter->asDatetime($model->factory_delivery_time) ?></td>
                             </tr>
                         </table>
                     </div>
                 </div>
             </div> 
             <div class="col-xs-12">   
           	   <div class="box-footer text-center" >
                    <?php
                    $buttonHtml = '';
                    switch ($model->bc_status){
            
                        //确认分配
                        case BuChanEnum::TO_CONFIRMED:
                            $buttonHtml .= Html::edit(['to-confirmed','id'=>$model->id ,'returnUrl'=>$returnUrl], '确认分配', [
                                'class'=>'btn btn-info btn-ms',
                                'style'=>"margin-left:5px",
                                'onclick' => 'rfTwiceAffirm(this,"确认分配","确定操作吗？");return false;',
            
                            ]);
                        //初始化
                        case BuChanEnum::INITIALIZATION:
                            $buttonHtml .= Html::edit(['to-factory','id'=>$model->id ,'returnUrl'=>$returnUrl], '分配工厂', [
                                'class'=>'btn btn-primary btn-ms',
                                'style'=>"margin-left:5px",
                                'data-toggle' => 'modal',
                                'data-target' => '#ajaxModal',
                            ]);
                            break;

                        //设置配料信息
                        case BuChanEnum::ASSIGNED:
                            if($model->from_type == \addons\Supply\common\enums\FromTypeEnum::ORDER){
                                $buttonHtml .= Html::edit(['set-peiliao','id'=>$model->id ,'returnUrl'=>$returnUrl], '设置配料信息', [
                                    'class'=>'btn btn-success btn-ms',
                                    'style'=>"margin-left:5px",
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModalLg',
                                ]);

                            }
                            break;
                        //待配料    
                        case BuChanEnum::TO_PEILIAO :
                            $buttonHtml .= Html::edit(['apply-peiliao','id'=>$model->id ,'returnUrl'=>$returnUrl], '申请配料', [
                                'class'=>'btn btn-success btn-ms',
                                'style'=>"margin-left:5px",
                                'onclick' => 'rfTwiceAffirm(this,"开始配料","确定操作吗？");return false;',
                            ]);
                            break;
                        //配料中
                        case BuChanEnum::IN_PEILIAO:
                            
                            break;
                        //待生产
                        case BuChanEnum::TO_PRODUCTION:
                            $buttonHtml .= Html::edit(['to-produce','id'=>$model->id ,'returnUrl'=>$returnUrl], '开始生产', [
                                'class'=>'btn btn-danger btn-ms',
                                'style'=>"margin-left:5px",
                                'onclick' => 'rfTwiceAffirm(this,"开始生产","确定操作吗？");return false;',
            
                            ]);
                            break;
                        //生产中
                        case BuChanEnum::IN_PRODUCTION :
                            ;
                        //部分出厂
                        case BuChanEnum::PARTIALLY_SHIPPED:
                            $buttonHtml .= Html::edit(['produce-shipment','id'=>$model->id ,'returnUrl'=>$returnUrl], '生产出厂', [
                                'class'=>'btn btn-success btn-ms',
                                'style'=>"margin-left:5px",
                                'data-toggle' => 'modal',
                                'data-target' => '#ajaxModalLg',
                            ]);
                            break;
                        default:
                            $buttonHtml .= '';
            
                    }
                    echo $buttonHtml;
            
                    if($model->bc_status >= BuChanEnum::ASSIGNED && $model->audit_follower_status != \common\enums\AuditStatusEnum::PENDING){
                        echo  Html::edit(['change-follower','id'=>$model->id ,'returnUrl'=>$returnUrl], '更改跟单人', [
                            'class'=>'btn btn-primary btn-ms',
                            'style'=>"margin-left:5px",
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModal',
                        ]);
                    }
            
                    if($model->audit_follower_status == \common\enums\AuditStatusEnum::PENDING) {
                        echo '&nbsp;';
                        echo Html::edit(['ajax-audit-follower', 'id' => $model->id], '跟单人审核', [
                            'class' => 'btn btn-success btn-ms',
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModal',
                        ]);
                    }
            
            
                    ?>
                </div>
         </div>
   </div> 

    <div class="col-xs-6" style="margin-top: 20px;">
        <div class="box">
            <div class="box-header" >
                <h3 class="box-title"><i class="fa fa-qrcode"></i> 属性信息</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <?php
                    foreach ($model->attrs as $k=>$attr){
                        $attrValues[$attr->attr_id] = $attr->attr_value;
                     ?>
                        <tr>
                            <td class="col-xs-2 text-right"><?= Yii::$app->attr->attrName($attr->attr_id)?>：</td>
                            <td><?= $attr->attr_value ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xs-6" style="margin-top: 20px;">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-info"></i> 图片信息</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                </table>
            </div>
        </div>
        <?php if($model->peiliao_status != PeiliaoStatusEnum::NONE) {?>
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
                    	<?php if(!empty($attrValues[AttrIdEnum::MATERIAL])) {?>
                    	<tr>
                    		<td><?= $attrValues[AttrIdEnum::MATERIAL]?></td>
                        	<td><?= $attrValues[AttrIdEnum::JINZHONG] ?? 0 ?>g</td>
                        	<td><?= PeiliaoStatusEnum::getValue($model->peiliao_status) ?></td>
                    	</tr>
                    	<?php }?>   
                    </tbody>
                </table>
            </div>
        </div>
        <?php }?>
        <?php if($model->peishi_status != PeishiStatusEnum::NONE) {?>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-info"></i> 石料信息<font style="font-size:14px;color:red">【商品数量:<?= $model->goods_num?>，主石石重=单颗石重，副石石重=副石总重】</font></h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <thead>
                    	<tr><th>石头位置</th><th>石头类型</th><th>数量</th><th>石重</th><th>证书类型</th><th>规格(形状/色彩/颜色/净度/切工/对称/荧光)</th><th>状态</th></tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($attrValues[AttrIdEnum::MAIN_STONE_TYPE])) {?>
                    	<tr>
                    		<td>主石</td>
                        	<td><?= $attrValues[AttrIdEnum::MAIN_STONE_TYPE]?></td>                        	
                        	<td><?= $attrValues[AttrIdEnum::MAIN_STONE_NUM]??'0'?></td>
                        	<td><?= $attrValues[AttrIdEnum::MAIN_STONE_WEIGHT]??'0'?>ct</td>
                        	<td><?= $attrValues[AttrIdEnum::DIA_CERT_TYPE]??'无'?></td>
                        	<td><?= ($attrValues[AttrIdEnum::DIA_SHAPE] ?? '无').'/'.($attrValues[AttrIdEnum::MAIN_STONE_SECAI] ?? '无').'/'.($attrValues[AttrIdEnum::DIA_COLOR] ?? '无').'/'.($attrValues[AttrIdEnum::DIA_CLARITY] ?? '无').'/'.($attrValues[AttrIdEnum::DIA_CUT] ?? '无').'/'.($attrValues[AttrIdEnum::DIA_SYMMETRY] ?? '无').'/'.($attrValues[AttrIdEnum::DIA_FLUORESCENCE] ?? '无')?></td>
                        	<td><?= PeishiStatusEnum::getValue($model->peishi_status) ?></td>
                    	</tr>
                    	<?php }?>                    	
                    	<?php if(!empty($attrValues[AttrIdEnum::SIDE_STONE1_TYPE])) {?>
                    	<tr>
                    		<td>副石1</td>
                        	<td><?= $attrValues[AttrIdEnum::SIDE_STONE1_TYPE]?></td>                        	
                        	<td><?= $attrValues[AttrIdEnum::SIDE_STONE1_NUM]??'0'?></td>
                        	<td><?= $attrValues[AttrIdEnum::SIDE_STONE1_WEIGHT]??'0'?>ct</td>
                        	<td>无</td>
                        	<td><?= ($attrValues[AttrIdEnum::SIDE_STONE1_SHAPE] ?? '无').'/'.($attrValues[AttrIdEnum::SIDE_STONE1_SECAI] ?? '无').'/'.($attrValues[AttrIdEnum::SIDE_STONE1_COLOR] ?? '无').'/'.($attrValues[AttrIdEnum::SIDE_STONE1_CLARITY] ?? '无').'/无/无/无'?></td>
                        	<td><?= PeishiStatusEnum::getValue($model->peishi_status) ?></td>
                    	</tr>
                    	<?php }?>                    	
                    	<?php if(!empty($attrValues[AttrIdEnum::SIDE_STONE2_TYPE])) {?>
                    	<tr>
                    		<td>副石2</td>
                        	<td><?= $attrValues[AttrIdEnum::SIDE_STONE2_TYPE]?></td>                        	
                        	<td><?= $attrValues[AttrIdEnum::SIDE_STONE2_NUM]??'0'?></td>
                        	<td><?= $attrValues[AttrIdEnum::SIDE_STONE2_WEIGHT]??'0'?>ct</td>
                        	<td>无</td>
                        	<td><?=($attrValues[AttrIdEnum::SIDE_STONE2_SHAPE] ?? '无').'/'.($attrValues[AttrIdEnum::SIDE_STONE2_SECAI] ?? '无').'/'.($attrValues[AttrIdEnum::SIDE_STONE2_COLOR] ?? '无').'/'.($attrValues[AttrIdEnum::SIDE_STONE2_CLARITY] ?? '无').'/无/无/无'?></td>
                        	<td><?= PeishiStatusEnum::getValue($model->peishi_status) ?></td>
                    	</tr>
                    	<?php }?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php }?>
    </div>
</div></div>