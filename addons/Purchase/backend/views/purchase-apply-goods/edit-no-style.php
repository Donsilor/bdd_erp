<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;
use addons\Style\common\enums\InlayEnum;
use addons\Style\common\enums\JintuoTypeEnum;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                
                <?php if($model->style_cate_id && $model->product_type_id) {?>
                    <div class="row">
                        <div class="box-header with-border" style="margin: 0px 0 0px 0;">
                            <h3 class="box-title" style="font-weight: bold"> 基本信息</h3>
                        </div>
                        <?php if($model->isNewRecord) {?>
                            <div class="col-lg-3">
                                <?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->styleCate->getGrpDropDown(),['prompt'=>'请选择','onchange'=>"searchGoods()"]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType->getGrpDropDown(),['prompt'=>'请选择','onchange'=>"searchGoods()"]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= $form->field($model, 'jintuo_type')->dropDownList(\addons\Style\common\enums\JintuoTypeEnum::getMap(),['prompt'=>'请选择','onchange'=>"searchGoods()"]) ?>
                            </div>
                        <?php }else{ ?>
                            <div class="col-lg-3">
                                <?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->styleCate->getGrpDropDown(),['prompt'=>'请选择','onchange'=>"searchGoods()",'disabled'=>true]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType->getGrpDropDown(),['prompt'=>'请选择','onchange'=>"searchGoods()",'disabled'=>true]) ?>
                            </div>
                            <div class="col-lg-3">
                                <?= $form->field($model, 'jintuo_type')->dropDownList(\addons\Style\common\enums\JintuoTypeEnum::getMap(),['prompt'=>'请选择','onchange'=>"searchGoods()",'disabled'=>true]) ?>
                            </div>
                        <?php } ?>

                        <div class="col-lg-3">
                            <?= $form->field($model, 'goods_sn')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"])->label("商品编号") ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'goods_type')->dropDownList(PurchaseGoodsTypeEnum::getMap(),['disabled'=>true]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'is_inlay')->dropDownList(\addons\Style\common\enums\InlayEnum::getMap(),['prompt'=>'请选择','disabled'=>true]) ?>
                            <?= $form->field($model, 'is_inlay')->hiddenInput()->label(false) ?>
                        </div>

                        <div class="col-lg-3">
                            <?= $form->field($model, 'goods_name')->textInput() ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'style_sex')->dropDownList(\addons\Style\common\enums\StyleSexEnum::getMap(),['prompt'=>'请选择']) ?>
                        </div>

                        <div class="col-lg-3">
                            <?= $form->field($model, 'cost_price')->textInput() ?>
                        </div>


                        <?php
                        $attr_list = \Yii::$app->styleService->attribute->module(\addons\Style\common\enums\AttrModuleEnum::PURCHASE)->getAttrListByCateId($model->style_cate_id,\addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type,'getAttrTypeMap'),$model->is_inlay);
                        foreach ($attr_list as $k=>$attr){
                            $attr_id  = $attr['id'];//属性ID
                            if(!in_array($attr_id,$model->getAttrType('base'))){
                                continue;
                            }

                            $is_require = $attr['is_require'];
                            $attr_name = \Yii::$app->attr->attrName($attr_id);//属性名称

                            $_field = in_array($attr_id,$model->getAttrType('require')) ? 'attr_require':'attr_custom';
                            $field = "{$_field}[{$attr_id}]";
                            switch ($attr['input_type']){
                                case common\enums\InputTypeEnum::INPUT_TEXT :{
                                    $input = $form->field($model,$field)->textInput()->label($attr_name);
                                    break;
                                }
                                default:{
                                    $attr_values = Yii::$app->styleService->attribute->getValuesByAttrId($attr_id);
                                    if(in_array($attr_id,\addons\Style\common\enums\AttrIdEnum::getMulteAttr())){
                                        $input = $form->field($model, $field)->widget(kartik\select2\Select2::class, [
                                            'data' => $attr_values,
                                            'options' => ['placeholder' => '请选择','multiple'=>true],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                                'multiple'=>true
                                            ],
                                        ])->label($attr_name);
                                    }else{
                                        $input = $form->field($model,$field)->dropDownList($attr_values,['prompt'=>'请选择'])->label($attr_name);
                                    }
                                    break;
                                }
                            }//end switch

                            $collLg = 3;
                            ?>

                            <div class="col-lg-<?=$collLg?>"><?= $input ?></div>
                            <?php
                             }
                            ?>

                        </div>
                        <div class="row">
                            <div class="box-header with-border" style="margin: 0px 0 0px 0;">
                                <h3 class="box-title" style="font-weight: bold"> 主石信息</h3>
                            </div>
                            <?php
                            foreach ($attr_list as $k=>$attr){
                                $attr_id  = $attr['id'];//属性ID
                                if(!in_array($attr_id,$model->getAttrType('stone'))){
                                    continue;
                                }

                                $is_require = $attr['is_require'];
                                $attr_name = \Yii::$app->attr->attrName($attr_id);//属性名称

                                //$_field = $is_require == 1 ? 'attr_require':'attr_custom';
                                $_field = in_array($attr_id,$model->getAttrType('require')) ? 'attr_require':'attr_custom';
                                $field = "{$_field}[{$attr_id}]";
                                switch ($attr['input_type']){
                                    case common\enums\InputTypeEnum::INPUT_TEXT :{
                                        $input = $form->field($model,$field)->textInput()->label($attr_name);
                                        break;
                                    }
                                    default:{
                                        /*
                                        $attr_values = Yii::$app->styleService->styleAttribute->getDropdowns($model->style_id,$attr_id);
                                        if(empty($attr_values)) {
                                            $attr_values = Yii::$app->styleService->attribute->getValuesByAttrId($attr_id);
                                        }
                                        */
                                        $attr_values = Yii::$app->styleService->attribute->getValuesByAttrId($attr_id);
                                        if(in_array($attr_id,\addons\Style\common\enums\AttrIdEnum::getMulteAttr())){
                                            $input = $form->field($model, $field)->widget(kartik\select2\Select2::class, [
                                                'data' => $attr_values,
                                                'options' => ['placeholder' => '请选择','multiple'=>true],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                    'multiple'=>true
                                                ],
                                            ])->label($attr_name);
                                        }else{
                                            $input = $form->field($model,$field)->dropDownList($attr_values,['prompt'=>'请选择'])->label($attr_name);
                                        }
                                        break;
                                    }
                                }//end switch
                                $collLg = 3;
                                ?>
                                <div class="col-lg-<?=$collLg?>"><?= $input ?></div>
                                <?php
                            }
                            ?>

                        </div>
                        <div class="row">
                            <div class="box-header with-border" style="margin: 0px 0 0px 0;">
                                <h3 class="box-title" style="font-weight: bold"> 副石信息</h3>
                            </div>
                            <?php
                            foreach ($attr_list as $k=>$attr){
                                $attr_id  = $attr['id'];//属性ID
                                if(!in_array($attr_id,$model->getAttrType('second_stone'))){
                                    continue;
                                }

                                $is_require = $attr['is_require'];
                                $attr_name = \Yii::$app->attr->attrName($attr_id);//属性名称

                                //$_field = $is_require == 1 ? 'attr_require':'attr_custom';
                                $_field = in_array($attr_id,$model->getAttrType('require')) ? 'attr_require':'attr_custom';
                                $field = "{$_field}[{$attr_id}]";
                                switch ($attr['input_type']){
                                    case common\enums\InputTypeEnum::INPUT_TEXT :{
                                        $input = $form->field($model,$field)->textInput()->label($attr_name);
                                        break;
                                    }
                                    default:{
                                        /*
                                        $attr_values = Yii::$app->styleService->styleAttribute->getDropdowns($model->style_id,$attr_id);
                                        if(empty($attr_values)) {
                                            $attr_values = Yii::$app->styleService->attribute->getValuesByAttrId($attr_id);
                                        }
                                        */
                                        $attr_values = Yii::$app->styleService->attribute->getValuesByAttrId($attr_id);
                                        if(in_array($attr_id,\addons\Style\common\enums\AttrIdEnum::getMulteAttr())){
                                            $input = $form->field($model, $field)->widget(kartik\select2\Select2::class, [
                                                'data' => $attr_values,
                                                'options' => ['placeholder' => '请选择','multiple'=>true],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                    'multiple'=>true
                                                ],
                                            ])->label($attr_name);
                                        }else{
                                            $input = $form->field($model,$field)->dropDownList($attr_values,['prompt'=>'请选择'])->label($attr_name);
                                        }
                                        break;
                                    }
                                }//end switch
                                $collLg = 3;
                                ?>
                                <div class="col-lg-<?=$collLg?>"><?= $input ?></div>
                                <?php
                            }
                            ?>

                        </div>
                        <div class="row">
                            <div class="box-header with-border" style="margin: 0px 0 0px 0;">
                                <h3 class="box-title" style="font-weight: bold"> 其它信息</h3>
                            </div>
                            <?php if($model->is_inlay == InlayEnum::Yes && $model->jintuo_type == JintuoTypeEnum::Chengpin) {?>
                                <div class="col-lg-6">
                                    <?= $form->field($model, 'stone_info')->textarea() ?>
                                </div>
                            <?php }?>
                            <div class="col-lg-6">
                                <?= $form->field($model, 'remark')->textarea() ?>
                            </div>


                            <div class="col-lg-6">
                                <?= $form->field($model, 'goods_images')->widget(common\widgets\webuploader\Files::class, [
                                    'config' => [
                                        'pick' => [
                                            'multiple' => true,
                                        ],
                                    ]
                                ]); ?>
                            </div>
                            <div class="col-lg-6" style="padding: 0px;">
                                <?php $model->goods_video = !empty($model->goods_video)?explode(',', $model->goods_video):null;?>
                                <?= $form->field($model, 'goods_video')->widget(common\widgets\webuploader\Files::class, [
                                    'type'=>'videos',
                                    'config' => [
                                        'pick' => [
                                            'multiple' => true,
                                        ],
                                    ]
                                ]); ?>
                            </div>

                        </div>
                  <?php } else {?>
                  <div class="row">
                        <div class="col-lg-3">
                            <?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->styleCate->getGrpDropDown(),['prompt'=>'请选择','onchange'=>"searchGoods()"]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType->getGrpDropDown(),['prompt'=>'请选择','onchange'=>"searchGoods()"]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'jintuo_type')->dropDownList(\addons\Style\common\enums\JintuoTypeEnum::getMap(),['prompt'=>'请选择','onchange'=>"searchGoods()"]) ?>
                        </div>
                   </div>   
                  <?php }?>

            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
var formID = 'purchaseapplygoodsform';
function searchGoods() {
    var style_cate_id = $("#"+formID+"-style_cate_id").val();
    var product_type_id = $("#"+formID+"-product_type_id").val();
    var jintuo_type = $("#"+formID+"-jintuo_type").val();

    if(!style_cate_id || !product_type_id || !jintuo_type) {
        return false;
    }
    var url = "<?= Url::buildUrl(\Yii::$app->request->url,[],['style_cate_id','jintuo_type','product_type_id'])?>&style_cate_id="+style_cate_id+"&product_type_id="+product_type_id+"&jintuo_type="+jintuo_type;
    window.location.href = url;
}
</script>
