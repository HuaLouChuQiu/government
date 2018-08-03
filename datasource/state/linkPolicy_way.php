<?php

/**
 * 爬取国务院公文的和处理存库的类
 */
class link_way {
    /**
     * 用curl爬取公文链接
     * @param $page 对应多少页的数据
     * @return 爬取到的数据数组
     */
    public function getpolichtml($page){
        $datas = array();
        $url = "http://sousuo.gov.cn/list.htm?q=&n=15&p=".$page."&t=paper&sort=pubtime&childtype=&subchildtype=&pcodeJiguan=&pcodeYear=&pcodeNum=&location=&searchfield=title:content:pcode:puborg:keyword&title=&content=&pcode=&puborg=&timetype=timeqb&mintime=&maxtime=";
        $curl_obj = new curlcity($url);
        

        $link_html = $curl_obj->curlMethod();
        $pquery_obj = new phpqueryGet($link_html);
        //var_dump($link_html);

        $index_num = $pquery_obj->getDetailedmess("table.dataList > tr","td.info > ul > li:eq(0)");
        array_shift($index_num);
        foreach($index_num as $key=>$value){
            //$value = htmlspecialchars_decode($value);
            $index_num[$key] = str_replace(array("索","  ","引","号：",chr(194) . chr(160)),"",$value);
            
        }
        array_push($datas,$index_num);

        $theamclass = $pquery_obj->getDetailedmess("table.dataList > tr","td.info > ul > li:eq(1)");
        array_shift($theamclass);
        foreach($theamclass as $key=>$value){
            $theamclass[$key] = str_replace("主题分类：","",$value);
        }
        array_push($datas,$theamclass);

        $organ = $pquery_obj->getDetailedmess("table.dataList > tr","td.info > ul > li:eq(2)");
        array_shift($organ);
        foreach($organ as $key=>$value){
            $organ[$key] = str_replace("发文机关：","",$value);
        }
        array_push($datas,$organ);

        $titls = $pquery_obj->getDetailedmess("table.dataList > tr","td:eq(1) > a");
        array_shift($titls);
        array_push($datas,$titls);

        $nums = $pquery_obj->getDetailedmess("table.dataList > tr","td:eq(0)");
        array_shift($nums);
        array_push($datas,$nums);

        $text_nums = $pquery_obj->getDetailedmess("table.dataList > tr","td:eq(2)");
        array_shift($text_nums);
        array_push($datas,$text_nums);

        $written_times = $pquery_obj->getDetailedmess("table.dataList > tr",'td:eq(3)');
        array_shift($written_times);
        array_push($datas,$written_times);

        $relas_times = $pquery_obj->getDetailedmess("table.dataList > tr","td:eq(4)");
        array_shift($relas_times);
        array_push($datas,$relas_times);

        $links = $pquery_obj->getTabAttributes("table.dataList > tr > td.info > a","href");
        array_push($datas,$links);

        return $datas;
    }


    /**
     * 把爬取的链接存放到书记中
     * 
     */
    /*public function savelink(){
        $pdo_obj = new pdoSql("127.0.0.1","root","","government");

        for($i=0;$i<51;$i++){
            $datas = $this->getpolichtml($i);

            foreach($datas[0] as $key=>$value){
                $in_array = array("num"=>$datas[4][$key],"title"=>$datas[3][$key],"link"=>$datas[8][$key],"index_num"=>$datas[0][$key],
                "themclass"=>$datas[1][$key],"organ"=>$datas[2][$key],"written_time"=>$datas[6][$key],"text_num"=>$datas[5][$key],
                "release_time"=>$datas[7][$key],"level"=>"state");

                $pdo_obj->insert("policy_link",$in_array);
            }
        }
    } */

    /**
     * 政策链接更新
     * @param $page更新的总页数 默认1
     * @param $day更新的天数 默认1
     */
    public function updatelink($page=1,$day=1){
        $time = date("Y年m月d日",strtotime("-$day day"));
        $pdo_obj = new pdoSql();

        for($i=0;$i<$page;$i++){
            $datas = $this->getpolichtml($i);       //爬取数据

            foreach($datas[0] as $key=>$value){
                if($datas[7][$key]>=$time){             //判断是不是最新的数据
                    $sw_link = array("title"=>$datas[3][$key],"link"=>$datas[8][$key]);
                    
                    $rs_data = $pdo_obj->select_all("policy_link",array("`id`"),$sw_link);

                    if(empty($rs_data)){                //插入先去重检测
                        $ai_obj = new AipNlp(APP_ID, APP_KEY, SECRET_KEY);     //ai接口对象
                        $title = str_replace(array(chr(194) . chr(160),"\n","\t"," ","\n\t")," ",$datas[3][$key]);
                        $title_msg = $ai_obj->lexer($title);                    //词法分析 
                        usleep(250000);
                        $title_json = json_encode($title_msg,JSON_UNESCAPED_UNICODE);

                        $in_array = array("title"=>$datas[3][$key],"titlejson"=>$title_json,"link"=>$datas[8][$key],"index_num"=>$datas[0][$key],
                        "themclass"=>$datas[1][$key],"organ"=>$datas[2][$key],"written_time"=>$datas[6][$key],"text_num"=>$datas[5][$key],
                        "release_time"=>$datas[7][$key],"level"=>"state");
                            $pdo_obj->insert("policy_link",$in_array);
                        }
                    
                }
            }
        }
    }

    /* public function updatetitlejson(){
        $pdo_obj = new pdoSql();
        $sw_link = array("status"=>1);
        $rs_data = $pdo_obj->select_all("policy_link",array("id","`title`"),$sw_link);
        foreach($rs_data as $v){
            $ai_obj = new AipNlp(APP_ID, APP_KEY, SECRET_KEY);     //ai接口对象
            $title = str_replace(array(chr(194) . chr(160),"\n","\t"," ","\n\t")," ",$v['title']);
            $title_msg = $ai_obj->lexer($title);                    //词法分析 
            usleep(250000);
            $title_json = json_encode($title_msg,JSON_UNESCAPED_UNICODE);

            $uw_link = array("id"=>$v['id']);
            $pdo_obj->update('policy_link',array("titlejson"=>$title_json),$uw_link);
        }
    } */

}