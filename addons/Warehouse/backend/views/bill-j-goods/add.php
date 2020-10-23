<?php

use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;

$this->title = '新增货品';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
           <?php $search = ActiveForm::begin([
                        'enableAjaxValidation' => false,
                        'validationUrl' => Url::to(['add','search'=>1]),
                     ]);
           
                  if(is_array($model->goods_ids)) {
                      $model->goods_ids = implode(',', $model->goods_ids);
                  }
           ?>
            <div class="box-body" style="padding:20px 50px">
                <?= Html::hiddenInput('search', 1) ?>
                <?= $search->field($model, 'id')->hiddenInput()->label(false) ?>                
                <div class="row">
                    <div class="col-lg-4">                     
                        <?= $search->field($model, 'goods_ids')->textarea(["placeholder" => "请输入货号，多个请用用逗号/空格/换行符隔开", 'style' => 'height:120px']) ?>
                    </div>
                    <div class="col-lg-1">
                        <?= Html::button('查询', ['class' => 'btn btn-info btn-sm', 'style' => 'margin-top:27px;', 'type' => "submit"]) ?>                        
                    </div>
                    <div class="col-sm-6">
                        <div>
                            <label class="control-label" style="vertical-align:top"> 操作日志提示</label>
                        </div>
                        <div id="search_logs" title="系统日志" style="width:100%; height:120px; padding:5px; color:red; border:1px solid #cecece;overflow:scroll">
                            <?= $model->getGoodsMessage()?>
                        </div>
                    </div>
                </div>
           </div>
           <?php ActiveForm::end(); ?>
           <?php $form = ActiveForm::begin([]); ?>
              <?php if(!empty($dataProvider)) {?>  
                <?= Html::hiddenInput('search', 0) ?>
                <div class="box-body table-responsive" style="padding:20px 50px">
                    <?= yii\grid\GridView::widget([
                        'dataProvider' => $dataProvider,
                        //'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['style' => 'white-space:nowrap;font-size:12px;'],
                        'id'=>'grid',
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => true,
                            ],                            
                            [
                                'attribute' => 'goods_id',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'style_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => true,
                            ],
                            [
                                'attribute' => 'goods_name',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'label' => '最大可借数量',
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value'=>function($model) {
                                    return $model->goods_num - $model->stock_num - $model->do_chuku_num;
                                }
                            ],
                            [
                                'attribute' => '借货数量',
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1',"style"=>"background-color:#84bf96;"],
                                'value'=>function($_model) use($form, $model){
                                    return $form->field($model, "goods_list[{$_model->goods_id}][return_num]")->textInput(['value'=>''])->label(false) ;
                                }
                            ],                            
                            [
                                'attribute' => 'goods_status',
                                'value' => function ($model) {
                                    return \addons\Warehouse\common\enums\GoodsStatusEnum::getValue($model->goods_status);
                                },
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],                            
                            [
                                'attribute' => 'warehouse_id',
                                'value' => "warehouse.name",
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'material_type',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->material_type) ?? "";
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'material_color',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->material_color) ?? "";
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '手寸',
                                'value' => function ($model) {
                                    $finger = '';
                                    if ($model->finger ?? false) {
                                        $finger .= Yii::$app->attr->valueName($model->finger) . '(US)';
                                    }
                                    if ($model->finger_hk ?? false) {
                                        $finger .= ' ' . Yii::$app->attr->valueName($model->finger_hk) . '(HK)';
                                    }
                                    return $finger;
                                },
                                'filter' => false,
                            ],
                            [
                                'label' => '连石重',
                                'value' => function ($model) {
                                    return $model->suttle_weight ?? '';
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'main_stone_type',
                                'value' => function ($model) {
                                    if ($model->main_stone_type) {
                                        return Yii::$app->attr->valueName($model->main_stone_type) ?? "";
                                    }
                                    return "";
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'diamond_carat',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'main_stone_num',
                                'filter' => false,
                            ],                                                       
                            [
                                'attribute' => 'cost_price',
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'cost_amount',
                                'value' => function ($model) {
                                    return $model->cost_price * $model->goods_num;
                                },
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                'filter' => false,
                            ],                            
                        ]
                    ]); ?>
                    <?php }?>
                </div>
                <!-- ./box-body -->
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    var formId = 'warehousebillthform';

    function searchGoods() {
        var goods_ids = $("#" + formId + "-goods_ids").val();
        if (!goods_ids) {
            rfMsg("请输入货号");
            return false;
        }
        goods_ids = goods_ids.replace(/\n/g, ',');
        $.ajax({
            type: "post",
            url: '<?= Url::buildUrl(\Yii::$app->request->url)?>',
            dataType: "json",
            data: {
                'search': 1,
                'goods_ids': goods_ids,
            },
            success: function (data) {
                if (parseInt(data.code) == 200) {
                    var url = "<?= Url::buildUrl(\Yii::$app->request->url, [], [ 'message'])?>&goods_ids=" + data.data.valid_goods_ids + "&message=" + data.data.message;
                    console.log(url);
                    window.location.href = url;
                }
            }
        });
    }
</script>
