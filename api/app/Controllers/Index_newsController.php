<?php
namespace app\Controllers;

use fastFrame\base\Controller;
use app\Models\Index_newsModel;
use app\Structurs\Index_newsStructur;
use method\check;
use method\pubway;

class Index_newsController extends Controller{

    /**
     * 获取主页国家新闻的接口
     * @param $id
     * @param $num 数量
     * @param $manum 标题最大数量
     */
    public function getState_news($id=0,$num=4,$maxnum=""){

        $check_obj = new check;             //通用检测类
        $innM_obj = new Index_newsModel;     //对应的模型类
        $innS_obj = new Index_newsStructur;  //对应数据处理的类

        //检查参数
        $c_msg[] = $check_obj->check_param($id,"number");
        $c_msg[] = $check_obj->check_param($num,"number",10);
        $c_msg[] = $check_obj->check_param($maxnum,"number");
        $c_msg = array_filter($c_msg);
        if(!empty($c_msg)){
            $c_msg['ok'] = false;
            $this->output($c_msg);
            die;
        }

        //处理是不是第一次加载
        $all_num = $num;
        $max_times=0;
        while($max_times<20){           
            if($id==0){
                $r_msg = $innM_obj->sl_state_news($id,$num);
            }else{
                $id_max = $id-$num;
                $id_min = $id-1;
                $r_msg = $innM_obj->sl_state_news($id_max,$id_min);
            }

            if(count($r_msg)>=$all_num){                 //有些事空的数量就再查找
                break;
            }

            $def = $all_num - count($r_msg);
            $num = $num+$def;
            $max_times++;
        }

        $maxnum = (int)$maxnum;
        //$innS_obj->su_processing($r_msg);die;
        $this->output($innS_obj->su_processing($r_msg,$maxnum));
    }


    /**
     * 获取新闻主文内容
     * 
     */
    public function getNews_contecnt($id=""){

        $check_obj = new check;             //通用检测类
        $innM_obj = new Index_newsModel;     //对应的模型类
        $innS_obj = new Index_newsStructur;  //对应数据处理的类

        //检查参数
        $c_msg[] = $check_obj->check_param($id,"number");
        $c_msg = array_filter($c_msg);
        if(!empty($c_msg) || $id==0){
            $c_msg['ok'] = false;
            $this->output($c_msg);
            die;
        }

        //判断是不是传了参数
        $text_content = $innM_obj->sl_news_keyword($id);
        $this->output($innS_obj->su_keyword($text_content));
    }

    /**
     * 文章摘要处理测试
     * @param $id 对应文章的id
     */
    public function getnews_sum($id=""){
        $check_obj = new check;             //通用检测类
        $pubway_obg = new pubway;
        $innM_obj = new Index_newsModel;     //对应的模型类
        $ai_obj = new \AipNlp(APP_ID, APP_KEY, SECRET_KEY);     //ai接口对象

        //检查参数
        $c_msg = $check_obj->check_param($id,"number");
        $this->check_err($c_msg);

        $text_content = $innM_obj->sl_news_keyword($id);        //获取整个数据

        if(empty($text_content)){
            $c_msg['errMsg'] = "id没有对应内容";
            $this->check_err($c_msg);
        }

        $text = htmlspecialchars_decode($text_content[0]['text']);

        $check_msg = $pubway_obg->clearebloddempty($text,1);        
        
        if(empty($check_msg)){
            $checkary = array("一是","第一","一要");
            $check_msg = $pubway_obg->clearempty($checkary,$text,4);
        }
        
        if(empty($check_msg)){
            $check_msg = $pubway_obg->clearparagraph($text);
                      
        }

        $title = $text_content[0]['title'];
        $title_msg = $pubway_obg->titleprocess($title,$ai_obj);
        var_dump($check_msg);
        var_dump($title);    
        var_dump($title_msg);    
        
        return $check_msg;
        
    }

    /**
     * 通过用户id去除用户可能感兴趣的篇文章
     * @param $id 用户表的主键
     * @param $israndom是否随机生成首页 1表示是，其他表示不是
     * @param $p_id 主键
     * @param $num 数量
     */
    public function getlike_news($id="",$openid="",$israndom=1,$p_id=0,$num=4){
        $check_obj = new check;             //通用检测类
        $innM_obj = new Index_newsModel;     //对应的模型类

        //检查参数
        $c_msg = $check_obj->check_param($id,"number");     //id
        $this->check_err($c_msg);
        /* $c_msg = $check_obj->check_param($openid,"string");     //id
        $this->check_err($c_msg);
        $c_msg = $check_obj->check_param($p_id,"number");       //p_id
        $this->check_err($c_msg); 
        $c_msg = $check_obj->check_param($num,"number");        //$num数量参数检测
        $this->check_err($c_msg); */

        //检测账号和对应的关系
        //$innM_obj->check_user($id)

        //取出用户的偏好
        $caseWord = $innM_obj->sl_user_caseword($id);
        $caseWord = json_decode($caseWord);         //装换成数组

        $pidAry = $innM_obj->sl_user_casepolicy($caseWord,$israndom,$p_id,$num);
        var_dump($pidAry);

    }
}