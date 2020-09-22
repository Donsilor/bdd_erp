<?php

namespace common\enums;

/**
 * 状态枚举
 *
 * Class StatusEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class LanguageEnum extends BaseEnum
{
	const EN_US = 'en-US';
	const ZH_CN = 'zh-CN';
	const ZH_HK = 'zh-TW';
	
	/**
	 * @return array
	 */
	public static function getMap(): array
	{
		return [				
				self::ZH_CN => '中文简体',
				self::ZH_HK => '中文繁体',
				self::EN_US => 'English',
		];
	}
	/**
	 * @return array
	 */
	public static function getFlipMap() : array
	{
	    return array_flip(self::getMap());
	}

}