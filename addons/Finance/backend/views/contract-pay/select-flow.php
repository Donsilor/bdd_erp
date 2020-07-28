<?php

use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = Yii::t('goods', 'flows');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'showFooter' => true,//显示footer行
                    'id'=>'grid',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => false,
                        ],

                        [
                            'header' => '',
                            'class'=>yii\grid\CheckboxColumn::class,
                            'name'=>'id',  //设置每行数据的复选框属性
                            'headerOptions' => ['width'=>'30'],

                        ],
                        [
                            'header' => '',
                            'filter' => Html::activeTextInput($searchModel, 'sid', [
                                'class' => 'hide',
                            ]),
                            'headerOptions' => ['width'=>'5'],
                        ],

                        [
                            'label' => '审批序号',
                            'value' =>'id',
                            'filter' => Html::activeTextInput($searchModel, 'id', [
                                'class' => 'form-control',
                                'style' =>'width:100px'
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'label' => '单号',
                            'value' => function($model){
                                return Html::a($model->target_no, $model->url, ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                            },
                            'filter' => Html::activeTextInput($searchModel, 'target_no', [
                                'class' => 'form-control',
                                'style' =>'width:100px'
                            ]),
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'label' => '标题',
                            'value' => function($model){
                                return $model->flow_name;
                            },
                            'filter' => Html::activeTextInput($searchModel, 'flow_name', [
                                'class' => 'form-control',
                                'style' =>'width:100px'
                            ]),
                            'format' => 'raw',
                        ],

                        [
                            'label' => '状态',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model){
                                return \common\enums\FlowStatusEnum::getValue($model->flow_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'flow_status',\common\enums\FlowStatusEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                        ],

                    ]
                ]); ?>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => $model->formName(),
                'enableAjaxValidation' => false,
                'validationUrl' => Url::to(['select-flow', 'id' => $model->id]),
            ]); ?>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>审批序号</th>
                        <th>单号</th>
                        <th>标题</th>
                        <th>状态</th>
                        <th class="action-column">操作</th>
                    </tr>
                    </thead>
                    <tbody id="flow_table">
                    </tbody>
                </table>

            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>


<script>
    $(function() {

        $(".content-header").hide();

        var $input = $("input[name='SearchModel[sid]']");
        if(!$input.val()){
            $input.val("<?= $model->flow_ids?>")
        }

        if($input.val()){
            showFlow($input.val().split('|'));
        }
        // $.each($input.val().split('|'), function (i, v) {
        //     if(v!="") {
        //         showFlow(v);
        //     }
        // });

        $.each($('input[name="id[]"]'),function (i,obj) {
            var id = obj.value;
            var index = $.inArray(id, $input.val().split('|'))
            if(index >= 0){
                $(this).prop("checked", true);
            }
        })

        $('input[name="id[]"]').change(function() {
            if($(this).prop("checked")) {
                let result = addFlow($(this).eq(0).val());
                if(!result) {
                    $(this).prop("checked", false);
                }
            }
            else {
                delFlow($(this).val());
            }
        });

        function changeURLArg(url, arg, arg_val) {
            var pattern = arg+'=([^&]*)';
            var replaceText = arg+'='+arg_val;
            if(url.match(pattern)){
                var tmp = '/('+ arg+'=)([^&]*)/gi';
                tmp = url.replace(eval(tmp),replaceText);
                return tmp;
            } else {
                if(url.match('[\?]')) {
                    return url+'&'+replaceText;
                } else {
                    return url+'?'+replaceText;
                }
            }
        }

        //给分页绑定事件
        $(".pagination a").click(function () {
            let href = $(this).attr("href");
            let name = 'SearchModel[sid]';
            let value = $input.val();

            href = decodeURI(href);
            href = changeURLArg(href,name,value);
            href = encodeURI(href);

            $(this).attr("href", href);
        });

        function addFlow(flow_id) {

            var hav = true;

            var flowIds = $input.val().split('|');

            $.each(flowIds, function(i, v) {
                if(v == flow_id) {
                    hav = false;
                }
                if(v=="") {
                    flowIds.pop(i);
                }
            });



            flowIds.push(flow_id);
            $input.val(flowIds.join("|"));

            showFlow(flow_id);

            return true;
        }

        function showFlow(flow_id) {
            $.ajax({
                type: "post",
                url: '<?= Url::to(['../common/flow/get-flow'])?>',
                dataType: "json",
                data: {flow_id:flow_id},
                success: function (data) {
                    if (parseInt(data.code) !== 200) {
                        rfMsg(data.message);
                    } else {

                        var data = data.data
                        $.each(data,function (i,v) {
                            var tr = $("<tr>"
                                +"<td>" + v.id + "</td>"
                                +"<td><a href='"+ v.url +"' style='text-decoration:underline;color:#3c8dbc'>" + v.target_no + "</a></td>"
                                +"<td>" + v.flow_name + "</td>"
                                +"<td>" + v.flow_status + "</td>"
                                + "<input type='hidden' name='flow_id[]' value='"+ v.id +"'/>"
                                +'<td><a class="btn btn-danger btn-sm deltr" href="#" data-flowId="'+v.id+'">删除</a></td>'
                                + "</tr>");

                            tr.find(".deltr").click(function() {
                                delFlow($(this).attr("data-flowId"));
                            });

                            $("#flow_table").append(tr);
                        })


                    }
                }
            });
        }

        function delFlow(flow_id) {
            //取消数据保存
            var flowIds = $input.val().split('|');

            $.each(flowIds, function(i, v) {
                if(v == flow_id) {
                    delete flowIds.splice(i,1);
                }
            });

            $input.val(flowIds.join("|"));

            //取消选中
            $('input[name="id[]"]:checked').each(function(i, va) {
                if($(this).val()==flow_id) {
                    $(this).prop("checked", false);
                }
            });

            //删除显示
            $("#flow_table").find("a[data-flowId='"+flow_id+"']").parents("tr").remove();
        }
    });

</script>
