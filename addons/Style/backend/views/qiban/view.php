<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Style\common\enums\AttrTypeEnum;

$this->title =  '详情';
$this->params['breadcrumbs'][] = ['label' => '起版', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-bars"></i> 基本信息</h3>
            </div>
            <div class="box-body table-responsive">
                <div class="col-xs-6">
                    <table class="table table-hover">
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('qiban_sn') ?>：</td>
                            <td><?= $model->qiban_sn ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_sn') ?>：</td>
                            <td><?= $model->style_sn ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('qiban_name') ?>：</td>
                            <td><?= $model->qiban_name ?></td>
                        </tr>

                        <?php if($model->audit_status == \common\enums\AuditStatusEnum::UNPASS){ ?>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('audit_remark') ?>：</td>
                            <td><?= $model->audit_remark ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_channel_id') ?>：</td>
                            <td><?= $model->channel ? $model->channel->name : '' ?></td>
                        </tr>
                        <?php if($model->qiban_type == \addons\Style\common\enums\QibanTypeEnum::HAVE_STYLE){ ?>
                        <tr>
                            <td class="col-xs-2 text-right">款式渠道：</td>
                            <td><?= $model->style->channel->name ?? '' ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('style_sex') ?>：</td>
                            <td><?= \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('qiban_type') ?>：</td>
                            <td><?= \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('qiban_source_id') ?>：</td>
                            <td><?= \addons\Style\common\enums\QibanSourceEnum::getValue($model->qiban_source_id) ?></td>
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
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('is_inlay') ?>：</td>
                            <td><?= \addons\Style\common\enums\InlayEnum::getValue($model->is_inlay) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('audit_status') ?>：</td>
                            <td><?= \common\enums\AuditStatusEnum::getValue($model->audit_status) ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('cost_price') ?>：</td>
                            <td><?= $model->cost_price ?></td>
                        </tr>

                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                            <td><?= $model->remark ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('stone_info') ?>：</td>
                            <td><?= $model->stone_info ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('created_at') ?>：</td>
                            <td><?= Yii::$app->formatter->asDatetime($model->created_at); ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('creator_id') ?>：</td>
                            <td><?= $model->creator->username ?? ''; ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('audit_time') ?>：</td>
                            <td><?= Yii::$app->formatter->asDatetime($model->audit_time); ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('auditor_id') ?>：</td>
                            <td><?= $model->auditor->username ?? ''; ?></td>
                        </tr>
                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('audit_remark') ?>：</td>
                            <td><?= $model->audit_remark ?></td>
                        </tr>

                    </table>
                </div>
                <div class="col-xs-6">
                    <div class="margin-bottom">
                        <?php
                        $style_image_list = !empty($model->style_image)?explode(',', $model->style_image):[];
                        foreach ($style_image_list as $img){
                        ?>
                        <?= \common\helpers\ImageHelper::fancyBox($img) ?>
                        <?php } ?>
                    </div>

                </div>


            </div>
            <div class="box-footer text-center">
                <?php
//                if(!$model->purchaseGoods){
                    if($model->qiban_type == 1){
                        echo Html::edit(['edit','id' => $model->id,'search'=>1,'returnUrl' => Url::getReturnUrl()],'编辑',[
                            'class' => 'btn btn-primary btn-sm openIframe',
                            'data-width'=>'90%',
                            'data-height'=>'90%',
                            'data-offset'=>'20px',
                        ]);
                    }else{
                        echo Html::edit(['edit-no-style','id' => $model->id,'returnUrl' => Url::getReturnUrl()],'编辑',[
                            'class' => 'btn btn-primary btn-sm openIframe',
                            'data-width'=>'90%',
                            'data-height'=>'90%',
                            'data-offset'=>'20px',
                        ]);
                    }
//                }
                ?>
                <?php
                    echo Html::edit(['format-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()],'版式编辑',[
                        'class' => 'btn btn-primary btn-sm openIframe',
                        'data-width'=>'90%',
                        'data-height'=>'90%',
                        'data-offset'=>'20px',
                    ]);
                ?>
                <?php
                $isAudit = Yii::$app->services->flowType->isAudit(\common\enums\TargetTypeEnum::STYLE_QIBAN,$model->id);
                if($model->audit_status == \common\enums\AuditStatusEnum::PENDING && $isAudit){
                    echo Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                        'class'=>'btn btn-success btn-sm',
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]);
                }
                if($model->audit_status == \common\enums\AuditStatusEnum::SAVE){
                    echo Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                        'class'=>'btn btn-success btn-sm',
                        'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                    ]);
                }
                ?>

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
                    $attr_list = \addons\Style\common\models\QibanAttribute::find()->orderBy('sort asc')->where(['qiban_id'=>$model->id])->all();
                    foreach ($attr_list as $k=>$attr){
                        if($attr->input_type == 1){
                            $attr_value = $attr->attr_values;
                        }else{
                            $attr_value = Yii::$app->attr->valueName($attr->attr_values);
                        }
//                        if(empty($attr_value)) continue;
                        ?>
                        <tr>
                            <td class="col-xs-2 text-right"><?= Yii::$app->attr->attrName($attr->attr_id)?>：</td>
                            <td><?= $attr_value ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-qrcode"></i> 版式信息</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('format_sn') ?>：</td>
                        <td><?= $model->format_sn; ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('format_images') ?>：</td>
                        <td>
                            <?php
                            $format_image_list = !empty($model->format_images)?explode(',', $model->format_images):[];
                            foreach ($format_image_list as $img){
                                ?>
                                <?= \common\helpers\ImageHelper::fancyBox($img) ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('format_video') ?>：</td>
                        <td>
                            <?php
                            $format_video = !empty($model->format_video)?explode(',', $model->format_video):null;
                            if($format_video){
                                echo common\widgets\webuploader\Files::widget([
                                    'type'=>'videos',
                                    'theme'=>'show',
                                    'value'=> $format_video,
                                    'name'=>'format_video',
                                ]);
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('format_info') ?>：</td>
                        <td>
                            <?php
                            $format_info = json_decode($model->format_info)?? [];
                            if($format_info){
                                ?>
                                <table class="table">
                                    <tr>
                                        <th>特殊工艺</th>
                                        <th>工艺描述</th>
                                        <th>工艺图片</th>
                                    </tr>
                                    <?php

                                    foreach ($format_info as $item){
                                        ?>
                                        <tr>
                                            <td><?= \addons\Purchase\common\enums\SpecialCraftEnum::getValue($item->format_craft_type)?></td>
                                            <td><?= \addons\Purchase\common\enums\SpecialCraftEnum::getValue($item->format_craft_desc)?></td>
                                            <td>
                                                <?php
                                                $format_craft_images = $item->format_craft_images ?? [];
                                                foreach ($format_craft_images as $img){
                                                    ?>
                                                    <?= \common\helpers\ImageHelper::fancyBox($img) ?>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            <?php } ?>
                        </td>
                    </tr>


                    <tr>
                        <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('format_remark') ?>：</td>
                        <td><?= $model->audit_remark ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xs-12">
        <div id="flow">
        </div>
    </div>
</div>
<script>
    $("#flow").load("<?= \common\helpers\Url::to(['../common/flow/audit-view','flow_type_id'=> \common\enums\TargetTypeEnum::STYLE_QIBAN,'target_id'=>$model->id])?>")

</script>








