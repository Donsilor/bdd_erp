
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
        <link href="/backend/resources/css/print.css" rel="stylesheet">
		<script language="javascript">
			function preview(fang) {
				if (fang < 10) {
					bdhtml = window.document.body.innerHTML; //获取当前页的html代码
					sprnstr = "<!--startprint" + fang + "-->"; //设置打印开始区域
					eprnstr = "<!--endprint" + fang + "-->"; //设置打印结束区域
					prnhtml = bdhtml.substring(bdhtml.indexOf(sprnstr) + 18); //从开始代码向后取html
					prnhtml = prnhtml.substring(0, prnhtml.indexOf(eprnstr)); //从结束代码向前取html
					window.document.body.innerHTML = prnhtml;
					window.print();
					window.document.body.innerHTML = bdhtml;
				} else {
					window.print();
				}
			}
		</script>
        <style media="print">
            .Noprint {   DISPLAY:   none;}
            .PageNext {   PAGE-BREAK-AFTER:   always   }
        </style>
    </head>
	<body>
		<div class="container" id="wdf">
			<!--startprint1-->
			<div class="title"><?= Yii::$app->formatter->asDatetime(time(),'Y年M月') ?>石料盘点单明细表</div>
			
			<!-- 基础信息 -->
			<div class="order-info">
				<div class="list clf">
					<div class="child fl clf">
						<div class="child-attr fl">盘点单号：</div>
						<div class="child-val fl"><?= $model->bill_no ?? '' ?> </div>
					</div>
				</div>
			</div>

			<!-- 订单列表 -->
			<table class="table" border="1" cellspacing="0" cellpadding="0" width="100%" >
                <!-- 列表头部 -->
                <tr class="t-head">
                    <td>
                        <div>序号</div>
                    </td>
                    <td>
                        <div>石头类型</div>
                    </td>
                    <td>
                        <div>名称</div>
                    </td>
                    <td>
                        <div>石包号</div>
                    </td>
                    <td>
                        <div>款号</div>
                    </td>
                    <td>
                        <div>色彩</div>
                    </td>
                    <td>
                        <div>形状</div>
                    </td>
                    <td>
                        <div>重量（ct）</div>
                    </td>
                    <td>
                        <div>库存数量（个）</div>
                    </td>
                    <td>
                        <div>单价/ct</div>
                    </td>
                    <td>
                        <div>尺寸</div>
                    </td>
                    <td>
                        <div>规格(颜色/净度/切工/石重)</div>
                    </td>
                    <td>
                        <div>实盘(数量)</div>
                    </td>
                    <td>
                        <div>实盘(重量)</div>
                    </td>
                    <td>
                        <div>差异(数量)</div>
                    </td>
                    <td>
                        <div>差异(重量)</div>
                    </td>
                    <td>
                        <div>备注</div>
                    </td>

                </tr>
                <!-- 列表内容 -->
                <?php
                foreach ($lists as $key => $val){
                    $pagesize = 10;
                ?>
                <tr>
                    <td>
                        <div><?= $key + 1 ?></div>
                    </td>
                    <td>
                        <div><?= $val['parts_type'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['parts_name'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['parts_sn'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['style_sn'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['color'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['shape'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['parts_weight'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['parts_num'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['parts_price'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['parts_size'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['spec'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['actual_num'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['actual_weight'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['diff_num'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['diff_weight'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['remark'] ?></div>
                    </td>
                </tr>
                <?php if(($key + 1) % $pagesize == 0){?>
                 </table>
                <div class="PageNext"></div>
                <table class="table" border="1" cellspacing="0" cellpadding="0" width="100%" >
                    <tr class="t-head">
                        <td>
                            <div>序号</div>
                        </td>
                        <td>
                            <div>配件类型</div>
                        </td>
                        <td>
                            <div>名称</div>
                        </td>
                        <td>
                            <div>配件编号</div>
                        </td>
                        <td>
                            <div>款号</div>
                        </td>
                        <td>
                            <div>色彩</div>
                        </td>
                        <td>
                            <div>形状</div>
                        </td>
                        <td>
                            <div>重量（ct）</div>
                        </td>
                        <td>
                            <div>库存数量（个）</div>
                        </td>
                        <td>
                            <div>单价/ct</div>
                        </td>
                        <td>
                            <div>尺寸</div>
                        </td>
                        <td>
                            <div>规格(颜色/净度/切工/石重)</div>
                        </td>
                        <td>
                            <div>实盘(数量)</div>
                        </td>
                        <td>
                            <div>实盘(重量)</div>
                        </td>
                        <td>
                            <div>差异(数量)</div>
                        </td>
                        <td>
                            <div>差异(重量)</div>
                        </td>
                        <td>
                            <div>备注</div>
                        </td>
                    </tr>
                <?php
                 }
                }
                ?>
                <tr>
                    <td colspan="3"><div>合计</div></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><div><?= $total['parts_weight_count']?></div></td>
                    <td><div><?= $total['parts_num_count']?></div></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><div><?= $total['actual_weight_count']?></div></td>
                    <td><div><?= $total['actual_num_count']?></div></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
			</table>
            <div><span>制单人：<?= $model->creator->username ?? ''?></span><span style="margin-left:300px; ">审核人：<?= $model->auditor->username ?? ''?></span></span></div>

			<!--endprint1-->
		</div>
        <div class="text-center Noprint">
            <!-- 打印按钮 -->
            <button type="button" class="btn-ms" target="_blank" onclick="preview(1)">打印</button>
        </div>

	</body>
</html>
