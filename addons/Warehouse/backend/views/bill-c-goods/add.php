<?php

use common\helpers\Html;
use common\helpers\Url;
use unclead\multipleinput\MultipleInput;
use yii\widgets\ActiveForm;

$this->title = '新增货品';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_ids')->textarea(["placeholder" => "请输入货号，多个请用用逗号/空格/换行符隔开", 'style' => 'height:100px']) ?>
                    </div>
                    <div class="col-lg-1">
                        <?= Html::button('查询', ['class' => 'btn btn-info btn-sm', 'style' => 'margin-top:27px;', 'onclick' => "searchGoods()"]) ?>
                    </div>
                    <div class="col-sm-6">
                        <div>
                            <label class="control-label" style="vertical-align:top">
                                操作日志提示：
                            </label>
                        </div>
                        <div id="search_logs" title="系统日志"
                             style="width:100%; height:100px; padding:5px; color:red; border:1px solid #cecece;overflow:scroll">
                            <?= $message ?>
                        </div>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <div class="tab-content">
                        <?= $form->field($model, 'goods')->widget(MultipleInput::className(), [
                            'max' => 99,
                            'addButtonOptions' => ['label' => '', 'class' => ''],
                            'value' => $searchGoods,
                            'columns' => [
                                [
                                    'name' => 'goods_id',
                                    'title' => "货号",
                                    'enableError' => false,
                                    'options' => [
                                        'class' => 'input-priority',
                                        'readonly' => 'true',
                                        'style' => 'width:120px'
                                    ]
                                ],
                                [
                                    'name' => 'style_sn',
                                    'title' => "款号",
                                    'enableError' => false,
                                    'options' => [
                                        'class' => 'input-priority',
                                        'readonly' => 'true',
                                        'style' => 'width:100px'
                                    ]
                                ],
                                [
                                    'name' => 'goods_name',
                                    'title' => "商品名称",
                                    'enableError' => false,
                                    'options' => [
                                        'class' => 'input-priority',
                                        'readonly' => 'true',
                                        'style' => 'width:140px'
                                    ]
                                ],
                                [
                                    'name' => "warehouse_id",
                                    'title' => "仓库",
                                    'enableError' => false,
                                    'type' => 'dropDownList',
                                    'options' => [
                                        'class' => 'input-priority',
                                        'disabled' => 'true',
                                        'style' => 'width:140px',
                                        'prompt' => '请选择',
                                    ],
                                    'items' => $gModel->getWarehouseMap()
                                ],
                                [
                                    'name' => 'stock_num',
                                    'title' => "库存数量",
                                    'enableError' => false,
                                    'options' => [
                                        'class' => 'input-priority',
                                        'readonly' => 'true',
                                        'style' => 'width:60px'
                                    ]
                                ],
                                [
                                    'name' => 'material_type',
                                    'title' => "材质",
                                    'enableError' => false,
                                    'options' => [
                                        'class' => 'input-priority',
                                        'readonly' => 'true',
                                        'style' => 'width:80px'
                                    ]
                                ],
                                [
                                    'name' => 'finger',
                                    'title' => "手寸",
                                    'enableError' => false,
                                    'options' => [
                                        'class' => 'input-priority',
                                        'readonly' => 'true',
                                        'style' => 'width:80px'
                                    ]
                                ],
                                [
                                    'name' => "suttle_weight",
                                    'title' => "连石重(g)",
                                    'enableError' => false,
                                    'defaultValue' => 0,
                                    'options' => [
                                        'class' => 'input-priority',
                                        'type' => 'number',
                                        'readonly' => 'true',
                                        'style' => 'width:75px'
                                    ]
                                ],
                                [
                                    'name' => "main_stone_num",
                                    'title' => "主石粒数",
                                    'enableError' => false,
                                    'defaultValue' => 0,
                                    'options' => [
                                        'class' => 'input-priority',
                                        'readonly' => 'true',
                                        'style' => 'width:75px'
                                    ]
                                ],
                                [
                                    'name' => "diamond_carat",
                                    'title' => "主石重(ct)",
                                    'enableError' => false,
                                    'defaultValue' => 0,
                                    'options' => [
                                        'class' => 'input-priority',
                                        'readonly' => 'true',
                                        'style' => 'width:75px'
                                    ]
                                ],
                                [
                                    'name' => 'product_size',
                                    'title' => "尺寸",
                                    'enableError' => false,
                                    'options' => [
                                        'class' => 'input-priority',
                                        'readonly' => 'true',
                                        'style' => 'width:60px'
                                    ]
                                ],
                                [
                                    'name' => "cost_price",
                                    'title' => "采购成本/单价",
                                    'enableError' => false,
                                    'defaultValue' => 0,
                                    'options' => [
                                        'class' => 'input-priority',
                                        'type' => 'number',
                                        'readonly' => 'true',
                                        'style' => 'width:100px'
                                    ]
                                ],
                            ]
                        ])->label(false) ?>
                    </div>
                </div>
                <!-- ./box-body -->
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    var formId = 'warehousebillcform';

    function searchGoods() {
        var goods_ids = $("#" + formId + "-goods_ids").val();
        if (!goods_ids) {
            rfMsg("请输入货号");
            return false;
        }
        goods_ids = goods_ids.replace(/\n/g, ',');
        $.ajax({
            type: "get",
            url: '<?= Url::buildUrl(\Yii::$app->request->url)?>',
            dataType: "json",
            data: {
                'search': 1,
                'goods_ids': goods_ids,
            },
            success: function (data) {
                if (parseInt(data.code) == 200) {
                    var url = "<?= Url::buildUrl(\Yii::$app->request->url, [], ['goods_ids', 'message'])?>&goods_ids=" + data.data.valid_goods_ids + "&message=" + data.data.message;
                    window.location.href = url;
                }
            }
        });
    }
</script>
