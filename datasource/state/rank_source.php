<?php
/**
 * 通过提取的文章的关键字和几大人民关心得问题比较相似度得到分数
 */
class rank_source {
    //百度ai接口调用次数
    public $ai_num;
    /**
     * 取出已经处理好sql语句的数据
     * @param $sql语句
     * @return 查找到的数据
     */
    public function getsqlmessaeg($sql_obj,$sql){
        $pdo_obj = $sql_obj;

        $s_data = $pdo_obj->select_sql($sql);
        return $s_data;
    }

    /**
     * 获取对应的十大关心问题的分数
     * @param $ai_obj 百度ai实列话的对象
     * @param $config 配置文件
     * @param $keyword 关键字
     * @param $confidence置信度
     * @return $r_score 返回对应的分数值
     */
    public function getscore($ai_obj,$config,$keyword,$confidence){
        $r_score = array();

        foreach($config as $key=>$value){
            $simdata = $ai_obj->simnet($value,$keyword);
            usleep(500000);
            $this->ai_num++;

            if(!isset($simdata['score'])) break;

            if($simdata['score']>0.5){
                $r_score[$key] = (int)(1000000-$key*50000)*round($simdata['score'],4)*100*round($confidence,2);
            }else{
                $r_score[$key] = 0;
            }
        }

        return $r_score;
    }

    /**
     * 取出得到关键字的标题和对应的关键字,得到分数
     */
    public function getrangksource($config){
        $pdo_obj = new pdoSql();                //pdo数据库类
        $baiduAi_obj = new AipNlp(APP_ID, APP_KEY, SECRET_KEY);  

        $sql_title = "SELECT policy_link.id,policy_link.title from policy_link where policy_link.level='state_news' and policy_link.is_use=3";

        $titledata = $this->getsqlmessaeg($pdo_obj,$sql_title);
        
        foreach($titledata as $key=>$value){
            $sc_id = 0;

            if(is_numeric($value['id']) && $value['id']>0){                         //先在分数表创建数据
                $sr_id = $pdo_obj->select_all("rank_score",array("*"),array("p_id"=>$value['id']));
                if(empty($sr_id)){
                    $sc_id = $pdo_obj->insert("rank_score",array("p_id"=>$value['id']));
                }else{
                    $sc_id = (int)$sr_id[0]['id'];
                    $uc_socre = array("s_title"=>0,"s_econoimc"=>0,"s_medical"=>0,"s_pension"=>0,"s_education"=>0,"s_housing"=>0,"s_environment"=>0,
                     "s_hardword"=>0,"s_poverty"=>0,"s_sannong"=>0,"s_culture"=>0);
                    $pdo_obj->update("rank_score",$uc_socre,array("`id`"=>$sc_id));             //把分数清空
                }
            }

            if(!is_numeric($sc_id) || $sc_id<=0)    continue;                       //没有正确插入直接跳过

            for($i=0;$i<count($config['title']);$i++){                             //是不是有标题匹配特殊格式，直接title满分
                if(strpos($value['title'],$config['title'][$i]) !== false){
                    $sql_up = "UPDATE rank_score set s_title=100000000+s_title where id=$sc_id";
                    $pdo_obj->prepareSql($sql_up);
                    break;
                }else{
                    continue;
                }
            }

            $sql_key = "SELECT keywords.key_word,rela_kp.confindence from rela_kp,keywords where rela_kp.p_id=".$value['id']." and rela_kp.k_id=keywords.id";
            $keydata = $this->getsqlmessaeg($pdo_obj,$sql_key);                     //找出关键词

            foreach($keydata as $k=>$v){                                        //通过关键词计算分数
                $allsocre = $this->getscore($baiduAi_obj,$config['keyword'],$v['key_word'],$v['confindence']);
                
                $sql_upscore = "UPDATE rank_score set s_econoimc=".$allsocre[0]."+s_econoimc,s_medical=".$allsocre[1]."+s_medical,s_pension=
                ".$allsocre[2]."+s_pension,s_education=".$allsocre[3]."+s_education,s_housing=".$allsocre[4]."+s_housing,s_environment=
                ".$allsocre[5]."+s_environment,s_hardword=".$allsocre[6]."+s_hardword,s_poverty=".$allsocre[7]."+s_poverty,s_sannong=
                ".$allsocre[8]."+s_sannong,s_culture=".$allsocre[9]."+s_culture where id=$sc_id";

                $pdo_obj->prepareSql($sql_upscore);
            }
            
            $uc_usesocre = array("is_use"=>4);
            $pdo_obj->update('policy_link',$uc_usesocre,array("`id`"=>$value['id']));
            
        }
    }
}