<?php

/**
 * IDE 组件提示,无任何实际功能
 *
 * Class Yii
 */
class Yii
{
    /**
     * @var MyApplication
     */
    public static $app;
}

/**
 * Class MyApplication
 *
 * @property \yii\redis\Connection $redis
 * @property \yii\queue\cli\Queue $queue
 * @property \services\Application $services
 * @property \common\components\Debris $debris
 * @property \common\components\Pay $pay
 * @property \common\components\Logistics $logistics
 * @property \common\components\UploadDrive $uploadDrive
 * @property \common\components\BaseAddonModule $addons
 * @property \addons\TinyShop\services\Application $tinyShopService
 * @property \addons\TinyDistribution\services\Application $tinyDistributionService
 * @property \addons\Wechat\services\Application $wechatService
 * @property \addons\Style\services\Application $styleService
 * @property \addons\Supply\services\Application $supplyService
 * @property \addons\RfOnlineDoc\services\Application $rfOnlineDocService
 * @property \Detection\MobileDetect $mobileDetect
 * @property \jianyan\easywechat\Wechat $wechat
 * @property \Da\QrCode\Component\QrCodeComponent $qr
 * @property \common\components\Attribute $attr
 * @property \common\components\Transaction $trans
 * @property \yii\db\Connection $styleDb //款式库连接
 * @author jianyan74 <751393839@qq.com>
 */
class MyApplication
{

}