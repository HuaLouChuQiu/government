<?php
/**
 * 把新闻的链接取出来然后用链接爬取的新闻具体内容
 * @method
 */
class textNews_way {
    /**
     * 调用爬虫的方法，分析数据并组成数组
     * @param $url 
     * @return $datas 返回的数据
     */
    public function getNewstext($url){
        $datas = array();
        $curl_obj = new curlcity($url);

        $text_html = $curl_obj->curlMethod();
        $pquery_obj = new phpqueryGet($text_html);

        /* $reg1 = '/\s+<div class=\"pages_content\" id=\"UCAP-CONTENT\">.*?<\/div>\s+/';
        preg_match_all($reg1,$text_html,$text);var_dump($text);
        array_push($datas,$text); */
        $text = $pquery_obj->getDetailedhtml("div.pages_content");
        array_push($datas,$text[0]);

        $source = $pquery_obj->getDetailedmess("div.pages-date","span.font");
        $source[0] = str_replace(array("来源： ","【字体：大 中 小】","打印"," ","\n","\t","\n\t"),"",$source[0]);
        array_push($datas,$source[0]);

        $edit = $pquery_obj->getDetailedmess("div.article","div.editor");
        $edit[0] = str_replace(array("【我要纠错】 责任编辑："," "),"",$edit[0]);
        array_push($datas,$edit[0]);

        return $datas;
    }

    /**
     * 从数据库里面取出链接爬取文章数据后再存存库。
     * 
     */
    public function saveNewstext(){
        $pdo_obj = new pdoSql();

        $sr_link = $pdo_obj->select_all("policy_link",array("`id`","link"),array("level"=>"state_news","is_use"=>0));

        foreach($sr_link as $key=>$value){
            $datas = $this->getNewstext($value["link"]);
            
            $ic_text_news = array("link_id"=>$sr_link[$key]['id'],"text"=>$datas[0],"source"=>$datas[1],"edit"=>$datas[2]);
            $text_id = $pdo_obj->insert("text_content",$ic_text_news);
            if(is_numeric($text_id)){
                $uw_link = array("`id`"=>$sr_link[$key]['id']);
                $pdo_obj->update("policy_link",array("is_use"=>1),$uw_link);die;
            }
        }
    }


}