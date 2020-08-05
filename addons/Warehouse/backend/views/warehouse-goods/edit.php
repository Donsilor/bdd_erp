<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;
use addons\Style\common\enums\AttrIdEnum;

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
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">基本信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_id')->textInput(['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'style_sn')->textInput(['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'jintuo_type')->dropDownList(\addons\Style\common\enums\JintuoTypeEnum::getMap(),['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_status')->dropDownList(\addons\Warehouse\common\enums\GoodsStatusEnum::getMap(),['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'warehouse_id')->dropDownList(Yii::$app->warehouseService->warehouse::getDropDown(),['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'put_in_type')->dropDownList(\addons\Warehouse\common\enums\PutInTypeEnum::getMap(),['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType::getDropDown(),['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->productType::getDropDown(),['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'supplier_id')->dropDownList(Yii::$app->supplyService->supplier->getDropDown(),['disabled'=>true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_name')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'produce_sn')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'material')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'finger')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::FINGER),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'length')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'xiangkou')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'market_price')->textInput() ?>
                    </div>
                </div>

                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">金属信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gold_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_gold_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gold_loss')->textInput() ?>
                    </div>
                </div>

                <div class="row">
                    <div class="with-border">
                        <h5 class="box-title" style="font-weight: bold">石头信息</h5>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cert_type')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cert_id')->textInput() ?>
                    </div>

                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_type')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_shape')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_SHAPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_carat')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'main_stone_num')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_color')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOR),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_clarity')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_CLARITY),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_cut')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_CUT),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_polish')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_POLISH),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_symmetry')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_SYMMETRY),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'diamond_fluorescence')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::DIA_FLUORESCENCE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_type1')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_weight1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_num1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_price1')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_type2')->dropDownList(Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_num2')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'second_stone_weight2')->textInput() ?>
                    </div>


                </div>


                <!-- ./box-body -->

            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
function searchGoods() {
   var style_sn = $.trim($("#qibanattrform-style_sn").val());
   var jintuo_type = $("#qibanattrform-jintuo_type").val();
   if(!style_sn) {
        alert("请输入款号");
        return false;
   }
   var url = "<?= Url::buildUrl(\Yii::$app->request->url,[],['style_sn','search','jintuo_type'])?>&search=1&style_sn="+style_sn+"&jintuo_type="+jintuo_type;
   window.location.href = url;
}
</script>
