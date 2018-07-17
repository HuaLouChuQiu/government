<?php
/**
 * 爬取国务院每天发表的最新新闻
 */
class linkNew_way {
    /**
     * 用curl爬取数据并分析数据
     * @param $URL 爬取新闻的链接
     * @return $datas 组成的数组
     */
    public function getnewsLink($url){
        $datas = array();
        $curl_obj = new curlcity($url);

        $link_html = $curl_obj->curlMethod();
        $pquery_obj = new phpqueryGet($link_html);

        $titls = $pquery_obj->getDetailedmess("div.list > ul > li","h4 > a");
        array_push($datas,$titls);

        $links = $pquery_obj->getTabAttributes("div.list > ul > li >h4 > a","href");
        array_push($datas,$links);

        $times = $pquery_obj->getDetailedmess("div.list > ul > li","h4 > span");
        array_push($datas,$times);

        return $datas;
    }

    /**
     * 把爬取的链接存到数据库中
     * @param $day爬取几天前的数据
     */
    public function  savenewsLink($day=1){
        $nowtime = date("Y.m.d",strtotime("-$day day"));
        $pdo_obj = new pdoSql();

        for($i=0;true;$i++){
            $url = "http://sousuo.gov.cn/column/31421/$i.htm";
            $datas = $this->getnewsLink($url);

            foreach($datas[0] as $key=>$value){
                $datas[2][$key] = str_replace(array(" ","\t","\n"),"",$datas[2][$key]);
                if($datas[2][$key] < $nowtime){            //不是当天时间就直接跳过去
                    break 2;
                }else{
                    $sw_news = array("title"=>$value,"link"=>$datas[1][$key]);

                    $rs_data = $pdo_obj->select_all("policy_link",array("`id`"),$sw_news);
                    
                    if(empty($rs_data)){
                        /* $uw_new = array("title"=>$value);
                        $uc_new = array("link"=>$datas[1][$key]);
                        $pdo_obj->update("policy_link",$uc_new,$uw_new); */
                        $in_array = array("title"=>$value,"link"=>$datas[1][$key],"release_time"=>$datas[2][$key],"level"=>"state_news");
                        $pdo_obj->insert("policy_link",$in_array);
                    }
                }
            }
        }

    }
}