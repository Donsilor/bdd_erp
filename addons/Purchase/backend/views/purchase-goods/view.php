<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Style\common\enums\AttrTypeEnum;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">

                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'style_sn')->textInput(['disabled'=>true]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'qiban_sn')->textInput(['disabled'=>true]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'goods_type')->dropDownList(\addons\Purchase\common\enums\PurchaseGoodsTypeEnum::getMap(),['disabled'=>true]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'style_sex')->dropDownList(\addons\Style\common\enums\StyleSexEnum::getMap(),['disabled'=>true]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'qiban_type')->dropDownList(\addons\Style\common\enums\QibanTypeEnum::getMap(),['disabled'=>true]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->styleCate->getDropDown(),['disabled'=>true]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType->getDropDown(),['disabled'=>true]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'jintuo_type')->dropDownList(\addons\Style\common\enums\JintuoTypeEnum::getMap(),['prompt'=>'请选择','onchange'=>"searchGoods()",'disabled'=>true]) ?>
                        </div>

                        <div class="col-lg-4">
                            <?= $form->field($model, 'goods_name')->textInput(['disabled'=>true]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'goods_num')->textInput(['disabled'=>true]) ?>
                        </div>

                        <div class="col-lg-4">
                            <?= $form->field($model, 'cost_price')->textInput(['disabled'=>true]) ?>
                        </div>



                <?php
                //print_r($model->getAttrList());exit;
                $attr_list = $model->getAttrList();
                foreach ($attr_list as $k=>$attr){
                    $attr_id  = $attr['attr_id'];//属性ID
                    $attr_values = $attr['attr_values'];//属性值
                    $is_require = $attr['is_require'];
                    $attr_name = \Yii::$app->attr->attrName($attr_id);//属性名称

                    $_field = $is_require == 1 ? 'attr_require':'attr_custom';
                    $field = "{$_field}[{$attr_id}]";
                    switch ($attr['input_type']){
                        case common\enums\InputTypeEnum::INPUT_TEXT :{
                            $input = $form->field($model,$field)->textInput(['disabled'=>true])->label($attr_name);
                            break;
                        }
                        case common\enums\InputTypeEnum::INPUT_MUlTI_RANGE: {
                            $input = $form->field($model,$field)->textInput(['disabled'=>true])->label($attr_name);
                            break;
                        }
                        default:{
                            if($attr_values == '') {
                                $attr_values = Yii::$app->styleService->attribute->getValuesByAttrId($attr_id);
                            }else {
                                $attr_values = Yii::$app->styleService->attribute->getValuesByValueIds($attr_values);
                            }
                            $input = $form->field($model,$field)->dropDownList($attr_values,['prompt'=>'请选择','disabled'=>true])->label($attr_name);
                            break;
                        }
                    }//end switch
                    $collLg = 4;
                    ?>

                    <div class="col-lg-<?=$collLg?>"><?= $input ?></div>

                    <?php
                }//end foreach $attr_list
                ?>
                <!-- ./box-body -->
                <?php if($model->style_sn) {?>

                        <div class="col-lg-8">
                            <?= $form->field($model, 'remark')->textarea(['disabled'=>true]) ?>
                        </div>
                    </div>
                <?php }?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

