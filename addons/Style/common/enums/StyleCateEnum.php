<?php

namespace addons\Style\common\enums;

/**
 * 款式品类  枚举
 * @package common\enums
 */
class StyleCateEnum extends \common\enums\BaseEnum
{
    const ANKLET = 1;
    const RING = 2;
    const NECKLACE = 4;
    const PENDANT = 5;
    const EARRING = 6;
    const BRACELET = 8;
    const BANGLES = 9;
    const LOVE_RING = 23;


    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::RING => '戒指',
            self::PENDANT => '吊坠',
            self::NECKLACE => '项链',
            self::EARRING => '耳饰',
            self::BRACELET => '手链',
            self::BANGLES => '手镯',
            self::LOVE_RING => '情侣戒',
            self::ANKLET => '脚链',
        ];
    }

    /**
     * @return array
     */
    public static function getCodeMap(): array
    {
        return [
            self::RING => [StyleSexEnum::MAN => 'O', StyleSexEnum::WOMEN => 'R'],
            self::PENDANT => [StyleSexEnum::MAN => 'Q', StyleSexEnum::WOMEN => 'P'],
            self::NECKLACE => [StyleSexEnum::MAN => 'M', StyleSexEnum::WOMEN => 'N'],
            self::BRACELET => [StyleSexEnum::MAN => 'D', StyleSexEnum::WOMEN => 'B'],
            self::EARRING => [StyleSexEnum::MAN => 'F', StyleSexEnum::WOMEN => 'E'],
            self::BANGLES => [StyleSexEnum::MAN => 'Z', StyleSexEnum::WOMEN => 'S'],
            self::LOVE_RING => [StyleSexEnum::COMMON => 'L'],
            self::ANKLET => [StyleSexEnum::COMMON => 'J'],
        ];
    }

}