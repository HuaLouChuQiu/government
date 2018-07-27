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
    public function su_processing($data){
        $r_msg = array();
        foreach($data as $key=>$value){
            $nei_ary = array();
            $nei_ary['port'] = $value['id'];            //id号

            if(mb_strlen($value['title']) > 32){       //标题
                $nei_ary['title'] = mb_substr($value['title'],0,32)."...";

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
}