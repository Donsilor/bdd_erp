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
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('audit_status') ?>：</td>
                            <td><?= \common\enums\AuditStatusEnum::getValue($model->audit_status) ?></td>
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
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('is_inlay') ?>：</td>
                            <td><?= \addons\Style\common\enums\InlayEnum::getValue($model->is_inlay) ?></td>
                        </tr>

                        <tr>
                            <td class="col-xs-2 text-right"><?= $model->getAttributeLabel('qiban_name') ?>：</td>
                            <td><?= $model->qiban_name ?></td>
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

                    </table>
                </div>
                <div class="col-xs-6">
                    <div class="margin-bottom">
                        <?= \common\helpers\ImageHelper::fancyBox($model->style_image,400,400) ?>
                    </div>

                </div>


            </div>
            <div class="box-footer text-center">
                <?php
                if(!$model->purchaseGoods){
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
                }
                ?>

                <?php
                if($model->audit_status == \common\enums\AuditStatusEnum::PENDING){
                    echo Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                        'class'=>'btn btn-success btn-sm',
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]);
                }
                if($model->audit_status == \common\enums\AuditStatusEnum::SAVE){
                    echo Html::edit(['ajax-apply','id'=>$model->id], '提交审核', [
                        'class'=>'btn btn-success btn-sm',
                        'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                    ]);
                }
                ?>

            </div>
        </div>
    </div>


    <div class="col-xs-12">
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
                            <td class="col-xs-1 text-right"><?= Yii::$app->attr->attrName($attr->attr_id)?>：</td>
                            <td><?= $attr_value ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>









