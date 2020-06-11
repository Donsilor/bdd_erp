<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 经营范围
 *
 * Class BusinessScopeEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class BusinessScopeEnum extends BaseEnum
{
	const CPZ = 1;
	const HJ = 2;
    const KJSJ = 3;
    const PTSJ = 4;
    const KJZSXQP = 5;
    const PTZSXQP = 6;
    const CBSP = 7;
    const YSP = 8;
    const GOLD = 9;
    const STONE = 10;
    const QT = 99;
	
	/**
	 * @return array
	 */
	public static function getMap(): array
	{
		return [
            self::CPZ => '成品钻',
            self::HJ => '黄金',
            self::KJSJ => 'K金素金',
            self::PTSJ => 'PT素金',
            self::KJZSXQP => 'K金钻石镶嵌品',
            self::PTZSXQP => 'PT钻石镶嵌品',
            self::CBSP => '彩宝饰品',
            self::YSP => '银饰品',
            self::GOLD => '金料',
            self::STONE => '石料',
            self::QT => '其他',
		];
	}
}