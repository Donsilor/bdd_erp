<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;
use common\widgets\langbox\LangBox;
use yii\base\Widget;
use common\widgets\skutable\SkuTable;
use common\helpers\Url;
use common\enums\AuditStatusEnum;

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
    <div class="tab-content">
         <div class="box col-xs-12">
            <div class="box-header">
                <h3 class="box-title">基本信息</h3>
                <div class="box-tools" >
                </div>
            </div>
            <div class="box-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('produce_sn') ?>：</label>
                            <?= $model->produce_sn ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('from_type') ?>：</label>
                            <?= \addons\Supply\common\enums\FromTypeEnum::getValue($model->from_type) ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('bc_status') ?>：</label>
                            <?= \addons\Supply\common\enums\BuChanEnum::getValue($model->bc_status)?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('from_order_sn') ?>：</label>
                            <?= $model->from_order_sn ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('from_detail_id') ?>：</label>
                            <?= $model->purchaseGoods->goods_name ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('follower_id') ?>：</label>
                            <?= $model->follower ?  $model->follower->username : ''?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('style_sn') ?>：</label>
                            <?= $model->style_sn ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('jintuo_type') ?>：</label>
                            <?= \common\enums\JinTuoEnum::getValue($model->jintuo_type) ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('style_sex') ?>：</label>
                            <?= \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex)  ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                             <label class="text-right col-lg-4"><?= $model->getAttributeLabel('qiban_sn') ?>：</label>
                             <?= $model->qiban_sn ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('product_type_id') ?>：</label>
                            <?= $model->type->name ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('style_cate_id') ?>：</label>
                            <?= $model->cate->name ?>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('created_at') ?>：</label>
                            <?= \Yii::$app->formatter->asDatetime($model->created_at) ?>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-right col-lg-4"><?= $model->getAttributeLabel('updated_at') ?>：</label>
                            <?= \Yii::$app->formatter->asDatetime($model->updated_at) ?>
                        </div>
                    </div>
                    <?php
                    $attr_list = \addons\Supply\common\models\ProduceAttribute::find()->where(['produce_id'=>$model->id])->all();
                    $collLg = 4;
                    foreach ($attr_list as $k=>$attr){
                        ?>
                        <?php if ($k % 3 ==0){ ?><div class="row"><?php }?>
                        <div class="col-lg-<?=$collLg?>">
                            <label class="text-right col-lg-<?=$collLg?>">
                                <?= Yii::$app->styleService->attribute->getAttrNameByAttrId($attr['attr_id'])?>：</label>
                            <?= $attr['attr_value'] ?>
                        </div>
                        <?php if(($k+1) % 3 == 0 || ($k+1) == count($attr_list)){?></div><?php }?>
                    <?php } ?>
            </div>
             <div class="box-header">
                 <h3 class="box-title">属性信息</h3>
                 <div class="box-tools" >
                 </div>
             </div>
            <div class="box-body">
                <?php
                $attr_list = \addons\Supply\common\models\ProduceAttribute::find()->where(['produce_id'=>$model->id])->all();
                $collLg = 4;
                foreach ($attr_list as $k=>$attr){
                ?>
                <?php if ($k % 3 ==0){ ?><div class="row"><?php }?>
                    <div class="col-lg-<?=$collLg?>">
                        <label class="text-right col-lg-<?=$collLg?>">
                            <?= Yii::$app->styleService->attribute->getAttrNameByAttrId($attr['attr_id'])?>：</label>
                            <?= $attr['attr_value'] ?>
                    </div>
                    <?php if(($k+1) % 3 == 0 || ($k+1) == count($attr_list)){?></div><?php }?>
                 <?php } ?>
            </div>



            <div class="box-footer text-center">

                 <?php 
                 if($model->bc_status == \addons\Supply\common\enums\BuChanEnum::INITIALIZATION){
                     echo Html::edit(['to-factory','id'=>$model->id], '分配工厂', [
                             'class'=>'btn btn-success btn-sm',
                             'data-toggle' => 'modal',
                             'data-target' => '#ajaxModal',
                     ]);
                 }
                 ?>             
                <span class="btn btn-white" onclick="window.location.href='<?php echo $returnUrl;?>'">返回</span>
          </div>
    </div>
    <!-- box end -->
</div>
<!-- tab-content end -->
</div>