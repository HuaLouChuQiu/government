<?php
/**
 * 提取国家新闻关键字的文件
 */
class keywordNews {

    public $ai_num;
    /**
     * 把数据从数据库里面取出来 标题和正文内容
     * @return $s_data返回数据库提取到的东西
     * 
     */
    public function getmessage(){
        $pdo_obj = new pdoSql();        //pdo类

        $sql = "SELECT policy_link.id,policy_link.title,text_content.text from policy_link,text_content where policy_link.id=text_content.link_id and policy_link.level='state_news' and policy_link.is_use=1";
        $s_data = $pdo_obj->select_sql($sql);

        foreach($s_data as $key=>$value){                           //用双重循环做出能获取文章标签的接口

            if(mb_strlen($s_data[$key]['title'])>40){                //标题不能大于40个字
                $s_data[$key]['title'] = trim(mb_substr($s_data[$key]['title'],0,40));
            }
            $text = htmlspecialchars_decode($value['text']);
            $pquery_obj = new phpqueryGet($text);
            $parags = $pquery_obj->getDetailedmess("p");            //把文本分段

            $check_parag = "";
            foreach($parags as $k=>$v){
                $check_parag .= $v." ";
                if(mb_strlen($check_parag)>800){
                    $check_parag = mb_substr($check_parag,0,800);
                    break;
                }
            }

            $s_data[$key]['text'] = $check_parag;
        }
        
        return $s_data;
    }

    /**
     * 用百度人工智能api提取文章关键字
     */
    public function getkeyword(){
        $textmessage = $this->getmessage();
        $baiduAi_obj = new AipNlp(APP_ID, APP_KEY, SECRET_KEY);                          //百度自然语言处理接口
        $pdo_obj = new pdoSql();                                                       //pdo类

        foreach($textmessage as $key=>$value){
            $value['text'] = trim(str_replace(array(" ","\n","\t","\n\t"),"",$value["text"]));
            if(mb_strlen($value['text'])<100){                                           //做标记已经处理
                $uc_link = array("is_use"=>2);
                $pdo_obj->update('policy_link',$uc_link,array("`id`"=>$value['id']));
                continue;
            } 
            usleep(500000);
            $key_word = $baiduAi_obj->keyword($value['title'],$value['text']);      $this->ai_num++;   //百度人工智能接口调用量

            if(empty($key_word['items'])){                                      //返回空的做标记处理
                /* $uc_link = array("is_use"=>1);
                $pdo_obj->update('policy_link',$uc_link,array("`id`"=>$value['id'])); */
                continue;
            }

            $rleaKP_id = "";
            foreach($key_word['items'] as $k=>$v){                               //获取标签对应的id插入关系表
                $sr_keyword = $pdo_obj->select_all("keywords",array("`id`"),array("key_word"=>$v['tag']));

                if(empty($sr_keyword)){
                    $keyword_id = $pdo_obj->insert('keywords',array("key_word"=>$v['tag']));
                }else{
                    $keyword_id = $sr_keyword[0]['id'];
                }

                if(is_numeric($keyword_id) && $keyword_id>0){
                    //$is_rlea = $pdo_obj->select_all("rela_kp",array("`id`"),array("p_id"=>$value['id'],"k_id"=>$keyword_id));

                    //if(empty($is_rlea))
                    $rleaKP_id = $pdo_obj->insert("rela_kp",array("p_id"=>$value['id'],"k_id"=>$keyword_id,"confindence"=>$v['score']));
                }
            }

            if(is_numeric($rleaKP_id) && $rleaKP_id>0){                        //更新链接库类型为3
                $uc_link = array("is_use"=>3);
                $pdo_obj->update('policy_link',$uc_link,array("`id`"=>$value['id']));
                continue;
            }
            
        }
    }

}
