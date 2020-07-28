<?php

namespace common\helpers;

use Yii;
use yii\helpers\BaseStringHelper;
use Ramsey\Uuid\Uuid;

/**
 * Class StringHelper
 * @package common\helpers
 * @author jianyan74 <751393839@qq.com>
 */
class StringHelper extends BaseStringHelper
{
    /**
     * 生成Uuid
     *
     * @param string $type 类型 默认时间 time/md5/random/sha1/uniqid 其中uniqid不需要特别开启php函数
     * @param string $name 加密名
     * @return string
     * @throws \Exception
     */
    public static function uuid($type = 'time', $name = 'php.net')
    {
        switch ($type) {
            // 生成版本1（基于时间的）UUID对象
            case  'time' :
                $uuid = Uuid::uuid1();

                break;
            // 生成第三个版本（基于名称的和散列的MD5）UUID对象
            case  'md5' :
                $uuid = Uuid::uuid3(Uuid::NAMESPACE_DNS, $name);

                break;
            // 生成版本4（随机）UUID对象
            case  'random' :
                $uuid = Uuid::uuid4();

                break;
            // 产生一个版本5（基于名称和散列的SHA1）UUID对象
            case  'sha1' :
                $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $name);

                break;
            // php自带的唯一id
            case  'uniqid' :
                return md5(uniqid(md5(microtime(true) . self::randomNum(8)), true));

                break;
        }

        return $uuid->toString();
    }

    /**
     * 日期转时间戳
     *
     * @param $value
     * @return false|int
     */
    public static function dateToInt($value)
    {
        if (empty($value)) {
            return $value;
        }

        if (!is_numeric($value)) {
            return strtotime($value);
        }

        return $value;
    }

    /**
     * 时间戳转日期
     *
     * @param $value
     * @return false|int
     */
    public static function intToDate($value, $format = 'Y-m-d H:i:s')
    {
        if (empty($value)) {
            return date($format);
        }

        if (is_numeric($value)) {
            return date($format, $value);
        }

        return $value;
    }

    /**
     * 获取缩略图地址
     *
     * @param string $url
     * @param int $width
     * @param int $height
     */
    public static function getThumbUrl($url, $width, $height)
    {
        $url = str_replace('attachment/images', 'attachment/thumb', $url);
        return self::createThumbUrl($url, $width, $height);
    }

    /**
     * 创建缩略图地址
     *
     * @param string $url
     * @param int $width
     * @param int $height
     */
    public static function createThumbUrl($url, $width, $height)
    {
        $url = explode('/', $url);
        $nameArr = explode('.', end($url));
        $url[count($url) - 1] = $nameArr[0] . "@{$width}x{$height}." . $nameArr[1];

        return implode('/', $url);
    }

    /**
     * 获取压缩图片地址
     *
     * @param $url
     * @param $quality
     * @return string
     */
    public static function getAliasUrl($url, $alias = 'compress')
    {
        $url = explode('/', $url);
        $nameArr = explode('.', end($url));
        $url[count($url) - 1] = $nameArr[0] . "@{$alias}." . $nameArr[1];

        return implode('/', $url);
    }

    /**
     * 根据Url获取本地绝对路径
     *
     * @param $url
     * @param string $type
     * @return string
     */
    public static function getLocalFilePath($url, $type = 'images')
    {
        if (RegularHelper::verify('url', $url)) {
            if (!RegularHelper::verify('url', Yii::getAlias("@attachurl"))) {
                $hostInfo = Yii::$app->request->hostInfo . Yii::getAlias("@attachurl");
                $url = str_replace($hostInfo, '', $url);
            } else {
                $url = str_replace(Yii::getAlias("@attachurl"), '', $url);
            }
        } else {
            $url = str_replace(Yii::getAlias("@attachurl"), '', $url);
        }

        return Yii::getAlias("@attachment") . $url;
    }

    /**
     * 分析枚举类型配置值
     *
     * 格式 a:名称1,b:名称2
     *
     * @param $string
     * @return array
     */
    public static function parseAttr($string)
    {
        $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
        if (strpos($string, ':')) {
            $value = [];
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k] = $v;
            }
        } else {
            $value = $array;
        }

        return $value;
    }

    /**
     * 返回字符串在另一个字符串中第一次出现的位置
     *
     * @param $string
     * @param $find
     * @return bool
     * true | false
     */
    public static function strExists($string, $find)
    {
        return !(strpos($string, $find) === false);
    }

    /**
     * XML 字符串载入对象中
     *
     * @param string $string 必需。规定要使用的 XML 字符串
     * @param string $class_name 可选。规定新对象的 class
     * @param int $options 可选。规定附加的 Libxml 参数
     * @param string $ns
     * @param bool $is_prefix
     * @return bool|\SimpleXMLElement
     */
    public static function simplexmlLoadString(
        $string,
        $class_name = 'SimpleXMLElement',
        $options = 0,
        $ns = '',
        $is_prefix = false
    ) {
        libxml_disable_entity_loader(true);
        if (preg_match('/(\<\!DOCTYPE|\<\!ENTITY)/i', $string)) {
            return false;
        }

        return simplexml_load_string($string, $class_name, $options, $ns, $is_prefix);
    }

    /**
     * 字符串提取汉字
     *
     * @param $string
     * @return mixed
     */
    public static function strToChineseCharacters($string)
    {
        preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $string, $chinese);

        return $chinese;
    }

    /**
     * 字符首字母转大小写
     *
     * @param $str
     * @return mixed
     */
    public static function strUcwords($str)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $str)));
    }

    /**
     * 驼峰命名法转下划线风格
     *
     * @param $str
     * @return string
     */
    public static function toUnderScore($str)
    {
        $array = [];
        for ($i = 0; $i < strlen($str); $i++) {
            if ($str[$i] == strtolower($str[$i])) {
                $array[] = $str[$i];
            } else {
                if ($i > 0) {
                    $array[] = '-';
                }

                $array[] = strtolower($str[$i]);
            }
        }

        return implode('', $array);
    }

    /**
     * 获取字符串后面的字符串
     *
     * @param string $fileName 文件名
     * @param string $type 字符类型
     * @param int $length 长度
     * @return bool|string
     */
    public static function clipping($fileName, $type = '.', $length = 0)
    {
        return substr(strtolower(strrchr($fileName, $type)), $length);
    }

    /**
     * 获取随机字符串
     *
     * @param $length
     * @param bool $numeric
     * @return string
     */
    public static function random($length, $numeric = false)
    {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));

        $hash = '';
        if (!$numeric) {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }

        $max = strlen($seed) - 1;
        $seed = str_split($seed);
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed[mt_rand(0, $max)];
        }

        return $hash;
    }

    /**
     * 获取数字随机字符串
     *
     * @param bool $prefix 判断是否需求前缀
     * @param int $length 长度
     * @return string
     */
    public static function randomNum($prefix = false, $length = 8)
    {
        $str = $prefix ?? '';
        return $str . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, $length);
    }

    /**
     * 生成随机code
     *
     * @param $merchant_id
     * @return false|string
     */
    public static function code($merchant_id)
    {
        $time_str = date('YmdHis');
        $rand_code = rand(0, 999999);

        return substr(md5($time_str . $rand_code . $merchant_id), 16, 32);
    }

    /**
     * 字符串匹配替换
     *
     * @param $search
     * @param $replace
     * @param $subject
     * @param null $count
     * @return mixed
     */
    public static function replace($search, $replace, $subject, &$count = null)
    {
        return str_replace($search, $replace, $subject, $count);
    }

    /**
     * 验证是否Windows
     *
     * @return bool
     */
    public static function isWindowsOS()
    {
        return strncmp(PHP_OS, 'WIN', 3) === 0;
    }

    /**
     * 将一个字符串部分字符用*替代隐藏
     *
     * @param string $string 待转换的字符串
     * @param int $bengin 起始位置，从0开始计数，当$type=4时，表示左侧保留长度
     * @param int $len 需要转换成*的字符个数，当$type=4时，表示右侧保留长度
     * @param int $type 转换类型：0，从左向右隐藏；1，从右向左隐藏；2，从指定字符位置分割前由右向左隐藏；3，从指定字符位置分割后由左向右隐藏；4，保留首末指定字符串
     * @param string $glue 分割符
     * @return bool|string
     */
    public static function hideStr($string, $bengin = 0, $len = 4, $type = 0, $glue = "@")
    {
        if (empty($string)) {
            return false;
        }

        $array = [];
        if ($type == 0 || $type == 1 || $type == 4) {
            $strlen = $length = mb_strlen($string);

            while ($strlen) {
                $array[] = mb_substr($string, 0, 1, "utf8");
                $string = mb_substr($string, 1, $strlen, "utf8");
                $strlen = mb_strlen($string);
            }
        }

        switch ($type) {
            case 0 :
                for ($i = $bengin; $i < ($bengin + $len); $i++) {
                    isset($array[$i]) && $array[$i] = "*";
                }

                $string = implode("", $array);
                break;
            case 1 :
                $array = array_reverse($array);
                for ($i = $bengin; $i < ($bengin + $len); $i++) {
                    isset($array[$i]) && $array[$i] = "*";
                }

                $string = implode("", array_reverse($array));
                break;
            case 2 :
                $array = explode($glue, $string);
                $array[0] = self::hideStr($array[0], $bengin, $len, 1);
                $string = implode($glue, $array);
                break;
            case 3 :
                $array = explode($glue, $string);
                $array[1] = self::hideStr($array[1], $bengin, $len, 0);
                $string = implode($glue, $array);
                break;
            case 4 :
                $left = $bengin;
                $right = $len;
                $tem = array();
                for ($i = 0; $i < ($length - $right); $i++) {
                    if (isset($array[$i])) {
                        $tem[] = $i >= $left ? "*" : $array[$i];
                    }
                }

                $array = array_chunk(array_reverse($array), $right);
                $array = array_reverse($array[0]);
                for ($i = 0; $i < $right; $i++) {
                    $tem[] = $array[$i];
                }
                $string = implode("", $tem);
                break;
        }

        return $string;
    }



    /**
     * 根据汉字字符串获取对应的首字母
     */
    public static function getFirstCode($str){
        $len = mb_strlen(trim($str),'utf-8');
        $code = '';
        for($i=0;$i<$len;$i++){
            $word = trim(mb_substr($str,$i,1));
            if(mb_detect_encoding($word) == 'UTF-8'){
                $code .= self::getFirstCharter($word);
            }else{
                $code .= $word;
            }
        }
        return $code;
    }

    /**
     * 根据汉字获取首字母
     */
    protected static function getFirstCharter($str){
        if(empty($str)){return '';}
        $fchar=ord($str{0});
        if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
        $s1=@iconv('UTF-8','GBK',$str);
        $s2=@iconv('GBK','UTF-8',$s1);
        $s=$s2==$str?$s1:$str;
        $asc=ord($s{0})*256+ord($s{1})-65536;
        if($asc>=-20319&&$asc<=-20284) return 'A';
        if($asc>=-20283&&$asc<=-19776) return 'B';
        if($asc>=-19775&&$asc<=-19219) return 'C';
        if($asc>=-19218&&$asc<=-18711) return 'D';
        if($asc>=-18710&&$asc<=-18527) return 'E';
        if($asc>=-18526&&$asc<=-18240) return 'F';
        if($asc>=-18239&&$asc<=-17923) return 'G';
        if($asc>=-17922&&$asc<=-17418) return 'H';
        if($asc>=-17417&&$asc<=-16475) return 'J';
        if($asc>=-16474&&$asc<=-16213) return 'K';
        if($asc>=-16212&&$asc<=-15641) return 'L';
        if($asc>=-15640&&$asc<=-15166) return 'M';
        if($asc>=-15165&&$asc<=-14923) return 'N';
        if($asc>=-14922&&$asc<=-14915) return 'O';
        if($asc>=-14914&&$asc<=-14631) return 'P';
        if($asc>=-14630&&$asc<=-14150) return 'Q';
        if($asc>=-14149&&$asc<=-14091) return 'R';
        if($asc>=-14090&&$asc<=-13319) return 'S';
        if($asc>=-13318&&$asc<=-12839) return 'T';
        if($asc>=-12838&&$asc<=-12557) return 'W';
        if($asc>=-12556&&$asc<=-11848) return 'X';
        if($asc>=-11847&&$asc<=-11056) return 'Y';
        if($asc>=-11055&&$asc<=-10247) return 'Z';
        return null;
    }
    /**
     * 
     * @param unknown $string
     * @param string $delimiter
     * @param string $trim
     * @param string $skipEmpty
     * @return array
     */
    public static function explodeIds($string, $delimiters = [',','，'])
    {        
        $string = preg_replace("/\s+/is", ' ', trim($string));
        
        if($string == '') return [];

        if(!empty($delimiters)) {
            foreach ($delimiters as $d) {
                $string = str_replace($d, ' ', $string);
            } 
        }
        $array = parent::explode($string, ' ', true, true);
        return array_unique($array);
    }


    /***
     * @param n $
     * @return string
     */
    public static function smalltoBIG($num)
    {
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        $num = round($num, 2);
        $num = $num * 100;
        if (strlen($num) > 10) {
            return "数据太长，没有这么大的钱吧，检查下";
        }
        $i = 0;
        $c = "";
        while (1) {
            if ($i == 0) {
                $n = substr($num, strlen($num)-1, 1);
            } else {
                $n = $num % 10;
            }
            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);
            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }
            $i = $i + 1;
            $num = $num / 10;
            $num = (int)$num;
            if ($num == 0) {
                break;
            }
        }
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
            $m = substr($c, $j, 6);
            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j-3;
                $slen = $slen-3;
            }
            $j = $j + 3;
        }

        if (substr($c, strlen($c)-3, 3) == '零') {
            $c = substr($c, 0, strlen($c)-3);
        }
        if (empty($c)) {
            return "零元整";
        }else{
            return $c . "整";
        }
    }
}