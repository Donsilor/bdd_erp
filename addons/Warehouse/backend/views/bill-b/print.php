
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
			<div class="title"><?= Yii::$app->formatter->asDatetime(time(),'Y年M月') ?>货品维修退货单</div>
			
			<!-- 基础信息 -->
			<div class="order-info">

				<div class="list clf">
					<div class="child fl clf">
						<div class="child-attr fl">退货单号：</div>
						<div class="child-val fl"><?= $model->repair_no ?? '' ?> </div>
					</div>
                    <div class="child fl clf">
                        <div class="child-attr fl">退货供应商：</div>
                        <div class="child-val fl"><?= $model->supplier->supplier_name ?? '' ?> </div>
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
                        <div>货品名称</div>
                    </td>
					<td >
						<div>条码号</div>
					</td>
					<td>
						<div>款号</div>
					</td>
					<td>
						<div>产品分类</div>
					</td>
                    <td>
                        <div>商品类型</div>
                    </td>
                    <td>
                        <div>仓库</div>
                    </td>

                    <td>
                        <div>材质</div>
                    </td>
                    <td>
                        <div>金重</div>
                    </td>
                    <td>
                        <div>成本</div>
                    </td>
                    <td>
                        <div>主石类型</div>
                    </td>
                    <td>
                        <div>主石重</div>
                    </td>
                    <td>
                        <div>主石粒数</div>
                    </td>
                    <td>
                        <div>主石规格(颜色/净度/切工/抛光/对称/荧光)</div>
                    </td>
                    <td>
                        <div>副石重</div>
                    </td>
                    <td>
                        <div>副石粒数</div>
                    </td>
                    <td>
                        <div>总重</div>
                    </td>
                    <td>
                        <div>手寸</div>
                    </td>
                    <td>
                        <div>尺寸</div>
                    </td>
                    <td>
                        <div>证书号</div>
                    </td>
                    <td>
                        <div>工费</div>
                    </td>
                    <td>
                        <div>成本价</div>
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
                        <div><?= $key+1 ?></div>
                    </td>
                    <td>
                        <div><?= $val['goods_name'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['goods_id'] ?></div>
                    </td>
					<td>
						<div><?= $val['style_sn'] ?></div>
					</td>
					<td>
						<div><?= $val['product_type_name'] ?></div>
					</td>
                    <td>
                        <div><?= $val['style_cate_name'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['warehouse_name'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['material'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['gold_weight'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['cost_price'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['main_stone_type'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['diamond_carat'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['main_stone_num'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['main_stone_info'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['second_stone_weight1'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['second_stone_num1'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['gross_weight'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['finger'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['product_size'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['cert_id'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['gong_fee'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['cost_price'] ?></div>
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
                                <div>货品名称</div>
                            </td>
                            <td >
                                <div>条码号</div>
                            </td>
                            <td>
                                <div>款号</div>
                            </td>
                            <td>
                                <div>产品分类</div>
                            </td>
                            <td>
                                <div>商品类型</div>
                            </td>
                            <td>
                                <div>仓库</div>
                            </td>

                            <td>
                                <div>材质</div>
                            </td>
                            <td>
                                <div>金重</div>
                            </td>
                            <td>
                                <div>成本</div>
                            </td>
                            <td>
                                <div>主石类型</div>
                            </td>
                            <td>
                                <div>主石重</div>
                            </td>
                            <td>
                                <div>主石粒数</div>
                            </td>
                            <td>
                                <div>主石规格(颜色/净度/切工/抛光/对称/荧光)</div>
                            </td>
                            <td>
                                <div>副石重</div>
                            </td>
                            <td>
                                <div>副石粒数</div>
                            </td>
                            <td>
                                <div>总重</div>
                            </td>
                            <td>
                                <div>手寸</div>
                            </td>
                            <td>
                                <div>尺寸</div>
                            </td>
                            <td>
                                <div>证书号</div>
                            </td>
                            <td>
                                <div>工费</div>
                            </td>
                            <td>
                                <div>成本价</div>
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
                    <td></td>
                    <td></td>
                    <td><div><?= $total['cost_price_count']?></div></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
			</table>
            <div><span>制单人：<?= $model->creator->username ?? ''?></span><span style="margin-left:300px; ">审核人：<?= $model->auditor->username ?? ''?></span></span><span style="margin-left:300px; ">签收人：</span></div>
			
			<!--endprint1-->
		</div>
        <div class="text-center">
            <!-- 打印按钮 -->
            <button type="button" class="btn-ms" target="_blank" onclick="preview(1)">打印</button>
        </div>

	</body>
</html>
