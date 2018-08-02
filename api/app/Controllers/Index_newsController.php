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
    public function getState_news($id=0,$num=4,$maxnum=24){

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
     * @param $id
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
     * 根据用户的喜好获取新闻内容 关注页面列表
     * @param $caseWord 
     * @param $israndom
     * @param $p_id
     * @param $num
     */
    public function getState_news_like($israndom=1,$p_id=0,$num=4,$maxnum=24,$caseWord=""){
        $check_obj = new check;             //通用检测类
        $innM_obj = new Index_newsModel;     //对应的模型类
        $innS_obj = new Index_newsStructur;  //对应数据处理的类

        //检查参数
        $c_msg = $check_obj->check_param($israndom,"number");       //$israndom 参数检测
        $this->check_err($c_msg); 
        $c_msg = $check_obj->check_param($p_id,"number");       //p_id
        $this->check_err($c_msg); 
        $c_msg = $check_obj->check_param($num,"number");        //$num数量参数检测
        $this->check_err($c_msg);
        $c_msg = $check_obj->check_param($maxnum,"number");        //$maxnum数量参数检测
        $this->check_err($c_msg);
        if($caseWord != ""){
            $c_msg = $check_obj->check_param($caseWord,"json");        //判读是不是json类型的数据
            $this->check_err($c_msg);
        }

        $all_num = $num;            //通过循环获取用户想要的数组长度
        $max_times = 0;
        while($max_times<20){
            $r_msg = $innM_obj->sl_user_casenews($caseWord,$israndom,$p_id,$num);
            if(count($r_msg)>=$all_num){
                $r_msg = array_slice($r_msg,0,$all_num);
                break;
            }
            $def = $all_num - count($r_msg);
            $num = $num+$def;
            $max_times++;
        }

        $maxnum = (int)$maxnum;
        $this->output($innS_obj->su_processing($r_msg,$maxnum));
    }


    /**
     * 文章摘要处理测试
     * @param $id 对应文章的id
     * @return array 返回文章信息，分数和标题分词组成的数组
     */
    public function getcontent_sum($id=""){
        $r_msg = array();
        $check_obj = new check;             //通用检测类
        $pubway_obg = new pubway;
        $innM_obj = new Index_newsModel;     //对应的模型类
        $ai_obj = new \AipNlp(APP_ID, APP_KEY, SECRET_KEY);     //ai接口对象

        //检查参数
        $c_msg = $check_obj->check_param($id,"number");
        $this->check_err($c_msg);

        $text_content = $innM_obj->sl_news_score($id);        //获取整个数据

        if(empty($text_content)){
           return $r_msg;
        }

        $text = htmlspecialchars_decode($text_content[0]['text']);          //反转义
        unset($text_content[0]['text']);                                    //去掉大的数据量
        unset($text_content[1]['id']);
        unset($text_content[1]['s_title']);
        unset($text_content[1]['s_culture']);
        unset($text_content[1]['status']);
        
        $check_msg = $pubway_obg->clearebloddempty($text,1);                //获取文章的粗体字
        
        if(empty($check_msg)){                                              //获取文章包含 一是，第一，一要的句子
            $checkary = array("一是","第一","一要");
            $check_msg = $pubway_obg->clearempty($checkary,$text,4);
        }
        
        if(empty($check_msg)){
            $check_msg = $pubway_obg->clearparagraph($text);                //简单的获取每段的第一句话                      
        }

        $title = $text_content[0]['title'];
        $title_msg = $pubway_obg->titleprocess($title,$ai_obj);            //标题分词处理
              
        $r_msg = array_merge($r_msg,$text_content);
        array_push($r_msg,$check_msg,$title_msg);

        return $r_msg;
        
    }

    /**
     * 通过用户id去除用户可能感兴趣的篇文章
     * @param $id 用户表的主键
     * @param $israndom是否随机生成首页 1表示是，其他表示不是
     * @param $p_id 主键
     * @param $num 数量
     */
    public function getlike_sum($id="",$openid="",$israndom=1,$p_id=0,$num=3){
        $check_obj = new check;             //通用检测类
        $innM_obj = new Index_newsModel;     //对应的模型类
        $innS_obj = new Index_newsStructur;  //对应数据处理的类

        //检查参数
        $c_msg = $check_obj->check_param($id,"number");     //id
        $this->check_err($c_msg);
        $c_msg = $check_obj->check_param($openid,"string");     //id
        $this->check_err($c_msg);
        $c_msg = $check_obj->check_param($israndom,"number");       //$israndom 参数检测
        $this->check_err($c_msg); 
        $c_msg = $check_obj->check_param($p_id,"number");       //p_id
        $this->check_err($c_msg); 
        $c_msg = $check_obj->check_param($num,"number");        //$num数量参数检测
        $this->check_err($c_msg);

        //检测账号和对应的关系
        $c_msg = $innM_obj->check_user($id,$openid);
        $this->check_err($c_msg);

        //取出用户的偏好
        $caseWord = $innM_obj->sl_user_caseword($id);

        $all_num = $num;            //通过循环获取用户想要的数组长度
        $max_times = 0;
        while($max_times<20){
            $pidAry = $innM_obj->sl_user_casepolicy($caseWord,$israndom,$p_id,$num);
            if(count($pidAry)>=$all_num){
                $pidAry = array_slice($pidAry,0,$all_num);
                break;
            }
            $def = $all_num - count($pidAry);
            $num = $num+$def;
            $max_times++;
        }

        foreach($pidAry as $id){
            $maicontent = $this->getcontent_sum($id['id']);
            $hang = mb_strlen($maicontent[0]["titlt"])/10;
            $hang = (int)(is_int($hang)?$hang:$hang+1);
            $keyNum = (9-$hang)*3;
            $shuju[] = $innS_obj->su_sumtext($maicontent,$keyNum);  //对应数据处理的类
        }
        
        $this->output($shuju);
    }
}