<?php

namespace console\controllers;

use yii\console\Controller;
use yii\helpers\Console;
use common\helpers\FileHelper;
use addons\Style\common\models\Style;
use addons\Style\common\models\StyleImages;


/**
 * 款式图片处理
 * Class StyleController
 * @package console\controllers
 */
class StyleImageController extends Controller
{
    /**
     *  导出图片
     * @param string $params
     */
    public function actionExport()
    {
        $dir = dirname(dirname(dirname(__FILE__)));
        $newDir = $dir.'/upload/style';
        FileHelper::createDirectory($newDir);
        $list = Style::find()->select(['style_sn','style_image'])->where(['status'=>1])->limit(10000)->all();
        foreach ($list as $model) {
            if(!$model->style_image) {
                console::output($model->style_sn.': empty---------------------');
                continue;
            }
            $data = file_get_contents($model->style_image);
            if($data) {
                $file = $newDir.'/'.$model->style_sn.'.'.end(explode('.', $model->style_image));
                file_put_contents($file, $data);
                echo $model->style_sn.',';
            }else{
                console::output($model->style_sn.': error---------------------');
            }
        }
    }
    /**
     * 导入款式
     * @param string $params
     */
    public function actionImport()
    {
        $dir = dirname(dirname(dirname(__FILE__)));
        $newDir = $dir.'/upload/2020/09/03';
        $imageUrlDir = 'https://cdn-erp.bddco.cn/images/2020/09/03';
        FileHelper::createDirectory($newDir);
        
        $list = FileHelper::findFiles($dir."/upload/styleImages03/");
        $style_image_list = [];
        foreach ($list as $file){
            if(!preg_match("/\.db$/is", $file)){
                $fileData = file_get_contents($file);
                //$style_sn = basename(dirname($file));
                $newfile = $newDir."/image_".preg_replace("/( |\(|\))/is","",basename($file));
                $imageUrl = $imageUrlDir."/image_".preg_replace("/( |\(|\))/is","",basename($file));
                preg_match("/image_(.*?)(\.|\-|\_)/", $newfile,$arr);
                if(empty($arr[1])) {
                    console::output($newfile.': error---------------------');
                    continue;
                }
                $style_sn = $arr[1];
                if(strlen($style_sn) <=5) {
                    console::output($file);exit;
                }else{
                     if(!file_exists($newfile) && !file_exists(str_replace('2020/08/14','2020/08/06',$newfile))) {
                        $res = file_put_contents($newfile, $fileData);
                        console::output($newfile.'-'.$res);
                    } 
                }
                $style_image_list[$style_sn][] = $imageUrl;
            }
        }
        console::output("不存在的款号排查--begin");
        foreach ($style_image_list as $style_sn=>$image_list){
            $style = Style::find()->where(['style_sn'=>$style_sn])->one();
            if(!$style) {
                console::output($style_sn);
                unset($style_image_list[$style_sn]);
            }
        }
        console::output("不存在的款号排查--end");

        foreach ($style_image_list as $style_sn=>$image_list){
            console::output($style_sn.': BEGING--------------');
            try{
                $trans = \Yii::$app->trans->beginTransaction();                
                $this->createStyleImage($style_sn, $image_list);
                $trans->commit();                
            }catch (\Exception $e){
                $trans->rollback();
                console::output($style_sn.': error:'.$e->getMessage());
            }
            console::output($style_sn.': END-------------');
        }
    }
    /**
     * 创建一个款的图片
     * @param unknown $style_sn
     * @param unknown $image_list
     * @throws \Exception
     */
    private function createStyleImage($style_sn, $image_list)
    {        
        
        $style = Style::find()->where(['style_sn'=>$style_sn])->one();
        if(!$style) {
            console::output($style_sn.": [{$style_sn}]款号不存在");
            return ;
        } else if($style->style_image) {
            console::output($style_sn.": [{$style_sn}]款号已导入过图片");
            return ;
        }  
        foreach ($image_list as $image) {
            $model = StyleImages::find()->where(['style_id'=>$style->id,'image'=>$image])->one();
            if(!$model) {
                $model = new StyleImages();
                $model->style_id = $style->id;
                $model->image = $image;
                $model->creator_id = 1;
                $model->type = 1;//商品图片
                $model->status = 1;
                $model->position = 51;//正面     
                if(false === $model->save()){
                    console::output('ERROR: '.$style_sn.':'.$image);
                }
                console::output('SUCCESS: '.$style_sn.':'.$image);
            } 
        }        
    }
}