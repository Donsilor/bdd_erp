<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

$this->title = '流程日志';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">流程日志 - <?php echo $model->finance_no?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="row col-xs-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
                <div class="box-body table-responsive" style="padding-left: 0px;padding-right: 0px;">
                    <div id="grid" style="white-space:nowrap;">
                        <table class="table table-hover"><thead>
                            <tr>
                                <th width="80">ID</th>
                                <th width="200">所属部门</th>
                                <th width="100">审批人</th>
                                <th>审批备注</th>
                                <th width="100">审批时间</th>

                            </tr>
                            </thead>
                            <tbody>
                              <?php
                                foreach ($flow_detail_arr as $flow_detail){
                              ?>
                                <tr data-key="2">
                                    <td><?= $flow_detail->id?></td>
                                    <td><?= $flow_detail->memeber->department->name ?? ''?></td>
                                    <td><?= $flow_detail->member->username ?? ''?></td>
                                    <td><?= $flow_detail->audit_remark?></td>
                                    <td><?= Yii::$app->formatter->asDatetime($flow_detail->audit_time)?></td>

                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <!-- box end -->
        </div>
    </div>
</div>