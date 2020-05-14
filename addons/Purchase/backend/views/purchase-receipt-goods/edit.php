<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Style\common\enums\AttrTypeEnum;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;
use addons\Style\common\enums\StyleSexEnum;

$this->title = '新增货品';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                 <?= $form->field($model, 'receipt_id')->hiddenInput()->label(false) ?>
                 <div class="row">
                     <div class="col-lg-3">
                        <?= $form->field($model, 'produce_sn')->textInput() ?>
                     </div>
                     <div class="col-lg-1">
                        <?= Html::button('查询',['class'=>'btn btn-info btn-sm','style'=>'margin-top:27px;','onclick'=>"searchReceiptGoods()"]) ?>
                     </div>
                 </div>
               <!-- ./box-body -->
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
function searchReceiptGoods() {
   var produce_sns = $.trim($("#purchasereceiptgoodsform-produce_sn").val());
   /*if(!produce_sns) {
	    rfMsg("请输入布产单编号");
        return false;
   }*/
   produce_sns = 'BC20051259902542';
    var url = "<?= Url::buildUrl(\Yii::$app->request->url,[],['produce_sns','search',])?>&search=1&produce_sns="+produce_sns;
    window.location.href = url;
}
</script>
