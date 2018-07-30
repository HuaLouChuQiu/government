<?php
namespace method;

/**
 * 一些通用的数据处理方法
 */
class pubway {
    /**
     * 取出指定字符串在大文章的后面内容，句号结尾
     * $char1 小字符串
     * $bigchar 大文章
     * $num 指定的数量 运行次数找到第几个
     */
    public function checkchar($char,$bigchar,$num,$type=""){
        $r_msg = array();
        $weinum = "";

        for($i=0;$i<$num;$i++){
            if($type!="span"){
                switch($i){                     //给数字对应的中文数字
                    case 0:
                    $weinum = "一";
                    break;
                    
                    case 1:
                    $weinum = "二";
                    break;

                    case 2:
                    $weinum = "三";
                    break;

                    case 3:
                    $weinum = "四";
                    break;

                    case 4:
                    $weinum = "五";
                    break;

                    case 5:
                    $weinum = "六";
                    break;

                    case 6:
                    $weinum = "七";
                    break;

                    case 7:
                    $weinum = "八";
                    break;

                    case 8:
                    $weinum = "九";
                    break;

                    case 9:
                    $weinum = "十";
                    break;

                    default:
                    $weinum = "零";
                }

                $char1 = str_replace("一",$weinum,$char);
                $preg = "/$char1([^.]*?)。/is";
                preg_match_all($preg,$bigchar,$belong);
                array_push($r_msg,$belong);
               
                if(empty($belong[0])){
                    if($i<2) $r_msg = array();         //如果只有第一，就去掉匹配项清空
                    break;
                }
            }else{
                $preg = "/<span style=\"font-weight: bold;\">([^<]*?)<\/span>/is";
                preg_match_all($preg,$bigchar,$belong);
                $r_msg = $belong;

                break;
            }
        }

        return $r_msg;
    }

    /**
     * 去除空数组
     * $checchar 搜索字符组成的数组
     * $text 原文本
     * $num 最多数组
     */
    public function clearempty($checchar,$text,$num){
        $r_msg = array();

        foreach($checchar as $check){
            $shi = $this->checkchar($check,$text,$num);
            
            if(empty($shi)){
                continue;
            }else{
                $r_msg = $shi;
                break;
            }
        }

        return $r_msg;
        
    }

   /**
     * 去除空数组
     * 
     * $text 原文本
     * $num 最多数组
     */
    public function clearebloddempty($text,$num){
        $r_msg = array();

        
        $shi = $this->checkchar("",$text,$num,"span");
        foreach($shi as $key=>$value){
            $shi[$key] = array_filter($value);
        }

        $shi = array_filter($shi);
        $r_msg = $shi;

        return $r_msg;
        
    }

    /**
     * 获取微信openid
     */
    public function getopenid($code){
        $api_url = "https://api.weixin.qq.com/sns/jscode2session?appid=".app_id."&secret=".app_secret."&js_code=$code&grant_type=authorization_code";

        //用file_get_contents方法获取数据
        $str = file_get_contents($api_url);

        //将json字符串转为数组
        $data = json_decode($str, true);
        return $data;
    }


    /**
     * 标题处理函数
     * @param $title 标题
     * @param $ai_obj 人工智能对象
     */
    public function titleprocess($title,$ai_obj){
        $r_msg = array();

        $title = str_replace(array(chr(194) . chr(160),"\n","\t"," ","\n\t")," ",$title);
        $title_msg = $ai_obj->lexer($title);                    //词法分析
        
        usleep(250000);

        $remainde = count($title_msg['items'])%2;
        if($remainde===1){
            $intnum = (count($title_msg['items'])-1)/2;
        }else{
            $intnum = count($title_msg['items'])/2-1;
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
        $r_msg[] = $keyvalue_v;
        $r_msg[] = $keyvalue_n;

        return $r_msg;
    }
}