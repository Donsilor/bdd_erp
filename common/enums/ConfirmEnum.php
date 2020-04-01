<?php

namespace common\enums;

/**
 * 是否
 *
 * Class GenderEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ConfirmEnum extends BaseEnum
{
	const YES = 1;
	const NO = 0;
	
	/**
	 * @return array
	 */
	public static function getMap(): array
	{
		return [
				self::YES => '是',
				self::NO => '否',
		];
	}
}