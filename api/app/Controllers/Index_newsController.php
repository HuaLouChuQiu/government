<?php
namespace app\Controllers;

use fastFrame\base\Controller;
use app\Models\Index_newsModel;
use method\check;
use app\Structurs\Index_newsStructur;

class Index_newsController extends Controller{

    /**
     * 获取主页国家新闻的接口
     */
    public function getState_news($id=0,$num=4){

        $check_obj = new check;             //通用检测类
        $innM_obj = new Index_newsModel;     //对应的模型类
        $innS_obj = new Index_newsStructur;  //对应数据处理的类

        //检查参数
        $c_msg[] = $check_obj->check_param($id,"number");
        $c_msg[] = $check_obj->check_param($num,"number",10);
        $c_msg = array_filter($c_msg);
        if(!empty($c_msg)){
            $c_msg['ok'] = false;
            $this->output($c_msg);
            die;
        }

        //处理是不是第一次加载
        if($id==0){
            $r_msg = $innM_obj->sl_state_news($id,$num);
        }else{
            $id_max = $id-$num;
            $id_min = $id-1;
            $r_msg = $innM_obj->sl_state_news($id_max,$id_min);
        }
        //$innS_obj->su_processing($r_msg);die;
        $this->output($innS_obj->su_processing($r_msg));
    }
}