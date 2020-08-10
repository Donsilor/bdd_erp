<?php

namespace addons\Supply\common\models;

use Yii;
use common\models\backend\Member;

/**
 * This is the model class for table "supply_supplier".
 *
 * @property int $id 供应商ID
 * @property int $merchant_id 商户ID
 * @property string $supplier_code 供应商编码
 * @property string $supplier_tag 供应商标签(编货号用)
 * @property string $supplier_name 供应商名称
 * @property string $business_no 营业执照号码
 * @property string $business_address 营业执照地址
 * @property string $business_scope 经营范围(逗号隔开的id)
 * @property string $contract_file 合同文件
 * @property string $business_file 营业执照文件
 * @property string $pay_type 结算方式
 * @property int $balance_type 付款周期
 * @property string $tax_file 税务登记文件
 * @property string $tax_no 税务登记证号
 * @property string $bank_name 开户行
 * @property string $bank_account 银行账户
 * @property string $bank_account_name 开户姓名
 * @property string $contactor 供应商联系人
 * @property string $telephone 供应商联系电话
 * @property string $mobile 供应商联系人手机
 * @property string $address 供应商地址(取货地址)
 * @property string $bdd_contactor BDD紧急联系人
 * @property string $bdd_mobile BDD紧急联系人手机
 * @property string $bdd_telephone BDD紧急联系人电话
 * @property string $structure_cert 组织机构代码证
 * @property string $production_licence 生产许可证
 * @property string $taxpayer_cert 纳税人资格证
 * @property string $account_licence 开户许可证
 * @property string $insure_cert 保险复印件
 * @property int $auditor_id 审核人
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property string $remark 供应商备注
 * @property int $sort 排序
 * @property int $status 状态 1启用 0禁用 -1 删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Supplier extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('supplier');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'merchant_id', 'balance_type', 'goods_type', 'supplier_status', 'auditor_id', 'audit_status', 'audit_time', 'sort', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['supplier_name','supplier_tag','supplier_status','goods_type'], 'required'],
            [['supplier_code', 'bank_account', 'bank_account_name', 'contactor', 'telephone', 'mobile', 'bdd_contactor', 'bdd_mobile', 'bdd_telephone'], 'string', 'max' => 30],
            [['supplier_tag'], 'string', 'max' => 10],
            [['supplier_name', 'business_address', 'address'], 'string', 'max' => 120],
            [['business_no', 'tax_no'], 'string', 'max' => 50],
            [['contract_file', 'business_file', 'tax_file', 'structure_cert', 'production_licence', 'taxpayer_cert', 'account_licence', 'insure_cert', 'audit_remark', 'remark'], 'string', 'max' => 255],
            [['bank_name'], 'string', 'max' => 100],
            [['business_scope', 'pay_type'], 'safe'],
            [['business_scope'], 'parseBusinessScope'],
            [['pay_type'], 'parsePayTypeScope'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户ID',
            'supplier_code' => '供应商编码',
            'supplier_tag'=>'供应商简称(编货号用)',
            'supplier_name' => '供应商名称',
            'business_no' => '营业执照号码',
            'business_address' => '营业执照地址',
            'business_scope' => '经营范围',
            'contract_file' => '合同文件[文件格式：jpg,doc,zip,rar]',
            'business_file' => '营业执照文件[文件格式：jpg]',
            'pay_type' => '结算方式',
            'balance_type' => '付款周期',
            'goods_type' => '供货类型',
            'supplier_status' => '供应商状态',
            'tax_file' => '税务登记文件[文件格式：jpg]',
            'tax_no' => '税务登记证号',
            'bank_name' => '开户行',
            'bank_account' => '银行账户',
            'bank_account_name' => '开户姓名',
            'contactor' => '联系人',
            'telephone' => '联系人手机',
            'mobile' => '联系电话',
            'address' => '取货地址',
            'bdd_contactor' => 'BDD紧急联系人',
            'bdd_mobile' => 'BDD紧急联系人手机',
            'bdd_telephone' => 'BDD紧急联系人电话',
            'structure_cert' => '组织机构代码证[文件格式：jpg,doc,zip,rar]',
            'production_licence' => '生产许可证[文件格式：jpg,doc,zip,rar]',
            'taxpayer_cert' => '纳税人资格证[文件格式：jpg,doc,zip,rar]',
            'account_licence' => '开户许可证[文件格式：jpg,doc,zip,rar]',
            'insure_cert' => '保险复印件[文件格式：jpg,doc,zip,rar]',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'remark' => '供应商备注',
            'sort' => '排序',
            'status' => '状态',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 经营范围
     */
    public function parseBusinessScope()
    {
        if(is_array($this->business_scope)){
            $this->business_scope = ','.implode(',',$this->business_scope).',';
        }
        return $this->business_scope;
    }

    /**
     * 结算方式
     */
    public function parsePayTypeScope()
    {
        if(is_array($this->pay_type)){
            $this->pay_type = ','.implode(',',$this->pay_type).',';
        }
        return $this->pay_type;
    }

    /**
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id'])->alias('creator');
    }
    /**
     * 审核人
     * @return \yii\db\ActiveQuery
     */
    public function getAuditor()
    {
        return $this->hasOne(Member::class, ['id'=>'auditor_id'])->alias('auditor');
    }
}
