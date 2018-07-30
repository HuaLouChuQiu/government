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

        
        //$innS_obj->su_processing($r_msg);die;
        $this->output($innS_obj->su_processing($r_msg));
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
     */
    public function getnews_sum($id=""){
        $pubway_obg = new pubway;
        $innM_obj = new Index_newsModel;     //对应的模型类
        $ai_obj = new \AipNlp(APP_ID, APP_KEY, SECRET_KEY);     //ai接口对象
        $text_content = $innM_obj->sl_news_keyword($id);        //获取整个数据
        
        $text = htmlspecialchars_decode($text_content[0]['text']);

        $checkary = array("一是","第一","一要");
        $check_msg = $pubway_obg->clearempty($checkary,$text,4);
        
        if(empty($check_msg)){
            $check_msg = $pubway_obg->clearebloddempty($text,1);
        }
        
        if(empty($check_msg)){
            return;
        }

        $title = $text_content[0]['title'];
        $title = str_replace(array(chr(194) . chr(160),"\n","\t"," ","\n\t")," ",$title);
        $title_msg = $ai_obj->lexer($title);                    //词法分析
        usleep(250000);

        $remainde = count($title_msg['items'])%2;
        if($remainde===1){
            $intnum = (count($title_msg['items'])-1)/2;
        }else{
            $intnum = count($title_msg['items'])/2;
        }

        $keyvalue_n = "";
        for($i=$intnum;$i<count($title_msg['items']);$i++){                     //获取标题和名词相关的关键字
            $part = $title_msg['items'][$i]['pos'];
            if(is_numeric(strpos($part,"n"))){
                if(!isset($part_1)){
                    $part_1 = $part;
                    $keyvalue_n = $title_msg['items'][$i]['item'];
                }else{
                    if($part==$part_1){
                        $keyvalue_n .= $title_msg['items'][$i]['item'];
                        $part_1 = $part;
                    }else{
                        break;
                    }
                }
            }else{
                if(isset($part_1)){
                    break;
                }
            }
        }

        $keyvalue_v = "";
        for($i=$intnum-1;$i>=0;$i--){                     //获取标题和动词相关的关键字
            $part = $title_msg['items'][$i]['pos'];
            if(is_numeric(strpos($part,"v"))){
                if(!isset($part_2)){
                    $part_2 = $part;
                    $keyvalue_v = $title_msg['items'][$i]['item'];
                }else{
                    if($part==$part_2){
                        $keyvalue_v = $title_msg['items'][$i]['item'].$keyvalue_v;
                        $part_2 = $part;
                    }else{
                        break;
                    }
                }
            }else{
                if(isset($part_2)){
                    break;
                }
            }
        }
        
        var_dump($keyvalue_v);
    }

    
}