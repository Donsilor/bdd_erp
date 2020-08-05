<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 结算方式
 *
 * Class SettlementWayEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class SettlementWayEnum extends BaseEnum
{
	const TRANSFER = 1;
	const CASH = 2;
    const CHEQUE = 3;
	
	/**
	 * @return array
	 */
	public static function getMap(): array
	{
		return [
				self::TRANSFER => '转账',
				self::CASH => '现金',
                self::CHEQUE => '支票',
		];
	}
}