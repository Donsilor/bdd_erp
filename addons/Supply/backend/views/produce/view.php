<?php

use common\helpers\Html;
use addons\Supply\common\enums\BuChanEnum;

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
</div>

<div class="row">
     <div class="col-xs-12">
         <div class="box" style="margin: 0px;">
            <div class="box-header" style="padding-top: 0px;padding-bottom: 0px;">
                <h3 class="box-title"><i class="fa fa-info"></i> 基本信息</h3>
                <div style="float: right;">
                    <?php
                    $buttonHtml = '';
                    switch ($model->bc_status){

                        //确认分配
                        case BuChanEnum::TO_CONFIRMED:
                            $buttonHtml .= Html::edit(['to-confirmed','id'=>$model->id ,'returnUrl'=>$returnUrl], '确认分配', [
                                'class'=>'btn btn-info btn-sm',
                                'style'=>"margin-left:5px",
                                'onclick' => 'rfTwiceAffirm(this,"确认分配","确定操作吗？");return false;',

                            ]);
                        //初始化
                        case BuChanEnum::INITIALIZATION:
                            $buttonHtml .= Html::edit(['to-factory','id'=>$model->id ,'returnUrl'=>$returnUrl], '分配工厂', [
                                'class'=>'btn btn-primary btn-sm',
                                'style'=>"margin-left:5px",
                                'data-toggle' => 'modal',
                                'data-target' => '#ajaxModal',
                            ]);
                            break;
                        //已分配
                        case BuChanEnum::ASSIGNED:
                            $buttonHtml .= Html::edit(['to-produce','id'=>$model->id ,'returnUrl'=>$returnUrl], '开始生产', [
                                'class'=>'btn btn-danger btn-sm',
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
                                'class'=>'btn btn-success btn-sm',
                                'style'=>"margin-left:5px",
                                'data-toggle' => 'modal',
                                'data-target' => '#ajaxModalLg',
                            ]);
                            break;
//                     case BuChanEnum::FACTORY:
//                         $buttonHtml .= Html::edit(['produce-oqc','id'=>$model->id ,'returnUrl'=>$returnUrl], 'QC质检', [
//                             'class'=>'btn btn-success btn-sm',
//                             'style'=>"margin-left:5px",
//                             'data-toggle' => 'modal',
//                             'data-target' => '#ajaxModalLg',
//                         ]);
//                         break;
                        default:
                            $buttonHtml .= '';

                    }
                    echo $buttonHtml;
                    ?>
                </div>
            </div>
             <div class="box-body table-responsive">
                 <table class="table table-hover">
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('produce_sn') ?>：</td>
                         <td><?= $model->produce_sn ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('from_type') ?>：</td>
                         <td><?= \addons\Supply\common\enums\FromTypeEnum::getValue($model->from_type) ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('bc_status') ?>：</td>
                         <td><?= \addons\Supply\common\enums\BuChanEnum::getValue($model->bc_status) ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('from_order_sn') ?>：</td>
                         <td><?= $model->from_order_sn ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('from_detail_id') ?>：</td>
                         <td><?= $model->from_type == 2 ? $model->purchaseGoods->goods_name : '客订单' ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('goods_num') ?>：</td>
                         <td><?= $model->goods_num ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('supplier_id') ?>：</td>
                         <td><?= $model->supplier ?  $model->supplier->supplier_name : '' ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('follower_id') ?>：</td>
                         <td><?=  $model->follower ?  $model->follower->member_name : '' ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('customer') ?>：</td>
                         <td><?= $model->customer ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('style_sn') ?>：</td>
                         <td><?= $model->style_sn ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('style_sex') ?>：</td>
                         <td><?= \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex) ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('jintuo_type') ?>：</td>
                         <td><?= \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type) ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('qiban_sn') ?>：</td>
                         <td><?= $model->qiban_sn ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('qiban_type') ?>：</td>
                         <td><?= \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type) ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('product_type_id') ?>：</td>
                         <td><?= $model->type->name ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('style_cate_id') ?>：</td>
                         <td><?= $model->cate->name ?></td>
                     </tr>

                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('created_at') ?>：</td>
                         <td><?= \Yii::$app->formatter->asDatetime($model->created_at) ?></td>
                     </tr>

                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('factory_distribute_time') ?>：</td>
                         <td><?= \Yii::$app->formatter->asDatetime($model->factory_distribute_time) ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('factory_order_time') ?>：</td>
                         <td><?= \Yii::$app->formatter->asDatetime($model->factory_order_time) ?></td>
                     </tr>
                     <tr>
                         <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('factory_delivery_time') ?>：</td>
                         <td><?= \Yii::$app->formatter->asDatetime($model->factory_delivery_time) ?></td>
                     </tr>
                 </table>
             </div>
         </div>
     </div>
    <div class="col-xs-6" style="margin-top: 20px;">
        <div class="box">
            <div class="box-header" >
                <h3 class="box-title"><i class="fa fa-info"></i> 属性信息</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <?php
                    $attr_list = \addons\Supply\common\models\ProduceAttribute::find()->where(['produce_id'=>$model->id])->all();
                    foreach ($attr_list as $k=>$attr){
                        ?>
                        <tr>
                            <td class="col-xs-2 text-right"><?= Yii::$app->attr->attrName($attr['attr_id'])?>：</td>
                            <td><?= $attr['attr_value'] ?></td>
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
    </div>




</div>



    <!-- box end -->

<!-- tab-content end -->
