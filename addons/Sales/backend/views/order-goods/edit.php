<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Supply\common\enums\PeiliaoTypeEnum;
use addons\Style\common\enums\AttrModuleEnum;
use addons\Style\common\enums\JintuoTypeEnum;
use addons\Supply\common\enums\PeishiTypeEnum;

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
			     <?php if($model->goods_sn) {?>
			         <div class="row">
    			         <?php if($model->isNewRecord) {?>      			    
            			 <div class="col-lg-3">         
                			<?= $form->field($model, 'goods_sn')->textInput() ?>
            			 </div>
            			 <div class="col-lg-1">
                            <?= Html::button('查询',['class'=>'btn btn-info btn-sm','style'=>'margin-top:27px;','onclick'=>"searchGoods()"]) ?>
        			     </div>
        			     <?php }else{?>
        			     <div class="col-lg-4">
                             <?php
                             $disabled = ['disabled'=>'disabled'];
                             if($model->qiban_type == QibanTypeEnum::NON_VERSION ){
                                 //非起版
                                 $is_exist = Yii::$app->styleService->style->isExist($model->goods_sn);
                                 if(!$is_exist){
                                     $disabled = [];
                                 }
                             }else{
                                 //起版
                                 $is_exist = Yii::$app->styleService->qiban->isExist($model->goods_sn);
                                 if(!$is_exist){
                                     $disabled = [];
                                 }
                             }
                             ?>
                			<?= $form->field($model, 'goods_sn')->textInput($disabled) ?>
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

        			 </div> 
        			 <div class="row">
                         <div class="col-lg-4">
                             <?= $form->field($model, 'jintuo_type')->dropDownList(\addons\Style\common\enums\JintuoTypeEnum::getMap(),['prompt'=>'请选择','onchange'=>"searchGoods()",'disabled'=>$model->qiban_type!= QibanTypeEnum::NON_VERSION]) ?>
                         </div>
                         <div class="col-lg-4">
                                <?= $form->field($model, 'goods_name')->textInput() ?> 
            			 </div>
        			 </div>
                     <div class="row">
                         <div class="col-lg-4">
                             <?= $form->field($model, 'goods_price')->textInput()->label('商品价格（<font color="red">价格以：订单选择的货币类型为准）</font>') ?>
                         </div>
                         <div class="col-lg-4">
                             <?= $form->field($model, 'goods_pay_price')->textInput()->label('实际成交价（<font color="red">价格以：订单选择的货币类型为准</font>）') ?>
                         </div>
                         <div class="col-lg-4">
                             <?= $form->field($model, 'assess_cost')->textInput()->label('预估成本（<font color="red">价格以：订单选择的货币类型为准</font>）') ?>
                         </div>
                     </div>
                     <div class="row">
                         <div class="col-lg-4">
                             <?= $form->field($model, 'remark')->textarea(['readonly'=>false,'style'=>'height:80px']) ?>
                         </div>
                         <?php if($model->getGoodsSpec()) {?>
                         <div class="col-lg-4">
                             <?php $model->goods_spec = str_replace("<br/>",PHP_EOL,$model->getGoodsSpec());?>
                             <?= $form->field($model, 'goods_spec')->textarea(['readonly'=>true,'style'=>'height:80px']) ?>
                         </div>
                         <?php }?>
                     </div>

                     <?php if($model->is_stock == \addons\Sales\common\enums\IsStockEnum::NO){ ?>
					<div style="margin-bottom:20px;">
                        <h3 class="box-title"> 属性信息</h3>
                    </div>

                     <?php
                     $attr_list = \Yii::$app->styleService->attribute->module(AttrModuleEnum::SALE)->getAttrListByCateId($model->style_cate_id,JintuoTypeEnum::getValue($model->jintuo_type,'getAttrTypeMap'),$model->is_inlay);
                     foreach ($attr_list as $k=>$attr){
                         $attr_id  = $attr['id'];//属性ID
                         $is_require = $attr['is_require'];
                         $attr_name = \Yii::$app->attr->attrName($attr_id);//属性名称

                         $_field = $is_require == 1 ? 'attr_require':'attr_custom';
                         $field = "{$_field}[{$attr_id}]";
                         switch ($attr['input_type']){
                             case common\enums\InputTypeEnum::INPUT_TEXT :{
                                 $input = $form->field($model,$field)->textInput()->label($attr_name);
                                 break;
                             }
                             default:{
                                 if($model->qiban_type == QibanTypeEnum::NON_VERSION) {
                                     //获取款式属性值列表
                                     $attr_values = Yii::$app->styleService->styleAttribute->getDropdowns($model->style_id,$attr_id);
                                 }else{
                                     //获取起版属性值列表
                                     $attr_values = Yii::$app->styleService->qibanAttribute->getDropdowns($model->style_id,$attr_id);
                                 }
                                 if(empty($attr_values)) {
                                     $attr_values = Yii::$app->styleService->attribute->getValuesByAttrId($attr_id);
                                 }
                                 $input = $form->field($model,$field)->dropDownList($attr_values,['prompt'=>'请选择'])->label($attr_name);
                                 break;
                             }
                         }//end switch
                         $collLg = 4;
                         ?>
                         <?php if ($k % 3 == 0){ ?><div class="row"><?php }?>
                         <div class="col-lg-<?=$collLg?>"><?= $input ?></div>
                         <?php if(($k+1) % 3 == 0 || ($k+1) == count($attr_list)){?></div><?php }?>
                         <?php
                     }//end foreach $attr_list
                     ?>
                     <div class="row">
                         <div class="col-lg-4">
                             <?= \common\helpers\ImageHelper::fancyBox($model->goods_image,90,90); ?>
                             <?= $form->field($model, 'goods_image')->hiddenInput()->label(false) ?>
                         </div>
                     </div>
                     <?php } ?>
    			<?php }else{?>
        			<div class="row">
            			 <div class="col-lg-4">         
                			<?= $form->field($model, 'goods_sn')->textInput() ?>
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
   var goods_sn = $.trim($("#ordergoodsform-goods_sn").val());
   var jintuo_type = $("#ordergoodsform-jintuo_type").val();
   if(!goods_sn) {
	    rfMsg("请输入款号或起版号");
        return false;
   }
   var url = "<?= Url::buildUrl(\Yii::$app->request->url,[],['goods_sn','search','jintuo_type'])?>&search=1&goods_sn="+goods_sn+"&jintuo_type="+jintuo_type;
   window.location.href = url;
}
</script>
