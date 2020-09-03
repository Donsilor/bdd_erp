<?php

namespace addons\Sales\common\enums;

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
    const MATERIAL = 93655;
    const CERT_TYPE = 72374;    
    const SEDOND_STONE_WEIGHT = 99874;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                "91083"=>[
                        'name'=>'钻石净度',
                        'idMap' =>[ 91083=>0 ],
                        'itemMap'=>[
                                'FL/无暇'=>0,
                                'IF/镜下无暇'=>0,
                                'VVS/极微瑕'=>0,
                                'VS/微瑕'=>0,
                                'SI/小瑕'=>0,
                                'P/不洁净'=>0,
                                '不分级'=>0
                        ]
                ],
                "91084"=>[
                        'name'=>'钻石切工',
                        'idMap' =>[ 91084=>0 ],
                        'itemMap'=>[
                                'Excellent极好'=>0,
                                'Very Good很好'=>0,
                                'Good好'=>0,
                                'Fair一般'=>0,
                                'Poor差'=>0,
                                '不分级'=>0
                        ]
                ],
                "91082"=>[
                        'name'=>'钻石颜色',
                        'idMap' =>[ 91082=>0 ],
                        'itemMap'=>[
                                'D'=>0,
                                'E'=>0,
                                'F'=>0,
                                'F-G'=>0,
                                'G'=>0,
                                'H'=>0,
                                'I'=>0,
                                'I-J'=>0,
                                'J'=>0,
                                'K'=>0,
                                'K-L'=>0,
                                'L'=>0,
                                'M'=>0,
                                'M-N'=>0,
                                'N'=>0,
                                '不分级'=>0
                        ]
                ],
                "93655"=>[
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
                "72374"=>[
                        'name'=>'证书类型',
                        'idMap' =>[ 72374=>0 ],
                        'itemMap'=>[
                                'GIA/美国宝石学院'=>0,
                                'NGTC/国家珠宝玉石质量监督检验中心'=>0,
                                'HRD/比利时钻石高层议会'=>0,
                                'IGI/国际宝石学院'=>0,
                                'NJQSIC/国家首饰质量监督检验中心'=>0,
                                'CCGTC/北京市中工商联珠宝检测中心'=>0,
                                'GIC/中国地质大学（武汉）珠宝检测中心'=>0,
                                '其它国内证书'=>0,
                                '其它国际证书'=>0,
                                '无证书'=>0,
                        ]
                ],
                "99873"=>[
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
                "99874"=>[
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
    public static function getAttrName($key)
    {
        $map = self::getMap();
        return $map[$key]['name'] ?? null;
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