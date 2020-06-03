<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 付款周期
 *
 * Class BalanceTypeEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class BalanceTypeEnum extends BaseEnum
{
	const HDFK = 1;
	const ZQJS = 2;
    const YJ18R = 3;
    const YJ28R = 4;
	
	/**
	 * @return array
	 */
	public static function getMap(): array
	{
		return [
				self::HDFK => '货到付款',
				self::ZQJS => '周期结算',
                self::YJ18R => '月结=18日',
                self::YJ28R => '月结=28日',
		];
	}
}