<?php

namespace addons\Supply\common\forms;

use Yii;

use addons\Supply\common\models\Supplier;
/**
 * 供应商 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class SupplierForm extends Supplier
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'merchant_id', 'balance_type', 'auditor_id', 'audit_status', 'audit_time', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['supplier_name', 'business_no'], 'required'],
            ['supplier_name', 'match', 'pattern' => '/[^a-z\d\x{4e00}-\x{9fa5}\(\)]/ui', 'message'=>'只能填写字母数字汉字和小括号'],
            [['supplier_code', 'bank_account', 'bank_account_name', 'contactor', 'telephone', 'mobile', 'bdd_contactor', 'bdd_mobile', 'bdd_telephone'], 'string', 'max' => 30],
            ['supplier_code', 'unique'],
            [['supplier_name', 'business_address', 'address'], 'string', 'max' => 120],
            [['business_no', 'tax_no'], 'string', 'max' => 50],
            [['business_scope'], 'parseBusinessScope'],
            [['pay_type'], 'PayTypeScope'],
            [['contract_file', 'business_file', 'tax_file', 'bank_name'], 'string', 'max' => 100],
            [['audit_remark', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getFirstCode($str){
        $len = mb_strlen(trim($str),'utf-8');
        $str_arr = [];
        for($i=0;$i<$len;$i++){
            $str_arr[]=trim(mb_substr($str,$i,1));
        }
        $code = '';
        foreach ($str_arr as $v) {
            if(mb_detect_encoding($v) == 'UTF-8'){
                $code .= self::getFirstCharter($v);
            }else{
                $code .= $v;
            }
        }
        return $code;
    }

    /**
     * {@inheritdoc}
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
}
