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

            if(mb_strlen($value['title']) > 38){       //标题
                $nei_ary['title'] = mb_substr($value['title'],0,38)."...";

            }else{
                $nei_ary['title'] = $value['title'];
            }

            $text = htmlspecialchars_decode($value['text']);
            $pquery_obj = new \phpqueryGet($text);
            $parags = $pquery_obj->getDetailedmess("p");
            $imgs = $pquery_obj->getTabAttributes("p img","src");

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
}