<?php
use common\helpers\Html;
use kartik\select2\Select2;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '配石列表';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?php
                        echo Html::a('批量配石', ['peishi','check'=>1],  [
                            'class'=>'btn btn-primary btn-xs',
                            "onclick" => "batchPop(this);return false;",
                            'data-grid'=>'grid',
                            'data-width'=>'90%',
                            'data-height'=>'90%',
                            'data-offset'=>'20px',
                            'data-title'=>'批量配石',
                        ]);
                        echo '&nbsp;';                        
                    ?>
                    <?php
                        echo Html::a('创建领石单', ['lingshi','check'=>1],  [
                            'class'=>'btn btn-success btn-xs',
                            "onclick" => "batchPop(this);return false;",
                            'data-grid'=>'grid',
                            'data-width'=>'90%',
                            'data-height'=>'90%',
                            'data-offset'=>'20px',
                            'data-title'=>'创建领石单-预览',
                        ]);
                        echo '&nbsp;';                        
                    ?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <?php echo Html::batchButtons(false)?>
                <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        //'options' => ['style'=>' width:130%;'],
                        'options' => ['style'=>'white-space:nowrap;'],
                        'showFooter' => false,//显示footer行
                        'id'=>'grid',
                        'columns' => [
                            
                            [
                                    'class'=>'yii\grid\CheckboxColumn',
                                    'name'=>'id',  //设置每行数据的复选框属性
                                    'headerOptions' => ['width'=>'30'],
                            ],
                            [
                                    'class' => 'yii\grid\SerialColumn',
                                    'visible' => true,
                            ],
                            [
                                    'attribute' => 'id',
                                    'value'  => 'id',
                                    'filter' => true,
                            ], 
                            [
                                    'attribute' => 'created_at',
                                    'filter' => false,
                                    'value' => function($model){
                                         return Yii::$app->formatter->asDatetime($model->created_at);
                                    }
                            ], 
                            [
                                    'attribute' => 'from_order_sn',
                                    'filter' => Html::activeTextInput($searchModel, 'from_order_sn', [
                                            'class' => 'form-control',
                                            'style' =>'width:150px'
                                    ]),
                                    'format' => 'raw',
                            
                            ],
                            [
                                'label' => '加工商',
                                'attribute' => 'supplier_id',
                                'value' =>"supplier.supplier_name",
                                'filter'=>Select2::widget([
                                    'name'=>'SearchModel[supplier_id]',
                                    'value'=>$searchModel->supplier_id,
                                    'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                                    'options' => ['placeholder' =>"请选择",'class' => 'col-md-4', 'style'=> 'width:120px;'],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'width'=>'200px',
                                    ],
                                ]),
                                'format' => 'raw',
                                'headerOptions' => [],
                            ],
                            [
                                    'attribute' => 'from_type',
                                    'value' => function ($model){
                                        return \addons\Supply\common\enums\FromTypeEnum::getValue($model->from_type);
                                    },
                                    'filter' =>Html::activeDropDownList($searchModel, 'from_type',\addons\Supply\common\enums\FromTypeEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                            'style' => 'width:80px;',
                                    ]),
                                    'format' => 'raw',
                            ],
                            [
                                    'attribute' => 'peishi_status',
                                    'value' => function ($model){
                                        return \addons\Supply\common\enums\PeishiStatusEnum::getValue($model->peishi_status);
                                    },
                                    'filter' =>Html::activeDropDownList($searchModel, 'peishi_status',\addons\Supply\common\enums\PeishiStatusEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                            'style' => 'width:80px;',
                                    ]),
                                    'format' => 'raw',
                            ],
                            [
                                    'label' => '领石单号',
                                    'attribute' => 'delivery_no',
                                    'filter' => Html::activeTextInput($searchModel, 'delivery_no', [
                                            'class' => 'form-control',
                                            'style' =>'width:150px'
                                    ]),
                                    'format' => 'raw',
                                    
                            ],
                            [
                                    'attribute' => 'stone_position',
                                    'value'  => function($model) {
                                         return \addons\Style\common\enums\StonePositionEnum::getValue($model->stone_position);
                                    },
                                    'filter' => false,
                                    
                            ],
                            [
                                    'attribute' => 'stone_type',
                                    'value'  => function($model) {
                                        return $model->stone_type ?? '无';
                                    },
                                    'filter' => false,
                                    
                            ],
                            [
                                    'attribute' => 'stone_num',
                                    'value' => 'stone_num',
                                    'filter' => false,

                            ],
                            [
                                    'attribute' => 'stone_weight',
                                    'value' => 'stone_weight',
                                    'filter' => false,
                            ],
                            [
                                    'attribute' => 'shape',
                                    'value' => function($model){
                                        return $model->shape ?? '无';
                                    },
                                    'filter' => false,
                                    
                            ],
                            [
                                    'attribute' => 'secai',
                                    'value' => function($model){
                                        return $model->secai ?? '无';
                                    },
                                    'filter' => false,
                                    
                            ],
                            [
                                    'attribute' => 'color',
                                    'value' => function($model){
                                        return $model->color ?? '无';
                                    },
                                    'filter' => false,                                    
                            ],
                            [
                                    'attribute' => 'clarity',
                                    'value' => function($model){
                                        return $model->clarity ?? '无';
                                    },
                                    'filter' => false,
                                    
                            ],
                            [
                                    'attribute' => 'stone_spec',
                                    'value' => 'stone_spec',
                                    'filter' => false,                                    
                            ], 
                            [
                                    'label' => '配石信息(石头编号/数量/总重)',
                                    'value' => function($model){
                                         $str = '';
                                         foreach ($model->stoneGoods ?? [] as $stone){
                                             $str .=$stone->stone_sn.'/'.$stone->stone_num."/".$stone->stone_weight."ct<br/>";
                                         }
                                         return $str;
                                    },
                                    'filter' => false,
                                    'format' => 'raw',
                            ], 
                            [
                                    'attribute' => 'peishi_user',
                                    'filter' => false,
                            ],
                            [
                                    'attribute' => 'peishi_time',
                                    'value' =>  function($model){
                                        return Yii::$app->formatter->asDatetime($model->peishi_time);
                                    },
                                    'filter' => false,
                            ],
                            [
                                    'attribute'=>'peishi_remark',
                                    'filter' => false,
                                    'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'remark',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                        ]
                    ]); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    //批量操作
    function batchPeishi(obj) {
        let $e = $(obj);
        let url = $e.attr('href');
        var ids = new Array;
        $('input[name="id[]"]:checked').each(function(i){
            var str = $(this).val();
            var arr = jQuery.parseJSON(str)
            ids[i] = arr.id;
        });
        if(ids.length===0) {
            rfInfo('未选中数据！','');
            return false;
        }
        var ids = ids.join(',');
        $.ajax({
            type: "get",
            url: url,
            dataType: "json",
            data: {
                ids: ids
            },
            success: function (data) {
                if (parseInt(data.code) !== 200) {
                    rfAffirm(data.message);
                } else {
                    var href = data.data.url;
                    var title = '基本信息';
                    var width = '80%';
                    var height = '80%';
                    var offset = "10%";
                    openIframe(title, width, height, href, offset);
                    e.preventDefault();
                    return false;
                }
            }
        });
    }

    //批量生成不良返厂单
    function batchDefective(obj) {
        let $e = $(obj);
        let url = $e.attr('href');
        var ids = new Array;
        $('input[name="id[]"]:checked').each(function(i){
            var str = $(this).val();
            var arr = jQuery.parseJSON(str)
            ids[i] = arr.id;
        });
        if(ids.length===0) {
            rfInfo('未选中数据！','');
            return false;
        }
        var ids = ids.join(',');
        appConfirm("确定要生成不良返厂单吗?", '', function (code) {
            if(code !== "defeat") {
                return;
            }
            $.ajax({
                type: "post",
                url: url,
                dataType: "json",
                data: {
                    ids: ids
                },
                success: function (data) {
                    if (parseInt(data.code) !== 200) {
                        rfAffirm(data.message);
                    } else {
                        window.location.reload();
                    }
                }
            });
        });
    }
</script>