<?php
namespace app\Structurs;



/**
 * 对应的控制器结构化数据的类
 */
class Index_newsStructur {
    /**
     * 处理刚从数据取出来的数据
     * @paran $data
     */
    public function su_processing($data,$maxnum){
        $r_msg = array();
        foreach($data as $key=>$value){
            $nei_ary = array();
            $nei_ary['port'] = $value['id'];            //id号

            if(mb_strlen($value['title']) > $maxnum){       //标题
                $nei_ary['title'] = mb_substr($value['title'],0,$maxnum)."...";

            }else{
                $nei_ary['title'] = $value['title'];
            }

            $text = htmlspecialchars_decode($value['text']);
            $preg = "/<script.*<\/script>/is";                  //去掉js代码
            $text = preg_replace($preg,"",$text);
            
            $pquery_obj = new \phpqueryGet($text);
            $parags = $pquery_obj->getDetailedmess("p");
            $imgs = $pquery_obj->getTabAttributes("p > img,p > span > img","src");

            $nei_ary['shortContent'] = "";                  //组成短文内容
            foreach($parags as $v){
                $nei_ary['shortContent'] .= $v." ";
                
                if(mb_strlen($nei_ary['shortContent'])>70){
                    $nei_ary['shortContent'] = trim(mb_substr($nei_ary['shortContent'],0,70));
                    $nei_ary['shortContent'] = $nei_ary['shortContent']."...";
                    break;
                }
            }

            $nowtime = date("Y.m.d");                           //今天
            $yestime = date("Y.m.d",strtotime("-1 day"));       //昨天
            $qiantime = date("Y.m.d",strtotime("-2 day"));       //前天
            switch($value['release_time']){
                case $nowtime:
                $nei_ary['timestamp'] = "今天";
                break;
                case $yestime:
                $nei_ary['timestamp'] = "昨天";
                break;
                case $qiantime:
                $nei_ary['timestamp'] = "前天";
                break;
                default:
                $nei_ary['timestamp'] = $value['release_time'];
            }
            
            $nei_ary["site"] = $value['source'];

            if(!empty($imgs)){
                $preg = "/http:\/\/www.gov.cn\/(.*?)content/";
                preg_match($preg,$value['link'],$prefix);
                
                foreach($imgs as $k=>$v){
                    $is_use = strpos($v,"http://");
                    if(is_numeric($is_use)) continue;                //不是想要的图片去掉

                    $imgurl = "http://www.gov.cn/".$prefix[1].$v;

                    $size = getimagesize($imgurl);
                    $ratio = $size[0]/$size[1];
                    if($ratio<1.48 || $ratio>2.3) continue;                 //去掉高大于宽的图片
                    $nei_ary['image'] = $imgurl;
                }
                
            }
            
            $r_msg[] = $nei_ary;
        }

        
        return $r_msg;

    }


    /**
     * 处理文章内容和关键字
     * $data 由模型查找返回的数据
     */
    public function su_keyword($data){
        $r_msg = array();

        if(empty($data)){ $r_msg['err_msg'] = "数据为空"; return $r_msg;}       //为空
        
        //一些文章信息
        $info = array("title"=>$data[0]['title'],"site"=>$data[0]['source'],"time"=>$data[0]['release_time'],"author"=>$data[0]['edit'],"url"=>$data[0]['link']);

        $keyWords = array();                //关键字
        for($i=1;$i<count($data);$i++){
            $keyWords[] = $data[$i]['key_word'];
        }
        
        $text = htmlspecialchars_decode($data[0]['text']);
        
        $preg = "/<script.*<\/script>/is";                          //去掉js代码
        $text = preg_replace($preg,"",$text);
        //echo preg_match_all($preg,$text,$ary);var_dump($ary);
        $pquery_obj = new \phpqueryGet($text);
        $parags = $pquery_obj->getDetailedmess("p");
        $imgs = $pquery_obj->getTabAttributes("p > img,p > span > img","src");

        $content = array();$k=0;                    //图片和正文内容
        foreach($parags as $key=>$value){
            if(empty($value)){
                if(!isset($imgs[$k])) continue;                       //检测是不是有这么多图片
                
                if(is_numeric(strpos($imgs[$k],"http://"))){            //如果图片链接是完整的
                    $content[] = array("image"=>$imgs[$k]);
                }else{
                    $preg = "/http:\/\/www.gov.cn\/(.*?)content/";
                    preg_match($preg,$data[0]['link'],$prefix);
                    $content[] = array("image"=>"http://www.gov.cn/".$prefix[1].$imgs[$k]);
                }
                $k++;
            }else{
                $content[] = array("text"=>$value);
            }
        }

        $r_msg = array("info"=>$info,"keyWords"=>$keyWords,"content"=>$content);
        return $r_msg;
    }

    /**
     * 获取数组中值最大的键
     */
    public function findmaxKey($ary){
        $max_score_key = "";
        $max_score_value = 0;
        foreach($ary as $key=>$value){
            if($value>$max_score_value){
                $max_score_value = $value;
                $max_score_key = $key;
            }
        }
        return $max_score_key;
    }
    /**
     * 处理文章摘要，格式化数据
     * @param $data 从数据库查找出来的数据
     */
    public function su_sumtext($data,$num){
        $r_msg = array();

        if(empty($data)){ $r_msg['err_msg'] = "数据为空"; return $r_msg;}       //为参数数据空

        $time = $data[0]['release_time'];                   //时间组合
        $time = explode(".",$time);

        $port = $data[0]['id'];                             //id组合

        $author = $data[0]['edit'];                         //作者组合

        $title = array();                                  //标题组合
        foreach($data[3] as $k=>$proword){
            if($proword==""){
                if(empty($title) && $k==1){
                    $title[] = array("plain"=>$data[0]['title']);
                }
            }else{
                if(count($title)!=3){
                    $title_smale = explode($proword,$data[0]['title'],2);
                    $title[] = array("plain"=>$title_smale[0]);
                    $title[] = array("point"=>$proword);
                    $title[] = array("plain"=>$title_smale[1]);
                }else{
                    $title_smale = explode($proword,$title[2]['plain'],2);
                    $title[2] = array("plain"=>$title_smale[0]);
                    $title[] = array("point"=>$proword);
                    $title[] = array("plain"=>$title_smale[1]);
                }
            }
        }

        $keyWords = array();
        for($i=0;$i<$num;$i++){
            $maxKey = $this->findmaxKey($data[1]);
            switch($maxKey){
                case "s_econoimc":
                $keyWords[] = "经济";
                break;

                case "s_medical":
                $keyWords[] = "医疗";
                break;

                case "s_pension":
                $keyWords[] = "养老";
                break;

                case "s_education":
                $keyWords[] = "教育";
                break;

                case "s_housing":
                $keyWords[] = "住房";
                break;

                case "s_environment":
                $keyWords[] = "环境";
                break;

                case "s_hardword":
                $keyWords[] = "办事难";
                break;

                case "s_poverty":
                $keyWords[] = "脱贫";
                break;

                case "s_sannong":
                $keyWords[] = "三农";
                break;

                default:
                $keyWords[] = "其它";
            }
            if(empty($maxKey)){
                break;
            }
            unset($data[1][$maxKey]);
        }

        $content = array();
        $allhang = 0;
        foreach($data[2] as $key=>$text){

            $fontNum = mb_strlen($text);
            if($fontNum < 16){
                $text_she = str_replace(array("新华社发","新华社记者","（","）","摄","\n","\t","\n\t"," ",chr(194) . chr(160)),"",$text);
                $fontNum_small = mb_strlen($text_she);
                $fontNum_def = $fontNum-$fontNum_small;
                if($fontNum_small<6 && $fontNum_def>5) continue;                
            }

            $old_allhang = $allhang;
            $hang = $fontNum/20;
            $hang = (int)(is_int($hang)?$hang:$hang+1);
            $allhang = $allhang+$hang;
            $maxhang = count($content)>=3?6:7;
            if($allhang >= $maxhang){
                $def = $maxhang-$old_allhang;              
                $text = mb_substr($text,0,$def*20)."...";
                $content[] = $text;
                break;
            }else{
                $content[] = $text;
            }
        }

        $r_msg = array("time"=>$time,"port"=>$port,"author"=>$author,"title"=>$title,"keyWords"=>$keyWords,"content"=>$content);

        return $r_msg;
    }
}