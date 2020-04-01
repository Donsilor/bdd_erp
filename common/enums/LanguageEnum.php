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
				self::ZH_CN => '简体中文',
				self::ZH_HK => '繁体中文',
				self::EN_US => 'English',
		];
	}

}