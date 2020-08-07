<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\enums\QibanTypeEnum;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                 <?= $form->field($model, 'order_id')->hiddenInput()->label(false) ?>
			     <?php if($model->goods_id) {?>
			         <div class="row">
    			         <?php if($model->isNewRecord) {?>
            			 <div class="col-lg-3">
                			<?= $form->field($model, 'goods_id')->textInput() ?>
            			 </div>
            			 <div class="col-lg-1">
                            <?= Html::button('查询',['class'=>'btn btn-info btn-sm','style'=>'margin-top:27px;','onclick'=>"searchGoods()"]) ?>
        			     </div>
        			     <?php }else{?>
        			     <div class="col-lg-4">
                			<?= $form->field($model, 'goods_id')->textInput(['disabled'=>'disabled']) ?>
            			 </div>
        			     <?php }?>
        			     <div class="col-lg-4">
                            <?= $form->field($model, 'qiban_type')->dropDownList(QibanTypeEnum::getMap(),['disabled'=>true]) ?>
            			 </div>
        			     <div class="col-lg-4">
            			 	<?= $form->field($model, 'style_sex')->dropDownList(StyleSexEnum::getMap(),['disabled'=>true]) ?>
            			 </div>
            			 <div class="col-lg-4">
            			 	<?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->styleCate->getDropDown(),['disabled'=>true]) ?>
            			 </div>
            			 <div class="col-lg-4">
            			 	<?= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType->getDropDown(),['disabled'=>true]) ?>
            			 </div>
                         <div class="col-lg-4">
                             <?= $form->field($model, 'goods_num')->textInput(['disabled'=>true]) ?>
                         </div>
                         <div class="col-lg-4">
                             <?= $form->field($model, 'jintuo_type')->dropDownList(\addons\Style\common\enums\JintuoTypeEnum::getMap(),['disabled'=>true]) ?>
                         </div>
                         <div class="col-lg-4">
                             <?php
                             $disabled = ['disabled'=>'disabled'];
                             $is_exeist = Yii::$app->styleService->style->isExist($model->style_sn);
                             if(!$is_exeist){
                                 $disabled = [];
                             }
                             ?>
                             <?= $form->field($model, 'style_sn')->textInput($disabled) ?>
                         </div>
                         <div class="col-lg-4">
                             <?php
                             $disabled = ['disabled'=>'disabled'];
                             $is_exeist = Yii::$app->styleService->qiban->isExist($model->qiban_sn);
                             if(!$is_exeist){
                                 $disabled = [];
                             }
                             ?>
                             <?= $form->field($model, 'qiban_sn')->textInput($disabled) ?>
                         </div>
                         <div class="col-lg-4">
                             <?= $form->field($model, 'goods_name')->textInput(['disabled'=>true]) ?>
                         </div>

        			 </div>
                     <div class="row">
                         <div class="col-lg-4">
                             <?= $form->field($model, 'goods_price')->textInput()->label('商品价格（<font color="red">价格以：订单选择的货币类型为准）</font>') ?>
                         </div>
                         <div class="col-lg-4">
                             <?= $form->field($model, 'goods_pay_price')->textInput()->label('实际成交价（<font color="red">价格以：订单选择的货币类型为准</font>）') ?>
                         </div>
                     </div>
                     <div class="row">
                         <div class="col-lg-4">
                             <?= $form->field($model, 'remark')->textarea() ?>
                         </div>
                     </div>

    			<?php }else{?>
        			<div class="row">
            			 <div class="col-lg-4">
                			<?= $form->field($model, 'goods_id')->textInput() ?>
            			 </div>
            			 <div class="col-lg-1">
                            <?= Html::button('查询',['class'=>'btn btn-info btn-sm','style'=>'margin-top:27px;','onclick'=>"searchGoods()"]) ?>
        			     </div>
        			</div>
    			<?php }?>



            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
function searchGoods() {
   var goods_id = $.trim($("#ordergoodsform-goods_id").val());
   if(!goods_id) {
	    rfMsg("请输入货号");
        return false;
   }
   var url = "<?= Url::buildUrl(\Yii::$app->request->url,[],['goods_id','search'])?>&search=1&goods_id="+goods_id;
   window.location.href = url;
}
</script>
