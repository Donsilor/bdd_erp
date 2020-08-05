
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
			<div class="title"><?= Yii::$app->formatter->asDatetime(time(),'Y年M月') ?>不良返厂单表</div>
			
			<!-- 基础信息 -->
			<div class="order-info">
				<div class="list clf">
					<div class="child fl clf">
						<div class="child-attr fl">供应商：</div>
						<div class="child-val fl"><?= $model->supplier->supplier_name ?></div>
					</div>
                    <div class="child fl clf">
                        <div class="child-attr fl">收货单号：</div>
                        <div class="child-val fl"><?= $model->receipt_no ?></div>
                    </div>
				</div>
				<div class="list clf">
					<div class="child fl clf">
						<div class="child-attr fl">返厂单号：</div>
						<div class="child-val fl"><?= $model->defective_no ?? '' ?> </div>
					</div>

				</div>

			</div>
			
			<!-- 订单列表 -->
			<table class="table" border="1" cellspacing="0" cellpadding="0" width="100%" >
				<!-- 列表头部 -->
				<tr class="t-head">
					<td rowspan="2">
						<div>序号</div>
					</td>
					<td rowspan="2">
						<div>款号</div>
					</td>
                    <td rowspan="2">
                        <div>货品名称</div>
                    </td>
					<td rowspan="2">
						<div>产品分类</div>
					</td>
					<td rowspan="2">
						<div>产品线</div>
					</td>
                    <td rowspan="2">
                        <div>材质</div>
                    </td>
                    <td rowspan="2">
                        <div>成色</div>
                    </td>
                    <td rowspan="2">
                        <div>件数</div>
                    </td>
                    <td rowspan="2">
                        <div>指圈</div>
                    </td>
                    <td rowspan="2">
                        <div>尺寸</div>
                    </td>
                    <td class="bg-blue bold" colspan="6">
                        <div>金料</div>
                    </td>
                    <td class="bg-blue bold" colspan="7">
                        <div>主石</div>
                    </td>
                    <td class="bg-blue bold" colspan="7">
                        <div>副石</div>
                    </td>
                    <td rowspan="2">
                        <div>配件(g)</div>
                    </td>
                    <td rowspan="2">
                        <div>配件额</div>
                    </td>
                    <td rowspan="2">
                        <div>配件工费</div>
                    </td>
                    <td rowspan="2">
                        <div>工费</div>
                    </td>
                    <td rowspan="2">
                        <div>镶石费</div>
                    </td>
                    <td rowspan="2">
                        <div>工艺费用</div>
                    </td>
                    <td rowspan="2">
                        <div>分色/分件</div>
                    </td>
                    <td rowspan="2">
                        <div>补口费</div>
                    </td>
                    <td rowspan="2">
                        <div>单价</div>
                    </td>
                    <td rowspan="2">
                        <div>总额</div>
                    </td>
                    <td rowspan="2">
                        <div>证书费</div>
                    </td>
                    <td rowspan="2">
                        <div>备注</div>
                    </td>
                    <td rowspan="2">
                        <div>倍率</div>
                    </td>
                    <td rowspan="2">
                        <div>标签价</div>
                    </td>
                    <td rowspan="2">
                        <div>质检未过原因</div>
                    </td>
                    <td rowspan="2">
                        <div>质检备注</div>
                    </td>

                </tr>
                <tr class="t-head bg-blue">
                    <td>
                        <div>货重</div>
                    </td>
                    <td>
                        <div>净重</div>
                    </td>
                    <td>
                        <div>损耗</div>
                    </td>
                    <td>
                        <div>含耗重</div>
                    </td>
                    <td>
                        <div>金价</div>
                    </td>
                    <td>
                        <div>金料额</div>
                    </td>
					<td>
						<div>石号</div>
					</td>
					<td>
						<div>粒数</div>
					</td>
					<td>
						<div>石重</div>
					</td>
					<td>
						<div>颜色</div>
					</td>
					<td>
						<div>净度</div>
					</td>
					<td>
						<div>单价</div>
					</td>
					<td>
						<div>金额</div>
					</td>

                    <td>
                        <div>石号</div>
                    </td>
                    <td>
                        <div>粒数</div>
                    </td>
                    <td>
                        <div>石重</div>
                    </td>
                    <td>
                        <div>颜色</div>
                    </td>
                    <td>
                        <div>净度</div>
                    </td>
                    <td>
                        <div>单价</div>
                    </td>
                    <td>
                        <div>金额</div>
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
						<div><?= $val['style_sn'] ?></div>
					</td>
                    <td>
                        <div><?= $val['goods_name'] ?></div>
                    </td>
					<td>
						<div><?= $val['style_cate_name'] ?></div>
					</td>
					<td>
						<div><?= $val['product_type_name'] ?></div>
					</td>
                    <td>
                        <div><?= $val['material'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['goods_color'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['goods_num'] ?></div>
                    </td>
					<td>
						<div><?= $val['finger'] ?></div>
					</td>
                    <td>
                        <div><?= $val['product_size'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['gold_weight'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['suttle_weight'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['gold_loss'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['gross_weight'] ?></div>
                    </td>


                    <td>
                        <div><?= $val['gold_price'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['gold_amount'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['main_stone_sn'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['main_stone_num'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['main_stone_weight'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['main_stone_color'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['main_stone_clarity'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['main_stone_price'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['main_stone_price_sum'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['second_stone_sn1'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['second_stone_num1'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['second_stone_weight1'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['second_stone_color1'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['second_stone_clarity1'] ?></div>
                    </td>

                    <td>
                        <div><?= $val['second_stone_price1'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['second_stone_price1_sum'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['parts_weight'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['parts_price'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['parts_fee'] ?></div>
                    </td>

                    <td>
                        <div><?= $val['gong_fee'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['xianqian_fee'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['biaomiangongyi_fee'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['fense_fee'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['bukou_fee'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['price'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['price_sum'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['cert_fee'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['goods_remark'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['markup_rate'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['sale_price'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['iqc_name'] ?></div>
                    </td>
                    <td>
                        <div><?= $val['iqc_remark'] ?></div>
                    </td>

				</tr>
                <?php if(($key + 1) % $pagesize == 0){?>
                </table>
                <div class="PageNext"></div>
                <table class="table" border="1" cellspacing="0" cellpadding="0" width="100%" >
                    <tr class="t-head">
                        <td rowspan="2">
                            <div>序号</div>
                        </td>
                        <td rowspan="2">
                            <div>款号</div>
                        </td>
                        <td rowspan="2">
                            <div>货品名称</div>
                        </td>
                        <td rowspan="2">
                            <div>产品分类</div>
                        </td>
                        <td rowspan="2">
                            <div>产品线</div>
                        </td>
                        <td rowspan="2">
                            <div>材质</div>
                        </td>
                        <td rowspan="2">
                            <div>成色</div>
                        </td>
                        <td rowspan="2">
                            <div>件数</div>
                        </td>
                        <td rowspan="2">
                            <div>指圈</div>
                        </td>
                        <td rowspan="2">
                            <div>尺寸</div>
                        </td>
                        <td class="bg-blue bold" colspan="6">
                            <div>金料</div>
                        </td>
                        <td class="bg-blue bold" colspan="7">
                            <div>主石</div>
                        </td>
                        <td class="bg-blue bold" colspan="7">
                            <div>副石</div>
                        </td>
                        <td rowspan="2">
                            <div>配件(g)</div>
                        </td>
                        <td rowspan="2">
                            <div>配件额</div>
                        </td>
                        <td rowspan="2">
                            <div>配件工费</div>
                        </td>
                        <td rowspan="2">
                            <div>工费</div>
                        </td>
                        <td rowspan="2">
                            <div>镶石费</div>
                        </td>
                        <td rowspan="2">
                            <div>工艺费用</div>
                        </td>
                        <td rowspan="2">
                            <div>分色/分件</div>
                        </td>
                        <td rowspan="2">
                            <div>补口费</div>
                        </td>
                        <td rowspan="2">
                            <div>单价</div>
                        </td>
                        <td rowspan="2">
                            <div>总额</div>
                        </td>
                        <td rowspan="2">
                            <div>证书费</div>
                        </td>
                        <td rowspan="2">
                            <div>备注</div>
                        </td>
                        <td rowspan="2">
                            <div>倍率</div>
                        </td>
                        <td rowspan="2">
                            <div>标签价</div>
                        </td>
                        <td rowspan="2">
                            <div>质检未过原因</div>
                        </td>
                        <td rowspan="2">
                            <div>质检备注</div>
                        </td>

                    </tr>
                    <tr class="t-head bg-blue">
                        <td>
                            <div>货重</div>
                        </td>
                        <td>
                            <div>净重</div>
                        </td>
                        <td>
                            <div>损耗</div>
                        </td>
                        <td>
                            <div>含耗重</div>
                        </td>
                        <td>
                            <div>金价</div>
                        </td>
                        <td>
                            <div>金料额</div>
                        </td>
                        <td>
                            <div>石号</div>
                        </td>
                        <td>
                            <div>粒数</div>
                        </td>
                        <td>
                            <div>石重</div>
                        </td>
                        <td>
                            <div>颜色</div>
                        </td>
                        <td>
                            <div>净度</div>
                        </td>
                        <td>
                            <div>单价【单价】</div>
                        </td>
                        <td>
                            <div>金额</div>
                        </td>

                        <td>
                            <div>石号</div>
                        </td>
                        <td>
                            <div>粒数</div>
                        </td>
                        <td>
                            <div>石重</div>
                        </td>
                        <td>
                            <div>颜色</div>
                        </td>
                        <td>
                            <div>净度</div>
                        </td>
                        <td>
                            <div>单价【单价】</div>
                        </td>
                        <td>
                            <div>金额</div>
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
                        <td><div><?= $total['goods_num_count']?></div></td>
                        <td></td>
                        <td></td>
                        <td><div><?= $total['gold_weight_count']?></div></td>
                        <td><div><?= $total['suttle_weight_count']?></div></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><div><?= $total['gold_amount_count']?></div></td>
                        <td></td>
                        <td></td>
                        <td><div><?= $total['main_stone_weight_count']?></div></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><div><?= $total['main_stone_price_sum_count']?></div></td>
                        <td></td>
                        <td></td>
                        <td><div><?= $total['second_stone_weight1_count']?></div></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><div><?= $total['second_stone_price1_sum_count']?></div></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><div><?= $total['price_count']?></div></td>
                        <td><div><?= $total['price_sum_count']?></div></td>
                        <td><div><?= $total['cert_fee_count']?></div></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

			</table>
            <div><span>制单人：<?= $model->creator->username ?? ''?></span><span style="margin-left:300px; ">签收人：</span></div>
			<!--endprint1-->
		</div>
        <div class="text-center">
            <!-- 打印按钮 -->
            <button type="button" class="btn-ms" target="_blank" onclick="preview(1)">打印</button>
        </div>

	</body>
</html>
