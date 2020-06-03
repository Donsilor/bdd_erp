
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
	</head>
	<body>
		<div class="container" id="wdf">
			<!--startprint1-->
			<div class="title">2020年4月恒得利珠宝产品订单下单表</div>
			
			<!-- 基础信息 -->
			<div class="order-info">
				<div class="list clf">
					<div class="child fl clf">
						<div class="child-attr fl">采购订单：</div>
						<div class="child-val fl">BSZ20200422002</div>
					</div>
					
					<div class="child fl clf">
						<div class="child-attr fl">采购下单员：</div>
						<div class="child-val fl">邹竹英</div>
					</div>
				</div>
				<div class="list clf">
					<div class="child fl clf">
						<div class="child-attr fl">委托供应商：</div>
						<div class="child-val fl">C08-金熠珠宝</div>
					</div>
				</div>
				<div class="list clf">
					<div class="child fl clf">
						<div class="child-attr fl">供应商地址：</div>
						<div class="child-val fl">番禺 </div>
					</div>
					
					<div class="child fl clf">
						<div class="child-attr fl">联系方式：</div>
						<div class="child-val fl"></div>
					</div>
					
					<div class="fr settle-accounts">
						结价:按款*件数+改图费
					</div>
				</div>
				<div class="list bold">
					<span>订单类型：</span>
					<span>银版</span>
					<span>常规</span>
					<span>定制</span>
					<span>试板</span>
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
						<div>图片</div>
					</td>
					<td rowspan="2">
						<div>款号</div>
					</td>
					<td rowspan="2">
						<div>品类</div>
					</td>
					<td rowspan="2">
						<div>产品线</div>
					</td>
					<td rowspan="2">
						<div>货品名称</div>
					</td>
					<td rowspan="2">
						<div>件数</div>
					</td>
					<td rowspan="2">
						<div>材质</div>
					</td>
					<td rowspan="2">
						<div>货品外部颜色</div>
					</td>
					<td rowspan="2">
						<div>手寸</div>
					</td>
					<td rowspan="2">
						<div>成品尺寸</div>
					</td>
					<td class="bg-blue bold" colspan="7">
						<div>主石</div>
					</td>
					<td class="bg-blue bold" colspan="7">
						<div>副石</div>
					</td>
					<td rowspan="2">
						<div>石料信息</div>
					</td>
					<td rowspan="2">
						<div></div>
					</td>
					<td rowspan="2">
						<div>连石总重(g)</div>
					</td>
					<td rowspan="2">
						<div>净重/单件(g)</div>
					</td>
					<td rowspan="2">
						<div>总净重(g)</div>
					</td>
					<td rowspan="2">
						<div>损耗</div>
					</td>
					<td rowspan="2">
						<div>银价</div>
					</td>
					<td rowspan="2">
						<div>单件银额</div>
					</td>
					<td rowspan="2">
						<div>金额</div>
					</td>
					<td rowspan="2">
						<div>配件信息</div>
					</td>
					<td rowspan="2">
						<div>工艺描述</div>
					</td>
					<td rowspan="2">
						<div>工费/件</div>
					</td>
					<td rowspan="2">
						<div>镶石费/件</div>
					</td>
					<td rowspan="2">
						<div>工费总额/件</div>
					</td>
					<td rowspan="2">
						<div>改图费</div>
					</td>
					<td rowspan="2">
						<div>喷蜡费</div>
					</td>
					<td rowspan="2">
						<div>单件额</div>
					</td>
					<td class="bg-orange" rowspan="2">
						<div>工厂总额</div>
					</td>
					<td class="bg-orange" rowspan="2">
						<div>公司成本总额</div>
					</td>
				</tr>
				<tr class="t-head bg-blue">
					<td>
						<div>主石</div>
					</td>
					<td>
						<div>主石</div>
					</td>
					<td>
						<div>主石</div>
					</td>
					<td>
						<div>主石</div>
					</td>
					<td>
						<div>主石</div>
					</td>
					<td>
						<div>主石</div>
					</td>
					<td>
						<div>主石</div>
					</td>
					<td>
						<div>副石</div>
					</td>
					<td>
						<div>副石</div>
					</td>
					<td>
						<div>副石</div>
					</td>
					<td>
						<div>副石</div>
					</td>
					<td>
						<div>副石</div>
					</td>
					<td>
						<div>副石</div>
					</td>
					<td>
						<div>副石</div>
					</td>
				</tr>
				
				<!-- 列表内容 -->
				<tr>
					<td>
						<div>1</div>
					</td>
					<td>
						<div></div>
					</td>
					<td>
						<div>DR0001182</div>
					</td>
					<td>
						<div>女戒</div>
					</td>
					<td>
						<div></div>
					</td>
					<td>
						<div>S925银莫桑石女戒</div>
					</td>
					<td>
						<div>15</div>
					</td>
					<td>
						<div>S925</div>
					</td>
					<td>
						<div>白色</div>
					</td>
					<td>
						<div></div>
					</td>
					<td>
						<div>US5.5#/3件US5#/2件US6#/3件US6.5#/2件US7#/3件US8#/2件</div>
					</td>
					<td>
						<div>莫桑石</div>
					</td>
					<td>
						<div>1.00</div>
					</td>
					<td>
						<div>1</div>
					</td>
					<td>
						<div>15</div>
					</td>
					<td>
						<div>15.00</div>
					</td>
					<td>
						<div>120</div>
					</td>
					<td>
						<div>1800.00</div>
					</td>
					<td>
						<div>莫桑石</div>
					</td>
					<td>
						<div>0.44</div>
					</td>
					<td>
						<div>34</div>
					</td>
					<td>
						<div>510</div>
					</td>
					<td>
						<div>6.60</div>
					</td>
					<td>
						<div>230</div>
					</td>
					<td>
						<div>1518.00</div>
					</td>
					<td>
						<div>石头公司自配
						主石尺寸6.5mm-1pc,
						共需15pc，重15.00ct
						副石1.4mm-26pc,
						共需390pc,重0.312ct
						1.5mm-4pc,
						共需60pc重0.06ct
						1.6mm-4pc,
						共需60pc重0.068ct</div>
					</td>
					<td>
						<div>3</div>
					</td>
					<td>
						<div>45</div>
					</td>
					<td>
						<div></div>
					</td>
					<td>
						<div></div>
					</td>
					<td>
						<div></div>
					</td>
					<td>
						<div></div>
					</td>
					<td>
						<div>15.00</div>
					</td>
					<td>
						<div></div>
					</td>
					<td>
						<div></div>
					</td>
					<td>
						<div>精工，光面</div>
					</td>
					<td>
						<div>35</div>
					</td>
					<td>
						<div>30</div>
					</td>
					<td>
						<div>65</div>
					</td>
					<td>
						<div>220</div>
					</td>
					<td>
						<div>110</div>
					</td>
					<td>
						<div>80</div>
					</td>
					<td>
						<div>1530</div>
					</td>
					<td>
						<div>4848</div>
					</td>
				</tr>
				
			</table>
			
			<!--endprint1-->
		</div>
        <div class="text-center">
            <!-- 打印按钮 -->
            <button type="button" class="btn-ms" target="_blank" onclick="preview(1)">打印</button>
        </div>

	</body>
</html>
