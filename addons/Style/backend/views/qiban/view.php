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
                <table class="table table-hover">
                    <tr>
                        <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('style_sn') ?>：</td>
                        <td><?= $model->style_sn ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('style_sex') ?>：</td>
                        <td><?= \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex) ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('qiban_type') ?>：</td>
                        <td><?= \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type) ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('style_cate_id') ?>：</td>
                        <td><?= $model->cate->name ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('product_type_id') ?>：</td>
                        <td><?= $model->type->name ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('jintuo_type') ?>：</td>
                        <td><?= \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type) ?></td>
                    </tr>

                    <tr>
                        <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('qiban_name') ?>：</td>
                        <td><?= $model->qiban_name ?></td>
                    </tr>
                    <tr>
                        <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('cost_price') ?>：</td>
                        <td><?= $model->cost_price ?></td>
                    </tr>



                    <tr>
                        <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('remark') ?>：</td>
                        <td><?= $model->remark ?></td>
                    </tr>

                </table>
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
                        if(empty($attr_value)) continue;
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









