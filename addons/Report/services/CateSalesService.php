<?php

namespace addons\Report\services;

use Yii;
use common\helpers\Url;
use common\components\Service;
use common\helpers\ArrayHelper;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderGoods;
use addons\Sales\common\models\SaleChannel;
use addons\Style\common\models\StyleCate;
use addons\Sales\common\enums\OrderStatusEnum;
use addons\Sales\common\enums\PayStatusEnum;
use common\enums\StatusEnum;

/**
 * Class CateSalesForm
 * @package services\common
 */
class CateSalesService extends Service
{
    public $channel;
    public $cateType;
    public $goods;

    /**
     * @param int $uid
     * @param array $channel
     * @return array
     * @throws
     */
    public function getCateSalesReport($uid = null, $channel = [])
    {
        //1.取订单商品数据
        $select = ['channel.name as channel_name', 'cate.name as cate_name', 'count(og.goods_num) as sales_num'];
        $query = Order::find()->alias('o')
            ->leftJoin(OrderGoods::tableName() . ' og', 'o.id=og.order_id')
            ->leftJoin(StyleCate::tableName() . ' cate', 'cate.id=og.style_cate_id')
            ->leftJoin(SaleChannel::tableName() . ' channel', 'channel.id=o.sale_channel_id')
            ->select($select);
        $query->andFilterWhere(['o.order_status' => [
            OrderStatusEnum::SAVE,
            OrderStatusEnum::PENDING,
            OrderStatusEnum::CONFORMED]
        ]);//审核状态
        $query->andFilterWhere(['o.pay_status' => [
            PayStatusEnum::PART_PAY,
            PayStatusEnum::HAS_PAY]
        ]);//支付状态
        if ($channel) {
            $query->andFilterWhere(['o.sale_channel_id' => $channel]);//销售渠道
        }
        $this->goods = $query->groupBy(['channel_name', 'cate_name'])->asArray()->all();

        //2取销售渠道
        $channelQuery = SaleChannel::find()->select(['id', 'name'])->where(['status' => StatusEnum::ENABLED])->orderBy('sort asc');
        if ($channel) {
            $channelQuery->andFilterWhere(['id' => $channel]);//销售渠道
        }
        $this->channel = $channelQuery->asArray()->all();
        $this->channel = ArrayHelper::map($this->channel, 'id', 'name');

        //3取款式分类
        $this->cateType = StyleCate::find()->select(['id', 'name'])->where(['status' => StatusEnum::ENABLED])->orderBy('sort asc')->asArray()->all();//, 'level' => [2, 3]
        $this->cateType = ArrayHelper::map($this->cateType, 'id', 'name');
        $data = [];

        //4整理数据
        $total_num = 0;
        if ($this->goods) {
            foreach ($this->goods as $id => $good) {
                $total_num = bcadd($total_num, $good['sales_num']);
                $data[$good['channel_name']][$good['cate_name']] = $good['sales_num'];
            }
            foreach ($this->channel as $cnl) {
                $cateArr = $data[$cnl] ?? [];
                $total_num = 0;
                foreach ($this->cateType as $cate) {
                    $num = $cateArr[$cate] ?? 0;
                    $cateArr[$cate] = $num;
                    $total_num = bcadd($total_num, $num);
                }
                arsort($cateArr);
                $cateArr['总计'] = $total_num;
                $list[$cnl] = $cateArr;
            }
        }
        return ['list' => $list ?? []];
    }
}