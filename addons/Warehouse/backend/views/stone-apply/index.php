<?php
use common\helpers\Html;
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
                        echo Html::a('批量配石', ['test'],  [
                            'class'=>'btn btn-success btn-xs',
                            "onclick" => "batchPop2(this);return false;",
                            'data-grid'=>'grid'
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
                                    'attribute' => 'from_order_sn',
                                    'filter' => Html::activeTextInput($searchModel, 'from_order_sn', [
                                            'class' => 'form-control',
                                            'style' =>'width:150px'
                                    ]),
                                    'format' => 'raw',
                            
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
                                    'attribute' => 'shape',
                                    'value' => function($model){
                                        return $model->shape ?? '无';
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
                                    'attribute' => 'songshi_user',
                                    'filter' => false,
                            ],
                            [
                                    'attribute' => 'songshi_time',
                                    'value' =>  function($model){
                                        return Yii::$app->formatter->asDatetime($model->songshi_time);
                                    },
                                    'filter' => false,
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
                                'attribute'=>'remark',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'created_at',
                                'filter' => false,
                                'value' => function($model){
                                    return Yii::$app->formatter->asDatetime($model->created_at);
                                }

                            ],
                            [
                                'attribute' => 'creator_name',
                                'filter' => false,
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