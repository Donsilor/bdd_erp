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
                            <?= $form->field($model, 'goods_id')->textInput(['disabled'=>true]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'goods_name')->textInput() ?>
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
                            <?= $form->field($model, 'finger')->textInput() ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'length')->textInput() ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'cert_type')->dropDownList(\addons\Style\common\enums\CertTypeEnum::getMap()) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'cert_id')->textInput() ?>
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
