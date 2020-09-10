<?php

namespace addons\Sales\common\enums;

use addons\Style\common\enums\AttrIdEnum;

/**
 * 京东属性 枚举
 * @package common\enums
 */
class JdAttrEnum extends \common\enums\BaseEnum
{
    const DIA_ClARITY = 91083;
    const DIA_CUT = 91084;
    const DIA_COLOR = 91082;
    const DIA_CATAT = 99873;    
    const MATERIAL = 91088;//93655
    const CERT_TYPE = 72374;    
    const SEDOND_STONE_WEIGHT = 99874;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::DIA_ClARITY =>[
                        'name'=>'钻石净度',
                        'idMap' =>[ self::DIA_ClARITY=>AttrIdEnum::DIA_CLARITY],
                        'itemMap'=>[
                                'FL/无暇'=>6,//【ERP:6        FL
                                'IF/镜下无暇'=>7,//【ERP:7        IF
                                'VVS/极微瑕'=>8,//【ERP:8        VVS1；62        VVS2
                                'VS/微瑕'=>63,//【ERP:        63        VS1；64        VS2
                                'SI/小瑕'=>448,//【ERP:448        SI
                                'P/不洁净'=>324,//【ERP:324        P1
                                '不分级'=>243   //【ERP:          243        不分级    
                        ]
                ],
                self::DIA_CUT =>[
                        'name'=>'钻石切工',
                        'idMap' =>[self::DIA_CUT=>AttrIdEnum::DIA_CUT ],
                        'itemMap'=>[
                                'Excellent极好'=>13,//【ERP:        13        EX
                                'Very Good很好'=>14,//【ERP:        14        VG
                                'Good好'=>15,//【ERP:        15        GD
                                'Fair一般'=>336,//【ERP:336        Fair
                                'Poor差'=>337,//【ERP:337        Poor
                                '不分级'=>null //【ERP为空
                        ]
                ],
                self::DIA_COLOR =>[
                        'name'=>'钻石颜色',
                        'idMap' =>[self::DIA_COLOR=>AttrIdEnum::DIA_COLOR ],
                        'itemMap'=>[
                                'D'=>18,//【映射ERP：18        D
                                'E'=>19,//【映射ERP：19        E
                                'F'=>22,//【映射ERP：22        F
                                'F-G'=>22,//【映射ERP：22        F  ；50  G
                                'G'=>50,//【映射ERP：50  G
                                'H'=>51, //【映射ERP：51        H
                                'I'=>52,//【映射ERP： 52        I
                                'I-J'=>447,//【映射ERP：         447        IJ
                                'J'=>53,//【映射ERP：        53        J
                                'K'=>153,//【映射ERP：         153        K
                                'K-L'=>154,//【映射ERP： 154        L；153        K
                                'L'=>154,//【映射ERP：         154        L；
                                'M'=>155,//【映射ERP：155        M
                                'M-N'=>155,//【映射ERP：155        M；156        N
                                'N'=>156,//【映射ERP：156        N
                                '不分级'=>242 //【映射ERP：242        不分级
                        ]
                ],
                self::MATERIAL =>[
                        'name'=>'镶嵌材质',
                        'idMap' =>[ 93655=>0 ],
                        'itemMap'=>[
                                'PT950铂金'=>0,
                                'PT900铂金'=>0,
                                'k金镶嵌宝石'=>0,
                                '玫瑰18k金'=>0,
                                '白18k金'=>0,
                                '黄18K金'=>0,
                                '铂金/PT镶嵌宝石'=>0,
                                '其它'=>0,
                        ]
                ],
                self::CERT_TYPE=>[
                        'name'=>'证书类型',
                        'idMap' =>[ self::CERT_TYPE=>AttrIdEnum::DIA_CERT_TYPE],
                        'itemMap'=>[
                                'GIA/美国宝石学院'=>138,//【ERP：138        GIA
                                'NGTC/国家珠宝玉石质量监督检验中心'=>151,//【ERP：151        NGTC-国检                                
                                'HRD/比利时钻石高层议会'=>346,//【ERP：346        HRD
                                'IGI/国际宝石学院'=>348,//【ERP：348        IGI
                                'NJQSIC/国家首饰质量监督检验中心'=>479,//【ERP：479        NJQSIC/国家首饰质量
                                'CCGTC/北京市中工商联珠宝检测中心'=>152,//【ERP:152        其它
                                'GIC/中国地质大学（武汉）珠宝检测中心'=>480,//【ERP:480   GIC/中国地质大学(武汉)
                                '其它国内证书'=>152,//【ERP:152        其它
                                '其它国际证书'=>152,//【ERP:152        其它
                                '无证书'=>null,//【ERP:为空
                        ]
                ],
                self::DIA_CATAT =>[
                        'name'=>'主石重量',
                        'idMap' =>[ ],
                        'itemMap'=>[
                                '10分以下'=>0,
                                '11-20分'=>0,
                                '21-40分'=>0,
                                '41-50分'=>0,
                                '51-70分'=>0,
                                '71分-1克拉'=>0,
                                '1克拉以上'=>0,
                                '2克拉以上'=>0,
                                '无主石'=>0,
                        ]
                ],
                self::SEDOND_STONE_WEIGHT =>[
                        'name'=>'副石重量',
                        'idMap' =>[ ],
                        'itemMap'=>[
                        ]
                ],
        ];  
    }
    /**
     * 属性名称
     * @param unknown $key
     * @param unknown $funcName
     * @return NULL|mixed
     */
    public static function getAttrName($attr_id,$attr_list)
    {  
        $map = array_column($attr_list, 'name','id');
        return $map[$attr_id] ?? null;
    }
    /**
     * 
     * @param unknown $key
     * @return NULL|mixed
     */
    public static function getAttrId($key)
    {
        $map = self::getMap();
        return $map[$key]['idMap'][$key] ?? null;        
    }
    
    /**
     *
     * @param unknown $key
     * @return NULL|mixed
     */
    public static function getValueId($key,$value)
    {
        $map = self::getMap();
        return $map[$key]['itemMap'][$value] ?? null;
    }
    
    
}